<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BumdesSetting;

class BumdesSettingSeeder extends Seeder
{
    public function run(): void
    {
        BumdesSetting::updateOrCreate(
            ['id' => 1],
            [
                'bumdes_name' => 'BUMDes Keude Bakongan',
                'bumdes_nib' => null,
                'bumdes_npwd' => null,
                'bumdes_address' => 'Keude Bakongan, Kec. Bakongan',
                'bumdes_village' => 'Keude Bakongan',
                'bumdes_district' => 'Bakongan',
                'bumdes_regency' => 'Aceh Selatan',
                'bumdes_province' => 'Aceh',
                'bumdes_postal_code' => '23771',
                'phone' => '628123456789',
                'whatsapp' => '628123456789',
                'email' => 'info@bumdeskeudebakongan.id',
                'website' => 'https://bumdes.ondesa.id',
                'vision' => 'Menjadi BUMDes yang mandiri, profesional, dan sejahtera untuk masyarakat desa Keude Bakongan.',
                'mission' => "1. Mengelola potensi desa secara profesional\n2. Meningkatkan pendapatan asli desa\n3. Menciptakan lapangan kerja bagi warga desa\n4. Memberikan layanan terbaik untuk masyarakat\n5. Menjaga transparansi pengelolaan keuangan",
                'about' => '<p>BUMDes Keude Bakongan adalah Badan Usaha Milik Desa yang bergerak di berbagai bidang usaha untuk meningkatkan kesejahteraan masyarakat desa.</p><p>Didirikan pada tahun 2020, BUMDes ini telah berkembang menjadi salah satu unit usaha desa yang sukses dalam mengelola berbagai potensi desa.</p>',
                'established_date' => '2020-01-15',
                'pdf_header_line1' => 'PEMERINTAH KABUPATEN ACEH SELATAN',
                'pdf_header_line2' => 'KECAMATAN BAKONGAN',
                'pdf_header_line3' => 'BUMDes KEUDE BAKONGAN',
                'pdf_footer_text' => 'Keude Bakongan, Kec. Bakongan, Kab. Aceh Selatan, Prov. Aceh',
                'meta_title' => 'BUMDes Keude Bakongan - Badan Usaha Milik Desa',
                'meta_description' => 'Website resmi BUMDes Keude Bakongan. Platform digital untuk pengelolaan BUMDes yang transparan dan profesional.',
                'meta_keywords' => 'BUMDes, Keude Bakongan, Aceh Selatan, Badan Usaha Milik Desa, UMKM',
                'is_active' => true,
            ]
        );
    }
}
