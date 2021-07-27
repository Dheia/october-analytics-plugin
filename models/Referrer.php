<?php 

namespace Synder\Analytics\Models;

use October\Rain\Database\Model;


class Referrer extends Model
{
    /**
     * created_at column name
     */
    const CREATED_AT = 'first';

    /**
     * updated_at column name
     */
    const UPDATED_AT = 'last';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'synder_analytics_referrers';

    /**
     * Fillable Columns
     *
     * @var array
     */
    public $fillable = [
        'hash',
        'host',
        'url',
        'visits'
    ];
}
