<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; }
        .page { padding: 20px 25px; }
        
        /* Kop Surat */
        .kop { text-align: center; border-bottom: 3px solid {{ $primaryColor }}; padding-bottom: 10px; margin-bottom: 15px; }
        .kop-line1 { font-size: 13px; font-weight: bold; color: {{ $primaryColor }}; text-transform: uppercase; }
        .kop-line2 { font-size: 15px; font-weight: bold; color: {{ $primaryColor }}; text-transform: uppercase; margin: 3px 0; }
        .kop-line3 { font-size: 9px; color: #555; }
        
        /* Title */
        .title { text-align: center; margin: 15px 0; }
        .title h2 { font-size: 14px; font-weight: bold; color: {{ $primaryColor }}; text-transform: uppercase; border-bottom: 2px solid {{ $primaryColor }}; display: inline-block; padding-bottom: 3px; }
        .title .period { font-size: 10px; color: #666; margin-top: 3px; }
        
        /* Table */
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table th { background: {{ $primaryColor }}; color: white; padding: 6px 8px; font-size: 9px; text-transform: uppercase; text-align: center; }
        table td { padding: 5px 8px; border-bottom: 1px solid #e5e5e5; font-size: 9px; }
        table tr:nth-child(even) { background: #f8f9fa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .total-row { background: {{ $accentColor }}20 !important; font-weight: bold; }
        .total-row td { border-top: 2px solid {{ $primaryColor }}; }
        
        /* Tanda Tangan */
        .signature-section { margin-top: 30px; display: flex; justify-content: space-between; }
        .signature-box { width: 30%; text-align: center; }
        .signature-box .date { margin-bottom: 50px; }
        .signature-box .name { font-weight: bold; border-bottom: 1px solid #333; display: inline-block; min-width: 120px; padding-bottom: 2px; }
        .signature-box .jabatan { font-size: 9px; color: #555; margin-top: 2px; }
        .signature-box .nip { font-size: 8px; color: #777; }
        
        /* Footer */
        .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="page">
        <!-- Kop Surat -->
        <div class="kop">
            <div class="kop-line1">{{ $setting->pdf_header_line1 ?? 'PEMERINTAH KABUPATEN ...' }}</div>
            <div class="kop-line2">{{ $setting->pdf_header_line2 ?? ($setting->bumdes_name ?? 'BUMDes') }}</div>
            <div class="kop-line3">{{ $setting->pdf_header_line3 ?? '' }}</div>
        </div>

        @yield('content')

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="date">Karangmekar, {{ now()->format('d F Y') }}</div>
                <div class="name">{{ $setting->direktur_name ?? 'Direktur BUMDes' }}</div>
                <div class="jabatan">Direktur BUMDes</div>
            </div>
            <div class="signature-box">
                <div class="date">&nbsp;</div>
                <div class="name">{{ $setting->sekretaris_name ?? 'Sekretaris' }}</div>
                <div class="jabatan">Sekretaris</div>
            </div>
            <div class="signature-box">
                <div class="date">&nbsp;</div>
                <div class="name">{{ $setting->bendahara_umum_name ?? 'Bendahara' }}</div>
                <div class="jabatan">Bendahara</div>
            </div>
        </div>

        <div class="footer">
            {{ $setting->pdf_footer_text ?? '' }} | Dicetak otomatis oleh Sistem BUMDes
        </div>
    </div>
</body>
</html>
