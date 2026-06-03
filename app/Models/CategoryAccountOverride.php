<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryAccountOverride extends Model
{
    protected $fillable = ['category_id', 'unit_id', 'account_code'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(BusinessUnit::class, 'unit_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_code', 'code');
    }
}
