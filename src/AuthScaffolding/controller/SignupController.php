<?php
namespace App\Controllers\Auth;

Use App\Controllers\Controller;
Use function Simple\view;
Use Simple\Request as r;
Use App\Models\User;
Use Simple\Session;
Use App\Helper\Validation\Validator as validate;
Use App\Helper\Auth\AuthHelper as auth;

class SignupController extends Controller
{

    /**
     * @return object|void
     */
    public function signup()
    {
        return view('auth.signup');
    }

    /**
     * @return object|void
     * @throws \Exception
     */
    public function signupNew()
    {
        r::filterRequest('POST');
        $v = new validate;
        $v->validation_rules(array(
            'name' => 'required|valid_name|min_len,6',
            'email' => 'required|valid_email|unique,users',
            'password' => 'required|min_len,6|alpha_numeric'
        ));
        $validated = $v->run(r::input());
        if($validated) {
            $user = new User();
            $user->save(r::input());
            if(auth::attempt(r::input())) {
                r::redirect('/');
            } else {
                r::redirect('/auth/index');
            }
        } else {
            Session::flush($v->get_errors_array());            
            return view('auth.signup');
        }
        
    }

    /*
     * @return view
     */
    public function success()
    {
        return view('signup.index');
    }

}