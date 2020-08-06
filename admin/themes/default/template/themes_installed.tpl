{include file='include/colorbox.inc.tpl'} 

{footer_script}{literal}
jQuery(document).ready(function() {
  $("a.preview-box").colorbox();
  
  $('.themeBox').each(function() {

    let screenImage = $(this).find(".preview-box img");
    screenImage.on( 'load', () => {
      let imageW = screenImage.innerWidth();
      let imageH = screenImage.innerHeight();
      let size = $(this).find(".preview-box").innerWidth();

      console.log(screenImage.innerHeight())

      if (imageW > imageH) {
        screenImage.css('height', size+'px');
        screenImage.css('width', (imageW * size / imageH)+'px');
      } else {
        screenImage.css('width', size+'px');
        screenImage.css('heigth', (imageH * size / imageW)+'px');
      }
    });

    let box = $(this);
    box.find('.showInfo').on('click', function() {
      box.find('.showInfo-dropdown').fadeToggle();
    });
  })
  
  $(document).mouseup(function (e) {
    e.stopPropagation();
    if (!$(event.target).hasClass('showInfo')) {
      $('.showInfo-dropdown').fadeOut();
    }
  });
  
});

{/literal}{/footer_script}

<div class="titrePage">
  <h2>{'Installed Themes'|@translate}</h2>
</div>

<div id="themesContent">

{assign var='field_name' value='null'} {* <!-- 'counter' for fieldset management --> *}
{foreach from=$tpl_themes item=theme}
    
{if $field_name != $theme.STATE}
  {if $field_name != 'null'}
    </div>
  </fieldset>
  {/if}
  
  <fieldset>
    <legend>
    {if $theme.STATE == 'active'}
      <span class="icon-purple icon-toggle-on"></span>{'Active Themes'|@translate}
    {else}
      <span class="icon-yellow icon-toggle-off"></span>{'Inactive Themes'|@translate}
    {/if}
    </legend>
    <div class="themeBoxes">
  {assign var='field_name' value=$theme.STATE}
{/if}

  {if not empty($theme.AUTHOR)}
    {if not empty($theme.AUTHOR_URL)}
      {assign var='author' value="<a href='%s'>%s</a>"|@sprintf:$theme.AUTHOR_URL:$theme.AUTHOR}
    {else}
      {assign var='author' value='<u>'|cat:$theme.AUTHOR|cat:'</u>'}
    {/if}
  {/if}
  {if not empty($theme.VISIT_URL)}
    {assign var='version' value="<a class='externalLink' href='"|cat:$theme.VISIT_URL|cat:"'>"|cat:$theme.VERSION|cat:"</a>"}
  {else}
    {assign var='version' value=$theme.VERSION}
  {/if}
  
  <div class="themeBox{if $theme.IS_DEFAULT} themeDefault{/if}">
    <div class="themeShot"><a href="{$theme.SCREENSHOT}" class="preview-box" title="{$theme.NAME}"><img src="{$theme.SCREENSHOT}" alt=""></a></div>
    <div class="themeName" title="{$theme.NAME}">
      {$theme.NAME} {if $theme.IS_DEFAULT}<em>({'default'|@translate})</em>{/if} {if $theme.IS_MOBILE}<em>({'Mobile'|@translate})</em>{/if}
      <a class="icon-ellipsis-v showInfo"></a>
    </div>
    <div class="showInfo-dropdown dropdown">
      <div class="showInfo-dropdown-header">
        {if !empty($author)}
          {'By %s'|@translate:$author} | 
        {/if}
        {'Version'|@translate} {$version}
      </div>
      <div class="showInfo-dropdown-content">
        {$theme.DESC|@escape:'html'}
      </div>
      {if $theme.DEACTIVABLE}
        <a href="{$deactivate_baseurl}{$theme.ID}" class="showInfo-dropdown-action tiptip icon-cancel-circled" title="{'Forbid this theme to users'|@translate}">{'Deactivate'|@translate}</a>
      {/if}
    </div>
    <div class="themeActions">
{if $theme.STATE == 'active'}
  {if $theme.ADMIN_URI}
        <a href="{$theme.ADMIN_URI}" class="icon-cog">{'Configuration'|@translate}</a>
  {/if}
  {if not $theme.IS_DEFAULT}
        <a href="{$set_default_baseurl}{$theme.ID}" class="tiptip" title="{'Set as default theme for unregistered and new users'|@translate}">{'Set as default'|@translate}</a>
  {else}
        <span class="tiptip" title="{'Theme is already set to default'|@translate}">{'Set as default'|@translate}</span>
  {/if}
{else}
  {if $theme.ACTIVABLE}
      <a href="{$activate_baseurl}{$theme.ID}" title="{'Make this theme available to users'|@translate}" class="tiptip">{'Activate'|@translate}</a>
  {else}
      <span title="{$theme.ACTIVABLE_TOOLTIP}" class="tiptip">{'Activate'|@translate}</span>
  {/if}
  {if $theme.DELETABLE}
      <a href="{$delete_baseurl}{$theme.ID}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');">{'Delete'|@translate}</a>
  {else}
      <span title="{$theme.DELETE_TOOLTIP}">{'Delete'|@translate}</span>
  {/if}
{/if}
    </div> <!-- themeActions -->
  </div>
  
{/foreach}
</div> <!-- themeBoxes -->
</fieldset>

</div> <!-- themesContent -->
