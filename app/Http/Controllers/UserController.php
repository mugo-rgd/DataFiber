<?php

namespace App\Http\Controllers;

use App\Models\County;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
// In your AdminController or existing controller
public function usersIndex()
{
    $users = User::orderBy('created_at', 'desc')->paginate(15);

    return view('users.index', compact('users'));
}
        /**
     * Remove a role (set back to default).
     */
    public function removeRole($id, $role)
    {
        $user = User::findOrFail($id);

        if ($user->role === $role) {
            $user->role = 'customer'; // default fallback role
            $user->save();
        }

        return redirect()->back()->with('success', "Role '{$role}' removed from {$user->name}");
    }

public function index()
{
    $users = User::paginate(10);
    $documentTypes = DocumentType::where('is_active', true)
                               ->orderBy('sort_order')
                               ->get();
    $accountManagers = User::where('role', 'account_manager')->get();
    $counties = County::orderBy('name')->get();

    // Debug: Check what's happening
    \Log::info('Loading users index', [
        'route' => request()->route()->getName(),
        'path' => request()->path(),
        'view_exists' => [
            'admin.users.index' => view()->exists('admin.users.index'),
            'users.index' => view()->exists('users.index'),
        ]
    ]);

    return view('users.index', compact('users', 'documentTypes', 'accountManagers', 'counties'));
}
    /**
     * Show the form for creating a new user.
     */
    // public function create()
    // {
    //     $accountManagers = User::where('role', 'account_manager')->get();

    //     return view('users.create', compact('accountManagers'));
    // }
// App\Http\Controllers\Admin\UserController.php or similar

public function create()
{
    $counties = County::orderBy('name')->get();
    $accountManagers = User::where('role', 'account_manager')->get();

    // If you're using Spatie roles
    $roles = \Spatie\Permission\Models\Role::all();
    // Or if you're using enum roles
    $roles = collect([
        'admin', 'customer', 'finance', 'designer', 'surveyor',
        'technician', 'ict_engineer', 'account_manager', 'system_admin',
        'accountmanager_admin', 'technical_admin', 'finance_admin',
        'guest', 'county_ict_engineer', 'regional_manager', 'debt_manager'
    ]);

    $roleDisplayNames = [
        'admin' => 'Administrator',
        'customer' => 'Customer',
        'finance' => 'Finance Manager',
        'designer' => 'Network Designer',
        'surveyor' => 'Field Surveyor',
        'technician' => 'Field Technician',
        'ict_engineer' => 'ICT Engineer',
        'account_manager' => 'Account Manager',
        'system_admin' => 'System Administrator',
        'accountmanager_admin' => 'Account Manager Admin',
        'technical_admin' => 'Technical Administrator',
        'finance_admin' => 'Finance Administrator',
        'guest' => 'Guest',
        'county_ict_engineer' => 'County ICT Engineer',
        'regional_manager' => 'Regional Manager',
        'debt_manager' => 'Debt Manager',
    ];

    return view('users.create', compact(
        'counties',
        'accountManagers',
        'roles',
        'roleDisplayNames'
    ));
}
    /**
     * Store a newly created user in storage.
     */
public function store(Request $request)
{
    // Get active document types for dynamic validation
    $documentTypes = DocumentType::where('is_active', true)->get();

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'company_name' => 'nullable|string|max:255',
        'email' => 'required|email|unique:users',
        'phone' => 'nullable|string|max:20',
        'password' => 'required|min:8|confirmed',
        'role' => 'required|in:debt_manager,customer,admin,finance,designer,surveyor,technician,ict_engineer,account_manager,system_admin,accountmanager_admin,technical_admin,finance_admin,guest,county_ict_engineer,regional_manager',
        'status' => 'required|in:active,inactive,suspended',
        'account_manager_id' => 'nullable|exists:users,id',
        'lease_start_date' => 'nullable|date',
        'billing_frequency' => 'nullable|in:monthly,quarterly,annually',
        'monthly_rate' => 'nullable|numeric|min:0',
        'auto_billing_enabled' => 'boolean',

        // New county fields
        'county_id' => 'nullable|exists:counties,id',
        'county_notes' => 'nullable|string|max:500',

        // Address fields
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',

        // Assignment notes
        'assignment_notes' => 'nullable|string|max:1000',

        // Document validation
        'documents' => 'nullable|array',
        'documents.*.document_type' => 'required|string|max:255|exists:document_types,document_type',
        'documents.*.file' => 'required|file',
        'documents.*.description' => 'nullable|string|max:500',
        'documents.*.expiry_date' => 'nullable|date|after:today',
    ]);

    // Validate county_id is required for county_ict_engineer role
    if ($validated['role'] === 'county_ict_engineer' && empty($validated['county_id'])) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['county_id' => 'County is required for County ICT Engineers.']);
    }

    // Additional dynamic validation for document files based on their type
    if ($request->has('documents')) {
        foreach ($request->documents as $index => $document) {
            if (isset($document['document_type'])) {
                $docType = $documentTypes->firstWhere('document_type', $document['document_type']);

                if ($docType) {
                    $rules = ['required', 'file'];

                    // Add file size validation
                    if ($docType->max_file_size) {
                        $rules[] = "max:{$docType->max_file_size}";
                    }

                    // Add mime type validation with proper array handling
                    $allowedExtensions = $docType->allowed_extensions;
                    if (!empty($allowedExtensions) && is_array($allowedExtensions)) {
                        $mimes = implode(',', $allowedExtensions);
                        $rules[] = "mimes:{$mimes}";
                    }

                    // Validate the specific document file
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

        // Create user data array
        $userData = [
            'name' => $validated['name'],
            'company_name' => $validated['company_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
            'lease_start_date' => $validated['lease_start_date'] ?? null,
            'billing_frequency' => $validated['billing_frequency'] ?? 'monthly',
            'monthly_rate' => $validated['monthly_rate'] ?? 0.00,
            'auto_billing_enabled' => $validated['auto_billing_enabled'] ?? true,
            'email_verified_at' => now(),

            // County fields
            'county_id' => $validated['county_id'] ?? null,
            'county_notes' => $validated['county_notes'] ?? null,

            // Address fields
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,

            // Assignment notes
            'assignment_notes' => $validated['assignment_notes'] ?? null,
        ];

        // Set county_assigned_at if county_id is provided
        if ($validated['county_id']) {
            $userData['county_assigned_at'] = now();
        }

        // Only add account_manager_id and assigned_at if they exist
        if (isset($validated['account_manager_id']) && $validated['account_manager_id']) {
            $userData['account_manager_id'] = $validated['account_manager_id'];
            $userData['assigned_at'] = now();
        }

        // If using Spatie permissions, assign the role
        if ($validated['role'] && class_exists(\Spatie\Permission\Models\Role::class)) {
            // Ensure the role exists in Spatie's roles table
            $role = \Spatie\Permission\Models\Role::firstOrCreate(
                ['name' => $validated['role']],
                [
                    'name' => $validated['role'],
                    'guard_name' => 'web',
                ]
            );
        }

        $user = User::create($userData);

        // Assign Spatie role if using permissions package
        if (isset($role) && class_exists(\Spatie\Permission\Models\Role::class)) {
            $user->assignRole($role);
        }

        // Handle document uploads
        if ($request->has('documents')) {
            $this->saveDocuments($request, $user);
        }

        // Send welcome email if needed
        // $user->sendEmailVerificationNotification();

        DB::commit();

        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error creating user: ' . $e->getMessage());
    }
}

/**
 * Save documents for the user with proper JSON handling
 */
// private function saveDocuments(Request $request, User $user)
// {
//     $documents = $request->documents;
//     $uploadedBy = Auth::id();

//     foreach ($documents as $documentData) {
//         // Skip if no file uploaded or file is invalid
//         if (empty($documentData['file']) || !$documentData['file']->isValid()) {
//             continue;
//         }

//         $file = $documentData['file'];
//         $documentTypeValue = $documentData['document_type'];
//         $description = $documentData['description'] ?? null;
//         $expiryDate = isset($documentData['expiry_date']) ? Carbon::parse($documentData['expiry_date']) : null;

//         // Get document type details
//         $docType = DocumentType::where('document_type', $documentTypeValue)->first();

//         if (!$docType) {
//             continue; // Skip if document type not found
//         }

//         // Generate unique file name
//         $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

//         // Store file
//         $filePath = $file->storeAs('documents', $fileName, 'public');

//         // Create document record
//         Document::create([
//             'user_id' => $user->id,
//             'name' => $file->getClientOriginalName(),
//             'slug' => Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
//             'document_type' => $documentTypeValue,
//             'file_path' => $filePath,
//             'disk' => 'public',
//             'file_name' => $fileName,
//             'uploaded_by' => $uploadedBy,
//             'status' => 'pending_review',
//             'mime_type' => $file->getMimeType(),
//             'file_size' => $file->getSize(),
//             'expiry_date' => $expiryDate,
//             'has_expiry' => !is_null($expiryDate),
//             'is_required' => $docType->is_required,
//             'description' => $description,
//             'created_at' => now(),
//             'updated_at' => now(),
//         ]);
//     }
// }

/**
 * Save documents for the user
 */
// private function saveDocuments(Request $request, User $user)
// {
//     $documents = $request->documents;
//     $uploadedBy = Auth::id(); // Current admin user ID

//     foreach ($documents as $documentData) {
//         // Skip if no file uploaded
//         if (empty($documentData['file']) || !$documentData['file']->isValid()) {
//             continue;
//         }

//         $file = $documentData['file'];
//         $documentType = $documentData['document_type'] ?? 'other';
//         $description = $documentData['description'] ?? null;
//         $expiryDate = isset($documentData['expiry_date']) ? Carbon::parse($documentData['expiry_date']) : null;

//         // Generate unique file name
//         $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

//         // Store file
//         $filePath = $file->storeAs('documents', $fileName, 'public');

//         // Create document record
//         Document::create([
//             'user_id' => $user->id,
//             'name' => $file->getClientOriginalName(),
//             'slug' => Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
//             'document_type' => $documentType,
//             'file_path' => $filePath,
//             'disk' => 'public',
//             'file_name' => $fileName,
//             'uploaded_by' => $uploadedBy,
//             'status' => 'pending_review',
//             'mime_type' => $file->getMimeType(),
//             'file_size' => $file->getSize(),
//             'expiry_date' => $expiryDate,
//             'has_expiry' => !is_null($expiryDate),
//             'is_required' => in_array($documentType, ['kra_pin_certificate', 'business_registration_certificate']),
//             'description' => $description,
//             'created_at' => now(),
//             'updated_at' => now(),
//         ]);
//     }
// }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $accountManagers = User::where('role', 'account_manager')->get();

        return view('users.edit', compact('user', 'accountManagers'));
    }

    /**
     * Update the specified user in storage.
     */

    public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'company_name' => 'nullable|string|max:255',
        'email' => [
            'required',
            'email',
            Rule::unique('users')->ignore($user->id),
        ],
        'phone' => 'nullable|string|max:20',
        'role' => 'required|in:debt_manager,customer,admin,finance,designer,surveyor,technician,ict_engineer,account_manager,system_admin,accountmanager_admin,technical_admin,finance_admin,guest,county_ict_engineer,regional_manager',
        'status' => 'required|in:active,inactive,suspended',
        'account_manager_id' => 'nullable|exists:users,id',
        'lease_start_date' => 'nullable|date',
        'billing_frequency' => 'nullable|in:monthly,quarterly,annually',
        'monthly_rate' => 'nullable|numeric|min:0',
        'auto_billing_enabled' => 'boolean',
        'assignment_notes' => 'nullable|string|max:1000',

        // New county fields
        'county_id' => 'nullable|exists:counties,id',
        'county_notes' => 'nullable|string|max:500',

        // Address fields
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',

        // Password update (optional)
        'password' => 'nullable|min:8|confirmed',
    ]);

    // Validate county_id is required for county_ict_engineer role
    if ($validated['role'] === 'county_ict_engineer' && empty($validated['county_id'])) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['county_id' => 'County is required for County ICT Engineers.']);
    }

    try {
        DB::beginTransaction();

        $updateData = [
            'name' => $validated['name'],
            'company_name' => $validated['company_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'status' => $validated['status'],
            'account_manager_id' => $validated['account_manager_id'] ?? null,
            'lease_start_date' => $validated['lease_start_date'] ?? null,
            'billing_frequency' => $validated['billing_frequency'] ?? 'monthly',
            'monthly_rate' => $validated['monthly_rate'] ?? 0.00,
            'auto_billing_enabled' => $validated['auto_billing_enabled'] ?? true,
            'assignment_notes' => $validated['assignment_notes'] ?? null,

            // County fields
            'county_id' => $validated['county_id'] ?? null,
            'county_notes' => $validated['county_notes'] ?? null,

            // Address fields
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
        ];

        // Update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // Update assigned_at if account manager is assigned
        if ($validated['account_manager_id'] && !$user->account_manager_id) {
            $updateData['assigned_at'] = now();
        } elseif (!$validated['account_manager_id'] && $user->account_manager_id) {
            // Clear assigned_at if account manager is removed
            $updateData['assigned_at'] = null;
        }

        // Handle county_assigned_at
        if ($validated['county_id'] && $user->county_id != $validated['county_id']) {
            // Set timestamp if county is being assigned or changed
            $updateData['county_assigned_at'] = now();
        } elseif (!$validated['county_id'] && $user->county_id) {
            // Clear timestamp if county is removed
            $updateData['county_assigned_at'] = null;
        }

        // Clear county-related data if role is not county_ict_engineer
        if ($validated['role'] !== 'county_ict_engineer') {
            $updateData['county_id'] = null;
            $updateData['county_notes'] = null;
            $updateData['county_assigned_at'] = null;
        }

        // Clear account manager data if role is not customer
        if ($validated['role'] !== 'customer') {
            $updateData['account_manager_id'] = null;
            $updateData['assigned_at'] = null;
            // Also clear billing info if not a customer
            $updateData['lease_start_date'] = null;
            $updateData['billing_frequency'] = 'monthly';
            $updateData['monthly_rate'] = 0.00;
            $updateData['auto_billing_enabled'] = true;
        }

        // Update profile completion timestamp if all required fields are filled
        if (!$user->profile_completed_at) {
            $requiredFields = ['name', 'email', 'phone', 'address'];
            $isComplete = true;
            foreach ($requiredFields as $field) {
                if (empty($updateData[$field])) {
                    $isComplete = false;
                    break;
                }
            }
            if ($isComplete) {
                $updateData['profile_completed_at'] = now();
            }
        }

        $user->update($updateData);

        // Update Spatie role if using permissions package
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            // Remove all existing roles
            $user->roles()->detach();

            // Assign new role
            $role = \Spatie\Permission\Models\Role::firstOrCreate(
                ['name' => $validated['role']],
                [
                    'name' => $validated['role'],
                    'guard_name' => 'web',
                ]
            );
            $user->assignRole($role);
        }

        DB::commit();

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error updating user: ' . $e->getMessage());
    }
}

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.edit', $user)
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, User $user, $role)
    {
        $validRoles = ['customer', 'admin', 'finance', 'designer', 'surveyor', 'technician', 'account_manager', 'system_admin', 'guest'];

        if (!in_array($role, $validRoles)) {
            return redirect()->route('users.index')
                ->with('error', 'Invalid role specified!');
        }

        $user->update(['role' => $role]);

        return redirect()->route('users.index')
            ->with('success', "User role updated to " . ucfirst($role) . " successfully!");
    }

    /**
     * Get user statistics
     */
    public function stats()
    {
        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'customers' => User::where('role', 'customer')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
        ];

        return response()->json($stats);
    }

    public function createAndLoginUser(Request $request)
{
    // Step 1: Basic validation for user creation
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
    ]);

    try {
        // Step 2: Create basic user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(), // Auto-verify for immediate access
        ]);

        // Step 3: Automatically log the user in
        Auth::login($user);

        // Step 4: Redirect to complete profile page
        return redirect()->route('users.complete-profile')
            ->with('success', 'Account created successfully! Please complete your profile.');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error creating account: ' . $e->getMessage());
    }
}

public function completeCustomerProfile(Request $request)
{
    // Ensure customer is logged in
    if (!Auth::check() || Auth::user()->role !== 'customer') {
        return redirect()->route('login')
            ->with('error', 'Please log in as a customer to complete your profile.');
    }
/** @var \App\Models\User $user */
    $user = Auth::user();

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

        // Update customer with complete profile data
        $user->update([
            'lease_start_date' => $validated['lease_start_date'] ?? null,
            'billing_frequency' => $validated['billing_frequency'] ?? 'monthly',
            'monthly_rate' => $validated['monthly_rate'] ?? 0.00,
            'auto_billing_enabled' => $validated['auto_billing_enabled'] ?? true,
            'profile_completed_at' => now(), // Track profile completion
        ]);

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
    return view('auth.register'); // Your existing blade file
}

public function showCompleteCustomerProfileForm()
{
    if (!Auth::check() || Auth::user()->role !== 'customer') {
        return redirect()->route('login');
    }

    $documentTypes = DocumentType::where('is_active', true)->get();
    $user = Auth::user();

    return view('customer.complete-profile', compact('documentTypes', 'user'));
}
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

}
