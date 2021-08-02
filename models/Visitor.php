<?php 

namespace Synder\Analytics\Models;

use Session;
use October\Rain\Database\Model;


class Visitor extends Model
{
    /**
     * created_at column name
     */
    const CREATED_AT = 'first_visit';

    /**
     * updated_at column name
     */
    const UPDATED_AT = 'last_seen';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'synder_analytics_visitors';

    /**
     * Belongs To Many Relationships
     *
     * @var array
     */
    public $belongsToMany = [
        'requests' => Request::class
    ];

    /**
     * JSONable Columns
     * 
     * @var array
     */
    public $jsonable = [
        'agent'
    ];

    /**
     * Fillable Columns
     *
     * @var array
     */
    public $fillable = [
        'hash',
        'bot',
        'agent',
        'views',
        'visits',
        'last_visit'
    ];
    
    /**
     * Generate Hash
     * @todo
     *
     * @return string
     */
    static public function generateHash()
    {
        $value = $_SERVER['REMOTE_ADDR'] . ($_SERVER['HTTP_USER_AGENT'] ?? 'local') . date('Y-m-d');
        return hash_hmac('sha1', $value, env('APP_KEY'));
    }
}
