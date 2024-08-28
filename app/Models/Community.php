<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;

    protected $table = 'communities';

    protected $fillable = ['name', 'user_id', 'about', 'location', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'community_users', 'community_id', 'user_id')->withPivot('status', 'approved_at')->withTimestamps();
    }

    protected static function booted()
    {
        static::creating(function ($community) {
            if (is_null($community->status)) {
                $community->status = 'active'; // Default status if not set
            }
        });
    }
}
