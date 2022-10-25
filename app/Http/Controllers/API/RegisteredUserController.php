<?php

namespace App\Http\Controllers\API;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegisteredUserRequest;
use App\Libraries\Response;
use App\Models\User;
use App\Models\UserActivity;
use App\Repositories\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public function store(StoreRegisteredUserRequest $request)
    {
        $name               = $request->post('name');
        $email              = $request->post('email');
        $password           = $request->post('password');
        $activity_type_id   = $request->post('activity_type_id');

        $response = Http::get("http://www.boredapi.com/api/activity?type=recreational");

        $registeredUser = new User();

        $registeredUser->name               = trim($name);
        $registeredUser->email              = trim($email);
        $registeredUser->password           = Hash::make($password);
        $registeredUser->remember_token     = Str::random(10);
        $registeredUser->role_id            = config('constants.USER_ROLES.USER');
        $registeredUser->activity_type_id   = $activity_type_id;

        $registeredUser->save();

        $user_activities_count = 0;
        $repetitive_count = 0;

        while($user_activities_count < 10) {
            $response = Http::get("http://www.boredapi.com/api/activity?type=".$registeredUser->activity_type->name);
            if ($response->successful()) {
                $activity_response  = $response->collect()->toArray();
                if (!UserActivity::where('user_id', $registeredUser->id)->where('activity_key', $activity_response['key'])->exists()) {
                    $registeredUser->user_activities()->create([
                        'activity'  => $activity_response['activity'],
                        'activity_type_id'  => $activity_type_id,
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

        UserRegistered::dispatch($registeredUser);

        return Response::success('Registered Successfully', $registeredUser);
    }
}
