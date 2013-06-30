{html_head} 
<link rel="alternate" type="application/rss+xml" title="{'Photos only RSS feed'|@translate}" href="{$U_FEED_IMAGE_ONLY}"> 
<link rel="alternate" type="application/rss+xml" title="{'Complete RSS feed (photos, comments)'|@translate}" href="{$U_FEED}"> 
{/html_head}
{include file='infos_errors.tpl'}
<div data-role="content">
  <ul data-role="listview" data-inset="true">
    <li data-role="list-divider">{'Notification'|@translate}</li>
    <li>{'The RSS notification feed provides notification on news from this website : new photos, updated albums, new comments. Use a RSS feed reader.'|@translate}</li>
    <li><a href="{$U_FEED_IMAGE_ONLY}">{'Photos only RSS feed'|@translate}</a></li>
    <li><a href="{$U_FEED}">{'Complete RSS feed (photos, comments)'|@translate}</a></li>
  </ul>
</div>
