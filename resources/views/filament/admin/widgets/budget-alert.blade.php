<div>
@if($this->hasAlerts())
    <div style="margin-bottom: 16px;">
        <div style="background: linear-gradient(135deg, #fef3c7, #fde68a); border-radius: 12px; padding: 16px; border-left: 4px solid #f59e0b;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                <span style="font-size: 20px;">🔔</span>
                <span style="font-weight: 700; font-size: 16px; color: #92400e;">Peringatan Keuangan</span>
            </div>

            {{-- Budget Alerts --}}
            @if(!empty($alerts))
                <div style="margin-bottom: 12px;">
                    <div style="font-weight: 600; font-size: 13px; color: #92400e; margin-bottom: 6px;">📊 Anggaran</div>
                    @foreach($alerts as $alert)
                        <div style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: white; border-radius: 8px; margin-bottom: 4px; border-left: 3px solid {{ $alert['type'] === 'danger' ? '#ef4444' : '#f59e0b' }};">
                            <span>{{ $alert['icon'] }}</span>
                            <div style="flex: 1;">
                                <div style="font-size: 13px; font-weight: 500;">{{ $alert['message'] }}</div>
                                <div style="font-size: 11px; color: #6b7280;">{{ $alert['unit'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Piutang Overdue --}}
            @if(!empty($piutangOverdue))
                <div style="margin-bottom: 12px;">
                    <div style="font-weight: 600; font-size: 13px; color: #92400e; margin-bottom: 6px;">💰 Piutang Jatuh Tempo</div>
                    @foreach($piutangOverdue as $p)
                        <div style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: white; border-radius: 8px; margin-bottom: 4px; border-left: 3px solid #ef4444;">
                            <span>🔴</span>
                            <div style="flex: 1;">
                                <div style="font-size: 13px; font-weight: 500;">{{ $p['name'] }} — Rp {{ number_format($p['amount'], 0, ',', '.') }}</div>
                                <div style="font-size: 11px; color: #6b7280;">Jatuh tempo: {{ $p['due_date'] }} | {{ $p['unit'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Hutang Overdue --}}
            @if(!empty($hutangOverdue))
                <div style="margin-bottom: 12px;">
                    <div style="font-weight: 600; font-size: 13px; color: #92400e; margin-bottom: 6px;">📋 Hutang Jatuh Tempo</div>
                    @foreach($hutangOverdue as $h)
                        <div style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: white; border-radius: 8px; margin-bottom: 4px; border-left: 3px solid #ef4444;">
                            <span>🔴</span>
                            <div style="flex: 1;">
                                <div style="font-size: 13px; font-weight: 500;">{{ $h['name'] }} — Rp {{ number_format($h['amount'], 0, ',', '.') }}</div>
                                <div style="font-size: 11px; color: #6b7280;">Jatuh tempo: {{ $h['due_date'] }} | {{ $h['unit'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
</div>
