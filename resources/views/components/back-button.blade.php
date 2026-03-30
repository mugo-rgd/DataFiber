@props([
    'url' => null,
    'text' => 'Back',
    'icon' => 'fas fa-arrow-left',
    'size' => 'sm',
    'class' => ''
])

@if($url)
    <a href="{{ $url }}" class="btn btn-secondary btn-{{ $size }} {{ $class }}">
        <i class="{{ $icon }} me-1"></i> {{ $text }}
    </a>
@else
    <button type="button" onclick="window.history.back()" class="btn btn-secondary btn-{{ $size }} {{ $class }}">
        <i class="{{ $icon }} me-1"></i> {{ $text }}
    </button>
@endif
