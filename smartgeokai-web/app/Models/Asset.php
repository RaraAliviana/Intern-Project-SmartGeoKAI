<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $table = 'assets';

    protected $fillable = [
        'id_asset',
        'asset_type',
        'classification',
        'asset_group',
        'province_id',
        'regency_id',
        'district_id',
        'area_m2',
        'latitude',
        'longitude',
        'gmaps_url',
        'status',
        'status_updated_at',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'area_m2' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'status_updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI FOTO ASET
    |--------------------------------------------------------------------------
    */

    public function images()
    {
        return $this->hasMany(AssetImage::class, 'asset_id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI WILAYAH & USER
    |--------------------------------------------------------------------------
    */

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}