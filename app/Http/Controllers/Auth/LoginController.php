<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request){
        $input = $request->all();
        $Id_session = session()->getID();
        $this->validate($request,[
        'email'=>'required|email',
        'password' => 'required',]);
        if(auth()->attempt(array('email' =>$input['email'],'password'=>$input['password']))){
            if(auth()->user()->is_admin == 1){
                return redirect()->route('admin.home');
            }else{
                $order = Order::where('session_id', $Id_session)->latest('date')->first();

                if ($order) {
                    $order->customer_id = auth()->user()->id;
                    $order->save();
                }
                var_dump("test");
                return redirect()->route('home');
            }
        }else{
            return redirect()->route('login')->with('error','password of username is incorect');
        }

    }

    public function showLoginForm(){
        return view('auth.login',[
            'totalOrder'=>Order::totalOrder(),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

}
