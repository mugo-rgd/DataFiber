@php
    // Method 1: Check if relationship is properly loaded
    if ($request->relationLoaded('quotations') && $request->quotations !== null) {
        $hasQuotations = $request->quotations->count() > 0;
        $existingQuotation = $hasQuotations ? $request->quotations->first() : null;
    } else {
        // Method 2: Fallback - check database directly
        $hasQuotations = \App\Models\Quotation::where('design_request_id', $request->id)->exists();
        $existingQuotation = $hasQuotations ? \App\Models\Quotation::where('design_request_id', $request->id)->first() : null;
    }

    $canGenerateQuote = !$hasQuotations && in_array($request->status, ['assigned', 'designed','pending']);
@endphp

@if($hasQuotations && $existingQuotation)
    <!-- Quotation Actions -->
    <div class="btn-group" role="group">
        <!-- Only this button opens in new window -->
        <a href="{{ route('designer.quotations.show', $existingQuotation) }}"
           target="_blank"
           rel="noopener noreferrer"
           class="btn btn-info btn-sm" title="View Quotation #{{ $existingQuotation->quotation_number }}">
            <i class="fas fa-search-dollar"></i>
        </a>

        @can('update', $existingQuotation)
            <a href="{{ route('designer.quotations.edit', $existingQuotation) }}"
               class="btn btn-warning btn-sm" title="Edit Quotation">
                <i class="fas fa-edit"></i>
            </a>
        @endcan

        <!-- Status indicator -->
        <span class="btn btn-outline-{{ $existingQuotation->status === 'sent' ? 'success' : 'secondary' }} btn-sm"
              title="Status: {{ ucfirst($existingQuotation->status) }}">
            <i class="fas fa-{{ $existingQuotation->status === 'sent' ? 'paper-plane' : 'file-invoice' }}"></i>
        </span>
    </div>

@elseif($canGenerateQuote)
    <!-- Generate Quote -->
    <a href="{{ route('designer.quotations.create', ['design_request_id' => $request->id]) }}"
       class="btn btn-success btn-sm" title="Generate Quote">
        <i class="fas fa-plus-circle"></i> Create Quote
    </a>

@else
    <!-- Cannot generate quote -->
    <span class="btn btn-outline-secondary btn-sm disabled"
          title="Cannot generate quote - Status: {{ $request->status }}">
        <i class="fas fa-ban"></i> Unavailable
    </span>
@endif
