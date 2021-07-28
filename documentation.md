Documentation
=============

What is the difference between View and Visit?
----------------------------------------------

In the context of this plugin a `View` is the general counter, so each time a URL 
is called a view will be counted. A `Visit` on the other hand is a filtered value 
and counts only once per user, per day and of course per URL.

We call it "Visits" and not "Unique Visitors", since the Simple Analytics plugin 
is not able to detect unique visitors at all, but only new created sessions. This
means, if a user closes and re-opens the browser (or use another browser or device 
at all) we aren't able to detect and evaluate this.

Keep this in mind, when scrolling through your statistics!


What does a bot probability of 0.0 mean, when 0.1 is the initial value?
-----------------------------------------------------------------------

It just means, that we don't have enough data to evaluate the humanity of the visitor.
The bot probability is based on data, which can be modified and blurred by the user, 
such as the user agent or the referrer. Our plugin is designed to rely on at least 2 
details to start evaluating, if even this is not present, the value will set to 0.0 
- so not evaluable.


How can I use the views and visits in my template?
--------------------------------------------------

Both counter values are injected into any page, claiming the 'synderstats' variable. 
This variable contains an array of the following values:

- `synderstats.views` - The 'View' counter of all time
- `synderstats.visits` - The 'Visit' counter of all time

You can access them using twig like shown below:

```
<div class="post">
	
	<!-- Your Post Content -->
	
	<div class="post-stats">
		{{ this.page.synderstats.views }}
		{{ this.page.synderstats.visits }}
	</div>
</div>
```
