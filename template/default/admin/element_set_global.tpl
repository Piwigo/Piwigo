<p style="text-align:center"><a href="{U_EMPTY_CADDIE}">Empty caddie</a></p>

<form action="{F_ACTION}" method="post">

  associate to
  <select style="width:400px" name="associate" size="1">
    <!-- BEGIN associate_option -->
    <option {associate_option.SELECTED} value="{associate_option.VALUE}">{associate_option.OPTION}</option>
    <!-- END associate_option -->
  </select>

  <br />dissociate from
  <select style="width:400px" name="dissociate" size="1">
    <!-- BEGIN dissociate_option -->
    <option {dissociate_option.SELECTED} value="{dissociate_option.VALUE}">{dissociate_option.OPTION}</option>
    <!-- END dissociate_option -->
  </select>

  <br />target
  <input type="radio" name="target" value="all" /> all
  <input type="radio" name="target" value="selection" /> selection
    
  <br /><input type="submit" value="{L_SUBMIT}" name="submit" class="bouton" />

  <!-- BEGIN thumbnails -->
  <table valign="top" align="center" class="thumbnail">
    <!-- BEGIN line -->
    <tr>
      <!-- BEGIN thumbnail -->
      <td class="thumbnail"
          onmousedown="document.getElementById('selection_{thumbnails.line.thumbnail.ID}').checked = (document.getElementById('selection_{thumbnails.line.thumbnail.ID}').checked ? false : true);">
        <img src="{thumbnails.line.thumbnail.SRC}"
             alt="{thumbnails.line.thumbnail.ALT}"
             title="{thumbnails.line.thumbnail.TITLE}"
             class="thumbLink" />
        <br /><input type="checkbox" name="selection[]" value="{thumbnails.line.thumbnail.ID}" id="selection_{thumbnails.line.thumbnail.ID}" />
      </td>
      <!-- END thumbnail -->
    </tr>
    <!-- END line -->
  </table>
  <!-- END thumbnails -->

</form>
