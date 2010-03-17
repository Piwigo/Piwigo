{known_script id="jquery" src=$ROOT_URL|@cat:"themes/default/js/jquery.packed.js"}
{known_script id="jquery.ui" src=$ROOT_URL|@cat:"themes/default/js/ui/packed/ui.core.packed.js" }
{known_script id="jquery.ui.sortable" src=$ROOT_URL|@cat:"themes/default/js/ui/packed/ui.sortable.packed.js" }

{literal}
<script type="text/javascript">
  $(function() {
  $('ul.thumbnails')
  .sortable(
  { revert: true,
  opacity: 0.7,
  handle: $('.rank-of-image').add('.rank-of-image img'),
  update: function() {
  $(this).find('li').each(function(i) { 
  $(this).find("input[name^=rank_of_image]")
  .each(function() { $(this).attr('value', (i+1)*10)});
  });
  }
  });
  });

</script>
{/literal}

<h2>{'Manage image ranks'|@translate}</h2>

<h3>{$CATEGORIES_NAV}</h3>

{if !empty($thumbnails)}
<form action="{$F_ACTION}" method="post">
  <p><input class="submit" type="submit" value="{'Submit'|@translate}" name="submit" {$TAG_INPUT_ENABLED}></p>
  <fieldset>
    <legend>{'Edit ranks'|@translate}</legend>
    {if !empty($thumbnails)}
    <ul class="thumbnails">
      {foreach from=$thumbnails item=thumbnail}
      <li  class="rank-of-image">
	<img src="{$thumbnail.TN_SRC}" class="thumbnail" alt="">
        <input type="text" name="rank_of_image[{$thumbnail.ID}]" value="{$thumbnail.RANK}">
      </li>
      {/foreach}
    </ul>
    {/if}
  </fieldset>
  <p><input class="submit" type="submit" value="{'Submit'|@translate}" name="submit" {$TAG_INPUT_ENABLED}></p>
</form>

{else}
<div class="infos"><p>{'No element in this category'|@translate}</p></div>
{/if}
