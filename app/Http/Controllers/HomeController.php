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
        $user = User::find(1)->toArray();
        Mail::send('auth.emails.password', $user, function($message) use ($user) {
            $message->to('aalviian@gmail.com');
            $message->subject('Mailgun Testing');
        });
        dd('Mail Send Successfully');
    }

}
