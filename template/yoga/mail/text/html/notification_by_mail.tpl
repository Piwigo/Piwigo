<div id="nbm_message">
<h2>{'Notification'|@translate}</h2>
<p>{'nbm_content_hello_1'|@translate}{$USERNAME}{'nbm_content_hello_2'|@translate}</p>

{if isset($subscribe_by_admin)}
<p>{'nbm_content_subscribe_by_admin'|@translate}</p>
{/if}
{if isset($subscribe_by_himself)}
<p>{'nbm_content_subscribe_by_himself'|@translate}</p>
{/if}
{if isset($unsubscribe_by_admin)}
<p>{'nbm_content_unsubscribe_by_admin'|@translate}</p>
{/if}
{if isset($unsubscribe_by_himself)}
<p>{'nbm_content_unsubscribe_by_himself'|@translate}</p>
{/if}
{if isset($content_new_elements_single)}
<p>{'nbm_content_new_elements'|@translate}{'nbm_content_new_elements_single'|@translate}{$content_new_elements_single.DATE_SINGLE}.</p>
{/if}
{if isset($content_new_elements_between)}
<p>{'nbm_content_new_elements'|@translate}{'nbm_content_new_elements_between_1'|@translate}{$content_new_elements_between.DATE_BETWEEN_1}{'nbm_content_new_elements_between_2'|@translate}{$content_new_elements_between.DATE_BETWEEN_2}.</p>
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
<p>{'nbm_content_goto_1'|@translate}<a href="{$GOTO_GALLERY_URL}">{$GOTO_GALLERY_TITLE}</a>{'nbm_content_goto_2'|@translate}</p>
{/if}
<p>{'nbm_content_byebye'|@translate}</p>
<p style="text-align:center">{$SEND_AS_NAME}</p>
<p>
<br><hr>
{'nbm_content_unsubscribe_link'|@translate}{'nbm_content_click_on'|@translate}<a href="{$UNSUBSCRIBE_LINK}">{$UNSUBSCRIBE_LINK}</a><br>
{'nbm_content_subscribe_link'|@translate}{'nbm_content_click_on'|@translate}<a href="{$SUBSCRIBE_LINK}">{$SUBSCRIBE_LINK}</a><br>
{'nbm_content_problem_contact'|@translate}<a href="mailto:{$CONTACT_EMAIL}?subject={'nbm_content_pb_contact_object'|@translate}">{$CONTACT_EMAIL}</a><br>
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
