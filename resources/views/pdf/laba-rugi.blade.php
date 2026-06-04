@extends('pdf.layout')

@section('content')
<div class="title">
    <h2>Laporan Laba Rugi</h2>
    <div class="period">{{ $period }}</div>
</div>

<!-- PENDAPATAN -->
<table>
    <thead>
        <tr>
            <th style="width:12%">Kode</th>
            <th style="width:68%">Nama Akun</th>
            <th style="width:20%">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <tr><td colspan="3" class="font-bold" style="background:#e8f0fe;">PENDAPATAN</td></tr>
        @foreach($pendapatanData as $row)
        <tr>
            <td class="text-center">{{ $row['code'] }}</td>
            <td>{{ $row['name'] }}</td>
            <td class="text-right">{{ \App\Services\PdfReportService::formatRupiah($row['amount']) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2" class="text-right font-bold">Total Pendapatan</td>
            <td class="text-right font-bold">{{ \App\Services\PdfReportService::formatRupiah($totalPendapatan) }}</td>
        </tr>

        <tr><td colspan="3" class="font-bold" style="background:#fce8e6;">BEBAN / BIAYA</td></tr>
        @foreach($bebanData as $row)
        <tr>
            <td class="text-center">{{ $row['code'] }}</td>
            <td>{{ $row['name'] }}</td>
            <td class="text-right">{{ \App\Services\PdfReportService::formatRupiah($row['amount']) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2" class="text-right font-bold">Total Beban</td>
            <td class="text-right font-bold">{{ \App\Services\PdfReportService::formatRupiah($totalBeban) }}</td>
        </tr>

        <tr style="background:{{ $labaBersih >= 0 ? '#e6f4ea' : '#fce8e6' }};">
            <td colspan="2" class="text-right font-bold" style="font-size:11px;">LABA / (RUGI) BERSIH</td>
            <td class="text-right font-bold" style="font-size:11px; color:{{ $labaBersih >= 0 ? 'green' : 'red' }};">
                {{ \App\Services\PdfReportService::formatRupiah(abs($labaBersih)) }}
                {{ $labaBersih < 0 ? '(Rugi)' : '' }}
            </td>
        </tr>
    </tbody>
</table>
@endsection
