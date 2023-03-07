Documentation
=============

How can I use the views and visits in my template?
--------------------------------------------------

The global `page` property, which is available natively on each page, receives a new `synderstats` array,
which contains the following values:

- `synderstats.views` - The 'View' counter of all time for the current page
- `synderstats.visits` - The 'Visit' counter of all time for the current page

Additionally, you can also use the Twig filters `synderviews` or `syndervisits` to receive the view or
visit counter according to the passed path (similar to the native `app` filter).

You can access both of them like shown below:

```
<div class="current-page-stats">
	Current Views: {{ this.page.synderstats.views }}
	Current Visits: {{ this.page.synderstats.visits }}
</div>

<div class="another-page-stats">
	Views from another Page: {{ '/another-page'|synderviews }}
	Visits from another Page: {{ '/another-page'|syndervisits }}
</div>
```


What is the difference between View and Visit?
----------------------------------------------

In the context of this plugin a `View` is the general counter, so each time a URL is called a view
will be counted. A `Visit` on the other hand is a filtered value and counts only once per session,
per day and of course per URL.

The Simple Analytics plugin is not able to detect and track so-called `Unique Visits`, which counts
and collects data directly to the user and also beyond different sessions or devices. Therefore,
we call it only `Visits` and simply refer to the browser session instead of the user itself. However,
this allows us to create a cookie and consent-free Analytics tools, at least for legal use in the EU.


How does the Bot Protection work?
---------------------------------

Nowadays it is almost impossible to determine exactly whether a call was made by a user or a bot,
since many bots pretend to be people and some users pretend to be bots (in order not to be tracked).
Thus, we're using multiple procieedings to calculate a probability:

**User Agents** Good bots, like Crawlers or cURL-interfaces, reveal themselves by using special User
Agents, such as 'Googlebot/2.1' or 'python-requests'. However, some people do the same with the aim of
exploiting precisely such mechanisms, but since the proportion of these people is very low we count
this value very high, at least to exclude bots.

**Browser / OS versions** Highly outdated browsers (such as IE 1 up to 7 or Netscape) and operating
systems (such as Windows 3.1 or Windows 2000) are often used or pretend to be used by bots but also
very rarely by humans (like doctors). We receive this information from the user agent on the Simple
Analytics version, and with JavaScript support on the Advanced Analytics version.

**Request Header** The Simple Analytics plugin also scans the received Request Headers for typical
entries, such as DNT and similar ones, which are most-likely set by real humans or explicitly used
by specific bots. Of course, this value is not high ranked, since many bots are smart enough to add
known headers as well.

**robots.txt** The robots.txt file contains access and index rules for search-engine crawler bots,
such as Google, and allows us to define URLs which aren't called by such ones. However, some bad bots
look explicitly for such URLs and will access them, which will lead them to a generated honeypot page.
In our experience this technique is no longer quite as successful as it was in the past, but it still
frees our statistics from outdated bot engines.

**Invisible Link** An invisible link works similar to the robots.txt technique, but places the honeypot
URL to the footer of your website. While human users will not see this link, many crawler bots will.
This technique is definitely more effective then using the robots.txt, but of course, it doesn't filter
everything either.


What does a bot probability of 0.0 mean, when 0.1 is the initial value?
-----------------------------------------------------------------------

The value 0.0 means, that the bot probability could not been detected, either because not enough data
are available to allow an evaluation yet, or because you enabled the lazy evaluation option and use the
bot probability value outside of the backend statistics pages. Keep in mind: The Lazy evaluation option
disables the probability calculation, when accessed on the frontend, to not impact the performance.


Why does the bot probability value change afterwards?
-----------------------------------------------------

The bot probability value consists of multiple values and behaviours some of these can only be recorded
and fulfilled retrospectively or refers to a steadily growing collection of data. To provide as natural
data as possible, the system updates the bot probability with each added and received value.
