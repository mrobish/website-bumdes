<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('village_info', function (Blueprint $table) {
            $table->id();
            
            // ============================================
            // 1. DATA UMUM DESA
            // ============================================
            $table->string('village_name', 100)->comment('Nama Desa');
            $table->string('village_code', 10)->nullable()->comment('Kode Desa (BPS)');
            $table->string('district_name', 100)->comment('Nama Kecamatan');
            $table->string('district_code', 10)->nullable()->comment('Kode Kecamatan');
            $table->string('regency_name', 100)->comment('Nama Kabupaten');
            $table->string('regency_code', 10)->nullable()->comment('Kode Kabupaten');
            $table->string('province_name', 100)->comment('Nama Provinsi');
            $table->string('province_code', 10)->nullable()->comment('Kode Provinsi');
            $table->text('address')->comment('Alamat Lengkap Kantor Desa');
            $table->string('postal_code', 10)->nullable()->comment('Kode Pos');
            $table->string('phone', 20)->nullable()->comment('Telepon Kantor Desa');
            $table->string('email', 150)->nullable()->comment('Email Resmi Desa');
            $table->string('website', 255)->nullable()->comment('Website Resmi Desa');
            
            // ============================================
            // 2. DATA GEOGRAFIS
            // ============================================
            $table->decimal('area_total', 10, 2)->comment('Luas Wilayah Total (Ha)');
            $table->decimal('area_land', 10, 2)->nullable()->comment('Luas Daratan (Ha)');
            $table->decimal('area_water', 10, 2)->nullable()->comment('Luas Perairan (Ha)');
            $table->decimal('area_farming', 10, 2)->nullable()->comment('Luas Lahan Pertanian (Ha)');
            $table->decimal('area_settlement', 10, 2)->nullable()->comment('Luas Pemukiman (Ha)');
            $table->decimal('area_forest', 10, 2)->nullable()->comment('Luas Hutan (Ha)');
            $table->decimal('elevation', 8, 2)->nullable()->comment('Ketinggian dari Permukaan Laut (mdpl)');
            $table->decimal('rainfall', 6, 2)->nullable()->comment('Curah Hujan Rata-rata (mm/tahun)');
            $table->string('climate_type', 50)->nullable()->comment('Jenis Iklim (Af, Am, dll)');
            $table->text('border_north')->nullable()->comment('Batas Utara');
            $table->text('border_south')->nullable()->comment('Batas Selatan');
            $table->text('border_east')->nullable()->comment('Batas Timur');
            $table->text('border_west')->nullable()->comment('Batas Barat');
            $table->text('topography')->nullable()->comment('Deskripsi Topografi');
            $table->text('geography_notes')->nullable()->comment('Catatan Geografis Lainnya');
            
            // ============================================
            // 3. DATA DEMOGRAFIS
            // ============================================
            $table->integer('total_population')->comment('Jumlah Penduduk Total');
            $table->integer('male_population')->nullable()->comment('Jumlah Laki-laki');
            $table->integer('female_population')->nullable()->comment('Jumlah Perempuan');
            $table->integer('total_family')->comment('Jumlah Kepala Keluarga (KK)');
            $table->integer('total_rt')->nullable()->comment('Jumlah RT');
            $table->integer('total_rw')->nullable()->comment('Jumlah RW');
            $table->integer('total_dusun')->nullable()->comment('Jumlah Dusun/Kampung');
            $table->integer('population_density')->nullable()->comment('Kepadatan Penduduk (/Ha)');
            $table->integer('birth_rate')->nullable()->comment('Angka Kelahiran per 1000');
            $table->integer('death_rate')->nullable()->comment('Angka Kematian per 1000');
            $table->integer('growth_rate')->nullable()->comment('Laju Pertumbuhan (%)');
            $table->text('population_notes')->nullable()->comment('Catatan Demografis');
            
            // ============================================
            // 4. DATA PEMERINTAHAN
            // ============================================
            $table->string('head_village_name', 100)->comment('Nama Kepala Desa');
            $table->string('head_village_nip', 30)->nullable()->comment('NIP/NIK Kepala Desa');
            $table->date('head_village_start')->nullable()->comment('Masa Jabatan Mulai');
            $table->date('head_village_end')->nullable()->comment('Masa Jabatan Berakhir');
            $table->string('village_secretary_name', 100)->nullable()->comment('Nama Sekretaris Desa');
            $table->integer('total_staff')->nullable()->comment('Jumlah Staf/Perangkat Desa');
            $table->integer('total_bpd_members')->nullable()->comment('Jumlah Anggota BPD');
            $table->text('organizational_structure')->nullable()->comment('Struktur Organisasi (JSON/Text)');
            $table->text('village_regulation')->nullable()->comment('Dasar Hukum Pendirian BUMDes');
            
            // ============================================
            // 5. DATA EKONOMI
            // ============================================
            $table->decimal('total_umskm', 10, 0)->nullable()->comment('Jumlah UMKM');
            $table->decimal('total_market', 10, 0)->nullable()->comment('Jumlah Pasar Tradisional');
            $table->decimal('avg_income', 15, 2)->nullable()->comment('Penghasilan Rata-rata (Rp)');
            $table->decimal('poverty_rate', 5, 2)->nullable()->comment('Tingkat Kemiskinan (%)');
            $table->decimal('unemployment_rate', 5, 2)->nullable()->comment('Tingkat Pengangguran (%)');
            $table->text('main_commodities')->nullable()->comment('Komoditas Utama');
            $table->text('economic_activities')->nullable()->comment('Kegiatan Ekonomi Utama');
            $table->text('economic_potential')->nullable()->comment('Potensi Ekonomi');
            $table->text('economic_challenges')->nullable()->comment('Tantangan Ekonomi');
            
            // ============================================
            // 6. DATA SOSIAL & KESEHATAN
            // ============================================
            $table->integer('total_schools')->nullable()->comment('Jumlah Sekolah');
            $table->integer('total_health_facilities')->nullable()->comment('Jumlah Fasilitas Kesehatan');
            $table->integer('total_mosques')->nullable()->comment('Jumlah Masjid/Musholla');
            $table->integer('total_churches')->nullable()->comment('Jumlah Gereja');
            $table->decimal('literacy_rate', 5, 2)->nullable()->comment('Tingkat Melek Huruf (%)');
            $table->decimal('school_participation_rate', 5, 2)->nullable()->comment('Tingkat Partisipasi Sekolah (%)');
            $table->text('education_facilities')->nullable()->comment('Fasilitas Pendidikan');
            $table->text('health_facilities')->nullable()->comment('Fasilitas Kesehatan');
            $table->text('social_facilities')->nullable()->comment('Fasilitas Sosial');
            $table->text('religious_facilities')->nullable()->comment('Fasilitas Keagamaan');
            $table->text('social_notes')->nullable()->comment('Catatan Sosial');
            
            // ============================================
            // 7. DATA INFRASTRUKTUR
            // ============================================
            $table->decimal('total_road_length', 10, 2)->nullable()->comment('Panjang Jalan Total (Km)');
            $table->decimal('road_paved', 10, 2)->nullable()->comment('Jalan Aspal/Beton (Km)');
            $table->decimal('road_not_paved', 10, 2)->nullable()->comment('Jalan Tanah/Belum Paving (Km)');
            $table->integer('total_bridges')->nullable()->comment('Jumlah Jembatan');
            $table->integer('total_irrigation')->nullable()->comment('Jumlah Saluran Irigasi');
            $table->integer('electricity_coverage')->nullable()->comment('Cakupan Listrik (%)');
            $table->integer('clean_water_coverage')->nullable()->comment('Cakupan Air Bersih (%)');
            $table->integer('internet_coverage')->nullable()->comment('Cakupan Internet (%)');
            $table->text('road_conditions')->nullable()->comment('Kondisi Jalan');
            $table->text('infrastructure_notes')->nullable()->comment('Catatan Infrastruktur');
            
            // ============================================
            // 8. DATA KEUANGAN DESA
            // ============================================
            $table->decimal('village_fund', 15, 2)->nullable()->comment('Dana Desa (DD) Tahun Berjalan');
            $table->decimal('add_fund', 15, 2)->nullable()->comment('ADD (Alokasi Dana Desa)');
            $table->decimal('bdg_fund', 15, 2)->nullable()->comment('Bagi Hasil Pajak/Retribusi');
            $table->decimal('own_revenue', 15, 2)->nullable()->comment('Pendapatan Asli Desa (PADes)');
            $table->decimal('other_revenue', 15, 2)->nullable()->comment('Pendapatan Lain-lain');
            $table->decimal('total_budget', 15, 2)->nullable()->comment('Total Anggaran Desa');
            $table->decimal('total_expenditure', 15, 2)->nullable()->comment('Total Belanja Desa');
            $table->string('budget_year', 4)->nullable()->comment('Tahun Anggaran');
            $table->text('budget_notes')->nullable()->comment('Catatan Keuangan');
            
            // ============================================
            // 9. DATA BUMDES
            // ============================================
            $table->string('bumdes_name', 100)->comment('Nama BUMDes');
            $table->string('bumdes_legal_number', 50)->nullable()->comment('Nomor SK/LEGAL BUMDes');
            $table->date('bumdes_established')->nullable()->comment('Tanggal Pendirian BUMDes');
            $table->decimal('bumdes_initial_capital', 15, 2)->nullable()->comment('Modal Awal BUMDes');
            $table->decimal('bumdes_current_capital', 15, 2)->nullable()->comment('Modal Saat Ini');
            $table->decimal('bumdes_total_assets', 15, 2)->nullable()->comment('Total Aset BUMDes');
            $table->decimal('bumdes_annual_revenue', 15, 2)->nullable()->comment('Omset Tahunan');
            $table->decimal('bumdes_profit', 15, 2)->nullable()->comment('Laba/Rugi Tahunan');
            $table->integer('bumdes_employees')->nullable()->comment('Jumlah Karyawan');
            $table->integer('bumdes_partners')->nullable()->comment('Jumlah Mitra/Partner');
            $table->text('bumdes_vision')->nullable()->comment('Visi BUMDes');
            $table->text('bumdes_mission')->nullable()->comment('Misi BUMDes');
            $table->text('bumdes_services')->nullable()->comment('Layanan BUMDes');
            $table->text('bumdes_achievements')->nullable()->comment('Pencapaian BUMDes');
            
            // ============================================
            // 10. DATA POTENSI DESA
            // ============================================
            $table->text('tourism_potential')->nullable()->comment('Potensi Wisata');
            $table->text('agriculture_potential')->nullable()->comment('Potensi Pertanian');
            $table->text('livestock_potential')->nullable()->comment('Potensi Peternakan');
            $table->text('fishery_potential')->nullable()->comment('Potensi Perikanan');
            $table->text('craft_potential')->nullable()->comment('Potensi Kerajinan');
            $table->text('cultural_potential')->nullable()->comment('Potensi Budaya');
            $table->text('natural_potential')->nullable()->comment('Potensi Sumber Daya Alam');
            $table->text('human_resource_potential')->nullable()->comment('Potensi Sumber Daya Manusia');
            $table->text('potential_notes')->nullable()->comment('Catatan Potensi Lainnya');
            
            // ============================================
            // 11. MEDIA & DOKUMENTASI
            // ============================================
            $table->string('logo_path', 255)->nullable()->comment('Logo Desa');
            $table->string('banner_path', 255)->nullable()->comment('Banner/Hero Image');
            $table->string('map_path', 255)->nullable()->comment('Peta Wilayah');
            $table->json('photo_gallery')->nullable()->comment('Galeri Foto');
            $table->json('video_links')->nullable()->comment('Link Video');
            
            // ============================================
            // 12. KONTAK & MEDIA SOSIAL
            // ============================================
            $table->string('contact_phone', 20)->nullable()->comment('Telepon Kantor');
            $table->string('contact_whatsapp', 20)->nullable()->comment('WhatsApp Kantor');
            $table->string('contact_email', 150)->nullable()->comment('Email Kantor');
            $table->string('facebook', 255)->nullable()->comment('Facebook');
            $table->string('instagram', 255)->nullable()->comment('Instagram');
            $table->string('youtube', 255)->nullable()->comment('YouTube');
            $table->string('tiktok', 255)->nullable()->comment('TikTok');
            $table->string('twitter', 255)->nullable()->comment('Twitter/X');
            
            // ============================================
            // 13. META & PENGATURAN
            // ============================================
            $table->text('about_village')->nullable()->comment('Tentang Desa (Deskripsi Panjang)');
            $table->text('village_history')->nullable()->comment('Sejarah Desa');
            $table->text('village_motto')->nullable()->comment('Moto/Slogan Desa');
            $table->string('meta_title', 255)->nullable()->comment('SEO Meta Title');
            $table->text('meta_description')->nullable()->comment('SEO Meta Description');
            $table->text('meta_keywords')->nullable()->comment('SEO Meta Keywords');
            $table->boolean('is_published')->default(false)->comment('Status Publikasi');
            $table->timestamp('published_at')->nullable()->comment('Tanggal Publikasi');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_info');
    }
};
