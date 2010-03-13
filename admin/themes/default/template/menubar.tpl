{known_script id="jquery" src=$ROOT_URL|@cat:"themes/default/js/jquery.packed.js"}
{known_script id="jquery.ui" src=$ROOT_URL|@cat:"themes/default/js/ui/packed/ui.core.packed.js" }
{known_script id="jquery-tablednd" src=$ROOT_URL|@cat:"themes/default/js/jquery.tablednd.js"}

{literal}
<script type="text/javascript">
$(function() {
$('table.table2').tableDnD({
onDrop:function(table,row) {
var rows = table.tBodies[0].rows;
for (var i=0; i<rows.length; i++) {
  $(rows[i])
     .attr('class', 'row'+i%2)
     .find("input:text").attr('value', (i+1)*10);
}
}
});
});
</script>
{/literal}

<div class="titrePage">
  <h2>Menubar</h2>
</div>
<div id="debugArea"></div>
<form method="post" action="{$F_ACTION}" class="properties">
  <table class="table2" style="width:99%">
    <thead>
      <tr class="throw">
	<td>Id</td>
	<td>{'Author'|@translate}</td>
	<td>{'Name'|@translate}</td>
	<td>{'Position'|@translate}</td>
	<td>{'Hide'|@translate}</td>
      </tr>
    </thead>
    <tbody>
    {foreach from=$blocks item=block name="block_loop"}
	<tr class="{if $smarty.foreach.block_loop.index is odd}row1{else}row2{/if}">
	  <td>{$block.reg->get_id()}</td>
	  <td>{$block.reg->get_owner()}</td>
	  <td>{$block.reg->get_name()|@translate}</td>
	  <td><input type="text" name="pos_{$block.reg->get_id()}" value={math equation="abs(pos)" pos=$block.pos} size="2"></td>
	  <td><input type="checkbox" name="hide_{$block.reg->get_id()}" {if $block.pos<0}checked="checked"{/if}></td>
	</tr>
     {/foreach}
     </tbody>
  </table>
  <p>
    <input type="submit" name="submit" value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED}>
    <input type="submit" name="reset" value="{'Reset'|@translate}" {$TAG_INPUT_ENABLED}>
  </p>
</form>
