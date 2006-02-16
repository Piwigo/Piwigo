<!-- $Id$ -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:title_configuration}</h2>
</div>

<form method="post" action="{F_ACTION}" class="properties">

<fieldset>
<!-- BEGIN general -->
  <legend>{lang:conf_general_title}</legend>

  <ul>
    <li>
      <label for="gallery_title"><strong>{lang:Gallery title}</strong></label>
      <input type="text" maxlength="255" size="50" name="gallery_title" id="gallery_title" value="{general.CONF_GALLERY_TITLE}" />
    </li>

    <li>
      <label for="gallery_description"><strong>{lang:Gallery description}</strong></label>
      <textarea cols="50" rows="5" name="gallery_description" id="gallery_description">{general.CONF_GALLERY_DESCRIPTION}</textarea>
    </li>

    <li>
      <label for="gallery_title"><strong>{lang:Gallery URL}</strong></label>
      <input type="text" maxlength="255" size="50" name="gallery_url" id="gallery_url" value="{general.CONF_GALLERY_URL}" />
    </li>

    <li>
      <label><strong>{lang:History}</strong></label>
      <input type="radio" class="radio" name="log" value="true" {general.HISTORY_YES} />{lang:Yes}
      <input type="radio" class="radio" name="log" value="false" {general.HISTORY_NO} />{lang:No}
    </li>

    <li>
      <label><strong>{lang:Lock gallery}</strong></label>
      <input type="radio" class="radio" name="gallery_locked" value="true" {general.GALLERY_LOCKED_YES} />{lang:Yes}
      <input type="radio" class="radio" name="gallery_locked" value="false" {general.GALLERY_LOCKED_NO} />{lang:No}
    </li>
  </ul>
  
  <ul>
    <li>
      <label><strong>{lang:Rating}</strong></label>
      <input type="radio" class="radio" name="rate" value="true" {general.RATE_YES} />{lang:Yes}
      <input type="radio" class="radio" name="rate" value="false" {general.RATE_NO} />{lang:No}
    </li>

    <li>
      <label><strong>{lang:Rating by guests}</strong></label>
      <input type="radio" class="radio" name="rate_anonymous" value="true" {general.RATE_ANONYMOUS_YES} />{lang:Yes}
      <input type="radio" class="radio" name="rate_anonymous" value="false" {general.RATE_ANONYMOUS_NO} />{lang:No}
    </li>
  </ul>
<!-- END general -->

<!-- BEGIN comments -->
  <legend>{lang:conf_comments_title}</legend>

  <ul>
    <li>
      <label><strong>{lang:Comments for all}</strong></label>
      <input type="radio" class="radio" name="comments_forall" value="true" {comments.COMMENTS_ALL_YES} />{lang:Yes}
      <input type="radio" class="radio" name="comments_forall" value="false" {comments.COMMENTS_ALL_NO} />{lang:No}
    </li>

    <li>
      <label for="nb_comment_page"><strong>{lang:Number of comments per page}</strong></label>
      <input type="text" size="3" maxlength="4" name="nb_comment_page" id="nb_comment_page" value="{comments.NB_COMMENTS_PAGE}" />
    </li>

    <li>
      <label><strong>{lang:Validation}</strong></label>
      <input type="radio" class="radio" name="comments_validation" value="true" {comments.VALIDATE_YES} />{lang:Yes}
      <input type="radio" class="radio" name="comments_validation" value="false" {comments.VALIDATE_NO} />{lang:No}
    </li>
  </ul>
<!-- END comments -->
<!-- BEGIN default -->
  <legend>{lang:conf_default_title}</legend>

  <ul>
    <li>
      <label for="language"><strong>{lang:Language}</strong></label>
      <select name="default_language" id="default_language">
        <!-- BEGIN language_option -->
        <option value="{default.language_option.VALUE}" {default.language_option.SELECTED}>{default.language_option.CONTENT}</option>
        <!-- END language_option -->
      </select>
    </li>

    <li>
      <label for="nb_image_line"><strong>{lang:Number of images per row}</strong></label>
      <input type="text" size="3" maxlength="2" name="nb_image_line" value="{default.NB_IMAGE_LINE}" />
    </li>

    <li>
      <label><strong>{lang:Number of rows per page}</strong></label>
      <input type="text" size="3" maxlength="2" name="nb_line_page" value="{default.NB_ROW_PAGE}" />
    </li>

    <li>
      <label><strong>{lang:Interface theme}</strong></label>
      <select name="default_template" id="default_template">
        <!-- BEGIN template_option -->
        <option value="{default.template_option.VALUE}" {default.template_option.SELECTED}>{default.template_option.CONTENT}</option>
        <!-- END template_option -->
      </select>
    </li>

    <li>
      <label for="recent_period"><strong>{lang:Recent period}</strong></label>
      <input type="text" size="3" maxlength="2" name="recent_period" id="recent_period" value="{default.CONF_RECENT}" />
    </li>

    <li>
      <label><strong>{lang:Expand all categories}</strong></label>
      <input type="radio" class="radio" name="auto_expand" value="true" {default.EXPAND_YES} />{lang:Yes}
      <input type="radio" class="radio" name="auto_expand" value="false" {default.EXPAND_NO} />{lang:No}
    </li>

    <li>
      <label><strong>{lang:Show number of comments}</strong></label>
      <input type="radio" class="radio" name="show_nb_comments" value="true" {default.SHOW_COMMENTS_YES} />{lang:Yes}
      <input type="radio" class="radio" name="show_nb_comments" value="false" {default.SHOW_COMMENTS_NO} />{lang:No}
    </li>

    <li>
      <label><strong>{lang:Maximum width of the pictures}</strong></label>
      <input type="text" size="4" maxlength="4" name="default_maxwidth" value="{default.MAXWIDTH}" />
    </li>

    <li>
      <label><strong>{lang:Maximum height of the pictures}</strong></label>
      <input type="text" size="4" maxlength="4" name="default_maxheight" value="{default.MAXHEIGHT}" />
    </li>
  </ul>
<!-- END default -->
  </fieldset>
  
  <p>
    <input type="submit" name="submit" value="{lang:Submit}">
    <input type="reset" name="reset" value="{lang:Reset}">
  </p>
</form>
