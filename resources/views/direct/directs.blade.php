@if(sizeof($directs) > 0)
	<div class="container-fluid">
		<div class="row mb-4">
			<div class="col-12">
				<h2 class="mb-0">Since Last Direct</h2>
				<div class="game-diff-container">
					@php
						$last_direct_years = $directs[0]->start_time->diff(\Carbon\Carbon::now())->format('%y');
						$last_direct_months = $directs[0]->start_time->diff(\Carbon\Carbon::now())->format('%m');
						$last_direct_days = $directs[0]->start_time->diff(\Carbon\Carbon::now())->format('%d');

						$avg_direct_years = \Carbon\Carbon::now()->subDays($avg_days_between_directs)->diff()->format('%y');
						$avg_direct_months = \Carbon\Carbon::now()->subDays($avg_days_between_directs)->diff()->format('%m');
						$avg_direct_days = \Carbon\Carbon::now()->subDays($avg_days_between_directs)->diff()->format('%d');
					@endphp

					@if($last_direct_years != "0")
						<div class="game-diff-num-container"><span class="game-diff-num">{{ $last_direct_years }} </span>years</div>
					@endif

					@if($last_direct_months != "0")
						<div class="game-diff-num-container"><span class="game-diff-num"> {{ $last_direct_months }} </span>months</div>
					@endif

					@if($last_direct_days != "0")
						<div class="game-diff-num-container"><span class="game-diff-num"> {{ $last_direct_days }} </span>days</div>
					@endif
				</div>

				<div class="days-bar mb-2">
					<div class="left" style="background-color:#E60012;"></div>
					<div class="center" data-width="{{ $max_days_between_directs > 0 ? $days_since_last_direct / $max_days_between_directs : 0 }}" style="background-color:#E60012;"></div>
					<div class="right" style="background-color:#E60012;"></div>
				</div>
			</div>
		</div>

		<div class="row mb-4">
			<div class="col-12">
				<h2 class="mb-0">Average</h2>
				<div class="game-diff-container">
					@if($avg_direct_years != "0")
						<div class="game-diff-num-container"><span class="game-diff-num">{{ $avg_direct_years }} </span>years</div>
					@endif

					@if($avg_direct_months != "0")
						<div class="game-diff-num-container"><span class="game-diff-num"> {{ $avg_direct_months }} </span>months</div>
					@endif

					@if($avg_direct_days != "0")
						<div class="game-diff-num-container"><span class="game-diff-num"> {{ $avg_direct_days }} </span>days</div>
					@endif
				</div>

				<div class="days-bar">
					<div class="left" style="background-color:#E60012;"></div>
					<div class="center" data-width="{{ $max_days_between_directs > 0 ? $avg_days_between_directs / $max_days_between_directs : 0 }}" style="background-color:#E60012;"></div>
					<div class="right" style="background-color:#E60012;"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="container-fluid franchise-container">
		@foreach($directs as $i => $direct)
			@php
				$days_diff = 0;
				if(isset($directs[$i + 1])) {
					$days_diff = $direct->start_time->diffInDays($directs[$i + 1]->start_time, absolute: true);
					$days_diff_years = $direct->start_time->diff($directs[$i + 1]->start_time)->format('%y');
					$days_diff_months = $direct->start_time->diff($directs[$i + 1]->start_time)->format('%m');
					$days_diff_days = $direct->start_time->diff($directs[$i + 1]->start_time)->format('%d');
				}
			@endphp

			<div class="row game-container align-items-center">
				<div class="col-12 col-md-4">
					<h3 class="game-name">@if($direct->external_link)<a href="{{ $direct->external_link }}" target="_blank" rel="noopener noreferrer">{{ $direct->name }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>@else{{ $direct->name }}@endif</h3>
					@if($direct->description)
						<p class="text-muted mb-1">{{ $direct->description }}</p>
					@endif
					<div class="game-tag-container d-none d-md-block">
						@foreach($direct->tags->sortBy('display_order') as $tag)
							@if($tag->is_active)
							<div class="game-tag" style="background-color:#{{ $tag->color_hex }}">
								{{ $tag->display_name }}
							</div>
							@endif
						@endforeach
					</div>
				</div>
				<div class="col-12 col-md-2">
					<div>
						{{ $direct->start_time->format('M d Y') }}
					</div>
				</div>
				<div class="col-12 col-md-6 d-flex flex-column justify-content-center">
					<div class="game-diff-container">
						@if(isset($directs[$i + 1]))
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
					@if($i != sizeof($directs) - 1)
						<div class="days-bar">
							<div class="left" style="background-color:#E60012;"></div>
							<div class="center" data-width="{{ $max_days_between_directs > 0 ? $days_diff / $max_days_between_directs : 0 }}" style="background-color:#E60012;"></div>
							<div class="right" style="background-color:#E60012;"></div>
						</div>
					@endif
				</div>
			</div>

		@endforeach
	</div>
@else
	<div class="alert alert-warning">No directs with current selection</div>
@endif
