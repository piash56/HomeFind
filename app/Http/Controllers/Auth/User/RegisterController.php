<?php

namespace App\Http\Controllers\Auth\User;


use App\{
    Http\Requests\UserRequest,
    Http\Controllers\Controller,
    Repositories\Front\UserRepository
};
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{

    /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     * @param  \App\Repositories\Back\UserRepository $repository
     *
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }


    public function showForm()
    {
      return view('user.auth.register');
    }


    public function register(UserRequest $request)
    {   
        $request->validate([
            'email' => 'required|email|unique:users,email'
        ]);
        
        $this->repository->register($request);

        $setting = Setting::first();
        if($setting->is_mail_verify == 0){
            Session::flash('success',__('Account Register Successfully please login'));
            return redirect()->route('user.login');
        }else{
            Session::flash('success',__('Account Register Successfully please check your email for verification'));
            return redirect()->route('user.verify');
        }
        
        
    }
    


    public function verify($token)
    {
        $user = User::where('email_token',$token)->first();
       
        if($user){
            
            Auth::login($user);
            
            return redirect(route('user.dashboard'));
        }else{
            return redirect(route('user.login'));
        }
    }



}
