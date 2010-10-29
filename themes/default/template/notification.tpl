{html_head} 
<link rel="alternate" type="application/rss+xml" title="{'Image only RSS feed'|@translate}" href="{$U_FEED_IMAGE_ONLY}"> 
<link rel="alternate" type="application/rss+xml" title="{'Complete RSS feed (images, comments)'|@translate}" href="{$U_FEED}"> 
{/html_head} 
<div id="content" class="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{$U_HOME}" title="{'Home'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/home.png" class="button" alt="{'Home'|@translate}"></a></li>
    </ul>
    <h2>{'Notification'|@translate}</h2>
  </div>

  <p>{'The RSS notification feed provides notification on news from this website : new pictures, updated categories, new comments. Use a RSS feed reader.'|@translate}</p>

  <dl>
    <dt>
      <a href="{$U_FEED_IMAGE_ONLY}">{'Image only RSS feed'|@translate}</a><br><br>
    </dt>
    <dt>
      <a href="{$U_FEED}">{'Complete RSS feed (images, comments)'|@translate}</a>
    </dt>
  </dl>
</div>
