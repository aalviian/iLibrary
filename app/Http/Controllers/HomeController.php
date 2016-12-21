<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Entrust;
use Auth;
use App\User;
use Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Entrust::hasRole('admin')) return $this->adminDashboard();
        if(Entrust::hasRole('member')) return $this->memberDashboard();
        return redirect('/');
    }

    protected function adminDashboard() {
        return view('dashboard.admin');
    }

    protected function memberDashboard() {
        $borrowLogs = Auth::user() -> borrowLogs() -> borrowed() -> get();
        return view('dashboard.member', compact('borrowLogs'));
    }

    public function mail()
    {
        $user = User::find(2);
        Mail::send('confirm.verification', ['user' => $user], function ($m) use ($user) {
            $m->from('admin@ilibrary.com', 'iLibrary Admin');

            $m->to($user->email, $user->name)->subject('Hello from iLibrary!');
        });
    }

}
