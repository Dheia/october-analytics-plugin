Changelog
=========

Version 1.1.2
-------------
-   Bugfix: Visitor -> Bot Probability bug

Version 1.1.1
-------------
-   Update: Use default values as placeholder on simple statistics widget.
-   Bugfix: Wrong use of Accessors (Laravel).

Version 1.1.0
-------------
-   Add: New settings page to configure the Synder Simple Analytics plugin.
-   Add: New chart configuration properties (accessible on the widget itself).
-   Add: New Bot probability feature to filter requests made by non-humans.
-   Add: New Robots.txt and invisible link traps.
-   Add: New 'synder-slider' formwidget, used to configure Bot probability filter.
-   Add: New 'hide' column on `synder_analytics` to hide pages and those requests.
-   Add: New 'browser' and 'os' columns on `synder_analytics_visitors` to skip evaluation on each (re-) load.
-   Add: Global option to filter backend users.
-   Add: Global option to format the date/time output format.
-   Add: Allow to toggle each single line on the basic statistics widget.
-   Add: Option to change the single line colors on the basic statistics widget.
-   Add: Option to toggle the today / days numeric counter on the basic statistics widget.
-   Add: Option to toggle the chart legend on the system / os widget.
-   Add: Option to define the timeperiod on top-referrers and top-pages widgets.
-   Add: Option to define the amount of shown items on top-referrers and top-pages widgets.
-   Add: The new Twig Filter `synderviews` and `syndervisits` to receive these counter per URL / path.
-   Add: Own Middleware methods for robots.txt and invisible link handling.
-   Add: robots.txt Support for [Arcane.Seo](https://octobercms.com/plugin/arcane-seo) plugin.
-   Add: robots.txt Support for [Mohsin.Txt](https://octobercms.com/plugin/mohsin-txt) plugin.
-   Add: robots.txt Support for [Zen.Robots](https://octobercms.com/plugin/zen-robots) plugin.
-   Add: Invisible Link injection on the Twig process event.
-   Add: Scheduled Task for Robots and Invisible Link re-generation.
-   Add: German Locale / Language.
-   Update: Don't skip favicon.ico, rather hide favicon.ico and similar requests per default.
-   Bugfix: Duplicated tick for the first counted date on basic statistics widget.
-   Bugfix: Missing Date-Tick on basic statistics widget.

Version 1.0.2
-------------
-   Update: The query used for the chart on the Top-Pages widget.
-   Bugfix: Forced height on the chart-legend of the browser / os widget.
-   Bugfix: Duplicated tick for the first counted date on basic statistics widget.
-   Bugfix: Small details and errors has been fixed.

Version 1.0.1
-------------
-   Update: Skip logs for favicon.ico file.
-   Update: Add centered counter number on os/browser charts.
-   Bugfix: Error / Exception due to empty user agent.
-   Bugfix: Error / Exception due to missing browser and us agent details.
-   Bugfix: Small details and errors has been fixed.

Version 1.0.0
-------------
-   Initial Release
