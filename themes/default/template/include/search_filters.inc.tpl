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

str_empty_search_top_alt = "{'Fill in the filters to start a search'|@translate|escape:javascript}";
str_empty_search_bot_alt = "{'Pre-established filters are proposed, but you can add or remove them using the "Choose filters" button.'|@translate|escape:javascript}";

{/footer_script}

{combine_script id='mcs' load='async' require='jquery' path='themes/default/js/mcs.js'}
<div class="mcs-container">
  <div class="filter-manager-popin">
    <div class="filter-manager-popin-container">
      <span class="pwg-icon-cancel filter-manager-close"></span>

      <div class="mcs-popin-title">{'Choose filters'|@translate}</div>

      <div class="filter-manager-controller-container">
        <label>
          <input data-wid='word' class="filter-manager-controller word" type="checkbox"/>
          <span class="mcs-icon pwg-icon-search">{'Search for words'|@translate}</span>
        </label>
        <label>
          <input data-wid='tag' class="filter-manager-controller tags" type="checkbox"/>
          <span class="mcs-icon pwg-icon-tag">{'Tag'|@translate}</span>
        </label>
        <label>
          <input data-wid='date_posted' class="filter-manager-controller date_posted" type="checkbox"/>
          <span class="mcs-icon pwg-icon-calendar-plus">{'Post date'|@translate}</span>
        </label>
        <label>
          <input data-wid='album' class="filter-manager-controller album" type="checkbox"/>
          <span class="mcs-icon pwg-icon-album">{'Album'|@translate}</span>
        </label>
        <label>
          <input data-wid='authors' class="filter-manager-controller author" type="checkbox"/>
          <span class="mcs-icon pwg-icon-user-edit">{'Author'|@translate}</span>
        </label>
        <label>
          <input data-wid='added_by' class="filter-manager-controller added_by" type="checkbox"/>
          <span class="mcs-icon pwg-icon-user">{'Added by'|@translate}</span>
        </label>
        <label>
          <input data-wid='filetypes' class="filter-manager-controller filetypes" type="checkbox"/>
          <span class="mcs-icon pwg-icon-file-image">{'File type'|@translate}</span>
        </label>
      </div>

      <div class="filter-manager-actions">
        <div class="filter-cancel">
          {'Cancel'|@translate}
        </div>
        <div class="filter-validate">
          <i class="loading pwg-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="filter-manager">
    <span class="mcs-icon pwg-icon-selecters"></span>{'Choose filters'|@translate}
  </div>
  <i class="filter-spinner pwg-icon-spin6 animate-spin"></i>

   <div class="filter filter-word">
    <span class="mcs-icon pwg-icon-search filter-icon"></span>
    <span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-word-form">
      <div class="filter-form-title">{'Search for words'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon pwg-icon-trash">{'Delete'|@translate}</span>
        <span class="clear mcs-icon pwg-icon-broom">{'Clear'|@translate}</span>
      </div>
      {* <span class="word-help"><i class="pwg-icon-help-circled"></i>Conseils de recherche</span> *}
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
        <i class="loading pwg-icon-spin6 animate-spin"></i>
        <span class="validate-text">{'Validate'|@translate}</span>
      </div>
      </div>
    </div>
  <div class="filter filter-tag">
    <span class="mcs-icon pwg-icon-tag filter-icon"></span>
    <span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-tag-form">
      <div class="filter-form-title">{'Tag'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon pwg-icon-trash">{'Delete'|@translate}</span>
        <span class="clear mcs-icon pwg-icon-broom">{'Clear'|@translate}</span>
      </div>
      <div class="search-params"> 
        <div>
          <input type="radio" id="tag-all" name="tag_mode" value="AND" checked>
          <label for="tag-all">{'All tags'|@translate}</label>
        </div>
        <div>
          <input type="radio" id="tag-one" name="tag_mode" value="OR">
          <label for="tag-one">{'Any tag'|@translate}</label>
        </div>
      </div>
      <div class="form-container">
        <select id="tag-search" placeholder="{'Type in a search term'|translate}" name="tags[]" multiple>
      {foreach from=$TAGS item=tag}
          <option value="{$tag.id}">{$tag.name} ({$tag.counter|translate_dec:'%d photo':'%d photos'})</option>
      {/foreach}
        </select>
        <div class="filter-validate">
          <i class="loading pwg-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="filter filter-date_posted">
    <span class="mcs-icon pwg-icon-calendar-plus filter-icon"></span>
    <span class="search-words">{'Post date'|@translate}</span>
    <span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-date_posted-form">
      <div class="filter-form-title">{'Post date'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon pwg-icon-trash" title="{'Delete'|@translate}"></span>
        <span class="clear mcs-icon pwg-icon-broom" title="{'Clear'|@translate}"></span>
      </div>

      <div class="date_posted-option-container">
        {foreach from=$DATE_POSTED item=date_posted key=k}
          <div class="date_posted-option">
            <input type="radio" id="date_posted-{$k}" value={$k} name="date_posted-period">
            <label for="date_posted-{$k}" id="{$k}">
              <span class="mcs-icon pwg-icon-checkmark checked-icon"></span>
              <span class="date-period">{$date_posted.label}</span>
              <span class="date_posted-badge">{$date_posted.counter}</span>
            </label>
          </div>
        {/foreach}
      </div>
      <div class="filter-validate">
        <i class="loading pwg-icon-spin6 animate-spin"></i>
        <span class="validate-text">{'Validate'|@translate}</span>
      </div>
    </div>
  </div>
  <div class="filter filter-album">
    <span class="mcs-icon pwg-icon-album filter-icon"></span>
    <span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-album-form">
    <div class="filter-form-title">{'Album'|@translate}</div>
    <div class="filter-actions"> 
      <span class="delete mcs-icon pwg-icon-trash">{'Delete'|@translate}</span>
      <span class="clear mcs-icon pwg-icon-broom">{'Clear'|@translate}</span>
    </div>
      <div class="search-params"> 
      </div>
      <div class="selected-categories-container">
      </div>
      <div class="add-album-button">
        <label class="head-button-2 icon-add-album">
          <p class="mcs-icon pwg-icon-plus-circled">{'Add Album'|@translate}</p>
        </label>
      </div>
      <div class="search-sub-cats">
        <input type="checkbox" id="search-sub-cats" name="search-sub-cats">
        <label for="search-sub-cats">Search in sub-albums</label>
      </div>
      <div class="filter-validate">
        <i class="loading pwg-icon-spin6 animate-spin"></i>
        <span class="validate-text">{'Validate'|@translate}</span>
      </div>
    </div>
  </div>
  {include file='admin/themes/default/template/include/album_selector.inc.tpl' 
    title={'Search in albums'|@translate}
    searchPlaceholder={'Search'|@translate}
    show_root_btn=false
    api_method='pwg.categories.getList'
  }
  {if isset($AUTHORS)}
  <div class="filter filter-authors">
    <span class="mcs-icon pwg-icon-user-edit filter-icon"></span>
    <span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>
    
    <div class="filter-form filter-author-form">
      <div class="filter-form-title">{'Author'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon pwg-icon-trash">{'Delete'|@translate}</span>
        <span class="clear mcs-icon pwg-icon-broom">{'Clear'|@translate}</span>
      </div>
      <div class="form-container">
        <select id="authors" placeholder="{'Type in a search term'|translate}" name="authors[]" multiple>
        {foreach from=$AUTHORS item=author}
          <option value="{$author.author|strip_tags:false|escape:html}">{$author.author|strip_tags:false} ({$author.counter|translate_dec:'%d photo':'%d photos'})</option>
        {/foreach}
        </select>

        <div class="filter-validate">
          <i class="loading pwg-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
        </div>
      </div>
    </div>
  </div>
  {/if}

  {if isset($ADDED_BY)}
  <div class="filter filter-added_by">
    <span class="mcs-icon pwg-icon-user filter-icon"></span>
    </span><span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-added_by-form">
      <div class="filter-form-title">{'Added by'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon pwg-icon-trash">{'Delete'|@translate}</span>
        <span class="clear mcs-icon pwg-icon-broom">{'Clear'|@translate}</span>
      </div>
      <div class="form-container">
        <select id="added_by" placeholder="{'Type in a search term'|translate}" name="added_by[]" multiple>
        {foreach from=$ADDED_BY item=added_by}
          <option value="{$added_by.added_by_id|strip_tags:false|escape:html}">{$added_by.added_by_name|strip_tags:false}<span class="badge">({$added_by.counter|translate_dec:'%d photo':'%d photos'})</span></option>
        {/foreach}
        </select>
        <div class="filter-validate">
          <i class="loading pwg-icon-spin6 animate-spin"></i>
          <span class="validate-text">{'Validate'|@translate}</span>
        </div>
      </div>
    </div>
  </div>
  {/if}

  {if isset($FILETYPES)}
  <div class="filter filter-filetypes">
    <span class="mcs-icon pwg-icon-file-image filter-icon"></span>
    </span><span class="search-words"></span>
    <span class="filter-arrow pwg-icon-up-open"></span>

    <div class="filter-form filter-filetypes-form">
      <div class="filter-form-title">{'File type'|@translate}</div>
      <div class="filter-actions"> 
        <span class="delete mcs-icon pwg-icon-trash tiptip" title="{'Delete'|@translate}"></span>
        <span class="clear mcs-icon pwg-icon-broom tiptip" title="{'Clear'|@translate}"></span>
      </div>
      <div class="form-container">
        <div class="filetypes-option-container">
        {foreach from=$FILETYPES item=filetypes key=k}
          <div class="filetypes-option">
              <input type="checkbox" id="filetype-{$k}" name="{$k}">
              <label for="filetype-{$k}">
                <span class="mcs-icon pwg-icon-checkmark checked-icon"></span>
                <span class="ext-name">{$k}</span>
                <span class="ext-badge">{$filetypes}</span>
              </label>
            </div>
        {/foreach}
        </div>
      </div>
      <div class="filter-validate">
        <i class="loading pwg-icon-spin6 animate-spin"></i>
        <span class="validate-text">{'Validate'|@translate}</span>
      </div>
    </div>
  </div>
  {/if}
  <div>
    <span class="mcs-icon pwg-icon-broom clear-all">{'Empty filters'|@translate}</span>
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
    <span class="pwg-icon-cancel tags-found-close"></span>
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
    <span class="pwg-icon-cancel albums-found-close"></span>
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
