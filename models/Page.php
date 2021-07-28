<?php 

namespace Synder\Analytics\Models;

use October\Rain\Database\Model;


class Page extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'synder_analytics';

    /**
     * Has Many Relationships
     *
     * @var array
     */
    public $hasMany = [
        'requests' => [
            Request::class,
            'key' => 'analytics_id' 
        ]
    ];
    
    /**
     * Has Many Through Relationships
     *
     * @var array
     */
    public $hasManyThrough = [
        'visitors' => [
            Visitor::class,
            'through'        => Request::class,
            'key'            => 'analytics_id',      // synder_analytics_requests
            'throughKey'     => 'id',                // synder_analytics_visitors
            'secondOtherKey' => 'visitor_id'         // synder_analytics_requests
        ],
    ];

    /**
     * Fillable Columns
     *
     * @var array
     */
    public $fillable = [
        'hash',
        'method',
        'path',
        'views',
        'visits'
    ];
}
