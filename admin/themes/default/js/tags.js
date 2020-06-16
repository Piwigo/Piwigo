//Orphan tags
$('.tag-warning p a').on('click', () => {
  let url = $('.tag-warning p a').data('url');
  let tags = $('.tag-warning p a').data('tags');
  let str_orphans = str_orphan_tags.replace('%s1', tags.length).replace('%s2', tags.join(', '));
  $.confirm({
    content : str_orphans,
    title : str_delete_orphan_tags,
    draggable: false,
    theme: "modern",
    animation: "zoom",
    boxWidth: '30%',
    useBootstrap: false,
    type: 'red',
    animateFromElement: false,
    backgroundDismiss: true,
    typeAnimated: false,
    buttons: {
      delete : {
        text:str_delete_them,
        btnClass: 'btn-red',
        action: function() {
          window.location.href = url.replaceAll('amp;', '');
        }
      },
      keep : {
        text:str_keep_them,
      }
    }
  })
})

//Add a tag
$('.add-tag-container').on('click', function() {
  $('#add-tag').addClass('input-mode');
})

$('#add-tag .icon-cancel').on('click', function() {
  $('#add-tag').removeClass('input-mode');
})

//Display/Hide tag option
$('.tag-box').each(function() {
  setupTagbox($(this))
})

/*-------
 Add a tag
-------*/

$('#add-tag').submit(function (e) {
  e.preventDefault();
  if ($('#add-tag-input').val() != "") {
    loadState = new TemporaryState();
    loadState.removeClass($('#add-tag .icon-validate'),'icon-plus-circled');
    loadState.changeHTML($('#add-tag .icon-validate') , "<i class='icon-spin6 animate-spin'> </i>")
    loadState.changeAttribute($('#add-tag .icon-validate'), 'style','pointer-event:none')
    addTag($('#add-tag-input').val()).then(function () {
      showMessage(str_tag_created.replace('%s', $('#add-tag-input').val()))
      loadState.reverse();
      $('#add-tag-input').val("");
      $('#add-tag').removeClass('input-mode');
    }).catch(message => {
      loadState.reverse();
      showError(message)
    })
  }
});

$('#add-tag .icon-validate').on('click', function () {
  if ($('#add-tag').hasClass('input-mode')) {
    $('#add-tag').submit();
  }
})

function addTag(name) {
  return new Promise((resolve, reject) => {
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.tags.add",
      type: "POST",
      data: "name=" + name + "&pwg_token=" + pwg_token,
      success: function (raw_data) {
        data = jQuery.parseJSON(raw_data);
        if (data.stat === "ok") {
          newTag = createTagBox(data.result.id, name);
          $('.tag-container').prepend(newTag);
          setupTagbox(newTag);
          resolve();
        } else {
          reject(str_already_exist.replace('%s', name));
        }
      },
      error : function (err) {
        reject(err);
      }
    })
  })
}

function createTagBox(id, name) {
  let u_edit = 'admin.php?page=batch_manager&filter=tag-'+id;
  let u_view = 'index.php?/tags/'+id+'-'+name.toLowerCase().replace(' ', '_');
  let html = $('.tag-template').html()
    .replaceAll('%name%', unescape(name))
    .replace('%U_VIEW%', u_view)
    .replace('%U_EDIT%', u_edit);
  newTag = $('<div class="tag-box" data-id='+data.result.id+' data-selected="0">'+html+'</div>');
  if ($("#toggleSelectionMode").is(":checked")) {
    newTag.addClass('selection');
    newTag.find(".in-selection-mode").show();
    newTag.find(".not-in-selection-mode").hide();
  }
  return newTag;
}

/*-------
 Setup Tag Box
-------*/

function setupTagbox(tagBox) {

  let id = tagBox.data('id');
  let name = tagBox.find('.tag-name').html();
  
  //Dropdown options
  tagBox.find('.showOptions').on('click', function () {
    tagBox.find(".tag-dropdown-block").css('display', 'grid');
  })

  $(document).mouseup(function (e) {
    e.stopPropagation();
    let option_is_clicked = false
    tagBox.find('.tag-dropdown-action').each(function () {
      if (!($(this).has(e.target).length === 0)) {
        option_is_clicked = true;
      }
    })
    if (!option_is_clicked) {
      tagBox.find(".tag-dropdown-block").hide();
    }
  });

  tagBox.on('click', function() {
    if (tagBox.hasClass('selection')) {
      if (tagBox.attr('data-selected') == '1') {
        tagBox.attr('data-selected', '0');
      } else {
        tagBox.attr('data-selected', '1');
      }
      updateListItem();
    }
  })

  //Edit Name
  tagBox.find('.tag-dropdown-action.edit').on('click', function() {
    tagBox.addClass('edit-name');
  })

  tagBox.find('.tag-rename .icon-cancel').on('click', function() {
    tagBox.removeClass('edit-name');
  })

  tagBox.find('.tag-rename .validate').on('click', function() {
    tagBox.find('.tag-rename form').submit();
  })

  tagBox.find('.tag-rename form').submit(function (e) {
    e.preventDefault();
    new_name = tagBox.find('.tag-rename .tag-name-editable').val();
    if (new_name != "") {
      let loadState = new TemporaryState();
      loadState.removeClass(tagBox.find('.tag-rename .validate'), 'icon-ok');
      loadState.changeHTML(tagBox.find('.tag-rename .validate'), "<i class='icon-spin6 animate-spin'> </i>");
      renameTag(id, new_name).then(() => {
        showMessage(str_tag_renamed.replace('%s1', name).replace('%s2', new_name));
        loadState.reverse();
        tagBox.removeClass('edit-name');
        name = new_name;
      }).catch((message) => {
        loadState.reverse();
        showError(message);
      })
    }
  })

  //Delete Tag
  tagBox.find('.tag-dropdown-action.delete').on('click', function () {
    $.confirm({
      title: str_delete.replace("%s",name),
      buttons: {
        confirm: {
          text: str_yes_delete_confirmation,
          btnClass: 'btn-red',
          action: function () {
            removeTag(id, name);
          },
        },
        cancel: {
          text: str_no_delete_confirmation
        }
      },
      ...jConfirm_confirm_options
    })
  })

  //Duplicate Tag
  tagBox.find('.tag-dropdown-action.duplicate').on('click', function () {
    duplicateTag(id, name).then((data) => {
      showMessage(str_tag_created.replace('%s',data.result.name))
    })
  })

}

function removeTag(id, name) {
  $.alert({
      title : str_tag_deleted.replace("%s",name),
      content: function() {
      return jQuery.ajax({
        url: "ws.php?format=json&method=pwg.tags.delete",
        type: "POST",
        data: "tag_id=" + id + "&pwg_token=" + pwg_token,
        success: function (raw_data) {
          data = jQuery.parseJSON(raw_data);
          showMessage(str_tag_deleted.replace('%s', name));
          if (data.stat === "ok") {
            $('.tag-box[data-id='+id+']').remove();
          }
        }
      })
    },
    ...jConfirm_alert_options
  });
}

function renameTag(id, new_name) {
  return new Promise((resolve, reject) => {
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.tags.rename",
      type: "POST",
      data: "tag_id=" + id + "&new_name=" + new_name + "&pwg_token=" + pwg_token,
      success: function (raw_data) {
        data = jQuery.parseJSON(raw_data);
        if (data.stat === "ok") {
          $('.tag-box[data-id='+id+'] p').html(data.result.name);
          $('.tag-box[data-id='+id+'] .tag-name-editable').attr('placeholder', data.result.name);
          resolve(data);
        } else {
          reject(str_already_exist.replace('%s', new_name))
        }
      },
      error:function(XMLHttpRequest) {
        reject(XMLHttpRequest.statusText);
      }
    })
  })
}

function duplicateTag(id, name) {
  return new Promise((resolve, reject) => {
    copy_name = name + str_copy;

    let name_exist = function(name) {
      exist = false;
      $(".tag-box .tag-name").each(function () {
        if ($(this).html() === name)
          exist = true
      })
      return exist;
    }

    let i = 1;
    while (name_exist(copy_name)) 
    {
      copy_name = name + str_other_copy.replace("%s", i++)
    }

    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.tags.duplicate",
      type: "POST",
      data: "tag_id=" + id + "&copy_name=" + copy_name + "&pwg_token=" + pwg_token,
      success: function (raw_data) {
        data = jQuery.parseJSON(raw_data);
        if (data.stat === "ok") {
          newTag = createTagBox(data.result.id, data.result.name);
          newTag.insertAfter($('.tag-box[data-id='+id+']'));
          if ($('.tag-box[data-id='+id+'] .tag-dropdown-action.view').css('display') == 'inline') {
            newTag.find('.tag-dropdown-action.view').show();
            newTag.find('.tag-dropdown-action.manage').show();
          }
          setupTagbox(newTag);
          resolve(data);
        }
      },
      error:function(XMLHttpRequest) {
        reject(XMLHttpRequest.statusText);
      }
    })
  })
}

/*-------
 Selection mode
-------*/
$("#toggleSelectionMode").attr("checked", false)
$("#toggleSelectionMode").click(function () {
  if ($(this).is(":checked")) {
    $(".in-selection-mode").show();
    $(".not-in-selection-mode").removeAttr('style');
    $(".tag-box").addClass("selection");
    $(".tag-box").removeClass('edit-name');
  } else {
    $(".in-selection-mode").removeAttr('style');
    $(".not-in-selection-mode").show();
    $(".tag-box").removeClass("selection");
    $(".tag-box").attr("data-selected", '0');
    updateListItem();
  }
});

function updateListItem() {

  let nowSelected = [];
  let selected = [];
  let shouldBeItem = [];
  let shouldNotBeItem = [];
  let names = {};
  $('.tag-box[data-selected="1"]').each(function () {
    let id = $(this).attr('data-id');
    nowSelected.push(id);
    names[id] = $(this).find('.tag-name').html();
  });

  $('.selection-mode-tag .tag-list div').each(function () {
    let id = $(this).attr('data-id');
    selected.push(id);
  });

  shouldNotBeItem = [...selected];
  shouldNotBeItem = shouldNotBeItem.filter(x => !nowSelected.includes(x));
  shouldBeItem = [...nowSelected];
  shouldBeItem = shouldBeItem.filter(x => !selected.includes(x));
  selected = nowSelected;
  
  shouldBeItem.forEach(function(id) {
    let newItemStructure = $('<div data-id="'+id+'"><a class="icon-cancel"></a><p>'+names[id]+'</p> </div>');
    $('.selection-mode-tag .tag-list').prepend(newItemStructure);
    $('.selection-mode-tag .tag-list div[data-id='+id+'] a').on('click', function () {
      $('.tag-box[data-id='+id+']').attr('data-selected', '0');
      updateListItem();
    })
  })

  shouldNotBeItem.forEach(function(id) {
    $('.selection-mode-tag .tag-list div[data-id='+id+']').remove();
  })

  $('#MergeOptionsChoices').html('');
  nowSelected.forEach(id => {
    $('#MergeOptionsChoices').append(
      $('<option value="'+id+'">'+names[id]+'</option>')
    )
  })

  updateSelectionContent()
}

mergeOption = false;

function updateSelectionContent() {
  number = $('.tag-box[data-selected="1"]').length;
  if (number == 0) {
    $('#nothing-selected').show();
    $('.selection-mode-tag').hide();
    $('#MergeOptionsBlock').hide();
  } else if (number == 1) {
    mergeOption = false;
    $('#nothing-selected').hide();
    $('.selection-mode-tag').show();
    $('#MergeOptionsBlock').hide();
    $('#MergeSelectionMode').addClass('unavailable');
  } else if (number > 1) {
    $('#MergeSelectionMode').removeClass('unavailable');
    if (mergeOption) {
      $('#MergeOptionsBlock').show();
      $('.selection-mode-tag').hide();
    } else {
    $('#MergeOptionsBlock').hide();
    $('.selection-mode-tag').show();
    }
  }
    
}

$('#MergeSelectionMode').on('click', function() {
  mergeOption = true;
  updateSelectionContent()
});

$('#CancelMerge').on('click', function() {
  mergeOption = false;
  updateSelectionContent()
});

/*-------
 Actions in selection mode
-------*/

//Remove tags
$('#DeleteSelectionMode').on('click', function() {
  names = [];

  $('.tag-box[data-selected=1]').each(function() {
    names.push($(this).find('.tag-name').html());
  })

  $.confirm({
    title: str_delete_tags.replace("%s",names.join(', ')),
    buttons: {
        confirm: {
          text: str_yes_delete_confirmation,
          btnClass: 'btn-red',
          action: function () {
            removeSelectedTags();
          }
        },
        cancel: {
          text: str_no_delete_confirmation
        }
    },
    ...jConfirm_confirm_options
  });
})

function removeSelectedTags() {
  str_id = "";
  names = [];
  ids = [];

  $('.tag-box[data-selected=1]').each(function() {
    id = $(this).data('id');
    ids.push(id);
    names.push($(this).find('.tag-name').html());
    str_id += "tag_id[]=" + id + "&";
  })

  console.log(names);

  $.alert({
    title : str_tags_deleted.replace("%s",names.join(', ')),
    content: function() {
      return jQuery.ajax({
        url: "ws.php?format=json&method=pwg.tags.delete",
        type: "POST",
        data: str_id + "pwg_token=" + pwg_token,
        success: function (raw_data) {
          data = jQuery.parseJSON(raw_data);
          if (data.stat === "ok") {
            ids.forEach(function(id) {
              $('.tag-box[data-id='+id+']').remove();
            })
            updateListItem();
            showMessage(str_tags_deleted.replace('%s', names.join(', ')));
          }
        }
      })
    },
    ...jConfirm_alert_options
  });
}

//Merge Tags
$('.ConfirmMergeButton').on('click',() => {
  merge_ids = [];
  $('.tag-box[data-selected=1]').each(function() {
    merge_ids.push($(this).data('id'))
  })
  dest_id = $('#MergeOptionsChoices').val();
  mergeGroups(dest_id, merge_ids)
})

function mergeGroups(destination_id, merge_ids) {

  destination_name = $('.tag-box[data-id='+destination_id+'] .tag-name').html();
  merge_name = [];

  merge_ids.forEach((id) =>{
    merge_name.push($('.tag-box[data-id='+id+'] .tag-name').html());
  })
  
  str_message = str_merged_into
    .replace('%s1', merge_name.join(', '))
    .replace('%s2', destination_name)

  $.alert({
    title : str_message,
    content: function() {
      return jQuery.ajax({
        url: "ws.php?format=json&method=pwg.tags.merge",
        type: "POST",
        data: "destination_tag_id=" + destination_id 
          + "&merge_tag_id[]=" + merge_ids.join('&merge_tag_id[]=')
          + "&pwg_token=" + pwg_token,
        success: function (raw_data) {
          data = jQuery.parseJSON(raw_data);
          if (data.stat === "ok") {
            console.log()
            data.result.deleted_tag.forEach((id) => {
              if (data.result.destination_tag != id)
                $('.tag-box[data-id='+id+']').remove();
            })
            if (data.result.images_in_merged_tag.length > 0) {
              tagBox = $('.tag-box[data-id='+data.result.destination_tag+']')
              tagBox.find('.tag-dropdown-action.view, .tag-dropdown-action.manage').show();
            }
            showMessage(str_message);
            $(".tag-box").attr("data-selected", '0');
            updateListItem();
          }
        }
      })
    },
    ...jConfirm_alert_options
  });
}

/*-------
 Filter research
-------*/

$("#search-tag .search-input").on("input", function() {
  let text = $(this).val().toLowerCase();
  var searchNumber = 0;
  $('.tag-box').each(function () {
    if (text == "") {
      $(this).fadeIn()
      searchNumber++;
    } else {
      let name = $(this).find("p").text().toLowerCase();
      if (name.search(text) != -1){
        $(this).delay(300).fadeIn()
        searchNumber++;
      } else {
        $(this).fadeOut()
      }
    }
  })
  if (searchNumber == 0) {
    $('.emptyResearch').delay(300).fadeIn();
  } else {
    $('.emptyResearch').fadeOut();
  }
})

/*-------
 Show Info
-------*/
function showError(message) {
  $('.tag-error p').html(message);
  $('.tag-info').hide()
  $('.tag-error').css('display', 'flex');
}

function showMessage(message) {
  $('.tag-message p').html(message);
  $('.tag-info').hide()
  $('.tag-message').css('display', 'flex');
}
