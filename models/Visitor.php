<?php 

namespace Synder\Analytics\Models;

use October\Rain\Database\Model;

use Synder\Analytics\Classes\BotProbability;


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
        'bot_details',
        'agent_details'
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
     *
     * @return string
     */
    static public function generateHash()
    {
        $value = $_SERVER['REMOTE_ADDR'] . ($_SERVER['HTTP_USER_AGENT'] ?? 'local') . date('Y-m-d');
        return hash_hmac('sha1', $value, env('APP_KEY'));
    }

    /**
     * Evaluate Visitor
     * 
     * @return void
     */
    public function evaluate($save = false)
    {
        if (!empty($this->agent)) {
            if ($this->agent[0] === '{') {
                try {
                    $this->agent = json_decode($this->agent, true)['agent'];
                } catch (\Exception $exception) {
                    $this->agent = '';
                }
            }
            if ($this->agent[0] === '"' && strrpos($this->agent, '"') === strlen($this->agent)-1) {
                $this->agent = substr($this->agent, 1, -2);
            }
        }

        $detect = new BotProbability($this->agent);
        if (!empty($this->bot_details)) {
            if (isset($this->bot_details['robots_trap'])) {
                $detect->setRobotsTrap(true);
            }
            if (isset($this->bot_details['inlink_trap'])) {
                $detect->setInvisibleLinkTrap(true);
            }
        }
        $detect->parse();

        if ($save) {
            $this->bot = $detect->getProbability();
            $this->bot_details = $detect->probabilities;
            $this->agent_details = $detect->getFullDetails();
            $this->browser = $detect->getBrowserDetail();
            $this->os = $detect->getOsDetail();
            $this->save();
            $this->reload();
        } else {
            $this->bot = $detect->getProbability();
            $this->bot_details = $detect->probabilities;
            $this->agent_details = $detect->getFullDetails();
            $this->browser = $detect->getBrowserDetail();
            $this->os = $detect->getOsDetail();
        }
    }

    /**
     * Add Bot Detail
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addBotDetail($key, $value)
    {
        if (!is_array($this->bot_details)) {
            $this->bot_details = [
                $key => $value
            ];
        } else {
            $this->bot_details = array_merge(
                $this->bot_details, [$key => $value]
            );
        }
        $this->save();
    }

    /**
     * Get Bot
     * 
     * @return string
     */
    public function getBotAttribute($value)
    {
        if (empty($this->agent)) {
            return 0.0;
        }
        if (empty($value)) {
            $this->evaluate(true);
            return $this->attributes['bot'] ?? 0.0;
        } else {
            return $value;
        }
    }

    /**
     * Get Browser
     * 
     * @return string
     */
    public function getBrowserAttribute($value)
    {
        if (empty($this->agent)) {
            return '';
        }
        if (empty($value) && empty($this->attributes['agent_details'])) {
            $this->evaluate(true);
            return $this->attributes['browser'] ?? '';
        } else {
            return $value;
        }
    }

    /**
     * Get OS
     * 
     * @return string
     */
    public function getOsAttribute($value)
    {
        if (empty($this->agent)) {
            return '';
        }
        if (empty($value) && empty($this->attributes['agent_details'])) {
            $this->evaluate(true);
            return $this->attributes['os'] ?? '';
        } else {
            return $value;
        }
    }
}
