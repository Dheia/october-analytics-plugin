Simple Analytics for OctoberCMS
===============================

Simple analyse the traffic of your website without relying on an external service. The simple
version of this plugin (look at 'Advanced Analytics for OctoberCMS' for more information) does not
use JavaScript and can be used "Consent-Free" according to the GDPR (See 'Legal Information' below).

**Attention:** Synder.Analytics requires PHP 7.4+!

**Work in Progress:** This plugin already provides a stable but basic functionality, however some
features are not yet included as these must be developed during active use. We release it anyway to
may receive valuable user feedback, which we can directly include in our active development process.


Features
--------

Simple Analytics does not include any JavaScript on your website, all information are collected on
the server-side. While this should actually provide enough data, it is of course not always as
precise as with JavaScript-based solutions.

-   Simple URL and Visit / View counters
    -   ... also usable on your frontend website
-   Simple but limited trace/route -tracking
-   Simple but limited Bot-Detection (Humanity testing)
-   Simple but limited Referrer collection
-   Simple but limited Browser and OS detection (JavaScript-less)
-   A bunch of neat statistics within 4 **configurable** dashboard widgets
-   An own admin page with extended access to the data collections
    -   ... with an Response-Code tracker to find 400 error pages


### Bot-Detection

The Bot-Detection is based on a value counting from 0.1 (should be human) to 5.0 (should be bot)
and is based on the following values (*-marked values must be configured before):

-   User Agent
-   Browser / OS
-	Request Header
-   \* robots.txt
-   \* invisible link

The Advanced Analytics plugin (below) contains additional, Cookie and JavaScript-based, solutions.
You can find more details about each single value and technique in the documentation.


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


Legal Information
-----------------

The Simple Version of this plugin is designed to be used consent-free, since it does NOT use
cookies or store any personal or similar related data in a way that can be traced back to the user.
The user tracking is based on a database-stored hashed value, consisting of the users IP address,
the user agent as well as the application key of your OctoberCMS website. Of course, this reduces
the evaluability of the individual view trace - especially across browsers and devices - but as the
name suggests, this is just a simple analytics system and not Google Analytics or Matomo.

**Attention:** This plugin is designed to comply with the GDPR (European General Data Protection
Regulation), however, from a legal point of view **we cannot give any guarantees**. The use of this
plugin is therefore at your own risk, contact us at october@synder.dev if you need some information
of how this plugin works / collects data.


Copyright & License
-------------------

Synder.Analytics is published under the MIT license.<br />
Copyright © 2021 Synder (info@synder.dev)
