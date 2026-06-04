<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BumdesSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        // Identitas BUMDes
        'bumdes_name',
        'village_name',
        'village_code',
        'bumdes_nib',
        'nomor_ahu',
        'established_date',
        'nomor_perdes',
        'tanggal_perdes',
        'file_perdes_path',
        'file_adart_path',
        
        // Alamat
        'bumdes_address',
        'bumdes_district',
        'bumdes_regency',
        'bumdes_province',
        'bumdes_postal_code',
        
        // Kontak
        'phone',
        'whatsapp',
        'email',
        'website',
        
        // Visi Misi & Tentang
        'vision',
        'mission',
        'about',
        
        // Logo & Foto
        'logo_path',
        'logo_white_path',
        'kantor_photo_path',
        'kegiatan_photo_path',
        'signature_photo_path',
        
        // Kop PDF
        'pdf_header_line1',
        'pdf_header_line2',
        'pdf_header_line3',
        'pdf_footer_text',
        'pdf_stamp_path',
        
        // Media Sosial
        'facebook',
        'instagram',
        'youtube',
        'tiktok',
        'twitter',
        
        // Motto
        'motto',

        // Informasi Bank
        'bank_name',
        'bank_account_number',
        'bank_account_name',

        // NPWP & Pejabat Desa
        'npwp',
        'kepala_desa_name',
        'kepala_desa_nip',
        'kaur_keuangan_name',
        'kaur_keuangan_nip',
        'bendahara_name',
        'bendahara_nip',

        // Pejabat PP 11/2021
        'pengawas1_name',
        'pengawas1_nip',
        'pengawas1_position',
        'pengawas2_name',
        'pengawas2_nip',
        'pengawas2_position',
        'direktur_name',
        'direktur_nip',
        'direktur_phone',

        // Warna Branding
        'motto_color',
        'primary_color',
        'secondary_color',
        'custom_css',

        // SEO
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_active',

        // Informasi Bank
        'bank_name',
        'bank_account_number',
        'bank_account_name',

        // NPWP
        'npwp',

        // Pejabat Desa
        'kepala_desa_name',
        'kepala_desa_nip',
        'kaur_keuangan_name',
        'kaur_keuangan_nip',
        'bendahara_name',
        'bendahara_nip',
    ];

    protected $casts = [
        'established_date' => 'date',
        'tanggal_perdes' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the first (and only) settings record
     */
    public static function getSettings()
    {
        return static::firstOrCreate([], [
            'bumdes_name' => 'BUMDes Keude Bakongan',
            'village_name' => 'Keude Bakongan',
            'bumdes_address' => 'Keude Bakongan, Kec. Bakongan',
            'bumdes_district' => 'Bakongan',
            'bumdes_regency' => 'Aceh Selatan',
            'bumdes_province' => 'Aceh',
            'phone' => '628123456789',
        ]);
    }

    // === Accessors ===

    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    public function getLogoWhiteUrlAttribute()
    {
        return $this->logo_white_path ? asset('storage/' . $this->logo_white_path) : null;
    }

    public function getKantorPhotoUrlAttribute()
    {
        return $this->kantor_photo_path ? asset('storage/' . $this->kantor_photo_path) : null;
    }

    public function getKegiatanPhotoUrlAttribute()
    {
        return $this->kegiatan_photo_path ? asset('storage/' . $this->kegiatan_photo_path) : null;
    }

    public function getFilePerdesUrlAttribute()
    {
        return $this->file_perdes_path ? asset('storage/' . $this->file_perdes_path) : null;
    }

    public function getFileAdartUrlAttribute()
    {
        return $this->file_adart_path ? asset('storage/' . $this->file_adart_path) : null;
    }

    public function getFullAddressAttribute()
    {
        return implode(', ', array_filter([
            $this->bumdes_address,
            $this->village_name,
            'Kec. ' . $this->bumdes_district,
            'Kab. ' . $this->bumdes_regency,
            $this->bumdes_province,
            $this->bumdes_postal_code,
        ]));
    }

    public function getPdfHeaderAttribute()
    {
        return array_filter([
            $this->pdf_header_line1,
            $this->pdf_header_line2,
            $this->pdf_header_line3,
        ]);
    }

    /**
     * Get formatted full address for official use (kop surat, etc.)
     * Format: Desa Karangmekar, Kec. Karangnunggal, Kab. Tasikmalaya, Prov. Jawa Barat 46181
     */
    public function getFullAddressFormattedAttribute(): string
    {
        $parts = array_filter([
            'Desa ' . $this->village_name,
            'Kec. ' . $this->bumdes_district,
            'Kab. ' . $this->bumdes_regency,
            'Prov. ' . $this->bumdes_province,
            $this->bumdes_postal_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get the full kop header lines for PDF generation
     */
    public function getKopHeaderAttribute(): array
    {
        return array_filter([
            $this->pdf_header_line1,
            $this->pdf_header_line2,
            $this->pdf_header_line3,
        ]);
    }

    /**
     * Get signature lines for tanda tangan (pejabat desa)
     * Returns array of [name, nip, title]
     */
    public function getSignatureLinesAttribute(): array
    {
        $lines = [];

        // Pembina (PP 11/2021)
        if ($this->kepala_desa_name) {
            $lines[] = [
                'name' => $this->kepala_desa_name,
                'nip' => $this->kepala_desa_nip,
                'title' => 'Pembina BUMDes',
            ];
        }

        // Pengawas 1 (PP 11/2021)
        if ($this->pengawas1_name) {
            $lines[] = [
                'name' => $this->pengawas1_name,
                'nip' => $this->pengawas1_nip,
                'title' => 'Pengawas BUMDes',
            ];
        }

        // Pengawas 2 (PP 11/2021)
        if ($this->pengawas2_name) {
            $lines[] = [
                'name' => $this->pengawas2_name,
                'nip' => $this->pengawas2_nip,
                'title' => 'Pengawas BUMDes',
            ];
        }

        // Direktur (PP 11/2021)
        if ($this->direktur_name) {
            $lines[] = [
                'name' => $this->direktur_name,
                'nip' => $this->direktur_nip,
                'title' => 'Direktur BUMDes',
            ];
        }

        return $lines;
    }

    /**
     * Get the motto tagline
     */
    public function getMottoAttribute(): ?string
    {
        return $this->attributes['motto'] ?? null;
    }
}
