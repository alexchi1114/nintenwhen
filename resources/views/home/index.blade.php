@extends('layouts.layout')
@section('pageTitle', 'Home')
@section('content')

<div class="row">
	<div class="col-12">
		<div class="jumbotron">
		  <h1 class="display-4">Nintenwhen</h1>
		  <p class="lead">Predict the next release dates for you favorite Nintendo franchises.</p>
		  <a class="btn btn-lg btn-primary" href="/franchise" role="button">View all franchises</a>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12 col-md-5">
		<div class="jumbotron small-padding">
			<h2>Upcoming Games</h2>
			@foreach($upcoming_games as $game)
			<div class="upcoming-game game-container">
				<h3 class="game-name">{{ $game->name }}</h3>
				@if($game->release_date !== null)
					<div class="countdown-container" style="background-color:#{{ $game->franchise->primary_theme_color_hex }}">
						<div class="countdown text-center" data-date="{{ $game->release_date }}" translate="no">	
							<div class="digit">--<span class="letter">d</span></div>
							<div class="digit">--<span class="letter">h</span></div>
							<div class="digit">--<span class="letter">m</span></div>
							<div class="digit">--<span class="letter">s</span></div>
						</div>
					</div>
				@else
					<div class="tbd">TBA</div>
				@endif

			</div>
			@endforeach
		</div>
		
	</div>
	<div class="col-12 col-md-7">
		<div class="jumbotron small-padding">
			<h2>Series to Watch</h2>
			@foreach($franchises_to_watch as $franchise)
			<div class="franchise-container py-4">
				<h3 class="franchise-title"><a href="/franchise/{{ $franchise->id }}">{{ $franchise->name }}</a></h3>
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
			</div>
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