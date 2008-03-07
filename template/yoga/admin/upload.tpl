{* $Id$ *}
<div class="titrePage">
  <h2>{'waiting'|@translate} {$TABSHEET_TITLE}</h2>
  {$TABSHEET}
</div>

<h3>{'title_upload'|@translate}</h3>

<form action="{$F_ACTION}" method="post" id="waiting">
  <table style="width:99%;" >
    <tr class="throw">
      <th style="width:20%;">{'category'|@translate}</th>
      <th style="width:20%;">{'date'|@translate}</th>
      <th style="width:20%;">{'file'|@translate}</th>
      <th style="width:20%;">{'thumbnail'|@translate}</th>
      <th style="width:20%;">{'Author'|@translate}</th>
      <th style="width:1px;">&nbsp;</th>
    </tr>
    
    {if not empty($pictures) }
    {foreach from=$pictures item=picture name=picture_loop}
    <tr class="{if $smarty.foreach.picture_loop.index is odd}row1{else}row2{/if}">
      <td style="white-space:nowrap;">{$picture.CATEGORY_IMG}</td>
      <td style="white-space:nowrap;">{$picture.DATE_IMG}</td>
      <td style="white-space:nowrap;">
        <a href="{$picture.PREVIEW_URL_IMG}" title="{$picture.FILE_TITLE}">{$picture.FILE_IMG}</a>
      </td>
      <td style="white-space:nowrap;">
        {if not empty($picture.thumbnail) }
        <a href="{$picture.thumbnail.PREVIEW_URL_TN_IMG}" title="{$picture.thumbnail.FILE_TN_TITLE}">{$picture.thumbnail.FILE_TN_IMG}</a>
        {/if}
      </td>
      <td style="white-space:nowrap;">
        <a href="mailto:{$picture.UPLOAD_EMAIL}">{$picture.UPLOAD_USERNAME}</a>
      </td>
      <td style="white-space:nowrap;">
        <label><input type="radio" name="action-{$picture.ID_IMG}" value="validate" /> {'Validate'|@translate}</label>
        <label><input type="radio" name="action-{$picture.ID_IMG}" value="reject" /> {'Reject'|@translate}</label>
      </td>
    </tr>
    {/foreach}
    {/if}
  </table>

  <p class="bottomButtons">
    <input type="hidden" name="list" value="{$LIST}" />
    <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED}/>
    <input class="submit" type="submit" name="validate-all" value="{'Validate All'|@translate}" {$TAG_INPUT_ENABLED}/>
    <input class="submit" type="submit" name="reject-all" value="{'Reject All'|@translate}" {$TAG_INPUT_ENABLED}/>
    <input class="submit" type="reset" value="{'Reset'|@translate}" />
  </p>

</form>
