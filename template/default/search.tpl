<div class="titrePage">{L_SEARCH_TITLE}</div>
<br />
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
  <tr class="admin">
    <th colspan="4">{L_SEARCH_TITLE}</th>
  </tr>
  <tr> 
    <td width="50%" colspan="2"><b>{L_SEARCH_KEYWORDS} : </b><br /><span class="small">{L_SEARCH_KEYWORDS_HINT}</span></td>
    <td colspan="2" valign="top">
	  <input type="text" style="width: 300px" name="search_keywords" size="30" />
	  <br />
	  <input type="radio" name="mode" value="AND" checked="checked" /> {L_SEARCH_ALL_TERMS}<br />
	  <input type="radio" name="mode" value="OR" /> {L_SEARCH_ANY_TERMS}
	</td>
  </tr>
  <tr> 
    <td colspan="2"><b>{L_SEARCH_AUTHOR} :</b><br /><span class="small">{L_SEARCH_AUTHOR_HINT}</span></td>
    <td colspan="2" valign="middle">
	  <input type="text" style="width: 300px" name="search_author" size="30" />
	</td>
  </tr>
  <tr> 
    <td colspan="2"><b>{L_SEARCH_DATE} :</b><br /><span class="small">{L_SEARCH_DATE_HINT}</span></td>
    <td colspan="2" valign="middle">
	<table><tr><td>
	  {L_SEARCH_DATE_FROM} :</td><td>
	  {S_CALENDAR_DAY}{S_CALENDAR_MONTH}{S_CALENDAR_YEAR}&nbsp;
	  <a href="#" name="#" onClick="document.post.start_day.value={TODAY_DAY};document.post.start_month.value={TODAY_MONTH};document.post.start_year.value={TODAY_YEAR};" />{L_TODAY}</a>
	  </tr><tr><td>
	  {L_SEARCH_DURATION} : </td><td>
	  <input name="duration_day" type="post" maxlength="5" size="3" value="{DURATION_DAY}" />&nbsp;{L_DAYS}
	  </td></tr></table>
	</td>
  </tr>
  <tr class="admin"> 
    <th colspan="4">{L_SEARCH_OPTIONS}</th>
  </tr>
  <tr> 
    <td width="25%" ><b>{L_SEARCH_CATEGORIES} : </b><br /><span class="small">{L_SEARCH_CATEGORIES_HINT}</span></td>
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
	  <input type="radio" name="date_type" value="date_creation" />{L_SEARCH_CREATION}<br />
	  <input type="radio" name="date_type" value="date_available" checked="checked" />{L_SEARCH_AVAILABILITY}
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
<a href="{U_HOME}" title="{L_RETURN_HINT}">[ {L_RETURN} ]</a>
