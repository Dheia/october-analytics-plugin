<?php

namespace Synder\Analytics\Classes;

use DeviceDetector\DeviceDetector;


class BotProbability extends DeviceDetector
{
    /**
     * USER_AGENT
     * Just 'don't trust anyone' basic value.
     */
    const DONT_TRUST_ANYONE = 0.1;

    /**
     * USER_AGENT
     * Small Crawler Jobs or Test Applications and Scripts may don't add an User Agent at all.
     * Security-Aimed Users and Browsers avoid this header value as well.
     */
    const NO_USER_AGENT = 4.5;

    /**
     * USER_AGENT
     * Suspicious and known user agents, such as python-requests or Postman* are, and similar ones.
     */
    const SUSPICIOUS_USER_AGENT = 4.8;

    /**
     * USER_AGENT
     * Crawler, Spiders and Bots which reveal themselves (based on Matomo's DeviceDetector)
     */
    const BOT_USER_AGENT = 4.8;

    /**
     * BROWSER
     * Unkown Browsers are most-likly no good sign.
     */
    const UNKNOWN_BROWER = 2.5;

    /**
     * BROWSER
     * Deprecated Browsers are used or pretent to be used by bots and similar scripts.
     */
    const DEPRECATED_BROWER = 2.7;

    /**
     * BROWSER
     * Highly deprecated browsers are mainly used or pretent to be used by bots and similar scripts.
     */
    const HIGHLY_DEPRECATED_BROWSER = 3.5;

    /**
     * BROWSER
     * Real Human Visitors don't use a browser without GUI.
     */
    const HEADLESS_BROWSER = 4.5;

    /**
     * BROWSER
     * No Browser no human!
     */
    const NO_BROWSER = 3.5;

    /**
     * OPERATING SYSTEM
     * Unkown os' are most-likly no good sign.
     */
    const UNKNOWN_OS = 2.0;

    /**
     * OPERATING SYSTEM
     * Deprecated os' are used or pretent to be used by bots and similar scripts.
     */
    const DEPRECATED_OS = 1.4;

    /**
     * OPERATING SYSTEM
     * Highy deprecated os' are mainly used or pretend to be used by bots and similar scripts.
     */
    const HIGHLY_DEPRECATED_OS = 2.0;

    /**
     * OPERATING SYSTEM
     * No OS no human!
     */
    const NO_OS = 1.5;

    /**
     * TRAP
     * Someone or Something trapped in our Robots honeypot, could also a curious human.
     */
    const ROBOTS_TRAP = 1.8;

    /**
     * TRAP
     * Someone or Something trapped in our InvisibleLink honeypot, could also a curious human.
     */
    const INLINK_TRAP = 1.8;


    /**
     * Bot Probability Value
     *
     * @var array
     */
    public $probabilities = [];

    /**
     * Suspicious User Agents
     * 
     * @var array
     */
    public $suspiciousAgents = [
        '/^python/i',
        '/^Postman/i',
        '/Google Favicon?/'
    ];

    /**
     * Constructor
     *
     * @param string $agent The user agent to scan.
     */
    public function __construct($agent)
    {
        parent::__construct($agent ?? '');
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
     * Check if User Agent is suspicious
     *
     * @return boolean
     */
    public function isSuspiciousUserAgent(): bool
    {
        if (empty($this->userAgent)) {
            return false;
        }

        foreach ($this->suspiciousAgents AS $agent) {
            if (preg_match($agent, $this->userAgent) === 1) {
                return true;
            }
        }
        return false;
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
     * Set RobotsTXT Trap
     * 
     * @return void
     */
    public function setRobotsTrap(bool $value)
    {
        if ($value) {
            $this->addProbability('robots_trap', self::ROBOTS_TRAP);
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
            $this->addProbability('inlink_trap', self::INLINK_TRAP);
        }
    }

    /**
     * Calculate Probability
     *
     * @return void
     */
    protected function calculateProbability()
    {
        $this->addProbability('dont_trust', self::DONT_TRUST_ANYONE);

        // User Agents
        // @todo improve
        if (empty($this->userAgent)) {
            $this->addProbability('no_user_agent', self::NO_USER_AGENT);
            return;
        }
        if ($this->isSuspiciousUserAgent()) {
            $this->addProbability('suspicious_user_agent', self::SUSPICIOUS_USER_AGENT);
            return;
        }
        if ($this->isBot()) {
            $this->addProbability('bot_user_agent', self::BOT_USER_AGENT);
        }

        // Client / Browser
        // @todo improve
        if (!empty($this->client)) {
            [$browser, $version] = [$this->client['short_name'] ?? 'unknown', $this->client['version']];

            if ($browser === 'FF' && version_compare($version, '10.0', '<')) {
                $this->addProbability('deprecated_browser', self::DEPRECATED_BROWER);
            }
            else if ($browser === 'CF') {
                $this->addProbability('deprecated_browser', self::DEPRECATED_BROWER);
            }
            else if ($browser === 'HC') {
                $this->addProbability('headless_browser', self::HEADLESS_BROWSER);
            }
            else if (($browser === 'CH' || $browser === 'CR') && version_compare($version, '28.0.0', '<')) {
                $this->addProbability('deprecated_browser', self::DEPRECATED_BROWER);
            }
            else if ($browser === 'IE') {
                if (version_compare($version, '6.0.0', '<')) {
                    $this->addProbability('deprecated_browser', self::HIGHLY_DEPRECATED_BROWSER);
                } else if (version_compare($version, '8.0.0', '<')) {
                    $this->addProbability('deprecated_browser', self::DEPRECATED_BROWER);
                }
            }
            else if ($browser === 'KO') {
                $this->addProbability('deprecated_browser', self::DEPRECATED_BROWER);
            }
            else if ($browser === 'NS') {
                $this->addProbability('deprecated_browser', self::HIGHLY_DEPRECATED_BROWSER);
            }
            else if ($browser === 'unknown') {
                $this->addProbability('unknown_browser', self::UNKNOWN_BROWER);
            }
        } else {
            $this->addProbability('no_browser', self::NO_BROWSER);
        }

        // Operating System
        // @todo improve
        if (!empty($this->os)) {
            [$os, $version] = [$this->os['short_name'] ?? 'unknown', $this->os['version']];

            if ($os === 'unknown') {
                $this->addProbability('unknown_os', self::UNKNOWN_OS);
            }
        } else {
            $this->addProbability('no_os', self::NO_OS);
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
