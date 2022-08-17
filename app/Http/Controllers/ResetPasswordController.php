<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use Redirect;
use Session;

class ResetPasswordController extends Controller
{
    public function index()
    {
        return view('admin/resetpassword/index');
    }

    public function store(Request $request)
    {
        $email = $request->get('email');
        $cekExist = \App\ResetPassword::where('email', $email)
            ->select('id', 'email')
            ->get();

        if (empty($cekExist[0])) {
            return Redirect::to('admin/resetpassword')
                ->withErrors('Email not Found!');
        } else {
            return $this->update($request);
        }
    }

    public function update(Request $request)
    {
        //
        $rules = [
            'email' => 'required',
            'newpassword' => 'required'
        ];

        $msg = [
            'email.required' => 'email must not empty',
            'newpassword.required' => 'new password must not empty'
        ];

        $validator = Validator::make($request->all(), $rules, $msg);

        if ($validator->fails()) {
            return Redirect::to('admin/resetpassword')
                ->withErrors($validator);
        } else {
            $updateQuery = \App\ResetPassword::where('email', $request->get('email'))
                ->update(['password' => Hash::make($request->get('newpassword'))]);

            if ($updateQuery) {
                Session::flash('message', 'Reset Password Success! Email: ' . $request->get('email') . ' Password: ' . $request->get('newpassword'));

                return Redirect::to('admin/resetpassword');
            } else {
                return Redirect::to('admin/resetpassword')
                    ->withErrors('Reset Password Failed!');
            }
        }
    }
}

