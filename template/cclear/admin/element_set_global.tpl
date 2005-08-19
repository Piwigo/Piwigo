<!-- $Id$ -->
<h2>{lang:Batch management}</h2>

<h3>{CATEGORIES_NAV}</h3>

<p style="text-align:center;">
  global mode 
  | <a href="{U_UNIT_MODE}">unit mode</a>
</p>

<form action="{F_ACTION}" method="post">

<fieldset>

  <legend>Caddie management</legend>

  <ul style="list-style-type:none;">

    <!-- BEGIN in_caddie -->
    <li><input type="radio" name="caddie_action" value="empty_all" /> Empty caddie</li>
    <li><input type="radio" name="caddie_action" value="empty_selected" /> Take selected elements out of caddie</li>
    <!-- END in_caddie -->

    <!-- BEGIN not_in_caddie -->
    <li><input type="radio" name="caddie_action" value="add_selected" /> Add selected elements to caddie</li>
    <!-- END not_in_caddie -->
  
  </ul>

  <p style="text-align:center;"><input type="submit" value="{L_SUBMIT}" name="submit_caddie" class="bouton" /></p>

</fieldset>

<fieldset>

  <legend>Display options</legend>

  <p>elements per line :
      <a href="{U_COLS}&amp;cols=4">4</a>
    | <a href="{U_COLS}&amp;cols=5">5</a>
    | <a href="{U_COLS}&amp;cols=6">6</a>
    | <a href="{U_COLS}&amp;cols=7">7</a>
    | <a href="{U_COLS}&amp;cols=8">8</a>
    | <a href="{U_COLS}&amp;cols=9">9</a>
    | <a href="{U_COLS}&amp;cols=10">10</a>
  </p>

  <p>elements per page :
      <a href="{U_DISPLAY}&amp;display=20">20</a>
    | <a href="{U_DISPLAY}&amp;display=50">50</a>
    | <a href="{U_DISPLAY}&amp;display=100">100</a>
    | <a href="{U_DISPLAY}&amp;display=all">all</a>
  </p>

</fieldset>

<fieldset>

  <legend>Form</legend>

  <table>

    <tr>
      <td>associate to category</td>
      <td>
       <select style="width:400px" name="associate" size="1">
         <!-- BEGIN associate_option -->
         <option {associate_option.SELECTED} value="{associate_option.VALUE}">{associate_option.OPTION}</option>
         <!-- END associate_option -->
       </select>
      </td>
    </tr>

    <tr>
      <td>dissociate from category</td>
      <td>
        <select style="width:400px" name="dissociate" size="1">
          <!-- BEGIN dissociate_option -->
          <option {dissociate_option.SELECTED} value="{dissociate_option.VALUE}">{dissociate_option.OPTION}</option>
          <!-- END dissociate_option -->
        </select>
      </td>
    </tr>

    <tr>
      <td>add keywords</td>
      <td><input type="text" name="add_keywords" value="" /></td>
    </tr>

    <tr>
      <td>remove keyword</td>
      <td>
        <select name="remove_keyword">
          <!-- BEGIN remove_keyword_option -->
          <option value="{remove_keyword_option.VALUE}">{remove_keyword_option.OPTION}</option>
          <!-- END remove_keyword_option -->
        </select>
      </td>
    </tr>

    <tr>
      <td>author</td>
      <td>
        <input type="radio" name="author_action" value="leave" checked="checked" /> leave unchanged
        <input type="radio" name="author_action" value="unset" /> unset
        <input type="radio" name="author_action" value="set" id="author_action_set" /> set to
        <input onmousedown="document.getElementById('author_action_set').checked = true;" type="text" name="author" value="" />
      </td>
    </tr>

    <tr>
      <td>title</td>
      <td>
        <input type="radio" name="name_action" value="leave" checked="checked" /> leave unchanged
        <input type="radio" name="name_action" value="unset" /> unset
        <input type="radio" name="name_action" value="set" id="name_action_set" /> set to
        <input onmousedown="document.getElementById('name_action_set').checked = true;" type="text" name="name" value="" />
      </td>
    </tr>

    <tr>
      <td>creation date</td>
      <td>
        <input type="radio" name="date_creation_action" value="leave" checked="checked" /> leave unchanged
        <input type="radio" name="date_creation_action" value="unset" /> unset
        <input type="radio" name="date_creation_action" value="set" id="date_creation_action_set" /> set to 
        <select onmousedown="document.getElementById('date_creation_action_set').checked = true;" name="date_creation_day">
          <!-- BEGIN date_creation_day -->
          <option {date_creation_day.SELECTED} value="{date_creation_day.VALUE}">{date_creation_day.OPTION}</option>
          <!-- END date_creation_day -->
        </select>
        <select onmousedown="document.getElementById('date_creation_action_set').checked = true;" name="date_creation_month">
          <!-- BEGIN date_creation_month -->
          <option {date_creation_month.SELECTED} value="{date_creation_month.VALUE}">{date_creation_month.OPTION}</option>
          <!-- END date_creation_month -->
        </select>
        <input onmousedown="document.getElementById('date_creation_action_set').checked = true;"
               name="date_creation_year"
               type="text"
               size="4"
               maxlength="4"
               value="{DATE_CREATION_YEAR_VALUE}" />
      </td>
    </tr>

  </table>

  <p style="text-align:center;">
    target
    <input type="radio" name="target" value="all" /> all
    <input type="radio" name="target" value="selection" checked="checked" /> selection
  </p>

    
  <p style="text-align:center;"><input type="submit" value="{L_SUBMIT}" name="submit" class="bouton" /></p>

</fieldset>

<fieldset>

  <legend>Elements</legend>

  <div class="navigationBar">{NAV_BAR}</div>

  <!-- BEGIN thumbnails -->
  <table valign="top" align="center" class="thumbnail">
    <!-- BEGIN line -->
    <tr>
      <!-- BEGIN thumbnail -->
      <td class="thumbnail">
        <label>
        <img src="{thumbnails.line.thumbnail.SRC}"
             alt="{thumbnails.line.thumbnail.ALT}"
             title="{thumbnails.line.thumbnail.TITLE}"
             class="thumbLink" />
        <br /><input type="checkbox" name="selection[]" value="{thumbnails.line.thumbnail.ID}" />
        </label>
      </td>
      <!-- END thumbnail -->
    </tr>
    <!-- END line -->
  </table>
  <!-- END thumbnails -->

</fieldset>

</form>
