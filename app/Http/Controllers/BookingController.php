<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Room;
use App\Support\ValidationMessages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with('customer', 'room', 'payment.fees')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->q, function ($query, $search) {
                $query->where(fn ($w) => $w->where('code', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%")));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('bookings.index', ['bookings' => $bookings]);
    }

    public function create(Request $request)
    {
        return view('bookings.create', $this->formData() + [
            'selectedCustomer' => $request->integer('customer_id') ?: null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['code'] = Booking::nextCode();

        $booking = Booking::create($data);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking berhasil dibuat. Lanjutkan dengan membuat pembayaran.');
    }

    public function show(Booking $booking)
    {
        $booking->load('customer.infoSource', 'room', 'payment.fees');

        return view('bookings.show', ['booking' => $booking]);
    }

    public function edit(Booking $booking)
    {
        return view('bookings.edit', $this->formData() + ['booking' => $booking]);
    }

    public function update(Request $request, Booking $booking)
    {
        $booking->update($this->validated($request, $booking));

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking diperbarui.');
    }

    public function destroy(Booking $booking)
    {
        if ($booking->payment()->exists()) {
            return back()->with('error', 'Booking sudah memiliki pembayaran. Hapus pembayarannya dulu, atau ubah status booking menjadi Dibatalkan.');
        }

        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Booking dihapus.');
    }

    private function formData(): array
    {
        return [
            'customers' => Customer::orderBy('name')->get(),
            'rooms'     => Room::orderBy('room_number')->get(),
            'statuses'  => Booking::STATUSES,
        ];
    }

    private function validated(Request $request, ?Booking $booking = null): array
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'room_id'     => ['required', 'exists:rooms,id'],
            'check_in'    => ['required', 'date'],
            'check_out'   => ['required', 'date', 'after:check_in'],
            'guests'      => ['required', 'integer', 'min:1'],
            'status'      => ['required', Rule::in(array_keys(Booking::STATUSES))],
            'notes'       => ['nullable', 'string', 'max:500'],
        ], ValidationMessages::all(), ValidationMessages::attributes());

        $room = Room::findOrFail($data['room_id']);
        $roomChanged = ! $booking || (int) $booking->room_id !== (int) $room->id;

        if ($roomChanged && $data['status'] !== 'cancelled' && $room->status === 'maintenance') {
            throw ValidationException::withMessages(['room_id' => 'Kamar ini sedang dalam perbaikan.']);
        }

        if ($data['guests'] > $room->capacity) {
            throw ValidationException::withMessages(['guests' => "Jumlah tamu melebihi kapasitas kamar ({$room->capacity} orang)."]);
        }

        if ($data['status'] !== 'cancelled'
            && Booking::overlapping($room->id, $data['check_in'], $data['check_out'], $booking?->id)->exists()) {
            throw ValidationException::withMessages(['room_id' => 'Kamar sudah dibooking pada tanggal tersebut.']);
        }

        // Harga per malam disimpan saat booking dibuat, supaya perubahan harga kamar tidak mengubah booking lama.
        $price = ($booking && ! $roomChanged) ? $booking->price_per_night : $room->price_per_night;
        $nights = (int) Carbon::parse($data['check_in'])->diffInDays(Carbon::parse($data['check_out']));

        $data['nights'] = $nights;
        $data['price_per_night'] = $price;
        $data['room_total'] = $nights * $price;

        return $data;
    }
}
