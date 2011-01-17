<div id="nbm_message">
<h2>{'Notification'|@translate}</h2>
<p>{'Hello'|@translate} {$USERNAME},</p>

{if isset($subscribe_by_admin)}
<p>{'The webmaster has subscribed you to receiving notifications by mail.'|@translate}</p>
{/if}
{if isset($subscribe_by_himself)}
<p>{'You have subscribed to receiving notifications by mail.'|@translate}</p>
{/if}
{if isset($unsubscribe_by_admin)}
<p>{'The webmaster has unsubscribed you from receiving notifications by mail.'|@translate}</p>
{/if}
{if isset($unsubscribe_by_himself)}
<p>{'You have unsubscribed from receiving notifications by mail.'|@translate}</p>
{/if}
{if isset($content_new_elements_single)}
<p>{'New photos were added'|@translate} {'on'|@translate} {$content_new_elements_single.DATE_SINGLE}.</p>
{/if}
{if isset($content_new_elements_between)}
<p>{'New photos were added'|@translate} {'between'|@translate} {$content_new_elements_between.DATE_BETWEEN_1} {'and'|@translate} {$content_new_elements_between.DATE_BETWEEN_2}.</p>
{/if}

{if not empty($global_new_lines)}
<ul id="nbm_new_line">
{foreach from=$global_new_lines item=line}
  <li>{$line}</li>
{/foreach}
</ul>
{/if}

{if not empty($custom_mail_content)}
<p>{$custom_mail_content}</p>
{/if}

{if not empty($GOTO_GALLERY_TITLE)}
<p>{'Go to'|@translate} <a href="{$GOTO_GALLERY_URL}">{$GOTO_GALLERY_TITLE}</a>.</p>
{/if}
<p>{'See you soon,'|@translate}</p>
<p style="text-align:center">{$SEND_AS_NAME}</p>
<p>
<br><hr>
{'To unsubscribe'|@translate}{', click on'|@translate} <a href="{$UNSUBSCRIBE_LINK}">{$UNSUBSCRIBE_LINK}</a><br>
{'To subscribe'|@translate}{', click on'|@translate} <a href="{$SUBSCRIBE_LINK}">{$SUBSCRIBE_LINK}</a><br>
{'If you encounter problems or have any question, please send a message to'|@translate} <a href="mailto:{$CONTACT_EMAIL}?subject={'[NBM] Problems or questions'|@translate}">{$CONTACT_EMAIL}</a><br>
<hr><br>
</p>
{if not empty($recent_posts)}
</div>
<div id="nbm_recent_post">
  {foreach from=$recent_posts item=recent_post }
  <h2>{$recent_post.TITLE}</h2>
  {$recent_post.HTML_DATA}
  {/foreach}
{/if}
</div>
