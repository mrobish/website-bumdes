@php
    $record = $getRecord();
    $entries = $record ? $record->journalEntries()->with('account')->get() : collect();
@endphp

<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b">
                <th class="px-4 py-2 text-left">Kode Akun</th>
                <th class="px-4 py-2 text-left">Nama Akun</th>
                <th class="px-4 py-2 text-left">Uraian</th>
                <th class="px-4 py-2 text-right">Debet</th>
                <th class="px-4 py-2 text-right">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $entry->account_code }}</td>
                    <td class="px-4 py-2">{{ $entry->account->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $entry->description ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">
                        @if($entry->debit > 0)
                            Rp {{ number_format($entry->debit, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="px-4 py-2 text-right">
                        @if($entry->credit > 0)
                            Rp {{ number_format($entry->credit, 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">Belum ada jurnal</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="border-b-2 font-bold">
                <td colspan="3" class="px-4 py-2">Total</td>
                <td class="px-4 py-2 text-right">Rp {{ number_format($entries->sum('debit'), 0, ',', '.') }}</td>
                <td class="px-4 py-2 text-right">Rp {{ number_format($entries->sum('credit'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>
