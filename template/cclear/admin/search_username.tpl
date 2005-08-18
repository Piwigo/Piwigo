<script language="javascript" type="text/javascript">
<!--
function refresh_username(selected_username)
{
	opener.document.forms['post'].username.value = selected_username;
	opener.focus();
	window.close();
}
//-->
</script>
<div class="titrePage">{L_SEARCH_USERNAME}</div>
<form method="post" name="search" action="{F_SEARCH_ACTION}">
  <input type="text" name="search_username" value="{USERNAME}" class="post" />&nbsp; 
  <input type="submit" name="search" value="{L_SEARCH}" class="bouton" /><br />
  <span class="gensmall">{L_SEARCH_EXPLAIN}</span><br />
  <!-- BEGIN switch_select_name -->
  <span class="genmed">{L_UPDATE_USERNAME}<br />
  <select name="username_list">{switch_select_name.F_USERNAME_OPTIONS}</select>&nbsp; 
  <input type="submit" class="bouton" onClick="refresh_username(this.form.username_list.options[this.form.username_list.selectedIndex].value);return false;" name="use" value="{L_SELECT}" /></span><br />
  <!-- END switch_select_name -->
  <br /><a href="javascript:window.close();" class="adminMenu">{L_CLOSE_WINDOW}</a>
</form>
