@php
    $borderColors = [
        'blue' => '#0066B3',
        'green' => '#009639',
        'yellow' => '#FFD700',
        'dark' => '#003f20',
    ];

    $iconColors = [
        'blue' => '#0066B3',
        'green' => '#009639',
        'yellow' => '#FFD700',
        'dark' => '#003f20',
    ];

    $borderColor = $borderColors[$type] ?? '#0066B3';
    $iconColor = $iconColors[$type] ?? '#0066B3';
@endphp

<div class="card shadow-sm h-100" style="border-left: 4px solid {{ $borderColor }};">
    <div class="card-body">
        @if($title)
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted">{{ $title }}</h6>
                    {{ $slot ?? '' }}
                </div>
                @if($icon)
                    <i class="fas fa-{{ $icon }} fa-2x opacity-50" style="color: {{ $iconColor }};"></i>
                @endif
            </div>
        @else
            {{ $slot }}
        @endif
    </div>
</div>
