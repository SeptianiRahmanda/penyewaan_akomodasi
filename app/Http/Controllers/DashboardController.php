<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\InfoSource;
use App\Models\Payment;
use App\Models\Room;

class DashboardController extends Controller
{
    public function index()
    {
        $activeBookings = Booking::whereIn('status', ['confirmed', 'checked_in'])->count();

        // Booking yang belum punya pembayaran, atau belum lunas
        $unpaid = Booking::with('payment.fees', 'customer', 'room')
            ->where('status', '!=', 'cancelled')
            ->get()
            ->filter(fn ($b) => ! $b->payment || $b->payment->status !== 'lunas');

        $sources = InfoSource::withCount('customers')->orderByDesc('customers_count')->orderBy('name')->get();

        return view('dashboard', [
            'totalCustomers' => Customer::count(),
            'totalRooms'     => Room::count(),
            'activeBookings' => $activeBookings,
            'revenue'        => (int) Payment::sum('amount_paid'),
            'unpaid'         => $unpaid->take(5),
            'unpaidCount'    => $unpaid->count(),
            'latest'         => Booking::with('customer', 'room')->latest()->take(5)->get(),
            'sources'        => $sources,
            'sourceMax'      => max(1, (int) $sources->max('customers_count')),
        ]);
    }
}
