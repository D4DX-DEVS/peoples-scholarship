<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Person;
use Validator,Auth,Redirect,Input;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Requests\Auth\AuthRequest;
use DB;


class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesUsers, RegistersUsers {
        AuthenticatesUsers::guard insteadof RegistersUsers;
        AuthenticatesUsers::redirectPath insteadof RegistersUsers;
    }

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
   
        $this->middleware('guest', ['except' => ['getLogout', 'redirect']]);
    }

    /**
     * gets the view for login page.
     *
     * @param  none
     * @return login view or authenticated dashboard view
     */
    protected function getLogin()
    {
      
        return view('auth.login');
    }

    /**
     * login user to dashboard.
     *
     *edited in march2017 for office admininistration
     *
     * @param  none
     * @return login view or authenticated dashboard view
     */
    protected function postLogin()
    {
        $validate = Validator::make(Input::all(), array(        
            'user_id' => 'required',
            'password' => 'required' 
        ));

        if ($validate->fails())
        {
            return Redirect::route('getLogin')->withErrors($validate)->withInput();
        }
        else
        {
            $remember = (Input::has('remember')) ? true : false;
            if(is_numeric(Input::get('user_id')))
            {
              if( Input::get('user_id') > 10000)
              {
                $auth = Auth::attempt(array(
                        'id' => Input::get('user_id'),
                        'password' => Input::get('password')
                    ), $remember);
              }
              else
                $auth = false;
            }
            else
            { 
              $auth = Auth::attempt(array(
                    'username' => Input::get('user_id'),
                    'password' => Input::get('password')
                ), $remember);
            }

            if($auth)
            {
                $user = Auth::user();
                return redirect::route('');

            }
  
            else{
                //login fails
                return Redirect::route('getLogin')->with('fail', 'You are enterd wrong credentials, please try again.');
            }
        }
    }

    /**
     * gets the view for register page.
     *
     * @param  none
     * @return a view
     */
    protected function getRegister()
    {
      return view('auth.register');
    }

    /**
     * create a new user.
     *
     * @param  none
     * @return a message
     */
    // protected function postRegister(AuthRequest $request)
    // {    
    //     // Set User Name
    //     $name = ucwords(strtolower($request->name));
    //     // Give User to random Password
    //     $password = $this->randomPassword();
    //     $user = User::create(array(
    //              'username' => $name,
    //              'password' => bcrypt($password),
    //         'password_view' => $password,
    //     ));


    //     if($user->save())
    //     {

    //             return Redirect::route('home')->with('success', 'You are Registered success fully. You can now log in');
    //     }
    //     else
    //     {
    //       return Redirect::route('home')->with('fail', 'An error occurred while creating the user. Please try again');
    //     }
    //   }
         /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    protected function getLogout()
    {
        $logout = Auth::logout();
        return Redirect::route('home');
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }


    

   
}
