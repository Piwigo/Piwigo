<!-- $Id$ -->
<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_HOME}" title="{lang:return to homepage}"><img src="./template/yoga/theme/home.png" alt="{lang:home}"/></a></li>
    </ul>
    <h2>{lang:User comments}</h2>
  </div>

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

  <p><input type="submit" name="submit" value="{lang:Filter and display}"></p>

</form>

<div class="navigationBar">{NAVBAR}</div>

<div id="comments">

  <!-- BEGIN comment -->
  <div class="comment">
    <a class="illustration" href="{comment.U_PICTURE}"><img src="{comment.TN_SRC}" /></a>
    <p class="commentHeader"><span class="author">{comment.AUTHOR}</span> - <span class="date">{comment.DATE}</span></p>
    <blockquote>{comment.CONTENT}</blockquote>
    <hr class="separation">
  </div>

  <!-- END comment -->

</div>

</div> <!-- content -->
