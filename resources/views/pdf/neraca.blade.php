@extends('pdf.layout')

@section('content')
<div class="title">
    <h2>Neraca (Laporan Posisi Keuangan)</h2>
    <div class="period">{{ $period }}</div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:12%">Kode</th>
            <th style="width:68%">Nama Akun</th>
            <th style="width:20%">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <!-- ASET -->
        <tr><td colspan="3" class="font-bold" style="background:#e8f0fe;">ASET</td></tr>
        @foreach($asetData as $row)
        <tr>
            <td class="text-center">{{ $row['code'] }}</td>
            <td>{{ $row['name'] }}</td>
            <td class="text-right">{{ \App\Services\PdfReportService::formatRupiah($row['amount']) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2" class="text-right font-bold">Total Aset</td>
            <td class="text-right font-bold">{{ \App\Services\PdfReportService::formatRupiah($totalAset) }}</td>
        </tr>

        <!-- KEWAJIBAN -->
        <tr><td colspan="3" class="font-bold" style="background:#fce8e6;">KEWAJIBAN</td></tr>
        @foreach($kewajibanData as $row)
        <tr>
            <td class="text-center">{{ $row['code'] }}</td>
            <td>{{ $row['name'] }}</td>
            <td class="text-right">{{ \App\Services\PdfReportService::formatRupiah($row['amount']) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2" class="text-right font-bold">Total Kewajiban</td>
            <td class="text-right font-bold">{{ \App\Services\PdfReportService::formatRupiah($totalKewajiban) }}</td>
        </tr>

        <!-- EKUITAS -->
        <tr><td colspan="3" class="font-bold" style="background:#e6f4ea;">EKUITAS</td></tr>
        @foreach($ekuitasData as $row)
        <tr>
            <td class="text-center">{{ $row['code'] }}</td>
            <td>{{ $row['name'] }}</td>
            <td class="text-right">{{ \App\Services\PdfReportService::formatRupiah($row['amount']) }}</td>
        </tr>
        @endforeach
        @if($labaDitahan != 0)
        <tr>
            <td class="text-center">3999</td>
            <td>Laba Ditahan</td>
            <td class="text-right">{{ \App\Services\PdfReportService::formatRupiah(abs($labaDitahan)) }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td colspan="2" class="text-right font-bold">Total Ekuitas</td>
            <td class="text-right font-bold">{{ \App\Services\PdfReportService::formatRupiah($totalEkuitas + $labaDitahan) }}</td>
        </tr>

        <!-- TOTAL KEWAJIBAN + EKUITAS -->
        <tr style="background:{{ $primaryColor }}10;">
            <td colspan="2" class="text-right font-bold" style="font-size:11px;">TOTAL KEWAJIBAN + EKUITAS</td>
            <td class="text-right font-bold" style="font-size:11px;">{{ \App\Services\PdfReportService::formatRupiah($totalKewajiban + $totalEkuitas + $labaDitahan) }}</td>
        </tr>
    </tbody>
</table>
@endsection
