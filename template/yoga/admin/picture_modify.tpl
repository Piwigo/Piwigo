<!-- $Id$ -->
<h2>{lang:title_picmod}</h2>

<img src="{TN_SRC}" alt="{lang:thumbnail}" class="thumbnail" />

<ul class="categoryActions">
  <!-- BEGIN jumpto -->
  <li><a href="{jumpto.URL}" title="{lang:jump to image}"><img src="{themeconf:icon_dir}/category_jump-to.png" class="button" alt="{lang:jump to image}" /></a></li>
  <!-- END jumpto -->
  <li><a href="{U_SYNC}" title="{lang:synchronize metadata}" {TAG_INPUT_ENABLED}><img src="{themeconf:icon_dir}/sync_metadata.png" class="button" alt="{lang:synchronize}" /></a></li>
</ul>

<form action="{F_ACTION}" method="post" id="properties">

  <fieldset>
    <legend>{lang:Informations}</legend>

    <table>

      <tr>
        <td><strong>{lang:Path}</strong></td>
        <td>{PATH}</td>
      </tr>

      <tr>
        <td><strong>{lang:Post date}</strong></td>
        <td>{REGISTRATION_DATE}</td>
      </tr>

      <tr>
        <td><strong>{lang:Dimensions}</strong></td>
        <td>{DIMENSIONS}</td>
      </tr>

      <tr>
        <td><strong>{lang:Filesize}</strong></td>
        <td>{FILESIZE}</td>
      </tr>

      <tr>
        <td><strong>{lang:Storage category}</strong></td>
        <td>{STORAGE_CATEGORY}</td>
      </tr>

      <!-- BEGIN links -->
      <tr>
        <td><strong>{lang:Linked categories}</strong></td>
        <td>
          <ul>
            <!-- BEGIN category -->
            <li>{links.category.NAME}</li>
            <!-- END category -->
          </ul>
        </td>
      </tr>
      <!-- END links -->

    </table>

  </fieldset>

  <fieldset>
    <legend>{lang:Properties}</legend>

    <table>

      <tr>
        <td><strong>{lang:Name}</strong></td>
        <td><input type="text" name="name" value="{NAME}" /></td>
      </tr>

      <tr>
        <td><strong>{lang:Author}</strong></td>
        <td><input type="text" name="author" value="{AUTHOR}" /></td>
      </tr>

      <tr>
        <td><strong>{lang:Creation date}</strong></td>
        <td>
          <label><input type="radio" name="date_creation_action" value="unset" /> {lang:unset}</label>
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

      <tr>
        <td><strong>{lang:Tags}</strong></td>
        <td>{TAG_SELECTION}</td>
      </tr>


      <tr>
        <td><strong>{lang:Description}</strong></td>
        <td><textarea name="description" class="description">{DESCRIPTION}</textarea></td>
      </tr>

    </table>

    <p style="text-align:center;">
      <input class="submit" type="submit" value="{lang:Submit}" name="submit" {TAG_INPUT_ENABLED}/>
      <input class="submit" type="reset" value="{lang:Reset}" name="reset" />
    </p>

  </fieldset>

</form>

<form id="associations" method="post" action="{F_ACTION}#associations">
  <fieldset>
    <legend>{lang:Association to categories}</legend>

    <table class="doubleSelect">
      <tr>
        <td>
          <h3>{lang:Associated}</h3>
          <select class="categoryList" name="cat_associated[]" multiple="multiple" size="30">
            <!-- BEGIN associated_option -->
            <option {associated_option.SELECTED} value="{associated_option.VALUE}">{associated_option.OPTION}</option>
            <!-- END associated_option -->
          </select>
          <p><input class="submit" type="submit" value="&raquo;" name="dissociate" style="font-size:15px;" {TAG_INPUT_ENABLED}/></p>
        </td>

        <td>
          <h3>{lang:Dissociated}</h3>
          <select class="categoryList" name="cat_dissociated[]" multiple="multiple" size="30">
            <!-- BEGIN dissociated_option -->
            <option {dissociated_option.SELECTED} value="{dissociated_option.VALUE}">{dissociated_option.OPTION}</option>
            <!-- END dissociated_option -->
          </select>
          <p><input class="submit" type="submit" value="&laquo;" name="associate" style="font-size:15px;" {TAG_INPUT_ENABLED}/></p>
        </td>
      </tr>
    </table>

  </fieldset>
</form>

<form id="representation" method="post" action="{F_ACTION}#representation">
  <fieldset>
    <legend>{lang:Representation of categories}</legend>

    <table class="doubleSelect">
      <tr>
        <td>
          <h3>{lang:Represents}</h3>
          <select class="categoryList" name="cat_elected[]" multiple="multiple" size="30">
            <!-- BEGIN elected_option -->
            <option {elected_option.SELECTED} value="{elected_option.VALUE}">{elected_option.OPTION}</option>
            <!-- END elected_option -->
          </select>
          <p><input class="submit" type="submit" value="&raquo;" name="dismiss" style="font-size:15px;" {TAG_INPUT_ENABLED}/></p>
        </td>

        <td>
          <h3>{lang:Does not represent}</h3>
          <select class="categoryList" name="cat_dismissed[]" multiple="multiple" size="30">
            <!-- BEGIN dismissed_option -->
            <option {dismissed_option.SELECTED} value="{dismissed_option.VALUE}">{dismissed_option.OPTION}</option>
            <!-- END dismissed_option -->
          </select>
          <p><input class="submit" type="submit" value="&laquo;" name="elect" style="font-size:15px;" {TAG_INPUT_ENABLED}/></p>
        </td>
      </tr>
    </table>

  </fieldset>
</form>
