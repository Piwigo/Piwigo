{footer_script require='jquery.ui.sortable'}{literal}
jQuery(document).ready(function() {
  function checkOrderOptions() {
    jQuery("#image_order_user_define_options").hide();
    if (jQuery("input[name=image_order_choice]:checked").val() == "user_define") {
      jQuery("#image_order_user_define_options").show();
    }        
  }

  jQuery('ul.thumbnails').sortable( { 
    revert: true, opacity: 0.7,
    handle: jQuery('.rank-of-image').add('.rank-of-image img'),
    update: function() {
      jQuery(this).find('li').each(function(i) { 
        jQuery(this).find("input[name^=rank_of_image]").each(function() {
          jQuery(this).attr('value', (i+1)*10)
        });
      });

      jQuery('#image_order_rank').attr('checked', true);
      checkOrderOptions();
    }
  });

  jQuery("input[name=image_order_choice]").click(function () {
    checkOrderOptions();
  });

  checkOrderOptions();
});
{/literal}{/footer_script}

<h2>{'Manage photo ranks'|@translate}</h2>

<h3>{$CATEGORIES_NAV}</h3>

<form action="{$F_ACTION}" method="post">
{if !empty($thumbnails)}
  <p><input class="submit" type="submit" value="{'Submit'|@translate}" name="submit"></p>
  <fieldset>
    <legend>{'Manual order'|@translate}</legend>
    {if !empty($thumbnails)}
    <p>{'Drag to re-order'|@translate}</p>
    <ul class="thumbnails">
      {foreach from=$thumbnails item=thumbnail}
      <li class="rank-of-image">
        <div class="clipwrapper">
	  <div class="clip" style="clip:rect({$thumbnail.CLIP_TOP}px {$thumbnail.CLIP_RIGHT}px {$thumbnail.CLIP_BOTTOM}px {$thumbnail.CLIP_LEFT}px);top:-{$thumbnail.CLIP_TOP}px;left:-{$thumbnail.CLIP_LEFT}px">
	    <img src="{$thumbnail.TN_SRC}" class="thumbnail" alt="">
	  </div>
	</div>
        <input type="text" name="rank_of_image[{$thumbnail.ID}]" value="{$thumbnail.RANK}" style="display:none">
      </li>
      {/foreach}
    </ul>
    {/if}
  </fieldset>
{/if}

  <fieldset>
    <legend>{'Sort order'|@translate}</legend>
    <p class="field">
      <input type="radio" name="image_order_choice" id="image_order_default" value="default"{if $image_order_choice=='default'} checked="checked"{/if}>
      <label for="image_order_default">{'Use the default photo sort order (defined in the configuration file)'|@translate}</label>
    </p>
    <p class="field">
      <input type="radio" name="image_order_choice" id="image_order_rank" value="rank"{if $image_order_choice=='rank'} checked="checked"{/if}>
      <label for="image_order_rank">{'manual order'|@translate}</label>
    </p>
    <p class="field">
      <input type="radio" name="image_order_choice" id="image_order_user_define" value="user_define"{if $image_order_choice=='user_define'} checked="checked"{/if}>
      <label for="image_order_user_define">{'automatic order'|@translate}</label>
      <div id="image_order_user_define_options">
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
      </div>
  </fieldset>
  <p>
    <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit">

    <label>
      <input type="checkbox" name="image_order_subcats" id="image_order_subcats">
      {'Apply to sub-albums'|@translate}
    </label>
  </p>
</form>
