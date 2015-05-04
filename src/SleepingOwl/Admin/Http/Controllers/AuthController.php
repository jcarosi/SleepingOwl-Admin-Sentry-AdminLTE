<?php namespace SleepingOwl\Admin\Http\Controllers;

use AdminTemplate;
use App;
use Illuminate\Routing\Controller;
use Illuminate\Support\MessageBag;
use Input;
use Redirect;
//use SleepingOwl\AdminAuth\Facades\AdminAuth;
use Validator;

class AuthController extends Controller
{

	protected function redirect()
	{
		return Redirect::route('admin.wildcard', '/');
	}

	public function getLogin()
	{
		if ( ! \Sentry::check() )
		{
			return $this->redirect();
		}
		$loginPostUrl = route('admin.login.post');
		return view(AdminTemplate::view('pages.login'), [
			'title' => config('admin.title'),
			'loginPostUrl' => $loginPostUrl,
		]);
	}

	public function postLogin()
	{
		$rules = config('admin.auth.rules');
		$data = Input::only(array_keys($rules));

		try
		{
		    // Authenticate the user
		    $user = Sentry::authenticate($data, false);

		}
		catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
		{
		    //echo 'Login field is required.';
		    //return Redirect::back()->withInput()->withErrors("User is banned.");
		}
		catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
		{
		    //echo 'Password field is required.';
		    //return Redirect::back()->withInput()->withErrors("User is banned.");
		}
		catch (Cartalyst\Sentry\Users\WrongPasswordException $e)
		{
		    //echo 'Wrong password, try again.';
		    $message = new MessageBag([
				'password' 	=> trans('admin::lang.auth.wrong-password')
			]);
		    return Redirect::back()->withInput()->withErrors($message);
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		    //echo 'User was not found.';
		    $message = new MessageBag([
				'email' 	=> trans('admin::lang.auth.wrong-email'),
			]);
		    return Redirect::back()->withInput()->withErrors($message);
		}
		catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
		{
		    //echo 'User is not activated.';
		    return Redirect::back()->withInput()->withErrors("User is not activated.");
		}

		// The following is only required if the throttling is enabled
		catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
		{
		    //echo 'User is suspended.';
		    return Redirect::back()->withInput()->withErrors("User is suspended.");
		}
		catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
		{
		    //echo 'User is banned.';
		    return Redirect::back()->withInput()->withErrors("User is banned.");
		}

		$validator = Validator::make($data, $rules, trans('admin::validation'));
		if ($validator->fails())
		{
			return Redirect::back()->withInput()->withErrors($validator);
		}

		/*if (AdminAuth::attempt($data))
		{
			return Redirect::intended(route('admin.wildcard', '/'));
		}*/

		$message = new MessageBag([
			'email' 	=> trans('admin::lang.auth.wrong-email'),
			'password' 	=> trans('admin::lang.auth.wrong-password')
		]);
		return Redirect::back()->withInput()->withErrors($message);
	}

	public function getLogout()
	{
		\Sentry::logout();
		return $this->redirect();
	}

}