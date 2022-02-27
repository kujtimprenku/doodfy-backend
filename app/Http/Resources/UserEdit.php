<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\City;
use App\Country;

class UserEdit extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'firstName' => $this->firstname,
            'lastName' => $this->lastname,
            'username' => $this->username,
            'email' => $this->email,
            'city' => City::find($this->city_id)->only(['id','country_id','name']),
            'country' => Country::find($this->my_country_id($this->city_id))->name,
            'address' => $this->address,
            'gender' => $this->gender,
            'xp_purchase' => $this->myXpPurchase($this->id) == null ? 0 : $this->myXpPurchase($this->id),
            'profile_image' => $this->profile_image,
            'birth_date' => date('D M d Y H:i:s e P', strtotime($this->birth_date)),
            'biography' => $this->biography,
            'facebook_url' => $this->facebook_url,
            'instagram_url' => $this->instagram_url,
            'twitter_url' => $this->twitter_url,


        ];
    }
}
