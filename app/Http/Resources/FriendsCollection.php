<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class FriendsCollection extends Resource
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
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'username' => $this->username,
            'profile_image' => $this->profile_image,
            'has_joined' => $this->has_joined,
            'friendship' => $this->friendship($this->id),
        ];

    }

}
