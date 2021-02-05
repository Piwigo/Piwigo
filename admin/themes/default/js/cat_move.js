$(document).ready(() => {

  formatedData = data;

  $('.tree').tree({
    data: formatedData,
    autoOpen : false,
    dragAndDrop: true,
    onCreateLi : createAlbumNode,
    onCanSelectNode: function(node) {return false}
  });

  function createAlbumNode(node, li) {
    icon = "<span class='%icon%'></span>";
    title = "<p class='move-cat-title' title='%name%'>%name%</p>";
    toggler_cont = "<div class='move-cat-toogler' data-id=%id%>%content%</div>";
    toggler_close = "<span class='icon-left-open'></span> <p>"+str_show_sub+"</p>";
    toggler_open = "<span class='icon-down-open'></span> <p>"+str_hide_sub+"</p>";
    actions = '<div class="move-cat-action-cont">'
        +"<div class='move-cat-action'>"
          +"<a class='move-cat-edit icon-pencil' href='admin.php?page=album-"+node.id+"'>"+str_edit+"</a>"
        +"</div>"
      +'</div>';
    action_order = "<a data-id='"+node.id+"' class='move-cat-order icon-sort-alt-up'>"+str_apply_order+"</a>";

    cont = li.find('.jqtree-element');
    cont.addClass('move-cat-container');
    cont.attr('id', 'cat-'+node.id)
    cont.html('');
    cont.append($(icon.replace(/%icon%/g, 'icon-grip-vertical-solid')));

    if (node.children.length != 0) {
      cont.append($(icon.replace(/%icon%/g, 'icon-sitemap')));
    } else {
      cont.append($(icon.replace(/%icon%/g, 'icon-folder-open')));
    }

    cont.append($(title.replace(/%name%/g, node.name)));

    if (node.status == 'private') {
      cont.find(".move-cat-title").addClass('icon-lock');
    }

    cont.append(actions);

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

      cont.find('.move-cat-action').append(action_order);
    }

    var colors = ["icon-red", "icon-blue", "icon-yellow", "icon-purple", "icon-green"];
    var colorId = Number(node.id)%5;
    cont.find(".icon-folder-open, .icon-sitemap").addClass(colors[colorId]);  
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
    }
  });

  $('.order-root').on( 'click', function() {
    $('.cat-move-order-popin').fadeIn();
    $('.cat-move-order-popin .album-name').html(str_root);
    $('.cat-move-order-popin input[name=id]').val(-1);
  });

  if (openCat != -1) {
    var node = $('.tree').tree('getNodeById', openCat);
    $('.tree').tree('openNode', node);
    $([document.documentElement, document.body]).animate({
      scrollTop: $("#cat-"+openCat).offset().top
    }, 500);
  }
});

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
    if (moveParent == null) {
      moveParent = 0
    }
    moveRank = 1;
  } 
  moveNode(id, moveRank, moveParent).then(() => {
    event.move_info.do_move();
    clearTimeout(waitingTimeout);
    $('.waiting-message').removeClass('visible');
  })
    .catch((message) => console.log('An error has occured : ' + message ));
}

function moveNode(node, rank, parent) {
  return new Promise ((res, rej) => {
    if (parent != null) {
      changeParent(node, parent).then(changeRank(node, rank)).then(() => res()).catch(() => rej())
    } else if (rank != null) {
      changeRank(node, rank).then(() => res()).catch(() => rej())
    }
  })
}

function changeParent(node, parent) {
  return new Promise((res, rej) => {
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.categories.move",
      type: "POST",
      data: {
        category_id : node,
        parent : parent,
        pwg_token : pwg_token
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

function makePrivateHierarchy (node) {
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
