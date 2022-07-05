{include file='include/colorbox.inc.tpl'} 
{footer_script}{literal}

$(window).bind("load", function() {
  $('.themeBox').each(function() {

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

{if not empty($new_themes)}
<div class="themeBoxes">
{foreach from=$new_themes item=theme name=themes_loop}
<div class="themeBox">
  <div class="themeShot"><a href="{$theme.screenshot}" class="preview-box" title="{$theme.name}"><img src="{$theme.screenshot}" onerror="this.src='{$default_screenshot}'"></a></div>
  <div class="themeName" title="{$theme.name}">{$theme.name}</div>
  <div class="themeActions"><a href="{$theme.install_url}">{'Install'|@translate}</a></div>
</div>
{/foreach}
</div> <!-- themeBoxes -->
{else}
<p>{'There is no other theme available.'|@translate}</p>
{/if}