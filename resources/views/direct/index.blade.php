@extends('layouts.layout')
@section('pageTitle', 'Nintendo Directs')
@section('content')
	<nav>
	    <ol class="breadcrumb">
	    	<li class="breadcrumb-item"><a href="/">Home</a></li>
	        <li class="breadcrumb-item active">Nintendo Directs</li>
	    </ol>
	</nav>

	<div class="text-center">
		<h1 class="page-title franchise-title" id="page-title" style="border-bottom-color:#E60012">Nintendo Directs</h1>
	</div>

	<div class="card w-100 mb-4">
		<div class="card-body">

			<div class = "card w-100 mb-0 filters">
				<div class="card-body" id="searchFormContainer">
					<form name="searchForm" id="search-form">
						<fieldset class="form-group">
							<legend class="filter-label">Filter by Type</legend>
							<div class="row">
								@foreach($tags as $tag)
									<div class="col-6 col-sm-4 col-md-2">
										<div class="form-check">
											<input name="tag[]" value="{{ $tag->id }}" class="form-check-input" type="checkbox" id="{{ $tag->code }}" data-search-field checked>
											<label class="form-check-label" for="{{ $tag->code }}">
												{{ $tag->display_name }}
											</label>
									    </div>
									</div>
								@endforeach
							</div>
						</fieldset>
					</form>
				</div>
			</div>

			<div id="directs-container" class="w-100">
				@include('direct.directs')
			</div>
		</div>
	</div>
@stop

@section('scripts')
	<script type="text/javascript">
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

			document.getElementById("loader-container").style.display = "block";

			const searchData = {
				tags: tagData
			};

			axios.post('/direct/search', searchData, {
				headers: { 'Content-Type': 'application/json' }
			})
			.then(function (response) {
				document.getElementById("directs-container").innerHTML = response.data;
				document.getElementById("loader-container").style.display = "none";
				initDaysBars();
			})
			.catch(function (error) {
				console.log(error);
			});
		}
	</script>
@stop
