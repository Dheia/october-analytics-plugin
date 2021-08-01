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
     * Settings Code
     * 
     * @var string
     */
    public $settingsCode = 'synder_analytics';
    
    /**
     * Settings Fields
     * 
     * @var string
     */
    public $settingsFields = 'fields.yaml';
    
    /**
     * Validation rules
     * 
     * @var array
     */
    public $rules = [];
    
    /**
     * @inheritDoc
     */
    public function initSettingsData()
    {
        $this->filter_backend_users = 1;
        $this->weekstart = 0;
        $this->dateformat = 'plain';
        $this->bot_lazy = 1;
        $this->bot_filter = 4.2;
    }

    /**
     * Get DateTime Definition
     *
     * @param boolean $time True to return timestring as well, False to just return the date.
     * @return string
     */
    public function getDateTimeDefinition($time = true)
    {
        if ($this->dateformat === 'plain') {
            return 'Y-m-d' . ($time? ' H:i:s': '');
        } else if($this->dateformat === 'big') {
            return 'Y, M d.' . ($time? ' - H:i': '');
        } else if($this->dateformat === 'middle') {
            return 'M d, Y' . ($time? ' - H:i': '');
        } else if($this->dateformat === 'little') {
            return 'd. M Y' . ($time? ' - H:i': '');
        } else if($this->dateformat === 'custom') {
            return $this->customformat;
        }
    }
}
