{include file='include/colorbox.inc.tpl'}

{combine_script id='jquery.cluetip' load='async' require='jquery' path='themes/default/js/plugins/jquery.cluetip.js'}

{footer_script require='jquery.cluetip'}
var piwigo_need_update_msg = '<a href="admin.php?page=updates">{'A new version of Piwigo is available.'|@translate|@escape:"javascript"} <i class="icon-right"></i></a>';
var ext_need_update_msg = '<a href="admin.php?page=updates&amp;tab=ext">{'Some upgrades are available for extensions.'|@translate|@escape:"javascript"} <i class="icon-right"></i></a>';
const str_gb_used = "{'%s GB used'|translate}";
const str_mb_used = "{'%s MB used'|translate}";
const str_gb = "{'%sGB'|translate}".replace(' ', '&nbsp;');
const str_mb = "{'%sMB'|translate}".replace(' ', '&nbsp;');
const storage_total = {$STORAGE_TOTAL};
const storage_details = {$STORAGE_CHART_DATA|json_encode};
const translate_files = "{'%d files'|translate|escape:javascript}";
let translate_type = {};
{if isset($SUBSCRIBE_BASE_URL)}
  const newsletter_base_url = "{$SUBSCRIBE_BASE_URL}";
{/if}
{literal}
jQuery().ready(function(){
	jQuery('.cluetip').cluetip({
		width: 300,
		splitTitle: '|',
		positionBy: 'bottomTop'
	});
{/literal}
{if $CHECK_FOR_UPDATES}
  jQuery.ajax({
    type: 'GET',
    url: 'ws.php',
    dataType: 'json',
    data: { method: 'pwg.extensions.checkUpdates', format: 'json' },
    timeout: 5000,
    success: function (data) {
      if (data['stat'] != 'ok')
        return;
      piwigo_update = data['result']['piwigo_need_update'];
      ext_update = data['result']['ext_need_update']
      if ((piwigo_update || ext_update) && !jQuery(".warnings").is('div'))
        jQuery(".eiw").prepend('<div class="warnings"><i class="eiw-icon icon-attention"></i><ul></ul></div>');
      if (piwigo_update)
        jQuery(".warnings ul").append('<li>'+piwigo_need_update_msg+'</li>');
      if (ext_update)
        jQuery(".warnings ul").append('<li>'+ext_need_update_msg+'</li>');
    }
  });
{/if}

{if isset($SUBSCRIBE_BASE_URL)}
  jQuery(".eiw").prepend(`
  <div class="promote-newsletter">
    <div class="promote-content">
      
      <img class="promote-image" src="admin/themes/default/images/promote-newsletter.png">

      <div class="promote-newsletter-content">
        <span class="promote-newsletter-title">{"Subscribe to our newsletter and stay updated!"|@translate|escape:javascript}</span>
        <div class="promote-content subscribe-newsletter">
          <input type="text" id="newsletterSubscribeInput" value="{$EMAIL}" class="left-side">
          <a href="{$SUBSCRIBE_BASE_URL}{$EMAIL}" id="newsletterSubscribeLink" class="right-side go-to-porg icon-thumbs-up newsletter-hide">{"Sign up to the newsletter"|@translate|escape:javascript}</a>
        </div>
        <a href="{$OLD_NEWSLETTERS_URL}" class="promote-link">{"See previous newsletters"|@translate|escape:javascript}</a>
      </div>

    </div>
    <a href="#" class="dont-show-again icon-cancel tiptip newsletter-hide" title="{'Understood, do not show again'|translate|escape:javascript}"></a>
  </div>`);
  
{/if}

{literal}

  jQuery("#newsletterSubscribeInput").change(function(){
    jQuery("#newsletterSubscribeLink").attr("href", newsletter_base_url + jQuery("#newsletterSubscribeInput").val())
  })

  jQuery('.newsletter-hide').click(function() {
    jQuery('.promote-newsletter').hide();

    jQuery.ajax({
      type: 'GET',
      url: 'admin.php?action=hide_newsletter_subscription'
    });

    if (jQuery(this).hasClass('newsletter-hide')) {
      return false;
    }
  });
  let size_info = storage_total > 1000000 ? str_gb_used : str_mb_used;
  let size_nb = storage_total > 1000000 ? (storage_total / 1000000).toFixed(2) : (storage_total / 1000).toFixed(0);
  $(".chart-title-infos").html(size_info.replace("%s", size_nb));
});
{/literal}
{foreach from=$STORAGE_CHART_DATA key=type_to_translate item=details}
translate_type['{$type_to_translate}'] = "{$type_to_translate|translate}";
{/foreach}
{/footer_script}
{combine_script id='intro_tooltips' load='footer' path='admin/themes/default/js/intro_tooltips.js'}

{html_style}
.eiw .messages ul li {
  list-style-type:none !important;
}

.eiw .messages .eiw-icon {
  margin-right:10px !important;
}
{/html_style}

<h2>{'Piwigo Administration'|@translate}</h2>

<div class="intro-page-container">
<div class="stat-boxes">

{if $NB_PHOTOS > 1}
<a class="stat-box" href="{$U_ADD_PHOTOS}">
<i class="icon-picture icon-yellow"></i>
<span class="number">{$NB_PHOTOS|number_format}</span><span class="caption">{'Photos'|translate}</span>
</a>
{/if}

{if $NB_ALBUMS > 1}
<a class="stat-box" href="{$U_ALBUMS}">
<i class="icon-sitemap icon-red"></i>
<span class="number">{$NB_ALBUMS}</span><span class="caption">{'Albums'|translate}</span>
</a>
{/if}

{if $NB_TAGS > 1}
<a class="stat-box" href="{$U_TAGS}">
<i class="icon-tags icon-yellow"></i>
<span class="number">{$NB_TAGS}</span><span class="caption" title="{'%d associations'|translate:$NB_IMAGE_TAG}">{'Tags'|translate}</span>
</a>
{/if}

{if $NB_USERS > 2}
<a class="stat-box" href="{$U_USERS}">
<i class="icon-users icon-purple"></i>
{* -1 because we don't count the "guest" user *}
<span class="number">{$NB_USERS - 1}</span><span class="caption">{'Users'|translate}</span>
</a>
{/if}

{if $NB_GROUPS > 0}
<a class="stat-box" href="{$U_GROUPS}">
<i class="icon-group icon-purple"></i>
<span class="number">{$NB_GROUPS}</span><span class="caption">{'Groups'|translate}</span>
</a>
{/if}

{if $NB_COMMENTS > 1}
<a class="stat-box" href="{$U_COMMENTS}">
<i class="icon-chat icon-blue"></i>
<span class="number">{$NB_COMMENTS}</span><span class="caption">{'Comments'|translate}</span>
</a>
{/if}

{if $NB_RATES > 0}
<a class="stat-box" href="{$U_RATING}">
<i class="icon-star icon-yellow"></i>
<span class="number">{$NB_RATES}</span><span class="caption">{'Rating'|translate}</span>
</a>
{/if}

{if $NB_VIEWS > 0}
<a class="stat-box" href="{$U_HISTORY_STAT}">
<i class="icon-signal icon-blue"></i>
<span class="number">{$NB_VIEWS}</span><span class="caption">{'Pages seen'|translate}</span>
</a>
{/if}

{if $NB_PLUGINS > 0}
<a class="stat-box" href="{$U_PLUGINS}">
<i class="icon-puzzle icon-green"></i>
<span class="number">{$NB_PLUGINS}</span><span class="caption">{'Plugins'|translate}</span>
</a>
{/if}

<div class="stat-box">
<i class="icon-hdd icon-blue"></i>
<span class="number">{$STORAGE_USED}</span><span class="caption">{'Storage used'|translate}</span>
</div>

</div> {* .stat-boxes *}

<div class="intro-charts">

  <div class="chart-title"> {"Activity peak in the last weeks"|@translate}</div>
  <div class="activity-chart" style="grid-template-rows: repeat({count($ACTIVITY_CHART_DATA) + 1}, 5vw);">
    {foreach from=$ACTIVITY_CHART_DATA item=WEEK_ACTIVITY key=WEEK_NUMBER}
      <div id="week-{$WEEK_NUMBER}-legend" class="row-legend"><div>{'Week %d'|@translate:$ACTIVITY_WEEK_NUMBER[$WEEK_NUMBER]}</div></div>
      {foreach from=$WEEK_ACTIVITY item=SIZE key=DAY_NUMBER}
        <span class="activity_tooltips">
          {if $SIZE != 0}
          {assign var='SIZE_IN_UNIT' value=$SIZE/$ACTIVITY_CHART_NUMBER_SIZES * 5 + 1}
          {assign var='OPACITY_IN_UNIT' value=$SIZE/$ACTIVITY_CHART_NUMBER_SIZES * 0.6 + 0.2}
          <div id="day{$WEEK_NUMBER}-{$DAY_NUMBER}" style="height:{$SIZE_IN_UNIT}vw;width:{$SIZE_IN_UNIT}vw;"></div>
          {if $ACTIVITY_LAST_WEEKS[$WEEK_NUMBER][$DAY_NUMBER]["number"] != 0}     
          <p class="tooltip" style="transform: translate(-50%,{$SIZE_IN_UNIT/2}vw);">
            <span class="tooltip-arrow"></span>
            <span class="tooltip-header"> 
              <span class="tooltip-title">{if $ACTIVITY_LAST_WEEKS[$WEEK_NUMBER][$DAY_NUMBER]["number"] > 1}{'%d Activities'|translate:$ACTIVITY_LAST_WEEKS[$WEEK_NUMBER][$DAY_NUMBER]["number"]}{else}{'%d Activity'|translate:$ACTIVITY_LAST_WEEKS[$WEEK_NUMBER][$DAY_NUMBER]["number"]}{/if}</span>
              <span class="tooltip-date">{$ACTIVITY_LAST_WEEKS[$WEEK_NUMBER][$DAY_NUMBER]["date"]}</span>
            </span>
            <span class="tooltip-details">
            {foreach from=$ACTIVITY_LAST_WEEKS[$WEEK_NUMBER][$DAY_NUMBER]["details"] item=actions key=cat}
              <span class="tooltip-details-cont">
                {if $cat == "Group"} <span class="icon-group icon-purple tooltip-details-title">{$cat|translate}</span>
                {elseif $cat == "User"} <span class="icon-users icon-purple tooltip-details-title"> {$cat|translate}</span>
                {elseif $cat == "Album"} <span class="icon-sitemap icon-red tooltip-details-title">{$cat|translate}</span>
                {elseif $cat == "Photo"} <span class="icon-picture icon-yellow tooltip-details-title">{$cat|translate} </span>
                {elseif $cat == "Tag"} <span class="icon-tags icon-green tooltip-details-title">{$cat|translate} </span>
                {else} <span class="tooltip-details-title"> {$cat|translate} </span> {/if}

                {foreach from=$actions item=number key=action}
                  {if $action == "Edit"} <span class="icon-pencil tooltip-detail" title="{"%s editions"|@translate:$number}">{$number}</span>
                  {elseif $action == "Add"} <span class="icon-plus tooltip-detail" title="{"%s additions"|@translate:$number}">{$number}</span>
                  {elseif $action == "Delete"} <span class="icon-trash tooltip-detail" title="{"%s deletions"|@translate:$number}">{$number}</span>
                  {elseif $action == "Login"} <span class="icon-key tooltip-detail" title="{"%s login"|@translate:$number}">{$number}</span>
                  {elseif $action == "Logout"} <span class="icon-logout tooltip-detail" title="{"%s logout"|@translate:$number}">{$number} </span>
                  {elseif $action == "Move"} <span class="icon-move tooltip-detail" title="{"%s movement"|@translate:$number}">{$number} </span>
                  {else} <span> ({$action|translate}) {$number} </span> 
                  {/if}  
                {/foreach}
                </span>
            {/foreach}
          </p>
          {/if}
          {/if}
        </span>
      {/foreach}
    {/foreach}
    <div></div>
    {foreach from=$DAY_LABELS item=day}
      <div class="col-legend">{$day} <div class="line-vertical" style="height: {count($ACTIVITY_CHART_DATA)*100 - 50}%;"></div></div>
    {/foreach}
  </div>

  <div id="chart-title-storage" class="chart-title"> {'Storage'|translate} <span class="chart-title-infos"> {'%s MB used'|translate:(round($STORAGE_TOTAL/1000, 0))} </span></div>

  <div class="storage-chart">
    {foreach from=$STORAGE_CHART_DATA key=type item=details}
      <span data-type="storage-{$type}" style="width:{$details.total.filesize/$STORAGE_TOTAL*100}%"> 
        <p>{round($details.total.filesize/$STORAGE_TOTAL*100)}%</p>
      </span>  
    {/foreach}
  </div>

  <div class="storage-tooltips">
    {foreach from=$STORAGE_CHART_DATA key=type item=value}
      <p id="storage-{$type}" class="tooltip">
      <span class="tooltip-arrow"></span>
        <span class="tooltip-header">
          <span id="storage-title-{$type}" class="tooltip-title"></span>
          <span id="storage-size-{$type}" class="tooltip-size"></span>
          <span id="storage-files-{$type}" class="tooltip-files"></span>
        </span>
        <span class="separated"></span>
        <span id="storage-detail-{$type}" class="tooltip-details"></span>
      </p>
    {/foreach}
  </div>

  <div class="storage-chart-legend">
    {foreach from=$STORAGE_CHART_DATA item=i key=type}
      <div><span></span> <p>{$type|translate}</p></div>
    {/foreach}
  </div>

</div> {* .intro-chart *}

</div> {* .intro-page-container *}

<p class="showCreateAlbum">
{if $ENABLE_SYNCHRONIZATION}
  <a href="{$U_QUICK_SYNC}" class="icon-exchange">{'Quick Local Synchronization'|translate}</a>
{/if}


</p>