{* $Id$ *}
<ul class="thumbnailCategories">
{foreach from=$comments item=comment name=comment_loop}
<li>
  <div class="thumbnailCategory {if $smarty.foreach.comment_loop.index is odd}odd{else}even{/if}">
    {if isset($comment.TN_SRC)}
    <div class="illustration">
      <a href="{$comment.U_PICTURE}">
        <img src="{$comment.TN_SRC}" alt="{$comment.ALT}">
      </a>
    </div>
    {/if}
    <div class="description" style="height:{if ($comment.IN_EDIT==1)}200{/if}px">
      {if isset($comment.U_DELETE) or isset($comment.U_VALIDATE) or isset($comment.U_EDIT) }
      <ul class="actions" style="float:right">
        {if isset($comment.U_DELETE)}
        <li>
          <a href="{$comment.U_DELETE}" title="{'delete this comment'|@translate}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');">
            <img src="{$ROOT_URL}{$themeconf.icon_dir}/delete.png" class="button" alt="[delete]">
          </a>
        </li>
        {/if}
        {if isset($comment.U_EDIT) and ($comment.IN_EDIT!=1)}
        <li>
          <a class="editComment" href="{$comment.U_EDIT}#edit_comment" title="{'edit this comment'|@translate}">
            <img src="{$ROOT_URL}{$themeconf.icon_dir}/edit.png" class="button" alt="[edit]">
          </a>
        </li>
        {/if}
        {if isset($comment.U_VALIDATE)}
        <li>
          <a href="{$comment.U_VALIDATE}" title="validate this comment">
            <img src="{$ROOT_URL}{$themeconf.icon_dir}/validate_s.png" class="button" alt="[validate]">
          </a>
        </li>
        {/if}
      </ul>
      {/if}
      <span class="author">{$comment.AUTHOR}</span> - <span class="date">{$comment.DATE}</span>
      {if ($comment.IN_EDIT==1)}
      <a name="edit_comment"></a>
      <form  method="post" action="{$comment.U_EDIT}" class="filter" id="editComment">
	<fieldset>
	  <legend>{'Edit a comment'|@translate}</legend>
	  <label>{'comment'|@translate}<textarea name="content" id="contenteditid" rows="5" cols="80">{$comment.CONTENT|@escape}</textarea></label>
	  <input type="hidden" name="key" value="{$comment.KEY}">
	  <input type="hidden" name="image_id" value="{$comment.IMAGE_ID|@default:$current.id}">
	  <input class="submit" type="submit" value="{'Submit'|@translate}">
	</fieldset>
      </form>
      {else}      
      <blockquote>{$comment.CONTENT}</blockquote>
      {/if}
    </div>
  </div>
</li>
{/foreach}
</ul>
