<h3>{'Identification'|@translate}</h3>
<ul data-role="listview">
  {if isset($U_REGISTER)}<li><a href="{$U_REGISTER}">{'Register'|@translate}</a></li>{/if}
  {if isset($U_LOGIN)}<li><a href="{$U_LOGIN}">{'Logout'|@translate}</a></li>{/if}
  {if isset($U_LOGOUT)}<li><a href="{$U_LOGOUT}">{'Logout'|@translate}</a></li>{/if}
  {if isset($U_PROFILE)}<li><a href="{$U_PROFILE}">{'Customize'|@translate}</a></li>{/if}
  {if isset($U_ADMIN)}<li><a href="{$U_ADMIN}">{'Administration'|@translate}</a></li>{/if}
</ul>
