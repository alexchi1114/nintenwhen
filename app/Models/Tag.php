<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
	public function games()
	{
		return $this->belongsToMany(Game::class);
	}

    public function getTagCountForFranchise($franchise_id) 
    {
    	return $this->games->where('franchise_id', '=', $franchise_id)->count();
    }
}
