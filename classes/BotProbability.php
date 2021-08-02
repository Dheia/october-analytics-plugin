<?php

namespace Synder\Analytics\Classes;

use DeviceDetector\DeviceDetector;


class BotProbability extends DeviceDetector
{
    /**
     * Bot Probability Value
     *
     * @var array
     */
    public $probability = [];

    /**
     * Constructor
     *
     * @param string $agent The user agent to scan.
     */
    public function __construct($agent)
    {
        parent::__construct($agent);

        if (empty($this->userAgent)) {
            $this->parseBot();
            $this->parseOs();
            $this->parseClient();
            $this->parseDevice();
            $this->calculateProbability();
        }
    }

    /**
     * Is Valid
     * 
     * @return bool True if the parse-process ended up to be valid, False if not.
     */
    public function isValid()
    {
        return !empty($this->os) && !empty($this->client) && !is_null($this->bot);
    }

    /**
     * Add Probability Value
     *
     * @param float $amount
     * @return void
     */
    protected function addProbability($amount)
    {
        $this->probability[] = $amount;
    }

    /**
     * Calculate Probability
     *
     * @return void
     */
    public function calculateProbability()
    {
        if (empty($this->userAgent)) {
            return;
        }

        // An extremly low audience may pretent to be a bot, mainly to don't get tracked or 
        // recognized by any website or service.
        if ($this->isBot) {
            $this->addProbability(4.8);
        }

        // Old browser Detection
        //  Will be improved soon...
        if (!empty($this->client)) {
            [$browser, $version] = [$this->client['short_name'], $this->client['version']];

            if ($browser === 'FF') {
                $this->addProbability(version_compare($version, '10.0', '<')? 0.25: -0.25);
            }
            else if ($browser === 'BR') {
                $this->addProbability(-0.25);
            }
            else if ($browser === 'CL') {
                $this->addProbability(-0.25);
            }
            else if ($browser === 'CF') {
                $this->addProbability(0.25);
            }
            else if ($browser === 'HC') {
                $this->addProbability(0.5);
            }
            else if ($browser === 'CH' || $browser === 'CR') {
                $this->addProbability(version_compare($version, '28.0.0', '<')? 0.25: -0.25);
            }
            else if ($browser === 'IE') {
                $this->addProbability(version_compare($version, '8.0.0', '<')? 0.25: -0.25);
            }
            else if ($browser === 'KO') {
                $this->addProbability(0.25);
            }
            else if ($browser === 'NS') {
                $this->addProbability(0.25);
            }
            else {
                $this->addProbability(-0.15);
            }
        }

        // Old System Detection
        //  Coming Soon...
        if (!empty($this->os)) {
            [$os, $version] = [$this->client['short_name'], $this->client['version']];
        }
    }

    /**
     * Get Bot Proability
     *
     * @return float
     */
    public function getProbability()
    {
        $value = array_sum($this->probability);
        
        if ($value > 5.0) {
            return 5.0;
        } else if($value < 0.0) {
            return 0.0;
        } else {
            return $value;
        }
    }
}
