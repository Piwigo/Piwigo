<!-- $Id$ -->
<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
      <li><a href="{U_HOME}" title="{lang:return to homepage}" rel="home"><img src="{themeconf:icon_dir}/home.png" class="button" alt="{lang:home}"/></a></li>
    </ul>
    <h2>{lang:Search}</h2>
  </div>

<form class="filter" method="post" name="search" action="{S_SEARCH_ACTION}">
<!-- BEGIN errors -->
<div class="errors">
  <ul>
    <!-- BEGIN error -->
    <li>{errors.error.ERROR}</li>
    <!-- END error -->
  </ul>
</div>
<!-- END errors -->
<fieldset>
  <legend>{lang:Filter}</legend>
  <label>{lang:search_keywords}<input type="text" style="width: 300px" name="search_allwords" size="30" /></label>
  <label>
    <span><input type="radio" name="mode" value="AND" checked="checked" /> {lang:search_mode_and}</span>
    <span><input type="radio" name="mode" value="OR" /> {lang:search_mode_or}</span>
  </label>
  <label>{lang:search_author}<input type="text" style="width: 300px" name="search_author" size="30" /></label>
</fieldset>
<fieldset>
  <legend>{lang:Search tags}</legend>
  {TAG_SELECTION}
  <label><span><input type="radio" name="tag_mode" value="AND" checked="checked" /> {lang:All tags}</span></label>
  <label><span><input type="radio" name="tag_mode" value="OR" /> {lang:Any tag}</span></label>
</fieldset>

<fieldset>
  <legend>{lang:search_date}</legend>
  <label>{lang:search_date_type}
    <span>
      <input type="radio" name="date_type" value="date_creation" checked="checked" />{lang:Creation date}
    </span>
    <span>
      <input type="radio" name="date_type" value="date_available" />{lang:Post date}
    </span>
  </label>
  <label>{lang:search_date_from}
    <span>
      <select name="start_day">
        <!-- BEGIN start_day -->
        <option {start_day.SELECTED} value="{start_day.VALUE}">{start_day.OPTION}</option>
        <!-- END start_day -->
      </select>
      <select name="start_month">
        <!-- BEGIN start_month -->
        <option {start_month.SELECTED} value="{start_month.VALUE}">{start_month.OPTION}</option>
        <!-- END start_month -->
      </select>
      <input name="start_year" type="text" size="4" maxlength="4">
    </span>
    <a href="#" onClick="document.search.start_day.value={TODAY_DAY};document.search.start_month.value={TODAY_MONTH};document.search.start_year.value={TODAY_YEAR};">{lang:today}</a>
  </label>
  <label>{lang:search_date_to}
    <span>
      <select name="end_day">
        <!-- BEGIN end_day -->
        <option {end_day.SELECTED} value="{end_day.VALUE}">{end_day.OPTION}</option>
        <!-- END end_day -->
      </select>
      <select name="end_month">
        <!-- BEGIN end_month -->
        <option {end_month.SELECTED} value="{end_month.VALUE}">{end_month.OPTION}</option>
        <!-- END end_month -->
      </select>
      <input name="end_year" type="text" size="4" maxlength="4">
    </span>
    <a href="#" onClick="document.search.end_day.value={TODAY_DAY};document.search.end_month.value={TODAY_MONTH};document.search.end_year.value={TODAY_YEAR};">{lang:today}</a>
  </label>
</fieldset>

<fieldset>
  <legend>{lang:search_options}</legend>
  <label>{lang:search_categories}
    <select style="width:200px" name="cat[]" multiple="multiple" size="8">
      <!-- BEGIN category_option -->
      <option value="{category_option.VALUE}">{category_option.OPTION}</option>
      <!-- END category_option -->
    </select>
  </label>
  <label>{lang:search_subcats_included}
    <span>
    <input type="radio" name="subcats-included" value="1" checked="checked" />{lang:yes}
    </span>
    <span>
    <input type="radio" name="subcats-included" value="0" />{lang:no}
    </span>
  </label>
  <label>{lang:search_sort}
    <span><input type="radio" name="sd" value="AND" />{lang:search_ascending}</span>
    <span><input type="radio" name="sd" value="d" checked="checked" />{lang:search_descending}</span>
  </label>
</fieldset>
<p>
  <input type="submit" name="submit" value="{lang:submit}" class="bouton" />
  <input type="reset" value="{lang:reset}" class="bouton" />
</p>
</form>

<script type="text/javascript"><!--
document.post.search_allwords.focus();
//--></script>

</div> <!-- content -->
