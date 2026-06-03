<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\Gallery;
use App\Models\ProductCategory;
use App\Models\Product;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // Sample News
        $news = [
            [
                'title' => 'Musyawarah Desa Tentang APBDes Tahun 2026',
                'slug' => 'musyawarah-desa-apbdes-2026',
                'excerpt' => 'Musyawarah desa membahas rancangan anggaran pendapatan dan belanja desa tahun 2026.',
                'content' => '<p>Pada tanggal 15 Januari 2026, telah dilaksanakan musyawarah desa untuk membahas rancangan APBDes tahun 2026. Musyawarah dihadiri oleh Kepala Desa, perangkat desa, BPD, tokoh masyarakat, dan warga desa.</p><p>Hasil musyawarah menyetujui rancangan APBDes tahun 2026 dengan total anggaran sebesar Rp 1.2 miliar yang bersumber dari Dana Desa, ADD, dan PADes.</p>',
                'category' => 'pembangunan',
                'is_featured' => true,
                'author' => 'Admin Desa',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Program Pelatihan UMKM Digital Marketing',
                'slug' => 'pelatihan-umkm-digital-marketing',
                'excerpt' => 'Pelatihan digital marketing untuk pelaku UMKM di desa guna meningkatkan penjualan online.',
                'content' => '<p>BUMDes bekerja sama dengan Dinas Koperasi mengadakan pelatihan digital marketing untuk 30 pelaku UMKM di desa.</p><p>Pelatihan meliputi pembuatan konten, pengelolaan media sosial, dan teknik pemasaran online.</p>',
                'category' => 'ekonomi',
                'is_featured' => false,
                'author' => 'Admin Desa',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Perayaan Hari Kemerdekaan RI ke-81',
                'slug' => 'perayaan-hari-kemerdekaan-ri-81',
                'excerpt' => 'Berbagai rangkaian acara perayaan HUT RI ke-81 di desa kami.',
                'content' => '<p>Dalam rangka memperingati Hari Kemerdekaan Republik Indonesia ke-81, desa kami mengadakan berbagai rangkaian acara.</p><p>Acara meliputi upacara bendera, lomba 17-an, dan pentas seni budaya.</p>',
                'category' => 'budaya',
                'is_featured' => false,
                'author' => 'Admin Desa',
                'published_at' => now()->subDays(10),
            ],
        ];

        foreach ($news as $item) {
            News::create($item);
        }

        // Sample Galleries
        $galleries = [
            [
                'title' => 'Pemandangan Alam Desa',
                'description' => 'Pemandangan alam yang indah dari bukit di desa kami.',
                'image' => 'galleries/sample-1.jpg',
                'type' => 'foto',
                'category' => 'alam',
                'sort_order' => 1,
            ],
            [
                'title' => 'Kegiatan Gotong Royong',
                'description' => 'Warga desa sedang melaksanakan gotong royong membersihkan lingkungan.',
                'image' => 'galleries/sample-2.jpg',
                'type' => 'foto',
                'category' => 'kegiatan',
                'sort_order' => 2,
            ],
            [
                'title' => 'Festival Budaya Tahunan',
                'description' => 'Video festival budaya tahunan di desa kami.',
                'image' => 'galleries/sample-3.jpg',
                'type' => 'video',
                'category' => 'budaya',
                'sort_order' => 3,
            ],
        ];

        foreach ($galleries as $item) {
            Gallery::create($item);
        }

        // Sample Product Categories
        $categories = [
            ['name' => 'Kuliner', 'slug' => 'kuliner', 'icon' => 'fas fa-utensils', 'description' => 'Makanan dan minuman khas desa', 'sort_order' => 1],
            ['name' => 'Kerajinan', 'slug' => 'kerajinan', 'icon' => 'fas fa-hands', 'description' => 'Kerajinan tangan dari masyarakat', 'sort_order' => 2],
            ['name' => 'Pertanian', 'slug' => 'pertanian', 'icon' => 'fas fa-seedling', 'description' => 'Hasil pertanian organik', 'sort_order' => 3],
            ['name' => 'Peternakan', 'slug' => 'peternakan', 'icon' => 'fas fa-dog', 'description' => 'Hasil peternakan', 'sort_order' => 4],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[] = ProductCategory::create($cat);
        }

        // Sample Products
        $products = [
            [
                'category_id' => $categoryModels[0]->id,
                'name' => 'Kopi Gayo Aceh',
                'slug' => 'kopi-gayo-aceh',
                'description' => 'Kopi gayo pilihan dari petani lokal, diolah dengan metode tradisional.',
                'price' => 75000,
                'unit' => '250gr',
                'stock' => 50,
                'is_featured' => true,
                'location' => 'Dusun Blang',
                'whatsapp_order' => '6281234567890',
            ],
            [
                'category_id' => $categoryModels[0]->id,
                'name' => 'Keripik Pisang',
                'slug' => 'keripik-pisang',
                'description' => 'Keripik pisang renyah dari pisang pilihan.',
                'price' => 25000,
                'unit' => 'pack',
                'stock' => 100,
                'is_featured' => false,
                'location' => 'Dusun Meunasah',
                'whatsapp_order' => '6281234567890',
            ],
            [
                'category_id' => $categoryModels[1]->id,
                'name' => 'Tikar Anyaman',
                'slug' => 'tikar-anyaman',
                'description' => 'Tikar anyaman dari eceng gondok, kuat dan tahan lama.',
                'price' => 150000,
                'unit' => 'pcs',
                'stock' => 20,
                'is_featured' => true,
                'location' => 'Dusun Rawa',
                'whatsapp_order' => '6281234567890',
            ],
            [
                'category_id' => $categoryModels[2]->id,
                'name' => 'Beras Organik',
                'slug' => 'beras-organik',
                'description' => 'Beras organik dari sawah tanpa pestisida.',
                'price' => 12000,
                'stock' => 200,
                'unit' => 'kg',
                'is_featured' => true,
                'location' => 'Dusun Sawah',
                'whatsapp_order' => '6281234567890',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
