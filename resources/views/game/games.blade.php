<?php
	use App\Models\Game;
	$games = Game::all();
?>

@extends('layouts.layout')
@section('content')
	<div class="card game-card">
	  <div class="card-body">
	    Card
	    <button type="button" class="btn btn-primary btn-sm">Button</button>
	  </div>
	</div>
	@foreach($games as $game)
		@if($game->external_link)<a href="{{ $game->external_link }}" target="_blank" rel="noopener noreferrer">{{ $game->name }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>@else{{ $game->name }}@endif
	@endforeach
@stop
