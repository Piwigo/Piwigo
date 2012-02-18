{if isset($errors)}
<div class="ui-bar ui-bar-e errors">
  <h3>{'Error'|@translate}</h3>
	<div><a href="#" data-role="button" data-icon="delete" data-iconpos="notext" class="close-button">Button</a></div>
	<p>{'<br>'|@implode:$errors}</p>
</div>
{/if}

{if not empty($infos)}
<div class="ui-bar ui-bar-b infos">
  <h3>{'Info'|@translate}</h3>
	<div><a href="#" data-role="button" data-icon="delete" data-iconpos="notext" class="close-button">Button</a></div>
	<p>{'<br>'|@implode:$infos}</p>
</div>
{/if}

{footer_script}{literal}
$(document).ready(function () {
  $('.close-button').click(function() {
    $(this).parents('.ui-bar').remove();
  });
});
{/literal}{/footer_script}