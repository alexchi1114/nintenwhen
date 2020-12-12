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
		{{ $game->name }}
	@endforeach
@stop