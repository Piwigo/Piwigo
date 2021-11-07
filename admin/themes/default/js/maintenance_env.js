$(document).ready(function () {
  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.plugins.getList",
    type: "GET",
    dataType: "JSON",
    success: function(data) {
      plugins = data.result;
      hasActivePlugins = false
      nbActivatedPlugins = 0;
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
      $(".badge-number").append(nbActivatedPlugins);
    },
    error: function () {
      $(".badge-number").append(0);
      $("#pluginList ul").append("<p>" + error_occured +"</p>");
      $("#pluginList ul i").hide();
    }
  });
})