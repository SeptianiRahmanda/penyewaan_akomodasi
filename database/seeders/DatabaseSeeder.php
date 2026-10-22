<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\InfoSource;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun login awal. Ganti password-nya setelah masuk pertama kali.
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );

        // Sumber informasi akomodasi
        $sources = [];
        foreach ([
            'Instagram', 'Facebook', 'TikTok', 'Google / Google Maps', 'Traveloka',
            'Tiket.com', 'Booking.com', 'Agoda', 'Teman / Keluarga', 'Datang Langsung (Walk-in)', 'Lainnya',
        ] as $name) {
            $sources[$name] = InfoSource::firstOrCreate(['name' => $name]);
        }

        // Kamar contoh
        $rooms = [];
        foreach ([
            ['101', 'Standard', 2, 350000],
            ['102', 'Standard', 2, 350000],
            ['201', 'Deluxe', 3, 550000],
            ['202', 'Deluxe', 3, 550000],
            ['301', 'Suite', 4, 900000],
            ['V01', 'Villa', 6, 1500000],
        ] as [$number, $type, $capacity, $price]) {
            $rooms[$number] = Room::firstOrCreate(
                ['room_number' => $number],
                ['type' => $type, 'capacity' => $capacity, 'price_per_night' => $price, 'status' => 'available']
            );
        }

        // Customer contoh
        $customers = [];
        foreach ([
            ['Made Wirawan', '081234567890', 'made@example.com', 'Instagram'],
            ['Putri Ayu', '082345678901', 'putri@example.com', 'Traveloka'],
            ['John Smith', '085678901234', 'john@example.com', 'Booking.com'],
            ['Komang Dewi', '087890123456', null, 'Teman / Keluarga'],
        ] as [$name, $phone, $email, $source]) {
            $customers[$name] = Customer::firstOrCreate(
                ['phone' => $phone],
                ['name' => $name, 'email' => $email, 'info_source_id' => $sources[$source]->id]
            );
        }

        if (Booking::count() > 0) {
            return;
        }

        // Booking + pembayaran contoh
        $make = function (string $customer, string $room, string $in, string $out, int $guests, string $status) use ($customers, $rooms) {
            $r = $rooms[$room];
            $nights = (int) \Carbon\Carbon::parse($in)->diffInDays(\Carbon\Carbon::parse($out));

            return Booking::create([
                'code'            => Booking::nextCode(),
                'customer_id'     => $customers[$customer]->id,
                'room_id'         => $r->id,
                'check_in'        => $in,
                'check_out'       => $out,
                'guests'          => $guests,
                'nights'          => $nights,
                'price_per_night' => $r->price_per_night,
                'room_total'      => $nights * $r->price_per_night,
                'status'          => $status,
            ]);
        };

        $b1 = $make('Made Wirawan', '201', '2026-10-18', '2026-10-21', 2, 'checked_in');
        $b2 = $make('Putri Ayu', '101', '2026-10-25', '2026-10-27', 2, 'confirmed');
        $make('John Smith', '301', '2026-11-02', '2026-11-05', 3, 'pending');

        $p1 = Payment::create([
            'invoice_number' => Payment::nextInvoiceNumber(),
            'booking_id'     => $b1->id,
            'payment_date'   => '2026-10-18',
            'method'         => 'transfer',
            'amount_paid'    => 1800000,
        ]);
        $p1->fees()->createMany([
            ['description' => 'Sarapan', 'amount' => 150000],
            ['description' => 'Extra bed', 'amount' => 100000],
            ['description' => 'Pajak & service', 'amount' => 165000],
        ]);

        Payment::create([
            'invoice_number' => Payment::nextInvoiceNumber(),
            'booking_id'     => $b2->id,
            'payment_date'   => '2026-10-20',
            'method'         => 'qris',
            'amount_paid'    => 300000, // DP
            'notes'          => 'DP awal, sisa dibayar saat check-in.',
        ])->fees()->create(['description' => 'Pajak & service', 'amount' => 70000]);
    }
}
