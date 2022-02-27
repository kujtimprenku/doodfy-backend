<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class Company extends JsonResource
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
            'user_id' => $this->user_id,
            'firm' => $this->firm,
            'street' => $this->street,
            'branch' => $this->branch,
            'website' => $this->website,
            'profile_logo'=>$this->profile_image,
            'cover_image' => $this->cover_image,
            'logo'=>$this->logo,
            'description' => $this->description,
            'facebook_url' => $this->facebook_url,
            'instagram_url' => $this->instagram_url,
            'twitter_url' => $this->twitter_url,
            'my_company' => auth('api')->check() ? ($this->user_id === auth('api')->user()->id) : false,
            'nr_subscribers' => $this->followers()->count(),
            'has_subscribed' => $this->hasUserSubscribed($this->id),
            'has_activities' => $this->activities($this->user_id),
        ];
    }
}
