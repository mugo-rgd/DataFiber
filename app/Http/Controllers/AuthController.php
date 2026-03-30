<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
 ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

         $request->session()->forget('url.intended');
        $user = Auth::user();

        // Redirect based on user role using URL paths
        switch ($user->role) {
            case 'system_admin':
            case 'admin':
            case 'accountmanager_admin':
            case 'technical_admin':
                return redirect('/admin/dashboard');

            case 'debt_manager':
           return redirect('/finance/debt/dashboard');

            case 'finance':
                return redirect('/finance/dashboard');

            case 'technician':
                return redirect('/technician/dashboard');

            case 'customer':
                return redirect('/customer/customer-dashboard');

            case 'designer':
                return redirect('/designer/dashboard');

                case 'ict_engineer':
                return redirect('/ictengineer/dashboard');

            case 'surveyor':
                return redirect('/surveyor/dashboard');

            case 'account_manager':
                return redirect('/account-manager/dashboard');

            default:
                return redirect('/home');
        }
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
}
public function showLoginForm()
{
    return view('auth.login');
}
//    public function registerCustomer(Request $request)
// {
//     Log::info('Registration data:', $request->all());

//     try {
//         $user = User::create([
//             'name' => $request->company_name,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//             'role' => 'customer',
//         ]);

//         Log::info('User created:', ['id' => $user->id]);
//         return response()->json(['success' => true, 'user_id' => $user->id]);

//     } catch (\Exception $e) {
//         Log::error('Error: ' . $e->getMessage());
//         return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
//     }
// }

    //  public function registerCustomer(Request $request)
    // {
    //     // Debug: Check if request is reaching the controller
    //     Log::info('Registration attempt', $request->all());

    //     $validator = Validator::make($request->all(), [
    //         'company_name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users',
    //         'phone' => 'required|string|max:20',
    //         'password' => 'required|min:8|confirmed',
    //     ]);

    //     if ($validator->fails()) {
    //         return back()->withErrors($validator)->withInput();
    //     }

    //     try {
    //         $user = User::create([
    //             'name' => $request->company_name, // Using company_name as name
    //             'email' => $request->email,
    //             'phone' => $request->phone,
    //             'password' => Hash::make($request->password),
    //             'role' => 'customer', // Make sure you have this field
    //             'company_name' => $request->company_name,
    //         ]);

    //         // Log success
    //         Log::info('User created successfully', ['user_id' => $user->id]);

    //         // Redirect to login with success message
    //         return redirect()->route('customer.dashboard')->with('success', 'Account created successfully!');

    //     } catch (\Exception $e) {
    //         Log::error('Registration error: ' . $e->getMessage());
    //         return back()->with('error', 'Registration failed. Please try again.')->withInput();
    //     }
    // }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
$request->session()->flush();
$request->session()->regenerate();
        return redirect('/');
    }

       ///
    //  public function registerCustomer(Request $request)
    // {
    //     // Step 1: Basic validation for customer registration
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users',
    //         'phone' => 'required|string|max:20',
    //         'company_name' => 'nullable|string|max:255',
    //         'password' => 'required|min:8|confirmed',
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         // Step 2: Create customer user with basic info
    //         $user = User::create([
    //             'name' => $validated['name'],
    //             'email' => $validated['email'],
    //             'phone' => $validated['phone'],
    //             'company_name' => $validated['company_name'] ?? null,
    //             'password' => Hash::make($validated['password']),
    //             'role' => 'customer', // Auto-assign customer role
    //             'status' => 'active', // Auto-activate
    //             'email_verified_at' => now(), // Auto-verify for immediate access
    //         ]);

    //         // Step 3: Automatically log the customer in
    //         Auth::login($user);

    //         DB::commit();

    //         // Step 4: Redirect to complete profile
    //         return redirect()->route('customer.complete-profile')
    //             ->with('success', 'Account created successfully! Please complete your profile setup.');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', 'Error creating account: ' . $e->getMessage());
    //     }
    // }

    public function completeCustomerProfile(Request $request)
    {
        // Ensure customer is logged in
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please log in to complete your profile.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Verify the user is a customer
        if ($user->role !== 'customer') {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Invalid user role.');
        }

        // Get active document types for dynamic validation
        $documentTypes = DocumentType::where('is_active', true)->get();

        $validated = $request->validate([
            // Additional customer-specific fields
            'lease_start_date' => 'nullable|date',
            'billing_frequency' => 'nullable|in:monthly,quarterly,annually',
            'monthly_rate' => 'nullable|numeric|min:0',
            'auto_billing_enabled' => 'boolean',

            // Document validation
            'documents' => 'nullable|array',
            'documents.*.document_type' => 'required|string|max:255|exists:document_types,document_type',
            'documents.*.file' => 'required|file',
            'documents.*.description' => 'nullable|string|max:500',
            'documents.*.expiry_date' => 'nullable|date|after:today',
        ]);

        // Additional dynamic validation for document files
        if ($request->has('documents')) {
            foreach ($request->documents as $index => $document) {
                if (isset($document['document_type'])) {
                    $docType = $documentTypes->firstWhere('document_type', $document['document_type']);

                    if ($docType) {
                        $rules = ['required', 'file'];

                        if ($docType->max_file_size) {
                            $rules[] = "max:{$docType->max_file_size}";
                        }

                        $allowedExtensions = $docType->allowed_extensions;
                        if (!empty($allowedExtensions) && is_array($allowedExtensions)) {
                            $mimes = implode(',', $allowedExtensions);
                            $rules[] = "mimes:{$mimes}";
                        }

                        $request->validate([
                            "documents.{$index}.file" => $rules
                        ], [
                            "documents.{$index}.file.max" => "The {$docType->name} must not be larger than " . ($docType->max_file_size / 1024) . "MB.",
                            "documents.{$index}.file.mimes" => "The {$docType->name} must be one of the following types: " . (is_array($allowedExtensions) ? implode(', ', $allowedExtensions) : '')
                        ]);
                    }
                }
            }
        }

        try {
            DB::beginTransaction();

            // Update customer with complete profile data - FIXED update method
            $updateData = [
                'lease_start_date' => $validated['lease_start_date'] ?? null,
                'billing_frequency' => $validated['billing_frequency'] ?? 'monthly',
                'monthly_rate' => $validated['monthly_rate'] ?? 0.00,
                'auto_billing_enabled' => $validated['auto_billing_enabled'] ?? true,
                'profile_completed_at' => now(), // Track profile completion
            ];

            // Method 1: Using update() - this should work
            $user->update($updateData);

            // Alternative Method 2: If update() still shows error, use this:
            // User::where('id', $user->id)->update($updateData);

            // Handle document uploads
            if ($request->has('documents')) {
                $this->saveDocuments($request, $user);
            }

            DB::commit();

            return redirect()->route('customer.customer-dashboard')
                ->with('success', 'Profile completed successfully! Welcome to your dashboard.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error completing profile: ' . $e->getMessage());
        }
    }

    public function showCustomerRegistrationForm()
    {
        return view('auth.register');
    }

    public function showCompleteCustomerProfileForm()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->role !== 'customer') {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Invalid user role.');
        }

        $documentTypes = DocumentType::where('is_active', true)->get();

        return view('customer.complete-profile', compact('documentTypes', 'user'));
    }

    /**
     * Handle document uploads
     */
    private function saveDocuments(Request $request, User $user)
{
    $documents = $request->documents;
    $uploadedBy = Auth::id();

    foreach ($documents as $documentData) {
        // Skip if no file uploaded or file is invalid
        if (empty($documentData['file']) || !$documentData['file']->isValid()) {
            continue;
        }

        $file = $documentData['file'];
        $documentTypeValue = $documentData['document_type'];
        $description = $documentData['description'] ?? null;
        $expiryDate = isset($documentData['expiry_date']) ? Carbon::parse($documentData['expiry_date']) : null;

        // Get document type details
        $docType = DocumentType::where('document_type', $documentTypeValue)->first();

        if (!$docType) {
            continue; // Skip if document type not found
        }

        // Generate unique file name
        $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

        // Store file
        $filePath = $file->storeAs('documents', $fileName, 'public');

        // Create document record
        Document::create([
            'user_id' => $user->id,
            'name' => $file->getClientOriginalName(),
            'slug' => Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
            'document_type' => $documentTypeValue,
            'file_path' => $filePath,
            'disk' => 'public',
            'file_name' => $fileName,
            'uploaded_by' => $uploadedBy,
            'status' => 'pending_review',
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'expiry_date' => $expiryDate,
            'has_expiry' => !is_null($expiryDate),
            'is_required' => $docType->is_required,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

public function registerCustomer(Request $request)
{
    Log::info('=== REGISTER CUSTOMER START ===');
    Log::info('Registration request data:', $request->all());

    try {
        // TEMPORARY FIX: Make name optional and use company_name as fallback
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255', // Changed to nullable
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20',
            'company_name' => 'required|string|max:255', // Make this required
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        Log::info('Validation passed', $validated);

        DB::beginTransaction();

        // Use company_name as name if name is not provided
        $name = $validated['name'] ?? $validated['company_name'];

        $userData = [
            'name' => $name,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'company_name' => $validated['company_name'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'status' => 'active',
            'email_verified_at' => now(),
        ];

        Log::info('User data to create:', $userData);

        $user = User::create($userData);
        Log::info('User created successfully', ['user_id' => $user->id]);

        Auth::login($user);
        DB::commit();

        return redirect()->route('customer.complete-profile')
            ->with('success', 'Account created successfully! Please complete your profile setup.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Registration failed:', ['error' => $e->getMessage()]);
        return redirect()->back()->withInput()->with('error', 'Error creating account: ' . $e->getMessage());
    }
}
}
