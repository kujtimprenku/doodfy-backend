<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            'id' => $this->id,
            'user' => User::find($this->user_id),
            'city_id' => $this->city_id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
            'group_type' => $this->group_type,
            'cover_image' => $this->cover_image,
            'facebook_url' => $this->facebook_url,
            'instagram_url' => $this->instagram_url,
            'twitter_url' => $this->twitter_url,
            'my_group' => auth('api')->user() ? ($this->user_id === auth('api')->user()->id) : false,
            'membership' => auth('api')->user() ? ($this->membership(auth('api')->user()->id, $this->id)) : -1,
            'nr_members' => $this->getAcceptedMemberships($this->id)->count(),
        ];
    }
}
