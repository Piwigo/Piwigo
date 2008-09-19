{* 

   Copied from mainpage_categories.tpl 

*}
{if !empty($category_thumbnails)}
  {html_head}
<style media="screen,handheld,projection,tv" type="text/css">
#content ul.thumbnailCategories li {ldelim} width:100%; z-index: 55; position: relative;}
#Titling h3 {ldelim} margin:11px 0 -13px; padding:0 3px 0 20px; text-align:left; z-index: 99; position: relative;}
#Titling h3 a {ldelim} background-color: #222; padding: 0 10px; }
#content ul.thumbnailCategories .unbordered {ldelim} width:49%; float:left; margin:0 0 0 5px; }
.content div.thumbnailCategory div.description p.dates {ldelim} margin: 0 45px 0 20px; }
#content .thumbnailCategory div.description .text {ldelim} margin: 0; padding: 0 4px; text-align: justify; }
p.Nb_images {ldelim} text-align: left; color: #444; }
/* hacks */
*+html #Titling h3, * html #Titling h3 {ldelim} font-weight: normal;} /* IE browsers */
</style>
  {/html_head}
  <ul class="thumbnailCategories" id="Titling"> {*                   1st difference: Titling/thumbnail *}
    {foreach from=$category_thumbnails item=cat}
    <div class="unbordered"> {*                                                   W3C HTML non conform *}
    <h3> {*                                               2nd difference: h3 is outside of description *}
      <a href="{$cat.URL}">{$cat.NAME}</a>{$cat.ICON_TS}
    </h3>
    <li>
      <div class="thumbnailCategory">
       <div class="illustration">
          <a href="{$cat.URL}">
            <img src="{$cat.TN_SRC}" alt="{$cat.ALT}" 
            title="{'hint_category'|@translate}">
          </a>
        </div>
        <div class="description">
  				{if isset($cat.INFO_DATES) }
    				<p class="dates">{$cat.INFO_DATES}</p>
  				{/if}
          <div class="text">
          <p class="Nb_images">{$cat.CAPTION_NB_IMAGES}</p>
    				{if not empty($cat.DESCRIPTION)}
    				<p>{$cat.DESCRIPTION}</p>
    				{/if}
          </div>
        </div>
      </div>
    </li>
    </div>    
    {/foreach}
  </ul>
{/if}