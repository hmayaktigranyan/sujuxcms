<?php

class AdminController extends BaseController {

    public function profile() {
        if (!Sentry::check()) {
            return Redirect::guest('user/login');
        }
        $user = Sentry::getUser();
        return View::make('admin.user.profile', compact('user'));
    }

    public function profileUpdate() {
        if (!Sentry::check()) {
            return Redirect::guest('user/login');
        }
        $user = Sentry::getUser();
        $rules = array(
            'currentpassword' => 'required',
            'newpassword' => 'required|confirmed',
            'newpassword_confirmation' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            Notification::error('Please check the form below for errors');

            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            if (!$user->checkPassword(Input::get('currentpassword'))) {
                Notification::error('Old password is not correct');
                return Redirect::back()->withInput();
            }
            $user->password = Input::get('newpassword');
            $user->save();
            Notification::success('Successfully updated !');
            return Redirect::to('admin/profile');
        }
    }

}
