{footer_script}{literal}
jQuery(document).ready(function(){
  jQuery(".addAlbumOpen").colorbox({
    inline: true,
    href: "#addAlbumForm",
    onComplete: function() {
      jQuery("input[name=category_name]").focus();
      
      jQuery("#category_parent").html('<option value="0">------------</option>')
        .append(jQuery("#albumSelect").html())
        .val(jQuery("#albumSelect").val());
    }
  });

  jQuery("#addAlbumForm form").submit(function() {
      jQuery("#categoryNameError").text("");
      
      var parent_id = jQuery("select[name=category_parent] option:selected").val(),
          name = jQuery("input[name=category_name]").val();

      jQuery.ajax({
        url: "ws.php",
        dataType: 'json',
        data: {
          format: 'json',
          method: 'pwg.categories.add',
          parent: parent_id,
          name: name
        },
        beforeSend: function() {
          jQuery("#albumCreationLoading").show();
        },
        success: function(data) {
          jQuery("#albumCreationLoading").hide();
          jQuery(".addAlbumOpen").colorbox.close();

          var newAlbum = data.result.id,
              newAlbum_name = '';
              
          if (parent_id!=0) {
            newAlbum_name = jQuery("#category_parent").find("option[value="+ parent_id +"]").text() +' / ';
          }
          newAlbum_name+= name;
          
          var new_option = jQuery("<option/>")
              .attr("value", newAlbum)
              .attr("selected", "selected")
              .text(newAlbum_name);
              
          jQuery("#albumSelect").find("option").removeAttr('selected');
          
          if (parent_id==0) {
            jQuery("#albumSelect").append(new_option);
          }
          else {
            jQuery("#albumSelect").find("option[value="+ parent_id +"]").after(new_option);
          }

          jQuery("#addAlbumForm form input[name=category_name]").val('');
          jQuery("#albumSelection").show();

          return true;
        },
        error: function(XMLHttpRequest, textStatus, errorThrows) {
            jQuery("#albumCreationLoading").hide();
            jQuery("#categoryNameError").text(errorThrows).css("color", "red");
        }
      });

      return false;
  });
});
{/literal}{/footer_script}

<div style="display:none">
  <div id="addAlbumForm" style="text-align:left;padding:1em;">
    <form>
      {'Parent album'|@translate}<br>
      <select id="category_parent" name="category_parent">
      </select>
      <br><br>
      
      {'Album name'|@translate}<br>
      <input name="category_name" type="text" maxlength="255"> <span id="categoryNameError"></span>
      <br><br><br>
      
      <input type="submit" value="{'Create'|@translate}">
      <span id="albumCreationLoading" style="display:none"><img src="themes/default/images/ajax-loader-small.gif"></span>
    </form>
  </div>
</div>
