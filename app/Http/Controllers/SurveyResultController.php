<?php

namespace App\Http\Controllers;

use App\Models\DesignRequest;
use App\Models\SurveyResult;
use App\Models\SurveyRoute;
use Illuminate\Http\Request;

class SurveyResultController extends Controller
{

    // app/Http/Controllers/SurveyResultController.php

public function store(Request $request, DesignRequest $designRequest)
{
    $validated = $request->validate([
        // ... existing validation
    ]);

    // Create survey result
    $surveyResult = SurveyResult::create(array_merge($validated, [
        'design_request_id' => $designRequest->id,
        'surveyor_id' => $designRequest->surveyor_id,
    ]));

    // Automatically create survey routes
    $surveyResult->createSurveyRoutes();

    // Update design request status
    $designRequest->update([
        'survey_status' => 'completed',
        'survey_completed_at' => now(),
        'survey_actual_hours' => $request->actual_hours,
        'status' => 'design_in_progress',
    ]);

    return redirect()->route('design-requests.show', $designRequest)
        ->with('success', 'Survey completed and routes created successfully!');
}

public function approveRoute(Request $request, SurveyRoute $surveyRoute)
{
    $request->validate([
        'is_approved' => 'required|boolean',
        'rejection_reason' => 'required_if:is_approved,false',
    ]);

    $surveyRoute->update([
        'is_approved' => $request->is_approved,
        'rejection_reason' => $request->rejection_reason,
    ]);

    $message = $request->is_approved ?
        'Route approved and made available for design!' :
        'Route rejected.';

    return redirect()->back()->with('success', $message);
}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

       /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
