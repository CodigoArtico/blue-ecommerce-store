<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonio extends Model
{
    use HasFactory;

    protected $table = 'testimonios';

    protected $fillable = [
        'user_web_id',
        'testimonio',
    ];

    public function userWeb()
    {
        return $this->belongsTo(UserWeb::class, 'user_web_id');
    }
}
