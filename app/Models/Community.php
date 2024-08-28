<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;

    protected $table = 'communities';

    protected $fillable = ['name', 'user_id', 'about', 'location', 'status'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function ($community) {
            if (is_null($community->status)) {
                $community->status = 'pending'; // Default status if not set
            }
        });
    }
}
