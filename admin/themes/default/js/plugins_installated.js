function setDisplayClassic() {
    $(".pluginContainer").removeClass("line").removeClass("compact").addClass("classic");

    $(".pluginDesc").show();
    $(".pluginDescCompact").hide();
    $(".pluginActions").show();
    $(".pluginActionsSmallIcons").hide();

    $(".pluginMiniBoxNameCell").removeClass("pluginMiniBoxNameCellCompact");

    // normalTitle();
}

function setDisplayCompact() {
    $(".pluginContainer").removeClass("line").addClass("compact").removeClass("classic");

    $(".pluginDesc").hide();
    $(".pluginDescCompact").show();
    $(".pluginActions").hide();
    $(".pluginActionsSmallIcons").show();

    $(".pluginMiniBoxNameCell").addClass("pluginMiniBoxNameCellCompact");

    // reduceTitle()
}

function setDisplayLine() {
    $(".pluginContainer").addClass("line").removeClass("compact").removeClass("classic");

    $(".pluginDesc").show();
    $(".pluginDescCompact").hide();
    $(".pluginActions").show();
    $(".pluginActionsSmallIcons").hide();
    // normalTitle();
}

function reduceTitle() {
    var x = document.getElementsByClassName("pluginMiniBoxNameCell");
    var length = 22;

    for (const div of x) {
        var text = div.innerHTML.trim()
        if (text.length > length) {
            var newText = text.substring(0, length);
            newText = newText + "...";

            div.innerHTML = newText;
            div.title =  text   
        }
    }
}

function normalTitle() {
    var x = document.getElementsByClassName("pluginMiniBoxNameCell");

    for (const div of x) {
        div.innerHTML = div.dataset.title
    }
}

function activatePlugin(id) {
    $("#"+id+" .switch").attr("disabled", true);

    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: 'ws.php',
        data: { method: 'pwg.plugins.performAction', 
                action: 'activate', 
                plugin: id, 
                pwg_token: pwg_token, 
                format: 'json' },
        success: function (data) {
            if (data.stat == 'ok') {
                let pluginName = id;
                $("#" + id + " .pluginNotif").stop(false, true);
                $("#" + id + " .AddPluginSuccess label span:first").html(plugin_added_str);
                $("#" + id + " .AddPluginSuccess").css("display", "flex");

                nb_plugin.active += 1;
                nb_plugin.inactive -= 1;
                actualizeFilter();
            }
        }, 
        error: function (e) {
            console.log(e);
            console.log("It didn't work");
            $("#" + id + " .pluginNotif").stop(false, true);
            $("#" + id + " .PluginActionError label span:first").html(plugin_action_error);
            $("#" + id + " .PluginActionError").css("display", "flex");
            $("#" + id + " .PluginActionError").delay(1500).fadeOut(2500);
        }
    }).done(function (data) {
        $("#"+id+" .switch").attr("disabled", false);
        $("#" + id + " .AddPluginSuccess").fadeOut(3000);
    })
}

function disactivatePlugin(id) {
    $("#"+id+" .switch").attr("disabled", true);

    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: 'ws.php',
        data: { method: 'pwg.plugins.performAction', 
                action: 'deactivate', 
                plugin: id, 
                pwg_token: pwg_token, 
                format: 'json' },
        success: function (data) {
            if (data.stat == 'ok') {
                let pluginName = id;
                $("#" + id + " .pluginNotif").stop(false, true);
                $("#" + id + " .DeactivatePluginSuccess label span:first").html(plugin_deactivated_str);
                $("#" + id + " .DeactivatePluginSuccess").css("display", "flex");

                nb_plugin.inactive += 1;
                nb_plugin.active -= 1;
                actualizeFilter();
            }
        }, 
        error: function (e) {
            console.log(e);
            console.log("It didn't work");
            $("#" + id + " .pluginNotif").stop(false, true);
            $("#" + id + " .PluginActionError label span:first").html(plugin_action_error);
            $("#" + id + " .PluginActionError").css("display", "flex");
            $("#" + id + " .PluginActionError").delay(1500).fadeOut(2500);
        }
    }).done(function (data) {
        $("#"+id+" .switch").attr("disabled", false);
        $("#" + id + " .DeactivatePluginSuccess").fadeOut(3000);
    })
}

function deletePlugin(id, name) {
    $.alert({
        title : deleted_plugin_msg.replace("%s",name),
        content: function() {
        return $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: 'ws.php',
                    data: { method: 'pwg.plugins.performAction', 
                            action: 'delete', 
                            plugin: id, 
                            pwg_token: pwg_token, 
                            format: 'json' },
                    success: function (data) {
                        if (data.stat === "ok") {
                            $("#"+id).remove();  
                            nb_plugin.inactive -=1;
                            nb_plugin.all -=1;
                            actualizeFilter();
                        }
                    }, 
                    error: function (e) {
                        console.log(e);
                        console.log("It didn't work");
                        $("#" + id + " .pluginNotif").stop(false, true);
                        $("#" + id + " .PluginActionError label span:first").html(plugin_action_error);
                        $("#" + id + " .PluginActionError").css("display", "flex");
                        $("#" + id + " .PluginActionError").delay(1500).fadeOut(2500);
                    }
                })
            },
        ...jConfirm_alert_options
    });
}

function restorePlugin(id) {
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: 'ws.php',
        data: { method: 'pwg.plugins.performAction', 
                action: 'restore', 
                plugin: id, 
                pwg_token: pwg_token, 
                format: 'json' },
        success: function (data) {
            if (data.stat == 'ok') {
                let pluginName = id;
                $("#" + id + " .pluginNotif").stop(false, true);
                $("#" + id + " .RestorePluginSuccess label span:first").html(plugin_restored_str);
                $("#" + id + " .RestorePluginSuccess").css("display", "flex");
            }
        }, 
        error: function (e) {
            console.log(e);
            console.log("It didn't work");
            $("#" + id + " .pluginNotif").stop(false, true);
            $("#" + id + " .PluginActionError label span:first").html(plugin_action_error);
            $("#" + id + " .PluginActionError").css("display", "flex");
            $("#" + id + " .PluginActionError").delay(1500).fadeOut(2500);
        }
    }).done(function (data) {
        $("#" + id + " .RestorePluginSuccess").fadeOut(3000);
    })
}

function uninstallPlugin(id) {
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: 'ws.php',
        data: { method: 'pwg.plugins.performAction', 
                action: 'uninstall', 
                plugin: id, 
                pwg_token: pwg_token, 
                format: 'json' },
        success: function (data) {
            $("#"+id).remove();
            nb_plugin.other -=1;
            nb_plugin.all -=1;
            actualizeFilter();
        }, 
        error: function (e) {
          console.log(e);
          console.log("It didn't work");
          $("#" + id + " .pluginNotif").stop(false, true);
          $("#" + id + " .PluginActionError label span:first").html(plugin_action_error);
          $("#" + id + " .PluginActionError").css("display", "flex");
          $("#" + id + " .PluginActionError").delay(1500).fadeOut(2500);
        }
    })
}

$(document).ready(function () {
    actualizeFilter();

    if (!$.cookie("pwg_plugin_manager_view")) {
        $.cookie("pwg_plugin_manager_view", "classic");
    }

    if ($("#displayClassic").is(":checked")) {
        setDisplayClassic();
    };

    if ($("#displayCompact").is(":checked")) {
        setDisplayCompact();
    };

    if ($("#displayLine").is(":checked")) {
        setDisplayLine();
    };

    $("#displayClassic").change(function () {
        setDisplayClassic();
        $.cookie("pwg_plugin_manager_view", "classic");
    })

    $("#displayCompact").change(function () {
        setDisplayCompact();
        $.cookie("pwg_plugin_manager_view", "compact");
    })

    $("#displayLine").change(function () {
        setDisplayLine();
        $.cookie("pwg_plugin_manager_view", "line");
    })

    /* Plugin Filters */

    // Set filter on Active on load
    if (nb_plugin.active > 0) {
      $(".pluginMiniBox").each(function () {
        if (!$(this).hasClass("plugin-active")) {
            $(this).hide();
        }
      });
      $("#seeActive").trigger("click");
    } else {
      $(".pluginMiniBox").show();
    }


    $("#seeAll").on("change", function () {
        $(".pluginMiniBox").show();
        $('.search-input').trigger("input");
    })

    $("#seeActive").on("change", function () {
        $(".pluginMiniBox").show();
        $(".pluginMiniBox").each(function () {
            if (!$(this).hasClass("plugin-active")) {
                $(this).hide();
            }
        })
        $('.search-input').trigger("input");
    })

    $("#seeInactive").on("change", function () {
        $(".pluginMiniBox").show();
        $(".pluginMiniBox").each(function () {
            if (!$(this).hasClass("plugin-inactive")) {
                $(this).hide();
            }
        })
        $('.search-input').trigger("input");
    })

    $("#seeOther").on("change", function () {
        $(".pluginMiniBox").show();
        $(".pluginMiniBox").each(function () {
            if (($(this).hasClass("plugin-active") || $(this).hasClass("plugin-inactive"))) {
                $(this).hide();
            }
        })
        $('.search-input').trigger("input");
    })

    /* Plugin Actions */ 
    /**
     * Activate / Deactivate
     */
    if (isWebmaster != 0) {
      $(".switch").change(function () {
      $(".pluginMiniBox").addClass("usable");

        if ($(this).find("#toggleSelectionMode").is(':checked')) {
            activatePlugin($(this).parent().parent().attr("id"));

            $(this).parent().parent().addClass("plugin-active").removeClass("plugin-inactive");
            if ($(this).parent().parent().find(".pluginUnavailableAction").attr("href")) {
                $(this).parent().parent().find(".pluginUnavailableAction").removeClass("pluginUnavailableAction").addClass("pluginActionLevel1");
            }
        } else {
            disactivatePlugin($(this).parent().parent().attr("id"))

            $(this).parent().parent().removeClass("plugin-active").addClass("plugin-inactive");
            $(this).parent().parent().find(".pluginActionLevel1").removeClass("pluginActionLevel1").addClass("pluginUnavailableAction");
        }
        
        actualizeFilter();
      })
    } else {
      $(".pluginMiniBox").addClass("notUsable");
      $(".plugin-active").find(".slider").addClass("desactivate_disabled");
      $(".plugin-inactive").find(".slider").addClass("activate_disabled");
      $(".switch input").on("click", function (event) {
        $(this).addClass("disabled");
        event.preventDefault();
        event.stopPropagation();

        var id = $(this).parent().parent().parent().attr("id");
        $("#" + id + " .pluginNotif").stop(false, true);
        $("#" + id + " .PluginActionError label span:first").html(not_webmaster);
        $("#" + id + " .PluginActionError").css("display", "flex");
        $("#" + id + " .PluginActionError").delay(1500).fadeOut(2500);

        setTimeout(function(){
          $(".switch input").removeClass("disabled");
        }, 400); //Same duration as the animation "desactivate_disabled" in css
      });
    }

    /**
     * Delete
     */
    $(".pluginContent").find('.dropdown-option.delete-plugin-button').on('click', function () {
        let plugin_name = $(this).closest(".pluginContent").find(".pluginMiniBoxNameCell").html().trim();
        let plugin_id = $(this).closest(".pluginContent").parent().attr("id");
        $.confirm({
          title: delete_plugin_msg.replace("%s",plugin_name),
          buttons: {
            confirm: {
              text: confirm_msg,
              btnClass: 'btn-red',
              action: function () {
                deletePlugin(plugin_id, plugin_name);
              },
            },
            cancel: {
              text: cancel_msg
            }
          },
          ...jConfirm_confirm_options
        })
      })

      /**
       * Restore
       */
      $(".pluginContent").find('.dropdown-option.plugin-restore').on('click', function () {
        let plugin_name = $(this).closest(".pluginContent").find(".pluginMiniBoxNameCell").html().trim();
        let plugin_id = $(this).closest(".pluginContent").parent().attr("id");
        $.confirm({
          title: restore_plugin_msg.replace('%s', plugin_name),
          buttons: {
            confirm: {
              text: confirm_msg,
              btnClass: 'btn-red',
              action: function () {
                restorePlugin(plugin_id);
              },
            },
            cancel: {
              text: cancel_msg
            }
          },
          ...jConfirm_confirm_options
        })
      })

      /**
       * Uninstall
       */
      $(".pluginContent").find('.uninstall-plugin-button').on('click', function () {
        let plugin_name = $(this).closest(".pluginContent").find(".pluginMiniBoxNameCell").html().trim();
        let plugin_id = $(this).closest(".pluginContent").parent().attr("id");
        $.confirm({
          title: uninstall_plugin_msg.replace('%s', plugin_name),
          buttons: {
            confirm: {
              text: confirm_msg,
              btnClass: 'btn-red',
              action: function () {
                uninstallPlugin(plugin_id);
              },
            },
            cancel: {
              text: cancel_msg
            }
          },
          ...jConfirm_confirm_options
        })
      })
})