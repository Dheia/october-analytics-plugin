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
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->bindEvent('model.beforeSave', [$this, 'customizeValue']);
        $this->bindEvent('model.afterSave', [$this, 'updateRobotsTxtProvider']);
    }

    /**
     * Customize Value
     * 
     * @return void
     */
    public function customizeValue()
    {
        if ($this->value['bot_robots'] === '1') {
            $this->setRobotsTxt();
        } else {
            $this->unsetRobotsTxt();
        }

        if ($this->value['bot_inlink'] === '1') {
            $this->setInvisibleLink();
        } else {
            $this->unsetInvisibleLink();
        }
    }
    
    /**
     * @inheritDoc
     */
    public function initSettingsData()
    {
        $this->filter_backend_users = 1;
        $this->weekstart = 0;
        $this->dateformat = 'plain';

        $this->dev_reevaluate = 1;

        $this->bot_lazy = 1;
        $this->bot_filter = 4.2;

        $this->bot_robots = '0';
        $this->bot_robots_relocate = '0';
        $this->bot_robots_relocate_cron = '0';
        $this->bot_robots_time = 0;
        $this->bot_robots_link = '';

        $this->bot_inlink = '0';
        $this->bot_inlink_relocate = '0';
        $this->bot_inlink_relocate_cron = '0';
        $this->bot_inlink_time = 0;
        $this->bot_inlink_link = '';
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
     * Get RobotsTXT Provider
     * 
     * @return string
     */
    public function getRobotsTxtProvider()
    {
        $plugins = PluginManager::instance();

        if ($plugins->exists('Arcane.Seo')) {
            return 'arcane.seo';
        } else if ($plugins->exists('Mohsin.Txt')) {
            return 'mohsin.txt';
        } else if ($plugins->exists('Zen.Robots')) {
            return 'zen.robots';
        } else {
            return 'synder.analytics';
        }
    }

    /**
     * Update RobotsTXT Provider
     *
     * @return void
     */
    public function updateRobotsTxtProvider()
    {
        $this->reload();

        if ($this->getRobotsTxtProvider() === 'arcane.seo') {
            $content = \Arcane\Seo\Models\Settings::get('robots_txt');
            if (empty($content)) {
                $content = "User-Agent: *\r\nAllow: /";
            }

            // Strip Honeypot
            [$start, $end] = [strpos($content, '#[synder'), strpos($content, '#[/synder]')];
            if ($start !== false) {
                $content = substr($content, 0, $start) . substr($content, $end + 12);
            }
            
            // Add Honeypot & Store
            if ($this->value['bot_robots'] === '1') {
                $content = trim($content) . $this->generateRobotsTxt();

                if (\Arcane\Seo\Models\Settings::get('enable_robots_txt') === '0') {
                    \Arcane\Seo\Models\Settings::set('enable_robots_txt', '1');
                }
            }
            \Arcane\Seo\Models\Settings::set('robots_txt', $content);
        }

        if ($this->getRobotsTxtProvider() === 'mohsin.txt') {
            $agent = \Mohsin\Txt\Models\Agent::where([
                'name' => '*:synder',
                'comment' => 'Synder Analytics'
            ])->first();
            $robot = \Mohsin\Txt\Models\Robot::where([
                'agent' => '*:synder',
            ])->first();

            if ($this->value['bot_robots'] === '1') {
                if (empty($agent)) {
                    $agent = new \Mohsin\Txt\Models\Agent();
                    $agent->name = '*:synder';
                    $agent->comment = 'Synder Analytics';
                    $agent->save();
                }
                if (empty($robot)) {
                    $robot = new \Mohsin\Txt\Models\Robot();
                    $robot->agent = '*:synder';
                    $robot->save();
                }

                $directive = \Mohsin\Txt\Models\Directive::where([
                    'robot_id' => $robot->id
                ])->first();

                if (empty($directive)) {
                    $directive = new \Mohsin\Txt\Models\Directive();
                    $directive->robot_id = $robot->id;
                } 
                $directive->type = 'Disallow';
                $directive->data = '/' . $this->value['bot_robots_link'];
                $directive->position = 999;
                $directive->save();
            } else {
                if (!empty($robot)) {
                    $directive = \Mohsin\Txt\Models\Directive::where([
                        'robot_id' => $robot->id
                    ])->first();
                    
                    if (!empty($directive)) {
                        $directive->delete();
                    }
                    $robot->delete();
                }
                if (!empty($agent)) {
                    $agent->delete();
                }
            }
        }

        if ($this->getRobotsTxtProvider() === 'zen.robots') {
            $content = \Zen\Robots\Models\Settings::get('content');

            // Strip Honeypot
            [$start, $end] = [strpos($content, '#[synder'), strpos($content, '#[/synder]')];
            if ($start !== false) {
                $content = substr($content, 0, $start) . substr($content, $end + 12);
            }
            
            // Add Honeypot & Store
            if ($this->value['bot_robots'] === '1') {
                $content = trim($content) . $this->generateRobotsTxt();
            }
            \Zen\Robots\Models\Settings::set('content', $content);
        }
    }

    /**
     * Set RobotsTXT Values
     *
     * @return void
     */
    protected function setRobotsTxt()
    {
        if (!empty($this->value['bot_robots_time'])) {
            if (time() - $this->value['bot_robots_time'] > 90 * 24 * 60 * 60) {
                $create = true;
            }
        } else {
            $create = true;
        }

        if (isset($create)) {
            $this->value = array_merge($this->value, [
                'bot_robots_time' => time(),
                'bot_robots_link' => bin2hex(random_bytes(6))
            ]);
        }
    }

    /**
     * Unset RobotsTXT Values
     *
     * @return void
     */
    protected function unsetRobotsTxt()
    {
        $this->value = array_merge($this->value, [
            'bot_robots_time' => 0,
            'bot_robots_link' => ''
        ]);
    }

    /**
     * Set Invisible Link Values
     *
     * @return void
     */
    protected function setInvisibleLink()
    {
        if (!empty($this->value['bot_inlink_time'])) {
            if (time() - $this->value['bot_inlink_time'] > 90 * 24 * 60 * 60) {
                $create = true;
            }
        } else {
            $create = true;
        }

        if (isset($create)) {
            $this->value = array_merge($this->value, [
                'bot_inlink_time' => time(),
                'bot_inlink_link' => bin2hex(random_bytes(6))
            ]);
        }
    }

    /**
     * Unset Invisible Link Values
     *
     * @return void
     */
    protected function unsetInvisibleLink()
    {
        $this->value = array_merge($this->value, [
            'bot_inlink_time' => 0,
            'bot_inlink_link' => ''
        ]);
    }
}
