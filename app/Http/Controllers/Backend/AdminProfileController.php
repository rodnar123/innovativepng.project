<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Admin;
use Auth;


class AdminProfileController extends Controller
{
    //

    public function AdminProfile(){

        $adminData = Admin::find(1);
        return view('admin.admin_profile_view',compact('adminData'));
    }

    public function AdminProfileEdit(){
        $adminProfileEdit = Admin::find(1);
        return view('admin.admin_profile_edit',compact('adminProfileEdit'));
    }

    public function AdminProfileUpdate(Request $request){
        $data = Admin::find(1);

        $data->name = $request->name;
        $data->email = $request->email;

        if($request->file('profile_photo_path')){
            $file = $request->file('profile_photo_path');
            @unlink(public_path('upload/admin_images/'.$data->profile_photo_path));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'),$filename);
            $data['profile_photo_path'] = $filename;
        }
        $data->save();
        $notification = array(
            'message' => 'Profile Details Updated Succesfully',
            'alert-type' => 'success'
        );

        return Redirect()->route('admin.profile')->with($notification);

    }

    public function AdminChangePassword(){
        return view('admin.admin_change_password');
    }

    public function AdminUpdatePassword(Request $request){

        $validateData = $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed', 
        ]);
        $hashedPassword = Admin::find(1)->password;
        if(Hash::check($request->current_password,$hashedPassword)){

            $admin = Admin::find(1);
            $admin->password = Hash::make($request->password);
            $admin->save();
            Auth::logout();

            $notification = array(
                'message' => 'Admin Updated Succesfully',
                'alert-type' => 'info'
            );

            return Redirect()->route('admin.logout')->with($notification);

        }else{
            $notification = array(
                'message' => 'Invalid Attempt',
                'alert-type' => 'warning'
            );

            return Redirect()->back()->with($notification);
        }

    }





}
