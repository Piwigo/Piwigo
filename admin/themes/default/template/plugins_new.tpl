{combine_script id='jquery.sort' load='footer' path='themes/default/js/plugins/jquery.sort.js'}

{combine_script id='jquery.ui.slider' require='jquery.ui' load='header' path='themes/default/js/ui/minified/jquery.ui.slider.min.js'}
{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css"}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_script id='pluginsNew' load='footer' require='jquery.ui.effect-blind,jquery.sort' path='admin/themes/default/js/plugins_new.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}

{footer_script}
const str_confirm_msg = "{"Yes, I am sure"|@translate}";
const str_cancel_msg = "{"No, I have chaged my mind"|@translate}";
const str_install_title = "{'Are you sure you want to install the plugin "%s"?'|@translate|@escape:'javascript'}";
const strs_certification = {
  "-1" : "{'This plugin is incompatible with your version'|@translate}",
  "0" : "{'This plugin have no update since 3 years ! It may be outdated'|@translate}",
  "1" : "{'This plugin has no recent update'|@translate}", 
  "2" : "{'This plugin was updated less than 6 months ago'|@translate}",
  "3" : "{'This plugin have been updated recently'|@translate}",
};
const str_x_month = "{"%d month"|@translate}";
const str_x_months = "{"%d months"|@translate}";
const str_x_year = "{"%d year"|@translate}";
const str_x_years = "{"%d years"|@translate}";
const str_from_begining = "{"since the beginning"|@translate}";
{/footer_script}

<div class="titrePage">
  <div class="sort">
    <div class="sort-actions">
      <div class="beta-test-plugin-switch tiptip" title="{'Show plugins compatible with previous version of Piwigo'|translate|escape:html}">
        <label class="switch">
          <input type="checkbox" id="showBetaTestPlugin" {if $BETA_TEST}checked{/if}>
          <span class="slider round"></span>
        </label>
        <label for='showBetaTestPlugin'>{'Show beta test plugins'|@translate}</label>
      </div>
      
      <div class="sort-by">
      <label>{'Sort order'|@translate}</label>
      <div class="select-container">
        {html_options name="selectOrder" options=$order_options selected=$order_selected}
      </div>
      </div>
      
      <div class="advanced-filter-btn icon-filter"> <span>{'Filters'|@translate}</span></div>
      
      <div id="search-plugin">
          <span class="icon-search search-icon"> </span>
          <span class="icon-cancel search-cancel"></span>
          <input class="search-input" type="text" placeholder="{'Search'|@translate}" id="search">
      </div>
    
    </div>

    <div class="advanced-filter advanced-filter-new-plugin">
      <div class="advanced-filter-header">
        <span class="advanced-filter-title">{'Advanced filters'|@translate}</span>
        <span class="advanced-filter-close icon-cancel"></span>
      </div>
      <div class="advanced-filter-container">
        
        <div class="advanced-filter-item advanced-filter-author">
          <label class="advanced-filter-item-label" for="author-filter">{'Author'|@translate}</label>
          <div class="advanced-filter-item-container">
            <select name="author-filter" id="author-filter"></select>
          </div>
        </div>

        <div class="advanced-filter-item advanced-filter-tag">
          <label class="advanced-filter-item-label" for="tag-filter">{'Tag'|@translate}</label>
          <div class="advanced-filter-item-container">
            <select name="tag-filter" id="tag-filter"></select>
          </div>
        </div>

        <div class="advanced-filter-item advanced-filter-rating">
          <label class="advanced-filter-item-label" for="notation-filter">
            {'Rating greater than'|@translate}
            <span class="rating-star-container">
              <span data-star="0"><i></i></span>
              <span data-star="1"><i></i></span>
              <span data-star="2"><i></i></span>
              <span data-star="3"><i></i></span>
              <span data-star="4"><i></i></span>
            </span>
          </label>
          <div class="advanced-filter-item-container">
            <div id="notation-filter" class="select-bar"></div>
            <div class="slider-bar-wrapper">
            <div class="slider-bar-container notation-filter-slider"></div>
            </div>
          </div>
        </div>

        <div class="advanced-filter-item advanced-filter-revision-date">
            <label class="advanced-filter-item-label" for="revision-date-filter">
                {'Last revision date is newer than'|@translate}<span class="revision-date"></span>
            </label>
            <div class="advanced-filter-item-container">
                <div id="revision-date" class="select-bar"></div>
                <div class="slider-bar-wrapper">
                    <div class="slider-bar-container revision-date-filter-slider"></div>
                </div>
            </div>
        </div>

        <div class="advanced-filter-item advanced-filter-certification">
          <label class="advanced-filter-item-label" for="certification-filter">
              {'Certification higher or equal to'|@translate}
              <span><i class="certification tiptip" title=""></i></span>
          </label>
          <div class="advanced-filter-item-container">
            <div id="certification-filter" class="select-bar"></div>
              <div class="slider-bar-wrapper">
                  <div class="slider-bar-container certification-filter-slider"></div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

{if not empty($plugins)}
<div id="availablePlugins">

{assign var='color_tab' value=["icon-red", "icon-blue", "icon-yellow", "icon-purple", "icon-green"]}

{foreach from=$plugins item=plugin name=plugins_loop}
<div class="pluginBox pluginBigBox" id="plugin_{$plugin.ID}"
  data-id="{$plugin.ID}"
  data-date="{$plugin.ID}"
  data-name="{$plugin.EXT_NAME}"
  data-revision="{$plugin.REVISION_DATE}"
  data-downloads="{$plugin.DOWNLOADS}"
  data-author="{$plugin.AUTHOR}"
  data-tags="{implode(', ', $plugin.TAGS)}"
>
  <div class="pluginContent">
    <div class="pluginImage">
      {if $plugin.SCREENSHOT == ''}
        <span class="noImage {$color_tab[$plugin.ID%5]}"><i class="icon-puzzle"></i></span>
      {else}
        <span class="screenshot" style="background-image: url({$plugin.SCREENSHOT});"></span>
      {/if}
    </div>
    <div class="pluginInfo">
      <div>
        <div class="pluginName">
          <span title="{$plugin.EXT_NAME}">{$plugin.EXT_NAME}</span>
          <i class="certification tiptip" data-certification={$plugin.CERTIFICATION}
            {if $plugin.CERTIFICATION == 3}
              title="{'This plugin have been updated recently'|@translate}"
            {elseif $plugin.CERTIFICATION == 2}
              title="{'This plugin was updated less than 6 months ago'|@translate}"
            {elseif $plugin.CERTIFICATION == 1}
              title="{'This plugin has no recent update'|@translate}"
            {elseif $plugin.CERTIFICATION == 0}
              title="{'This plugin have no update since 3 years ! It may be outdated'|@translate}"
            {elseif $plugin.CERTIFICATION == -1}
              title="{'This plugin is incompatible with your version'|@translate}"
            {/if}
          ></i>
        </div>
        <div class="pluginAuthorVersion">{'by %s'|@translate:$plugin.AUTHOR}</div>
      </div>

      <div>
        {if !is_null($plugin.RATING)}
          <div class="pluginRating tiptip" data-rating="{$plugin.RATING}" title="{'On %d rating(s)'|@translate:$plugin.NB_RATINGS}">
            <div class="rating-star-container">
              <span data-star="0"><i></i></span>
              <span data-star="1"><i></i></span>
              <span data-star="2"><i></i></span>
              <span data-star="3"><i></i></span>
              <span data-star="4"><i></i></span>
            </div>
            <span class="rating">{$plugin.RATING}</span>
          </div>
        {/if}
        <div class="pluginDownload tiptip" title="{$plugin.DOWNLOADS} {'Downloads'|@translate}"><i class="icon-download">{$plugin.DOWNLOADS}</i></div>
        <div class="pluginVersion tiptip" title="{$plugin.REVISION_FORMATED_DATE}"><i class="icon-flow-branch"></i>{'Version %s'|@translate:$plugin.VERSION}</div>
        <a class="pluginLink" href="{$plugin.EXT_URL}"><i class="icon-link"></i>{'Website'|@translate}</a>
      </div>

      <div class="pluginInstall">
        <a class="buttonLike buttonInstall" href="{$plugin.URL_INSTALL}"><i class="icon-plus-circled"></i>{'Add'|@translate}</a>
      </div>
    </div>
    <div class="pluginMoreInfo">
      <div class="pluginTags tiptip" title="{'Tags'|@translate} : {implode(', ', $plugin.TAGS)}">
      {foreach from=$plugin.TAGS key=tag_id item=tag_label}
        <span data-id="{$tag_id}">{$tag_label}</span>
      {/foreach}
      </div>
      <div class="pluginDesc" >
      {$plugin.BIG_DESC|@nl2br}
      </div>
    </div>
  </div>
</div>
{/foreach}
</div>
{else}
<p>{'There is no other plugin available.'|@translate}
{if isset($BETA_URL)}
  <a href="{$BETA_URL}" class="buttonLike icon-fire">{'Show beta test plugins'|@translate}</a>
{/if}
</p>
{/if}