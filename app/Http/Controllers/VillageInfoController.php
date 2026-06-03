<?php

namespace App\Http\Controllers;

use App\Models\VillageInfo;
use Illuminate\Http\Request;

class VillageInfoController extends Controller
{
    public function index()
    {
        $villageInfo = VillageInfo::first();
        
        return view('village-info.index', compact('villageInfo'));
    }
    
    public function profile()
    {
        $villageInfo = VillageInfo::first();
        
        return view('village-info.profile', compact('villageInfo'));
    }
    
    public function geography()
    {
        $villageInfo = VillageInfo::first();
        
        return view('village-info.geography', compact('villageInfo'));
    }
    
    public function demographics()
    {
        $villageInfo = VillageInfo::first();
        
        return view('village-info.demographics', compact('villageInfo'));
    }
    
    public function government()
    {
        $villageInfo = VillageInfo::first();
        
        return view('village-info.government', compact('villageInfo'));
    }
    
    public function economy()
    {
        $villageInfo = VillageInfo::first();
        
        return view('village-info.economy', compact('villageInfo'));
    }
    
    public function bumdes()
    {
        $villageInfo = VillageInfo::first();
        
        return view('village-info.bumdes', compact('villageInfo'));
    }
    
    public function potential()
    {
        $villageInfo = VillageInfo::first();
        
        return view('village-info.potential', compact('villageInfo'));
    }
    
    public function contact()
    {
        $villageInfo = VillageInfo::first();
        
        return view('village-info.contact', compact('villageInfo'));
    }
}
