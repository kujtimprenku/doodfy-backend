<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'city_id' => $this->city_id,
            'role_id' => $this->role_id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'username' => $this->username,
            'email' => $this->email,
            'position' => $this->position,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'language' => $this->language,
            'address' => $this->address,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'profile_image' => $this->thumbnail($this->profile_image, 300),
            'is_active' => $this->is_active,
            'xp_points' => $this->countMyXp($this->id) == null ? 0 : $this->countMyXp($this->id),
            'website' => $this->website,
            'biography' => $this->biography,
            'facebook_url' => $this->facebook_url,
            'instagram_url' => $this->instagram_url,
            'twitter_url' => $this->twitter_url,
            'friendship' => $this->friendship($this->id),
            'topics' => $this->topicCategories($this->id),
            'rating'=>$this->getAverageRatingsForUser($this->id),
            'nr_activities'=>$this->activities->count(),
        ];
    }
}
