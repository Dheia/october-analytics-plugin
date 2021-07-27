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
        'views' => [
            View::class,
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
            'through'       => View::class,
            'key'           => 'analytics_id',      # synder_analytics_views.analytics_id
            'throughKey'    => 'id',                # synder_analytics_visitors.id
            'otherKey'      => 'visitor_id'         # synder_analytics_views.visitor_id
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
        'unique'
    ];
}
