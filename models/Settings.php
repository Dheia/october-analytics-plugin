<?php 

namespace Synder\Analytics\Models;

use Model;
use System\Behaviors\SettingsModel;


class Settings extends Model
{
    /**
     * Implement Controller Behaviours
     * 
     * @var array
     */
    public $implement = [
        SettingsModel::class
    ];
    
    /**
     * @var string
     */
    public $settingsCode = 'synder_analytics';
    
    /**
     * @var string
     */
    public $settingsFields = 'fields.yaml';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}
