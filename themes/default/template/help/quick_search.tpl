{combine_css path="themes/default/css/help/quick_search.css"}

<div class="quick-search-headers">
  <span class="mcs-popin-title">{"Search using extended syntax"|translate}</span>
</div>
<div class="quick-search-infos">
  <p>
    {"The quick search engine allows you to use boolean operators to refine your search. By default, the search applies to all keywords. Searches are not case sensitive."|translate}
  </p>
  <p>{"Here is a list of actions you can perform:"|translate}</p>
</div>

<p class="quick-search-section">{"With keywords"|translate}</p>

<div class="quick-search-items{if $is_dark_mode} dark {/if}">
  {* quoted keyword / phrase *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>{"Exact search"|translate}</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>"</b>george washington<b>"</b></p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      <p>{"Use quotes to search for an exact keyword or phrase."|translate}</p>
    </div>
  </div>

  {* OR inclusive *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>{"OR inclusive"|translate}</p>
      </div>
      <div class="quick-search-item-example">
        <p>john <b>OR</b> bill</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      <p>{"Add an OR between words."|translate}</p>
    </div>
  </div>

  {* exclude *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>{"Exclude"|translate}</p>
      </div>
      <div class="quick-search-item-example">
        <p>george washington <b>NOT</b> bush</p>
        <p><b>-</b>george</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      {"Add a dash (-) or NOT before a word to exclude from search. Note that NOT acts as a filtering operator so you cannot have a search containing only NOT operators. You cannot combine OR with NOT (john OR NOT bill is not valid)"|translate}
    </div>
  </div>

  {* OR inclusive *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>{"Grouping"|translate} ()</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>(</b>mother OR father<b>)</b> AND <b>(</b>daugther OR son<b>)</b></p>
      </div>
    </div>
  </div>
</div>

<p class="quick-search-section" style="padding-top: 30px;">{"Others"|translate}</p>
<div class="quick-search-items{if $is_dark_mode} dark {/if}">
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>{"Supported numeric operators"|translate}</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>=100</b> {"equals 100"|translate}</p>
        <p><b>&gt;100</b> {"greater than 100"|translate}</p>
        <p><b>&lt;100</b> {"less than 100"|translate}</p>
        <p><b>10..100</b> {"between 10 and 100 (inclusive)"|translate}</p>
        <p><b>..100</b> {"up to 100 (inclusive)"|translate}</p>
        <p><b>100..</b> {"from 100 and above"|translate}</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      {"These operators can be used with numeric fields such as"|translate} created:, posted:, width:, height:, size:, ratio:, hits:, score:, filesize:, id:.
    </div>
  </div>

  {* tags: *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>tag:</p>
        <p>tags:</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>tag:</b>(john OR bill)</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      {"Searches only in tag names without looking at photo titles or descriptions."|translate}
    </div>
  </div>

  {* photos: *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>photo:</p>
        <p>photos:</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>photo:</b>Tiger</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      {"Searches only for photos with the given words in title or description."|translate}
    </div>
  </div>

  {* files: *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>file:</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>file:</b>DSC_</p>
        <p><b>file:</b>*.webp</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      {"Searches by file name."|translate}
    </div>
  </div>

  {* author: *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>author:</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>author:</b>John</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      {"Searches by author."|translate}
    </div>
  </div>

  {* created: taken: shot: *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>created:</p>
        <p>taken:</p>
        <p>shot:</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>taken:2003</b> {"photos taken in 2003"|translate}</p>
        <p><b>taken:20035, taken:2003-5, taken:2003-05</b> {"photos from may 2003"|translate}</p>
        <p><b>taken:2003..2008</b> {"photos from 2003 to 2008"|translate}</p>
        <p><b>taken:>2008, taken:2008*, taken:2008..</b> {"photos after Jan 1st 2008"|translate}</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      {"Searches photos by taken date."|translate}
    </div>
  </div>

  {* posted: *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>posted:</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>posted:2003</b> {"photos posted in 2003"|translate}</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      {"Searches photos by posted date."|translate} {"Same principle as for"|translate} created:.
    </div>
  </div>

  {* width: height: *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>width:</p>
        <p>height:</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>width:</b>>500 {"return photos wider than 500px"|translate}</p>
        <p><b>height:</b>&lt;700 {"return photos less than 700px high"|translate}</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      {"Searches photos with a given width or height."|translate}
    </div>
  </div>

  {* size: *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>size:</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>size:</b>5m {"returns photos of 5 megapixels"|translate}</p>
        <p><b>size:</b>>12m {"returns photos of 12 megapixels or more"|translate}</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      {"Searches photos by size in pixels."|translate}
    </div>
  </div>

  {* ratio: *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>ratio:</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>ratio:</b>3/4 OR <b>ratio:</b>4/3
          {"finds photos from compact cameras in portrait or landscape modes"|translate}</p>
        <p><b>ratio:</b>>16/9 {"finds panoramas"|translate}</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      {"Searches photos by width/height ratio."|translate}
    </div>
  </div>

  {* hits: *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>hits:</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>hits:</b>>1000 {"return photos with at least 1,000 views"|translate}</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      <p>{"Searches for photos by number of views."|translate}</p>
    </div>
  </div>

  {* score: rating:*}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>score:</p>
        <p>rating:</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>score:</b>* {"will give you all photos with at least one note."|translate}</p>
        <p><b>score:</b> {"will give you photos without notes."|translate}</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      <p>{"Searches photos by rating."|translate}</p>
    </div>
  </div>

  {* filesize: *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>filesize:</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>filesize:</b>1m..10m {"finds files between 1MB and 10MB."|translate}</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      {"Searches photos by file size."|translate}
    </div>
  </div>

  {* id: *}
  <div class="quick-search-item">
    <div class="quick-search-item-info">
      <div class="quick-search-item-type">
        <p>id:</p>
      </div>
      <div class="quick-search-item-example">
        <p><b>id:</b>123..126
          {"finds photo 123 to 126 (it may find between 0 and 4 photos, because photos can be deleted)"|translate}</p>
      </div>
    </div>
    <div class="quick-search-item-tip">
      {"Searches photos by its numeric identifier in Piwigo."|translate}
    </div>
  </div>
</div>