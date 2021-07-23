$(document).ready(function () {
  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.plugins.getList",
    type: "GET",
    dataType: "JSON",
    success: function(data) {
      plugins = data.result;
      hasActivePlugins = false
      nbActivatedPlugins = 0;
      console.log(data);
      plugins.forEach(plugin => {
        if (plugin.state == "active") {
          hasActivePlugins = true
          $("#pluginList ul").append("<li>" + plugin.name +"</li>");
          $("#pluginList ul i").hide();
          nbActivatedPlugins++;
        }
      });

      if (!hasActivePlugins) {
        $("#pluginList ul i").hide();
        $("#pluginList ul").append("<p>" + no_active_plugin +"</p>");
      }
      $(".adminMenubarCounter").append(nbActivatedPlugins);
    },
    error: function () {
      $(".adminMenubarCounter").append(0);
      $("#pluginList ul").append("<p>" + error_occured +"</p>");
      $("#pluginList ul i").hide();
    }
  });
})