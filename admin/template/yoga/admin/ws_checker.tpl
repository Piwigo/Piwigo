{* $Id$ *}

<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{$U_HELP}" onclick="popuphelp(this.href); return false;" title="{'Help'|@translate}"><img src="{$themeconf.icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{'title_wscheck'|@translate} - {'web_services'|@translate}</h2>
</div>

{if !empty($update_results)}
<ul>
  {foreach from=$update_results item=result}
  <li>$result</li>
  {/foreach}
</ul>
{/if}

{* Add Access *}
<form method="post" name="adding_access" action="{$F_STATUS_ACTION}">
  <!-- Current Default -->
  <fieldset>
    <legend>{'ws_adding_legend'|@translate}</legend>
    <table>
    {* Access key *}
      <tr>
        <td>
          <label for="KeyName">{'Confidential partner key'|@translate} </label>
        </td>
        <td>
          <input type="text" maxlength="35" size="35" name="add_partner"
            id="add_partner" value="{$F_ADD_PARTNER}"
            title="{'Basis of access key calculation'|@translate}" />
        </td>
      </tr>

    {* Target (cat/ids, tag/ids, or list/ids ids=id,id-id,...) *}
      <tr>
        <td>
          <label for="Access">{'Target'|@translate}</label>
        </td>
        <td>
          <input type="text" maxlength="128" size="35" name="add_target"
            id="add_target" value="{$F_ADD_ACCESS}"
            title="{'Facultative and restrictive option'|@translate}" />
          <i><small> ({'Access: see help text for more'|@translate})
          </small></i>
        </td>
      </tr>

    {* Restricted access to specific request *}
      <tr>
        <td>
          <label for="add_request">{'Restrict access to'|@translate}</label>
        </td>
        <td>
          <select name="add_request" id="add_request" style="width: 18em"  
            onfocus="this.className='focus';" 
            onblur="this.className='nofocus';">
            <option value=""></option>
            {html_options values=$add_requests output=$add_requests}
          </select> 
          <i><small> ({'ws_Methods'|@translate})</small></i>
        </td>
      </tr>

     {* Limit number of images information to be return *}
     <tr>
        <td>
          <label for="add_limit">{'Returned images limit'|@translate}</label>
        </td>
        <td>
          <select name="add_limit" id="add_limit" style="width: 10em"  
            onfocus="this.className='focus';" 
            onblur="this.className='nofocus';">
            {html_options values=$add_limits output=$add_limits}
          </select>
        </td>
      </tr>

    {* Open service is postponed by n days *}
    {* In comment currently
      <tr>
        <td>
          <label for="add_start">{'Postponed availability in days'|@translate}</label>
        </td>
        <td>
          <select name="add_start" id="add_start" style="width: 10em" 
            onfocus="this.className='focus';" 
            onblur="this.className='nofocus';">
            <!-- BEGIN add_start -->
            <option value="{add_start.VALUE}"
              {add_start.SELECTED}>{add_start.CONTENT}
            </option> 
            <!-- END add_start -->
          </select>
        </td>
      </tr>
    *}

    {* Opened service only for n days *}
      <tr>
        <td>
          <label for="add_end">{'Duration in days'|@translate}</label>
        </td>
        <td>
          <select name="add_end" id="add_end" style="width: 10em"  
            onfocus="this.className='focus';" 
            onblur="this.className='nofocus';">
            {html_options values=$add_ends output=$add_ends}
          </select>
        </td>
      </tr>

    {* Idendify your partner (name / website / phone) as you want *}
      <tr>
        <td>
          <label for="add_Comment">{'ws_Comment'|@translate}</label>
          <br />
        </td>
        <td>
          <textarea name="add_comment" id="add_comment" 
            rows="4" cols="80">{'Comment to identify your partner clearly'|@translate}</textarea>  
        </td>
      </tr>

    {* Add submit button *}
      <tr>
        <td>
        </td>
        <td>
          <input class="submit" type="submit" name="wsa_submit" style="width: 10em; padding-top: 3px;"
            value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED} 
            title="{'Add this access definition'|@translate}" />
        </td>
      </tr>      
    </table>
  </fieldset>
</form>

{if !empty($access_list)}
<!-- Access list -->
<form method="post" name="preferences" action="{$F_STATUS_ACTION}">
  <input type="hidden" name="partner_prev" value="{$F_PREV_PARTNER}">
  <input type="hidden" name="request_prev" value="{$F_PREV_REQUEST}">
  <input type="hidden" name="high_prev" value="{$F_PREV_HIGH}">
  <input type="hidden" name="normal_prev" value="{$F_PREV_NORMAL}">
  <input type="hidden" name="order_prev" value="{$F_PREV_ORDER}">
  <input type="hidden" name="dir5n_prev" value="{$F_PREV_DIR5N}">
  <!-- Delete / Update Selected -->
  <fieldset>
    <legend>{'ws_update_legend'|@translate}</legend>
    <table class="table2">
      <tr class="throw">
        <th>&nbsp;</th>
        <th>{'ws_KeyName'|@translate}</th>
        <th>{'ws_Access'|@translate}</th>
        <th>{'ws_End'|@translate}</th>
        <th>{'ws_Request'|@translate}</th>
        <th>{'ws_Limit'|@translate}</th>
        <th>{'ws_Comment'|@translate}</th>
      </tr>
      {foreach from=$access_list item=access name=access_loop}
      <tr class="{if $smarty.foreach.access_loop.index is odd}row1{else}row2{/if}">
        <td>
          <input type="radio" name="selection" 
            value="{$access.ID}" id="selection-{$access.ID}">
        </td>
        <td><label for="selection-{$access.ID}">{$access.NAME}</label></td>
        <td>{$access.TARGET}</td>
        <td>{$access.END}</td>
        <td>{$access.REQUEST}</td>
        <td>{$access.LIMIT}</td>
        <td>{$access.COMMENT}</td>
      </tr>
      {/foreach}
    </table>

    <table>   
    <tr>    
    <td>
    {'ws_delete_legend'|@translate}
    </td>
    <td>
    <input type="radio" name="delete_confirmation" 
      value="true">
    <input class="submit" type="submit" name="wsX_submit" style="width: 10em; padding-top: 3px;"
      value="{'Delete'|@translate}" {$TAG_INPUT_ENABLED}>
    </td>
    </tr>
    </table>
    <hr>    
    <table>
    <tr>
    <td>
    <span class="property">
      <label for="upd_end">{'Modify End from Now +'|@translate} </label>
    </span> 
    <select name="upd_end" id="upd_end"  style="width: 10em" 
      onfocus="this.className='focus';" 
      onblur="this.className='nofocus';">
      {html_options values=$add_ends output=$add_ends}
    </select> 
    <input class="submit" type="submit" name="wsu_submit" style="width: 10em; padding-top: 3px;"
       value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED}>
    </td>
    <td>
    <i><small> ({'Web Services availability duration in days'|@translate})</small></i>
    </td>
    </tr>
    </table>

  </fieldset>
</form>
{/if}

{if isset($WS_STATUS)}
    <h3>{$WS_STATUS}</h3>
{/if}
