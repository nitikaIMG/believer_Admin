<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Request;

class YoutuberLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/youtuber';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:youtuber')->except('logout');
    }
    
    public function showLoginForm()
    {
      return view('auth.login_youtuber');
    }
    
    public function login(Request $request)
    {
      $this->validate(request(), [
        'email'   => 'required|email',
        'password' => 'required|min:6'
      ]);
      session()->forget('url.intended');
      if (Auth::guard('youtuber')->attempt(['email' => request()->email, 'password' => request()->password], request()->remember)) {
        return redirect()->intended(route('youtuber'));
      } 
      // if unsuccessful, then redirect back to the login with the form data
      return redirect()->back()->withInput(request()->only('email', 'remember'))->with('error', 'Invalid');
    }
    
    public function logout()
    {
        Auth::guard('youtuber')->logout();
        return redirect('/youtuber');
    }
}
