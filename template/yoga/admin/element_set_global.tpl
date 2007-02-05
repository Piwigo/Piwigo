<!-- $Id$ -->
<h2>{lang:Batch management}</h2>

<h3>{CATEGORIES_NAV}</h3>

<p style="text-align:center;">
  {lang:global mode}
  | <a href="{U_UNIT_MODE}">{lang:unit mode}</a>
</p>

<fieldset>

  <legend>{lang:Display options}</legend>

  <p>{lang:elements per page}:
      <a href="{U_DISPLAY}&amp;display=20">20</a>
    | <a href="{U_DISPLAY}&amp;display=50">50</a>
    | <a href="{U_DISPLAY}&amp;display=100">100</a>
    | <a href="{U_DISPLAY}&amp;display=all">{lang:all}</a>
  </p>

</fieldset>

<form action="{F_ACTION}" method="post">

<fieldset>

  <legend>{lang:Elements}</legend>
<!--
  <div class="navigationBar">{NAV_BAR}</div>
-->
  <!-- BEGIN thumbnails -->
  <ul class="thumbnails">
    <!-- BEGIN thumbnail -->
    <li><span class="wrap1">
        <label>
          <span class="wrap2"><span>
            <img src="{thumbnails.thumbnail.SRC}"
               alt="{thumbnails.thumbnail.ALT}"
               title="{thumbnails.thumbnail.TITLE}"
               class="thumbnail" />
          </span></span>
          <input type="checkbox" name="selection[]" value="{thumbnails.thumbnail.ID}" />
        </label>
        </span>
    </li>
    <!-- END thumbnail -->
  </ul>
  <!-- END thumbnails -->

</fieldset>

<fieldset>

  <legend>{lang:Form}</legend>

  <table>

    <tr>
      <td>{lang:associate to category}</td>
      <td>
       <select style="width:400px" name="associate" size="1">
         <!-- BEGIN associate_option -->
         <option {associate_option.SELECTED} value="{associate_option.VALUE}">{associate_option.OPTION}</option>
         <!-- END associate_option -->
       </select>
      </td>
    </tr>

    <tr>
      <td>{lang:dissociate from category}</td>
      <td>
        <select style="width:400px" name="dissociate" size="1">
          <!-- BEGIN dissociate_option -->
          <option {dissociate_option.SELECTED} value="{dissociate_option.VALUE}">{dissociate_option.OPTION}</option>
          <!-- END dissociate_option -->
        </select>
      </td>
    </tr>

    <tr>
      <td>{lang:add tags}</td>
      <td>{ADD_TAG_SELECTION}</td>
    </tr>

    <tr>
      <td>{lang:remove tags}</td>
      <td>{DEL_TAG_SELECTION}</td>
    </tr>
    
    <tr>
      <td>{lang:Author}</td>
      <td>
        <input type="radio" name="author_action" value="leave" checked="checked" /> {lang:leave}
        <input type="radio" name="author_action" value="unset" /> {lang:unset}
        <input type="radio" name="author_action" value="set" id="author_action_set" /> {lang:set to}
        <input onmousedown="document.getElementById('author_action_set').checked = true;" type="text" name="author" value="" />
      </td>
    </tr>

    <tr>
      <td>{lang:title}</td>
      <td>
        <input type="radio" name="name_action" value="leave" checked="checked" /> {lang:leave}
        <input type="radio" name="name_action" value="unset" /> {lang:unset}
        <input type="radio" name="name_action" value="set" id="name_action_set" /> {lang:set to}
        <input onmousedown="document.getElementById('name_action_set').checked = true;" type="text" name="name" value="" />
      </td>
    </tr>

    <tr>
      <td>{lang:Creation date}</td>
      <td>
        <input type="radio" name="date_creation_action" value="leave" checked="checked" /> {lang:leave}
        <input type="radio" name="date_creation_action" value="unset" /> {lang:unset}
        <input type="radio" name="date_creation_action" value="set" id="date_creation_action_set" /> {lang:set to}
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

  <p>
    {lang:target}
    <label><input type="radio" name="target" value="all" /> {lang:all}</label>
    <label><input type="radio" name="target" value="selection" checked="checked" /> {lang:selection}</label>
  </p>

    
  <p><input class="submit" type="submit" value="{L_SUBMIT}" name="submit" {TAG_INPUT_ENABLED}/></p>

</fieldset>

<fieldset>

  <legend>{lang:Caddie management}</legend>

  <ul style="list-style-type:none;">
    <!-- BEGIN in_caddie -->
    <li><label><input type="radio" name="caddie_action" value="empty_all" /> {lang:Empty caddie}</label></li>
    <li><label><input type="radio" name="caddie_action" value="empty_selected" /> {lang:Take selected elements out of caddie}</label></li>
    <!-- END in_caddie -->

    <!-- BEGIN not_in_caddie -->
    <li><label><input type="radio" name="caddie_action" value="add_selected" /> {lang:Add selected elements to caddie}</label></li>
    <!-- END not_in_caddie -->

    <li><label><input type="radio" name="caddie_action" value="export" /> {lang:Export data}</label></li>
  
  </ul>

  <p><input class="submit" type="submit" value="{L_SUBMIT}" name="submit_caddie" {TAG_INPUT_ENABLED}/></p>

</fieldset>

</form>
