<?php 

namespace Synder\Analytics\Models;

use Model;
use System\Behaviors\SettingsModel;
use System\Classes\PluginManager;

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

    /**
     * Get Robots TXT
     * 
     * @return string
     */
    public function generateRobotsTxt()
    {
        $value = "\n\n";
        $value .= '#[synder time=' . $this->value['bot_robots_time'] . ']' . "\n";
        $value .= 'User-Agent: *' . "\n";
        $value .= 'Disallow: /' . $this->value['bot_robots_link'] . "\n";
        $value .= '#[/synder]' . "\n\n";
        return $value;
    }

    /**
     * Set RobotsTXT Values
     *
     * @return void
     */
    protected function setRobotsTxt()
    {
        $this->bot_robots_time = time();
        $this->bot_robots_link = bin2hex(random_bytes(6));

        $plugins = PluginManager::instance();
        if ($plugins->exists('zen.robots')) {

        }
    }

    /**
     * Unset RobotsTXT Values
     *
     * @return void
     */
    protected function unsetRobotsTxt()
    {

    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function setInvisibleLink()
    {

    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function unsetInvisibleLink()
    {

    }
}
