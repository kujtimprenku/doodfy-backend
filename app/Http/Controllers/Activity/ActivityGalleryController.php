<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Http\Resources\ActivityGalleryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ActivityGalleryController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $activity = Activity::findOrFail($request->activity_id);
        foreach($request->images as $image){
            $img_name = $activity->imageUpload($image, 'activity', 300, 100);
            DB::table('activity_gallery')->insert([
                'user_id' => auth('api')->user()->id,
                'activity_id' => $request->activity_id,
                'img_name' => $img_name,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        return response()->json(['message' => 'Uploaded successfully!']);
    }


    /**
     * @param $activity_id
     * @return ActivityGalleryResource
     */
    public function activityGallery($activity_id, $size)
    {
        $activity_gallery = DB::table('activity_gallery')
            ->where('activity_id', $activity_id)
            ->get()
            ->each(function($item) use($size){
                if ($size == 'thumbnail') {
                    $item->img_name = str_replace('.png', '_100x100.png', $item->img_name);
                }
            });

        return ActivityGalleryResource::collection($activity_gallery);
    }


    /**
     * @param $activity_id
     * @return ActivityGalleryResource
     */
    public function removeImageFromGallery($activity_id, $img_id)
    {
        $image = DB::table('activity_gallery')->where('id', $img_id)->first();
        try {
            $img_name = explode('uploads', $image->img_name);
            Storage::disk('local')->delete(str_replace('.png', '_100x100.png', $img_name[1]));
            return response()->json(['message' => 'Removed successfully!']);
        }
        catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong!']);
        }
        finally {
            Storage::disk('local')->delete($img_name[1]);
            Storage::disk('local')->delete(str_replace('.png', '_300x300.png', $img_name[1]));
            DB::table('activity_gallery')->where('id', $img_id)->delete();
        }
    }
}
