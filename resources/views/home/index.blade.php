@extends('layouts.layout')
@section('content')

<div class="row">
	<div class="col-12">
		<div class="jumbotron">
		  <h1 class="display-4">Nintenwhen</h1>
		  <p class="lead">This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>
		  <hr class="my-4">
		  <p>It uses utility classes for typography and spacing to space content out within the larger container.</p>
		  <a class="btn btn-primary" href="/franchise" role="button">View all franchises</a>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12 col-md-6">
		<div class="jumbotron">
			<h2>Upcoming Games</h2>
			@foreach($upcoming_games as $game)
			<div class="row game-container upcoming-game">
				<div class="col-sm-6 col-md-4">
					<!-- <img class="game-image mr-3" src="/images/{{ $game->img_path }}"> -->
					<div class="game-name">{{ $game->name }}</div>
					@foreach($game->tags->sortBy('display_name') as $tag)
					<div class="game-tag" style="background-color:#{{ $tag->color_hex }}">
						{{ $tag->display_name }}
					</div>
					@endforeach
				</div>
				<div class="col-sm-6 col-md-2">
					<div>
						{{ $game->release_date === null ? "TBA" : $game->release_date->format('M d Y')}}
					</div>
					<div>
						{{ $game->systems->implode('name', '/') }}
					</div>
				</div>
				<div class="col-sm-6 col-md-4">
					@if($game->release_date !== null)
						<div class="countdown" data-date="{{ $game->release_date }}">
							<div class="digit">--<span class="letter">d</span></div>
							<div class="digit">--<span class="letter">h</span></div>
							<div class="digit">--<span class="letter">m</span></div>
							<div class="digit">--<span class="letter">s</span></div>
						</div>
					@endif
				</div>
				<div class="col-sm-6 col-md-2">
					<a href="{{ $game->preorder_link }}" target="_blank" class="btn btn-primary">
						Pre-order on Amazon
					</a>
				</div>
			</div>
			@endforeach
		</div>
		
	</div>
	<div class="col-12 col-md-6">
		<div class="jumbotron">
			<h2>Series to Watch</h2>
			@foreach($franchises_to_watch as $franchise)
			<a class="franchise-container" href="/franchise/{{ $franchise->id }}">
				<h3 class="franchise-title">{{ $franchise->name }}</h3>
				<h4 class="mb-0">Since Last Release</h4>
				<div class="game-diff-container">
					@php
						$since_width = $franchise->getMaxDaysBetweenReleases() > 0 ? $franchise->getDaysSinceLastRelease() / $franchise->getMaxDaysBetweenReleases() : 0;

			 			$avg_width = $franchise->getMaxDaysBetweenReleases() > 0 ? $franchise->getAvgDaysBetweenReleases() / $franchise->getMaxDaysBetweenReleases() : 0;

						$last_release_years = \Carbon\Carbon::now()->subDays($franchise->getDaysSinceLastRelease())->diff()->format('%y');
						$last_release_months = \Carbon\Carbon::now()->subDays($franchise->getDaysSinceLastRelease())->diff()->format('%m');
						$last_release_days = \Carbon\Carbon::now()->subDays($franchise->getDaysSinceLastRelease())->diff()->format('%d');

						$avg_release_years = \Carbon\Carbon::now()->subDays($franchise->getAvgDaysBetweenReleases())->diff()->format('%y');
						$avg_release_months = \Carbon\Carbon::now()->subDays($franchise->getAvgDaysBetweenReleases())->diff()->format('%m');
						$avg_release_days = \Carbon\Carbon::now()->subDays($franchise->getAvgDaysBetweenReleases())->diff()->format('%d');
					@endphp

					@if($last_release_years != "0")
						<div class="game-diff-num-container"><span class="game-diff-num">{{ $last_release_years }} </span>y</div>
					@endif

					@if($last_release_months != "0")
						<div class="game-diff-num-container"><span class="game-diff-num"> {{ $last_release_months }} </span>m</div>
					@endif

					@if($last_release_days != "0")
						<div class="game-diff-num-container"><span class="game-diff-num"> {{ $last_release_days }} </span>d</div>
					@endif
				</div>

				<div class="days-bar mb-2">
					<div class="left" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
					<div class="center" data-width="{{ $since_width }}" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
					<div class="right" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
				</div>

				<h4 class="mb-0">Average</h4>
				<div class="game-diff-container">
					@if($avg_release_years != "0")
						<div class="game-diff-num-container"><span class="game-diff-num">{{ $avg_release_years }} </span>y</div>
					@endif

					@if($avg_release_months != "0")
						<div class="game-diff-num-container"><span class="game-diff-num"> {{ $avg_release_months }} </span>m</div>
					@endif

					@if($avg_release_days != "0")
						<div class="game-diff-num-container"><span class="game-diff-num"> {{ $avg_release_days }} </span>d</div>
					@endif
				</div>

				<div class="days-bar">
					<div class="left" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
					<div class="center" data-width="{{ $avg_width }}" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
					<div class="right" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
				</div>
			</a>
			@endforeach
		</div>
	</div>
</div>
@stop
@section('scripts')
<script type="text/javascript">
	$(document).ready(function(){
		$(".days-bar").each(function(){
			let $center = $(this).find(".center").first();
			let end_width = $(this).find(".left").first().width();
			let center_width = ($center.data("width") * $(this).width()) - end_width;
			let left_translation = Math.floor(center_width) - 1

			$center.css("transform", "scaleX("+ Math.floor(center_width) +")");
			$(this).find(".right").first().css("transform", "translateX("+ left_translation+"px)");

		});
	});
</script>
@stop