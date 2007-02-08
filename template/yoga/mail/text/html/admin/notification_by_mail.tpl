<div id="nbm_mail_content">
<h2>{lang:Notification}</h2>
<p>{lang:nbm_content_hello_1}{USERNAME}{lang:nbm_content_hello_2}</p>

<!-- BEGIN subscribe_by_admin -->
<p>{lang:nbm_content_subscribe_by_admin}</p>
<!-- END subscribe_by_admin -->
<!-- BEGIN subscribe_by_himself -->
<p>{lang:nbm_content_subscribe_by_himself}</p>
<!-- END subscribe_by_himself -->
<!-- BEGIN unsubscribe_by_admin -->
<p>{lang:nbm_content_unsubscribe_by_admin}</p>
<!-- END unsubscribe_by_admin -->
<!-- BEGIN unsubscribe_by_himself -->
<p>{lang:nbm_content_unsubscribe_by_himself}</p>
<!-- END unsubscribe_by_himself -->
<!-- BEGIN content_new_elements_single -->
<p>{lang:nbm_content_new_elements}{lang:nbm_content_new_elements_single}{content_new_elements_single.DATE_SINGLE}{content_new_elements_single.END_PUNCT}</p>
<!-- END content_new_elements_single -->
<!-- BEGIN content_new_elements_between -->
<p>{lang:nbm_content_new_elements}{lang:nbm_content_new_elements_between_1}{content_new_elements_between.DATE_BETWEEN_1}{lang:nbm_content_new_elements_between_2}{content_new_elements_between.DATE_BETWEEN_2}{content_new_elements_between.END_PUNCT}</p>
<!-- END content_new_elements_between -->
<!-- BEGIN global_new_line -->
<ul id="nbm_new_line">
  <!-- BEGIN new_line -->
  <li>{global_new_line.new_line.DATA}</li>
  <!-- END new_line -->
</ul>
<!-- END global_new_line -->
<!-- BEGIN custom -->
<p>{custom.CUSTOMIZE_MAIL_CONTENT}</p>
<!-- END custom -->
<!-- BEGIN goto -->
<p>{lang:nbm_content_goto_1}<a href="{goto.GALLERY_URL}">{goto.GALLERY_TITLE}</a>{lang:nbm_content_goto_2}</p>
<!-- END goto -->
<p>{lang:nbm_content_byebye}</p>
<p ALIGN=center>{SEND_AS_NAME}</p>
<p>
<br/><hr>
{lang:nbm_content_unsubscribe_link}{lang:nbm_content_click_on}<a href="{UNSUBSCRIBE_LINK}">{UNSUBSCRIBE_LINK}</a><br/>
{lang:nbm_content_subscribe_link}{lang:nbm_content_click_on}<a href="{SUBSCRIBE_LINK}">{SUBSCRIBE_LINK}</a><br/>
{lang:nbm_content_problem_contact}<a href="mailto:{CONTACT_EMAIL}?subject={lang:nbm_content_pb_contact_object}">{CONTACT_EMAIL}</a><br/>
<hr><br/>
</p>
<!-- BEGIN recent_post -->
</div>
</div>
<div id="content">
<div id="nbm_mail_recent_post">
  <!-- BEGIN recent_post_block -->
  <h2>{recent_post.recent_post_block.TITLE}</h2>
  {recent_post.recent_post_block.HTML_DATA}
  <!-- END recent_post_block -->
<!-- END recent_post -->
</div>
