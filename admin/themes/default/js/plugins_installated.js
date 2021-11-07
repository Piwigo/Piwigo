function setDisplayClassic() {
    console.log("DISPLAY CLASSIC");
    $(".pluginContainer").removeClass("line").removeClass("compact").addClass("classic");

    $(".pluginDesc").show();
    $(".pluginDescCompact").hide();
    $(".pluginActions").show();
    $(".pluginActionsSmallIcons").hide();

    $(".pluginMiniBoxNameCell").removeClass("pluginMiniBoxNameCellCompact");

    // normalTitle();
}

function setDisplayCompact() {
    console.log("DISPLAY COMPACT");
    $(".pluginContainer").removeClass("line").addClass("compact").removeClass("classic");

    $(".pluginDesc").hide();
    $(".pluginDescCompact").show();
    $(".pluginActions").hide();
    $(".pluginActionsSmallIcons").show();

    $(".pluginMiniBoxNameCell").addClass("pluginMiniBoxNameCellCompact");

    // reduceTitle()
}

function setDisplayLine() {
    console.log("DISPLAY LINE");
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

function actualizeFilterBadges() {
  $("label[for='seeAll'] .filter-badge").html(nb_plugin.all);
  $("label[for='seeActive'] .filter-badge").html(nb_plugin.active);
  $("label[for='seeInactive'] .filter-badge").html(nb_plugin.inactive);
  $("label[for='seeOther'] .filter-badge").html(nb_plugin.other);
}

function activatePlugin(id) {
    console.log("Plugin activated");
    console.log(id);

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
                actualizeFilterBadges();
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
        console.log(data);
        $("#"+id+" .switch").attr("disabled", false);
        $("#" + id + " .AddPluginSuccess").fadeOut(3000);
    })
}

function disactivatePlugin(id) {
    console.log("Plugin disactivated");
    console.log(id);
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
                actualizeFilterBadges(nb_plugin.all, nb_plugin.active, nb_plugin.inactive, nb_plugin.other)
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
        console.log(data);
        $("#"+id+" .switch").attr("disabled", false);
        $("#" + id + " .DeactivatePluginSuccess").fadeOut(3000);
    })
}

function deletePlugin(id, name) {
    console.log("Plugin deletetion");
    console.log(id);
    console.log(pwg_token);

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
                            actualizeFilter();
                        }
                        nb_plugin.inactive -=1;
                        nb_plugin.all -=1;
                        actualizeFilterBadges();
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
    console.log("Plugin restoration");
    console.log(id);
    console.log(pwg_token);

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
    console.log("Plugin uninstallated");
    console.log(id);
    console.log(pwg_token);

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
            console.log(data);
            console.log("it works (uninstallated)");
            nb_plugin.other -=1;
            nb_plugin.all -=1;
            actualizeFilterBadges();
        }, 
        error: function (e) {
            console.log(e);
            console.log("It didn't work");
        }
    })
}

function actualizeFilter() {
    $(".filterLabel").hide();
    $(".pluginMiniBox").each(function () {
        if ($(this).hasClass("plugin-active")) {
            $("label[for='seeActive']").show();
        }
        if ($(this).hasClass("plugin-inactive")) {
            $("label[for='seeInactive']").show();
        }
        if (($(this).hasClass("plugin-merged")) || ($(this).hasClass("plugin-missing"))) {
            $("label[for='seeOther']").show();
        }
    })
}

$(document).ready(function () {
    actualizeFilter();
    actualizeFilterBadges();

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
      })
    } else {
      $(".pluginMiniBox").show();
    }


    $("#seeAll").on("change", function () {
        console.log("All");
        $(".pluginMiniBox").show();
        $('.search-input').trigger("input");
    })

    $("#seeActive").on("change", function () {
        console.log("Active");
        $(".pluginMiniBox").show();
        $(".pluginMiniBox").each(function () {
            if (!$(this).hasClass("plugin-active")) {
                $(this).hide();
            }
        })
        $('.search-input').trigger("input");
    })

    $("#seeInactive").on("change", function () {
        console.log("Inactive");
        $(".pluginMiniBox").show();
        $(".pluginMiniBox").each(function () {
            if (!$(this).hasClass("plugin-inactive")) {
                $(this).hide();
            }
        })
        $('.search-input').trigger("input");
    })

    $("#seeOther").on("change", function () {
        console.log("Other");
        $(".pluginMiniBox").show();
        $(".pluginMiniBox").each(function () {
            if (($(this).hasClass("plugin-active") || $(this).hasClass("plugin-inactive"))) {
                $(this).hide();
            }
        })
        $('.search-input').trigger("input");
    })

    console.log(nb_plugin);

    /* Plugin Actions */ 
    /**
     * Activate / Deactivate
     */
    if (isWebmaster != 0) {
      $(".switch").change(function () {
      $(".pluginMiniBox").addClass("usable");

        if ($(this).find("#toggleSelectionMode").is(':checked')) {
            activatePlugin($(this).parent().parent().attr("id"));
            console.log("activatePlugin");

            $(this).parent().parent().addClass("plugin-active").removeClass("plugin-inactive");
            if ($(this).parent().parent().find(".pluginUnavailableAction").attr("href")) {
                $(this).parent().parent().find(".pluginUnavailableAction").removeClass("pluginUnavailableAction").addClass("pluginActionLevel1");
            }
        } else {
            disactivatePlugin($(this).parent().parent().attr("id"))
            console.log("disactivatePlugin");

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
        console.log($(this).closest(".pluginContent").parent().attr("id"));
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
        console.log($(this).closest(".pluginContent").parent().attr("id"));
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
        console.log($(this).closest(".pluginContent").parent().attr("id"));
        $.confirm({
          title: restore_plugin_msg.replace('%s', plugin_name),
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