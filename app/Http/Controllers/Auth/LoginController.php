<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
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
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        /*
         * Remove the socialite session variable if exists
         */

        \Session::forget(config('access.socialite_session_name'));

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect('/admin');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        try
        {
            $errors = [$this->username() => 'Email atau password anda salah!'];

            if ($request->expectsJson()) {
                return response()->json($errors, 422);
            }

            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors($errors);
        }
        catch (\Exception $ex){
            Log::error('LoginController - sendFailedLoginResponse : '. $ex);
        }
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        try{

            $errors = [];

//            if (config('auth.users.confirm_email') && !$user->confirmed) {
//                $errors = [$this->username() => __('auth.notconfirmed', ['url' => route('confirm.send', [$user->email])])];
//            }

            if (!$user->active) {
                $errors = [$this->username() => __('auth.active')];
            }

            if ($user->status_id == 2){
                $errors = [$this->username() => 'Email atau password anda salah!'];
            }

            if ($errors) {
                auth()->logout();  //logout

                return redirect()->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors($errors);
            }

            if($request->input('redirect') !== 'default'){
                $url = $request->input('redirect');
                return redirect($url);
            }

            return redirect('/admin');
        }
        catch(\Exception $ex){
            Log::error('LoginController - authenticated : '. $ex);
        }
    }

    public function showLoginForm(){

        $redirect = "default";
        if(!empty(\request()->redirect)){
            $redirect = \request()->redirect;
        }

        return view('auth.login', compact('redirect'));
    }
}
