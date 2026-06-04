<?php

namespace App\Http\Controllers;

use App\Models\County;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        $documentTypes = DocumentType::where('is_active', true)->orderBy('sort_order')->get();
        $accountManagers = User::where('role', 'account_manager')->get();
        $counties = County::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'documentTypes', 'accountManagers', 'counties'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $counties = County::orderBy('name')->get();
        $accountManagers = User::where('role', 'account_manager')->get();

        // Role options (strings)
        $roles = [
            'admin', 'customer', 'finance', 'designer', 'surveyor',
            'technician', 'ict_engineer', 'executive', 'account_manager',
            'system_admin', 'accountmanager_admin', 'technical_admin',
            'finance_admin', 'guest', 'county_ict_engineer',
            'regional_manager', 'debt_manager'
        ];

        $roleDisplayNames = [
            'admin' => 'Administrator',
            'customer' => 'Customer',
            'finance' => 'Finance Manager',
            'designer' => 'Network Designer',
            'surveyor' => 'Field Surveyor',
            'technician' => 'Field Technician',
            'ict_engineer' => 'ICT Engineer',
            'executive' => 'Executive',
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

        return view('admin.users.create', compact('counties', 'accountManagers', 'roles', 'roleDisplayNames'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
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
            'county_id' => 'nullable|exists:counties,id',
            'county_notes' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'assignment_notes' => 'nullable|string|max:1000',
            'documents' => 'nullable|array',
            'documents.*.document_type' => 'required_with:documents.*.file|string|exists:document_types,document_type',
            'documents.*.file' => 'nullable|file',
            'documents.*.description' => 'nullable|string|max:500',
            'documents.*.expiry_date' => 'nullable|date|after:today',
        ]);

        // Validate county requirement for county_ict_engineer
        if ($validated['role'] === 'county_ict_engineer' && empty($validated['county_id'])) {
            return redirect()->back()->withInput()->withErrors(['county_id' => 'County is required for County ICT Engineers.']);
        }

        // Validate documents
        if ($request->has('documents')) {
            foreach ($request->documents as $index => $document) {
                if (!empty($document['file']) && isset($document['document_type'])) {
                    $docType = $documentTypes->firstWhere('document_type', $document['document_type']);
                    if ($docType) {
                        $rules = ['file'];
                        if ($docType->max_file_size) {
                            $rules[] = "max:{$docType->max_file_size}";
                        }
                        $allowedExtensions = $docType->allowed_extensions;
                        if (!empty($allowedExtensions) && is_array($allowedExtensions)) {
                            $rules[] = "mimes:" . implode(',', $allowedExtensions);
                        }
                        $request->validate(["documents.{$index}.file" => $rules]);
                    }
                }
            }
        }

        try {
            DB::beginTransaction();

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
                'county_id' => $validated['county_id'] ?? null,
                'county_notes' => $validated['county_notes'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'country' => $validated['country'] ?? null,
                'assignment_notes' => $validated['assignment_notes'] ?? null,
            ];

            if ($validated['county_id']) {
                $userData['county_assigned_at'] = now();
            }

            if (!empty($validated['account_manager_id'])) {
                $userData['account_manager_id'] = $validated['account_manager_id'];
                $userData['assigned_at'] = now();
            }

            $user = User::create($userData);

            // Assign Spatie role if available
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $role = \Spatie\Permission\Models\Role::firstOrCreate(
                    ['name' => $validated['role']],
                    ['name' => $validated['role'], 'guard_name' => 'web']
                );
                $user->assignRole($role);
            }

            // Save documents
            if ($request->has('documents')) {
                $this->saveDocuments($request, $user);
            }

            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'User created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $accountManagers = User::where('role', 'account_manager')->get();
        $counties = County::orderBy('name')->get();

        $roles = [
            'admin', 'customer', 'finance', 'designer', 'surveyor',
            'technician', 'ict_engineer', 'executive', 'account_manager',
            'system_admin', 'accountmanager_admin', 'technical_admin',
            'finance_admin', 'guest', 'county_ict_engineer',
            'regional_manager', 'debt_manager'
        ];

        $roleDisplayNames = [
            'admin' => 'Administrator',
            'customer' => 'Customer',
            'finance' => 'Finance Manager',
            'designer' => 'Network Designer',
            'surveyor' => 'Field Surveyor',
            'technician' => 'Field Technician',
            'ict_engineer' => 'ICT Engineer',
            'executive' => 'Executive',
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

        return view('admin.users.edit', compact('user', 'accountManagers', 'counties', 'roles', 'roleDisplayNames'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:debt_manager,customer,admin,finance,designer,surveyor,technician,ict_engineer,account_manager,system_admin,accountmanager_admin,technical_admin,finance_admin,guest,county_ict_engineer,regional_manager',
            'status' => 'required|in:active,inactive,suspended',
            'account_manager_id' => 'nullable|exists:users,id',
            'lease_start_date' => 'nullable|date',
            'billing_frequency' => 'nullable|in:monthly,quarterly,annually',
            'monthly_rate' => 'nullable|numeric|min:0',
            'auto_billing_enabled' => 'boolean',
            'assignment_notes' => 'nullable|string|max:1000',
            'county_id' => 'nullable|exists:counties,id',
            'county_notes' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if ($validated['role'] === 'county_ict_engineer' && empty($validated['county_id'])) {
            return redirect()->back()->withInput()->withErrors(['county_id' => 'County is required for County ICT Engineers.']);
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
                'county_id' => $validated['county_id'] ?? null,
                'county_notes' => $validated['county_notes'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'country' => $validated['country'] ?? null,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            // Update assignment timestamps
            if ($validated['account_manager_id'] && !$user->account_manager_id) {
                $updateData['assigned_at'] = now();
            } elseif (!$validated['account_manager_id'] && $user->account_manager_id) {
                $updateData['assigned_at'] = null;
            }

            if ($validated['county_id'] && $user->county_id != $validated['county_id']) {
                $updateData['county_assigned_at'] = now();
            } elseif (!$validated['county_id'] && $user->county_id) {
                $updateData['county_assigned_at'] = null;
            }

            // Clear role-specific fields
            if ($validated['role'] !== 'county_ict_engineer') {
                $updateData['county_id'] = null;
                $updateData['county_notes'] = null;
                $updateData['county_assigned_at'] = null;
            }

            if ($validated['role'] !== 'customer') {
                $updateData['account_manager_id'] = null;
                $updateData['assigned_at'] = null;
                $updateData['lease_start_date'] = null;
                $updateData['billing_frequency'] = 'monthly';
                $updateData['monthly_rate'] = 0.00;
                $updateData['auto_billing_enabled'] = true;
            }

            $user->update($updateData);

            // Update Spatie role
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $user->roles()->detach();
                $role = \Spatie\Permission\Models\Role::firstOrCreate(
                    ['name' => $validated['role']],
                    ['name' => $validated['role'], 'guard_name' => 'web']
                );
                $user->assignRole($role);
            }

            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    /**
     * Save documents for the user.
     */
    private function saveDocuments(Request $request, User $user)
    {
        $documents = $request->documents;
        $uploadedBy = Auth::id();

        foreach ($documents as $documentData) {
            if (empty($documentData['file']) || !$documentData['file']->isValid()) {
                continue;
            }

            $file = $documentData['file'];
            $documentTypeValue = $documentData['document_type'];
            $description = $documentData['description'] ?? null;
            $expiryDate = isset($documentData['expiry_date']) ? Carbon::parse($documentData['expiry_date']) : null;

            $docType = DocumentType::where('document_type', $documentTypeValue)->first();
            if (!$docType) {
                continue;
            }

            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $fileName, 'public');

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
            ]);
        }
    }

    /**
     * Remove a role from user.
     */
    public function removeRole($id, $role)
    {
        $user = User::findOrFail($id);

        if ($user->role === $role) {
            $user->role = 'customer';
            $user->save();
        }

        return redirect()->back()->with('success', "Role '{$role}' removed from {$user->name}");
    }

    /**
     * Assign role to user.
     */
    public function assignRole(Request $request, User $user, $role)
    {
        $validRoles = ['customer', 'admin', 'finance', 'designer', 'surveyor', 'technician', 'account_manager', 'system_admin', 'guest', 'executive', 'ict_engineer'];

        if (!in_array($role, $validRoles)) {
            return redirect()->route('admin.users.index')->with('error', 'Invalid role specified!');
        }

        $user->update(['role' => $role]);

        return redirect()->route('admin.users.index')->with('success', "User role updated to " . ucfirst($role) . " successfully!");
    }

    /**
 * Check if email exists (for AJAX validation)
 */
public function checkEmail(Request $request)
{
    $email = $request->get('email');
    $exists = User::where('email', $email)->exists();

    return response()->json(['exists' => $exists]);
}
    /**
     * Get user statistics.
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

    /**
     * Update user password.
     */
    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('admin.users.edit', $user)->with('success', 'Password updated successfully!');
    }
}
