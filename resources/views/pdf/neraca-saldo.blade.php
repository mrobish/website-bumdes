@extends('pdf.layout')

@section('content')
<div class="title">
    <h2>Neraca Saldo</h2>
    <div class="period">{{ $period }}</div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:12%">Kode</th>
            <th style="width:48%">Nama Akun</th>
            <th style="width:20%">Debit</th>
            <th style="width:20%">Kredit</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $row)
        <tr>
            <td class="text-center">{{ $row['code'] }}</td>
            <td>{{ $row['name'] }}</td>
            <td class="text-right">{{ $row['debit'] > 0 ? \App\Services\PdfReportService::formatRupiah($row['debit']) : '' }}</td>
            <td class="text-right">{{ $row['credit'] > 0 ? \App\Services\PdfReportService::formatRupiah($row['credit']) : '' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">Tidak ada data</td>
        </tr>
        @endforelse
        <tr class="total-row">
            <td colspan="2" class="text-right font-bold">TOTAL</td>
            <td class="text-right font-bold">{{ \App\Services\PdfReportService::formatRupiah($totalDebit) }}</td>
            <td class="text-right font-bold">{{ \App\Services\PdfReportService::formatRupiah($totalCredit) }}</td>
        </tr>
    </tbody>
</table>
@endsection
