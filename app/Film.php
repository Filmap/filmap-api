<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Film extends Model
{
    /**
	* The database table used by the model.
	*
	* @var string
	*/
    protected $table = 'films';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
	protected $fillable = ['omdb', 'user_id', 'watched'];

	/*
	* Relationships
	*/

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function geo()
	{
		return $this->hasOne('App\Geo');
	}

	public function scopeNear($query, $radius, $lat, $lng)
	{
		// SELECT id, ( 
		// 	3959 * acos( 
		// 		cos( radians(37) ) * cos( radians( lat ) ) 
		// 		* cos( radians( lng ) - radians(-122) ) 
		// 		+ sin( radians(37) ) * sin( radians( lat ) ) ) ) 
		// AS distance 
		// FROM markers 
		// HAVING distance < 25 
		// ORDER BY distance 
		// LIMIT 0 , 20;

		// return $this->geo()->near($radius, $lat, $lng);
		return $query->join('geos', 'films.id', '=', 'geos.film_id')
					->select(DB::raw('films.omdb, geos.lat, geos.lng,
										TRUNCATE( ( 6371 * acos( 
											cos( radians(' . $lat . ') ) * cos( radians( geos.lat ) ) 
											* cos( radians( geos.lng ) - radians(' . $lng . ') ) 
											+ sin( radians(' . $lat . ') ) 
											* sin( radians( geos.lat ) ) ) ), 3)
										AS distance '
									))
					->having('distance', '<', $radius)
					->orderBy('distance');
	}
}