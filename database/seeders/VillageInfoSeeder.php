<?php

namespace Database\Seeders;

use App\Models\VillageInfo;
use Illuminate\Database\Seeder;

class VillageInfoSeeder extends Seeder
{
    public function run(): void
    {
        VillageInfo::create([
            // ============================================
            // 1. DATA UMUM DESA
            // ============================================
            'village_name' => 'Contoh Desa',
            'village_code' => '320101001',
            'district_name' => 'Kecamatan Contoh',
            'district_code' => '320101',
            'regency_name' => 'Kabupaten Contoh',
            'regency_code' => '3201',
            'province_name' => 'Jawa Barat',
            'province_code' => '32',
            'address' => 'Jl. Raya Desa No. 1, Kecamatan Contoh, Kabupaten Contoh',
            'postal_code' => '40000',
            'phone' => '(022) 1234567',
            'email' => 'desa@contoh.id',
            'website' => 'https://desa-contoh.id',
            
            // ============================================
            // 2. DATA GEOGRAFIS
            // ============================================
            'area_total' => 500.00,
            'area_land' => 450.00,
            'area_water' => 50.00,
            'area_farming' => 200.00,
            'area_settlement' => 100.00,
            'area_forest' => 100.00,
            'elevation' => 500.00,
            'rainfall' => 2000.00,
            'climate_type' => 'Af (Hujan Sepanjang Tahun)',
            'border_north' => 'Desa Sebelah Utara',
            'border_south' => 'Desa Sebelah Selatan',
            'border_east' => 'Desa Sebelah Timur',
            'border_west' => 'Desa Sebelah Barat',
            'topography' => 'Dataran tinggi dengan sedikit perbukitan. Sebagian besar wilayah adalah lahan pertanian dan perkebunan.',
            
            // ============================================
            // 3. DATA DEMOGRAFIS
            // ============================================
            'total_population' => 5000,
            'male_population' => 2500,
            'female_population' => 2500,
            'total_family' => 1500,
            'total_rt' => 15,
            'total_rw' => 5,
            'total_dusun' => 3,
            'population_density' => 10,
            'birth_rate' => 15,
            'death_rate' => 5,
            'growth_rate' => 1.5,
            'population_notes' => 'Pertumbuhan penduduk stabil. Mayoritas penduduk bekerja di sektor pertanian.',
            
            // ============================================
            // 4. DATA PEMERINTAHAN
            // ============================================
            'head_village_name' => 'Bapak Kepala Desa',
            'head_village_nip' => '197001012000121001',
            'head_village_start' => '2021-01-01',
            'head_village_end' => '2026-12-31',
            'village_secretary_name' => 'Ibu Sekretaris Desa',
            'total_staff' => 10,
            'total_bpd_members' => 5,
            'village_regulation' => 'Peraturan Desa No. 1 Tahun 2021 tentang Pendirian BUMDes Maju Jaya',
            
            // ============================================
            // 5. DATA EKONOMI
            // ============================================
            'total_umskm' => 50,
            'total_market' => 2,
            'avg_income' => 3000000,
            'poverty_rate' => 8.5,
            'unemployment_rate' => 5.2,
            'main_commodities' => 'Padi, Jagung, Kopi, Kakao, Sayuran',
            'economic_activities' => 'Pertanian, Perkebunan, Peternakan, Perdagangan, Jasa',
            'economic_potential' => 'Potensi pertanian organik dan agrowisata. Kopi robusta kualitas premium.',
            'economic_challenges' => 'Akses pasar terbatas, kurangnya pengolahan hasil pertanian, infrastruktur jalan perlu ditingkatkan.',
            
            // ============================================
            // 6. DATA SOSIAL & KESEHATAN
            // ============================================
            'total_schools' => 5,
            'total_health_facilities' => 3,
            'total_mosques' => 4,
            'total_churches' => 1,
            'literacy_rate' => 95.0,
            'school_participation_rate' => 98.0,
            'education_facilities' => 'SD Negeri 1, SD Negeri 2, MI Al-Hidayah, SMP Negeri 1, MTS Al-Ikhlas',
            'health_facilities' => 'Puskesmas Pembantu, Posyandu 5, Apotek Desa',
            'social_notes' => 'Tingkat literasi tinggi. Fasilitas pendidikan cukup memadai.',
            
            // ============================================
            // 7. DATA INFRASTRUKTUR
            // ============================================
            'total_road_length' => 25.50,
            'road_paved' => 15.00,
            'road_not_paved' => 10.50,
            'total_bridges' => 3,
            'total_irrigation' => 5,
            'electricity_coverage' => 95,
            'clean_water_coverage' => 80,
            'internet_coverage' => 70,
            'infrastructure_notes' => 'Listrik sudah merata. Air bersih dari PDAM dan sumur gali. Internet masih perlu penambahan tower.',
            
            // ============================================
            // 8. DATA KEUANGAN DESA
            // ============================================
            'village_fund' => 1200000000,
            'add_fund' => 800000000,
            'bdg_fund' => 200000000,
            'own_revenue' => 150000000,
            'other_revenue' => 50000000,
            'total_budget' => 2400000000,
            'total_expenditure' => 2200000000,
            'budget_year' => '2026',
            'budget_notes' => 'Anggaran desa dialokasikan untuk pembangunan infrastrasi, pemberdayaan masyarakat, dan operasional pemerintahan.',
            
            // ============================================
            // 9. DATA BUMDes
            // ============================================
            'bumdes_name' => 'BUMDes Maju Jaya',
            'bumdes_legal_number' => 'SK/01/PDT/2021',
            'bumdes_established' => '2021-03-15',
            'bumdes_initial_capital' => 100000000,
            'bumdes_current_capital' => 250000000,
            'bumdes_total_assets' => 500000000,
            'bumdes_annual_revenue' => 300000000,
            'bumdes_profit' => 50000000,
            'bumdes_employees' => 15,
            'bumdes_partners' => 50,
            'bumdes_vision' => 'Menjadi BUMDes yang mandiri dan sejahterakan masyarakat desa melalui pengelolaan potensi desa secara profesional.',
            'bumdes_mission' => '1. Mengelola potensi desa secara profesional\n2. Memberdayakan masyarakat desa\n3. Menciptakan lapangan kerja\n4. Meningkatkan pendapatan asli desa',
            'bumdes_services' => '1. Pengelolaan Air Bersih\n2. Simpan Pinjam\n3. Toko Desa\n4. Jasa Pertanian\n5. Agrowisata',
            'bumdes_achievements' => '1. Berhasil menyuplai air bersih ke 80% rumah tangga\n2. Pemberian pinjaman ke 50 UMKM\n3. Omset meningkat 25% per tahun',
            
            // ============================================
            // 10. DATA POTENSI
            // ============================================
            'tourism_potential' => '1. Agrowisata Kopi\n2. Curug/ Air Terjun\n3. Kampung Adat\n4. Jalur Tracking Gunung',
            'agriculture_potential' => 'Padi, Jagung, Ubi Jalar, Kacang Tanah, Sayuran Organik',
            'livestock_potential' => 'Sapi Potong, Kambing, Ayam Kampung, Lele',
            'fishery_potential' => 'Ikan Lele, Ikan Nila, Udang Air Tawar',
            'craft_potential' => 'Kerajinan Bambu, Anyaman Rotan, Batik Tulis',
            'cultural_potential' => '1. Tari Tradisional\n2. Upacara Adat\n3. Kuliner Khas Desa\n4. Seni Budaya Lainnya',
            'natural_potential' => 'Hutan Lindung, Sungai, Mata Air, Tanah Subur untuk Pertanian',
            'human_resource_potential' => 'Pemuda kreatif, Petani trampil, Pengrajin handal',
            
            // ============================================
            // 11. MEDIA & KONTAK
            // ============================================
            'contact_phone' => '(022) 1234567',
            'contact_whatsapp' => '62812345678',
            'contact_email' => 'desa@contoh.id',
            'facebook' => 'https://facebook.com/desacontoh',
            'instagram' => 'https://instagram.com/desacontoh',
            'youtube' => 'https://youtube.com/@desacontoh',
            
            // ============================================
            // 12. PROFIL & SEO
            // ============================================
            'about_village' => 'Desa Contoh adalah desa yang terletak di Kecamatan Contoh, Kabupaten Contoh, Provinsi Jawa Barat. Desa ini memiliki potensi alam yang melimpah dan masyarakat yang gotong royong. Dengan luas wilayah 500 hektar dan penduduk sekitar 5.000 jiwa, desa ini dikenal sebagai penghasil kopi robusta berkualitas tinggi.',
            'village_history' => 'Desa Contoh didirikan pada tahun 1800-an oleh para pendatang yang mencari lahan pertanian. Nama "Contoh" diambil dari julukan yang diberikan karena desa ini sering dijadikan contoh dalam pengelolaan pertanian. Pada tahun 2021, BUMDes Maju Jaya didirikan untuk mengelola potensi desa secara profesional.',
            'village_motto' => 'Bersama Membangun Desa yang Maju dan Sejahtera',
            'meta_title' => 'Desa Contoh - Profil Desa & BUMDes Maju Jaya',
            'meta_description' => 'Profil lengkap Desa Contoh, Kecamatan Contoh, Kabupaten Contoh. Informasi BUMDes, potensi desa, data demografis, dan layanan masyarakat.',
            'is_published' => true,
        ]);
    }
}
