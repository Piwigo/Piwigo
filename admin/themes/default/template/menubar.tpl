{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{footer_script require='jquery.ui.sortable'}{literal}
jQuery(document).ready(function(){
	jQuery(".menuPos").hide();
	jQuery(".drag_button").show();
	jQuery(".menuLi").css("cursor","move");
	jQuery(".menuUl").sortable({
		axis: "y",
		opacity: 0.8
	});
	jQuery("input[name^='hide_']").click(function() {
		men = this.name.split('hide_');
		if (this.checked) {
			jQuery("#menu_"+men[1]).addClass('menuLi_hidden');
		} else {
			jQuery("#menu_"+men[1]).removeClass('menuLi_hidden');
		}
	});
	jQuery("#menuOrdering").submit(function(){
		ar = jQuery('.menuUl').sortable('toArray');
		for(i=0;i<ar.length;i++) {
			men = ar[i].split('menu_');
			document.getElementsByName('pos_' + men[1])[0].value = i+1;
		}
	});
});
{/literal}{/footer_script}

{html_style}
.font-checkbox i {
  margin-left:5px;
}
{/html_style}

<form id="menuOrdering" action="{$F_ACTION}" method="post">
  <ul class="menuUl">
    {foreach from=$blocks item=block name="block_loop"}
    <li class="menuLi {if $block.pos<0}menuLi_hidden{/if}" id="menu_{$block.reg->get_id()}">
      <p>
        <span>
          <label class="font-checkbox"><strong>{'Hide'|@translate}</strong><i class="icon-check"></i><input type="checkbox" name="hide_{$block.reg->get_id()}" {if $block.pos<0}checked="checked"{/if}></label>
        </span>

        <img src="{$themeconf.admin_icon_dir}/cat_move.png" class="drag_button" style="display:none;" alt="{'Drag to re-order'|@translate}" title="{'Drag to re-order'|@translate}">
        <strong>{$block.reg->get_name()|@translate}</strong> ({$block.reg->get_id()})
      </p>

      {if $block.reg->get_owner() != 'piwigo'}
      <p class="menuAuthor">
        {'Author'|@translate}: <i>{$block.reg->get_owner()}</i>
      </p>
      {/if}

      <p class="menuPos">
        <label>
          {'Position'|@translate} :
          <input type="text" size="4" name="pos_{$block.reg->get_id()}" maxlength="4" value="{math equation="abs(pos)" pos=$block.pos}">
        </label>
      </p>
    </li>
    {/foreach}
  </ul>
  <p class="menuSubmit">
    <button name="submit" type="submit" class="buttonLike" {if $isWebmaster != 1}disabled{/if}>
      <i class="icon-floppy"></i> {'Save Settings'|@translate}
    </button>
  </p>

</form>
