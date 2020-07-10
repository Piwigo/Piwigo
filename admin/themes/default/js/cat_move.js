$(document).ready(() => {

  formatedData = $.map(data, function(value, index) {
    return [formatInArray(value)];
  });

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
    actions_tree ="<a class='move-cat-manage icon-cog'>"+str_manage_sub_album+"</a>"
        +"<a class='move-cat-order icon-sort-alt-up'>"+str_apply_order+"</a>";

    cont = li.find('.jqtree-element')
    cont.addClass('move-cat-container')
    cont.html('');
    cont.append($(icon.replace(/%icon%/g, 'icon-ellipsis-vert')));

    if (node.children.length != 0) {
      cont.append($(icon.replace(/%icon%/g, 'icon-flow-tree')));
    } else {
      cont.append($(icon.replace(/%icon%/g, 'icon-folder-open')));
    }
    
    cont.append($(title.replace(/%name%/g, node.name)))
    if (node.status == 'private') {
      cont.append($(icon.replace(/%icon%/g, 'icon-lock')))
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

      cont.find('.move-cat-action').append(actions_tree);
    }



    var colors = ["icon-red", "icon-blue", "icon-yellow", "icon-purple", "icon-green"];
    var colorId = Number(node.id)%5;
    cont.find(".icon-folder-open, .icon-flow-tree").addClass(colors[colorId]);  
  }

  $('.tree').on( 'click', '.move-cat-toogler', function(e) {
    console.log('clic');
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
                  event.move_info.moved_node.status = 'private';
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
});

function formatInArray(obj) {
  if (obj['children'] != null) {
    obj['children'] = $.map(obj['children'], function(value, index) {
      return [formatInArray(value)];
    });
  }
  return obj;
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
  }
  moveNode(id, moveRank, moveParent).then(() => event.move_info.do_move())
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
