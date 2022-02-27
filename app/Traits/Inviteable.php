<?php

namespace App\Traits;
use App\Club;
use App\User;
use App\Group;
use App\Invites;
use App\Membership;
use Illuminate\Database\Eloquent\Model;


trait Inviteable{

    /**
     * @param $model
     * @return \Illuminate\Database\Eloquent\Collection|Invites[]
     */
    public function sendInvite(Model $recipient, Model $where){

        $membership = (new Invites())->fillRecipient($recipient)->fillWhere($where)->fill(['status_id' => 0]);

        $this->inviteships()->save($membership);
        return $membership;
}

    public function acceptInvite(Model $recipient,Model $where)
    {
        $accepted = $this->findInviter($recipient)->whereRecipient($this)->whereWhere($where)->update([
            'status_id' => 1,
        ]);

        return $accepted;
    }


    private function findInviter(Model $recipient){

        return Invites::betweenModels($this, $recipient);
    }

    private function findInviteships($status = null, $group_id)
    {

        $query = Invites::where(function ($query) {
            $query->where(function ($q) {
                $q->whereSender($this);
            })->orWhere(function ($q) {
                $q->whereRecipient($this);
            });
        });

        //if $status is passed, add where clause
        if (!is_null($status)) {
            $query->where('status_id', $status)->where('where_id', $group_id);
        }

        return $query;
    }

    public function getPendingInviteShips($group_id)
    {
        return $this->findInviteships(0, $group_id)->get();
    }


//  /**
//      * @return \Illuminate\Database\Eloquent\Collection|Inviteable[]
//      */
//     public function getAcceptedMemberships()
//     {
//         return $this->findMemberships(1)->get();
//     }
}
?>
