<!-- DEV TAG: not smarty migrated -->
<!-- $Id$ -->
<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}" rel="nofollow"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
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
  <label>{lang:search_keywords}
    <input type="text" style="width: 300px" name="search_allwords" size="30"  />
  </label>
  <ul>
    <li><label>
      <input type="radio" name="mode" value="AND" checked="checked" />{lang:search_mode_and}
    </label></li>
    <li><label>
      <input type="radio" name="mode" value="OR" />{lang:search_mode_or}
    </label></li>
  </ul>
  <label>{lang:search_author}
    <input type="text" style="width: 300px" name="search_author" size="30"  />
  </label>
</fieldset>

<!-- BEGIN tags -->
<fieldset>
  <legend>{lang:Search tags}</legend>
  {TAG_SELECTION}
  <label><span><input type="radio" name="tag_mode" value="AND" checked="checked" /> {lang:All tags}</span></label>
  <label><span><input type="radio" name="tag_mode" value="OR" /> {lang:Any tag}</span></label>
</fieldset>
<!-- END tags -->

<fieldset>
  <legend>{lang:search_date}</legend>
  <ul>
    <li><label>{lang:search_date_type}</label></li>
    <li><label>
      <input type="radio" name="date_type" value="date_creation" checked="checked" />{lang:Creation date}
    </label></li>
    <li><label>
      <input type="radio" name="date_type" value="date_available" />{lang:Post date}
    </label></li>
  </ul>
  <ul>
    <li><label>{lang:search_date_from}</label></li>
    <li>
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
      <input name="start_year" type="text" size="4" maxlength="4" >
    </li>
    <li>
      <a href="#" onClick="document.search.start_day.value={TODAY_DAY};document.search.start_month.value={TODAY_MONTH};document.search.start_year.value={TODAY_YEAR};return false;">{lang:today}</a>
    </li>
  </ul>
  <ul>
    <li><label>{lang:search_date_to}</label></li>
    <li>
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
      <input name="end_year" type="text" size="4" maxlength="4" >
    </li>
    <li>
      <a href="#" onClick="document.search.end_day.value={TODAY_DAY};document.search.end_month.value={TODAY_MONTH};document.search.end_year.value={TODAY_YEAR};return false;">{lang:today}</a>
    </li>
  </ul>
</fieldset>

<fieldset>
  <legend>{lang:search_options}</legend>
  <label>{lang:search_categories}
    <select class="categoryList" name="cat[]" multiple="multiple" >
      <!-- BEGIN category_option -->
      <option value="{category_option.VALUE}">{category_option.OPTION}</option>
      <!-- END category_option -->
    </select>
  </label>
  <ul>
    <li><label>{lang:search_subcats_included}</label></li>
    <li><label>
      <input type="radio" name="subcats-included" value="1" checked="checked" />{lang:yes}
    </label></li>
    <li><label>
      <input type="radio" name="subcats-included" value="0" />{lang:no}
    </label></li>
  </ul>
  <ul>
    <li><label>{lang:search_sort}</label></li>
    <li><label>
      <input type="radio" name="sd" value="AND" />{lang:search_ascending}
    </label></li>
    <li><label>
      <input type="radio" name="sd" value="d" checked="checked" />{lang:search_descending}
    </label></li>
  </ul>
</fieldset>
<p>
  <input class="submit" type="submit" name="submit" value="{lang:submit}" />
  <input class="submit" type="reset" value="{lang:reset}" />
</p>
</form>

<script type="text/javascript"><!--
document.search.search_allwords.focus();
//--></script>

</div> <!-- content -->
