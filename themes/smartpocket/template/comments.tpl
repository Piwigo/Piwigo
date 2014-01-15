{include file='infos_errors.tpl'}
<div data-role="content">

{if isset($comments)}
	{include file='comment_list.tpl' comment_derivative_params=$derivative_params}
{/if}

</div> <!-- content -->

