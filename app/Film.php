<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * @return App\Geo
     */
    public function geo()
    {
        return $this->hasOne('App\Geo');
    }

    /*
     * Queries
    */

    /**
     * @param  $query
     * @param  number
     * @param  float
     * @param  float
     *
     * @return [type]
     */
    public function scopeNear($query, $radius, $lat, $lng)
    {
        return $query->join('geos', 'films.id', '=', 'geos.film_id')
                    ->select(DB::raw('films.omdb, films.user_id, geos.lat, geos.lng,
										TRUNCATE( ( 6371 * acos( 
											cos( radians('.$lat.') ) * cos( radians( geos.lat ) ) 
											* cos( radians( geos.lng ) - radians('.$lng.') ) 
											+ sin( radians('.$lat.') ) 
											* sin( radians( geos.lat ) ) ) ), 3)
										AS distance '
                                    ))
                    ->having('distance', '<', $radius)
                    ->orderBy('distance');
    }
}
