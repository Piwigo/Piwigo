{$CONTENT}

{if isset($TECHNICAL)}
-----------------------------
{'Connected user: %s'|translate:$TECHNICAL.username}
{'IP: %s'|translate:$TECHNICAL.ip}
{'Browser: %s'|translate:$TECHNICAL.user_agent}
{/if}