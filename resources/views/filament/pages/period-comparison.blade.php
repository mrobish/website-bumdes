<x-filament-panels::page>
    <style>
        .comparison-card { background: white; border-radius: 12px; padding: 20px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .comparison-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .comparison-title { font-size: 18px; font-weight: 700; }
        .comparison-table { width: 100%; border-collapse: collapse; }
        .comparison-table th, .comparison-table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #f0f0f0; }
        .comparison-table th { background: #f8fafc; font-weight: 600; font-size: 13px; color: #64748b; }
        .comparison-table td { font-size: 14px; }
        .comparison-table .amount { text-align: right; font-family: monospace; }
        .comparison-table .total-row { background: #f0fdf4; font-weight: 700; }
        .comparison-table .total-row.negative { background: #fef2f2; }
        .change-positive { color: #16a34a; font-weight: 600; }
        .change-negative { color: #dc2626; font-weight: 600; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
        .summary-item { background: #f8fafc; border-radius: 8px; padding: 16px; text-align: center; }
        .summary-label { font-size: 12px; color: #64748b; margin-bottom: 4px; }
        .summary-value { font-size: 20px; font-weight: 700; }
        .summary-value.positive { color: #16a34a; }
        .summary-value.negative { color: #dc2626; }
    </style>

    <div>
        {{ $this->form }}

        @if($loaded)
            @php
                $laba1 = $labaRugi1['laba_bersih'] ?? 0;
                $laba2 = $labaRugi2['laba_bersih'] ?? 0;
                $change = $laba2 - $laba1;
                $pctChange = $laba1 != 0 ? (($laba2 - $laba1) / abs($laba1)) * 100 : 0;
            @endphp

            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Laba Bersih (Periode 1)</div>
                    <div class="summary-value {{ $laba1 >= 0 ? 'positive' : 'negative' }}">
                        Rp {{ number_format($laba1, 0, ',', '.') }}
                    </div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Laba Bersih (Periode 2)</div>
                    <div class="summary-value {{ $laba2 >= 0 ? 'positive' : 'negative' }}">
                        Rp {{ number_format($laba2, 0, ',', '.') }}
                    </div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Perubahan</div>
                    <div class="summary-value {{ $change >= 0 ? 'positive' : 'negative' }}">
                        {{ $change >= 0 ? '+' : '' }}Rp {{ number_format($change, 0, ',', '.') }}
                        <div style="font-size: 14px; margin-top: 4px;">
                            {{ $pctChange >= 0 ? '📈' : '📉' }} {{ number_format(abs($pctChange), 1) }}%
                        </div>
                    </div>
                </div>
            </div>

            {{-- Laba/Rugi Comparison --}}
            <div class="comparison-card">
                <div class="comparison-title">📊 Perbandingan Laba/Rugi</div>
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Akun</th>
                            <th class="amount">{{ \Carbon\Carbon::parse($period1 . '-01')->format('F Y') }}</th>
                            <th class="amount">{{ \Carbon\Carbon::parse($period2 . '-01')->format('F Y') }}</th>
                            <th class="amount">Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="background: #f0fdf4;">
                            <td colspan="4" style="font-weight: 700; color: #166534;">📈 Pendapatan</td>
                        </tr>
                        @foreach(($labaRugi1['pendapatan'] ?? []) as $i => $item)
                            @php
                                $item2 = ($labaRugi2['pendapatan'] ?? [])[$i] ?? null;
                                $val1 = $item['jumlah'];
                                $val2 = $item2['jumlah'] ?? 0;
                                $diff = $val2 - $val1;
                            @endphp
                            <tr>
                                <td>{{ $item['kode'] }} — {{ $item['nama'] }}</td>
                                <td class="amount">Rp {{ number_format($val1, 0, ',', '.') }}</td>
                                <td class="amount">Rp {{ number_format($val2, 0, ',', '.') }}</td>
                                <td class="amount {{ $diff >= 0 ? 'change-positive' : 'change-negative' }}">
                                    {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td>Total Pendapatan</td>
                            <td class="amount">Rp {{ number_format($labaRugi1['total_pendapatan'] ?? 0, 0, ',', '.') }}</td>
                            <td class="amount">Rp {{ number_format($labaRugi2['total_pendapatan'] ?? 0, 0, ',', '.') }}</td>
                            @php $diff = ($labaRugi2['total_pendapatan'] ?? 0) - ($labaRugi1['total_pendapatan'] ?? 0); @endphp
                            <td class="amount {{ $diff >= 0 ? 'change-positive' : 'change-negative' }}">
                                {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                            </td>
                        </tr>

                        <tr style="background: #fef2f2;">
                            <td colspan="4" style="font-weight: 700; color: #991b1b;">📉 Beban</td>
                        </tr>
                        @foreach(($labaRugi1['beban'] ?? []) as $i => $item)
                            @php
                                $item2 = ($labaRugi2['beban'] ?? [])[$i] ?? null;
                                $val1 = $item['jumlah'];
                                $val2 = $item2['jumlah'] ?? 0;
                                $diff = $val2 - $val1;
                            @endphp
                            <tr>
                                <td>{{ $item['kode'] }} — {{ $item['nama'] }}</td>
                                <td class="amount">Rp {{ number_format($val1, 0, ',', '.') }}</td>
                                <td class="amount">Rp {{ number_format($val2, 0, ',', '.') }}</td>
                                <td class="amount {{ $diff <= 0 ? 'change-positive' : 'change-negative' }}">
                                    {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td>Total Beban</td>
                            <td class="amount">Rp {{ number_format($labaRugi1['total_beban'] ?? 0, 0, ',', '.') }}</td>
                            <td class="amount">Rp {{ number_format($labaRugi2['total_beban'] ?? 0, 0, ',', '.') }}</td>
                            @php $diff = ($labaRugi2['total_beban'] ?? 0) - ($labaRugi1['total_beban'] ?? 0); @endphp
                            <td class="amount {{ $diff <= 0 ? 'change-positive' : 'change-negative' }}">
                                {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                            </td>
                        </tr>

                        <tr class="total-row {{ $laba2 < 0 ? 'negative' : '' }}">
                            <td>💰 Laba Bersih</td>
                            <td class="amount">Rp {{ number_format($laba1, 0, ',', '.') }}</td>
                            <td class="amount">Rp {{ number_format($laba2, 0, ',', '.') }}</td>
                            @php $diff = $laba2 - $laba1; @endphp
                            <td class="amount {{ $diff >= 0 ? 'change-positive' : 'change-negative' }}">
                                {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Neraca Comparison --}}
            <div class="comparison-card">
                <div class="comparison-title">📋 Perbandingan Neraca</div>
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Akun</th>
                            <th class="amount">{{ \Carbon\Carbon::parse($period1 . '-01')->format('F Y') }}</th>
                            <th class="amount">{{ \Carbon\Carbon::parse($period2 . '-01')->format('F Y') }}</th>
                            <th class="amount">Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="background: #eff6ff;">
                            <td colspan="4" style="font-weight: 700; color: #1e40af;">🏠 Aset</td>
                        </tr>
                        @foreach(($neraca1['aset'] ?? []) as $item)
                            @php
                                $item2 = collect($neraca2['aset'] ?? [])->firstWhere('kode', $item['kode']);
                                $val1 = $item['jumlah'];
                                $val2 = $item2['jumlah'] ?? 0;
                                $diff = $val2 - $val1;
                            @endphp
                            <tr>
                                <td>{{ $item['kode'] }} — {{ $item['nama'] }}</td>
                                <td class="amount">Rp {{ number_format($val1, 0, ',', '.') }}</td>
                                <td class="amount">Rp {{ number_format($val2, 0, ',', '.') }}</td>
                                <td class="amount {{ $diff >= 0 ? 'change-positive' : 'change-negative' }}">
                                    {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td>Total Aset</td>
                            <td class="amount">Rp {{ number_format($neraca1['total_aset'] ?? 0, 0, ',', '.') }}</td>
                            <td class="amount">Rp {{ number_format($neraca2['total_aset'] ?? 0, 0, ',', '.') }}</td>
                            @php $diff = ($neraca2['total_aset'] ?? 0) - ($neraca1['total_aset'] ?? 0); @endphp
                            <td class="amount {{ $diff >= 0 ? 'change-positive' : 'change-negative' }}">
                                {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                            </td>
                        </tr>

                        <tr style="background: #fef2f2;">
                            <td colspan="4" style="font-weight: 700; color: #991b1b;">📋 Kewajiban</td>
                        </tr>
                        @foreach(($neraca1['kewajiban'] ?? []) as $item)
                            @php
                                $item2 = collect($neraca2['kewajiban'] ?? [])->firstWhere('kode', $item['kode']);
                                $val1 = $item['jumlah'];
                                $val2 = $item2['jumlah'] ?? 0;
                                $diff = $val2 - $val1;
                            @endphp
                            <tr>
                                <td>{{ $item['kode'] }} — {{ $item['nama'] }}</td>
                                <td class="amount">Rp {{ number_format($val1, 0, ',', '.') }}</td>
                                <td class="amount">Rp {{ number_format($val2, 0, ',', '.') }}</td>
                                <td class="amount {{ $diff <= 0 ? 'change-positive' : 'change-negative' }}">
                                    {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td>Total Kewajiban</td>
                            <td class="amount">Rp {{ number_format($neraca1['total_kewajiban'] ?? 0, 0, ',', '.') }}</td>
                            <td class="amount">Rp {{ number_format($neraca2['total_kewajiban'] ?? 0, 0, ',', '.') }}</td>
                            @php $diff = ($neraca2['total_kewajiban'] ?? 0) - ($neraca1['total_kewajiban'] ?? 0); @endphp
                            <td class="amount {{ $diff <= 0 ? 'change-positive' : 'change-negative' }}">
                                {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                            </td>
                        </tr>

                        <tr style="background: #f0fdf4;">
                            <td colspan="4" style="font-weight: 700; color: #166534;">💎 Ekuitas</td>
                        </tr>
                        @foreach(($neraca1['ekuitas'] ?? []) as $item)
                            @php
                                $item2 = collect($neraca2['ekuitas'] ?? [])->firstWhere('kode', $item['kode']);
                                $val1 = $item['jumlah'];
                                $val2 = $item2['jumlah'] ?? 0;
                                $diff = $val2 - $val1;
                            @endphp
                            <tr>
                                <td>{{ $item['kode'] }} — {{ $item['nama'] }}</td>
                                <td class="amount">Rp {{ number_format($val1, 0, ',', '.') }}</td>
                                <td class="amount">Rp {{ number_format($val2, 0, ',', '.') }}</td>
                                <td class="amount {{ $diff >= 0 ? 'change-positive' : 'change-negative' }}">
                                    {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td>Total Ekuitas</td>
                            <td class="amount">Rp {{ number_format($neraca1['total_ekuitas'] ?? 0, 0, ',', '.') }}</td>
                            <td class="amount">Rp {{ number_format($neraca2['total_ekuitas'] ?? 0, 0, ',', '.') }}</td>
                            @php $diff = ($neraca2['total_ekuitas'] ?? 0) - ($neraca1['total_ekuitas'] ?? 0); @endphp
                            <td class="amount {{ $diff >= 0 ? 'change-positive' : 'change-negative' }}">
                                {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-filament-panels::page>
