<?php

namespace App;

use Auth;
use Request;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KwtModel extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];    

    /**
     * The attributes that are NOT mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by_id',
        'created_oauth_client_id',
        'updated_by_id',
        'updated_oauth_client_id'
    ];
    
    protected $hidden = [
        'deleted_at'
    ];
    
    /**
     * Holds some callbacks for updating entries being inserted and updated
     */
    protected static function boot() {

        parent::boot();

        //Associate some auth data with entry being created, so we know who and when did this
        static::saving(function ($model) {
            if (Auth::check()) {
                $model->created_by_id = auth()->guard('api')->user()->id;
                $model->updated_by_id = auth()->guard('api')->user()->id;
                $model->created_oauth_client_id = auth()->guard('api')->user()->get_oauth_client()->id;
                $model->updated_oauth_client_id = auth()->guard('api')->user()->get_oauth_client()->id;
            } else {
                $model->created_oauth_client_id = Request::all()['oauth_client_id'];
                $model->updated_oauth_client_id = Request::all()['oauth_client_id'];
            }
        });

        //Associate some auth data with entry being updated, so we know who and when did this
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by_id = auth()->guard('api')->user()->id;
                $model->updated_oauth_client_id = auth()->guard('api')->user()->get_oauth_client()->id;
            } else {
                $model->updated_oauth_client_id = Request::all()['oauth_client_id'];
            }
        });
    }

    /**
     * Retrieves user-creator of an entry
     * 
     * @return App\User
     */
    public function creator() {
        return $this->belongsTo('App\User', 'created_by_id', 'id');
    }

    /**
     * Retrieves user-last-updater of an entry
     * 
     * @return App\User
     */
    public function updater() {
        return $this->belongsTo('App\User', 'updated_by_id', 'id');
    }
    
    public function scopeToday($query) {
        return $query->where('created_at', '>', Carbon::today())->where('created_at', '<', Carbon::today()->addDay());
    }

    public function scopeWeek($query) {
        return $query->where('created_at', '>', Carbon::today()->subWeek())->where('created_at', '<', Carbon::today()->addDay());
    }

    public function scopeMonth($query) {
        return $query->where('created_at', '>', Carbon::today()->subMonth())->where('created_at', '<', Carbon::today()->addDay());
    }
    
    /**
     * Performs all respective increments and audit
     * 
     * @param array $inventory
     * @param array $inventoryAdjustment
     * 
     * @return array $totals the new totals we have for ProductInventory after adjustment
     */
    public static function adjustInventory($inventory, $inventoryAdjustment)
    {
        if ( !is_array($inventory) )
        {
            $inventory = $inventory->getAttributes();
        };

        if ( !is_array($inventoryAdjustment) )
        {
            $inventoryAdjustment = $inventoryAdjustment->getAttributes();
        };

        $totals = [];

        $keys = [
            'items_in_stock',
            'items_supplied',
            'items_reserved',
            'items_shipped',
            'items_returned',
            'items_lost_stolen'
        ];

        foreach ( $keys as $key )
        {
            $value  = array_key_exists($key, $inventory) ? $inventory[$key] : 0;
            $value += array_key_exists($key, $inventoryAdjustment) ? $inventoryAdjustment[$key] : 0;

            if ( $key == 'items_in_stock' )
            {
                $value = max(0, $value);
            }

            $totals[$key] = $value;
        }

        return $totals;
    }

}
