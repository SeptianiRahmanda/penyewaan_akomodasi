<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $payment->invoice_number }}</title>
    <style>
        @page { margin: 36px 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 22px; margin: 0; letter-spacing: 2px; }
        .muted { color: #666; }
        .header { width: 100%; border-bottom: 2px solid #222; padding-bottom: 10px; margin-bottom: 18px; }
        .header td { vertical-align: top; }
        .right { text-align: right; }
        .info { width: 100%; margin-bottom: 18px; }
        .info td { vertical-align: top; width: 50%; padding: 0; }
        .label { font-size: 9px; text-transform: uppercase; color: #888; letter-spacing: 1px; margin-bottom: 3px; }
        table.items { width: 100%; border-collapse: collapse; }
        table.items th { background: #222; color: #fff; text-align: left; padding: 7px 8px; font-size: 10px; }
        table.items td { padding: 7px 8px; border-bottom: 1px solid #ddd; }
        table.items .num { text-align: right; white-space: nowrap; }
        table.totals { width: 55%; margin-left: 45%; margin-top: 12px; border-collapse: collapse; }
        table.totals td { padding: 5px 8px; }
        table.totals .grand td { border-top: 2px solid #222; font-weight: bold; font-size: 13px; }
        .status { display: inline-block; padding: 3px 10px; border: 2px solid; font-weight: bold; font-size: 12px; letter-spacing: 1px; }
        .status.lunas { color: #198754; border-color: #198754; }
        .status.sebagian { color: #b58100; border-color: #b58100; }
        .status.belum { color: #dc3545; border-color: #dc3545; }
        .footer { margin-top: 40px; text-align: center; color: #888; font-size: 10px; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
@php $b = $payment->booking; $no = 1; @endphp

<table class="header">
    <tr>
        <td>
            <h1>INVOICE</h1>
            <div class="muted">{{ config('app.name') }}</div>
        </td>
        <td class="right">
            <strong>{{ $payment->invoice_number }}</strong><br>
            <span class="muted">Tanggal: {{ $payment->payment_date->format('d/m/Y') }}</span><br>
            <span class="muted">Booking: {{ $b->code }}</span>
        </td>
    </tr>
</table>

<table class="info">
    <tr>
        <td>
            <div class="label">Ditagihkan kepada</div>
            <strong>{{ $b->customer->name }}</strong><br>
            {{ $b->customer->phone }}<br>
            @if ($b->customer->email) {{ $b->customer->email }}<br> @endif
            @if ($b->customer->address) {{ $b->customer->address }} @endif
        </td>
        <td>
            <div class="label">Detail menginap</div>
            Kamar {{ $b->room->room_number }} ({{ $b->room->type }})<br>
            Check-in: {{ $b->check_in->format('d/m/Y') }}<br>
            Check-out: {{ $b->check_out->format('d/m/Y') }}<br>
            {{ $b->nights }} malam · {{ $b->guests }} tamu
        </td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr><th style="width:5%">No</th><th>Deskripsi</th><th class="num" style="width:22%">Jumlah</th></tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $no++ }}</td>
            <td>Sewa kamar {{ $b->room->room_number }} — {{ $b->nights }} malam × @rupiah($b->price_per_night)</td>
            <td class="num">@rupiah($b->room_total)</td>
        </tr>
        @foreach ($payment->fees as $fee)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $fee->description }}</td>
                <td class="num">@rupiah($fee->amount)</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="totals">
    <tr><td>Sewa kamar</td><td class="num right">@rupiah($payment->room_total)</td></tr>
    <tr><td>Biaya lain-lain</td><td class="right">@rupiah($payment->fees_total)</td></tr>
    <tr class="grand"><td>Total tagihan</td><td class="right">@rupiah($payment->grand_total)</td></tr>
    <tr><td>Dibayar ({{ $payment->method_label }})</td><td class="right">@rupiah($payment->amount_paid)</td></tr>
    <tr><td><strong>Sisa tagihan</strong></td><td class="right"><strong>@rupiah($payment->balance)</strong></td></tr>
</table>

<p style="margin-top:18px;">
    <span class="status {{ $payment->status }}">{{ strtoupper($payment->status_label) }}</span>
</p>

@if ($payment->notes)
    <p class="muted">Catatan: {{ $payment->notes }}</p>
@endif

<div class="footer">Terima kasih telah menginap bersama kami.</div>
</body>
</html>
