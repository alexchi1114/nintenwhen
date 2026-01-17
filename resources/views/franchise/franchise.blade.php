@extends('layouts.layout')
@section('pageTitle', $franchise->name . ' - Franchises')
@section('content')
	<nav>
	    <ol class="breadcrumb">
	    	<li class="breadcrumb-item"><a href="/">Home</a></li>
	        <li class="breadcrumb-item"><a href="/franchise">Franchises</a></li>
	        <li class="breadcrumb-item active">{{ $franchise->name }}</li>
	    </ol>
	</nav>

	<div class="text-center">
		<h1 class="page-title franchise-title" id="page-title" style="border-bottom-color:#{{ $franchise->primary_theme_color_hex }}">{{ $franchise->name }}</h1>
	</div>

    <div class="card mb-3">
        <div class="card-body">
            <h2><i class="fas fa-brain"></i> Nintenwhen Analysis</h2>
            <p id="ai-analysis-container"></p>
            <div class="d-flex justify-content-center">
                <div id="ai-analysis-loader" class='loader'>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
    </div>

	@if($upcoming_games !== null && $upcoming_games->count() > 0)
		<div class="card w-100 mb-4">
			<div class="card-body">
				<h2>Upcoming Games</h2>
				@foreach($upcoming_games as $game)
					<div class="upcoming-game game-container">
						<h3 class="game-name">{{ $game->name }}</h3>
						@if($game->release_date !== null)
							<div class="countdown-container" style="color:#{{ $game->franchise->primary_theme_color_hex }}; border-color:#{{ $game->franchise->primary_theme_color_hex }};">
								<div class="countdown text-center" data-date="{{ $game->release_date }}">
									<div class="digit">--<span class="letter">d</span></div>
									<div class="digit">--<span class="letter">h</span></div>
									<div class="digit">--<span class="letter">m</span></div>
									<div class="digit">--<span class="letter">s</span></div>
								</div>
                                <div class="text-center"><strong>{{ $game->release_date->format('m/d/Y') }}</strong></div>
							</div>
						@elseif($game->release_date_tentative !== null)
							<div class="tbd">{{ $game->release_date_tentative }}</div>
						@else
							<div class="tbd">TBA</div>
						@endif

					</div>
				@endforeach
			</div>
		</div>
	@endif

	<div class="card w-100 mb-4">
		<div class="card-body">

			<div class = "card w-100 mb-0 filters">
				<div class="card-body" id="searchFormContainer">
					<form name="searchForm" id="search-form">
						@if($children !== null && $children->count() > 0)
						<div class="row mb-3">
							<div class="col-12 col-md-6">
								<div class="form-group">
									<label for="seriesSelect" class="filter-label">Subseries</label>
									<select name="series" id="seriesSelect" class="form-control" data-search-field>
										<option>Select One</option>
										@foreach($children as $child)
											@if($child->id == $selected_franchise)
												<option value="{{ $child->id }}" selected>{{ $child->name }}</option>
											@else
												<option value="{{ $child->id }}">{{ $child->name }}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
						</div>
						@endif

						<fieldset class="form-group">
							<legend class="filter-label">Exclude</legend>
							<div class="row">
								@foreach($tags as $tag)
									<div class="col-6 col-sm-4 col-md-2">
										<div class="form-check">
											<input name="tag[]" value="{{ $tag->id }}" class="form-check-input" type="checkbox" id="{{ $tag->code }}" data-search-field>
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
				@include('franchise.games')
			</div>
		</div>
	</div>
@stop

@section('scripts')
	<script type="text/javascript">
		const searchFormContainer = document.getElementById("searchFormContainer");
		const filtersToggle = document.getElementById("filtersToggle");

		searchFormContainer.addEventListener('show.bs.collapse', function (e) {
			filtersToggle.querySelector(".expand").innerHTML = "<i class='fas fa-caret-up'></i>";
		});

		searchFormContainer.addEventListener('hide.bs.collapse', function (e) {
			filtersToggle.querySelector(".expand").innerHTML = "<i class='fas fa-caret-down'></i>";
		});

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

			const loader = document.getElementById("ai-analysis-loader");
			const container = document.getElementById("ai-analysis-container");

			loader.style.display = "flex";
			axios.get('{{ route("franchise.analysis.byid", ["id" => $franchise->id]) }}')
				.then(response => {
					loader.style.display = "none";
					container.innerHTML = markdownLinksToHtml(response.data);
				})
				.catch(err => {
					loader.style.display = "none";
					container.textContent = "There was an error loading AI analysis";
				});
		});

		document.querySelectorAll("[data-search-field]").forEach(function(el){
			el.addEventListener("change", search);
		});

		function search(){
			const tagInputs = document.querySelectorAll("input[name='tag[]']:checked");
			const tagData = Array.from(tagInputs).map(function(input) {
				return input.value;
			});

			const seriesSelect = document.getElementById("seriesSelect");
			const childFranchiseId = isNaN(parseInt(seriesSelect.value)) ? null : parseInt(seriesSelect.value);

			document.getElementById("loader-container").style.display = "block";

			const searchData = {
				tags: tagData,
				franchise_id: {{ $franchise->id }},
				child_franchise_id: childFranchiseId
			};

			axios.post('/franchise/search/', searchData, {
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

		function markdownLinksToHtml(input) {
			// Replace [text](url) with <a href="url">text</a>
			return input.replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2">$1</a>');
		}
	</script>
@stop
