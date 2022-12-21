$(document).ready(() => {
  formatedData = data;

  $("h1").append(`<span class='badge-number'>`+nb_albums+`</span>`);

  // console.log(formatedData);
  $('.tree').tree({
    data: formatedData,
    autoOpen : false,
    dragAndDrop: true,
    openFolderDelay: delay_autoOpen,
    onCreateLi : createAlbumNode,
    onCanSelectNode: function(node) {return false}
  });

  var url_split = window.location.href.split("cat_move");
  var catToOpen = url_split[url_split.length-1].split("-")[1];

  if(catToOpen && isNumeric(catToOpen)) {
    nodeToGo = $('.tree').tree('getNodeById', catToOpen);

    goToNode(nodeToGo, nodeToGo);
    if (nodeToGo.children) {
      $(".tree").tree("openNode", nodeToGo, false);
    }
  }

  $('.tree').on( 'click', '.move-cat-toogler', function(e) {
    var node_id = $(this).attr('data-id');
    var node = $('.tree').tree('getNodeById', node_id);
    if (node) {
      open_nodes = $('.tree').tree('getState').open_nodes;
      if (!open_nodes.includes(node_id)) {
        $(this).html(toggler_open);
        $('.tree').tree('openNode', node);
      } else {
        $(this).html(toggler_close);
        $('.tree').tree('closeNode', node);
      }
    }
  });

  $('.tree').on(
    'tree.open',
    function(e) {
      $('.move-cat-toogler[data-id='+e.node.id+']').html(toggler_open);
    }
  );

  $('.tree').on(
    'tree.close',
    function(e) {
      $('.move-cat-toogler[data-id='+e.node.id+']').html(toggler_close);
    }
  );

  $('.tree').on(
    'tree.move',
    function(event) {
      event.preventDefault();

      if (event.move_info.moved_node.status != 'private') {
        parentIsPrivate = false;
        if (event.move_info.position == 'after') {
          parentIsPrivate = (event.move_info.target_node.parent.status == 'private');
        } else if (event.move_info.position == 'inside') {
          parentIsPrivate = (event.move_info.target_node.status == 'private');
        }
        
        if (parentIsPrivate) {
          $.confirm({
            title: str_are_you_sure.replace(/%s/g, event.move_info.moved_node.name),
            buttons: {
              confirm: {
                text: str_yes_change_parent,
                btnClass: 'btn-red',
                action: function () {
                  makePrivateHierarchy(event.move_info.moved_node);
                  applyMove(event);
                },
              },
              cancel: {
                text: str_no_change_parent
              }
            },
            ...jConfirm_confirm_options
          })
        } else {
          applyMove(event);
        }
      } else {
        applyMove(event);
      }
    }
  );

  $('.tree').on( 'click', '.move-cat-order', function(e) {
    var node_id = $(this).attr('data-id');
    var node = $('.tree').tree('getNodeById', node_id);
    if (node) {
      $('.cat-move-order-popin').fadeIn();
      $('.cat-move-order-popin .album-name').html(getPathNode(node));
      $('.cat-move-order-popin input[name=id]').val(node_id);
      $('input[name=simpleAutoOrder]').attr('value', str_sub_album_order);
    }
  });

  $('.order-root').on( 'click', function() {
    $('.cat-move-order-popin').fadeIn();
    $('.cat-move-order-popin .album-name').html(str_root);
    $('.cat-move-order-popin input[name=id]').val(-1);
    $('input[name=simpleAutoOrder]').attr('value', str_root_order);
  });

  $('.tree').on('mousedown mouseup', function mouseState(e) {
    if (e.type == "mousedown") {
      $(".tree").addClass("dragging")
    } else if (e.type == "mouseup") {
      $(".dragging").removeClass("dragging")
    }
  });

  if (openCat != -1) {
    var node = $('.tree').tree('getNodeById', openCat);
    $('.tree').tree('openNode', node);
    $([document.documentElement, document.body]).animate({
      scrollTop: $("#cat-"+openCat).offset().top
    }, 500);
  }

  // RenameAlbumPopIn
  $(".RenameAlbumErrors").hide();
  $(".move-cat-title-container").on("click", function () {
    openRenameAlbumPopIn($(this).find(".move-cat-title").attr("title"));
    $(".RenameAlbumSubmit").data("cat_id", $(this).attr('data-id'));
  });
  $(".CloseRenameAlbum").on("click", function () {
    closeRenameAlbumPopIn();
  });
  $(".RenameAlbumCancel").on("click", function () {
    closeRenameAlbumPopIn();
  })
  
  $(".RenameAlbumSubmit").on("click", function () {
    catToEdit = $(this).data("cat_id");
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.categories.setInfo",
      type: "POST",
      data: {
        category_id : catToEdit,
        name : $(".RenameAlbumLabelUsername input").val(),
      },
      success: function (raw_data) {
        data = jQuery.parseJSON(raw_data);
        $("#cat-"+catToEdit).find(".move-cat-title-container p.move-cat-title").html($(".RenameAlbumLabelUsername input").val());
        $("#cat-"+catToEdit).find(".move-cat-title-container p.move-cat-title").attr('title', $(".RenameAlbumLabelUsername input").val());
        closeRenameAlbumPopIn();
      },
      error: function(message) {
        console.log(message);
      }
    });
  })

  // AddAlbumPopIn
  $(".AddAlbumErrors").hide();
  $(".DeleteAlbumErrors").hide();
  $(".add-album-button").on("click", function () {
    openAddAlbumPopIn(0);
    $(".AddAlbumSubmit").data("a-parent", 0);
  })
  $(".move-cat-add").on("click", function () {
    openAddAlbumPopIn($(this).data("aid"));
    $(".AddAlbumSubmit").data("a-parent", $(this).data("aid"));
  })
  $(".CloseAddAlbum").on("click", function () {
    closeAddAlbumPopIn();
  });
  $(".AddAlbumCancel").on("click", function () {
    closeAddAlbumPopIn();
  });
  $(".DeleteAlbumCancel").on("click", function () {
    closeDeleteAlbumPopIn();
  });

  $(".AddAlbumSubmit").on("click", function () {
    $(this).addClass("notClickable");

    newAlbumName = $(".AddAlbumLabelUsername input").val();
    newAlbumParent = $(".AddAlbumSubmit").data("a-parent");
    newAlbumPosition = $("input[name=position]:checked").val();

    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.categories.add",
      type: "POST",
      data: {
        name : newAlbumName,
        parent : newAlbumParent,
        position : newAlbumPosition
      },
      success: function (raw_data) {
        data = jQuery.parseJSON(raw_data);
        var parent_node = $('.tree').tree('getNodeById', newAlbumParent);
        
        if (data.stat == "ok") {
          if (newAlbumPosition == "last") {
            $('.tree').tree(
              'appendNode',
              {
                id: data.result.id,
                isEmptyFolder: true,
                name: newAlbumName
              },
              parent_node
            );
          } else {
            $('.tree').tree(
              'prependNode',
              {
                id: data.result.id,
                isEmptyFolder: true,
                name: newAlbumName
              },
              parent_node
            );
          }
  
          if (parent_node) {
            setSubcatsBadge(parent_node);
  
            $("#cat-"+parent_node.id).on( 'click', '.move-cat-toogler', function(e) {
              var node_id = parent_node.id;
              var node = $('.tree').tree('getNodeById', node_id);
              if (node) {
                open_nodes = $('.tree').tree('getState').open_nodes;
                if (!open_nodes.includes(node_id)) {
                  $(this).html(toggler_open);
                  $('.tree').tree('openNode', node);
                } else {
                  $(this).html(toggler_close);
                  $('.tree').tree('closeNode', node);
                }
              }
            });
          } 
          
          $(".move-cat-add").unbind("click").on("click", function () {
            openAddAlbumPopIn($(this).data("aid"));
            $(".AddAlbumSubmit").data("a-parent", $(this).data("aid"));
          });
          $(".move-cat-delete").on("click", function () {
            triggerDeleteAlbum($(this).data("id"));
          });
          $(".move-cat-title-container").unbind("click").on("click", function () {
            openRenameAlbumPopIn($(this).find(".move-cat-title").attr("title"));
            $(".RenameAlbumSubmit").data("cat_id", $(this).attr('data-id'));
          });
          $('.tiptip').tipTip({
            delay: 0,
            fadeIn: 200,
            fadeOut: 200,
            edgeOffset: 3
          });

          updateTitleBadge(nb_albums+1)

          goToNode($(".tree").tree('getNodeById', data.result.id), $(".tree").tree('getNodeById', data.result.id));
          $('html,body').animate({
            scrollTop: $("#cat-" + data.result.id).offset().top - screen.height / 2},
            'slow');

          closeAddAlbumPopIn();
          $(".AddAlbumSubmit").removeClass("notClickable");
        } else {
          $(".AddAlbumErrors").text(str_album_name_empty).show();
          $(".AddAlbumSubmit").removeClass("notClickable");
        }
      },
      error: function(message) {
        console.log(message);
      }
    });
  })

  // Delete Album
  $(".move-cat-delete").on("click", function () {
    triggerDeleteAlbum($(this).data("id"));
  });

  $('.user-list-checkbox').unbind("change").change(checkbox_change);
  $('.user-list-checkbox').unbind("click").click(checkbox_click);

  if (!light_album_manager) {
    $('.tiptip').tipTip({
      delay: 0,
      fadeIn: 200,
      fadeOut: 200,
      edgeOffset: 3
    });
  }
});

function createAlbumNode(node, li) {
  icon = "<span class='%icon%'></span>";
  title = '<span data-id="'+node.id+'" class="move-cat-title-container ';
  if (node.status == 'private') {
    title += 'icon-lock';
  }
  title += '"><p class="move-cat-title" title="'+node.name+'">%name%</p> <span class="icon-pencil"></span> </span>';
  toggler_cont = "<div class='move-cat-toogler' data-id=%id%>%content%</div>";
  toggler_close = "<span class='icon-left-open'></span>";
  toggler_open = "<span class='icon-down-open'></span>";
  actions = 
    '<div class="move-cat-action-cont">'
      +"<div class='move-cat-action'>"
        +'<a class="move-cat-add icon-add-album tiptip" title="'+ str_add_album +'" href="#" data-aid="'+node.id+'"></a>'
        +'<a class="move-cat-edit icon-pencil tiptip" title="'+ str_edit_album +'" href="admin.php?page=album-'+node.id+'"></a>'
        +'<a class="move-cat-upload icon-plus-circled tiptip" title="'+ str_add_photo +'" href="admin.php?page=photos_add&album='+node.id+'"></a>'
        +'<a class="move-cat-see icon-eye tiptip" title="'+ str_visit_gallery +'" href="index.php?/category/'+node.id+'"></a>'
        +'<a data-id="'+node.id+'" class="move-cat-order icon-sort-name-up tiptip" title="'+ str_sort_order +'"></a>'
        +'<a data-id="'+node.id+'" class="move-cat-delete icon-trash tiptip" title="'+ str_delete_album +'" ></a>'
      +"</div>"
    +'</div>';
  // action_order = '<a data-id="'+node.id+'" class="move-cat-order icon-sort-name-up tiptip" title="'+ str_sort_order +'"></a>';

  cont = li.find('.jqtree-element');
  cont.addClass('move-cat-container');
  cont.attr('id', 'cat-'+node.id)
  cont.html('');

  cont.append(actions);

  cont.find(".toggle-cat-option").on("click", function () {
    $(".cat-option").hide();
    $(this).find(".cat-option").toggle();
  });

  if (node.children.length != 0) {
    open_nodes = $('.tree').tree('getState').open_nodes;
    if (open_nodes.includes(node.id)) {
      toggler = toggler_open;
    } else {
      toggler = toggler_close;
    }
    cont.append($(toggler_cont
      .replace(/%content%/g, toggler)
      .replace(/%id%/g, node.id)));
  } else {
    cont.find('.move-cat-order').addClass("notClickable");

    cont.append($(toggler_cont
      .replace(/%content%/g, toggler_close)
      .replace(/%id%/g, node.id))).addClass("disabledToggle");
  }

  cont.append($(icon.replace(/%icon%/g, 'icon-grip-vertical-solid')));

  if (node.children.length != 0) {
    cont.append($(icon.replace(/%icon%/g, 'icon-sitemap')));
  } else {
    cont.append($(icon.replace(/%icon%/g, 'icon-folder-open')));
  }

  cont.append($(title.replace(/%name%/g, node.name)));

  var colors = ["icon-red", "icon-blue", "icon-yellow", "icon-purple", "icon-green"];
  var colorId = Number(node.id)%5;
  cont.find("span.icon-folder-open, span.icon-sitemap").addClass(colors[colorId]).addClass("node-icon");

  cont.find(".move-cat-title-container").after(
    "<div class='badge-container'>" 
      +"<i class='icon-blue icon-sitemap nb-subcats'>"+node.nb_subcats+"</i>"
      +"<i class='icon-purple icon-picture nb-images'>"+node.nb_images+"</i>"
      +"<i class='icon-green icon-imagefolder-01 nb-sub-photos'>"+node.nb_sub_photos+"</i>"
        +"<div class='badge-dropdown'>"
          +"<span class='icon-blue icon-sitemap nb-subcats'>"+x_nb_subcats.replace('%d', node.nb_subcats)+"</span>"
          +"<span class='icon-purple icon-picture nb-images'>"+x_nb_images.replace('%d', node.nb_images)+"</span>"
          +"<span class='icon-green icon-imagefolder-01 nb-sub-photos'>"+x_nb_sub_photos.replace('%d', node.nb_sub_photos)+"</span>"
        +"</div>"
    +"</div>"
  )

  if (!(node.nb_subcats)) {
    cont.find(".nb-subcats").hide();
  }

  if (!(node.nb_images != 0 && node.nb_images)) {
    cont.find(".nb-images").hide();
  }

  if (!(node.nb_sub_photos)) {
    cont.find(".nb-sub-photos").hide();
  }

  if (node.has_not_access) {
    cont.find(".move-cat-see").addClass("notClickable");
  }
}

/*----------------
Checkboxes
----------------*/

function checkbox_change() {
  if ($(this).attr('data-selected') == '1') {
      $(this).find("i").hide();
  } else {
      $(this).find("i").show();
  }
}

function checkbox_click() {
  if ($(this).attr('data-selected') == '1') {
      $(this).attr('data-selected', '0');
      $(this).find("i").hide();
  } else {
      $(this).attr('data-selected', '1');
      $(this).find("i").show();
  }
}

function isNumeric(num){
  return !isNaN(num)
}

function openAddAlbumPopIn(parentAlbumId) {
  if (parentAlbumId != 0) {
    $("#AddAlbum .AddIconTitle span").html(add_sub_album_of.replace("%s", $(".tree").tree('getNodeById', parentAlbumId).name));
  } else {
    $("#AddAlbum .AddIconTitle span").html(add_album_root_title)
  }
  $("#AddAlbum").fadeIn();
  $(".AddAlbumLabelUsername .user-property-input").val('');
  $(".AddAlbumLabelUsername .user-property-input").focus();

  $("#AddAlbum").unbind('keyup');
  $("#AddAlbum").on('keyup', function (e) {
    // 13 is 'Enter'
    if(e.keyCode === 13) {
      $(".AddAlbumSubmit").trigger("click");
    }
    // 27 is 'Escape'
    if(e.keyCode === 27) {
      closeAddAlbumPopIn();
    }
  })
}

function closeAddAlbumPopIn() {
  $("#AddAlbum").fadeOut();
}

function openRenameAlbumPopIn(replacedAlbumName) {
  $("#RenameAlbum").fadeIn();
  $(".RenameAlbumTitle span").html(rename_item.replace("%s", replacedAlbumName))
  $(".RenameAlbumLabelUsername .user-property-input").val(replacedAlbumName);
  $(".RenameAlbumLabelUsername .user-property-input").focus();

  $(document).unbind("keypress").on('keypress',function(e) {
    if(e.which == 13) {
      $(".RenameAlbumSubmit").trigger("click");
    }
  });
}

function closeRenameAlbumPopIn() {
  $("#RenameAlbum").fadeOut();
}

function triggerDeleteAlbum(cat_id) {
  $.ajax({
    url: "ws.php?format=json&method=pwg.categories.calculateOrphans",
    type: "GET",
    data: {
      category_id: cat_id,
    },
    success: function (raw_data) {
      let data = JSON.parse(raw_data).result[0]
      if (data.nb_images_recursive == 0) {
        $(".deleteAlbumOptions").hide();
      } else {
        $(".deleteAlbumOptions").show();
        if (data.nb_images_associated_outside == 0) {
          $("#IMAGES_ASSOCIATED_OUTSIDE").hide();
        } else {
          $("#IMAGES_ASSOCIATED_OUTSIDE .innerText").html("");
          $("#IMAGES_ASSOCIATED_OUTSIDE .innerText").append(has_images_associated_outside.replace('%d', data.nb_images_recursive).replace('%d', data.nb_images_associated_outside));
        }
        if (data.nb_images_becoming_orphan == 0) {
          $("#IMAGES_BECOMING_ORPHAN").hide();
        } else {
          $("#IMAGES_BECOMING_ORPHAN .innerText").html("");
          $("#IMAGES_BECOMING_ORPHAN .innerText").append(has_images_becomming_orphans.replace('%d', data.nb_images_becoming_orphan));
        }
      }
    },
    error: function(message) {
      console.log(message);
    }
  }).done(function () {
    openDeleteAlbumPopIn(cat_id);
  });
}

function openDeleteAlbumPopIn(cat_to_delete) {
  $("#DeleteAlbum").fadeIn();
  node = $(".tree").tree('getNodeById', cat_to_delete);
  if (node.children.length == 0) {
    $(".DeleteIconTitle span").html(delete_album_with_name.replace("%s", node.name));
  } else {
    nb_sub_cats = 0;
    $(".DeleteIconTitle span").html(delete_album_with_subs.replace("%s", node.name).replace("%d", getAllSubAlbumsFromNode(node, nb_sub_cats)));
  }

  // Actually delete
  $(".DeleteAlbumSubmit").unbind("click").on("click", function () {
    $.ajax({
      url: "ws.php?format=json&method=pwg.categories.delete",
      type: "POST",
      data: {
        category_id: cat_to_delete,
        photo_deletion_mode: $("input[name=photo_deletion_mode]:checked").val(),
        pwg_token: pwg_token,
      },
      success: function (raw_data) {
        parentOfDeletedNode = node.parent
        $('.tree').tree('removeNode', node);

        $(".move-cat-add").on("click", function () {
          openAddAlbumPopIn($(this).data("aid"));
          $(".AddAlbumSubmit").data("a-parent", $(this).data("aid"));
        });
        $(".move-cat-delete").on("click", function () {
          triggerDeleteAlbum($(this).data("id"));
        });
        $(".move-cat-title-container").unbind("click").on("click", function () {
          openRenameAlbumPopIn($(this).find(".move-cat-title").attr("title"));
          $(".RenameAlbumSubmit").data("cat_id", $(this).attr('data-id'));
        });
        $('.tiptip').tipTip({
          delay: 0,
          fadeIn: 200,
          fadeOut: 200,
          edgeOffset: 3
        });

        updateTitleBadge(nb_albums-1);
        setSubcatsBadge(parentOfDeletedNode);
        closeDeleteAlbumPopIn();
      },
      error: function(message) {
        console.log(message);
      }
    });
  })

}

function closeDeleteAlbumPopIn() {
  $("#DeleteAlbum").fadeOut();
}

function getAllSubAlbumsFromNode(node, nb_sub_cats) {
  nb_sub_cats = 0;
  if (node.children != 0) {
    node.children.forEach(child => {
      nb_sub_cats++;
      tmp = getAllSubAlbumsFromNode(child, nb_sub_cats);
      nb_sub_cats += tmp;
    });
  } else {
    return 0;
  }
  return nb_sub_cats;
}

function setSubcatsBadge(node) {
  if (node.children.length != 0) {
    $("#cat-"+node.id).find(".nb-subcats").text(node.children.length).show(100);
  } else {
    $("#cat-"+node.id).find(".nb-subcats").hide(100)
  }
}

function updateTitleBadge(new_nb_albums) {
  nb_albums = new_nb_albums;
  $(".badge-number").text(new_nb_albums);
}

function goToNode(node, firstNode) {
  // console.log(firstNode.id, node.id);
  if (node.parent) {
    goToNode(node.parent, firstNode);
    if(node != firstNode) {
      $(".tree").tree('openNode', node);
      // console.log("parent id : " + node.parent.id);
      $("#cat-"+node.parent.id).show();
      $("#cat-"+node.parent.id).addClass("imune");
    }
  } else {
    $(".tree").tree('openNode', node);
    $("#cat-"+firstNode.id).addClass("animateFocus");

    showNodeChildrens(firstNode);
  }
}

function showNodeChildrens(node) {
  if (node.children) {
    // console.log("childrens : " + node.children);
    node.children.forEach(child => {
      // console.log("children : " + child.id, child.name);
      $("#cat-"+child.id).addClass("imune");
      showNodeChildrens(child);
    });
    
  }
}

function closeTree(tree) {
  // console.log(tree);
  if (tree.tree('getState').open_nodes.length > 0) {
    tree.tree('getState').open_nodes.forEach(nodeItem => {
      var node = tree.tree('getNodeById', nodeItem);
      tree.tree('closeNode', node);
    });
  }

}

function getId(parent) {
  if (parent.getLevel() == 0) {
    return 0;
  } else {
    return parent.id;
  }
}

function getRank(node, ignoreId = null) {
  if (node.getPreviousSibling() != null) {
    if (node.id != ignoreId) {
      return 1 + getRank(node.getPreviousSibling(), ignoreId);
    } else {
      return getRank(node.getPreviousSibling(), ignoreId);
    }
  } else {
    if (node.id != ignoreId) {
      return 1;
    } else {
      return 0;
    }
  }
}

function applyMove(event) {
  waitingTimeout = setTimeout(() => {
    $('.waiting-message').addClass('visible');  
  }, 500);
  id = event.move_info.moved_node.id;
  moveParent = null;
  moveRank = null;
  previous_parent = event.move_info.previous_parent;
  target = event.move_info.target_node;
  if (event.move_info.position == 'after') {
    if (getId(previous_parent) != getId(target.parent)) {
      moveParent = getId(target.parent);
    }
    moveRank = getRank(target, id) + 1;
  } else if (event.move_info.position == 'inside') {
    if (getId(previous_parent) != getId(target)) {
      moveParent = getId(target);
    }
    moveRank = 1;
  } else if (event.move_info.position == 'before') {
    if (getId(previous_parent) != getId(target.parent)) {
      moveParent = getId(target.parent);
    }
    moveRank = 1;
  } 
  moveNode(id, moveRank, moveParent).then(() => {
    event.move_info.do_move();
    clearTimeout(waitingTimeout);
    $('.waiting-message').removeClass('visible');
    setSubcatsBadge(previous_parent);
    setSubcatsBadge($('.tree').tree('getNodeById', moveParent));

    $(".move-cat-add").unbind("click").on("click", function () {
      openAddAlbumPopIn($(this).data("aid"));
      $(".AddAlbumSubmit").data("a-parent", $(this).data("aid"));
    });
    $(".move-cat-delete").on("click", function () {
      triggerDeleteAlbum($(this).data("id"));
    });
    $(".move-cat-title-container").on("click", function () {
      openRenameAlbumPopIn($(this).find(".move-cat-title").attr("title"));
      $(".RenameAlbumSubmit").data("cat_id", $(this).attr('data-id'));
    });
    $('.tiptip').tipTip({
      delay: 0,
      fadeIn: 200,
      fadeOut: 200,
      edgeOffset: 3
    });
  })
    .catch(function (message) {
      console.log('An error has occured : ' + message );
      $(".move-cat-add").unbind("click").on("click", function () {
        openAddAlbumPopIn($(this).data("aid"));
        $(".AddAlbumSubmit").data("a-parent", $(this).data("aid"));
      });
      $(".move-cat-delete").on("click", function () {
        triggerDeleteAlbum($(this).data("id"));
      });
      $(".move-cat-title-container").on("click", function () {
        openRenameAlbumPopIn($(this).find(".move-cat-title").attr("title"));
        $(".RenameAlbumSubmit").data("cat_id", $(this).attr('data-id'));
      });
      $('.tiptip').tipTip({
        delay: 0,
        fadeIn: 200,
        fadeOut: 200,
        edgeOffset: 3
      });
    })
}

function moveNode(node, rank, parent) {
  return new Promise ((res, rej) => {
    if (parent != null) {
      changeParent(node, parent, rank).then(() => res()).catch(() => rej())
    } else if (rank != null) {
      changeRank(node, rank).then(() => res()).catch(() => rej())
    }
  })
}

function changeParent(node, parent, rank) {
  oldParent = node.parent
  return new Promise((res, rej) => {
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.categories.move",
      type: "POST",
      data: {
        category_id : node,
        parent : parent,
        pwg_token : pwg_token
      },
      before: function () {
        oldParent = node.parent
      },
      success: function (raw_data) {
        data = jQuery.parseJSON(raw_data);
        if (data.stat === "ok") {
          changeRank(node, rank)
          res();
        } else {
          rej(raw_data);
        }
      },
      error: function(message) {
        rej(message);
      }
    });
  })
}

function changeRank(node, rank) {
  return new Promise((res, rej) => {
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.categories.setRank",
      type: "POST",
      data: {
        category_id : node,
        rank : rank
      },
      success: function (raw_data) {
        data = jQuery.parseJSON(raw_data);
        if (data.stat === "ok") {
          res();
        } else {
          rej(raw_data);
        }
      },
      error: function(message) {
        rej(message);
      }
    });
  })
}

function makePrivateHierarchy(node) {
  node.status = 'private';
  node.children.forEach(node => {
    makePrivateHierarchy(node);
  });
}

function getPathNode(node) {
  if (node.parent.getLevel() != 0) {
    return getPathNode(node.parent) + ' / ' + node.name;
  } else {
    return node.name;
  }
}
