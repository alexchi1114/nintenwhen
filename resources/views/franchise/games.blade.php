@if(sizeof($games) > 0)
	<div class="container-fluid">
		<div class="row mb-4">
			<div class="col-12">
				<h2 class="mb-0">Since Last Release</h2>
				<div class="game-diff-container">
					@php
						$last_release_years = $games[0]->release_date->diff(\Carbon\Carbon::now())->format('%y');
						$last_release_months = $games[0]->release_date->diff(\Carbon\Carbon::now())->format('%m');
						$last_release_days = $games[0]->release_date->diff(\Carbon\Carbon::now())->format('%d');

						$avg_release_years = \Carbon\Carbon::now()->subDays($avg_days_between_releases)->diff()->format('%y');
						$avg_release_months = \Carbon\Carbon::now()->subDays($avg_days_between_releases)->diff()->format('%m');
						$avg_release_days = \Carbon\Carbon::now()->subDays($avg_days_between_releases)->diff()->format('%d');
					@endphp

					@if($last_release_years != "0")
						<div class="game-diff-num-container"><span class="game-diff-num">{{ $last_release_years }} </span>years</div>
					@endif

					@if($last_release_months != "0")
						<div class="game-diff-num-container"><span class="game-diff-num"> {{ $last_release_months }} </span>months</div>
					@endif

					@if($last_release_days != "0")
						<div class="game-diff-num-container"><span class="game-diff-num"> {{ $last_release_days }} </span>days</div>
					@endif
				</div>

				<div class="days-bar mb-2">
					<div class="left" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
					<div class="center" data-width="{{ $days_since_last_release / $max_days_between_releases }}" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
					<div class="right" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
				</div>
			</div>
		</div>

		<div class="row mb-4">
			<div class="col-12">
				<h2 class="mb-0">Average</h2>
				<div class="game-diff-container">
					@if($avg_release_years != "0")
						<div class="game-diff-num-container"><span class="game-diff-num">{{ $avg_release_years }} </span>years</div>
					@endif

					@if($avg_release_months != "0")
						<div class="game-diff-num-container"><span class="game-diff-num"> {{ $avg_release_months }} </span>months</div>
					@endif

					@if($avg_release_days != "0")
						<div class="game-diff-num-container"><span class="game-diff-num"> {{ $avg_release_days }} </span>days</div>
					@endif
				</div>

				<div class="days-bar">
					<div class="left" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
					<div class="center" data-width="{{ $avg_days_between_releases / $max_days_between_releases }}" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
					<div class="right" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="container-fluid franchise-container">
		@foreach($games as $i => $game)
			@php
				$days_diff = 0;
				if(isset($games[$i + 1])) {
					$days_diff = $game->release_date->diffInDays($games[$i + 1]->release_date, absolute: true);
					$days_diff_years = $game->release_date->diff($games[$i + 1]->release_date)->format('%y');
					$days_diff_months = $game->release_date->diff($games[$i + 1]->release_date)->format('%m');
					$days_diff_days = $game->release_date->diff($games[$i + 1]->release_date)->format('%d');
				}
			@endphp

			<div class="row game-container align-items-center">
				<div class="col-12 col-md-4">
					<!-- <img class="game-image mr-3" src="/images/{{ $game->img_path }}"> -->
					<h3 class="game-name">@if($game->external_link)<a href="{{ $game->external_link }}" target="_blank" rel="noopener noreferrer">{{ $game->name }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>@else{{ $game->name }}@endif</h3>
					<div class="game-tag-container d-none d-md-block">
						@foreach($game->tags->sortBy('display_name') as $tag)
							@if($tag->is_active)
							<div class="game-tag" style="background-color:#{{ $franchise->secondary_theme_color_hex }}">
								{{ $tag->display_name }}
							</div>
							@endif
						@endforeach
					</div>
				</div>
				<div class="col-12 col-md-2">
					<div>
						{{ $game->release_date === null ? "TBA" : $game->release_date->format('M d Y')}}
					</div>
					<div>

						{{ $game->systems->implode('name', '/') }}
					</div>
				</div>
				<div class="col-12 col-md-6 d-flex flex-column justify-content-center">
					<div class="game-diff-container">
						@if(isset($games[$i + 1]))
							@if($days_diff_years != "0")
								<div class="game-diff-num-container"><span class="game-diff-num">{{ $days_diff_years }} </span>years</div>
							@endif

							@if($days_diff_months != "0")
								<div class="game-diff-num-container"><span class="game-diff-num"> {{ $days_diff_months }} </span>months</div>
							@endif

							@if($days_diff_days != "0")
								<div class="game-diff-num-container"><span class="game-diff-num"> {{ $days_diff_days }} </span>days</div>
							@endif
						@endif
					</div>
					@if($i != sizeof($games) - 1)
						<div class="days-bar">
							<div class="left" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
							<div class="center" data-width="{{ $days_diff / $max_days_between_releases }}" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
							<div class="right" style="background-color:#{{$franchise->primary_theme_color_hex}};"></div>
						</div>
					@endif
				</div>
			</div>

		@endforeach
	</div>
@else
	<div class="alert alert-warning">No games with current selection</div>
@endif
