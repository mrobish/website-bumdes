/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.10-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: bumdes
-- ------------------------------------------------------
-- Server version	10.11.10-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asset_depreciations`
--

DROP TABLE IF EXISTS `asset_depreciations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_depreciations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint(20) unsigned NOT NULL,
  `depreciation_date` date NOT NULL,
  `period` varchar(7) NOT NULL,
  `beginning_book_value` decimal(15,2) NOT NULL,
  `depreciation_amount` decimal(15,2) NOT NULL,
  `accumulated_depreciation` decimal(15,2) NOT NULL,
  `ending_book_value` decimal(15,2) NOT NULL,
  `status` enum('draft','posted','reversed') NOT NULL DEFAULT 'draft',
  `journal_entry_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_depreciations`
--

LOCK TABLES `asset_depreciations` WRITE;
/*!40000 ALTER TABLE `asset_depreciations` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_depreciations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `asset_code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('tanah','bangunan','peralatan','kendaraan','inventaris','lainnya') NOT NULL,
  `business_unit_id` bigint(20) unsigned NOT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `purchase_price` decimal(15,2) NOT NULL,
  `salvage_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `purchase_date` date NOT NULL,
  `useful_life_months` int(11) NOT NULL,
  `depreciation_method` enum('straight_line','declining_balance') NOT NULL DEFAULT 'straight_line',
  `depreciation_rate` decimal(5,2) DEFAULT NULL,
  `status` enum('active','fully_depreciated','disposed','sold') NOT NULL DEFAULT 'active',
  `disposal_date` date DEFAULT NULL,
  `disposal_price` decimal(15,2) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assets_asset_code_unique` (`asset_code`),
  KEY `assets_business_unit_id_foreign` (`business_unit_id`),
  KEY `assets_account_id_foreign` (`account_id`),
  CONSTRAINT `assets_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`),
  CONSTRAINT `assets_business_unit_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assets`
--

LOCK TABLES `assets` WRITE;
/*!40000 ALTER TABLE `assets` DISABLE KEYS */;
/*!40000 ALTER TABLE `assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `budgets`
--

DROP TABLE IF EXISTS `budgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL,
  `year` year(4) NOT NULL,
  `planned_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `actual_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `budgets_account_id_year_unique` (`account_id`,`year`),
  KEY `budgets_year_index` (`year`),
  CONSTRAINT `budgets_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `budgets`
--

LOCK TABLES `budgets` WRITE;
/*!40000 ALTER TABLE `budgets` DISABLE KEYS */;
/*!40000 ALTER TABLE `budgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bumdes_settings`
--

DROP TABLE IF EXISTS `bumdes_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bumdes_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bumdes_name` varchar(255) NOT NULL COMMENT 'Nama BUMDes',
  `village_name` varchar(255) DEFAULT NULL COMMENT 'Nama Desa',
  `village_code` varchar(255) DEFAULT NULL COMMENT 'Kode Desa',
  `bumdes_nib` varchar(255) DEFAULT NULL COMMENT 'Nomor Induk Berusaha',
  `nomor_ahu` varchar(255) DEFAULT NULL COMMENT 'Nomor AHU (Jika sudah terbit SK Kemenkumham)',
  `bumdes_address` text NOT NULL COMMENT 'Alamat BUMDes',
  `bumdes_village` varchar(255) NOT NULL COMMENT 'Nama Desa',
  `bumdes_district` varchar(255) NOT NULL COMMENT 'Kecamatan',
  `bumdes_regency` varchar(255) NOT NULL COMMENT 'Kabupaten',
  `bumdes_province` varchar(255) NOT NULL COMMENT 'Provinsi',
  `bumdes_postal_code` varchar(10) DEFAULT NULL COMMENT 'Kode Pos',
  `phone` varchar(255) NOT NULL COMMENT 'Nomor Telepon/HP',
  `whatsapp` varchar(255) DEFAULT NULL COMMENT 'Nomor WhatsApp',
  `email` varchar(255) DEFAULT NULL COMMENT 'Email',
  `website` varchar(255) DEFAULT NULL COMMENT 'Website',
  `vision` text DEFAULT NULL COMMENT 'Visi BUMDes',
  `mission` text DEFAULT NULL COMMENT 'Misi BUMDes',
  `about` longtext DEFAULT NULL COMMENT 'Tentang BUMDes',
  `established_date` date DEFAULT NULL COMMENT 'Tanggal Berdiri',
  `nomor_perdes` varchar(255) DEFAULT NULL COMMENT 'Nomor Perdes Pendirian',
  `tanggal_perdes` date DEFAULT NULL COMMENT 'Tanggal Perdes Pendirian',
  `file_perdes_path` varchar(255) DEFAULT NULL COMMENT 'File Perdes Pendirian (PDF)',
  `file_adart_path` varchar(255) DEFAULT NULL COMMENT 'File AD/ART BUMDes (PDF)',
  `logo_path` varchar(255) DEFAULT NULL COMMENT 'Logo BUMDes',
  `logo_white_path` varchar(255) DEFAULT NULL COMMENT 'Logo BUMDes (Putih)',
  `kantor_photo_path` varchar(255) DEFAULT NULL COMMENT 'Foto Kantor (untuk background website)',
  `kegiatan_photo_path` varchar(255) DEFAULT NULL COMMENT 'Foto Kegiatan (untuk background login)',
  `signature_photo_path` varchar(255) DEFAULT NULL COMMENT 'Foto Tangan (untuk tanda tangan digital)',
  `pdf_header_line1` varchar(255) DEFAULT NULL COMMENT 'Baris 1 Kop PDF (mis: PEMERINTAH KAB. ACEH SELATAN)',
  `pdf_header_line2` varchar(255) DEFAULT NULL COMMENT 'Baris 2 Kop PDF (mis: KECAMATAN BAKONGAN)',
  `pdf_header_line3` varchar(255) DEFAULT NULL COMMENT 'Baris 3 Kop PDF (mis: DESA KEUDE BAKONGAN)',
  `pdf_footer_text` varchar(255) DEFAULT NULL COMMENT 'Footer PDF',
  `pdf_stamp_path` varchar(255) DEFAULT NULL COMMENT 'Stempel/Base64 untuk PDF',
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `tiktok` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bumdes_settings`
--

LOCK TABLES `bumdes_settings` WRITE;
/*!40000 ALTER TABLE `bumdes_settings` DISABLE KEYS */;
INSERT INTO `bumdes_settings` VALUES
(1,'BUM Desa Karya Mekar Karangmekar','Karangmekar','3206022005',NULL,NULL,'Jalan Raya Karangnunggal','Karangmekar','Karangnunggal','Tasikmalaya','Jawa Barat','46186','628123456789','628123456789','info@bumdeskeudebakongan.id','https://bumdes.ondesa.id','Menjadi BUMDes yang mandiri, profesional, dan sejahtera untuk masyarakat desa Keude Bakongan.','1. Mengelola potensi desa secara profesional\n2. Meningkatkan pendapatan asli desa\n3. Menciptakan lapangan kerja bagi warga desa\n4. Memberikan layanan terbaik untuk masyarakat\n5. Menjaga transparansi pengelolaan keuangan','<p>BUMDes Keude Bakongan adalah Badan Usaha Milik Desa yang bergerak di berbagai bidang usaha untuk meningkatkan kesejahteraan masyarakat desa.</p>','2020-01-15','03/PERDES/2020','2020-01-10',NULL,NULL,'bumdes/logo/01KT62WGMCD0X9Q1757TAH03YJ.png',NULL,'bumdes/foto/01KT63FR35KET82MY2M2F5CC02.jpg','bumdes/foto/01KT63FR37SQN09D2TVHMYY6G7.jpg',NULL,NULL,'BADAN USAHA MILIK DESA ','Karya Mekar Karangmekar','Desa Karangmekar, Kec. Karangnunggal, Kab. Tasikmalaya, Prov. Jawa Barat',NULL,NULL,NULL,NULL,NULL,NULL,'BUMDes Keude Bakongan - Badan Usaha Milik Desa','Website resmi BUMDes Keude Bakongan.','BUMDes, Keude Bakongan, Aceh Selatan',1,'2026-06-02 21:45:57','2026-06-03 01:55:02');
/*!40000 ALTER TABLE `bumdes_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bumdes_structures`
--

DROP TABLE IF EXISTS `bumdes_structures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bumdes_structures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `position` varchar(255) NOT NULL COMMENT 'Nama Jabatan',
  `position_group` varchar(255) NOT NULL COMMENT 'Kelompok: pengurus, unit, pengawas',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Urutan tampil',
  `name` varchar(255) NOT NULL COMMENT 'Nama Pejabat',
  `nip` varchar(255) DEFAULT NULL COMMENT 'NIP (jika ada)',
  `photo_path` varchar(255) DEFAULT NULL COMMENT 'Foto Pejabat',
  `phone` varchar(255) DEFAULT NULL COMMENT 'Nomor HP',
  `email` varchar(255) DEFAULT NULL COMMENT 'Email',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_village_head` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Jika ini Kepala Desa (otomatis Penasehat)',
  `business_unit_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Unit Usaha terkait',
  `start_date` date DEFAULT NULL COMMENT 'Mulai jabatan',
  `end_date` date DEFAULT NULL COMMENT 'Akhir jabatan',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bumdes_structures_business_unit_id_foreign` (`business_unit_id`),
  CONSTRAINT `bumdes_structures_business_unit_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bumdes_structures`
--

LOCK TABLES `bumdes_structures` WRITE;
/*!40000 ALTER TABLE `bumdes_structures` DISABLE KEYS */;
INSERT INTO `bumdes_structures` VALUES
(1,'Penasehat','pengurus',1,'Kepala Desa',NULL,NULL,NULL,NULL,1,1,NULL,NULL,NULL,'Otomatis dijabat oleh Kepala Desa','2026-06-02 21:46:01','2026-06-02 21:46:01'),
(2,'Direktur','pengurus',2,'Direktur BUMDes',NULL,NULL,NULL,NULL,1,0,NULL,NULL,NULL,NULL,'2026-06-02 21:46:01','2026-06-02 21:46:01'),
(3,'Sekretaris','pengurus',3,'Sekretaris BUMDes',NULL,NULL,NULL,NULL,1,0,NULL,NULL,NULL,NULL,'2026-06-02 21:46:01','2026-06-02 21:46:01'),
(4,'Bendahara','pengurus',4,'Bendahara BUMDes',NULL,NULL,NULL,NULL,1,0,NULL,NULL,NULL,NULL,'2026-06-02 21:46:01','2026-06-02 21:46:01'),
(5,'Pengawas','pengawas',5,'Ketua BPD',NULL,NULL,NULL,NULL,1,0,NULL,NULL,NULL,NULL,'2026-06-02 21:46:01','2026-06-02 21:46:01'),
(6,'Pengelola Unit Pangan','unit',10,'Pengelola Unit Pangan',NULL,NULL,NULL,NULL,1,0,2,NULL,NULL,NULL,'2026-06-02 21:46:16','2026-06-02 21:46:16'),
(7,'Pengelola Unit Wisata','unit',11,'Pengelola Unit Wisata',NULL,NULL,NULL,NULL,1,0,3,NULL,NULL,NULL,'2026-06-02 21:46:16','2026-06-02 21:46:16'),
(8,'Pengelola Unit Sampah','unit',12,'Pengelola Unit Sampah',NULL,NULL,NULL,NULL,1,0,4,NULL,NULL,NULL,'2026-06-02 21:46:16','2026-06-02 21:46:16'),
(9,'Pengelola Unit Internet','unit',13,'Pengelola Unit Internet',NULL,NULL,NULL,NULL,1,0,5,NULL,NULL,NULL,'2026-06-02 21:46:16','2026-06-02 21:46:16');
/*!40000 ALTER TABLE `bumdes_structures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_units`
--

DROP TABLE IF EXISTS `business_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business_units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT 'indigo',
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `about` longtext DEFAULT NULL,
  `manager` varchar(255) DEFAULT NULL,
  `operating_hours` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('induk','unit_usaha') NOT NULL DEFAULT 'unit_usaha',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `business_units_code_unique` (`code`),
  UNIQUE KEY `business_units_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_units`
--

LOCK TABLES `business_units` WRITE;
/*!40000 ALTER TABLE `business_units` DISABLE KEYS */;
INSERT INTO `business_units` VALUES
(1,'INDUK','bumdes-induk','indigo','628123456789',NULL,NULL,NULL,'<p>BUMDes Induk (Pusat) mengelola seluruh kegiatan usaha BUMDes Keude Bakongan. Sebagai induk, unit ini bertanggung jawab atas koordinasi, perencanaan, dan pengawasan seluruh unit usaha di bawahnya.</p><p>Visi: Menjadi BUMDes yang mandiri dan sejahtera untuk masyarakat desa.</p><p>Misi:</p><ul><li>Mengelola potensi desa secara profesional</li><li>Meningkatkan pendapatan asli desa</li><li>Menciptakan lapangan kerja bagi warga desa</li></ul>','Pengelola Induk','Senin-Jumat 08:00-16:00','active','BUMDes Induk (Pusat)','Pusat pengelolaan BUMDes. Menangani transaksi manajerial umum, penyertaan modal dari desa, operasional kantor BUMDes.','induk',1,1,'2026-06-02 20:24:34','2026-06-02 20:24:34'),
(2,'PANG','unit-pangan','indigo',NULL,NULL,NULL,NULL,'<p>Unit Ketahanan Pangan mengelola usaha di bidang pertanian, perkebunan, dan pangan. Kami menyediakan produk pangan berkualitas dari hasil pertanian warga desa.</p><p>Layanan kami:</p><ul><li>Pengadaan dan distribusi pupuk serta bibit</li><li>Pengolahan hasil pertanian</li><li>Pemasaran produk pangan lokal</li><li>Pelatihan pertanian modern</li></ul>',NULL,NULL,'active','Unit Ketahanan Pangan','Pengelolaan lahan pertanian, produksi pangan, dan distribusi hasil pertanian.','unit_usaha',1,2,'2026-06-02 20:24:34','2026-06-02 20:24:34'),
(3,'WIS','unit-wisata','indigo',NULL,NULL,NULL,NULL,'<p>Unit Pariwisata mengelola potensi wisata desa Keude Bakongan. Kami menyediakan layanan wisata yang menarik dan berkualitas.</p><p>Layanan kami:</p><ul><li>Paket wisata alam dan budaya</li><li>Pemandu wisata lokal</li><li>Penginapan dan homestay</li><li>Souvenir dan cinderamata khas desa</li></ul>',NULL,NULL,'active','Unit Pariwisata','Pengelolaan objek wisata, homestay, dan jasa pariwisata desa.','unit_usaha',1,3,'2026-06-02 20:24:34','2026-06-02 20:24:34'),
(4,'SAM','unit-sampah','indigo',NULL,NULL,NULL,NULL,'<p>Unit Pengelolaan Sampah mengelola layanan pengelolaan sampah desa secara profesional dan ramah lingkungan.</p><p>Layanan kami:</p><ul><li>Pengangkutan sampah rutin</li><li>Daur ulang dan pengolahan sampah</li><li>Pelatihan pengelolaan sampah rumah tangga</li><li>Penyediaan tempat sampah portable</li></ul>',NULL,NULL,'active','Unit Pengelolaan Sampah','Pengelolaan sampah desa, daur ulang, dan bank sampah.','unit_usaha',1,4,'2026-06-02 20:24:34','2026-06-02 20:24:34'),
(5,'NET','unit-internet','indigo',NULL,NULL,NULL,NULL,'<p>Unit Jaringan Internet menyediakan layanan internet murah dan berkualitas untuk warga desa. Kami ingin menjembatani kesenjangan digital di pedesaan.</p><p>Layanan kami:</p><ul><li>Internet unlimited dengan harga terjangkau</li><li>Instalasi dan maintenance jaringan</li><li>Warnet dan akses digital</li><li>Pelatihan digital literacy</li></ul>',NULL,NULL,'active','Unit Jaringan Internet','Pengelolaan jaringan internet desa (RT/RW Net).','unit_usaha',1,5,'2026-06-02 20:24:34','2026-06-02 20:24:34');
/*!40000 ALTER TABLE `business_units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `capital_contributions`
--

DROP TABLE IF EXISTS `capital_contributions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `capital_contributions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contribution_number` varchar(30) NOT NULL,
  `source_type` enum('desa','pemda_kab_kot','pemprov','kementerian','lembaga_negara','lembaga_swasta','perorangan','bantuan_luar_negeri','lainnya') NOT NULL,
  `source_name` varchar(255) NOT NULL,
  `source_address` text DEFAULT NULL,
  `source_contact` varchar(255) DEFAULT NULL,
  `source_phone` varchar(255) DEFAULT NULL,
  `source_email` varchar(255) DEFAULT NULL,
  `contribution_type` enum('hibah','pinjaman','pinjaman_bunga','penyertaan_saham') NOT NULL,
  `form` enum('uang','barang','uang_dan_barang','jasa') NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `goods_description` text DEFAULT NULL,
  `goods_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `contribution_year` year(4) NOT NULL,
  `disbursement_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `interest_rate` decimal(5,2) DEFAULT NULL,
  `loan_term_months` int(11) DEFAULT NULL,
  `first_payment_date` date DEFAULT NULL,
  `monthly_payment` decimal(15,2) DEFAULT NULL,
  `repayment_terms` text DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `restrictions` text DEFAULT NULL,
  `is_restricted` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('pending','approved','disbursed','received','active','completed','cancelled','rejected') NOT NULL DEFAULT 'pending',
  `agreement_number` varchar(255) DEFAULT NULL,
  `agreement_date` date DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `business_unit_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `capital_contributions_contribution_number_unique` (`contribution_number`),
  KEY `capital_contributions_business_unit_id_foreign` (`business_unit_id`),
  KEY `capital_contributions_created_by_foreign` (`created_by`),
  KEY `capital_contributions_approved_by_foreign` (`approved_by`),
  CONSTRAINT `capital_contributions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `capital_contributions_business_unit_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`),
  CONSTRAINT `capital_contributions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `capital_contributions`
--

LOCK TABLES `capital_contributions` WRITE;
/*!40000 ALTER TABLE `capital_contributions` DISABLE KEYS */;
/*!40000 ALTER TABLE `capital_contributions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chart_of_accounts`
--

DROP TABLE IF EXISTS `chart_of_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chart_of_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('aset','kewajiban','modal','pendapatan','beban') NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `is_group` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chart_of_accounts_code_unique` (`code`),
  KEY `chart_of_accounts_type_index` (`type`),
  KEY `chart_of_accounts_parent_id_index` (`parent_id`),
  KEY `chart_of_accounts_is_active_index` (`is_active`),
  CONSTRAINT `chart_of_accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chart_of_accounts`
--

LOCK TABLES `chart_of_accounts` WRITE;
/*!40000 ALTER TABLE `chart_of_accounts` DISABLE KEYS */;
INSERT INTO `chart_of_accounts` VALUES
(1,'1100','Aset Lancar','aset',NULL,1,'Aset yang dapat dikonversi menjadi kas dalam waktu satu tahun',1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(2,'1101','Kas','aset',1,0,'Kas tunai',1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(3,'1102','Bank','aset',1,0,'Simpanan di bank',1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(4,'1103','Piutang','aset',1,0,'Piutang dari penjualan',1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(5,'1104','Persediaan','aset',1,0,'Barang dagang',1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(6,'1200','Aset Tetap','aset',NULL,1,'Aset jangka panjang',1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(7,'1201','Tanah','aset',6,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(8,'1202','Bangunan','aset',6,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(9,'1203','Peralatan','aset',6,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(10,'1204','Kendaraan','aset',6,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(11,'2100','Kewajiban Lancar','kewajiban',NULL,1,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(12,'2101','Hutang Usaha','kewajiban',11,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(13,'2102','Hutang Bank','kewajiban',11,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(14,'3100','Modal','modal',NULL,1,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(15,'3101','Modal Setoran','modal',14,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(16,'3102','Laba Ditahan','modal',14,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(17,'3103','Laba Tahun Berjalan','modal',14,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(18,'4100','Pendapatan Usaha','pendapatan',NULL,1,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(19,'4101','Penjualan Produk','pendapatan',18,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(20,'4102','Pendapatan Jasa','pendapatan',18,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(21,'4200','Pendapatan Lain','pendapatan',NULL,1,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(22,'4201','Pendapatan Bunga','pendapatan',21,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(23,'4202','Pendapatan Sewa','pendapatan',21,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(24,'5100','Beban Usaha','beban',NULL,1,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(25,'5101','Beban Gaji','beban',24,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(26,'5102','Beban Sewa','beban',24,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(27,'5103','Beban Listrik','beban',24,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(28,'5104','Beban ATK','beban',24,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(29,'5200','Beban Lain','beban',NULL,1,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(30,'5201','Beban Administrasi Bank','beban',29,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39'),
(31,'5202','Beban Pajak','beban',29,0,NULL,1,'2026-06-02 19:50:39','2026-06-02 19:50:39');
/*!40000 ALTER TABLE `chart_of_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consolidated_reports`
--

DROP TABLE IF EXISTS `consolidated_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consolidated_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `report_number` varchar(30) NOT NULL,
  `period` varchar(255) NOT NULL,
  `period_type` enum('monthly','quarterly','yearly') NOT NULL,
  `balance_sheet` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`balance_sheet`)),
  `income_statement` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`income_statement`)),
  `cash_flow` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`cash_flow`)),
  `notes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notes`)),
  `unit_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`unit_breakdown`)),
  `total_assets` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_liabilities` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_equity` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_revenue` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_expenses` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_profit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','final','archived') NOT NULL DEFAULT 'draft',
  `prepared_by` bigint(20) unsigned NOT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `consolidated_reports_report_number_unique` (`report_number`),
  KEY `consolidated_reports_prepared_by_foreign` (`prepared_by`),
  KEY `consolidated_reports_approved_by_foreign` (`approved_by`),
  CONSTRAINT `consolidated_reports_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `consolidated_reports_prepared_by_foreign` FOREIGN KEY (`prepared_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consolidated_reports`
--

LOCK TABLES `consolidated_reports` WRITE;
/*!40000 ALTER TABLE `consolidated_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `consolidated_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financial_report_templates`
--

DROP TABLE IF EXISTS `financial_report_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `financial_report_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `type` enum('balance_sheet','income_statement','cash_flow','notes') NOT NULL,
  `description` text DEFAULT NULL,
  `structure` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`structure`)),
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `financial_report_templates_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_report_templates`
--

LOCK TABLES `financial_report_templates` WRITE;
/*!40000 ALTER TABLE `financial_report_templates` DISABLE KEYS */;
INSERT INTO `financial_report_templates` VALUES
(1,'Neraca SAK EMKM','NERACA','balance_sheet','Laporan Posisi Keuangan sesuai SAK EMKM.','\"{\\\"rows\\\":[{\\\"label\\\":\\\"ASET\\\",\\\"type\\\":\\\"header\\\"},{\\\"label\\\":\\\"Aset Lancar\\\",\\\"type\\\":\\\"section\\\"},{\\\"label\\\":\\\"Kas dan Ekivalen Kas\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"1101\\\",\\\"1102\\\"]},{\\\"label\\\":\\\"Piutang\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"1103\\\"]},{\\\"label\\\":\\\"Persediaan\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"1104\\\"]},{\\\"label\\\":\\\"Total Aset Lancar\\\",\\\"type\\\":\\\"subtotal\\\"},{\\\"label\\\":\\\"Aset Tidak Lancar\\\",\\\"type\\\":\\\"section\\\"},{\\\"label\\\":\\\"Tanah\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"1201\\\"]},{\\\"label\\\":\\\"Bangunan\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"1202\\\"]},{\\\"label\\\":\\\"Peralatan\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"1203\\\"]},{\\\"label\\\":\\\"Kendaraan\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"1204\\\"]},{\\\"label\\\":\\\"Total Aset Tidak Lancar\\\",\\\"type\\\":\\\"subtotal\\\"},{\\\"label\\\":\\\"JUMLAH ASET\\\",\\\"type\\\":\\\"total\\\"},{\\\"label\\\":\\\"\\\",\\\"type\\\":\\\"spacer\\\"},{\\\"label\\\":\\\"KEWAJIBAN DAN EKUITAS\\\",\\\"type\\\":\\\"header\\\"},{\\\"label\\\":\\\"Kewajiban\\\",\\\"type\\\":\\\"section\\\"},{\\\"label\\\":\\\"Kewajiban Lancar\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"2101\\\",\\\"2102\\\"]},{\\\"label\\\":\\\"Total Kewajiban\\\",\\\"type\\\":\\\"subtotal\\\"},{\\\"label\\\":\\\"\\\",\\\"type\\\":\\\"spacer\\\"},{\\\"label\\\":\\\"Ekuitas\\\",\\\"type\\\":\\\"section\\\"},{\\\"label\\\":\\\"Modal Setoran\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"3101\\\"]},{\\\"label\\\":\\\"Laba Ditahan\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"3102\\\"]},{\\\"label\\\":\\\"Laba Tahun Berjalan\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"3103\\\"]},{\\\"label\\\":\\\"Total Ekuitas\\\",\\\"type\\\":\\\"subtotal\\\"},{\\\"label\\\":\\\"JUMLAH KEWAJIBAN DAN EKUITAS\\\",\\\"type\\\":\\\"total\\\"}]}\"',NULL,1,1,'2026-06-02 20:24:34','2026-06-02 20:24:34'),
(2,'Laporan Laba/Rugi Konsolidasi','LR_KONSOL','income_statement','Laporan Laba/Rugi yang menggabungkan seluruh performa BUMDes.','\"{\\\"rows\\\":[{\\\"label\\\":\\\"PENDAPATAN\\\",\\\"type\\\":\\\"header\\\"},{\\\"label\\\":\\\"Pendapatan Usaha\\\",\\\"type\\\":\\\"section\\\"},{\\\"label\\\":\\\"Penjualan Produk\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"4101\\\"]},{\\\"label\\\":\\\"Pendapatan Jasa\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"4102\\\"]},{\\\"label\\\":\\\"Total Pendapatan Usaha\\\",\\\"type\\\":\\\"subtotal\\\"},{\\\"label\\\":\\\"Pendapatan Lain-lain\\\",\\\"type\\\":\\\"section\\\"},{\\\"label\\\":\\\"Pendapatan Bunga\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"4201\\\"]},{\\\"label\\\":\\\"Pendapatan Sewa\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"4202\\\"]},{\\\"label\\\":\\\"Total Pendapatan Lain-lain\\\",\\\"type\\\":\\\"subtotal\\\"},{\\\"label\\\":\\\"JUMLAH PENDAPATAN\\\",\\\"type\\\":\\\"total\\\"},{\\\"label\\\":\\\"\\\",\\\"type\\\":\\\"spacer\\\"},{\\\"label\\\":\\\"BEBAN\\\",\\\"type\\\":\\\"header\\\"},{\\\"label\\\":\\\"Beban Usaha\\\",\\\"type\\\":\\\"section\\\"},{\\\"label\\\":\\\"Beban Gaji\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"5101\\\"]},{\\\"label\\\":\\\"Beban Sewa\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"5102\\\"]},{\\\"label\\\":\\\"Beban Listrik\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"5103\\\"]},{\\\"label\\\":\\\"Beban ATK\\\",\\\"type\\\":\\\"item\\\",\\\"account_codes\\\":[\\\"5104\\\"]},{\\\"label\\\":\\\"Total Beban Usaha\\\",\\\"type\\\":\\\"subtotal\\\"},{\\\"label\\\":\\\"JUMLAH BEBAN\\\",\\\"type\\\":\\\"total\\\"},{\\\"label\\\":\\\"\\\",\\\"type\\\":\\\"spacer\\\"},{\\\"label\\\":\\\"LABA BERSIH\\\",\\\"type\\\":\\\"total_highlight\\\"}]}\"',NULL,1,1,'2026-06-02 20:24:34','2026-06-02 20:24:34'),
(3,'Laporan Arus Kas','ARUS_KAS','cash_flow','Laporan Arus Kas sesuai SAK EMKM.','\"{\\\"rows\\\":[{\\\"label\\\":\\\"ARUS KAS DARI AKTIVITAS OPERASIONAL\\\",\\\"type\\\":\\\"header\\\"},{\\\"label\\\":\\\"Penerimaan dari pelanggan\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"Pembayaran kepada pemasok\\\",\\\"type\\\":\\\"item\\\",\\\"negative\\\":true},{\\\"label\\\":\\\"Pembayaran gaji\\\",\\\"type\\\":\\\"item\\\",\\\"negative\\\":true},{\\\"label\\\":\\\"Kas Bersih dari Aktivitas Operasional\\\",\\\"type\\\":\\\"subtotal\\\"},{\\\"label\\\":\\\"\\\",\\\"type\\\":\\\"spacer\\\"},{\\\"label\\\":\\\"ARUS KAS DARI AKTIVITAS INVESTASI\\\",\\\"type\\\":\\\"header\\\"},{\\\"label\\\":\\\"Pembelian aset tetap\\\",\\\"type\\\":\\\"item\\\",\\\"negative\\\":true},{\\\"label\\\":\\\"Penjualan aset tetap\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"Kas Bersih dari Aktivitas Investasi\\\",\\\"type\\\":\\\"subtotal\\\"},{\\\"label\\\":\\\"\\\",\\\"type\\\":\\\"spacer\\\"},{\\\"label\\\":\\\"ARUS KAS DARI AKTIVITAS PENDANAAN\\\",\\\"type\\\":\\\"header\\\"},{\\\"label\\\":\\\"Penyertaan modal dari desa\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"Pembayaran dividen\\\",\\\"type\\\":\\\"item\\\",\\\"negative\\\":true},{\\\"label\\\":\\\"Kas Bersih dari Aktivitas Pendanaan\\\",\\\"type\\\":\\\"subtotal\\\"},{\\\"label\\\":\\\"\\\",\\\"type\\\":\\\"spacer\\\"},{\\\"label\\\":\\\"PENAMBAHAN (PENGURANGAN) KAS BERSIH\\\",\\\"type\\\":\\\"total_highlight\\\"},{\\\"label\\\":\\\"Saldo Kas Awal\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"SALDO KAS AKHIR\\\",\\\"type\\\":\\\"total\\\"}]}\"',NULL,1,1,'2026-06-02 20:24:34','2026-06-02 20:24:34'),
(4,'Catatan Atas Laporan Keuangan (CALK)','CALK','notes','Rincian penjelasan angka-angka di dalam laporan keuangan.','\"{\\\"rows\\\":[{\\\"label\\\":\\\"1. Umum\\\",\\\"type\\\":\\\"header\\\"},{\\\"label\\\":\\\"1.1 Bentuk Badan Hukum\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"1.2 Bidang Usaha\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"1.3 Periode Pelaporan\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"2. Ikhtisar Laporan Keuangan\\\",\\\"type\\\":\\\"header\\\"},{\\\"label\\\":\\\"2.1 Ikhtisar Posisi Keuangan\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"2.2 Ikhtisar Hasil Usaha\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"3. Ikhtisar Posisi Keuangan\\\",\\\"type\\\":\\\"header\\\"},{\\\"label\\\":\\\"3.1 Kas dan Ekivalen Kas\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"3.2 Piutang\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"3.3 Aset Tetap\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"3.4 Kewajiban\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"3.5 Ekuitas\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"4. Ikhtisar Hasil Usaha\\\",\\\"type\\\":\\\"header\\\"},{\\\"label\\\":\\\"4.1 Pendapatan\\\",\\\"type\\\":\\\"item\\\"},{\\\"label\\\":\\\"4.2 Beban\\\",\\\"type\\\":\\\"item\\\"}]}\"',NULL,1,1,'2026-06-02 20:24:34','2026-06-02 20:24:34');
/*!40000 ALTER TABLE `financial_report_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financial_templates`
--

DROP TABLE IF EXISTS `financial_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `financial_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('jurnal','neraca','laba_rugi','arus_kas','modal','realisasi_anggaran','buku_besar','cat') NOT NULL,
  `frequency` enum('harian','bulanan','tahunan','sekali') NOT NULL DEFAULT 'bulanan',
  `columns` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`columns`)),
  `sample_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sample_data`)),
  `validation_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`validation_rules`)),
  `file_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `financial_templates_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_templates`
--

LOCK TABLES `financial_templates` WRITE;
/*!40000 ALTER TABLE `financial_templates` DISABLE KEYS */;
INSERT INTO `financial_templates` VALUES
(1,'Jurnal Umum','jurnal-umum','Jurnal umum bulanan - Input semua transaksi keuangan BUMDes. 1 baris = 1 sisi transaksi (debet ATAU kredit). Setiap transaksi minimal 2 baris (saldi).','jurnal','bulanan','\"[{\\\"key\\\":\\\"tanggal\\\",\\\"name\\\":\\\"Tanggal (*)\\\",\\\"type\\\":\\\"date\\\",\\\"width\\\":14,\\\"rules\\\":[\\\"required\\\"]},{\\\"key\\\":\\\"no_ref\\\",\\\"name\\\":\\\"No. Ref\\\",\\\"type\\\":\\\"text\\\",\\\"width\\\":12},{\\\"key\\\":\\\"kode_akun\\\",\\\"name\\\":\\\"Kode Akun (*)\\\",\\\"type\\\":\\\"text\\\",\\\"width\\\":14,\\\"rules\\\":[\\\"required\\\"]},{\\\"key\\\":\\\"nama_akun\\\",\\\"name\\\":\\\"Nama Akun\\\",\\\"type\\\":\\\"text\\\",\\\"width\\\":24},{\\\"key\\\":\\\"keterangan\\\",\\\"name\\\":\\\"Keterangan (*)\\\",\\\"type\\\":\\\"text\\\",\\\"width\\\":32,\\\"rules\\\":[\\\"required\\\"]},{\\\"key\\\":\\\"debet\\\",\\\"name\\\":\\\"Debet (Rp)\\\",\\\"type\\\":\\\"number\\\",\\\"width\\\":18},{\\\"key\\\":\\\"kredit\\\",\\\"name\\\":\\\"Kredit (Rp)\\\",\\\"type\\\":\\\"number\\\",\\\"width\\\":18}]\"','\"[{\\\"tanggal\\\":\\\"01\\\\\\/01\\\\\\/2026\\\",\\\"no_ref\\\":\\\"J-001\\\",\\\"kode_akun\\\":\\\"1101\\\",\\\"nama_akun\\\":\\\"\\\",\\\"keterangan\\\":\\\"Setoran modal dari Pemerintah Desa\\\",\\\"debet\\\":50000000,\\\"kredit\\\":0},{\\\"tanggal\\\":\\\"01\\\\\\/01\\\\\\/2026\\\",\\\"no_ref\\\":\\\"J-001\\\",\\\"kode_akun\\\":\\\"3101\\\",\\\"nama_akun\\\":\\\"\\\",\\\"keterangan\\\":\\\"Setoran modal dari Pemerintah Desa\\\",\\\"debet\\\":0,\\\"kredit\\\":50000000},{\\\"tanggal\\\":\\\"05\\\\\\/01\\\\\\/2026\\\",\\\"no_ref\\\":\\\"J-002\\\",\\\"kode_akun\\\":\\\"1101\\\",\\\"nama_akun\\\":\\\"\\\",\\\"keterangan\\\":\\\"Penjualan produk UMKM\\\",\\\"debet\\\":10000000,\\\"kredit\\\":0},{\\\"tanggal\\\":\\\"05\\\\\\/01\\\\\\/2026\\\",\\\"no_ref\\\":\\\"J-002\\\",\\\"kode_akun\\\":\\\"4101\\\",\\\"nama_akun\\\":\\\"\\\",\\\"keterangan\\\":\\\"Penjualan produk UMKM\\\",\\\"debet\\\":0,\\\"kredit\\\":10000000},{\\\"tanggal\\\":\\\"10\\\\\\/01\\\\\\/2026\\\",\\\"no_ref\\\":\\\"J-003\\\",\\\"kode_akun\\\":\\\"1101\\\",\\\"nama_akun\\\":\\\"\\\",\\\"keterangan\\\":\\\"Pembelian bahan baku\\\",\\\"debet\\\":0,\\\"kredit\\\":5000000},{\\\"tanggal\\\":\\\"10\\\\\\/01\\\\\\/2026\\\",\\\"no_ref\\\":\\\"J-003\\\",\\\"kode_akun\\\":\\\"5102\\\",\\\"nama_akun\\\":\\\"\\\",\\\"keterangan\\\":\\\"Pembelian bahan baku\\\",\\\"debet\\\":5000000,\\\"kredit\\\":0},{\\\"tanggal\\\":\\\"15\\\\\\/01\\\\\\/2026\\\",\\\"no_ref\\\":\\\"J-004\\\",\\\"kode_akun\\\":\\\"1101\\\",\\\"nama_akun\\\":\\\"\\\",\\\"keterangan\\\":\\\"Pembayaran gaji karyawan\\\",\\\"debet\\\":0,\\\"kredit\\\":8000000},{\\\"tanggal\\\":\\\"15\\\\\\/01\\\\\\/2026\\\",\\\"no_ref\\\":\\\"J-004\\\",\\\"kode_akun\\\":\\\"5101\\\",\\\"nama_akun\\\":\\\"\\\",\\\"keterangan\\\":\\\"Pembayaran gaji karyawan\\\",\\\"debet\\\":8000000,\\\"kredit\\\":0},{\\\"tanggal\\\":\\\"20\\\\\\/01\\\\\\/2026\\\",\\\"no_ref\\\":\\\"J-005\\\",\\\"kode_akun\\\":\\\"1101\\\",\\\"nama_akun\\\":\\\"\\\",\\\"keterangan\\\":\\\"Pembayaran listrik\\\",\\\"debet\\\":0,\\\"kredit\\\":500000},{\\\"tanggal\\\":\\\"20\\\\\\/01\\\\\\/2026\\\",\\\"no_ref\\\":\\\"J-005\\\",\\\"kode_akun\\\":\\\"5103\\\",\\\"nama_akun\\\":\\\"\\\",\\\"keterangan\\\":\\\"Pembayaran listrik\\\",\\\"debet\\\":500000,\\\"kredit\\\":0},{\\\"tanggal\\\":\\\"31\\\\\\/01\\\\\\/2026\\\",\\\"no_ref\\\":\\\"J-006\\\",\\\"kode_akun\\\":\\\"1101\\\",\\\"nama_akun\\\":\\\"\\\",\\\"keterangan\\\":\\\"Pendapatan jasa konsultasi\\\",\\\"debet\\\":3000000,\\\"kredit\\\":0},{\\\"tanggal\\\":\\\"31\\\\\\/01\\\\\\/2026\\\",\\\"no_ref\\\":\\\"J-006\\\",\\\"kode_akun\\\":\\\"4102\\\",\\\"nama_akun\\\":\\\"\\\",\\\"keterangan\\\":\\\"Pendapatan jasa konsultasi\\\",\\\"debet\\\":0,\\\"kredit\\\":3000000}]\"',NULL,NULL,1,1,'2026-06-02 23:41:34','2026-06-03 02:53:33'),
(7,'Realisasi Anggaran','realisasi-anggaran','Realisasi anggaran per program - Input bulanan. Anggaran ditetapkan terpisah dari transaksi.','realisasi_anggaran','bulanan','\"[{\\\"key\\\":\\\"kode_akun\\\",\\\"name\\\":\\\"Kode Akun (*)\\\",\\\"type\\\":\\\"text\\\",\\\"width\\\":14,\\\"rules\\\":[\\\"required\\\"]},{\\\"key\\\":\\\"nama_akun\\\",\\\"name\\\":\\\"Nama Akun\\\",\\\"type\\\":\\\"text\\\",\\\"width\\\":24},{\\\"key\\\":\\\"uraian\\\",\\\"name\\\":\\\"Uraian (*)\\\",\\\"type\\\":\\\"text\\\",\\\"width\\\":30,\\\"rules\\\":[\\\"required\\\"]},{\\\"key\\\":\\\"anggaran\\\",\\\"name\\\":\\\"Anggaran (Rp) (*)\\\",\\\"type\\\":\\\"number\\\",\\\"width\\\":18,\\\"rules\\\":[\\\"required\\\",\\\"numeric\\\"]},{\\\"key\\\":\\\"realisasi\\\",\\\"name\\\":\\\"Realisasi (Rp) (*)\\\",\\\"type\\\":\\\"number\\\",\\\"width\\\":18,\\\"rules\\\":[\\\"required\\\",\\\"numeric\\\"]},{\\\"key\\\":\\\"persentase\\\",\\\"name\\\":\\\"Persentase (%)\\\",\\\"type\\\":\\\"number\\\",\\\"width\\\":12},{\\\"key\\\":\\\"selisih\\\",\\\"name\\\":\\\"Selisih (Rp)\\\",\\\"type\\\":\\\"number\\\",\\\"width\\\":16}]\"','\"[{\\\"kode_akun\\\":\\\"5101\\\",\\\"nama_akun\\\":\\\"\\\",\\\"uraian\\\":\\\"Beban gaji karyawan\\\",\\\"anggaran\\\":15000000,\\\"realisasi\\\":15000000,\\\"persentase\\\":0,\\\"selisih\\\":0},{\\\"kode_akun\\\":\\\"5103\\\",\\\"nama_akun\\\":\\\"\\\",\\\"uraian\\\":\\\"Beban listrik\\\",\\\"anggaran\\\":2400000,\\\"realisasi\\\":2000000,\\\"persentase\\\":0,\\\"selisih\\\":0},{\\\"kode_akun\\\":\\\"5104\\\",\\\"nama_akun\\\":\\\"\\\",\\\"uraian\\\":\\\"Beban air\\\",\\\"anggaran\\\":1200000,\\\"realisasi\\\":1000000,\\\"persentase\\\":0,\\\"selisih\\\":0}]\"',NULL,NULL,1,2,'2026-06-02 23:41:34','2026-06-03 02:53:33'),
(8,'CAT (Catatan Atas Laporan Keuangan)','cat-laporan','Catatan atas laporan keuangan SAK EMKM - Input manual untuk narasi/catatan kaki.','cat','tahunan','\"[{\\\"key\\\":\\\"no\\\",\\\"name\\\":\\\"No. (*)\\\",\\\"type\\\":\\\"text\\\",\\\"width\\\":8,\\\"rules\\\":[\\\"required\\\"]},{\\\"key\\\":\\\"uraian\\\",\\\"name\\\":\\\"Uraian (*)\\\",\\\"type\\\":\\\"text\\\",\\\"width\\\":50,\\\"rules\\\":[\\\"required\\\"]},{\\\"key\\\":\\\"nilai\\\",\\\"name\\\":\\\"Nilai (Rp)\\\",\\\"type\\\":\\\"number\\\",\\\"width\\\":18},{\\\"key\\\":\\\"keterangan\\\",\\\"name\\\":\\\"Keterangan\\\",\\\"type\\\":\\\"text\\\",\\\"width\\\":35}]\"','\"[{\\\"no\\\":\\\"1\\\",\\\"uraian\\\":\\\"Bentuk Usaha\\\",\\\"nilai\\\":\\\"\\\",\\\"keterangan\\\":\\\"Badan Usaha Milik Desa (BUMDes)\\\"},{\\\"no\\\":\\\"2\\\",\\\"uraian\\\":\\\"Kebijakan Akuntansi\\\",\\\"nilai\\\":\\\"\\\",\\\"keterangan\\\":\\\"K basis akrual, SAK EMKM\\\"},{\\\"no\\\":\\\"3\\\",\\\"uraian\\\":\\\"Aset Tetap\\\",\\\"nilai\\\":\\\"\\\",\\\"keterangan\\\":\\\"Depresiasi garis lurus, umur ekonomis 5 tahun\\\"},{\\\"no\\\":\\\"4\\\",\\\"uraian\\\":\\\"Modal Disetor\\\",\\\"nilai\\\":50000000,\\\"keterangan\\\":\\\"Dari Pemerintah Desa\\\"}]\"',NULL,NULL,1,3,'2026-06-02 23:41:34','2026-06-03 02:53:33');
/*!40000 ALTER TABLE `financial_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financial_transactions`
--

DROP TABLE IF EXISTS `financial_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `financial_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_unit_id` bigint(20) unsigned DEFAULT NULL,
  `transaction_number` varchar(20) NOT NULL,
  `transaction_date` date NOT NULL,
  `type` enum('pemasukan','pengeluaran') NOT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `counter_account_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `status` enum('draft','pending','approved','rejected') NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `financial_transactions_transaction_number_unique` (`transaction_number`),
  KEY `financial_transactions_counter_account_id_foreign` (`counter_account_id`),
  KEY `financial_transactions_created_by_foreign` (`created_by`),
  KEY `financial_transactions_approved_by_foreign` (`approved_by`),
  KEY `financial_transactions_transaction_date_index` (`transaction_date`),
  KEY `financial_transactions_type_index` (`type`),
  KEY `financial_transactions_account_id_index` (`account_id`),
  KEY `financial_transactions_status_index` (`status`),
  KEY `financial_transactions_business_unit_id_foreign` (`business_unit_id`),
  CONSTRAINT `financial_transactions_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`),
  CONSTRAINT `financial_transactions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `financial_transactions_business_unit_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`),
  CONSTRAINT `financial_transactions_counter_account_id_foreign` FOREIGN KEY (`counter_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `financial_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_transactions`
--

LOCK TABLES `financial_transactions` WRITE;
/*!40000 ALTER TABLE `financial_transactions` DISABLE KEYS */;
INSERT INTO `financial_transactions` VALUES
(1,NULL,'J-202606-0001','2026-06-03','pemasukan',2,NULL,100000.00,'Pembayaran sewa kios',NULL,NULL,1,NULL,'draft',NULL,NULL,'2026-06-03 04:52:23','2026-06-03 04:52:23',NULL);
/*!40000 ALTER TABLE `financial_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financial_uploads`
--

DROP TABLE IF EXISTS `financial_uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `financial_uploads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `template_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` varchar(255) NOT NULL,
  `period` varchar(255) DEFAULT NULL,
  `status` enum('pending','validated','approved','rejected') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `imported_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`imported_data`)),
  `imported_count` int(11) NOT NULL DEFAULT 0,
  `import_status` varchar(255) NOT NULL DEFAULT 'pending',
  `import_error` text DEFAULT NULL,
  `validation_errors` text DEFAULT NULL,
  `parsed_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parsed_data`)),
  `validated_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financial_uploads_user_id_foreign` (`user_id`),
  KEY `financial_uploads_template_id_period_index` (`template_id`,`period`),
  CONSTRAINT `financial_uploads_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `financial_templates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `financial_uploads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_uploads`
--

LOCK TABLES `financial_uploads` WRITE;
/*!40000 ALTER TABLE `financial_uploads` DISABLE KEYS */;
/*!40000 ALTER TABLE `financial_uploads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `galleries`
--

DROP TABLE IF EXISTS `galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `galleries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'foto',
  `category` varchar(255) NOT NULL DEFAULT 'umum',
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `views` int(11) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `galleries_category_index` (`category`),
  KEY `galleries_type_index` (`type`),
  KEY `galleries_is_published_index` (`is_published`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galleries`
--

LOCK TABLES `galleries` WRITE;
/*!40000 ALTER TABLE `galleries` DISABLE KEYS */;
INSERT INTO `galleries` VALUES
(1,'Pemandangan Alam Desa','Pemandangan alam yang indah dari bukit di desa kami.','galleries/sample-1.jpg','foto','alam',1,2,1,'2026-06-02 18:55:43','2026-06-02 20:47:11',NULL),
(2,'Kegiatan Gotong Royong','Warga desa sedang melaksanakan gotong royong membersihkan lingkungan.','galleries/sample-2.jpg','foto','kegiatan',1,2,2,'2026-06-02 18:55:43','2026-06-02 20:47:11',NULL),
(3,'Festival Budaya Tahunan','Video festival budaya tahunan di desa kami.','galleries/sample-3.jpg','video','budaya',1,2,3,'2026-06-02 18:55:43','2026-06-02 20:47:11',NULL);
/*!40000 ALTER TABLE `galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inter_account_transfers`
--

DROP TABLE IF EXISTS `inter_account_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inter_account_transfers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_number` varchar(30) NOT NULL,
  `transfer_date` date NOT NULL,
  `from_business_unit_id` bigint(20) unsigned NOT NULL,
  `from_account_id` bigint(20) unsigned NOT NULL,
  `to_business_unit_id` bigint(20) unsigned NOT NULL,
  `to_account_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inter_account_transfers_transfer_number_unique` (`transfer_number`),
  KEY `inter_account_transfers_from_business_unit_id_foreign` (`from_business_unit_id`),
  KEY `inter_account_transfers_from_account_id_foreign` (`from_account_id`),
  KEY `inter_account_transfers_to_business_unit_id_foreign` (`to_business_unit_id`),
  KEY `inter_account_transfers_to_account_id_foreign` (`to_account_id`),
  KEY `inter_account_transfers_approved_by_foreign` (`approved_by`),
  CONSTRAINT `inter_account_transfers_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `inter_account_transfers_from_account_id_foreign` FOREIGN KEY (`from_account_id`) REFERENCES `chart_of_accounts` (`id`),
  CONSTRAINT `inter_account_transfers_from_business_unit_id_foreign` FOREIGN KEY (`from_business_unit_id`) REFERENCES `business_units` (`id`),
  CONSTRAINT `inter_account_transfers_to_account_id_foreign` FOREIGN KEY (`to_account_id`) REFERENCES `chart_of_accounts` (`id`),
  CONSTRAINT `inter_account_transfers_to_business_unit_id_foreign` FOREIGN KEY (`to_business_unit_id`) REFERENCES `business_units` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inter_account_transfers`
--

LOCK TABLES `inter_account_transfers` WRITE;
/*!40000 ALTER TABLE `inter_account_transfers` DISABLE KEYS */;
/*!40000 ALTER TABLE `inter_account_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_reset_tokens_table',1),
(3,'2019_08_19_000000_create_failed_jobs_table',1),
(4,'2019_12_14_000001_create_personal_access_tokens_table',1),
(5,'2026_06_03_022150_create_village_info_table',2),
(6,'2026_06_03_024801_create_news_table',3),
(7,'2026_06_03_024802_create_galleries_table',3),
(8,'2026_06_03_024801_50_create_product_categories_table',4),
(9,'2026_06_03_024801_60_create_products_table',5),
(10,'2026_06_03_034222_create_chart_of_accounts_table',6),
(11,'2026_06_03_034223_create_budgets_table',6),
(12,'2026_06_03_034223_create_financial_transactions_table',6),
(13,'2026_06_03_041824_create_business_units_table',7),
(14,'2026_06_03_041825_create_consolidated_reports_table',7),
(15,'2026_06_03_041825_create_financial_report_templates_table',7),
(16,'2026_06_03_041825_create_inter_account_transfers_table',7),
(17,'2026_06_03_041851_add_business_unit_and_attachments_to_financial_transactions_table',7),
(18,'2026_06_03_044942_create_assets_table',8),
(19,'2026_06_03_044942_create_assets_table',9),
(20,'2026_06_03_044942_create_asset_depreciations_table',9),
(21,'2026_06_03_050638_create_capital_contributions_table',10),
(22,'2026_06_03_051813_enhance_business_units_table',1),
(23,'2026_06_03_051913_add_business_unit_to_products_table',11),
(24,'2026_06_03_051940_add_business_unit_to_news_table',12),
(25,'2026_06_03_054136_create_bumdes_settings_table',13),
(26,'2026_06_03_054159_create_bumdes_structures_table',13),
(27,'2026_06_03_055929_update_bumdes_settings_add_legal_docs_table',14),
(28,'2026_06_03_065652_enhance_users_table_for_roles',15),
(29,'2026_06_03_071600_create_roles_table',16),
(30,'2026_06_03_071610_add_role_id_to_users_table',15),
(31,'2026_06_03_073632_create_financial_templates_table',17),
(32,'2026_06_03_073632_create_financial_uploads_table',17),
(33,'2026_06_03_103048_add_imported_data_to_financial_uploads_table',18);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'umum',
  `business_unit_id` bigint(20) unsigned DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `views` int(11) NOT NULL DEFAULT 0,
  `author` varchar(255) DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `news_slug_unique` (`slug`),
  KEY `news_category_index` (`category`),
  KEY `news_is_published_index` (`is_published`),
  KEY `news_is_featured_index` (`is_featured`),
  KEY `news_business_unit_id_foreign` (`business_unit_id`),
  CONSTRAINT `news_business_unit_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES
(1,'Musyawarah Desa Tentang APBDes Tahun 2026','musyawarah-desa-apbdes-2026','Musyawarah desa membahas rancangan anggaran pendapatan dan belanja desa tahun 2026.','<p>Pada tanggal 15 Januari 2026, telah dilaksanakan musyawarah desa untuk membahas rancangan APBDes tahun 2026. Musyawarah dihadiri oleh Kepala Desa, perangkat desa, BPD, tokoh masyarakat, dan warga desa.</p><p>Hasil musyawarah menyetujui rancangan APBDes tahun 2026 dengan total anggaran sebesar Rp 1.2 miliar yang bersumber dari Dana Desa, ADD, dan PADes.</p>',NULL,'pembangunan',NULL,1,1,2,'Admin Desa','2026-05-31 18:55:43','2026-06-02 18:55:43','2026-06-02 20:47:08',NULL),
(2,'Program Pelatihan UMKM Digital Marketing','pelatihan-umkm-digital-marketing','Pelatihan digital marketing untuk pelaku UMKM di desa guna meningkatkan penjualan online.','<p>BUMDes bekerja sama dengan Dinas Koperasi mengadakan pelatihan digital marketing untuk 30 pelaku UMKM di desa.</p><p>Pelatihan meliputi pembuatan konten, pengelolaan media sosial, dan teknik pemasaran online.</p>',NULL,'ekonomi',NULL,0,1,2,'Admin Desa','2026-05-28 18:55:43','2026-06-02 18:55:43','2026-06-02 20:47:08',NULL),
(3,'Perayaan Hari Kemerdekaan RI ke-81','perayaan-hari-kemerdekaan-ri-81','Berbagai rangkaian acara perayaan HUT RI ke-81 di desa kami.','<p>Dalam rangka memperingati Hari Kemerdekaan Republik Indonesia ke-81, desa kami mengadakan berbagai rangkaian acara.</p><p>Acara meliputi upacara bendera, lomba 17-an, dan pentas seni budaya.</p>',NULL,'budaya',NULL,0,1,2,'Admin Desa','2026-05-23 18:55:43','2026-06-02 18:55:43','2026-06-02 20:47:09',NULL);
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories`
--

LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;
INSERT INTO `product_categories` VALUES
(1,'Kuliner','kuliner','Makanan dan minuman khas desa','fas fa-utensils',1,1,'2026-06-02 18:55:43','2026-06-02 18:55:43'),
(2,'Kerajinan','kerajinan','Kerajinan tangan dari masyarakat','fas fa-hands',2,1,'2026-06-02 18:55:43','2026-06-02 18:55:43'),
(3,'Pertanian','pertanian','Hasil pertanian organik','fas fa-seedling',3,1,'2026-06-02 18:55:43','2026-06-02 18:55:43'),
(4,'Peternakan','peternakan','Hasil peternakan','fas fa-dog',4,1,'2026-06-02 18:55:43','2026-06-02 18:55:43');
/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `business_unit_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `details` longtext DEFAULT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_price` decimal(15,2) DEFAULT NULL,
  `unit` varchar(255) NOT NULL DEFAULT 'pcs',
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `gallery_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery_images`)),
  `sku` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `views` int(11) NOT NULL DEFAULT 0,
  `sold_count` int(11) NOT NULL DEFAULT 0,
  `weight` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `whatsapp_order` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_index` (`category_id`),
  KEY `products_is_featured_index` (`is_featured`),
  KEY `products_is_published_index` (`is_published`),
  KEY `products_price_index` (`price`),
  KEY `products_business_unit_id_foreign` (`business_unit_id`),
  CONSTRAINT `products_business_unit_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES
(1,1,NULL,'Kopi Gayo Aceh','kopi-gayo-aceh','Kopi gayo pilihan dari petani lokal, diolah dengan metode tradisional.',NULL,75000.00,NULL,'250gr',50,NULL,NULL,NULL,1,1,2,0,NULL,'Dusun Blang','6281234567890','2026-06-02 18:55:43','2026-06-02 20:47:11',NULL),
(2,1,NULL,'Keripik Pisang','keripik-pisang','Keripik pisang renyah dari pisang pilihan.',NULL,25000.00,NULL,'pack',100,NULL,NULL,NULL,0,1,2,0,NULL,'Dusun Meunasah','6281234567890','2026-06-02 18:55:43','2026-06-02 20:47:14',NULL),
(3,2,NULL,'Tikar Anyaman','tikar-anyaman','Tikar anyaman dari eceng gondok, kuat dan tahan lama.',NULL,150000.00,NULL,'pcs',20,NULL,NULL,NULL,1,1,3,0,NULL,'Dusun Rawa','6281234567890','2026-06-02 18:55:43','2026-06-02 20:47:11',NULL),
(4,3,NULL,'Beras Organik','beras-organik','Beras organik dari sawah tanpa pestisida.',NULL,12000.00,NULL,'kg',200,NULL,NULL,NULL,1,1,2,0,NULL,'Dusun Sawah','6281234567890','2026-06-02 18:55:43','2026-06-02 20:47:11',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`permissions`)),
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'Super Admin','super_admin','Akses penuh ke semua fitur','[\"*\"]',1,1,'2026-06-02 23:18:35','2026-06-02 23:18:35'),
(2,'Admin','admin','Akses ke semua fitur kecuali manajemen user','[\"dashboard\",\"berita\",\"galeri\",\"produk\",\"unit_usaha\",\"keuangan\",\"aset\",\"penyertaan_modal\",\"rak\",\"konsolidasi\",\"laporan\",\"identitas_bumdes\",\"struktur\",\"system_info\"]',1,2,'2026-06-02 23:18:35','2026-06-02 23:18:35'),
(3,'Bendahara','bendahara','Akses ke fitur keuangan dan laporan','[\"dashboard\",\"keuangan\",\"aset\",\"penyertaan_modal\",\"rak\",\"konsolidasi\",\"laporan\",\"identitas_bumdes\"]',1,3,'2026-06-02 23:18:35','2026-06-02 23:18:35'),
(4,'Operator','operator','Akses ke konten dan produk','[\"dashboard\",\"berita\",\"galeri\",\"produk\",\"unit_usaha\",\"identitas_bumdes\"]',1,4,'2026-06-02 23:18:35','2026-06-02 23:18:35'),
(5,'Viewer','viewer','Hanya bisa melihat dashboard','[\"dashboard\"]',1,5,'2026-06-02 23:18:35','2026-06-02 23:18:35');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'operator',
  `role_id` bigint(20) unsigned DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` text DEFAULT NULL,
  `login_count` int(11) NOT NULL DEFAULT 0,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Super Admin','super_admin',1,NULL,NULL,'active',NULL,NULL,0,'admin@bumdes.id',NULL,'$2y$12$A6YpOyH.iO45wQa1v519aOKUzEYHNRTmYEXgNGfUc/Y87KZ1xRrCy','JlVhOYqSJjjP9YCBKl0AAF8EezVTyHg3jC5RloXwcN57uDlZ8eQC6bcXBcGE','2026-06-02 18:03:04','2026-06-02 23:19:55'),
(2,'Admin BUMDes','admin',2,NULL,NULL,'active',NULL,NULL,0,'admin@bumdeskeude.id',NULL,'$2y$12$vMktlymgBsWjF40HiJUWsu6Bs7ON23Y7r5XHSlx.NmTdQ5i61dt7a',NULL,'2026-06-02 22:57:45','2026-06-02 23:19:56'),
(3,'Bendahara','bendahara',3,NULL,NULL,'active',NULL,NULL,0,'bendahara@bumdeskeude.id',NULL,'$2y$12$uBfNkEhTVkwWO5dgT3BhPOD1bHcbfhOLBH2snrlYSXh6gITerRn5i',NULL,'2026-06-02 22:57:46','2026-06-02 23:19:56'),
(4,'Operator','operator',4,NULL,NULL,'active',NULL,NULL,0,'operator@bumdeskeude.id',NULL,'$2y$12$74bakw5AnLWPIV9kWxUIreBCuxdk0EEKLiR/jB0o33B7VKImt97h6',NULL,'2026-06-02 22:57:46','2026-06-02 23:19:56'),
(5,'Viewer','viewer',5,NULL,NULL,'active',NULL,NULL,0,'viewer@bumdeskeude.id',NULL,'$2y$12$O2SjQWNSZ3xUejofeyd5/.OI8lHLUzB9iVhTZG/EB8KHqs8lCGBkO',NULL,'2026-06-02 22:57:46','2026-06-02 23:19:57');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `village_info`
--

DROP TABLE IF EXISTS `village_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `village_info` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `village_name` varchar(100) NOT NULL COMMENT 'Nama Desa',
  `village_code` varchar(10) DEFAULT NULL COMMENT 'Kode Desa (BPS)',
  `district_name` varchar(100) NOT NULL COMMENT 'Nama Kecamatan',
  `district_code` varchar(10) DEFAULT NULL COMMENT 'Kode Kecamatan',
  `regency_name` varchar(100) NOT NULL COMMENT 'Nama Kabupaten',
  `regency_code` varchar(10) DEFAULT NULL COMMENT 'Kode Kabupaten',
  `province_name` varchar(100) NOT NULL COMMENT 'Nama Provinsi',
  `province_code` varchar(10) DEFAULT NULL COMMENT 'Kode Provinsi',
  `address` text NOT NULL COMMENT 'Alamat Lengkap Kantor Desa',
  `postal_code` varchar(10) DEFAULT NULL COMMENT 'Kode Pos',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Telepon Kantor Desa',
  `email` varchar(150) DEFAULT NULL COMMENT 'Email Resmi Desa',
  `website` varchar(255) DEFAULT NULL COMMENT 'Website Resmi Desa',
  `area_total` decimal(10,2) NOT NULL COMMENT 'Luas Wilayah Total (Ha)',
  `area_land` decimal(10,2) DEFAULT NULL COMMENT 'Luas Daratan (Ha)',
  `area_water` decimal(10,2) DEFAULT NULL COMMENT 'Luas Perairan (Ha)',
  `area_farming` decimal(10,2) DEFAULT NULL COMMENT 'Luas Lahan Pertanian (Ha)',
  `area_settlement` decimal(10,2) DEFAULT NULL COMMENT 'Luas Pemukiman (Ha)',
  `area_forest` decimal(10,2) DEFAULT NULL COMMENT 'Luas Hutan (Ha)',
  `elevation` decimal(8,2) DEFAULT NULL COMMENT 'Ketinggian dari Permukaan Laut (mdpl)',
  `rainfall` decimal(6,2) DEFAULT NULL COMMENT 'Curah Hujan Rata-rata (mm/tahun)',
  `climate_type` varchar(50) DEFAULT NULL COMMENT 'Jenis Iklim (Af, Am, dll)',
  `border_north` text DEFAULT NULL COMMENT 'Batas Utara',
  `border_south` text DEFAULT NULL COMMENT 'Batas Selatan',
  `border_east` text DEFAULT NULL COMMENT 'Batas Timur',
  `border_west` text DEFAULT NULL COMMENT 'Batas Barat',
  `topography` text DEFAULT NULL COMMENT 'Deskripsi Topografi',
  `geography_notes` text DEFAULT NULL COMMENT 'Catatan Geografis Lainnya',
  `total_population` int(11) NOT NULL COMMENT 'Jumlah Penduduk Total',
  `male_population` int(11) DEFAULT NULL COMMENT 'Jumlah Laki-laki',
  `female_population` int(11) DEFAULT NULL COMMENT 'Jumlah Perempuan',
  `total_family` int(11) NOT NULL COMMENT 'Jumlah Kepala Keluarga (KK)',
  `total_rt` int(11) DEFAULT NULL COMMENT 'Jumlah RT',
  `total_rw` int(11) DEFAULT NULL COMMENT 'Jumlah RW',
  `total_dusun` int(11) DEFAULT NULL COMMENT 'Jumlah Dusun/Kampung',
  `population_density` int(11) DEFAULT NULL COMMENT 'Kepadatan Penduduk (/Ha)',
  `birth_rate` int(11) DEFAULT NULL COMMENT 'Angka Kelahiran per 1000',
  `death_rate` int(11) DEFAULT NULL COMMENT 'Angka Kematian per 1000',
  `growth_rate` int(11) DEFAULT NULL COMMENT 'Laju Pertumbuhan (%)',
  `population_notes` text DEFAULT NULL COMMENT 'Catatan Demografis',
  `head_village_name` varchar(100) NOT NULL COMMENT 'Nama Kepala Desa',
  `head_village_nip` varchar(30) DEFAULT NULL COMMENT 'NIP/NIK Kepala Desa',
  `head_village_start` date DEFAULT NULL COMMENT 'Masa Jabatan Mulai',
  `head_village_end` date DEFAULT NULL COMMENT 'Masa Jabatan Berakhir',
  `village_secretary_name` varchar(100) DEFAULT NULL COMMENT 'Nama Sekretaris Desa',
  `total_staff` int(11) DEFAULT NULL COMMENT 'Jumlah Staf/Perangkat Desa',
  `total_bpd_members` int(11) DEFAULT NULL COMMENT 'Jumlah Anggota BPD',
  `organizational_structure` text DEFAULT NULL COMMENT 'Struktur Organisasi (JSON/Text)',
  `village_regulation` text DEFAULT NULL COMMENT 'Dasar Hukum Pendirian BUMDes',
  `total_umskm` decimal(10,0) DEFAULT NULL COMMENT 'Jumlah UMKM',
  `total_market` decimal(10,0) DEFAULT NULL COMMENT 'Jumlah Pasar Tradisional',
  `avg_income` decimal(15,2) DEFAULT NULL COMMENT 'Penghasilan Rata-rata (Rp)',
  `poverty_rate` decimal(5,2) DEFAULT NULL COMMENT 'Tingkat Kemiskinan (%)',
  `unemployment_rate` decimal(5,2) DEFAULT NULL COMMENT 'Tingkat Pengangguran (%)',
  `main_commodities` text DEFAULT NULL COMMENT 'Komoditas Utama',
  `economic_activities` text DEFAULT NULL COMMENT 'Kegiatan Ekonomi Utama',
  `economic_potential` text DEFAULT NULL COMMENT 'Potensi Ekonomi',
  `economic_challenges` text DEFAULT NULL COMMENT 'Tantangan Ekonomi',
  `total_schools` int(11) DEFAULT NULL COMMENT 'Jumlah Sekolah',
  `total_health_facilities` int(11) DEFAULT NULL COMMENT 'Jumlah Fasilitas Kesehatan',
  `total_mosques` int(11) DEFAULT NULL COMMENT 'Jumlah Masjid/Musholla',
  `total_churches` int(11) DEFAULT NULL COMMENT 'Jumlah Gereja',
  `literacy_rate` decimal(5,2) DEFAULT NULL COMMENT 'Tingkat Melek Huruf (%)',
  `school_participation_rate` decimal(5,2) DEFAULT NULL COMMENT 'Tingkat Partisipasi Sekolah (%)',
  `education_facilities` text DEFAULT NULL COMMENT 'Fasilitas Pendidikan',
  `health_facilities` text DEFAULT NULL COMMENT 'Fasilitas Kesehatan',
  `social_facilities` text DEFAULT NULL COMMENT 'Fasilitas Sosial',
  `religious_facilities` text DEFAULT NULL COMMENT 'Fasilitas Keagamaan',
  `social_notes` text DEFAULT NULL COMMENT 'Catatan Sosial',
  `total_road_length` decimal(10,2) DEFAULT NULL COMMENT 'Panjang Jalan Total (Km)',
  `road_paved` decimal(10,2) DEFAULT NULL COMMENT 'Jalan Aspal/Beton (Km)',
  `road_not_paved` decimal(10,2) DEFAULT NULL COMMENT 'Jalan Tanah/Belum Paving (Km)',
  `total_bridges` int(11) DEFAULT NULL COMMENT 'Jumlah Jembatan',
  `total_irrigation` int(11) DEFAULT NULL COMMENT 'Jumlah Saluran Irigasi',
  `electricity_coverage` int(11) DEFAULT NULL COMMENT 'Cakupan Listrik (%)',
  `clean_water_coverage` int(11) DEFAULT NULL COMMENT 'Cakupan Air Bersih (%)',
  `internet_coverage` int(11) DEFAULT NULL COMMENT 'Cakupan Internet (%)',
  `road_conditions` text DEFAULT NULL COMMENT 'Kondisi Jalan',
  `infrastructure_notes` text DEFAULT NULL COMMENT 'Catatan Infrastruktur',
  `village_fund` decimal(15,2) DEFAULT NULL COMMENT 'Dana Desa (DD) Tahun Berjalan',
  `add_fund` decimal(15,2) DEFAULT NULL COMMENT 'ADD (Alokasi Dana Desa)',
  `bdg_fund` decimal(15,2) DEFAULT NULL COMMENT 'Bagi Hasil Pajak/Retribusi',
  `own_revenue` decimal(15,2) DEFAULT NULL COMMENT 'Pendapatan Asli Desa (PADes)',
  `other_revenue` decimal(15,2) DEFAULT NULL COMMENT 'Pendapatan Lain-lain',
  `total_budget` decimal(15,2) DEFAULT NULL COMMENT 'Total Anggaran Desa',
  `total_expenditure` decimal(15,2) DEFAULT NULL COMMENT 'Total Belanja Desa',
  `budget_year` varchar(4) DEFAULT NULL COMMENT 'Tahun Anggaran',
  `budget_notes` text DEFAULT NULL COMMENT 'Catatan Keuangan',
  `bumdes_name` varchar(100) NOT NULL COMMENT 'Nama BUMDes',
  `bumdes_legal_number` varchar(50) DEFAULT NULL COMMENT 'Nomor SK/LEGAL BUMDes',
  `bumdes_established` date DEFAULT NULL COMMENT 'Tanggal Pendirian BUMDes',
  `bumdes_initial_capital` decimal(15,2) DEFAULT NULL COMMENT 'Modal Awal BUMDes',
  `bumdes_current_capital` decimal(15,2) DEFAULT NULL COMMENT 'Modal Saat Ini',
  `bumdes_total_assets` decimal(15,2) DEFAULT NULL COMMENT 'Total Aset BUMDes',
  `bumdes_annual_revenue` decimal(15,2) DEFAULT NULL COMMENT 'Omset Tahunan',
  `bumdes_profit` decimal(15,2) DEFAULT NULL COMMENT 'Laba/Rugi Tahunan',
  `bumdes_employees` int(11) DEFAULT NULL COMMENT 'Jumlah Karyawan',
  `bumdes_partners` int(11) DEFAULT NULL COMMENT 'Jumlah Mitra/Partner',
  `bumdes_vision` text DEFAULT NULL COMMENT 'Visi BUMDes',
  `bumdes_mission` text DEFAULT NULL COMMENT 'Misi BUMDes',
  `bumdes_services` text DEFAULT NULL COMMENT 'Layanan BUMDes',
  `bumdes_achievements` text DEFAULT NULL COMMENT 'Pencapaian BUMDes',
  `tourism_potential` text DEFAULT NULL COMMENT 'Potensi Wisata',
  `agriculture_potential` text DEFAULT NULL COMMENT 'Potensi Pertanian',
  `livestock_potential` text DEFAULT NULL COMMENT 'Potensi Peternakan',
  `fishery_potential` text DEFAULT NULL COMMENT 'Potensi Perikanan',
  `craft_potential` text DEFAULT NULL COMMENT 'Potensi Kerajinan',
  `cultural_potential` text DEFAULT NULL COMMENT 'Potensi Budaya',
  `natural_potential` text DEFAULT NULL COMMENT 'Potensi Sumber Daya Alam',
  `human_resource_potential` text DEFAULT NULL COMMENT 'Potensi Sumber Daya Manusia',
  `potential_notes` text DEFAULT NULL COMMENT 'Catatan Potensi Lainnya',
  `logo_path` varchar(255) DEFAULT NULL COMMENT 'Logo Desa',
  `banner_path` varchar(255) DEFAULT NULL COMMENT 'Banner/Hero Image',
  `map_path` varchar(255) DEFAULT NULL COMMENT 'Peta Wilayah',
  `photo_gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Galeri Foto' CHECK (json_valid(`photo_gallery`)),
  `video_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Link Video' CHECK (json_valid(`video_links`)),
  `contact_phone` varchar(20) DEFAULT NULL COMMENT 'Telepon Kantor',
  `contact_whatsapp` varchar(20) DEFAULT NULL COMMENT 'WhatsApp Kantor',
  `contact_email` varchar(150) DEFAULT NULL COMMENT 'Email Kantor',
  `facebook` varchar(255) DEFAULT NULL COMMENT 'Facebook',
  `instagram` varchar(255) DEFAULT NULL COMMENT 'Instagram',
  `youtube` varchar(255) DEFAULT NULL COMMENT 'YouTube',
  `tiktok` varchar(255) DEFAULT NULL COMMENT 'TikTok',
  `twitter` varchar(255) DEFAULT NULL COMMENT 'Twitter/X',
  `about_village` text DEFAULT NULL COMMENT 'Tentang Desa (Deskripsi Panjang)',
  `village_history` text DEFAULT NULL COMMENT 'Sejarah Desa',
  `village_motto` text DEFAULT NULL COMMENT 'Moto/Slogan Desa',
  `meta_title` varchar(255) DEFAULT NULL COMMENT 'SEO Meta Title',
  `meta_description` text DEFAULT NULL COMMENT 'SEO Meta Description',
  `meta_keywords` text DEFAULT NULL COMMENT 'SEO Meta Keywords',
  `is_published` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Status Publikasi',
  `published_at` timestamp NULL DEFAULT NULL COMMENT 'Tanggal Publikasi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `village_info`
--

LOCK TABLES `village_info` WRITE;
/*!40000 ALTER TABLE `village_info` DISABLE KEYS */;
INSERT INTO `village_info` VALUES
(1,'Contoh Desa','320101001','Kecamatan Contoh','320101','Kabupaten Contoh','3201','Jawa Barat','32','Jl. Raya Desa No. 1, Kecamatan Contoh, Kabupaten Contoh','40000','(022) 1234567','desa@contoh.id','https://desa-contoh.id',500.00,450.00,50.00,200.00,100.00,100.00,500.00,2000.00,'Af (Hujan Sepanjang Tahun)','Desa Sebelah Utara','Desa Sebelah Selatan','Desa Sebelah Timur','Desa Sebelah Barat','Dataran tinggi dengan sedikit perbukitan. Sebagian besar wilayah adalah lahan pertanian dan perkebunan.',NULL,5000,2500,2500,1500,15,5,3,10,15,5,2,'Pertumbuhan penduduk stabil. Mayoritas penduduk bekerja di sektor pertanian.','Bapak Kepala Desa','197001012000121001','2021-01-01','2026-12-31','Ibu Sekretaris Desa',10,5,NULL,'Peraturan Desa No. 1 Tahun 2021 tentang Pendirian BUMDes Maju Jaya',50,2,3000000.00,8.50,5.20,'Padi, Jagung, Kopi, Kakao, Sayuran','Pertanian, Perkebunan, Peternakan, Perdagangan, Jasa','Potensi pertanian organik dan agrowisata. Kopi robusta kualitas premium.','Akses pasar terbatas, kurangnya pengolahan hasil pertanian, infrastruktur jalan perlu ditingkatkan.',5,3,4,1,95.00,98.00,'SD Negeri 1, SD Negeri 2, MI Al-Hidayah, SMP Negeri 1, MTS Al-Ikhlas','Puskesmas Pembantu, Posyandu 5, Apotek Desa',NULL,NULL,'Tingkat literasi tinggi. Fasilitas pendidikan cukup memadai.',25.50,15.00,10.50,3,5,95,80,70,NULL,'Listrik sudah merata. Air bersih dari PDAM dan sumur gali. Internet masih perlu penambahan tower.',1200000000.00,800000000.00,200000000.00,150000000.00,50000000.00,2400000000.00,2200000000.00,'2026','Anggaran desa dialokasikan untuk pembangunan infrastrasi, pemberdayaan masyarakat, dan operasional pemerintahan.','BUMDes Maju Jaya','SK/01/PDT/2021','2021-03-15',100000000.00,250000000.00,500000000.00,300000000.00,50000000.00,15,50,'Menjadi BUMDes yang mandiri dan sejahterakan masyarakat desa melalui pengelolaan potensi desa secara profesional.','1. Mengelola potensi desa secara profesional\\n2. Memberdayakan masyarakat desa\\n3. Menciptakan lapangan kerja\\n4. Meningkatkan pendapatan asli desa','1. Pengelolaan Air Bersih\\n2. Simpan Pinjam\\n3. Toko Desa\\n4. Jasa Pertanian\\n5. Agrowisata','1. Berhasil menyuplai air bersih ke 80% rumah tangga\\n2. Pemberian pinjaman ke 50 UMKM\\n3. Omset meningkat 25% per tahun','1. Agrowisata Kopi\\n2. Curug/ Air Terjun\\n3. Kampung Adat\\n4. Jalur Tracking Gunung','Padi, Jagung, Ubi Jalar, Kacang Tanah, Sayuran Organik','Sapi Potong, Kambing, Ayam Kampung, Lele','Ikan Lele, Ikan Nila, Udang Air Tawar','Kerajinan Bambu, Anyaman Rotan, Batik Tulis','1. Tari Tradisional\\n2. Upacara Adat\\n3. Kuliner Khas Desa\\n4. Seni Budaya Lainnya','Hutan Lindung, Sungai, Mata Air, Tanah Subur untuk Pertanian','Pemuda kreatif, Petani trampil, Pengrajin handal',NULL,NULL,NULL,NULL,NULL,NULL,'(022) 1234567','62812345678','desa@contoh.id','https://facebook.com/desacontoh','https://instagram.com/desacontoh','https://youtube.com/@desacontoh',NULL,NULL,'Desa Contoh adalah desa yang terletak di Kecamatan Contoh, Kabupaten Contoh, Provinsi Jawa Barat. Desa ini memiliki potensi alam yang melimpah dan masyarakat yang gotong royong. Dengan luas wilayah 500 hektar dan penduduk sekitar 5.000 jiwa, desa ini dikenal sebagai penghasil kopi robusta berkualitas tinggi.','Desa Contoh didirikan pada tahun 1800-an oleh para pendatang yang mencari lahan pertanian. Nama \"Contoh\" diambil dari julukan yang diberikan karena desa ini sering dijadikan contoh dalam pengelolaan pertanian. Pada tahun 2021, BUMDes Maju Jaya didirikan untuk mengelola potensi desa secara profesional.','Bersama Membangun Desa yang Maju dan Sejahtera','Desa Contoh - Profil Desa & BUMDes Maju Jaya','Profil lengkap Desa Contoh, Kecamatan Contoh, Kabupaten Contoh. Informasi BUMDes, potensi desa, data demografis, dan layanan masyarakat.',NULL,1,NULL,'2026-06-02 18:25:00','2026-06-02 18:40:51','2026-06-02 18:40:51'),
(2,'Senang Senang','3206022005','Karangnunggal','320602','Tasikmalaya','3206','Jawa Barat','32','Jalan Raya Karangnunggal No 100','46186','087777942737','m.robishaeri@gmail.com','https://bumdes.ondesa.id',100.00,100.00,0.00,42.00,50.00,8.00,250.00,90.00,'-',NULL,NULL,NULL,NULL,NULL,NULL,10000,NULL,NULL,4500,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Muhamad Robi Shaeri','3206020000000001','2019-06-03','2026-06-03','Riswandi',14,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'BUMDes Senang Senang','AHU-0001','2026-06-03',100000000.00,150000000.00,70000000.00,60000000.00,200000000.00,4,10,'Maju Tak Gentar Bela Yang Benar',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2026-06-02 18:46:51','2026-06-02 18:46:51',NULL),
(3,'Contoh Desa','320101001','Kecamatan Contoh','320101','Kabupaten Contoh','3201','Jawa Barat','32','Jl. Raya Desa No. 1, Kecamatan Contoh, Kabupaten Contoh','40000','(022) 1234567','desa@contoh.id','https://desa-contoh.id',500.00,450.00,50.00,200.00,100.00,100.00,500.00,2000.00,'Af (Hujan Sepanjang Tahun)','Desa Sebelah Utara','Desa Sebelah Selatan','Desa Sebelah Timur','Desa Sebelah Barat','Dataran tinggi dengan sedikit perbukitan. Sebagian besar wilayah adalah lahan pertanian dan perkebunan.',NULL,5000,2500,2500,1500,15,5,3,10,15,5,2,'Pertumbuhan penduduk stabil. Mayoritas penduduk bekerja di sektor pertanian.','Bapak Kepala Desa','197001012000121001','2021-01-01','2026-12-31','Ibu Sekretaris Desa',10,5,NULL,'Peraturan Desa No. 1 Tahun 2021 tentang Pendirian BUMDes Maju Jaya',50,2,3000000.00,8.50,5.20,'Padi, Jagung, Kopi, Kakao, Sayuran','Pertanian, Perkebunan, Peternakan, Perdagangan, Jasa','Potensi pertanian organik dan agrowisata. Kopi robusta kualitas premium.','Akses pasar terbatas, kurangnya pengolahan hasil pertanian, infrastruktur jalan perlu ditingkatkan.',5,3,4,1,95.00,98.00,'SD Negeri 1, SD Negeri 2, MI Al-Hidayah, SMP Negeri 1, MTS Al-Ikhlas','Puskesmas Pembantu, Posyandu 5, Apotek Desa',NULL,NULL,'Tingkat literasi tinggi. Fasilitas pendidikan cukup memadai.',25.50,15.00,10.50,3,5,95,80,70,NULL,'Listrik sudah merata. Air bersih dari PDAM dan sumur gali. Internet masih perlu penambahan tower.',1200000000.00,800000000.00,200000000.00,150000000.00,50000000.00,2400000000.00,2200000000.00,'2026','Anggaran desa dialokasikan untuk pembangunan infrastrasi, pemberdayaan masyarakat, dan operasional pemerintahan.','BUMDes Maju Jaya','SK/01/PDT/2021','2021-03-15',100000000.00,250000000.00,500000000.00,300000000.00,50000000.00,15,50,'Menjadi BUMDes yang mandiri dan sejahterakan masyarakat desa melalui pengelolaan potensi desa secara profesional.','1. Mengelola potensi desa secara profesional\\n2. Memberdayakan masyarakat desa\\n3. Menciptakan lapangan kerja\\n4. Meningkatkan pendapatan asli desa','1. Pengelolaan Air Bersih\\n2. Simpan Pinjam\\n3. Toko Desa\\n4. Jasa Pertanian\\n5. Agrowisata','1. Berhasil menyuplai air bersih ke 80% rumah tangga\\n2. Pemberian pinjaman ke 50 UMKM\\n3. Omset meningkat 25% per tahun','1. Agrowisata Kopi\\n2. Curug/ Air Terjun\\n3. Kampung Adat\\n4. Jalur Tracking Gunung','Padi, Jagung, Ubi Jalar, Kacang Tanah, Sayuran Organik','Sapi Potong, Kambing, Ayam Kampung, Lele','Ikan Lele, Ikan Nila, Udang Air Tawar','Kerajinan Bambu, Anyaman Rotan, Batik Tulis','1. Tari Tradisional\\n2. Upacara Adat\\n3. Kuliner Khas Desa\\n4. Seni Budaya Lainnya','Hutan Lindung, Sungai, Mata Air, Tanah Subur untuk Pertanian','Pemuda kreatif, Petani trampil, Pengrajin handal',NULL,NULL,NULL,NULL,NULL,NULL,'(022) 1234567','62812345678','desa@contoh.id','https://facebook.com/desacontoh','https://instagram.com/desacontoh','https://youtube.com/@desacontoh',NULL,NULL,'Desa Contoh adalah desa yang terletak di Kecamatan Contoh, Kabupaten Contoh, Provinsi Jawa Barat. Desa ini memiliki potensi alam yang melimpah dan masyarakat yang gotong royong. Dengan luas wilayah 500 hektar dan penduduk sekitar 5.000 jiwa, desa ini dikenal sebagai penghasil kopi robusta berkualitas tinggi.','Desa Contoh didirikan pada tahun 1800-an oleh para pendatang yang mencari lahan pertanian. Nama \"Contoh\" diambil dari julukan yang diberikan karena desa ini sering dijadikan contoh dalam pengelolaan pertanian. Pada tahun 2021, BUMDes Maju Jaya didirikan untuk mengelola potensi desa secara profesional.','Bersama Membangun Desa yang Maju dan Sejahtera','Desa Contoh - Profil Desa & BUMDes Maju Jaya','Profil lengkap Desa Contoh, Kecamatan Contoh, Kabupaten Contoh. Informasi BUMDes, potensi desa, data demografis, dan layanan masyarakat.',NULL,1,NULL,'2026-06-02 20:24:06','2026-06-02 22:10:36','2026-06-02 22:10:36'),
(4,'Contoh Desa','320101001','Kecamatan Contoh','320101','Kabupaten Contoh','3201','Jawa Barat','32','Jl. Raya Desa No. 1, Kecamatan Contoh, Kabupaten Contoh','40000','(022) 1234567','desa@contoh.id','https://desa-contoh.id',500.00,450.00,50.00,200.00,100.00,100.00,500.00,2000.00,'Af (Hujan Sepanjang Tahun)','Desa Sebelah Utara','Desa Sebelah Selatan','Desa Sebelah Timur','Desa Sebelah Barat','Dataran tinggi dengan sedikit perbukitan. Sebagian besar wilayah adalah lahan pertanian dan perkebunan.',NULL,5000,2500,2500,1500,15,5,3,10,15,5,2,'Pertumbuhan penduduk stabil. Mayoritas penduduk bekerja di sektor pertanian.','Bapak Kepala Desa','197001012000121001','2021-01-01','2026-12-31','Ibu Sekretaris Desa',10,5,NULL,'Peraturan Desa No. 1 Tahun 2021 tentang Pendirian BUMDes Maju Jaya',50,2,3000000.00,8.50,5.20,'Padi, Jagung, Kopi, Kakao, Sayuran','Pertanian, Perkebunan, Peternakan, Perdagangan, Jasa','Potensi pertanian organik dan agrowisata. Kopi robusta kualitas premium.','Akses pasar terbatas, kurangnya pengolahan hasil pertanian, infrastruktur jalan perlu ditingkatkan.',5,3,4,1,95.00,98.00,'SD Negeri 1, SD Negeri 2, MI Al-Hidayah, SMP Negeri 1, MTS Al-Ikhlas','Puskesmas Pembantu, Posyandu 5, Apotek Desa',NULL,NULL,'Tingkat literasi tinggi. Fasilitas pendidikan cukup memadai.',25.50,15.00,10.50,3,5,95,80,70,NULL,'Listrik sudah merata. Air bersih dari PDAM dan sumur gali. Internet masih perlu penambahan tower.',1200000000.00,800000000.00,200000000.00,150000000.00,50000000.00,2400000000.00,2200000000.00,'2026','Anggaran desa dialokasikan untuk pembangunan infrastrasi, pemberdayaan masyarakat, dan operasional pemerintahan.','BUMDes Maju Jaya','SK/01/PDT/2021','2021-03-15',100000000.00,250000000.00,500000000.00,300000000.00,50000000.00,15,50,'Menjadi BUMDes yang mandiri dan sejahterakan masyarakat desa melalui pengelolaan potensi desa secara profesional.','1. Mengelola potensi desa secara profesional\\n2. Memberdayakan masyarakat desa\\n3. Menciptakan lapangan kerja\\n4. Meningkatkan pendapatan asli desa','1. Pengelolaan Air Bersih\\n2. Simpan Pinjam\\n3. Toko Desa\\n4. Jasa Pertanian\\n5. Agrowisata','1. Berhasil menyuplai air bersih ke 80% rumah tangga\\n2. Pemberian pinjaman ke 50 UMKM\\n3. Omset meningkat 25% per tahun','1. Agrowisata Kopi\\n2. Curug/ Air Terjun\\n3. Kampung Adat\\n4. Jalur Tracking Gunung','Padi, Jagung, Ubi Jalar, Kacang Tanah, Sayuran Organik','Sapi Potong, Kambing, Ayam Kampung, Lele','Ikan Lele, Ikan Nila, Udang Air Tawar','Kerajinan Bambu, Anyaman Rotan, Batik Tulis','1. Tari Tradisional\\n2. Upacara Adat\\n3. Kuliner Khas Desa\\n4. Seni Budaya Lainnya','Hutan Lindung, Sungai, Mata Air, Tanah Subur untuk Pertanian','Pemuda kreatif, Petani trampil, Pengrajin handal',NULL,NULL,NULL,NULL,NULL,NULL,'(022) 1234567','62812345678','desa@contoh.id','https://facebook.com/desacontoh','https://instagram.com/desacontoh','https://youtube.com/@desacontoh',NULL,NULL,'Desa Contoh adalah desa yang terletak di Kecamatan Contoh, Kabupaten Contoh, Provinsi Jawa Barat. Desa ini memiliki potensi alam yang melimpah dan masyarakat yang gotong royong. Dengan luas wilayah 500 hektar dan penduduk sekitar 5.000 jiwa, desa ini dikenal sebagai penghasil kopi robusta berkualitas tinggi.','Desa Contoh didirikan pada tahun 1800-an oleh para pendatang yang mencari lahan pertanian. Nama \"Contoh\" diambil dari julukan yang diberikan karena desa ini sering dijadikan contoh dalam pengelolaan pertanian. Pada tahun 2021, BUMDes Maju Jaya didirikan untuk mengelola potensi desa secara profesional.','Bersama Membangun Desa yang Maju dan Sejahtera','Desa Contoh - Profil Desa & BUMDes Maju Jaya','Profil lengkap Desa Contoh, Kecamatan Contoh, Kabupaten Contoh. Informasi BUMDes, potensi desa, data demografis, dan layanan masyarakat.',NULL,1,NULL,'2026-06-02 20:24:34','2026-06-02 22:10:40','2026-06-02 22:10:40');
/*!40000 ALTER TABLE `village_info` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-03 22:26:50
