@extends('layouts.layout')
@section('pageTitle', 'Franchises')
@section('content')
	<nav>
	    <ol class="breadcrumb">
	    	<li class="breadcrumb-item"><a href="/">Home</a></li>
	        <li class="breadcrumb-item active">Franchises</li>
	    </ol>
	</nav>

	<div class="text-center">
		<h1 class="page-title" id="page-title">Franchises</h1>
		<div class="container mb-4" id="franchise-name-container">
			<input name="franchise-name" id="franchise-name" class="form-control" aria-label="Search for a franchise by name" placeholder="Seach by name" />
		</div>
		<p class="page-instructions">Select a franchise to view more details, including sub-series</p>
	</div>

	<div class="container">
		<div class="row d-flex">		
			@foreach($franchises as $franchise)
				@php
				 $since_width = $franchise->getMaxDaysBetweenReleases() > 0 ? $franchise->getDaysSinceLastRelease() / $franchise->getMaxDaysBetweenReleases() : 0;

				 $avg_width = $franchise->getMaxDaysBetweenReleases() > 0 ? $franchise->getAvgDaysBetweenReleases() / $franchise->getMaxDaysBetweenReleases() : 0;
				@endphp
			<div class="col col-6 col-sm-4 col-md-3 col-lg-2 d-flex p-3 franchise-overview-card-column" data-name="{{ strtolower($franchise->name) }}">
				<a class="card w-100 franchise-overview-card" href="/franchise/{{ $franchise->id }}">
					<h2 class="card-header">{{ $franchise->name }}</h2>
					<div class="card-body">
						<div class="face-status {{ $franchise->getStatus() }}">
							@if($franchise->getStatus() == "good")
								<i class="fas fa-smile"></i>
							@elseif($franchise->getStatus() == "neutral")
								<i class="fas fa-meh"></i>
							@elseif($franchise->getStatus() == "bad")
								<i class="fas fa-frown"></i>
							@elseif($franchise->getStatus() == "dead")
								<i class="fas fa-dizzy"></i>
							@endif
						</div>

						<h3 class="mb-0">Since Last Release</h3>
						<div class="game-diff-container">
							@php
								$last_release_years = \Carbon\Carbon::now()->subDays($franchise->getDaysSinceLastRelease())->diff()->format('%y');
								$last_release_months = \Carbon\Carbon::now()->subDays($franchise->getDaysSinceLastRelease())->diff()->format('%m');
								$last_release_days = \Carbon\Carbon::now()->subDays($franchise->getDaysSinceLastRelease())->diff()->format('%d');

								$avg_release_years = \Carbon\Carbon::now()->subDays($franchise->getAvgDaysBetweenReleases())->diff()->format('%y');
								$avg_release_months = \Carbon\Carbon::now()->subDays($franchise->getAvgDaysBetweenReleases())->diff()->format('%m');
								$avg_release_days = \Carbon\Carbon::now()->subDays($franchise->getAvgDaysBetweenReleases())->diff()->format('%d');
							@endphp

							@if($last_release_years != "0")
								<div class="game-diff-num-container" translate="no"><span class="game-diff-num">{{ $last_release_years }} </span>y</div>
							@endif

							@if($last_release_months != "0")
								<div class="game-diff-num-container" translate="no"><span class="game-diff-num"> {{ $last_release_months }} </span>m</div>
							@endif

							@if($last_release_days != "0")
								<div class="game-diff-num-container" translate="no"><span class="game-diff-num"> {{ $last_release_days }} </span>d</div>
							@endif
						</div>

						<div class="days-bar mb-2">
							<div class="left" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
							<div class="center" data-width="{{ $since_width }}" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
							<div class="right" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
						</div>

						<h3 class="mb-0">Average</h3>
						<div class="game-diff-container">
							@if($avg_release_years != "0")
								<div class="game-diff-num-container" translate="no"><span class="game-diff-num">{{ $avg_release_years }} </span>y</div>
							@endif

							@if($avg_release_months != "0")
								<div class="game-diff-num-container" translate="no"><span class="game-diff-num"> {{ $avg_release_months }} </span>m</div>
							@endif

							@if($avg_release_days != "0")
								<div class="game-diff-num-container" translate="no"><span class="game-diff-num"> {{ $avg_release_days }} </span>d</div>
							@endif
						</div>

						<div class="days-bar">
							<div class="left" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
							<div class="center" data-width="{{ $avg_width }}" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
							<div class="right" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
						</div>
					</div>
				</a>
			</div>
		    @endforeach
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
				let left_translation = Math.floor(center_width) - 3

				$center.css("transform", "scaleX("+ Math.floor(center_width) +")");
				$(this).find(".right").first().css("transform", "translateX("+ left_translation+"px)");

			});

			$("#franchise-name").on("input", function(){
				let $this  = $(this);
	            let search = $this.val().toLowerCase();

	            if(search.length > 0){
	                $('.franchise-overview-card-column').removeClass("d-flex").hide();
	                $(".franchise-overview-card-column[data-name*=" + search + "]").addClass("d-flex").show();
	            } else {
	            	$('.franchise-overview-card-column').addClass("d-flex").show();
	            }
			});
		});
	</script>
@stop
