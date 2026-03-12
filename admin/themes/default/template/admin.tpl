{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{footer_script}
jQuery.fn.lightAccordion = function(options) {
  var settings = $.extend({
    header: 'dt',
    content: 'dd',
    active: 0
  }, options);
  
  return this.each(function() {
    var self = jQuery(this);
    
    var contents = self.find(settings.content),
        headers = self.find(settings.header);
    
    contents.not(contents[settings.active]).hide();
  
    self.on('click', settings.header, function() {
        var content = jQuery(this).next(settings.content);
        content.slideDown();
        contents.not(content).slideUp();
    });
  });
};

$('#menubar').lightAccordion({
  active: {$ACTIVE_MENU}
});

/* in case we have several infos/errors/warnings display bullets */
jQuery(document).ready(function() {
  var eiw = ["infos","erros","warnings", "messages"];

  for (var i = 0; i < eiw.length; i++) {
    var boxType = eiw[i];

    if (jQuery("."+boxType+" ul li").length > 1) {
      jQuery("."+boxType+" ul li").css("list-style-type", "square");
      jQuery("."+boxType+" .eiw-icon").css("margin-right", "20px");
    }
  }

  if (jQuery('h2').length > 0) {
    jQuery('h1').html(jQuery('h2').html());
  }
});

{* Variables for menubar script *}
let username = '{$USERNAME}'

{/footer_script}

{strip}
{combine_css path="admin/themes/default/fontello/css/fontello.css" order=-10}
{assign "theme_id" ""}
{foreach from=$themes item=theme}
  {assign "theme_id" $theme.id}
{/foreach}

{combine_script id='jquery' path='themes/default/js/jquery.min.js'}
{/strip}

{include file='include/admin_nav.inc.tpl'}

<div id="content" class="content">

  <h1>{$ADMIN_PAGE_TITLE}<span class="admin-object-id">{$ADMIN_PAGE_OBJECT_ID}</span></h1>

  {if isset($TABSHEET)}
  {$TABSHEET}
  {/if}
  {if isset($U_HELP)}
  {include file='include/colorbox.inc.tpl'}
{footer_script}
  jQuery('.help-popin').colorbox({ width:"500px" });
{/footer_script}
  <ul class="HelpActions">
    <li><a href="{$U_HELP}&amp;output=content_only" title="{'Help'|@translate}" class="help-popin"><span class="icon-help-circled"></span></a></li>
  </ul>
  {/if}

<div class="eiw">
  {if isset($errors)}
  <div class="errors">
    <ul>
      {foreach from=$errors item=error}
      <li><i class="eiw-icon icon-cancel"></i>{$error}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

  {if isset($infos)}
  <div class="infos">
    <ul>
      {foreach from=$infos item=info}
      <li><i class="eiw-icon icon-ok-circled"></i>{$info}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

  {if isset($warnings)}
  <div class="warnings">
    <ul>
      {foreach from=$warnings item=warning}
      <li><i class="eiw-icon icon-attention"></i>{$warning}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

  {if isset($messages)}
  <div class="messages">
    <ul>
      {foreach from=$messages item=message}
          <li><i class="eiw-icon icon-info-circled-1"></i>{$message}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

</div> {* .eiw *}

  {$ADMIN_CONTENT}
</div>
