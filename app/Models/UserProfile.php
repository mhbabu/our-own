<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class UserProfile extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['user_id', 'location', 'birth_date', 'language', 'gender', 'address'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_pictures');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
