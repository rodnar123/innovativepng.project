<?php

namespace App\Http\Controllers\Frontend;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class IndexController extends Controller
{
    //
    public function index(){
        return view('frontend.index');
    }


    public function UserLogout(){
        Auth::logout();
        return Redirect()->route('login');
    }

    public function UserProfile(Request $request){
        $id = Auth::user()->id;
        $user = User::find($id);

        return view('frontend.profile.user_profile_view',compact('user'));
    }

    public function UserProfileUpdate(Request $request){
        $data = User::find(Auth::user()->id);

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;

        if($request->file('profile_photo_path')){
            $file = $request->file('profile_photo_path');
            @unlink(public_path('upload/user_images/'.$data->profile_photo_path));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/user_images'),$filename);
            $data['profile_photo_path'] = $filename;
        }
        $data->save();
        $notification = array(
            'message' => 'User Profile Details Updated Succesfully',
            'alert-type' => 'success'
        );

        return Redirect()->route('dashboard')->with($notification);

    }






    public function UserPassword(){
        $id = Auth::user()->id;
        $user = User::find($id);
       
        return view('frontend.profile.user_change_password',compact('user'));

    }

    public function UserPasswordUpdate(Request $request){

      
        $validateData = $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed', 
        ]);
        $hashedPassword = Auth::user()->password;
        if(Hash::check($request->current_password,$hashedPassword)){

            $user = User::find(Auth::id());
            $user->password = Hash::make($request->password);
            $user->save();
            Auth::logout();

            $notification = array(
                'message' => 'Password Updated Succesfully',
                'alert-type' => 'success'
            );

            return Redirect()->route('user.logout')->with($notification);

        }else{
            $notification = array(
                'message' => 'Invalid Attempt',
                'alert-type' => 'warning'
            );

            return Redirect()->back()->with($notification);

    }
}
}
