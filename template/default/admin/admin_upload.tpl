<div class="admin">{L_UPLOAD_TITLE}</div>
<form name="form1" method="post" action="" style="text-align:center;width:80%;">
<div style="clear:both;"></div>
  <div style="float:left;padding:10px;">
  {L_AUTHORIZED}<br />
  <select style="width:280px" name="cat_data[]" multiple="multiple" size="5">
    {UPLOADABLE_CATEGORIES}
  </select><br />
  <input type="submit" name="delete" value="{L_DELETE}" class="bouton" />
  </div>
  <div style="float:right;padding:10px;">
  {L_FORBIDDEN}<BR />
  <select style="width:280px" name="cat_data[]" multiple="multiple" size="5">
    {PRIVATE_CATEGORIES}
  </select>
  <br>
  <input type="submit" name="submit" value="{L_SUBMIT}" class="bouton" /> &nbsp;
  <input type="reset" name="reset" value="{L_RESET}" class="bouton" />
  </div>
<div style="clear:both;"></div>
</form>
<div class="infoCat">{L_UPLOAD_INFO}</div>