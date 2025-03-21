{combine_script id='jquery.ui'  load='async' path='themes/default/js/ui/minified/jquery.ui.core.min.js'}
{combine_script id='jquery.ui.slider' require='jquery.ui' load='async' path='themes/default/js/ui/minified/jquery.ui.slider.min.js'}
{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css" order=-999}
{combine_script id='doubleSlider' load='footer' require='jquery.ui.slider' path='admin/themes/default/js/doubleSlider.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}
{combine_script id='jquery.tipTip' load='header' path='themes/default/js/plugins/jquery.tipTip.minified.js'}
{combine_css path="themes/default/css/search.css" order=-100}
{combine_css path="themes/default/css/{$themeconf.colorscheme}-search.css" order=-100}
{combine_css path="themes/default/vendor/fontello/css/gallery-icon.css" order=-10} 

{footer_script}
{if isset($GP)}
  global_params = {$GP};
{/if}

{if isset($fullname_of)}
fullname_of_cat = {$fullname_of};
{/if}

{if isset($SEARCH_ID)}
search_id = '{$SEARCH_ID}';
{/if}

str_word_widget_label = "{'Search for words'|@translate|escape:javascript}";
str_tags_widget_label = "{'Tag'|@translate|escape:javascript}";
str_album_widget_label = "{'Album'|@translate|escape:javascript}";
str_author_widget_label = "{'Author'|@translate|escape:javascript}";
str_added_by_widget_label = "{'Added by'|@translate|escape:javascript}";
str_filetypes_widget_label = "{'File type'|@translate|escape:javascript}";

str_rating_widget_label = "{'Rating'|@translate|escape:javascript}";
str_no_rating = "{'no rate'|@translate|escape:javascript}";
str_between_rating= "{'between %d and %d'|@translate}";
str_filesize_widget_label = "{'Filesize'|@translate|escape:javascript}";
str_width_widget_label = "{'Width'|@translate|escape:javascript}";
str_height_widget_label = "{'Height'|@translate|escape:javascript}";
str_ratio_widget_label = "{'Ratio'|@translate|escape:javascript}";
str_ratios_label = [];
str_ratios_label['Portrait'] ="{'Portrait'|@translate|escape:javascript}";
str_ratios_label['square'] = "{'square'|@translate|escape:javascript}";
str_ratios_label['Landscape'] = "{'Landscape'|@translate|escape:javascript}";
str_ratios_label['Panorama'] = "{'Panorama'|@translate|escape:javascript}";

str_empty_search_top_alt = "{'Fill in the filters to start a search'|@translate|escape:javascript}";
str_empty_search_bot_alt = "{'Pre-established filters are proposed, but you can add or remove them using the "Choose filters" button.'|@translate|escape:javascript}";
const str_search_in_ab = '{'Search in albums'|@translate|escape:javascript}';

const prefix_icon = 'gallery-icon-';

{*<!-- sliders config -->*}
  var sliders = {

    {if isset($FILESIZE)}
    filesizes: {  
      values: [{$FILESIZE.list}],
      selected: {
        min: {$FILESIZE.selected.min},
        max: {$FILESIZE.selected.max},
      },
      text: '{'between %s and %s MB'|translate|escape:'javascript'}',
    },
    {/if}

    {if isset($HEIGHT)}
    heights: {
      values: [{$HEIGHT.list}],
      selected: {
        min: {$HEIGHT.selected.min},
        max: {$HEIGHT.selected.max},
      },
      text: '{'between %d and %d pixels'|translate|escape:'javascript'}',
    },
    {/if}

    {if isset($WIDTH)}
    widths: {
      values: [{$WIDTH.list}],
      selected: {
        min: {$WIDTH.selected.min},
        max: {$WIDTH.selected.max},
      },
      text: '{'between %d and %d pixels'|translate|escape:'javascript'}',
    },
    {/if}
  };

  {if isset($SHOW_FILTER_RATINGS)}
  var show_filter_ratings = {if $SHOW_FILTER_RATINGS}{$SHOW_FILTER_RATINGS}{else}false{/if};
  {/if}

{/footer_script}

{combine_script id='mcs' load='async' require='jquery' path='themes/default/js/mcs.js'}
<div class="mcs-container">
  <div class="filter-manager-popin">
    <div class="filter-manager-popin-container">
      <span class="gallery-icon-cancel filter-manager-close"></span>

      <div class="mcs-popin-title">{'Choose filters'|@translate}</div>

      <div class="filter-manager-controller-container">
        <label>
          <input data-wid='word' class="filter-manager-controller word" type="checkbox"/>
          <span class="mcs-icon gallery-icon-search">{'Search for words'|@translate}</span>
        </label>
        <label>
          <input data-wid='tag' class="filter-manager-controller tags" type="checkbox"/>
          <span class="mcs-icon gallery-icon-tag">{'Tag'|@translate}</span>
        </label>
        <label>
          <input data-wid='date_posted' class="filter-manager-controller date_posted" type="checkbox"/>
          <span class="mcs-icon gallery-icon-calendar-plus">{'Post date'|@translate}</span>
        </label>
        <label>
          <input data-wid='date_created' class="filter-manager-controller date_created" type="checkbox"/>
          <span class="mcs-icon gallery-icon-calendar">{'Creation date'|@translate}</span>
        </label>
        <label>
          <input data-wid='album' class="filter-manager-controller album" type="checkbox"/>
          <span class="mcs-icon gallery-icon-album">{'Album'|@translate}</span>
        </label>
        <label>
          <input data-wid='authors' class="filter-manager-controller author" type="checkbox"/>
          <span class="mcs-icon gallery-icon-user-edit">{'Author'|@translate}</span>
        </label>
        <label>
          <input data-wid='added_by' class="filter-manager-controller added_by" type="checkbox"/>
          <span class="mcs-icon gallery-icon-user">{'Added by'|@translate}</span>
        </label>
        <label>
          <input data-wid='filetypes' class="filter-manager-controller filetypes" type="checkbox"/>
          <span class="mcs-icon gallery-icon-file-image">{'File type'|@translate}</span>
        </label>
        <label>
          <input data-wid='ratios' class="filter-manager-controller ratios" type="checkbox"/>
          <span class="mcs-icon gallery-icon-crop">{'Ratio'|@translate}</span>
        </label>
{if $SHOW_FILTER_RATINGS and isset($SHOW_FILTER_RATINGS)}
        <label>
          <input data-wid='ratings' class="filter-manager-controller ratings" type="checkbox"/>
          <span class="mcs-icon gallery-icon-star-1">{'Rating'|@translate}</span>
        </label>
{/if}
        <label>
          <input data-wid='filesize' class="filter-manager-controller filesize" type="checkbox"/>
          <span class="mcs-icon gallery-icon-hdd">{'Filesize'|@translate}</span>
        </label>
        <label>
          <input data-wid='height' class="filter-manager-controller height" type="checkbox"/>
          <span class="mcs-icon gallery-icon-height">{'Height'|@translate}</span>
        </label>
        <label>
          <input data-wid='width' class="filter-manager-controller width" type="checkbox"/>
          <span class="mcs-icon gallery-icon-width">{'Width'|@translate}</span>
        </label>
      </div>

      <div class="filter-manager-actions">
        <div class="filter-cancel">
          {'Cancel'|@translate}
        </div>
        <div class="filter-validate">
          <i class="loading gallery-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="filter-manager">
    <span class="mcs-icon gallery-icon-selecters"></span>{'Choose filters'|@translate}
  </div>
  <i class="filter-spinner gallery-icon-spin6 animate-spin"></i>

   <div class="filter filter-word">
    <span class="mcs-icon gallery-icon-search filter-icon"></span>
    <span class="search-words"></span>
    <span class="filter-arrow gallery-icon-up-open"></span>

    <div class="filter-form filter-word-form">
      <div class="filter-form-title gallery-icon-search">{'Search for words'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon gallery-icon-trash">{'Delete'|@translate}</span>
        <span class="clear mcs-icon gallery-icon-arrow-rotate-left">{'Clear'|@translate}</span>
      </div>
      {* <span class="word-help"><i class="gallery-icon-help-circled"></i>Conseils de recherche</span> *}
      <div class="word-search-options">
        <label><input type="radio" name="mode" value="AND" checked> {'Search for all terms'|@translate}</label>
        <label><input type="radio" name="mode" value="OR"> {'Search for any term'|@translate}</label>
      </div>

      <input type="text" id="word-search" name="word">
      <span class="search-params-title">{'Search in :'|@translate}</span>
      <div class="search-params"> 
        <div>
          <input type="checkbox" id="name" name="name">
          <label for="name">{'Photo title'|@translate}</label>
        </div>
        <div>
          <input type="checkbox" id="file" name="file">
          <label for="file">{'File name'|@translate}</label>
        </div>
        <div>
          <input type="checkbox" id="author" name="author">
          <label for="author">{'Author'|translate}</label>
        </div>
        <div>
          <input type="checkbox" id="comment" name="comment">
          <label for="comment">{'Photo description'|@translate}</label>
        </div>
        <div>
          <input type="checkbox" id="tags" name="tags">
          <label for="tags">{'Tags'|@translate}</label>
        </div>
        <div>
          <input type="checkbox" id="cat-title" name="cat-title">
          <label for="cat-title">{'Album title'|@translate}</label>
        </div>
        <div>
          <input type="checkbox" id="cat-desc" name="cat-desc">
          <label for="cat-desc">{'Album description'|@translate}</label>
        </div>
      </div>
      <div class="filter-validate">
        <i class="loading gallery-icon-spin6 animate-spin"></i>
        <span class="validate-text">{'Validate'|@translate}</span>
      </div>
      </div>
    </div>
{if isset($TAGS)}
  <div class="filter filter-tag">
    <span class="mcs-icon gallery-icon-tag filter-icon"></span>
    <span class="search-words"></span>
    <span class="filter-arrow gallery-icon-up-open"></span>

    <div class="filter-form filter-tag-form">
      <div class="filter-form-title gallery-icon-tag">{'Tag'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon gallery-icon-trash">{'Delete'|@translate}</span>
        <span class="clear mcs-icon gallery-icon-arrow-rotate-left">{'Clear'|@translate}</span>
      </div>
      {if empty($TAGS)}
        <p class="no_filtered_photos">{'There are no tags available for the photos currently filtered'|translate}</p>
      {/if}
      <div class="search-params {if empty($TAGS)}mcs_hide{/if}"> 
        <div>
          <input type="radio" id="tag-all" name="tag_mode" value="AND" checked>
          <label for="tag-all">{'All tags'|@translate}</label>
        </div>
        <div>
          <input type="radio" id="tag-one" name="tag_mode" value="OR">
          <label for="tag-one">{'Any tag'|@translate}</label>
        </div>
      </div>
      <div class="form-container {if empty($TAGS)}mcs_hide{/if}">
        <select id="tag-search" placeholder="{'Type in a search term'|translate}" name="tags[]" multiple>
      {foreach from=$TAGS item=tag}
          <option value="{$tag.id}">{$tag.name} ({$tag.counter|translate_dec:'%d photo':'%d photos'})</option>
      {/foreach}
        </select>
        <div class="filter-validate">
          <i class="loading gallery-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
        </div>
      </div>
    </div>
  </div>
{/if}

{* For post date *}
  {if isset($DATE_POSTED) or isset($LIST_DATE_POSTED)}
  <div class="filter filter-date_posted">
    <span class="mcs-icon gallery-icon-calendar-plus filter-icon"></span>
    <span class="search-words">{'Post date'|@translate}</span>
    <span class="filter-arrow gallery-icon-up-open"></span>

    <div class="filter-form filter-date_posted-form">
      <div class="filter-form-title gallery-icon-calendar-plus">{'Post date'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon gallery-icon-trash" title="{'Delete'|@translate}"></span>
        <span class="clear mcs-icon gallery-icon-arrow-rotate-left" title="{'Clear'|@translate}"></span>
      </div>
      <div class="date_posted-option-container">
        <div class="preset_posted_date">
  {foreach from=$DATE_POSTED item=date_posted key=k}
          <div class="date_posted-option {if 0 == $date_posted.counter}disabled{/if}">
            {if 0 != $date_posted.counter}<input type="radio" id="date_posted-{$k}" value={$k} name="date_posted-period">{/if}
            <label for="date_posted-{$k}" id="{$k}">
              <span class="mcs-icon gallery-icon-checkmark checked-icon"></span>
              <span class="date-period">{$date_posted.label}</span>
              {if 0 != $date_posted.counter}<span class="date_posted-badge">{$date_posted.counter}</span>{/if}
            </label>
          </div>
  {/foreach}
          <div class="date_posted-option">
            <input type="radio" id="date_posted-custom" value="custom" name="date_posted-period">
            <label for="date_posted_custom" class="custom_posted_date_toggle">
              <span class="mcs-icon gallery-icon-checkmark checked-icon"></span>
              <span class="date-period">{'Custom dates'|translate}</span>
            </label>
          </div>
        </div>

        <div class="custom_posted_date">
    {foreach from=$LIST_DATE_POSTED key=y item=year}
          <div class="date_posted-option year" id="container_{$y}">
            <div class="year_input">
              <input type="checkbox" id="date_posted_{$y}" value='y{$y}' data-year="{$y}">
              <i class="gallery-icon-up-open accordion-toggle" data-type='year'></i>
              <label for="date_posted_{$y}" id="{$y}">
                <span class="date-period">{$year.label}</span>
                <span class="date_posted-badge">{$year.count}</span>
                <span class="mcs-icon gallery-icon-checkmark checked-icon"></span>
              </label>
            </div>

            <div class="months_container">
      {foreach from=$year.months key=m item=month}
              <div class="date_posted-option month" id="container_{$m}">
                <div class="month_input">
                  <input type="checkbox" id="date_posted_{$m}" value='m{$m}' data-year="{$y}">
                  <i class="gallery-icon-up-open accordion-toggle" data-type='month'></i>
                  <label for="date_posted_{$m}" id="{$m}">
                    <span class="date-period">{$month.label}</span>
                    <span class="date_posted-badge">{$month.count}</span>
                    <span class="mcs-icon gallery-icon-checkmark checked-icon"></span>
                  </label>
                </div>

                <div class="days_container">
       {foreach from=$month.days key=d item=day}
                  <div class="date_posted-option day" id="container_{$d}">
                    <input type="checkbox" id="date_posted_{$d}" value='d{$d}' data-year="{$y}">
                    <label for="date_posted_{$d}" id="{$d}">
                      <span class="date-period">{$day.label}</span>
                      <span class="date_posted-badge">{$day.count}</span>
                      <span class="mcs-icon gallery-icon-checkmark checked-icon"></span>
                    </label>
                  </div>
       {/foreach}
              </div>
            </div>

    {/foreach}
            </div>
          </div>
  {/foreach}
        </div>

      </div>
      <div>
        <div class="custom_posted_date custom_posted_date_toggle">
          <i class="gallery-icon-up-open"></i>
          <span>{'Previous'|translate}</span>
        </div>

        <div class="filter-validate">
          <i class="loading gallery-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
        </div>
      </div>
    </div>
  </div>
  {/if}

  
{* For creation date *}
{if isset($DATE_CREATED) or isset($LIST_DATE_CREATED)}
  <div class="filter filter-date_created">
    <span class="mcs-icon gallery-icon-calendar filter-icon"></span>
    <span class="search-words">{'Creation date'|@translate}</span>
    <span class="filter-arrow gallery-icon-up-open"></span>

    <div class="filter-form filter-date_created-form">

      <div class="filter-form-title gallery-icon-calendar">{'Creation date'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon gallery-icon-trash" title="{'Delete'|@translate}"></span>
        <span class="clear mcs-icon gallery-icon-arrow-rotate-left" title="{'Clear'|@translate}"></span>
      </div>
      {if empty($LIST_DATE_CREATED)}
        <p class="no_filtered_photos">{'There are no creation dates available for the photos currently filtered'|translate}</p>
      {else}

      <div class="date_created-option-container">
        <div class="preset_created_date">
  {foreach from=$DATE_CREATED item=date_created key=k}
          <div class="date_created-option {if 0 == $date_created.counter}disabled{/if}">
            {if 0 != $date_created.counter}<input type="radio" id="date_created-{$k}" value={$k} name="date_created-period">{/if}
            <label for="date_created-{$k}" id="{$k}">
              <span class="mcs-icon gallery-icon-checkmark checked-icon"></span>
              <span class="date-period">{$date_created.label}</span>
              {if 0 != $date_created.counter}<span class="date_created-badge">{$date_created.counter}</span>{/if}
            </label>
          </div>
  {/foreach}
          <div class="date_created-option">
            <input type="radio" id="date_created-custom" value="custom" name="date_created-period">
            <label for="date_created_custom" class="custom_created_date_toggle">
              <span class="mcs-icon gallery-icon-checkmark checked-icon"></span>
              <span class="date-period">{'Custom dates'|translate}</span>
            </label>
          </div>
        </div>

        <div class="custom_created_date">
  {if !empty($LIST_DATE_CREATED)}
    {foreach from=$LIST_DATE_CREATED key=y item=year}
          <div class="date_created-option year" id="container_{$y}">
            <div class="year_input">
              <input type="checkbox" id="date_created_{$y}" value='y{$y}' data-year="{$y}">
              <i class="gallery-icon-up-open accordion-toggle" data-type='year'></i>
              <label for="date_created_{$y}" id="{$y}">
                <span class="date-period">{$year.label}</span>
                <span class="date_created-badge">{$year.count}</span>
                <span class="mcs-icon gallery-icon-checkmark checked-icon"></span>
              </label>
            </div>

            <div class="months_container">
      {foreach from=$year.months key=m item=month}
              <div class="date_created-option month" id="container_{$m}">
                <div class="month_input">
                  <input type="checkbox" id="date_created_{$m}" value='m{$m}' data-year="{$y}">
                  <i class="gallery-icon-up-open accordion-toggle" data-type='month'></i>
                  <label for="date_created_{$m}" id="{$m}">
                    <span class="date-period">{$month.label}</span>
                    <span class="date_created-badge">{$month.count}</span>
                    <span class="mcs-icon gallery-icon-checkmark checked-icon"></span>
                  </label>
                </div>

                <div class="days_container">
        {foreach from=$month.days key=d item=day}
                  <div class="date_created-option day" id="container_{$d}">
                    <input type="checkbox" id="date_created_{$d}" value='d{$d}' data-year="{$y}">
                    <label for="date_created_{$d}" id="{$d}">
                      <span class="date-period">{$day.label}</span>
                      <span class="date_created-badge">{$day.count}</span>
                      <span class="mcs-icon gallery-icon-checkmark checked-icon"></span>
                    </label>
                  </div>
      {/foreach}
              </div>
            </div>

      {/foreach}
            </div>
          </div>
    {/foreach}
  {/if}
        </div>
      </div>
      <div>
        <div class="custom_created_date custom_created_date_toggle">
          <i class="gallery-icon-up-open"></i>
          <span>{'Previous'|translate}</span>
        </div>

        <div class="filter-validate">
          <i class="loading gallery-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
        </div>
      </div>
{/if}
    </div>
  
  </div>
  {/if}

  <div class="filter filter-album">
    <span class="mcs-icon gallery-icon-album filter-icon"></span>
    <span class="search-words"></span>
    <span class="filter-arrow gallery-icon-up-open"></span>

    <div class="filter-form filter-album-form">
    <div class="filter-form-title gallery-icon-album"> {'Album'|@translate}</div>
    <div class="filter-actions"> 
      <span class="delete mcs-icon gallery-icon-trash">{'Delete'|@translate}</span>
      <span class="clear mcs-icon gallery-icon-arrow-rotate-left">{'Clear'|@translate}</span>
    </div>
      <div class="search-params"> 
      </div>
      <div class="selected-categories-container">
      </div>
      <div class="add-album-button">
        <label class="head-button-2 icon-add-album">
          <p class="mcs-icon gallery-icon-plus-circled">{'Add Album'|@translate}</p>
        </label>
      </div>
      <div class="search-sub-cats">
        <input type="checkbox" id="search-sub-cats" name="search-sub-cats">
        <label for="search-sub-cats">{'Search in sub-albums'|@translate}</label>
      </div>
      <div class="filter-validate">
        <i class="loading gallery-icon-spin6 animate-spin"></i>
        <span class="validate-text">{'Validate'|@translate}</span>
      </div>
    </div>
  </div>
  {include file='admin/themes/default/template/include/album_selector.inc.tpl'}

  {if isset($AUTHORS)}
  <div class="filter filter-authors">
    <span class="mcs-icon gallery-icon-user-edit filter-icon"></span>
    <span class="search-words"></span>
    <span class="filter-arrow gallery-icon-up-open"></span>
    
    <div class="filter-form filter-author-form">
      <div class="filter-form-title gallery-icon-user-edit"> {'Author'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon gallery-icon-trash">{'Delete'|@translate}</span>
        <span class="clear mcs-icon gallery-icon-arrow-rotate-left">{'Clear'|@translate}</span>
      </div>
      {if empty($AUTHORS)}
        <p class="no_filtered_photos">{'There are no authors available for the photos currently filtered'|translate}</p>
      {/if}
    <div class="form-container {if empty($AUTHORS)}mcs_hide{/if}">
        <select id="authors" placeholder="{'Type in a search term'|translate}" name="authors[]" multiple>
        {foreach from=$AUTHORS item=author}
          <option value="{$author.author|strip_tags:false|escape:html}">{$author.author|strip_tags:false} ({$author.counter|translate_dec:'%d photo':'%d photos'})</option>
        {/foreach}
        </select>

        <div class="filter-validate">
          <i class="loading gallery-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
        </div>
      </div>
    </div>
  </div>
  {/if}

  {if isset($ADDED_BY)}
  <div class="filter filter-added_by">
    <span class="mcs-icon gallery-icon-user filter-icon"></span>
    </span><span class="search-words"></span>
    <span class="filter-arrow gallery-icon-up-open"></span>

    <div class="filter-form filter-added_by-form">
      <div class="filter-form-title gallery-icon-user">{'Added by'|translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon gallery-icon-trash tiptip" title="{'Delete'|@translate}"></span>
        <span class="clear mcs-icon gallery-icon-arrow-rotate-left tiptip" title="{'Clear'|@translate}"></span>
      </div>

      <div class="form-container">
        <div class="added_by-option-container">
        {foreach from=$ADDED_BY item=added_by key=k}
          <div class="added_by-option">
              <input type="checkbox" id="added_by-{$added_by.added_by_id}" name="{$added_by.added_by_id}">
              <label for="added_by-{$added_by.added_by_id}">
                <span class="mcs-icon gallery-icon-checkmark checked-icon"></span>
                <span class="added_by-name">{$added_by.added_by_name|strip_tags:false}</span>
                <span class="added_by-badge">{$added_by.counter}</span>
              </label>
            </div>
        {/foreach}
        </div>
      </div>
      <div class="filter-validate">
        <i class="loading gallery-icon-spin6 animate-spin"></i>
        <span class="validate-text">{'Validate'|@translate}</span>
      </div>
    </div>
  </div>
  {/if}

  {if isset($FILETYPES)}
  <div class="filter filter-filetypes">
    <span class="mcs-icon gallery-icon-file-image filter-icon"></span>
    </span><span class="search-words"></span>
    <span class="filter-arrow gallery-icon-up-open"></span>

    <div class="filter-form filter-filetypes-form">
      <div class="filter-form-title gallery-icon-file-image">{'File type'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon gallery-icon-trash tiptip" title="{'Delete'|@translate}"></span>
        <span class="clear mcs-icon gallery-icon-arrow-rotate-left tiptip" title="{'Clear'|@translate}"></span>
      </div>
      <div class="form-container">
        <div class="filetypes-option-container">
        {foreach from=$FILETYPES item=filetypes key=k}
          <div class="filetypes-option {if 0 == $filetypes}disabled{/if}">
          <input type="checkbox" id="filetype-{$k}" name="{$k}" {if 0 == $filetypes}disabled{/if}>
              <label for="filetype-{$k}">
                <span class="mcs-icon gallery-icon-checkmark checked-icon"></span>
                <span class="ext-name">{$k}</span>
                {if 0 != $filetypes}<span class="ext-badge">{$filetypes}</span>{/if}
              </label>
            </div>
        {/foreach}
        </div>
      </div>
      <div class="filter-validate">
        <i class="loading gallery-icon-spin6 animate-spin"></i>
        <span class="validate-text">{'Validate'|@translate}</span>
      </div>
    </div>
  </div>
  {/if}

  
  {if isset($RATIOS)}
  <div class="filter filter-ratios">
    <span class="mcs-icon gallery-icon-crop filter-icon"></span>
    </span><span class="search-words"></span>
    <span class="filter-arrow gallery-icon-up-open"></span>

    <div class="filter-form filter-ratios-form">
      <div class="filter-form-title gallery-icon-crop">{'Ratio'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon gallery-icon-trash tiptip" title="{'Delete'|@translate}"></span>
        <span class="clear mcs-icon gallery-icon-arrow-rotate-left tiptip" title="{'Clear'|@translate}"></span>
      </div>
      <div class="form-container">
        <div class="ratios-option-container">
        {foreach from=$RATIOS item=ratio key=k}
        <div class="ratios-option {if 0 == $ratio}disabled{/if}">
              <input type="checkbox" id="ratio-{$k}" name="{$k}" {if 0 == $ratio}disabled{/if}>
              <label for="ratio-{$k}">
                <span class="mcs-icon gallery-icon-checkmark checked-icon"></span>
                <span class="ratio-name">{$k|translate}</span>
              {if 0 != $ratio}<span class="ratio-badge">{$ratio}</span>{/if}
              </label>
            </div>
        {/foreach}
        </div>
      </div>
      <div class="filter-validate">
        <i class="loading gallery-icon-spin6 animate-spin"></i>
        <span class="validate-text">{'Validate'|@translate}</span>
      </div>
    </div>
  </div>
  {/if}

  {* Add filter for rating *}
  {if $SHOW_FILTER_RATINGS and isset($RATING)}
  <div class="filter filter-ratings">
    <span class="mcs-icon mcs-icon gallery-icon-star-1 filter-icon"></span>
    </span><span class="search-words"></span>
    <span class="filter-arrow gallery-icon-up-open"></span>

    <div class="filter-form filter-ratings-form">
      <div class="filter-form-title gallery-icon-star-1">{'Rating'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon gallery-icon-trash tiptip" title="{'Delete'|@translate}"></span>
        <span class="clear mcs-icon gallery-icon-arrow-rotate-left tiptip" title="{'Clear'|@translate}"></span>
      </div>
      <div class="form-container">

        <div class="ratings-option-container">
          <form>
          {foreach from=$RATING item=rating key=k}
            
          <div class="ratings-option {if 0 == $rating}disabled{/if}">
            <input type="checkbox" id="rating-{$k}" name="{if 0 == $k}0{else}{$k}{/if}" {if 0 == $rating}disabled{/if}>
            <label for="rating-{$k}">
              <span class="mcs-icon gallery-icon-checkmark checked-icon"></span>
              <span class="ratings-name">{if 0 == $k}{'no rate'|translate}{else}{'between %d and %d'|@translate:(intval($k)-1):$k|escape:'javascript'}{/if}</span>
              {if 0 != $rating}<span class="ratings-badge">{$rating}</span>{/if}
            </label>
          </div>

          {/foreach}

          </form>
        </div>
      </div>
      <div class="filter-validate">
        <i class="loading gallery-icon-spin6 animate-spin"></i>
        <span class="validate-text">{'Validate'|@translate}</span>
      </div>
    </div>
    
  </div>
  {/if}


{* Add filter for filesize *}
{if isset($FILESIZE)}
  <div class="filter filter-filesize">
    <span class="mcs-icon mcs-icon gallery-icon-hdd filter-icon"></span>
    </span><span class="search-words"></span>
    <span class="filter-arrow gallery-icon-up-open"></span>

    <div class="filter-form filter-filesize-form">
      <div class="filter-form-title mcs-icon gallery-icon-hdd">{'Filesize'|translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon gallery-icon-trash tiptip" title="{'Delete'|@translate}"></span>
        <span class="clear mcs-icon gallery-icon-arrow-rotate-left tiptip" data-min="{$FILESIZE.bounds.min}" data-max="{$FILESIZE.bounds.max}" title="{'Clear'|@translate}"></span>
      </div>

      <div class="form-container">
        <div class="filesize-option-container">

          <div class="slider_input">
            <div class="min_input">
              <p>Min</p>
              <input type="number" step=".1" min="0" name="filter_filesize_min_text" value="{$FILESIZE.selected.min}" disabled>
            </div>
            <div class="max_input">
              <p>Max</p>
              <input type="number" step=".1" min="0" name="filter_filesize_max_text" value="{$FILESIZE.selected.max}" disabled>
            </div>
          </div>

          <div data-slider="filesizes">
            <span class="slider-info"></span>
            <div class="slider-slider"></div>

            <input type="hidden" data-input="min" name="filter_filesize_min" value="{$FILESIZE.selected.min}">
            <input type="hidden" data-input="max" name="filter_filesize_max" value="{$FILESIZE.selected.max}">
          </div>

        </div>
      </div>
      <div class="filter-validate">
        <i class="loading gallery-icon-spin6 animate-spin"></i>
        <span class="validate-text">{'Validate'|@translate}</span>
      </div>
    </div>
  </div>
{/if}

{* Add filter for Height *}
{if isset($HEIGHT)}
  <div class="filter filter-height">
    <span class="mcs-icon mcs-icon gallery-icon-height"></span>
      <span class="search-words"></span>
      <span class="filter-arrow gallery-icon-up-open"></span>
      <div class="filter-form filter-height-form">
        <div  class="filter-form-title mcs-icon gallery-icon-heigh">
          {'height'|translate}
        </div>
        <div class="filter-actions">
          <span class="delete mcs-icon gallery-icon-trash tiptip" title="{'Delete'|@translate}"></span>
          <span class="clear mcs-icon gallery-icon-arrow-rotate-left tiptip" data-min="{$HEIGHT.bounds.min}" data-max="{$HEIGHT.bounds.max}" title="{'Reset'|@translate}"></span>
        </div>
        <div class="form-container">

          <div class="height-option-container">
            <div data-slider="heights">
              <span class="slider-info"></span>
              <div class="slider-slider"></div>

              <input type="hidden" data-input="min" name="filter_height_min" value="{$HEIGHT.selected.min}">
              <input type="hidden" data-input="max" name="filter_height_max" value="{$HEIGHT.selected.max}">
            </div>
          </div>

        </div>
      <div class="filter-validate">
          <i class="loading gallery-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
      </div>
    </div>
  </div>
{/if}

{* Add filter for Width *} 
{if  isset($WIDTH)}
  <div class="filter filter-width">
    <span class="mcs-icon mcs-icon gallery-icon-width"></span>
      <span class="search-words"></span>
      <span class="filter-arrow gallery-icon-up-open"></span>
      <div class="filter-form filter-width-form">
        <div  class="filter-form-title mcs-icon gallery-icon-width">
          {'width'|translate}
        </div>
        <div class="filter-actions">
          <span class="delete mcs-icon gallery-icon-trash tiptip" title="{'Delete'|@translate}"></span>
         <span  class="clear mcs-icon gallery-icon-arrow-rotate-left tiptip"  title="{'Clear'|@translate}"></span>
        </div>
        <div class="form-container">
          
        <div class="width-option-container">
            <div data-slider="widths">
              <span class="slider-info"></span>
              <div class="slider-slider"></div>

              <input type="hidden" data-input="min" name="filter_width_min" value="{$WIDTH.selected.min}">
              <input type="hidden" data-input="max" name="filter_width_max" value="{$WIDTH.selected.max}">
            </div>
          </div>
          
        </div>
      <div class="filter-validate">
          <i class="loading gallery-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
      </div>
    </div>
  </div>
{/if}


  <div>
    <span class="mcs-icon gallery-icon-arrow-rotate-left clear-all">{'Empty filters'|@translate}</span>
  </div>
</div>

{if isset($TAGS_FOUND) or isset($ALBUMS_FOUND)}
<div class="mcs-side-results">
  {if isset($TAGS_FOUND)}
  <div class="mcs-tags-found">
    <span class="mcs-side-badge">{count($TAGS_FOUND)}</span>
    <p>{'Tags found'|@translate}</p>
  </div>
  {/if}
  {if isset($ALBUMS_FOUND)}
  <div class="mcs-albums-found">
    <span class="mcs-side-badge">{count($ALBUMS_FOUND)}</span>
    <p>{'Albums found'|@translate}</p>
  </div>
  {/if}
</div>

  {if isset($TAGS_FOUND)}
<div class="tags-found-popin">
  <div class="tags-found-popin-container">
    <span class="gallery-icon-cancel tags-found-close"></span>
    <div class="mcs-popin-title">{'Tags found'|@translate}</div>
    <div class="mcs-popin-desc">{'Tags listed here match your search by word. Click on one to browse by tag.'|translate}</div>
    <div class="tags-found-container">
    {foreach from=$TAGS_FOUND item=tag_path key=k}
      <div class="tag-item">
        {$tag_path}
      </div>
    {/foreach}
    </div>
  </div>
</div>
  {/if}
  {if isset($ALBUMS_FOUND)}
<div class="albums-found-popin">
  <div class="albums-found-popin-container">
    <span class="gallery-icon-cancel albums-found-close"></span>
    <div class="mcs-popin-title">{'Albums found'|@translate}</div>
    <div class="mcs-popin-desc">{'Albums listed here match your search by word. Click on one to browse by album.'|translate}</div>
    <div class="albums-found-container">
      {foreach from=$ALBUMS_FOUND item=album_path key=k}
        <div class="album-item">
          {$album_path}
        </div>
      {/foreach}
    </div>
  </div>
</div>
  {/if}
{/if}
