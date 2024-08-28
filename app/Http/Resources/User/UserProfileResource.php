<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'location'   => $this->location,
            'birth_date' => $this->birth_date,
            'language'   => $this->language,
            'gender'     => $this->gender,
            'address'    => $this->address,
            'image'      => $this->getFirstMediaUrl('profile_images')
        ];
    }
}
