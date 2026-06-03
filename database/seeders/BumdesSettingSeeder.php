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
                // Identitas BUMDes
                'bumdes_name' => 'BUMDes Keude Bakongan',
                'village_name' => 'Keude Bakongan',
                'village_code' => '11.03.01.2001',
                'bumdes_nib' => null,
                'nomor_ahu' => null,
                'established_date' => '2020-01-15',
                'nomor_perdes' => '03/PERDES/2020',
                'tanggal_perdes' => '2020-01-10',
                
                // Alamat
                'bumdes_address' => 'Keude Bakongan, Kec. Bakongan',
                'bumdes_district' => 'Bakongan',
                'bumdes_regency' => 'Aceh Selatan',
                'bumdes_province' => 'Aceh',
                'bumdes_postal_code' => '23771',
                
                // Kontak
                'phone' => '628123456789',
                'whatsapp' => '628123456789',
                'email' => 'info@bumdeskeudebakongan.id',
                'website' => 'https://bumdes.ondesa.id',
                
                // Visi Misi
                'vision' => 'Menjadi BUMDes yang mandiri, profesional, dan sejahtera untuk masyarakat desa Keude Bakongan.',
                'mission' => "1. Mengelola potensi desa secara profesional\n2. Meningkatkan pendapatan asli desa\n3. Menciptakan lapangan kerja bagi warga desa\n4. Memberikan layanan terbaik untuk masyarakat\n5. Menjaga transparansi pengelolaan keuangan",
                'about' => '<p>BUMDes Keude Bakongan adalah Badan Usaha Milik Desa yang bergerak di berbagai bidang usaha untuk meningkatkan kesejahteraan masyarakat desa.</p>',
                
                // Kop PDF
                'pdf_header_line1' => 'PEMERINTAH KABUPATEN ACEH SELATAN',
                'pdf_header_line2' => 'KECAMATAN BAKONGAN',
                'pdf_header_line3' => 'BUMDes KEUDE BAKONGAN',
                'pdf_footer_text' => 'Keude Bakongan, Kec. Bakongan, Kab. Aceh Selatan, Prov. Aceh',
                
                // SEO
                'meta_title' => 'BUMDes Keude Bakongan - Badan Usaha Milik Desa',
                'meta_description' => 'Website resmi BUMDes Keude Bakongan.',
                'meta_keywords' => 'BUMDes, Keude Bakongan, Aceh Selatan',
                'is_active' => true,
            ]
        );
    }
}
