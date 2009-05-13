{* $Id: /piwigo/trunk/template/yoga/mail/text/plain/notification_by_mail.tpl 6468 2008-09-30T21:14:16.664623Z rub  $ *}
{'nbm_content_hello_1'|@translate}{$USERNAME}{'nbm_content_hello_2'|@translate}

{if isset($subscribe_by_admin)}
{'nbm_content_subscribe_by_admin'|@translate}
{/if}
{if isset($subscribe_by_himself)}
{'nbm_content_subscribe_by_himself'|@translate}
{/if}
{if isset($unsubscribe_by_admin)}
{'nbm_content_unsubscribe_by_admin'|@translate}
{/if}
{if isset($unsubscribe_by_himself)}
{'nbm_content_unsubscribe_by_himself'|@translate}
{/if}
{if isset($content_new_elements_single)}
{'nbm_content_new_elements'|@translate}{'nbm_content_new_elements_single'|@translate}{$content_new_elements_single.DATE_SINGLE}.
{/if}
{if isset($content_new_elements_between)}
{'nbm_content_new_elements'|@translate}{'nbm_content_new_elements_between_1'|@translate}{$content_new_elements_between.DATE_BETWEEN_1}{'nbm_content_new_elements_between_2'|@translate}{$content_new_elements_between.DATE_BETWEEN_2}.
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
{'nbm_content_goto_1'|@translate}{$GOTO_GALLERY_TITLE} {$GOTO_GALLERY_URL} {'nbm_content_goto_2'|@translate}
{/if}

{'nbm_content_byebye'|@translate}
  {$SEND_AS_NAME}

______________________________________________________________________________

{'nbm_content_unsubscribe_link'|@translate}{'nbm_content_click_on'|@translate}{$UNSUBSCRIBE_LINK}
{'nbm_content_subscribe_link'|@translate}{'nbm_content_click_on'|@translate}{$SUBSCRIBE_LINK}
{'nbm_content_problem_contact'|@translate}{$CONTACT_EMAIL}
______________________________________________________________________________
