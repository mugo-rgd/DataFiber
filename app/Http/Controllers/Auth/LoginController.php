<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;

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
     * Maximum number of authentication attempts allowed.
     *
     * @var int
     */
    protected $maxAttempts = 5;

    /**
     * Number of minutes to throttle for.
     *
     * @var int
     */
    protected $decayMinutes = 1;

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

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // Validate the login request
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // Attempt to authenticate the user
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Check if user is a customer and has documents
        if ($user->role === 'customer') {
            $requiredDocTypes = DocumentType::where('is_required', true)
                                          ->where('is_active', true)
                                          ->pluck('document_type')
                                          ->toArray();

            if (!empty($requiredDocTypes)) {
                $uploadedRequiredDocs = Document::where('user_id', $user->id)
                                              ->whereIn('document_type', $requiredDocTypes)
                                              ->where('status', 'approved')
                                              ->count();

                if ($uploadedRequiredDocs < count($requiredDocTypes)) {
                    return redirect()->route('customer.documents.upload')
                                  ->with('warning', 'Please upload all required documents to access the dashboard.');
                }
            }
        }

        // Redirect based on role
        if ($user->role === 'admin' || $user->role === 'technical_admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'customer') {
            return redirect()->route('customer.customer-dashboard');
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
  public function logout(Request $request)
{
    // Debug: Log current session state
    \Log::info('Before logout', [
        'user_id' => Auth::id(),
        'session_id' => session()->getId(),
        'is_authenticated' => Auth::check(),
    ]);

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    \Log::info('After logout', [
        'user_id' => Auth::id(),
        'is_authenticated' => Auth::check(),
    ]);

    return redirect('/login');
}

    /**
     * Get the throttle key for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input($this->username())) . '|' . $request->ip();
    }
}
