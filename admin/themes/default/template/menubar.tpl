{combine_script id='jquery.ui' load='async' require='jquery' path='themes/default/js/ui/packed/ui.core.packed.js' }
{combine_script id='jquery.ui.sortable' load='async' require='jquery.ui' path='themes/default/js/ui/packed/ui.sortable.packed.js' }
{footer_script require='jquery.ui.sortable'}
jQuery(document).ready(function(){ldelim}
	jQuery(".menuPos").hide();
	jQuery(".drag_button").show();
	jQuery(".menuLi").css("cursor","move");
	jQuery(".menuUl").sortable({ldelim}
		axis: "y",
		opacity: 0.8
	});
	jQuery("input[name^='hide_']").click(function() {ldelim}
		men = this.name.split('hide_');
		if (this.checked) {ldelim}
			jQuery("#menu_"+men[1]).addClass('menuLi_hidden');
		} else {ldelim}
			jQuery("#menu_"+men[1]).removeClass('menuLi_hidden');
		}
	});
	jQuery("#menuOrdering").submit(function(){ldelim}
		ar = jQuery('.menuUl').sortable('toArray');
		for(i=0;i<ar.length;i++) {ldelim}
			men = ar[i].split('menu_');
			document.getElementsByName('pos_' + men[1])[0].value = i+1;
		}
	});
});
{/footer_script}

<div class="titrePage">
  <h2>{'Menu Management'|translate}</h2>
</div>

<form id="menuOrdering" action="{$F_ACTION}" method="post">
  <ul class="menuUl">
    {foreach from=$blocks item=block name="block_loop"}
    <li class="menuLi {if $block.pos<0}menuLi_hidden{/if}" id="menu_{$block.reg->get_id()}">
      <p>
        <span>
          <strong>{'Hide'|@translate} <input type="checkbox" name="hide_{$block.reg->get_id()}" {if $block.pos<0}checked="checked"{/if}></strong>
        </span>

        <img src="{$themeconf.admin_icon_dir}/cat_move.png" class="button drag_button" style="display:none;" alt="{'Drag to re-order'|@translate}" title="{'Drag to re-order'|@translate}">
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
    <input type="submit" name="submit" value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED}>
    <input type="submit" name="reset" value="{'Reset'|@translate}" {$TAG_INPUT_ENABLED}>
  </p>

</form>
