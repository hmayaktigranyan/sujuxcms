<?php

class UserController extends BaseController {

    /**
     * User Model
     * @var User
     */
    protected $user;

    /**
     * Inject the models.
     * @param User $user
     */
    public function __construct(User $user) {

        $this->user = $user;
    }

    /**
     * Users settings page
     *
     * @return View
     */
    /* public function getIndex() {
      list($user, $redirect) = $this->user->checkAuthAndRedirect('user');
      if ($redirect) {
      return $redirect;
      }

      // Show the page
      return View::make('user/index', compact('user'));
      } */

    /**
     * Stores new user
     *
     */
    /* public function postIndex() {
      $this->user->username = Input::get('username');
      $this->user->email = Input::get('email');

      $password = Input::get('password');
      $passwordConfirmation = Input::get('password_confirmation');

      if (!empty($password)) {
      if ($password === $passwordConfirmation) {
      $this->user->password = $password;
      // The password confirmation will be removed from model
      // before saving. This field will be used in Ardent's
      // auto validation.
      $this->user->password_confirmation = $passwordConfirmation;
      } else {
      // Redirect to the new user page
      return Redirect::to('user/create')
      ->withInput(Input::except('password', 'password_confirmation'))
      ->with('error', Lang::get('admin/users/messages.password_does_not_match'));
      }
      } else {
      unset($this->user->password);
      unset($this->user->password_confirmation);
      }

      // Save if valid. Password field will be hashed before save
      $this->user->save();

      if ($this->user->id) {
      // Redirect with success message, You may replace "Lang::get(..." for your custom message.
      return Redirect::to('user/login')
      ->with('notice', Lang::get('user/user.user_account_created'));
      } else {
      // Get validation errors (see Ardent package)
      $error = $this->user->errors()->all();

      return Redirect::to('user/create')
      ->withInput(Input::except('password'))
      ->with('error', $error);
      }
      } */

    /**
     * Edits a user
     *
     */
    /*public function postEdit($user) {
        // Validate the inputs
        $validator = Validator::make(Input::all(), $user->getUpdateRules());


        if ($validator->passes()) {
            $oldUser = clone $user;
            $user->username = Input::get('username');
            $user->email = Input::get('email');

            $password = Input::get('password');
            $passwordConfirmation = Input::get('password_confirmation');

            if (!empty($password)) {
                if ($password === $passwordConfirmation) {
                    $user->password = $password;
                    // The password confirmation will be removed from model
                    // before saving. This field will be used in Ardent's
                    // auto validation.
                    $user->password_confirmation = $passwordConfirmation;
                } else {
                    // Redirect to the new user page
                    return Redirect::to('users')->with('error', Lang::get('admin/users/messages.password_does_not_match'));
                }
            } else {
                unset($user->password);
                unset($user->password_confirmation);
            }

            $user->prepareRules($oldUser, $user);

            // Save if valid. Password field will be hashed before save
            $user->amend();
        }

        // Get validation errors (see Ardent package)
        $error = $user->errors()->all();

        if (empty($error)) {
            return Redirect::to('user')
                            ->with('success', Lang::get('user/user.user_account_updated'));
        } else {
            return Redirect::to('user')
                            ->withInput(Input::except('password', 'password_confirmation'))
                            ->with('error', $error);
        }
    }

    /**
     * Displays the form for user creation
     *
     */
    /* public function getCreate() {
      return View::make('user/create');
      } */

    /**
     * Displays the login form
     *
     */
    public function getLogin() {
        if (!Sentry::check()) {
            return View::make('user/login');
        }
        return Redirect::to('/');
    }

    /**
     * Attempt to do login
     *
     */
    public function postLogin() {

        try {
            // Login credentials
            $credentials = array(
                'username' => Input::get('username'),
                'password' => Input::get('password'),
            );
            $user = Sentry::authenticate($credentials, (boolean) Input::get('remember'));
            return Redirect::to('admin');
        } catch (Cartalyst\Sentry\Users\LoginRequiredException $e) {
            $err_msg = 'Login field is required.';
        } catch (Cartalyst\Sentry\Users\PasswordRequiredException $e) {
            $err_msg = 'Password field is required.';
        } catch (Cartalyst\Sentry\Users\WrongPasswordException $e) {
            $err_msg = 'Wrong password, try again.';
        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            $err_msg = 'User was not found.';
        } catch (Cartalyst\Sentry\Users\UserNotActivatedException $e) {
            $err_msg = 'User is not activated.';
        }

// The following is only required if the throttling is enabled
        catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
            $err_msg = 'User is suspended.';
        } catch (Cartalyst\Sentry\Throttling\UserBannedException $e) {
            $err_msg = 'User is banned.';
        }

        return Redirect::to('user/login')
                        ->withInput(Input::except('password'))
                        ->with('error', $err_msg);
    }

    /**
     * Attempt to confirm account with code
     *
     * @param  string  $code
     */
    /* public function getConfirm($code) {
      if (Confide::confirm($code)) {
      return Redirect::to('user/login')
      ->with('notice', Lang::get('confide::confide.alerts.confirmation'));
      } else {
      return Redirect::to('user/login')
      ->with('error', Lang::get('confide::confide.alerts.wrong_confirmation'));
      }
      } */

    /**
     * Displays the forgot password form
     *
     */
    /*  public function getForgot() {
      return View::make('user/forgot');
      } */

    /**
     * Attempt to reset password with given email
     *
     */
    /*  public function postForgot() {
      if (Confide::forgotPassword(Input::get('email'))) {
      return Redirect::to('user/login')
      ->with('notice', Lang::get('confide::confide.alerts.password_forgot'));
      } else {
      return Redirect::to('user/forgot')
      ->withInput()
      ->with('error', Lang::get('confide::confide.alerts.wrong_password_forgot'));
      }
      } */

    /**
     * Shows the change password form with the given token
     *
     */
    /* public function getReset($token) {

      return View::make('user/reset')
      ->with('token', $token);
      }
     */
    /**
     * Attempt change password of the user
     *
     */
    /* public function postReset() {
      $input = array(
      'token' => Input::get('token'),
      'password' => Input::get('password'),
      'password_confirmation' => Input::get('password_confirmation'),
      );

      // By passing an array with the token, password and confirmation
      if (Confide::resetPassword($input)) {
      return Redirect::to('user/login')
      ->with('notice', Lang::get('confide::confide.alerts.password_reset'));
      } else {
      return Redirect::to('user/reset/' . $input['token'])
      ->withInput()
      ->with('error', Lang::get('confide::confide.alerts.wrong_password_reset'));
      }
      } */

    /**
     * Log the user out of the application.
     *
     */
    public function getLogout() {
        Sentry::logout();
        return Redirect::to('/');
    }

    /**
     * Get user's profile
     * @param $username
     * @return mixed
     */
    public function getProfile($username) {
        $userModel = new User;
        $user = $userModel->getUserByUsername($username);

        // Check if the user exists
        if (is_null($user)) {
            return App::abort(404);
        }

        return View::make('user/profile', compact('user'));
    }

    public function getSettings() {
        if (!Sentry::check()) {
            return Redirect::guest('user/login');
        }
        $user = Sentry::getUser();
        return View::make('user/profile', compact('user'));
    }

}
