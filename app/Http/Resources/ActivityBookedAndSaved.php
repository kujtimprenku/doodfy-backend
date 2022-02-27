<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ActivityBookedAndSaved extends Resource
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
           'title' => $this->title,
           'description'=>$this->description,
           'image' => $this->thumbnail($this->image, 300),
           'location' => $this->location,
           'start_date' => date('F d', strtotime($this->start_date)),
           'end_date' => date('F d, h:i', strtotime($this->end_date)),
           'nr_joined' => $this->hasJoined->count() > 0 ? $this->hasJoined->sum('has_joined') : 0,
           'has_joined' => $this->hasJoined->where('user_id', auth('api')->user()->id)->where('has_joined', 1)->count() > 0 ? true : false,
           'has_saved' => $this->hasJoined->where('user_id', auth('api')->user()->id)->where('has_saved', 1)->count() > 0 ? true : false,
           'has_xp' => $this->has_xp == 1 ? true : false,
           'is_active' => $this->end_date > now(),
           'user' => $this->user->only(['id', 'username']),
           'users' => $this->friendsHasJoined($this->id),
           'rating' =>$this->findNrRaters($this->id),
        ];
    }
}
