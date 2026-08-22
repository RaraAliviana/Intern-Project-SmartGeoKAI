<?php
// app/Models/District.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'districts';
    public $timestamps = false;

    protected $fillable = ['regency_id', 'code', 'name'];

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }
}