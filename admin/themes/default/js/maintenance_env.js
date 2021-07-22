$(document).ready(function () {
  jQuery.ajax({
    url: "ws.php?format=json&method=pwg.plugins.getList",
    type: "GET",
    dataType: "JSON",
    success: function(data) {
        plugins = data.result;
        plugins.forEach(plugin => {
          if (plugin.state == "active") {
            $("#pluginList ul").append("<li>" + plugin.name +"</li>");
            $("#pluginList ul i").hide();
          }
        });
    }
  });
})