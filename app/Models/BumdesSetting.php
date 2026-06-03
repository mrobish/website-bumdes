<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BumdesSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'bumdes_name',
        'bumdes_nib',
        'bumdes_npwd',
        'bumdes_address',
        'bumdes_village',
        'bumdes_district',
        'bumdes_regency',
        'bumdes_province',
        'bumdes_postal_code',
        'phone',
        'whatsapp',
        'email',
        'website',
        'vision',
        'mission',
        'about',
        'established_date',
        'logo_path',
        'logo_white_path',
        'kantor_photo_path',
        'kegiatan_photo_path',
        'signature_photo_path',
        'pdf_header_line1',
        'pdf_header_line2',
        'pdf_header_line3',
        'pdf_footer_text',
        'pdf_stamp_path',
        'facebook',
        'instagram',
        'youtube',
        'tiktok',
        'twitter',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_active',
    ];

    protected $casts = [
        'established_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the first (and only) settings record
     */
    public static function getSettings()
    {
        return static::firstOrCreate([], [
            'bumdes_name' => 'BUMDes Keude Bakongan',
            'bumdes_address' => 'Keude Bakongan, Kec. Bakongan',
            'bumdes_village' => 'Keude Bakongan',
            'bumdes_district' => 'Bakongan',
            'bumdes_regency' => 'Aceh Selatan',
            'bumdes_province' => 'Aceh',
            'phone' => '628123456789',
        ]);
    }

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    /**
     * Get white logo URL
     */
    public function getLogoWhiteUrlAttribute()
    {
        return $this->logo_white_path ? asset('storage/' . $this->logo_white_path) : null;
    }

    /**
     * Get kantor photo URL
     */
    public function getKantorPhotoUrlAttribute()
    {
        return $this->kantor_photo_path ? asset('storage/' . $this->kantor_photo_path) : null;
    }

    /**
     * Get kegiatan photo URL
     */
    public function getKegiatanPhotoUrlAttribute()
    {
        return $this->kegiatan_photo_path ? asset('storage/' . $this->kegiatan_photo_path) : null;
    }

    /**
     * Get full address
     */
    public function getFullAddressAttribute()
    {
        return implode(', ', array_filter([
            $this->bumdes_address,
            $this->bumdes_village,
            'Kec. ' . $this->bumdes_district,
            'Kab. ' . $this->bumdes_regency,
            $this->bumdes_province,
            $this->bumdes_postal_code,
        ]));
    }

    /**
     * Get PDF header lines as array
     */
    public function getPdfHeaderAttribute()
    {
        return array_filter([
            $this->pdf_header_line1,
            $this->pdf_header_line2,
            $this->pdf_header_line3,
        ]);
    }
}
