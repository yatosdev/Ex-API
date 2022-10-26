<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\UserProfile;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function GetProfile()
    {
        $userID = Auth::user()->id;
        $userProfile = UserProfile::where('user_id', $userID)->first();
        if($userProfile == null){
            return response()->json([
                'status' => 'error',
                'message' => 'User Profile not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'userProfile' => $userProfile,
        ]);
    }

    public function GetAllUsers()
    {
        // Get user level
        $userLevel = Auth::user()->level;
        if($userLevel != 1 || $userLevel > 1){
            return response()->json([
                'status' => 'error',
                'users' => 'You`r unauthorized to view this datas',
            ]);
        }
        
        $usersProfile = UserProfile::addSelect('id', 'name', 'email')->get();
        return response()->json([
            'status' => 'success',
            'usersprofile' => $usersProfile,
        ]);
    }

    public function GetUserProfile($id)
    {
        // Get user level
        $userLevel = Auth::user()->level;
        if($userLevel != 1){
            return response()->json([
                'status' => 'error',
                'users' => 'You`r unauthorized to view this datas',
            ]);
        }

        $userProfile = UserProfile::where('id', $id)->first();
        return response()->json([
            'status' => 'success',
            'userProfile' => $userProfile,
        ]);
    }
    
    public function UpdateUserProfile(Request $request, $id)
    {
        // Get user level
        $userLevel = Auth::user()->level;
        // Get user id
        $userID = Auth::user()->id;
        $profileID = UserProfile::where('id', $id)->first()->user_id;
        // Verify if user is admin or if user is the owner of the profile
        if($userID != $profileID && $userLevel != 1){
            return response()->json([
                'status' => 'error',
                'users' => 'You`r unauthorized to change this datas',
            ]);
        }
        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email',
            'phone_number' => 'required|string|max:255',
            'address' => 'required|string|max:40',
            'photo' => 'required|string|max:40',
        ]);
        // Update user profile
        $userprofile = UserProfile::find($id);
        $userprofile->name = $request->name;
        $userprofile->email = $request->email;
        $userprofile->phone_number = $request->phone_number;
        $userprofile->address = $request->address;
        $userprofile->photo = $request->photo;
        $userprofile->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'todo' => $userprofile,
        ]);
    }
}
