<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Support\ValidationMessages;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with('booking.customer', 'booking.room', 'fees')
            ->when($request->q, function ($query, $search) {
                $query->where(fn ($w) => $w->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('booking.customer', fn ($c) => $c->where('name', 'like', "%{$search}%")));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('payments.index', ['payments' => $payments]);
    }

    public function create(Request $request)
    {
        // Hanya booking yang belum punya pembayaran dan tidak dibatalkan
        $bookings = Booking::with('customer', 'room')
            ->doesntHave('payment')
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();

        return view('payments.create', [
            'bookings'    => $bookings,
            'selected'    => $request->integer('booking_id') ?: null,
            'methods'     => Payment::METHODS,
            'suggestions' => Payment::FEE_SUGGESTIONS,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(
            ['booking_id' => ['required', 'exists:bookings,id', Rule::unique('payments', 'booking_id')]],
            ValidationMessages::all() + ['booking_id.unique' => 'Booking ini sudah memiliki pembayaran.'],
            ValidationMessages::attributes()
        );

        $booking = Booking::findOrFail($request->booking_id);
        [$data, $fees] = $this->validated($request, $booking);

        $payment = DB::transaction(function () use ($data, $fees, $booking) {
            $payment = Payment::create($data + [
                'booking_id'     => $booking->id,
                'invoice_number' => Payment::nextInvoiceNumber(),
            ]);
            $payment->fees()->createMany($fees);

            return $payment;
        });

        return redirect()->route('payments.show', $payment)->with('success', 'Pembayaran tersimpan. Invoice siap dicetak.');
    }

    public function show(Payment $payment)
    {
        $payment->load('booking.customer.infoSource', 'booking.room', 'fees');

        return view('payments.show', ['payment' => $payment]);
    }

    public function edit(Payment $payment)
    {
        $payment->load('booking.customer', 'booking.room', 'fees');

        return view('payments.edit', [
            'payment'     => $payment,
            'methods'     => Payment::METHODS,
            'suggestions' => Payment::FEE_SUGGESTIONS,
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $payment->load('booking');
        [$data, $fees] = $this->validated($request, $payment->booking);

        DB::transaction(function () use ($payment, $data, $fees) {
            $payment->update($data);
            $payment->fees()->delete();
            $payment->fees()->createMany($fees);
        });

        return redirect()->route('payments.show', $payment)->with('success', 'Pembayaran diperbarui.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete(); // biaya lain ikut terhapus (cascade)

        return redirect()->route('payments.index')->with('success', 'Pembayaran dihapus.');
    }

    /** Invoice PDF. Tambahkan ?download=1 untuk mengunduh langsung. */
    public function invoice(Request $request, Payment $payment)
    {
        $payment->load('booking.customer', 'booking.room', 'fees');

        $pdf = Pdf::loadView('payments.invoice', ['payment' => $payment])->setPaper('a4');
        $filename = 'invoice-' . $payment->invoice_number . '.pdf';

        return $request->boolean('download') ? $pdf->download($filename) : $pdf->stream($filename);
    }

    /**
     * @return array{0: array, 1: array} [data pembayaran, daftar biaya lain]
     */
    private function validated(Request $request, Booking $booking): array
    {
        // Buang baris biaya lain yang kosong sebelum divalidasi
        $request->merge([
            'fees' => collect($request->input('fees', []))
                ->filter(fn ($f) => filled($f['description'] ?? null) || filled($f['amount'] ?? null))
                ->values()
                ->all(),
        ]);

        $data = $request->validate([
            'payment_date'       => ['required', 'date'],
            'method'             => ['required', Rule::in(array_keys(Payment::METHODS))],
            'amount_paid'        => ['required', 'integer', 'min:0'],
            'notes'              => ['nullable', 'string', 'max:500'],
            'fees'               => ['nullable', 'array'],
            'fees.*.description' => ['required', 'string', 'max:100'],
            'fees.*.amount'      => ['required', 'integer', 'min:0'],
        ], ValidationMessages::all(), ValidationMessages::attributes());

        $fees = collect($data['fees'] ?? [])
            ->map(fn ($f) => ['description' => $f['description'], 'amount' => (int) $f['amount']])
            ->all();
        unset($data['fees']);

        $grandTotal = $booking->room_total + array_sum(array_column($fees, 'amount'));

        if ($data['amount_paid'] > $grandTotal) {
            throw ValidationException::withMessages([
                'amount_paid' => 'Jumlah dibayar melebihi total tagihan (Rp ' . number_format($grandTotal, 0, ',', '.') . ').',
            ]);
        }

        return [$data, $fees];
    }
}
