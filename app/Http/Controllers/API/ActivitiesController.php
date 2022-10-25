<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Libraries\Response;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ActivitiesController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page') ?? 1;
        $limit = 3;

        $offset = ($page - 1) * 3;

        $total = UserActivity::where('user_id', auth()->id())->count();

        $total_page = ceil($total/$limit);

        $activities = UserActivity::where('user_id', auth()->id())->limit($limit)->offset($offset)->get();

        $data = [
            'total' => $total,
            'data'  => $activities,
            'prev_page' => ($page > 1) ? url('api/activities?page='.$page-1) : null,
            'next_page' => ($page < $total_page) ? url('api/activities?page='.$page+1) : null
        ];

        return Response::success($data);
    }

    public function show(UserActivity $activity)
    {
        if ($activity->user_id != auth()->id() && auth()->user()->role_id != config('constants.USER_ROLES.ADMIN')) {
            return Response::error("You don't have permission to access this activity");
        }
        return Response::success($activity);
    }

    public function fetch_activity()
    {
        $user_activities_count = 0;
        $repetitive_count = 0;

        $user = auth()->user();
        $start_date = date('Y-m-d 00:00:00');
        $end_date   = date('Y-m-d 23:59:59');

        $today_activities_count = UserActivity::where('user_id', $user->id)->whereBetween('created_at', [$start_date, $end_date])->count();

        if ($today_activities_count >= 2) {
            return Response::error("Today's activity limit is over. Try next day");
        }

        while($user_activities_count < 1) {
            $response = Http::get("http://www.boredapi.com/api/activity?type=".$user->activity_type->name);
            if ($response->successful()) {
                $activity_response  = $response->collect()->toArray();
                if (!UserActivity::where('user_id', $user->id)->where('activity_key', $activity_response['key'])->exists()) {
                    $user->user_activities()->create([
                        'activity'  => $activity_response['activity'],
                        'activity_type_id'  => $user->activity_type_id,
                        'participants'  => $activity_response['participants'],
                        'price'         => $activity_response['price'],
                        'link'          => $activity_response['link'],
                        'activity_key'  => $activity_response['key']
                    ]);
                    $user_activities_count++;
                    $repetitive_count = 0;
                } else {
                    $repetitive_count++;
                    if ($repetitive_count >= 10) {
                        break;
                    }
                }
            }
        }

        if ($repetitive_count >= 10) {
            return Response::error('No new activities found');
        }

        return Response::success('Activity fetch successfully');
    }

    public function update(UserActivity $activity, Request $request)
    {
        $activity_text         = $request->post('activity_text');
        $activity->activity     = $activity_text;
        $activity->save();

        return Response::success('Activity updated successfully');
    }

    public function destroy(UserActivity $activity)
    {
        if (auth()->user()->role_id != config('constants.USER_ROLES.ADMIN'))
        {
            return Response::error("You don't have permission to delete this activity");
        }

        $activity->delete();

        return Response::success('Activity deleted successfully');
    }
}
