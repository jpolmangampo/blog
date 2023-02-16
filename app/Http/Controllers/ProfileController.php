<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Password;

class ProfileController extends Controller
{
    private $user;
    const LOCAL_STORAGE_FOLDER = 'public/avatars';

    public function __construct(User $user){
        $this->user = $user;
    }

    public function show($id){
        $user = $this->user->findOrFail($id);

        return view('users.profile.show')->with('user', $user);
    }

    public function edit(){
        $user = $this->user->findOrFail(Auth::user()->id);

        return view('users.profile.edit')->with('user', $user);

    }

    public function update(Request $request){
        $request->validate([
            'name'      => 'required|min:1|max:50',
            'email'     => 'required|email|max:50|unique:users,email,' . Auth::user()->id,
            'avatar'    => 'mimes:jpg,jpeg,gif,png|max:1048',
            'introduction' => 'max:100'
        ]);
        $user               = $this->user->findOrFail(Auth::user()->id);
        $user->name         = $request->name;
        $user->email        = $request->email;
        $user->introduction = $request->introduction;

        // if the user uploads an avatar
        if($request->avatar){
            // check if the user currently has an avatar, if true, delete the avatar from local storage
            if($user->avatar){
                $this->deleteAvatar($user->avatar);
            }
            // save the new avatar in the local storage
            $user->avatar = $this->saveAvatar($request);

        }
        $user->save();
        return redirect()->route('profile.show', Auth::user()->id);

    }

    private function deleteAvatar($avatar_name){
        $avatar_path = self::LOCAL_STORAGE_FOLDER . $avatar_name;

        if(Storage::disk('local')->exists($avatar_path)){
            Storage::disk('local')->delete($avatar_path);
        }
    }

    private function saveAvatar($request){
        // rename the image to the CURRENT TIME
        $avatar_name = time() . "." . $request->avatar->extension();
        // image.png => 16546834643465465421.png

        $request->avatar->storeAs(self::LOCAL_STORAGE_FOLDER, $avatar_name);

        return $avatar_name;

    }

    public function followers($id){
        $user = $this->user->findOrFail($id);
        return view('users.profile.followers')->with('user', $user);
    }

    public function following($id){
        $user = $this->user->findOrFail($id);
        return view('users.profile.following')->with('user', $user);
    }

    public function updatePassword(Request $request)
    {   
        $error_password = 'Unable to change your password.';
        
        if(!Hash::check($request->current_password, Auth::user()->password)){
            $current_password_error = 'That is not your current password. Try again.';
            
            return redirect()->back()
                ->with('current_password_error', $current_password_error)
                ->with('error_password',$error_password );
        }

        if($request->current_password === $request->new_password){
            $new_password_error = 'New password cannot be the same with your current password. Try again.';
            return redirect()->back()
                ->with('new_password_error', $new_password_error)
                ->with('error_password', $error_password);
        }

        $request->validate([
            'new_password' => 'required|confirmed|min:8|alpha_num'
        ]);

        $user           = $this->user->findOrFail(Auth::user()->id);
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success_password', 'Password changed successfully.');

    }

}

