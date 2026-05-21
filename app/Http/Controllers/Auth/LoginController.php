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
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers, ThrottlesLogins;

    /**
     * Maximum number of authentication attempts allowed.
     *
     * @var int
     */
    protected $maxAttempts = 5;

    /**
     * Number of minutes to lock out after max attempts.
     *
     * @var int
     */
    protected $decayMinutes = 15;

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
     */
  public function login(Request $request)
{
    $this->validateLogin($request);

    // ===== ADD THIS DEBUG CODE =====
    \Log::info('=== LOGIN DEBUG ===');
    \Log::info('Email: ' . $request->email);
    \Log::info('IP: ' . $request->ip());

    $throttleKey = $this->throttleKey($request);
    \Log::info('Throttle Key: ' . $throttleKey);

    $currentAttempts = $this->limiter()->attempts($throttleKey);
    \Log::info('Current attempts before check: ' . $currentAttempts);
    // ===== END DEBUG CODE =====

    // If the class is using the ThrottlesLogins trait, we can automatically throttle
    // the login attempts for this application.
    if ($this->hasTooManyLoginAttempts($request)) {
        \Log::info('Too many attempts - sending lockout response');
        $this->fireLockoutEvent($request);
        return $this->sendLockoutResponse($request);
    }

    if ($this->attemptLogin($request)) {
        \Log::info('Login successful - clearing attempts');
        return $this->sendLoginResponse($request);
    }

    // If the login attempt was unsuccessful we will increment the number of attempts
    // and redirect the user back to the login form.
    \Log::info('Login failed - incrementing attempts');
    $this->incrementLoginAttempts($request);

    $newAttempts = $this->limiter()->attempts($throttleKey);
    \Log::info('New attempts after increment: ' . $newAttempts);

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
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        $user = Auth::user();

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
        $roleRedirections = [
            'admin' => '/admin/dashboard',
            'finance' => '/finance/dashboard',
            'designer' => '/designer/dashboard',
            'surveyor' => '/surveyor/dashboard',
            'technician' => '/technician/dashboard',
            'account_manager' => '/account-manager/dashboard',
            'system_admin' => '/system-admin/dashboard',
            'accountmanager_admin' => '/accountmanager-admin/dashboard',
            'technical_admin' => '/technical-admin/dashboard',
            'customer' => '/customer/dashboard',
            'ict_engineer' => '/ict-engineer/dashboard',
            'debt_manager' => '/debt-manager/dashboard',
            'compliance_officer' => '/compliance-officer/dashboard',
            'viewer' => '/viewer/dashboard',
        ];

        return redirect()->intended($roleRedirections[$user->role] ?? $this->redirectPath());
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|null  $remainingAttempts
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendFailedLoginResponse(Request $request, $remainingAttempts = null)
    {
        if ($remainingAttempts === null) {
            $throttleKey = $this->throttleKey($request);
            $attempts = $this->limiter()->attempts($throttleKey);
            $remainingAttempts = max(0, $this->maxAttempts() - $attempts);
        }

        if ($remainingAttempts > 0) {
            $message = "These credentials do not match our records. You have {$remainingAttempts} attempt(s) remaining.";
        } else {
            $message = "These credentials do not match our records.";
        }

        throw ValidationException::withMessages([
            $this->username() => [$message],
        ]);
    }

    /**
     * Get the lockout response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $minutes
     * @return \Symfony\Component\HttpFoundation\Response
     */
   protected function sendLockoutResponse(Request $request)
{
    $seconds = $this->limiter()->availableIn($this->throttleKey($request));
    $minutes = ceil($seconds / 60);

    throw ValidationException::withMessages([
        $this->username() => ["Too many login attempts. Please try again in {$minutes} minute(s)."],
    ])->status(429);
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

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
