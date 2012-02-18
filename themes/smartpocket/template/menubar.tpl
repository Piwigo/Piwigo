<select name="identification" id="identification" data-icon="grid" data-iconpos="notext" data-native-menu="false">

  <option></option>
  <optgroup label="{'Identification'|@translate}">

	{if isset($U_REGISTER)}
	<option value="{$U_REGISTER}">{'Register'|@translate}</option>
	{/if}
	{if isset($U_LOGIN)}
	<option value="{$U_LOGIN}">{'Login'|@translate}</option>
	{/if}
	{if isset($U_LOGOUT)}
	<option value="{$U_LOGOUT}">{'Logout'|@translate}</option>
	{/if}
	{if isset($U_PROFILE)}
	<option value="{$U_PROFILE}">{'Customize'|@translate}</option>
	{/if}
	{if isset($U_ADMIN)}
	<option value="{$U_ADMIN}">{'Administration'|@translate}</option>
	{/if}

  </optgroup>

</select>

{footer_script}{literal}
$(document).ready(function() {
  $('#identification').change(function() {
    window.location = this.value;
  });
});
{/literal}{/footer_script}
