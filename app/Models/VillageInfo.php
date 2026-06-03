<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VillageInfo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'village_info';

    protected $fillable = [
        // Data Umum
        'village_name', 'village_code', 'district_name', 'district_code',
        'regency_name', 'regency_code', 'province_name', 'province_code',
        'address', 'postal_code', 'phone', 'email', 'website',
        
        // Data Geografis
        'area_total', 'area_land', 'area_water', 'area_farming',
        'area_settlement', 'area_forest', 'elevation', 'rainfall',
        'climate_type', 'border_north', 'border_south', 'border_east',
        'border_west', 'topography', 'geography_notes',
        
        // Data Demografis
        'total_population', 'male_population', 'female_population',
        'total_family', 'total_rt', 'total_rw', 'total_dusun',
        'population_density', 'birth_rate', 'death_rate', 'growth_rate',
        'population_notes',
        
        // Data Pemerintahan
        'head_village_name', 'head_village_nip', 'head_village_start',
        'head_village_end', 'village_secretary_name', 'total_staff',
        'total_bpd_members', 'organizational_structure', 'village_regulation',
        
        // Data Ekonomi
        'total_umskm', 'total_market', 'avg_income', 'poverty_rate',
        'unemployment_rate', 'main_commodities', 'economic_activities',
        'economic_potential', 'economic_challenges',
        
        // Data Sosial
        'total_schools', 'total_health_facilities', 'total_mosques',
        'total_churches', 'literacy_rate', 'school_participation_rate',
        'education_facilities', 'health_facilities', 'social_facilities',
        'religious_facilities', 'social_notes',
        
        // Data Infrastruktur
        'total_road_length', 'road_paved', 'road_not_paved', 'total_bridges',
        'total_irrigation', 'electricity_coverage', 'clean_water_coverage',
        'internet_coverage', 'road_conditions', 'infrastructure_notes',
        
        // Data Keuangan
        'village_fund', 'add_fund', 'bdg_fund', 'own_revenue',
        'other_revenue', 'total_budget', 'total_expenditure',
        'budget_year', 'budget_notes',
        
        // Data BUMDes
        'bumdes_name', 'bumdes_legal_number', 'bumdes_established',
        'bumdes_initial_capital', 'bumdes_current_capital', 'bumdes_total_assets',
        'bumdes_annual_revenue', 'bumdes_profit', 'bumdes_employees',
        'bumdes_partners', 'bumdes_vision', 'bumdes_mission',
        'bumdes_services', 'bumdes_achievements',
        
        // Data Potensi
        'tourism_potential', 'agriculture_potential', 'livestock_potential',
        'fishery_potential', 'craft_potential', 'cultural_potential',
        'natural_potential', 'human_resource_potential', 'potential_notes',
        
        // Media
        'logo_path', 'banner_path', 'map_path', 'photo_gallery', 'video_links',
        
        // Kontak & Media Sosial
        'contact_phone', 'contact_whatsapp', 'contact_email',
        'facebook', 'instagram', 'youtube', 'tiktok', 'twitter',
        
        // Meta
        'about_village', 'village_history', 'village_motto',
        'meta_title', 'meta_description', 'meta_keywords',
        'is_published', 'published_at',
    ];

    protected $casts = [
        'area_total' => 'decimal:2',
        'area_land' => 'decimal:2',
        'area_water' => 'decimal:2',
        'area_farming' => 'decimal:2',
        'area_settlement' => 'decimal:2',
        'area_forest' => 'decimal:2',
        'elevation' => 'decimal:2',
        'rainfall' => 'decimal:2',
        'total_population' => 'integer',
        'male_population' => 'integer',
        'female_population' => 'integer',
        'total_family' => 'integer',
        'total_rt' => 'integer',
        'total_rw' => 'integer',
        'total_dusun' => 'integer',
        'population_density' => 'integer',
        'birth_rate' => 'integer',
        'death_rate' => 'integer',
        'growth_rate' => 'integer',
        'total_staff' => 'integer',
        'total_bpd_members' => 'integer',
        'total_umskm' => 'integer',
        'total_market' => 'integer',
        'avg_income' => 'decimal:2',
        'poverty_rate' => 'decimal:2',
        'unemployment_rate' => 'decimal:2',
        'total_schools' => 'integer',
        'total_health_facilities' => 'integer',
        'total_mosques' => 'integer',
        'total_churches' => 'integer',
        'literacy_rate' => 'decimal:2',
        'school_participation_rate' => 'decimal:2',
        'total_road_length' => 'decimal:2',
        'road_paved' => 'decimal:2',
        'road_not_paved' => 'decimal:2',
        'total_bridges' => 'integer',
        'total_irrigation' => 'integer',
        'electricity_coverage' => 'integer',
        'clean_water_coverage' => 'integer',
        'internet_coverage' => 'integer',
        'village_fund' => 'decimal:2',
        'add_fund' => 'decimal:2',
        'bdg_fund' => 'decimal:2',
        'own_revenue' => 'decimal:2',
        'other_revenue' => 'decimal:2',
        'total_budget' => 'decimal:2',
        'total_expenditure' => 'decimal:2',
        'bumdes_initial_capital' => 'decimal:2',
        'bumdes_current_capital' => 'decimal:2',
        'bumdes_total_assets' => 'decimal:2',
        'bumdes_annual_revenue' => 'decimal:2',
        'bumdes_profit' => 'decimal:2',
        'bumdes_employees' => 'integer',
        'bumdes_partners' => 'integer',
        'photo_gallery' => 'array',
        'video_links' => 'array',
        'is_published' => 'boolean',
        'head_village_start' => 'date',
        'head_village_end' => 'date',
        'bumdes_established' => 'date',
        'published_at' => 'datetime',
    ];

    // Accessor untuk format Rupiah
    public function getFormattedVillageFundAttribute()
    {
        return 'Rp ' . number_format($this->village_fund ?? 0, 0, ',', '.');
    }

    public function getFormattedAddFundAttribute()
    {
        return 'Rp ' . number_format($this->add_fund ?? 0, 0, ',', '.');
    }

    public function getFormattedBumdesCapitalAttribute()
    {
        return 'Rp ' . number_format($this->bumdes_current_capital ?? 0, 0, ',', '.');
    }

    public function getFormattedBumdesRevenueAttribute()
    {
        return 'Rp ' . number_format($this->bumdes_annual_revenue ?? 0, 0, ',', '.');
    }

    // Scope untuk data yang dipublikasikan
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Get single instance (singleton pattern)
    public static function getVillageInfo()
    {
        return static::firstOrCreate([], [
            'village_name' => 'Nama Desa',
            'district_name' => 'Nama Kecamatan',
            'regency_name' => 'Nama Kabupaten',
            'province_name' => 'Nama Provinsi',
            'address' => 'Alamat Kantor Desa',
            'bumdes_name' => 'Nama BUMDes',
            'total_population' => 0,
            'total_family' => 0,
        ]);
    }
}
