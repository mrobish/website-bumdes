@extends('pdf.layout')

@section('content')
<div class="title">
    <h2>Jurnal Umum</h2>
    <div class="period">{{ $period }}</div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:10%">Tanggal</th>
            <th style="width:12%">No. Ref</th>
            <th style="width:10%">Kode</th>
            <th style="width:28%">Keterangan</th>
            <th style="width:20%">Debit</th>
            <th style="width:20%">Kredit</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $trx)
            @foreach($trx->journalEntries as $i => $je)
            <tr>
                @if($i === 0)
                    <td class="text-center" rowspan="{{ $trx->journalEntries->count() }}">{{ $trx->transaction_date->format('d/m/Y') }}</td>
                    <td class="text-center" rowspan="{{ $trx->journalEntries->count() }}">{{ $trx->transaction_number }}</td>
                @endif
                <td class="text-center">{{ $je->account->code ?? '-' }}</td>
                <td>{{ $i === 0 ? $trx->description : '' }}</td>
                <td class="text-right">{{ $je->debit > 0 ? \App\Services\PdfReportService::formatRupiah($je->debit) : '' }}</td>
                <td class="text-right">{{ $je->credit > 0 ? \App\Services\PdfReportService::formatRupiah($je->credit) : '' }}</td>
            </tr>
            @endforeach
        @empty
        <tr>
            <td colspan="6" class="text-center">Tidak ada data jurnal</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
