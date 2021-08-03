Simple Analytics for OctoberCMS
===============================

Simple analyse the traffic of your website without relying on an external service. The simple
version of this plugin (see 'Advanced Analytics for OctoberCMS' for more details) does not use 
JavaScript and can be used "Consent-Free" according to the GDPR (See 'Legal Information' below).

**Attention:** Synder.Analytics requires PHP 7.4+!


Features
--------

Simple Analytics does not include any JavaScript on your website, all information are collected on
the server-side. While this should actually provide enough data, it is of course not always as
precise as with JavaScript-based support.

-   Simple URL and Visit / View counters
    -   ... also usable as Twig variables / filters on your frontend website
-   Simple but limited Bot-Detection (Humanity testing)
-   Simple but limited Referrer collection
-   Simple but limited Browser and OS detection (JavaScript-less)
-   A bunch of graphical statistics within 4 **configurable** dashboard widgets


### Bot-Detection

The Bot-Detection is based on a value counting from 0.1 (should be human) to 5.0 (should be bot)
and is based on the following values (*-marked values must be configured before):

-   User Agent
-   Browser / OS
-	Request Header
-   \* robots.txt trap
-   \* invisible link trap

The Advanced Analytics plugin contains additional, Cookie and JavaScript-based, solutions. You can 
find more details about each single value and technique in the documentation.


### Coming Soon

The following features may be part of a future release.

-   Simple but limited trace/route -tracking
-   An own admin page with extended access to the data collections
    -   ... with an Response-Code tracker to find 400 error pages


Advanced Analytics for OctoberCMS
---------------------------------

We're currently working on an extended version of this plugin, which adds the following features:

1.  Event Monitoring - Define and Monitor custom events on your website
2.  Referral Links - Create referral links to monitor third-party linkings.
3.  Cookie Tracking - A GDPR-compliant but **NOT** Consent-free User Tracking
4.  Consent-Support - Own Consent Banner _or_ support of an consent banner plugin
5.  Extended Collections for referrers, browser & os datas and trace details
6.  JavaScript-supported Humanity-Testing - Extended Bot-evaluation & detection

More may be added during the development. The Advanced Analytics plugin will be available as a paid
addition for this plugin and won't replace the free version.


Requirements
------------

-   PHP 7.4+
-   [matomo/device-detector](https://github.com/matomo-org/device-detector) 4.3+


### Compatible With

The robots.txt honeypot can be used without additional dependencies, but if one of the following 
plugins is found, those functionality will be used instead. 

-   [Arcane.Seo](https://octobercms.com/plugin/arcane-seo)
-   [Mohsin.Txt](https://octobercms.com/plugin/mohsin-txt)
-   [Zen.Robots](https://octobercms.com/plugin/zen-robots)


Legal Information
-----------------

The Simple version of this plugin is designed to be used consent-free, since it does NOT use cookies 
or store personal (or similar related) data in a way that permits traceability. The user tracking 
is based on a daily-changing database-stored hashed value, consisting of the IP address, the user 
agent, the current date as well as the application key of your OctoberCMS website. We're using PHP's
`hash_hmac` function, used with SHA1 as algorithm and the OctoberCMS application key as key.

Of course, this method also affects the quality and reduces the evaluability of the collected data 
and makes it impossible to track a single user across days, browsers or devices. Nevertheless, you 
will get enough usable data without having to obtain the visitor's consent in advance.

**Attention:** This plugin is designed to comply with the GDPR (European General Data Protection
Regulation), however, from a legal point of view **we cannot give any guarantees**. The use of this
plugin is therefore at your own risk, contact us at october@synder.dev if you need some information
or explicit details of how this plugin works.


Copyright & License
-------------------

Synder.Analytics is published under the MIT license.<br />
Copyright © 2021 Synder (info@synder.dev)
