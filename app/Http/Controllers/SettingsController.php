<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use Session;
use Validator;

class SettingsController extends Controller
{
	public function __construct() {
		$this->middleware('auth');
	}

    public function profile() {
    	return view('settings.profile');
    }

    public function editProfile() {
    	return view('settings.editprofile');
    }

    public function updateProfile(Request $request){
    	$user = Auth::user();

        $rules = [
            'name' => 'required',
            'email' => 'required|unique:users,email,'. $user->id
        ];

        $messages = [
            'required' => 'Field harus di isi alias tidak boleh kosong',
            'unique' => 'Email sudah ada dalam database iLibrary',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('settings/profile/edit')
                            ->withErrors($validator)
                            ->withInput();
        }
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->save();

        Session::flash("flash_notif", [
        	"level" => "success",
        	"message" => "Profile berhasil diubah"
        ]);

        return redirect('settings/profile');
    }

    public function editPass() {
    	return view('settings.editpass');
    }

    public function updatePass(Request $request) {
    	$user = Auth::user();
        $rules = [
            'password' => 'required|passcheck:'. $user->password,
            'new_password' => 'required|confirmed|min:6',
        ];

        $messages = [
            'required' => 'Field harus di isi alias tidak boleh kosong',
            'password.passcheck' => 'Password lama tidak sesuai',
            'confirmed' => 'Konfirmasi password tidak sesuai',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('settings/password')
                            ->withErrors($validator)
                            ->withInput();
        }

        $user->password = bcrypt($request->get('new_password'));
        $user->save();

        Session::flash("flash_notif", [
        	"level" => "success",
        	"message" => "Password berhasil diubah"
        ]);

        return redirect('settings/password');
    }

}
