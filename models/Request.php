<?php 

namespace Synder\Analytics\Models;

use October\Rain\Database\Model;


class Request extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'synder_analytics_requests';

    /**
     * Belongs To Relationships
     *
     * @var array
     */
    public $belongsTo = [
        'page' => [
            Page::class,
            'key' => 'analytics_id'
        ],
        'visitor' => [
            Visitor::class,
            'key' => 'visitor_id'
        ]
    ];

    /**
     * JSONAble Columns
     * 
     * @var array
     */
    public $jsonable = [
        'request',
        'response'
    ];

    /**
     * Fillable Columns
     *
     * @var array
     */
    public $fillable = [
        'type',
        'order',
        'views',
        'request',
        'referrer',
        'response',
        'response_status',
    ];
}
