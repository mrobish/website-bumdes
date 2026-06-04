<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 15mm 20mm 20mm 20mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; }
        
        /* ===== KOP SURAT ===== */
        .kop { text-align: center; padding-bottom: 8px; margin-bottom: 12px; border-bottom: 4px double {{ $primaryColor }}; }
        .kop-inner { display: table; width: 100%; }
        .kop-logo { display: table-cell; width: 80px; vertical-align: middle; text-align: center; }
        .kop-logo img { max-width: 65px; max-height: 65px; }
        .kop-text { display: table-cell; vertical-align: middle; text-align: center; }
        .kop-line1 { font-size: 11px; font-weight: bold; color: {{ $primaryColor }}; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-line2 { font-size: 14px; font-weight: bold; color: {{ $primaryColor }}; text-transform: uppercase; margin: 2px 0; letter-spacing: 1px; }
        .kop-line3 { font-size: 8.5px; color: #555; line-height: 1.4; }
        .kop-line4 { font-size: 8px; color: #777; margin-top: 2px; }
        
        /* ===== TITLE ===== */
        .title { text-align: center; margin: 12px 0 8px; }
        .title h2 { font-size: 12px; font-weight: bold; color: {{ $primaryColor }}; text-transform: uppercase; border-bottom: 2px solid {{ $primaryColor }}; display: inline-block; padding-bottom: 2px; }
        .title .period { font-size: 9px; color: #666; margin-top: 3px; }
        
        /* ===== TABLE ===== */
        table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        table th { background: {{ $primaryColor }}; color: white; padding: 5px 6px; font-size: 8.5px; text-transform: uppercase; text-align: center; border: 1px solid {{ $primaryColor }}; }
        table td { padding: 4px 6px; border: 1px solid #ddd; font-size: 9px; }
        table tr:nth-child(even) { background: #f8f9fa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .total-row { background: {{ $accentColor }}20 !important; font-weight: bold; }
        .total-row td { border-top: 2px solid {{ $primaryColor }}; }
        .subtotal { background: #e8f0fe !important; font-weight: bold; }
        
        /* ===== TANDA TANGAN ===== */
        .ttd-section { margin-top: 25px; page-break-inside: avoid; }
        .ttd-header { text-align: right; margin-bottom: 5px; font-size: 9px; color: #555; }
        
        /* Baris "Mengetahui" di atas (Pengawas + Penasihat) */
        .ttd-top { display: table; width: 100%; margin-bottom: 15px; }
        .ttd-top .ttd-box { display: table-cell; width: 50%; text-align: center; vertical-align: top; }
        
        /* Baris tanda tangan utama (Direktur, Sekretaris, Bendahara) */
        .ttd-main { display: table; width: 100%; }
        .ttd-main .ttd-box { display: table-cell; width: 33.33%; text-align: center; vertical-align: top; }
        
        .ttd-box .label { font-size: 9px; font-weight: bold; color: #333; margin-bottom: 3px; }
        .ttd-box .date { font-size: 9px; margin-bottom: 45px; }
        .ttd-box .name { font-weight: bold; font-size: 10px; border-bottom: 1px solid #333; display: inline-block; min-width: 130px; padding-bottom: 2px; }
        .ttd-box .jabatan { font-size: 9px; color: #333; margin-top: 2px; }
        .ttd-box .nip { font-size: 8px; color: #666; }
        
        /* ===== FOOTER ===== */
        .footer { margin-top: 15px; text-align: center; font-size: 7.5px; color: #999; border-top: 1px solid #ddd; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="page">
        <!-- ===== KOP SURAT ===== -->
        <div class="kop">
            <div class="kop-inner">
                @if($setting->logo_path ?? null)
                <div class="kop-logo">
                    <img src="{{ public_path('storage/' . $setting->logo_path) }}" alt="Logo">
                </div>
                @endif
                <div class="kop-text">
                    <div class="kop-line1">{{ $setting->pdf_header_line1 ?? '' }}</div>
                    <div class="kop-line2">{{ $setting->pdf_header_line2 ?? ($setting->bumdes_name ?? 'BUMDes') }}</div>
                    <div class="kop-line3">{{ $setting->pdf_header_line3 ?? '' }}</div>
                    @if(($setting->phone ?? null) || ($setting->email ?? null))
                    <div class="kop-line4">
                        @if($setting->phone ?? null) Telp: {{ $setting->phone }} @endif
                        @if(($setting->phone ?? null) && ($setting->email ?? null)) | @endif
                        @if($setting->email ?? null) Email: {{ $setting->email }} @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @yield('content')

        <!-- ===== TANDA TANGAN ===== -->
        <div class="ttd-section">
            <div class="ttd-header">{{ $setting->bumdes_village ?? '' }}, {{ now()->format('d F Y') }}</div>
            
            <!-- Baris atas: Mengetahui Pengawas & Penasihat -->
            <div class="ttd-top">
                @if(($setting->pengawas1_name ?? null) || ($setting->pengawas2_name ?? null))
                <div class="ttd-box">
                    <div class="label">Mengetahui,</div>
                    <div class="date">&nbsp;</div>
                    <div class="name">{{ $setting->pengawas1_name ?? 'Pengawas 1' }}</div>
                    <div class="jabatan">Pengawas BUMDes</div>
                </div>
                @endif
                @if($setting->kepala_desa_name ?? null)
                <div class="ttd-box">
                    <div class="label">Mengetahui,</div>
                    <div class="date">&nbsp;</div>
                    <div class="name">{{ $setting->kepala_desa_name ?? 'Penasihat' }}</div>
                    <div class="jabatan">Penasihat BUMDes<br>(Kepala Desa)</div>
                </div>
                @endif
            </div>
            
            <!-- Baris bawah: Pelaksana Operasional -->
            <div class="ttd-main">
                <div class="ttd-box">
                    <div class="label">&nbsp;</div>
                    <div class="date">&nbsp;</div>
                    <div class="name">{{ $setting->direktur_name ?? 'Direktur BUMDes' }}</div>
                    <div class="jabatan">Direktur BUMDes</div>
                </div>
                <div class="ttd-box">
                    <div class="label">&nbsp;</div>
                    <div class="date">&nbsp;</div>
                    <div class="name">{{ $setting->sekretaris_name ?? 'Sekretaris' }}</div>
                    <div class="jabatan">Sekretaris</div>
                </div>
                <div class="ttd-box">
                    <div class="label">&nbsp;</div>
                    <div class="date">&nbsp;</div>
                    <div class="name">{{ $setting->bendahara_umum_name ?? 'Bendahara' }}</div>
                    <div class="jabatan">Bendahara</div>
                </div>
            </div>
        </div>

        <div class="footer">
            {{ $setting->pdf_footer_text ?? '' }} | Dicetak otomatis oleh Sistem {{ $setting->bumdes_name ?? 'BUMDes' }}
        </div>
    </div>
</body>
</html>
