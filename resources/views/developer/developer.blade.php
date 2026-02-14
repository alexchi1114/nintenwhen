@extends('layouts.layout')
@section('pageTitle', $developer->name . ' - Developers')
@section('content')
	<nav>
	    <ol class="breadcrumb">
	    	<li class="breadcrumb-item"><a href="/">Home</a></li>
	        <li class="breadcrumb-item"><a href="/developer">Developers</a></li>
	        <li class="breadcrumb-item active">{{ $developer->name }}</li>
	    </ol>
	</nav>

	<div class="text-center">
		<h1 class="page-title franchise-title" id="page-title" style="border-bottom-color:#{{ $developer->primary_theme_color_hex }}">{{ $developer->name }}</h1>
	</div>

	@if($upcoming_games !== null && $upcoming_games->count() > 0)
		<div class="card w-100 mb-4">
			<div class="card-body">
				<h2>Upcoming Games</h2>
				@foreach($upcoming_games as $game)
					<x-game-release :game="$game" />
				@endforeach
			</div>
		</div>
	@endif

	<div class="card w-100 mb-4">
		<div class="card-body">

			<div class = "card w-100 mb-0 filters">
				<div class="card-body" id="searchFormContainer">
					<form name="searchForm" id="search-form">
						@if($types->count() > 1)
						<fieldset class="form-group mb-3">
							<legend class="filter-label">Developer Role</legend>
							<div class="row">
								@foreach($types as $type)
									<div class="col-6 col-sm-4 col-md-2">
										<div class="form-check">
											<input name="type[]" value="{{ $type }}" class="form-check-input" type="checkbox" id="type-{{ \Illuminate\Support\Str::slug($type) }}" data-search-field checked>
											<label class="form-check-label" for="type-{{ \Illuminate\Support\Str::slug($type) }}">
												{{ ucfirst($type) }}
											</label>
									    </div>
									</div>
								@endforeach
							</div>
						</fieldset>
						@endif

						<fieldset class="form-group">
							<legend class="filter-label">Filter by Game Type</legend>
							<div class="row">
								@foreach($tags as $tag)
									<div class="col-6 col-sm-4 col-md-2">
										<div class="form-check">
											<input name="tag[]" value="{{ $tag->id }}" class="form-check-input" type="checkbox" id="{{ $tag->code }}" data-search-field checked>
											<label class="form-check-label" for="{{ $tag->code }}">
												{{ $tag->display_name }}s
											</label>
									    </div>
									</div>
								@endforeach
							</div>
						</fieldset>
					</form>
				</div>
			</div>

			<div id="games-container" class="w-100">
				@include('developer.games')
			</div>
		</div>
	</div>
@stop

@section('scripts')
	<script type="text/javascript">
		const searchFormContainer = document.getElementById("searchFormContainer");

		function initDaysBars() {
			document.querySelectorAll(".days-bar").forEach(function(bar){
				const center = bar.querySelector(".center");
				const left = bar.querySelector(".left");
				const right = bar.querySelector(".right");
				const endWidth = left.offsetWidth;
				const centerWidth = (parseFloat(center.dataset.width) * bar.offsetWidth) - endWidth;
				const leftTranslation = Math.floor(centerWidth) - 1;

				right.style.transform = "translateX("+ leftTranslation +"px)";
				center.style.transform = "scaleX("+ Math.floor(centerWidth) +")";
			});
		}

		document.addEventListener('DOMContentLoaded', function(){
			initDaysBars();
		});

		document.querySelectorAll("[data-search-field]").forEach(function(el){
			el.addEventListener("change", search);
		});

		function search(){
			const tagInputs = document.querySelectorAll("input[name='tag[]']:not(:checked)");
			const tagData = Array.from(tagInputs).map(function(input) {
				return input.value;
			});

			const typeInputs = document.querySelectorAll("input[name='type[]']:not(:checked)");
			const excludedTypes = Array.from(typeInputs).map(function(input) {
				return input.value;
			});

			document.getElementById("loader-container").style.display = "block";

			const searchData = {
				tags: tagData,
				excluded_types: excludedTypes,
				developer_id: {{ $developer->id }}
			};

			axios.post('/developer/search/', searchData, {
				headers: { 'Content-Type': 'application/json' }
			})
			.then(function (response) {
				document.getElementById("games-container").innerHTML = response.data;
				document.getElementById("loader-container").style.display = "none";
				document.querySelectorAll(".days-bar").forEach(function(bar){
					const center = bar.querySelector(".center");
					const left = bar.querySelector(".left");
					const right = bar.querySelector(".right");
					const endWidth = left.offsetWidth;
					const centerWidth = (parseFloat(center.dataset.width) * bar.offsetWidth) - endWidth;
					const leftTranslation = Math.floor(centerWidth) - 1;

					right.style.marginRight = "-3px";
					right.style.transform = "translateX("+ leftTranslation +"px)";
					center.style.transform = "scaleX("+ Math.floor(centerWidth) +")";
				});
			})
			.catch(function (error) {
				console.log(error);
			});
		}
	</script>
@stop
