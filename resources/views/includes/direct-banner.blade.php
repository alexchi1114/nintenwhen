@php
    $upcomingDirect = \App\Models\Direct::getUpcomingDirect();
@endphp

@if($upcomingDirect)
<div class="direct-banner">
    <div class="direct-banner-content">
        <a href="{{ $upcomingDirect->external_link }}" target="_blank" rel="noopener noreferrer" class="direct-banner-link text-decoration-underline">
            <span class="direct-banner-name">{{ $upcomingDirect->name }}</span>
            <span class="countdown" data-date="{{ $upcomingDirect->start_time->format('Y-m-d H:i') }}"></span>
        </a>
    </div>
</div>
@endif
