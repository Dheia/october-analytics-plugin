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
    public $probabilities = [];

    /**
     * Constructor
     *
     * @param string $agent The user agent to scan.
     */
    public function __construct($agent)
    {
        parent::__construct($agent);
    }

    /**
     * Set RobotsTXT Trap
     * 
     * @return void
     */
    public function setRobotsTrap(bool $value)
    {
        if ($value) {
            $this->addProbability('robots_trap', 1.5);
        } else {
            $this->addProbability('robots_trap', -0.5);
        }
    }

    /**
     * Set Invisible Link Trap
     * 
     * @return void
     */
    public function setInvisibleLinkTrap(bool $value)
    {
        if ($value) {
            $this->addProbability('inlink_trap', 1.2);
        } else {
            $this->addProbability('inlink_trap', -0.5);
        }
    }

    /**
     * @inheritDoc
     */
    public function parse(): void
    {
        if (empty($this->userAgent)) {
            return;
        }
        $this->parsed = true;

        $this->parseBot();
        $this->parseOs();
        $this->parseClient();
        $this->parseDevice();
        $this->calculateProbability();
    }

    /**
     * Add Probability Value
     *
     * @param float $amount
     * @return void
     */
    protected function addProbability($key, $amount)
    {
        $this->probabilities[$key] = $amount;
    }

    /**
     * Calculate Probability
     *
     * @return void
     */
    protected function calculateProbability()
    {
        if (empty($this->userAgent)) {
            $this->addProbability('no_user_agent', 3.7);
            return;
        }

        // Filter Python Requests
        if (strpos($this->userAgent, 'python-requests') === 0) {
            $this->addProbability('invalid_user_agent', 4.8);
            return;
        }

        // An extremly low audience may pretent to be a bot, mainly to don't get tracked or 
        // recognized by any website or service.
        if ($this->isBot()) {
            $this->addProbability('user_agent', 4.8);
        }

        // Old browser Detection
        //  Will be improved soon...
        if (!empty($this->client)) {
            [$browser, $version] = [$this->client['short_name'] ?? 'unknown', $this->client['version']];

            if ($browser === 'FF') {
                $this->addProbability('deprecated_browser', version_compare($version, '10.0', '<')? 0.2: -0.2);
            }
            else if ($browser === 'BR') {
                $this->addProbability('deprecated_browser', -0.2);
            }
            else if ($browser === 'CL') {
                $this->addProbability('deprecated_browser', -0.2);
            }
            else if ($browser === 'CF') {
                $this->addProbability('deprecated_browser', 0.2);
            }
            else if ($browser === 'HC') {
                $this->addProbability('deprecated_browser', 0.5);
            }
            else if ($browser === 'CH' || $browser === 'CR') {
                $this->addProbability('deprecated_browser', version_compare($version, '28.0.0', '<')? 0.2: -0.2);
            }
            else if ($browser === 'IE') {
                $this->addProbability('deprecated_browser', version_compare($version, '8.0.0', '<')? 0.2: -0.2);
            }
            else if ($browser === 'KO') {
                $this->addProbability('deprecated_browser', 0.2);
            }
            else if ($browser === 'NS') {
                $this->addProbability('deprecated_browser', 0.2);
            }
            else if ($browser === 'unknown') {
                $this->addProbability('unknown_browser', 1.1);
            }
            else {
                $this->addProbability('deprecated_browser', -0.1);
            }
        } else {
            $this->addProbability('no_browser', 2.4);
        }

        // Old System Detection
        //  Coming Soon...
        if (!empty($this->os)) {
            [$os, $version] = [$this->os['short_name'] ?? 'unknown', $this->os['version']];

            if ($os === 'unknown') {
                $this->addProbability('unknown_os', 1.1);
            }
        } else {
            $this->addProbability('no_os', 2.4);
        }

        if (strpos($this->userAgent, 'Expanse') === 0) {
            dd($this->probabilities);
        }
    }

    /**
     * Get Bot Proability
     *
     * @return float
     */
    public function getProbability()
    {
        $value = array_sum(array_values($this->probabilities));
        
        if ($value > 5.0) {
            return 5.0;
        } else if($value <= 0.1) {
            return 0.1;
        } else {
            return $value;
        }
    }
    
    /**
     * Get Full Details
     *
     * @return array
     */
    public function getFullDetails()
    {
        return [
            'agent'     => $this->getUserAgent(),
            'client'    => $this->getClient(),
            'os'        => $this->getOs(),
            'device'    => $this->getDeviceName(),
            'brand'     => $this->getBrandName(),
            'model'     => $this->getModel(),
        ];
    }
    
    /**
     * Get Browser Detail
     *
     * @return void
     */
    public function getBrowserDetail()
    {
        $client = $this->getClient();
        if (empty($client) || !isset($client['name'])) {
            return '';
        }

        return $client['name'] . (isset($client['version'])? ' ' . $client['version']: '');
    }
    
    /**
     * Get OS Detail
     *
     * @return void
     */
    public function getOsDetail()
    {
        $os = $this->getOs();
        if (empty($os) || !isset($os['name'])) {
            return '';
        }

        return $os['name'] . (isset($os['version'])? ' ' . $os['version']: '');
    }
}
