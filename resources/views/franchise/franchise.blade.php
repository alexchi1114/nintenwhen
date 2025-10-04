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

    <div class="card">
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
							<div class="countdown-container" style="background-color:#{{ $game->franchise->primary_theme_color_hex }}">
								<div class="countdown text-center" data-date="{{ $game->release_date }}">
									<div class="digit">--<span class="letter">d</span></div>
									<div class="digit">--<span class="letter">h</span></div>
									<div class="digit">--<span class="letter">m</span></div>
									<div class="digit">--<span class="letter">s</span></div>
								</div>
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
						<div class="row">
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
		$("#searchFormContainer").on('show.bs.collapse', function (e) {
			$("#filtersToggle").find(".expand").first().html("<i class='fas fa-caret-up'></i>");
		});

		$("#searchFormContainer").on('hide.bs.collapse', function (e) {
			$("#filtersToggle").find(".expand").first().html("<i class='fas fa-caret-down'></i>");
		});

		$(document).ready(function(){
			$(".days-bar").each(function(){
				let $center = $(this).find(".center").first();
				let end_width = $(this).find(".left").first().width();
				let center_width = ($center.data("width") * $(this).width()) - end_width;
				let left_translation = Math.floor(center_width) - 1

				$(this).find(".right").first().css("transform", "translateX("+ left_translation+"px)");
				$center.css("transform", "scaleX("+ Math.floor(center_width) +")");
			});

            $("#ai-analysis-loader").show();
            axios.get('{{ route("franchise.analysis.byid", ["id" => $franchise->id]) }}')
                .then(response => {
                    $("#ai-analysis-loader").hide();
                    $("#ai-analysis-container")[0].innerHTML = markdownLinksToHtml(response.data);
                })
                .catch(err => {
                    $("#ai-analysis-loader").hide();
                    $("#ai-analysis-container")[0].textContent = "There was an error loading AI analysis";
                });
		});

		$("[data-search-field]").on("change", search);

		function search(){

			var tagData = $("input[name='tag[]']:checked").map(function () {
      			return this.value;
		  	}).get();

		  	let child_franchise_id = isNaN(parseInt($("#seriesSelect").val())) ? null : parseInt($("#seriesSelect").val());

		  	$("#loader-container").show();

			var searchData = {
				tags: tagData,
				franchise_id: {{ $franchise->id }},
				child_franchise_id: child_franchise_id
			}
			axios.post('/franchise/search/', searchData, {
				  headers: { 'Content-Type': 'application/json' }
				})
				.then(function (response) {
					$("#games-container").html(response.data);
					$("#loader-container").hide();
					$(".days-bar").each(function(){
						let $center = $(this).find(".center").first();
						let end_width = $(this).find(".left").first().width();
						let center_width = ($center.data("width") * $(this).width()) - end_width;
						let left_translation = Math.floor(center_width) - 1
						$(this).find(".right").first().css("margin-right", "-3px").css("transform", "translateX("+ left_translation+"px)")
						$center.css("transform", "scaleX("+ Math.floor(center_width) +")");
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
