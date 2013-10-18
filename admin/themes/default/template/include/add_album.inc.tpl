{footer_script}{literal}
jQuery(document).ready(function(){
  function fillCategoryListbox(selectId, selectedValue) {
    jQuery.getJSON(
      "ws.php?format=json&method=pwg.categories.getList",
      {
        recursive: true,
        fullname: true,
        format: "json"
      },
      function(data) {
        jQuery.each(
          data.result.categories,
          function(i,category) {
            var selected = null;
            if (category.id == selectedValue) {
              selected = "selected";
            }
            
            jQuery("<option/>")
              .attr("value", category.id)
              .attr("selected", selected)
              .text(category.name)
              .appendTo("#"+selectId)
              ;
          }
        );
      }
    );
  }

  jQuery(".addAlbumOpen").colorbox({
    inline:true,
    href:"#addAlbumForm",
    onComplete:function(){
      jQuery("input[name=category_name]").focus();
    }
  });

  jQuery("#addAlbumForm form").submit(function(){
      jQuery("#categoryNameError").text("");

      jQuery.ajax({
        url: "ws.php?format=json&method=pwg.categories.add",
        data: {
          parent: jQuery("select[name=category_parent] option:selected").val(),
          name: jQuery("input[name=category_name]").val()
        },
        beforeSend: function() {
          jQuery("#albumCreationLoading").show();
        },
        success:function(html) {
          jQuery("#albumCreationLoading").hide();

          var newAlbum = jQuery.parseJSON(html).result.id;
          jQuery(".addAlbumOpen").colorbox.close();

          jQuery("#albumSelect").find("option").remove();
          fillCategoryListbox("albumSelect", newAlbum);

          /* we refresh the album creation form, in case the user wants to create another album */
          jQuery("#category_parent").find("option").remove();

          jQuery("<option/>")
            .attr("value", 0)
            .text("------------")
            .appendTo("#category_parent")
          ;

          fillCategoryListbox("category_parent", newAlbum);

          jQuery("#addAlbumForm form input[name=category_name]").val('');

          jQuery("#albumSelection").show();

          return true;
        },
        error:function(XMLHttpRequest, textStatus, errorThrows) {
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
      <select id ="category_parent" name="category_parent">
        <option value="0">------------</option>
        {html_options options=$category_parent_options selected=$category_parent_options_selected}
      </select>

      <br><br>{'Album name'|@translate}<br><input name="category_name" type="text" maxlength="255"> <span id="categoryNameError"></span>
      <br><br><br><input type="submit" value="{'Create'|@translate}"> <span id="albumCreationLoading" style="display:none"><img src="themes/default/images/ajax-loader-small.gif"></span>
    </form>
  </div>
</div>
