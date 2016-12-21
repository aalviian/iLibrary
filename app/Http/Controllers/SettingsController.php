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

    public function edit() {
    	return view('settings.edit');
    }

    public function update(Request $request){
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
}
