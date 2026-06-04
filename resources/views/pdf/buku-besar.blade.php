@extends('pdf.layout')

@section('content')
<div class="title">
    <h2>Buku Besar</h2>
    <div class="period">{{ $account->code }} — {{ $account->name }} | {{ $period }}</div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:10%">Tanggal</th>
            <th style="width:10%">No. Ref</th>
            <th style="width:30%">Keterangan</th>
            <th style="width:16.6%">Debit</th>
            <th style="width:16.6%">Kredit</th>
            <th style="width:16.6%">Saldo</th>
        </tr>
    </thead>
    <tbody>
        @forelse($ledgerData as $row)
        <tr>
            <td class="text-center">{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
            <td class="text-center">{{ $row['ref'] }}</td>
            <td>{{ $row['description'] }}</td>
            <td class="text-right">{{ $row['debit'] > 0 ? \App\Services\PdfReportService::formatRupiah($row['debit']) : '' }}</td>
            <td class="text-right">{{ $row['credit'] > 0 ? \App\Services\PdfReportService::formatRupiah($row['credit']) : '' }}</td>
            <td class="text-right font-bold">{{ \App\Services\PdfReportService::formatRupiah(abs($row['balance'])) }}{{ $row['balance'] < 0 ? ' (D)' : '' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">Tidak ada transaksi</td>
        </tr>
        @endforelse
        <tr class="total-row">
            <td colspan="3" class="text-right font-bold">TOTAL</td>
            <td class="text-right font-bold">{{ \App\Services\PdfReportService::formatRupiah($totalDebit) }}</td>
            <td class="text-right font-bold">{{ \App\Services\PdfReportService::formatRupiah($totalCredit) }}</td>
            <td class="text-right font-bold">{{ \App\Services\PdfReportService::formatRupiah(abs($balance)) }}{{ $balance < 0 ? ' (D)' : '' }}</td>
        </tr>
    </tbody>
</table>
@endsection
