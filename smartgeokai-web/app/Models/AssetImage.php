<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetImage extends Model
{
    use HasFactory;

    protected $table = 'asset_images';

    protected $fillable = [
        'asset_id',
        'file_path',
        'file_name',
        'file_size',
        'is_before_repair',
        'is_after_repair',
        'uploaded_by',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}