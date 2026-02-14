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
				 $days_since = $franchise->getDaysSinceLastRelease();
				 $avg_days = $franchise->getAvgDaysBetweenReleases();
				 $max_days = $franchise->getMaxDaysBetweenReleases();
				 $status = $franchise->getStatus();

				 $since_width = $max_days > 0 ? $days_since / $max_days : 0;
				 $avg_width = $max_days > 0 ? $avg_days / $max_days : 0;
				@endphp
			<div class="col col-6 col-sm-4 col-md-3 col-lg-2 d-flex p-3 franchise-overview-card-column" data-name="{{ strtolower($franchise->name) }}">
				<a class="card w-100 franchise-overview-card text-decoration-none" href="/franchise/{{ $franchise->id }}">
					<h2 class="card-header">{{ $franchise->name }}</h2>
					<div class="card-body">
						<div class="face-status {{ $status }}">
							@if($status == "good")
								<i class="fas fa-smile"></i>
							@elseif($status == "neutral")
								<i class="fas fa-meh"></i>
							@elseif($status == "bad")
								<i class="fas fa-frown"></i>
							@elseif($status == "dead")
								<i class="fas fa-dizzy"></i>
							@endif
						</div>

						<h3 class="mb-0">Since Last Release</h3>
						<div class="game-diff-container">
							@php
								$last_release_diff = \Carbon\Carbon::now()->subDays($days_since)->diff();
								$last_release_years = $last_release_diff->format('%y');
								$last_release_months = $last_release_diff->format('%m');
								$last_release_days = $last_release_diff->format('%d');

								$avg_release_diff = \Carbon\Carbon::now()->subDays($avg_days)->diff();
								$avg_release_years = $avg_release_diff->format('%y');
								$avg_release_months = $avg_release_diff->format('%m');
								$avg_release_days = $avg_release_diff->format('%d');
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
		document.addEventListener('DOMContentLoaded', function(){
			document.querySelectorAll(".days-bar").forEach(function(bar){
				const center = bar.querySelector(".center");
				const left = bar.querySelector(".left");
				const right = bar.querySelector(".right");
				const endWidth = left.offsetWidth;
				const centerWidth = (parseFloat(center.dataset.width) * bar.offsetWidth) - endWidth;
				const leftTranslation = Math.floor(centerWidth) - 3;

				center.style.transform = "scaleX("+ Math.floor(centerWidth) +")";
				right.style.transform = "translateX("+ leftTranslation +"px)";
			});

			document.getElementById("franchise-name").addEventListener("input", function(){
				const search = this.value.toLowerCase();
				const cards = document.querySelectorAll('.franchise-overview-card-column');

				if(search.length > 0){
					cards.forEach(function(card){
						if(card.dataset.name.includes(search)){
							card.classList.add("d-flex");
							card.style.display = "";
						} else {
							card.classList.remove("d-flex");
							card.style.display = "none";
						}
					});
				} else {
					cards.forEach(function(card){
						card.classList.add("d-flex");
						card.style.display = "";
					});
				}
			});
		});
	</script>
@stop
