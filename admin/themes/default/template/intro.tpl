{include file='include/colorbox.inc.tpl'}

{combine_script id='jquery.cluetip' load='async' require='jquery' path='themes/default/js/plugins/jquery.cluetip.js'}

{footer_script require='jquery.cluetip'}
var piwigo_need_update_msg = '<a href="admin.php?page=updates">{'A new version of Piwigo is available.'|@translate|@escape:"javascript"} <i class="icon-right"></i></a>';
var ext_need_update_msg = '<a href="admin.php?page=updates&amp;tab=ext">{'Some upgrades are available for extensions.'|@translate|@escape:"javascript"} <i class="icon-right"></i></a>';

{literal}
  jQuery().ready(function(){
	jQuery('.cluetip').cluetip({
		width: 300,
		splitTitle: '|',
		positionBy: 'bottomTop'
	});
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

  jQuery('.newsletter-subscription a').click(function() {
    jQuery('.newsletter-subscription').hide();

    jQuery.ajax({
      type: 'GET',
      url: 'admin.php?action=hide_newsletter_subscription'
    });

    if (jQuery(this).hasClass('newsletter-hide')) {
      return false;
    }
  });
});
{/literal}
{/footer_script}

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
<a class="stat-box" href="{$U_CATEGORIES}">
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
<span class="number">{$NB_USERS}</span><span class="caption">{'Users'|translate}</span>
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

{if $NB_PHOTOS > 1}
<div class="stat-box">
<i class="icon-back-in-time icon-yellow"></i>
<span class="number">{$first_added_age}</span><span class="caption" title="{'first photo added on %s'|translate:$first_added_date}">{'First photo added'|translate}</span>
</div>
{/if}

</div> {* .stat-boxes *}

<div class="intro-charts">

  <div class="chart-title"> {"Activity peak in the last weeks"|@translate}</div>
  <div class="activity-chart" style="grid-template-rows: repeat({count($ACTIVITY_CHART_DATA) + 1}, 6vw);">
    {foreach from=$ACTIVITY_CHART_DATA item=WEEK_ACTIVITY key=WEEK_NUMBER}
      <div id="week-{$WEEK_NUMBER}-legend" class="row-legend"><div>{"Week of %s/%s"|@translate:$ACTIVITY_MONDAYS_DATE[$WEEK_NUMBER]['d']:$ACTIVITY_MONDAYS_DATE[$WEEK_NUMBER]['m']}</div></div>
      {foreach from=$WEEK_ACTIVITY item=SIZE key=DAY_NUMBER}
        <span>
          {if $SIZE != 0}
          <div id="day{$WEEK_NUMBER}-{$DAY_NUMBER}" style="height:{$SIZE/$ACTIVITY_CHART_NUMBER_SIZES * 7 + 1}vw;width:{$SIZE/$ACTIVITY_CHART_NUMBER_SIZES * 7 + 1}vw;opacity:{$SIZE/$ACTIVITY_CHART_NUMBER_SIZES * 0.7 + 0.1}"></div>
          {if $ACTIVITY_LAST_WEEKS[$WEEK_NUMBER][$DAY_NUMBER]["number"] != 0}
          <p style="transform: translate(-50%, 50%) translate(0, {if $SIZE/2 >= 1}{$SIZE/2}{else}2{/if}vw)"> 
            <b>{"%s Activities"|@translate:$ACTIVITY_LAST_WEEKS[$WEEK_NUMBER][$DAY_NUMBER]["number"]}</b>
            {foreach from=$ACTIVITY_LAST_WEEKS[$WEEK_NUMBER][$DAY_NUMBER]["details"] item=actions key=cat}
              <br> {$cat} : {foreach from=$actions item=number key=action} ({$action}) {$number} {/foreach}
            {/foreach}
          </p>
          {/if}
          {/if}
        </span>
      {/foreach}
    {/foreach}
    <div></div>
    {foreach from=array('Mon'|translate, 'Tue'|translate, 'Wed'|translate, 'Thu'|translate, 'Fri'|translate, 'Sat'|translate, 'Sun'|translate) item=day}
      <div class="col-legend">{$day} <div class="line-vertical" style="height: {count($ACTIVITY_CHART_DATA)*100 - 50}%;"></div></div>
    {/foreach}
  </div>

  <div class="chart-title"> {"Storage"|@translate} <span class="chart-title-infos"> {'%s MB used'|translate:$STORAGE_TOTAL} </span></div>

  <div class="storage-chart">
    {foreach from=$STORAGE_CHART_DATA item=value}
      <span style="width:{$value}%"> <p>{round($value)}%</p> </span>  
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


{if isset($SUBSCRIBE_BASE_URL)}
  <br><span class="newsletter-subscription"><a href="{$SUBSCRIBE_BASE_URL}{$EMAIL}" id="newsletterSubscribe" class="externalLink cluetip icon-mail-alt" title="{'Piwigo Announcements Newsletter'|@translate}|{'Keep in touch with Piwigo project, subscribe to Piwigo Announcement Newsletter. You will receive emails when a new release is available (sometimes including a security bug fix, it\'s important to know and upgrade) and when major events happen to the project. Only a few emails a year.'|@translate|@htmlspecialchars|@nl2br}">{'Subscribe %s to Piwigo Announcements Newsletter'|@translate:$EMAIL}</a> <a href="#" class="newsletter-hide">{'... or hide this link'|translate}</a></span>
{/if}
</p>