@props(['game'])

@php
$containerStyle = "border-color:#{$game->franchise->primary_theme_color_hex};";
if ($game->img_path) {
    $containerStyle .= "background-image:url('{$game->img_path}');";
}
@endphp

@if($game->external_link)
<a href="{{ $game->external_link }}" target="_blank" rel="noopener noreferrer" class="game-tile">
@else
<div class="game-tile">
@endif

    @if($game->release_date !== null)
        <div class="countdown-container" style="{{ $containerStyle }}">
            <h3 class="game-name">{{ $game->name }}@if($game->external_link) @endif</h3>
            <div class="countdown text-center" data-date="{{ $game->release_date }}" translate="no">
                <div class="digit">--<span class="letter">d</span></div>
                <div class="digit">--<span class="letter">h</span></div>
                <div class="digit">--<span class="letter">m</span></div>
                <div class="digit">--<span class="letter">s</span></div>
            </div>
            <div class="text-center"><strong>{{ $game->release_date->format('l, m/d/Y') }}</strong></div>
        </div>
    @elseif($game->release_date_tentative !== null)
        <div class="tbd" style="{{ $containerStyle }}">
            <h3 class="game-name">{{ $game->name }}@if($game->external_link) <i class="fas fa-external-link"></i>@endif</h3>
            <span>{{ $game->release_date_tentative }}</span>
        </div>
    @else
        <div class="tbd" style="{{ $containerStyle }}">
            <h3 class="game-name">{{ $game->name }}@if($game->external_link) <i class="fas fa-external-link"></i>@endif</h3>
            <span>TBA</span>
        </div>
    @endif

@if($game->external_link)
</a>
@else
</div>
@endif
