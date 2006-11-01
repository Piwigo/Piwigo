<p>
The event tracer is a developer tool that logs in the footer of the window all calls to trigger_event method.
You can use this plugin to see what events is PhpWebGallery calling.
<b>Note that $conf['show_queries'] must be true.</b>
</p>
<form method="post" action="{EVENT_TRACER_F_ACTION}" class="general">
<fieldset>
	<legend>Event Tracer</legend>

<label>Show event argument
	<input type="checkbox" name="eventTracer_show_args" {EVENT_TRACER_SHOW_ARGS} />
</label>
<br/>
<label>Fill below a list of regular expressions (one per line).
An event will be logged if its name matches at least one expression in the list.
	<textarea name="eventTracer_filters" id="eventTracer_filters"rows="10" cols="80">{EVENT_TRACER_FILTERS}</textarea>
</label>

</fieldset>

<p><input type="submit" value="Submit" /></p>
</form>
