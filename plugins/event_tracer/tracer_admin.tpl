<div class="titrePage">
  <h2>Event Tracer</h2>
</div>

<p>
The event tracer is a developer tool that logs in the footer of the window all calls to trigger_event method.
You can use this plugin to see what events is Piwigo calling.
<b>Note that $conf['show_queries'] must be true.</b>
</p>
<form method="post" action="" class="general">
<fieldset>
	<legend>Event Tracer</legend>

<label>Show event arguments
	<input type="checkbox" name="eventTracer_show_args" {$EVENT_TRACER_SHOW_ARGS} />
</label>

<br/>

<label>Fill below a list of regular expressions (one per line).
An event will be logged if its name matches at least one expression in the list.
	<textarea name="eventTracer_filters" id="eventTracer_filters"rows="10" cols="80">{$EVENT_TRACER_FILTERS}</textarea>
</label>

<br/>

<label>Show all registered handlers
	<input type="checkbox" name="eventTracer_show_registered" {$EVENT_TRACER_SHOW_REGISTERED} />
</label>

</fieldset>

<p><input class="submit" type="submit" value="Submit" /></p>

<p><a href="{$U_LIST_EVENTS}">Click here to see a complete list of actions and events trigered by this PWG version</a>.</p>
</form>
