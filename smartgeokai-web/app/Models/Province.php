<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'provinces';

    public $timestamps = false; // tabel provinces tidak punya created_at/updated_at

    protected $fillable = [
        'code',
        'name',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function regencies()
    {
        return $this->hasMany(Regency::class);
    }
}