<!-- BEGIN title -->
<div class="titrePage">{L_COMMENT_TITLE}</div>
<!-- END title -->

<form class="filter" action="{F_ACTION}" method="get">

  <fieldset>
    <legend>{lang:Filter}</legend>

    <label>{lang:Keyword}<input type="text" name="keyword" value="{F_KEYWORD}" /></label>

    <label>{lang:Author}<input type="text" name="author" value="{F_AUTHOR}" /></label>

    <label>
      {lang:Category}
      <select name="cat">
        <!-- BEGIN category -->
        <option class="{category.CLASS}" {category.SELECTED} value="{category.VALUE}">{category.OPTION}</option>
        <!-- END category -->
      </select>
    </label>

    <label>
      {lang:Since}
      <select name="since">
        <!-- BEGIN since_option -->
        <option {since_option.SELECTED} value="{since_option.VALUE}">{since_option.CONTENT}</option>
        <!-- END since_option -->
      </select>
    </label>

  </fieldset>

  <fieldset>

    <legend>{lang:Display}</legend>

    <label>
      {lang:Sort by}
      <select name="sort_by">
        <!-- BEGIN sort_by_option -->
        <option value="{sort_by_option.VALUE}" {sort_by_option.SELECTED} >{sort_by_option.CONTENT}</option>
        <!-- END sort_by_option -->
      </select>
    </label>

    <label>
      {lang:Sort order}
      <select name="sort_order">
        <!-- BEGIN sort_order_option -->
        <option value="{sort_order_option.VALUE}" {sort_order_option.SELECTED} >{sort_order_option.CONTENT}</option>
        <!-- END sort_order_option -->
      </select>
    </label>

    <label>
      {lang:Number of items}
      <select name="items_number">
        <!-- BEGIN items_number_option -->
        <option value="{items_number_option.VALUE}" {items_number_option.SELECTED} >{items_number_option.CONTENT}</option>
        <!-- END items_option -->
      </select>
    </label>

  </fieldset>

  <input type="submit" name="submit" value="{lang:Filter and display}" />

</form>

<div class="navigationBar">{NAVBAR}</div>
<a class="admin" href="{U_HOME}" title="{lang:return to homepage}">{lang:home}</a>

<!-- BEGIN validation -->
<form action="{F_ACTION}" method="post">
<!-- END validation -->
<table class="table2">
<!-- BEGIN picture -->
<tr class="row1">
<td >
<a href="{picture.U_THUMB}" title="{picture.TITLE_IMG}">
<img src="{picture.I_THUMB}" class="thumbLink" alt="{picture.THUMB_ALT_IMG}"/>
</a>
</td>
<td class="tablecompact">
  <div class="commentTitle">{picture.TITLE_IMG}</div>
  <div class="commentsNavigationBar">{picture.NAV_BAR}</div>
  <table class="tablecompact">
  <!-- BEGIN comment -->
	<tr class="throw">
	  <td class="throw">
	  {picture.comment.COMMENT_AUTHOR}
	  </td>
	  <td class="commentDate">
	  {picture.comment.COMMENT_DATE}
	<!-- BEGIN validation -->
	<input type="checkbox" name="comment_id[]" value="{picture.comment.validation.ID}" {picture.comment.validation.CHECKED} />
	<!-- END validation -->
	  </td>
	</tr>
	<tr class="row1">
	  <td class="comment" colspan="2">{picture.comment.COMMENT}</td>
	</tr>
	<!-- END comment -->
  </table>
</td>
</tr>
<!-- END picture -->
</table>
<!-- BEGIN validation -->
<div align="center">
<input type="submit" name="validate" class="bouton" value="{lang:submit}" />
<input type="submit" name="delete" class="bouton" value="{lang:delete}" />
</div>
</form>
<!-- END validation -->
