<?php 

namespace Synder\Analytics\Models;

use October\Rain\Database\Model;
use Session;

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
        'views' => View::class
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
        'visits'
    ];

    /**
     * Generate Hash
     *
     * @return string
     */
    static public function generateHash()
    {
        $user = Session::get('Synder.Analytics.User', null);
        if (empty($user)) {
            $user = sha1($_SERVER['REMOTE_ADDR'] . Session::getId() . bin2hex(random_bytes(8)));
            Session::put('Synder.Analytics.User', $user);
        }
        return $user;
    }
}
