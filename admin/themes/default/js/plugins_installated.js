function setDisplayClassic() {
    $(".pluginContainer").removeClass("line-form").removeClass("compact-form").addClass("classic-form");

    $(".pluginDesc").show();
    $(".pluginActions").show();
    $(".pluginActionsSmallIcons").hide();

    $(".pluginName").removeClass("pluginNameCompact");

    // normalTitle();
}

function setDisplayCompact() {
    $(".pluginContainer").removeClass("line-form").addClass("compact-form").removeClass("classic-form");

    $(".pluginDesc").hide();
    $(".pluginActions").hide();
    $(".pluginActionsSmallIcons").show();

    $(".pluginName").addClass("pluginNameCompact");

    // reduceTitle()
}

function setDisplayLine() {
    $(".pluginContainer").addClass("line-form").removeClass("compact-form").removeClass("classic-form");

    $(".pluginDesc").show();
    $(".pluginActions").show();
    $(".pluginActionsSmallIcons").hide();
    // normalTitle();
}

function reduceTitle() {
    var x = document.getElementsByClassName("pluginName");
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
    var x = document.getElementsByClassName("pluginName");

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
            console.log(e.responseText);
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
          $("#" + id + " .pluginNotif").stop(false, true);
          $("#" + id + " .PluginActionError label span:first").html(plugin_action_error);
          $("#" + id + " .PluginActionError").css("display", "flex");
          $("#" + id + " .PluginActionError").delay(1500).fadeOut(2500);
          console.log(e.message);
        }
    })
}

$(document).ready(function () {
    actualizeFilter();

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
        set_view_selector('classic');
    })

    $("#displayCompact").change(function () {
        setDisplayCompact();
        set_view_selector('compact');
    })

    $("#displayLine").change(function () {
        setDisplayLine();
        set_view_selector('line');
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
        $(".pluginBox").show();
        $('.search-input').trigger("input");
    })

    $("#seeActive").on("change", function () {
        $(".pluginBox").show();
        $(".pluginBox").each(function () {
            if (!$(this).hasClass("plugin-active")) {
                $(this).hide();
            }
        })
        $('.search-input').trigger("input");
    })

    $("#seeInactive").on("change", function () {
        $(".pluginBox").show();
        $(".pluginBox").each(function () {
            if (!$(this).hasClass("plugin-inactive")) {
                $(this).hide();
            }
        })
        $('.search-input').trigger("input");
    })

    $("#seeOther").on("change", function () {
        $(".pluginBox").show();
        $(".pluginBox").each(function () {
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
        let plugin_name = $(this).closest(".pluginContent").find(".pluginName").html().trim();
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
        let plugin_name = $(this).closest(".pluginContent").find(".pluginName").html().trim();
        let plugin_id = $(this).closest(".pluginContent").parent().attr("id");
        $.confirm({
          title: restore_plugin_msg.replace('%s', plugin_name),
          content: str_restore_def,
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
        let plugin_name = $(this).closest(".pluginContent").find(".pluginName").html().trim();
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

function set_view_selector(view_type) {
  $.ajax({
    url: "ws.php?format=json&method=pwg.users.preferences.set",
    type: "POST",
    dataType: "JSON",
    data: {
      param: 'plugin-manager-view',
      value: view_type,
    }
  })
}

// TPL part :

const queuedManager = jQuery.manageAjax.create("queued", {
    queue: true,
    maxRequests: 1,
  });
  
const nb_plugins = jQuery("div.active").size();
const done = 0;

function showInactivePlugins () {
  jQuery(".showInactivePlugins").fadeOut(
    (complete = function () {
      jQuery(".plugin-inactive").fadeIn();
    })
  );
};

function actualizeFilter() {
  $("label[for='seeAll'] .filter-badge").html(nb_plugin.all);
  $("label[for='seeActive'] .filter-badge").html(nb_plugin.active);
  $("label[for='seeInactive'] .filter-badge").html(nb_plugin.inactive);
  $("label[for='seeOther'] .filter-badge").html(nb_plugin.other);
  $(".filterLabel").show();
  
  $(".pluginMiniBox").each(function () {
    if (nb_plugin.active == 0) {
      $("label[for='seeActive']").hide();
      if ($("#seeActive").is(":checked")) {
        $("#seeAll").trigger("click");
      }
    }
    if (nb_plugin.inactive == 0) {
      $("label[for='seeInactive']").hide();
      if ($("#seeInactive").is(":checked")) {
        $("#seeAll").trigger("click");
      }
    }
    if (nb_plugin.other == 0) {
      $("label[for='seeOther']").hide();
      if ($("#seeOther").is(":checked")) {
        $("#seeAll").trigger("click");
      }
    }
  });
}

function performPluginDeactivate(id) {
  queuedManager.add({
    type: "GET",
    dataType: "json",
    url: "ws.php",
    data: {
      method: "pwg.plugins.performAction",
      action: "deactivate",
      plugin: id,
      pwg_token: pwg_token,
      format: "json",
    },
    success: function (data) {
      if (data["stat"] == "ok")
        jQuery("#" + id)
          .removeClass("active")
          .addClass("inactive");
      done++;
      if (done == nb_plugins) location.reload();
    },
  });
}

/* group action */

jQuery(document).ready(function () {
    $("label[for='seeActive'] .filter-badge").html(nb_plugin.active);
    $("label[for='seeInactive'] .filter-badge").html(nb_plugin.inactive);
    $("label[for='seeOther'] .filter-badge").html(nb_plugin.other);
    $(".filterLabel").show(); 

    $(".pluginBox").each(function () {
        if (nb_plugin.active == 0) {
            $("label[for='seeActive']").hide();
            if ($("#seeActive").is(":checked")) {
            $("#seeAll").trigger("click");
            }
        }
        if (nb_plugin.inactive == 0) {
            $("label[for='seeInactive']").hide();
            if ($("#seeInactive").is(":checked")) {
            $("#seeAll").trigger("click");
            }
        }
        if (nb_plugin.other == 0) {
            $("label[for='seeOther']").hide();
            if ($("#seeOther").is(":checked")) {
            $("#seeAll").trigger("click");
            }
        }

        let myplugin = jQuery(this);
        myplugin.find(".showOptions").click(function () {
            myplugin.find(".PluginOptionsBlock").toggle();
        });
    });

    jQuery("div.deactivate_all a").click(function () {
        $.confirm({
        title: deactivate_all_msg,
        buttons: {
            confirm: {
            text: confirm_msg,
            btnClass: "btn-red",
            action: function () {
                jQuery("div.active").each(function () {
                performPluginDeactivate(jQuery(this).attr("id"));
                });
            },
            },
            cancel: {
            text: cancel_msg,
            },
        },
        ...jConfirm_confirm_options,
        });
    });

    /* incompatible plugins */
    jQuery.ajax({
        method: "GET",
        url: "admin.php",
        data: { page: "plugins_installed", incompatible_plugins: true },
        dataType: "json",
        success: function (data) {
        for (i = 0; i < data.length; i++) {
            if (show_details)
            jQuery("#" + data[i] + " .pluginName").prepend(
                '<a class="warning" title="' + incompatible_msg + '"></a>'
            );
            else
            jQuery("#" + data[i] + " .pluginName").prepend(
                '<span class="warning" title="' + incompatible_msg + '"></span>'
            );
            jQuery("#" + data[i]).addClass("incompatible");
            jQuery("#" + data[i] + " .activate").each(function () {
            $(this).pwg_jconfirm_follow_href({
                alert_title: incompatible_msg + activate_msg,
                alert_confirm: confirm_msg,
                alert_cancel: cancel_msg,
            });
            });
        }
        jQuery(".warning").tipTip({
            delay: 0,
            fadeIn: 200,
            fadeOut: 200,
            maxWidth: "250px",
        });
        },
    });

    jQuery(".fullInfo").tipTip({
        delay: 500,
        fadeIn: 200,
        fadeOut: 200,
        maxWidth: "300px",
        keepAlive: false,
    });

    /*Add the filter research*/
    document.onkeydown = function (e) {
        if (e.keyCode == 58) {
        jQuery(".pluginFilter input.search-input").focus();
        return false;
        }
    };

    jQuery(".pluginFilter input").on("input", function () {
        let text = jQuery(this).val().toLowerCase();
        var searchNumber = 0;

        var searchActive = 0;
        var searchInactive = 0;
        var searchOther = 0;

        $(".pluginBox").each(function () {
        if (text == "") {
            jQuery(".nbPluginsSearch").hide();
            if ($("#seeAll").is(":checked")) {
            jQuery(this).show();
            }
            if (
            $("#seeActive").is(":checked") &&
            jQuery(this).hasClass("plugin-active")
            ) {
            jQuery(this).show();
            }
            if (
            $("#seeInactive").is(":checked") &&
            jQuery(this).hasClass("plugin-inactive")
            ) {
            jQuery(this).show();
            }
            if (
            $("#seeOther").is(":checked") &&
            (jQuery(this).hasClass("plugin-merged") ||
                jQuery(this).hasClass("plugin-missing"))
            ) {
            jQuery(this).show();
            }

            if ($(this).hasClass("plugin-active")) {
            searchActive++;
            }
            if ($(this).hasClass("plugin-inactive")) {
            searchInactive++;
            }
            if (
            $(this).hasClass("plugin-merged") ||
            $(this).hasClass("plugin-missing")
            ) {
            searchOther++;
            }
            searchNumber++;

            nb_plugin.all = searchNumber;
            nb_plugin.active = searchActive;
            nb_plugin.inactive = searchInactive;
            nb_plugin.other = searchOther;
        } else {
            let name = jQuery(this).find(".pluginName").text().toLowerCase();
            jQuery(".nbPluginsSearch").show();
            let description = jQuery(this).find(".pluginDesc").text().toLowerCase();
            if (name.search(text) != -1 || description.search(text) != -1) {
            searchNumber++;

            if ($("#seeAll").is(":checked")) {
                jQuery(this).show();
            }
            if (
                $("#seeActive").is(":checked") &&
                jQuery(this).hasClass("plugin-active")
            ) {
                jQuery(this).show();
            }
            if (
                $("#seeInactive").is(":checked") &&
                jQuery(this).hasClass("plugin-inactive")
            ) {
                jQuery(this).show();
            }
            if (
                $("#seeOther").is(":checked") &&
                (jQuery(this).hasClass("plugin-merged") ||
                jQuery(this).hasClass("plugin-missing"))
            ) {
                jQuery(this).show();
            }

            if ($(this).hasClass("plugin-active")) {
                searchActive++;
            }
            if ($(this).hasClass("plugin-inactive")) {
                searchInactive++;
            }
            if (
                $(this).hasClass("plugin-merged") ||
                $(this).hasClass("plugin-missing")
            ) {
                searchOther++;
            }

            nb_plugin.all = searchNumber;
            nb_plugin.active = searchActive;
            nb_plugin.inactive = searchInactive;
            nb_plugin.other = searchOther;
            } else {
            jQuery(this).hide();

            nb_plugin.all = searchNumber;
            nb_plugin.active = searchActive;
            nb_plugin.inactive = searchInactive;
            nb_plugin.other = searchOther;
            }
        }
        });

        actualizeFilter();

        if (searchNumber == 0) {
        jQuery(".nbPluginsSearch").html(nothing_found);
        } else if (searchNumber == 1) {
        jQuery(".nbPluginsSearch").html(plugin_found.replace("%s", searchNumber));
        } else {
        jQuery(".nbPluginsSearch").html(
            x_plugins_found.replace("%s", searchNumber)
        );
        }
    });

    /* Show Inactive plugins or button to show them*/
    jQuery(".showInactivePlugins button").on("click", showInactivePlugins);

    if (plugin_filter == "deactivated") {
      jQuery(".filterLabel[for='seeInactive']").trigger("click");
    }
});

$(document).mouseup(function (e) {
  e.stopPropagation();
  $(".pluginBox").each(function () {
    if ($(this).find(".showOptions").has(e.target).length === 0) {
      $(this).find(".PluginOptionsBlock").hide();
    }
  });
});