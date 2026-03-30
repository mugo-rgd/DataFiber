<?php
// app/Http/Controllers/Admin/SurveyRequestController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesignRequest;
use App\Models\Surveyor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SurveyRequestController extends Controller
{
    /**
     * Display all design requests that need survey assignment
     */
    public function index(Request $request)
    {
        try {
            // Base query
            $query = DesignRequest::with(['customer', 'designer', 'surveyor.user'])
                ->where('status', '!=', 'cancelled')
                ->where(function ($q) {
                    $q->whereNull('surveyor_id')
                      ->orWhereIn('survey_status', ['not_required', 'requested']);
                });

            // Filtering
            if ($request->filled('filter') && $request->filter !== 'all') {
                switch ($request->filter) {
                    case 'needs_survey':
                        $query->where('survey_status', 'not_required')->whereNull('surveyor_id');
                        break;
                    case 'survey_requested':
                        $query->where('survey_status', 'requested');
                        break;
                    case 'survey_assigned':
                        $query->where('survey_status', 'assigned')->whereNotNull('surveyor_id');
                        break;
                    case 'survey_in_progress':
                        $query->where('survey_status', 'in_progress');
                        break;
                }
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('request_number', 'LIKE', "%{$search}%")
                      ->orWhere('title', 'LIKE', "%{$search}%")
                      ->orWhereHas('customer', fn($c) => $c->where('name', 'LIKE', "%{$search}%"));
                });
            }

            $designRequests = $query->orderByDesc('created_at')->paginate(20);

            // Get surveyors based on actual database structure
            $surveyors = $this->getAvailableSurveyors();

            return view('admin.survey-requests.index', compact('designRequests', 'surveyors'));

        } catch (\Exception $e) {
            logger('Survey requests index error: ' . $e->getMessage());
            return back()->with('error', 'Error loading survey requests: ' . $e->getMessage());
        }
    }

    /**
     * Get available surveyors based on actual database structure
     */
    private function getAvailableSurveyors()
    {
        // If surveyors table exists and has data, use it
        if (Schema::hasTable('surveyors')) {
            $surveyors = Surveyor::with('user')->where('is_active', true)->get();
            if ($surveyors->count() > 0) {
                return $surveyors;
            }
        }

        // Fallback: create temporary surveyor objects from users
        return User::where('role', 'surveyor')
            ->where('status', 'active')
            ->get()
            ->map(function ($user) {
                // Create a temporary surveyor object
                return (object) [
                    'id' => $user->id, // This might be the issue - we're using user.id instead of surveyor.id
                    'user' => $user,
                    'user_id' => $user->id,
                    'employee_id' => 'SUR' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'specialization' => 'General Surveyor',
                    'is_active' => true,
                ];
            });
    }

    /**
     * Get the correct surveyor ID based on database structure
     */
    private function getSurveyorId($inputSurveyorId)
    {
        // If surveyors table exists, check if input is a user ID or surveyor ID
        if (Schema::hasTable('surveyors')) {
            // Check if input is a valid surveyor ID
            $surveyor = Surveyor::find($inputSurveyorId);
            if ($surveyor) {
                return $surveyor->id;
            }

            // Check if input is a user ID that has a surveyor record
            $surveyorByUser = Surveyor::where('user_id', $inputSurveyorId)->first();
            if ($surveyorByUser) {
                return $surveyorByUser->id;
            }

            // If no surveyor record exists, create one
            $user = User::find($inputSurveyorId);
            if ($user && $user->role === 'surveyor') {
                $newSurveyor = Surveyor::create([
                    'user_id' => $user->id,
                    'employee_id' => 'SUR' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'specialization' => 'General',
                    'is_active' => true,
                ]);
                return $newSurveyor->id;
            }
        }

        // Fallback: use input as surveyor_id (assuming direct user->surveyor mapping)
        return $inputSurveyorId;
    }

    /**
     * Assign surveyor to design request - FIXED VERSION
     */
    public function assignSurveyor(Request $request, DesignRequest $designRequest)
    {
        // Debug the incoming request
        logger('Assign surveyor request:', $request->all());

        $validated = $request->validate([
            'surveyor_id' => 'required',
            'survey_requirements' => 'required|string|min:10',
            'survey_scheduled_at' => 'required|date|after:now',
            'survey_estimated_hours' => 'required|numeric|min:1|max:24',
        ]);

        try {
            DB::beginTransaction();

            // Get the correct surveyor ID based on database structure
            $correctSurveyorId = $this->getSurveyorId($validated['surveyor_id']);

            logger("Updating design request {$designRequest->id} with surveyor_id: {$correctSurveyorId}");

            // Update the design request
            $designRequest->update([
                'surveyor_id' => $correctSurveyorId,
                'survey_requirements' => $validated['survey_requirements'],
                'survey_scheduled_at' => $validated['survey_scheduled_at'],
                'survey_estimated_hours' => $validated['survey_estimated_hours'],
                'survey_status' => 'assigned',
                'survey_requested_at' => now(),
                'updated_at' => now(), // Force update
            ]);

            DB::commit();

            // Verify the update
            $updatedRequest = DesignRequest::find($designRequest->id);
            logger("After update - surveyor_id: {$updatedRequest->surveyor_id}, survey_status: {$updatedRequest->survey_status}");

            return redirect()->route('admin.survey-requests')
                ->with('success', 'Surveyor assigned successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            logger('Surveyor assignment error: ' . $e->getMessage());
            return back()->with('error', 'Failed to assign surveyor: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Quick test method to check database structure
     */
    public function testAssignment(DesignRequest $designRequest)
    {
        $testData = [
            'surveyor_id' => 1, // Test with first available
            'survey_requirements' => 'Test survey requirements',
            'survey_scheduled_at' => now()->addDays(1)->format('Y-m-d\TH:i'),
            'survey_estimated_hours' => 4,
        ];

        $request = new Request($testData);
        return $this->assignSurveyor($request, $designRequest);
    }
}
