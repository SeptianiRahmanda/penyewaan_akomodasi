<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu booking = satu pembayaran/invoice
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 20)->unique();
            $table->foreignId('booking_id')->unique()->constrained('bookings')->cascadeOnDelete();
            $table->date('payment_date');
            $table->string('method', 20); // cash | transfer | qris | card
            $table->unsignedBigInteger('amount_paid')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Biaya lain-lain (sarapan, extra bed, laundry, pajak, dll)
        Schema::create('payment_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->string('description', 100);
            $table->unsignedBigInteger('amount');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_fees');
        Schema::dropIfExists('payments');
    }
};
