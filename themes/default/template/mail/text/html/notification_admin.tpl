{$CONTENT}

{if isset($TECHNICAL)}
<p style="padding-top:10px;font-size:11px;">
{'Connected user: %s'|translate:$TECHNICAL.username}<br>
{'IP: %s'|translate:$TECHNICAL.ip}<br>
{'Browser: %s'|translate:$TECHNICAL.user_agent}
</p>
{/if}