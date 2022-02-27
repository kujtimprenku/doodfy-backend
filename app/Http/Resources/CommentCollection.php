<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Auth;

class CommentCollection extends Resource
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
            'comment' => $this->comment,
            'created_at' => date('D M d Y H:i:s e P', strtotime($this->created_at)),
            'my_comment' =>  auth('api')->check()  ? $this->user_id == auth('api')->user()->id : false,
            'user' => $this->user->only(['id', 'username', 'profile_image']),
        ];
    }


}
