{combine_script id='jquery.ui' load='async' require='jquery' path='themes/default/js/ui/packed/ui.core.packed.js' }
{combine_script id='jquery.ui.sortable' load='async' require='jquery.ui' path='themes/default/js/ui/packed/ui.sortable.packed.js' }
{footer_script require='jquery.ui.sortable'}{literal}
jQuery(document).ready(function() {
	jQuery('ul.thumbnails').sortable( { 
		revert: true,	opacity: 0.7,
		handle: $('.rank-of-image').add('.rank-of-image img'),
		update: function() {
			$(this).find('li').each(function(i) { 
			$(this).find("input[name^=rank_of_image]")
			.each(function() { $(this).attr('value', (i+1)*10)});
			});
			$('#image_order_rank').attr('checked', true);
			}
	});
});
{/literal}{/footer_script}

<h2>{'Manage photo ranks'|@translate}</h2>

<h3>{$CATEGORIES_NAV}</h3>

{if !empty($thumbnails)}
<form action="{$F_ACTION}" method="post">
  <p><input class="submit" type="submit" value="{'Submit'|@translate}" name="submit"></p>
  <fieldset>
    <legend>{'Edit ranks'|@translate}</legend>
    {if !empty($thumbnails)}
    <ul class="thumbnails">
      {foreach from=$thumbnails item=thumbnail}
      <li class="rank-of-image">
        <div class="clipwrapper">
	  <div class="clip" style="clip:rect({$thumbnail.CLIP_TOP}px {$thumbnail.CLIP_RIGHT}px {$thumbnail.CLIP_BOTTOM}px {$thumbnail.CLIP_LEFT}px);top:-{$thumbnail.CLIP_TOP}px;left:-{$thumbnail.CLIP_LEFT}px">
	    <img src="{$thumbnail.TN_SRC}" class="thumbnail" alt="">
	  </div>
	</div>
        <input type="text" name="rank_of_image[{$thumbnail.ID}]" value="{$thumbnail.RANK}">
      </li>
      {/foreach}
    </ul>
    {/if}
  </fieldset>

  <fieldset>
    <legend>{'Sort order'|@translate}</legend>
    <p class="field">
      <input type="radio" name="image_order_choice" id="image_order_default" value="default"{if $image_order_choice=='default'} checked="checked"{/if}>
      <label for="image_order_default">{'Use the default photo sort order (defined in the configuration file)'|@translate}</label>
    </p>
    <p class="field">
      <input type="radio" name="image_order_choice" id="image_order_rank" value="rank"{if $image_order_choice=='rank'} checked="checked"{/if}>
      <label for="image_order_rank">{'By rank'|@translate}</label>
    </p>
    <p class="field">
      <input type="radio" name="image_order_choice" id="image_order_user_define" value="user_define"{if $image_order_choice=='user_define'} checked="checked"{/if}>
      <label for="image_order_user_define">{'Manual order'|@translate}</label>
      {foreach from=$image_orders item=order}
      <p class="field">
        <select name="order_field_{$order.ID}">
          {html_options options=$image_order_field_options selected=$order.FIELD }
        </select>
        <select name="order_direction_{$order.ID}">
          {html_options options=$image_order_direction_options selected=$order.DIRECTION }
        </select>      
      </p>
      {/foreach}
  </fieldset>
  <p><input class="submit" type="submit" value="{'Submit'|@translate}" name="submit"></p>
</form>


{else}
<div class="infos"><p>{'No photo in this album'|@translate}</p></div>
{/if}
