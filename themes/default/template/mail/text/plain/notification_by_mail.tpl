{'Hello'|@translate} {$USERNAME},

{if isset($subscribe_by_admin)}
{'The webmaster has subscribed you to receiving notifications by mail.'|@translate}
{/if}
{if isset($subscribe_by_himself)}
{'You have subscribed to receiving notifications by mail.'|@translate}
{/if}
{if isset($unsubscribe_by_admin)}
{'The webmaster has unsubscribed you from receiving notifications by mail.'|@translate}
{/if}
{if isset($unsubscribe_by_himself)}
{'You have unsubscribed from receiving notifications by mail.'|@translate}
{/if}
{if isset($content_new_elements_single)}
{'New elements were added '|@translate} {'on'|@translate} {$content_new_elements_single.DATE_SINGLE}.
{/if}
{if isset($content_new_elements_between)}
{'New elements were added '|@translate} {'between'|@translate} {$content_new_elements_between.DATE_BETWEEN_1} {'and'|@translate} {$content_new_elements_between.DATE_BETWEEN_2}.
{/if}
{if not empty($global_new_lines)}
{foreach from=$global_new_lines item=line}
  o {$line}
{/foreach}
{/if}
{if not empty($custom_mail_content)}
{$custom_mail_content}
{/if}
{if not empty($GOTO_GALLERY_TITLE)}
.{$GOTO_GALLERY_TITLE} {$GOTO_GALLERY_URL} .
{/if}

{'See you soon,'|@translate}
  {$SEND_AS_NAME}

______________________________________________________________________________

{'To unsubscribe'|@translate}{', click on'|@translate} {$UNSUBSCRIBE_LINK}
{'To subscribe'|@translate}{', click on'|@translate} {$SUBSCRIBE_LINK}
{'If you encounter problems or have any question, please send a message to'|@translate} {$CONTACT_EMAIL}
______________________________________________________________________________
