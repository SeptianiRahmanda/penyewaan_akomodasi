<?php

namespace App\Support;

/**
 * Pesan validasi Bahasa Indonesia, dipakai di semua controller:
 * $request->validate($rules, ValidationMessages::all(), ValidationMessages::attributes());
 */
class ValidationMessages
{
    public static function all(): array
    {
        return [
            'required'    => ':attribute wajib diisi.',
            'string'      => ':attribute harus berupa teks.',
            'integer'     => ':attribute harus berupa angka bulat.',
            'date'        => ':attribute bukan tanggal yang valid.',
            'email'       => 'Format :attribute tidak valid.',
            'exists'      => ':attribute yang dipilih tidak valid.',
            'unique'      => ':attribute sudah digunakan.',
            'in'          => ':attribute yang dipilih tidak valid.',
            'after'       => ':attribute harus setelah tanggal check-in.',
            'max.string'  => ':attribute maksimal :max karakter.',
            'min.numeric' => ':attribute minimal :min.',
            'min.integer' => ':attribute minimal :min.',
            'max.numeric' => ':attribute maksimal :max.',
        ];
    }

    public static function attributes(): array
    {
        return [
            'name'              => 'Nama',
            'phone'             => 'No. HP',
            'email'             => 'Email',
            'id_number'         => 'No. identitas',
            'address'           => 'Alamat',
            'info_source_id'    => 'Sumber informasi',
            'room_number'       => 'Nomor kamar',
            'type'              => 'Tipe kamar',
            'capacity'          => 'Kapasitas',
            'price_per_night'   => 'Harga per malam',
            'status'            => 'Status',
            'description'       => 'Deskripsi',
            'customer_id'       => 'Customer',
            'room_id'           => 'Kamar',
            'check_in'          => 'Tanggal check-in',
            'check_out'         => 'Tanggal check-out',
            'guests'            => 'Jumlah tamu',
            'notes'             => 'Catatan',
            'booking_id'        => 'Booking',
            'payment_date'      => 'Tanggal pembayaran',
            'method'            => 'Metode pembayaran',
            'amount_paid'       => 'Jumlah dibayar',
            'fees.*.description' => 'Nama biaya lain',
            'fees.*.amount'      => 'Jumlah biaya lain',
        ];
    }
}
