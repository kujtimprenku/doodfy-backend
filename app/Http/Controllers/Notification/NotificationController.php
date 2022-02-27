<?php

namespace App\Http\Controllers\Notification;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{

    /**
     * @param int $limit //only 10 or 50
     * @return \Illuminate\Http\Response
     */
    public function notifications()
    {
        if(request()->limit != 10 && request()->limit != 50){
            return response()->json(['error' => 'Something goes wrong']);
        }

        $joinMyActivity = $this->oneOrMoreNotifications('App\Notifications\JoinMyActivity',
            ' joined to your activity ',
            'join'
        );
        $commentInMyActivity = $this->oneOrMoreNotifications('App\Notifications\CommentToYourActivity',
            ' commented to your activity ',
            'comment'
        );
        $inviteFriendsToActivity = $this->oneOrMoreNotifications('App\Notifications\InviteFriendsToActivity',
            ' invited you to join ',
            'invite'
        );
        $inviteFriendsToGroup = $this->oneOrMoreNotifications('App\Notifications\InviteFriendsToGroup',
            ' invited you to join group',
            'invite_group'
        );

        $all = array_merge(
            $joinMyActivity,
            $commentInMyActivity,
            $inviteFriendsToActivity,
            $inviteFriendsToGroup
        );

        return collect($all)->sortByDesc('created_at_order')->slice(0, request()->limit)->values();
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\Response $message
     */
    public function removeNotification($id)
    {
        $notifications_id = explode('&', $id);
        foreach($notifications_id as $notification_id){
            DB::table('notifications')->where('id', $notification_id)->delete();
        }
        return response()->json(['message' => 'Deleted successfully']);
    }

    /**
     *
     */
    public function countUnReadNotifications()
    {
        $user = auth('api')->user();
        return $user->unreadNotifications()->count();
    }

    /**
     *
     */
    public function makeReadNotifications()
    {
        $user = auth('api')->user();
        $user->unreadNotifications()->update(['read_at' => now()]);
        return [];
    }

    /**
     *
     */
    private function oneOrMoreNotifications($type, $message, $type2)
    {
        $user = auth('api')->user();
        $data = [];
        $notifications_db = [];
        $notifications = [];

        foreach($user->notifications as $notification){
            if($notification->type == $type){
                $data = $notification->data;
                $data['message'] = $message;
                $data['created_at'] = date('D M d Y H:i:s e P', strtotime($notification->created_at));
                $data['created_at_order'] = $notification->created_at;
                $data['type'] = $type2;
                $data['id'] = $notification->id;
                array_push($notifications_db, $data);
            }
        }

        $grouped_notifications_by_activity_id =  collect($notifications_db)->groupBy('activity.id')->toArray();
        foreach($grouped_notifications_by_activity_id as $notifications_by_activity_id)
        {
            $unique_users = collect($notifications_by_activity_id)->unique('user.username')->values()->reverse()->toArray();
            if (count($unique_users) ==  1) {
                $one_notification =  $notifications_by_activity_id[0];
                array_push($notifications, $one_notification);
            }
            else {
                foreach ($unique_users as $unique_user) {
                    $model_name = array_keys($unique_user)[1];  //second element of notification object
                    $more_notifications =  [
                        'user' => $unique_user['user'],
                        $model_name => $unique_user[$model_name],
                        'message' => ' and ' . (count($unique_users) - 1) . ' others ' . $message,
                        'created_at' => date('D M d Y H:i:s e P', strtotime($notifications_by_activity_id[0]['created_at'])),
                        'created_at_order' => $notifications_by_activity_id[0]['created_at_order'],
                        'type' => $type2,
                        'id' => collect($notifications_by_activity_id)->pluck('id')->implode('&')
                    ];
                };
                array_push($notifications, $more_notifications);
            }
        }
        return $notifications;
    }
}
