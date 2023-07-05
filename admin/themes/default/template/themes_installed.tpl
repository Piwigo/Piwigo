{include file='include/colorbox.inc.tpl'} 

{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{footer_script}
const title_msg = '{'Are you sure you want to delete this theme?'|@translate|@escape:'javascript'}';
const confirm_msg = '{"Yes, I am sure"|@translate}';
const cancel_msg = "{"No, I have changed my mind"|@translate}";
$(".delete-theme-button").each(function() {
  let theme_name = $(this).closest(".themeBox").find(".themeName").attr("title");
  let title = '{'Are you sure you want to delete the theme "%s"?'|@translate|@escape:'javascript'}';
  $(this).pwg_jconfirm_follow_href({
    alert_title: title.replace("%s", theme_name),
    alert_confirm: confirm_msg,
    alert_cancel: cancel_msg
  });
});
{/footer_script}

{footer_script}{literal}
jQuery(document).ready(function() {
  $("a.preview-box").colorbox();
  
  $(document).mouseup(function (e) {
    e.stopPropagation();
    if (!$(event.target).hasClass('showInfo')) {
      $('.showInfo-dropdown').fadeOut();
    }
  });
  
});

$(window).bind("load", function() {
  $('.themeBox').each(function() {

    let box = $(this);
    box.find('.showInfo').on('click', function() {
      let dropdown = box.find('.showInfo-dropdown');
      $('.showInfo-dropdown').each(function() {
        if ($(this) !== dropdown) {
          $(this).fadeOut();
        }  
      })
      box.find('.showInfo-dropdown').fadeToggle();
    });

    let screenImage = $(this).find(".preview-box img");
    let imageW = screenImage.innerWidth();
    let imageH = screenImage.innerHeight();
    let size = $(this).find(".preview-box").innerWidth();

    if (imageW > imageH) {
      screenImage.css('height', size+'px');
      screenImage.css('width', (imageW * size / imageH)+'px');
    } else {
      screenImage.css('width', size+'px');
      screenImage.css('heigth', (imageH * size / imageW)+'px');
    }
  })
})

{/literal}{/footer_script}

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
  
  <div class="themeBox{if isset($theme.IS_DEFAULT) and $theme.IS_DEFAULT} themeDefault{/if}">
    <div class="themeShot"><a href="{$theme.SCREENSHOT}" class="preview-box" title="{$theme.NAME}"><img src="{$theme.SCREENSHOT}" alt=""></a></div>
    <div class="themeName" title="{$theme.NAME}">
      {$theme.NAME} {if isset($theme.IS_DEFAULT) and $theme.IS_DEFAULT}<i class="icon-star" title="{'default'|@translate}"></i>{/if} {if $theme.IS_MOBILE}<i class="icon-mobile" title="{'Mobile'|translate}"></i>{/if}
      {if $isWebmaster == 1} <a class="icon-ellipsis-v showInfo"></a>{/if}
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
      {if $theme.STATE != "active" and $CONF_ENABLE_EXTENSIONS_INSTALL}
        {if $theme.DELETABLE}
            <a class="dropdown-option icon-trash delete-plugin-button delete-theme-button" href="{$delete_baseurl}{$theme.ID}">{'Delete'|@translate}</a>
        {else}
            <span class="dropdown-option icon-trash delete-plugin-button"title="{$theme.DELETE_TOOLTIP}">{'Delete'|@translate}</span>
        {/if}
      {/if}
      {if isset($theme.DEACTIVABLE) and $theme.DEACTIVABLE}
        <a href="{$deactivate_baseurl}{$theme.ID}" class="showInfo-dropdown-action tiptip icon-cancel-circled" title="{'Forbid this theme to users'|@translate}">{'Deactivate'|@translate}</a>
      {/if}
    </div>
{if $isWebmaster == 1}
    <div class="themeActions">
{if $theme.STATE == 'active'}
  {if $theme.ADMIN_URI}
        <a href="{$theme.ADMIN_URI}" class="icon-cog">{'Configuration'|@translate}</a>
  {else}
        <div class="pluginUnavailableAction icon-cog tiptip" title="{'N/A'|translate}">{'Configuration'|@translate}</div>
  {/if}
  {if isset($theme.IS_DEFAULT) and not $theme.IS_DEFAULT}
        <a href="{$set_default_baseurl}{$theme.ID}" class="tiptip icon-star" title="{'Set as default theme for unregistered and new users'|@translate}">{'Set as default'|@translate}</a>
  {else}
        <span class="tiptip icon-star" title="{'This is already the default theme'|@translate}">{'Set as default'|@translate}</span>
  {/if}
{else}
  {if $theme.ACTIVABLE}
      <a href="{$activate_baseurl}{$theme.ID}" title="{'Make this theme available to users'|@translate}" class="icon-plus tiptip">{'Activate'|@translate}</a>
  {else}
      <span title="{$theme.ACTIVABLE_TOOLTIP}" class="icon-plus tiptip">{'Activate'|@translate}</span>
  {/if}
{/if}
    </div> <!-- themeActions -->
{/if}
  </div>
  
{/foreach}
</div> <!-- themeBoxes -->
</fieldset>

</div> <!-- themesContent -->
