<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
      <li><a href="{U_HOME}" title="{lang:return to homepage}" rel="home"><img src="{themeconf:icon_dir}/home.png" class="button" alt="{lang:home}"/></a></li>
    </ul>
    <h2>{lang:Search}</h2>
  </div>

<!-- TO DO -->
<form method="post" name="post" action="{S_SEARCH_ACTION}">
<!-- BEGIN errors -->
<div class="errors">
  <ul>
    <!-- BEGIN error -->
    <li>{errors.error.ERROR}</li>
    <!-- END error -->
  </ul>
</div>
<!-- END errors -->
<table width="100%" align="center" cellpadding="2">
  <tr> 
    <td width="50%" colspan="2"><b>{L_SEARCH_KEYWORDS} : </b>
    <td colspan="2" valign="top">
	  <input type="text" style="width: 300px" name="search_allwords" size="30" />
	  <br />
	  <input type="radio" name="mode" value="AND" checked="checked" /> {L_SEARCH_ALL_TERMS}<br />
	  <input type="radio" name="mode" value="OR" /> {L_SEARCH_ANY_TERMS}
	</td>
  </tr>
  <tr> 
    <td colspan="2"><b>{L_SEARCH_AUTHOR} :</b>
    <td colspan="2" valign="middle">
	  <input type="text" style="width: 300px" name="search_author" size="30" />
	</td>
  </tr>
  <tr> 
    <td colspan="2"><b>{L_SEARCH_DATE} :</b>
    <td colspan="2" valign="middle">
      <table>
        <tr>
          <td>{L_SEARCH_DATE_FROM} :</td>
          <td>
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
	    <input name="start_year" type="text" size="4" maxlength="4">&nbsp;
	    <a href="#" name="#" onClick="document.post.start_day.value={TODAY_DAY};document.post.start_month.value={TODAY_MONTH};document.post.start_year.value={TODAY_YEAR};" />{L_TODAY}</a>
          </td>
        </tr>
        <tr>
          <td>{L_SEARCH_DATE_TO} :</td>
          <td>
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
            <input name="end_year" type="text" size="4" maxlength="4">&nbsp;
	    <a href="#" name="#" onClick="document.post.end_day.value={TODAY_DAY};document.post.end_month.value={TODAY_MONTH};document.post.end_year.value={TODAY_YEAR};" />{L_TODAY}</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr class="admin"> 
    <th colspan="4">{L_SEARCH_OPTIONS}</th>
  </tr>
  <tr> 
    <td width="25%" ><b>{L_SEARCH_CATEGORIES} : </b>
    <td width="25%" nowrap="nowrap">
	  <select style="width:200px" name="cat[]" multiple="multiple" size="8">
      <!-- BEGIN category_option -->
        <option value="{category_option.VALUE}">{category_option.OPTION}</option>
      <!-- END category_option -->
      </select>
	</td>
    <td width="25%" nowrap="nowrap"><b>{L_SEARCH_SUBFORUMS} : </b></td>
    <td width="25%" nowrap="nowrap">
	  <input type="radio" name="subcats-included" value="1" checked="checked" />{L_YES}&nbsp;&nbsp;
	  <input type="radio" name="subcats-included" value="0" />{L_NO}
	</td>
   </tr>
   <tr> 
    <td width="25%" nowrap="nowrap"><b>{L_SEARCH_DATE_TYPE} : </b></td>
    <td width="25%" nowrap="nowrap">
	  <input type="radio" name="date_type" value="date_creation" checked="checked" />{L_SEARCH_CREATION}<br />
	  <input type="radio" name="date_type" value="date_available" />{L_SEARCH_AVAILABILITY}
	</td>
	<td><b>{L_RESULT_SORT} : </b></td>
    <td nowrap="nowrap">
	  <input type="radio" name="sd" value="AND" />{L_SORT_ASCENDING}<br />
	  <input type="radio" name="sd" value="d" checked="checked" />{L_SORT_DESCENDING}
	</td>
  </tr>
<!--  <tr> 
    <td width="25%" nowrap="nowrap"><b>{L_SEARCH_WITHIN} : </b></td>
    <td width="25%" nowrap="nowrap">
	  <input type="radio" name="search_fields" value="all" checked="checked" />{L_SEARCH_ALL}<br />
	  <input type="radio" name="search_fields" value="imgonly" />{L_SEARCH_IMG_ONLY}<br />
	  <input type="radio" name="search_fields" value="commentsonly" />{L_SEARCH_COMMENTS_ONLY}
	</td>
  </tr>
  <tr>
    <td><b>{L_RESULT_SORT} : </b></td>
    <td nowrap="nowrap">
	  {S_SELECT_SORT_KEY}<br />
	  <input type="radio" name="sd" value="a" />{L_SORT_ASCENDING}<br />
	  <input type="radio" name="sd" value="d" checked="checked" />{L_SORT_DESCENDING}
	</td>
    <td nowrap="nowrap"><b>{L_DISPLAY_RESULTS} : </b></td>
    <td nowrap="nowrap">
	  <input type="radio" name="show_results" value="images" checked="checked" />{L_IMAGES}&nbsp;&nbsp;
	  <input type="radio" name="show_results" value="comments" /> {L_COMMENTS}
	</td>
  </tr>  
  -->
<tr> 
<td align="center" valign="bottom" colspan="4" height="38">
<input type="submit" name="submit" value="{L_SUBMIT}" class="bouton" />&nbsp;&nbsp;
<input type="reset" value="{L_RESET}" class="bouton" />
</td>
</table>
</form>

</div> <!-- content -->
