//Get the data
var dataTags = $('.tag-container').data('tags');

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
        action: function() {
          $('.tag-warning').hide();
        }
      }
    }
  })
})


//Create and recycle tag box
function createTagBox(id, name, url_name, count) {
  let u_edit = 'admin.php?page=batch_manager&filter=tag-'+id;
  let u_view = 'index.php?/tags/'+id+'-'+url_name;
  let html = $('.tag-template').html()
    .replaceAll('%name%', unescape(name))
    .replace('%U_VIEW%', u_view)
    .replace('%U_EDIT%', u_edit)
  newTag = $('<div class="tag-box" data-id='+id+' data-selected="0">'+html+'</div>');
  if ($("#toggleSelectionMode").is(":checked")) {
    newTag.addClass('selection');
    newTag.find(".in-selection-mode").show();
  }
  if (count > 0) {
    newTag.find('.dropdown-option.view, .dropdown-option.manage').css('display', 'block');
    newTag.find('.tag-dropdown-header i').html(str_number_photos.replace('%d', count));
  } else {
    newTag.find('.tag-dropdown-header i').html(str_no_photos);
  }
  return newTag;
}

function recycleTagBox(tagBox, id, name, url_name, count) {
  tagBox = tagBox.first();
  tagBox.attr('data-id', id);
  tagBox.find('.tag-name, .tag-dropdown-header b').html(name);
  tagBox.find('.tag-name-editable').val(name)

  //Dropdown
  let u_edit = 'admin.php?page=batch_manager&filter=tag-'+id;
  let u_view = 'index.php?/tags/'+id+'-'+url_name;
  tagBox.find('.dropdown-option.view').attr('href', u_view);
  tagBox.find('.dropdown-option.manage').attr('href', u_edit);
  
  if (count > 0) {
    tagBox.find('.dropdown-option.view, .dropdown-option.manage').css('display', 'block');
    tagBox.find('.tag-dropdown-header i').html(str_number_photos.replace('%d', count));
  } else {
    tagBox.find('.tag-dropdown-header i').html(str_no_photos);
  }
}

//Number On Badge
function updateBadge() {
  $('.badge-number').html(dataTags.length)
}

//Add a tag
$('.add-tag-container').on('click', function() {
  $('#add-tag').addClass('input-mode');
  $('.tag-info').hide();
})

$('#add-tag .icon-cancel-circled').on('click', function() {
  $('#add-tag').removeClass('input-mode');
  $('.tag-info').hide();
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
      data: {
        name: name
      },
      success: function (raw_data) {
        data = jQuery.parseJSON(raw_data);
        if (data.stat === "ok") {
          newTag = createTagBox(data.result.id, data.result.name, data.result.url_name, 0);
          $('.tag-container').prepend(newTag);
          setupTagbox(newTag);
          updateBadge();

          //Update the data
          dataTags.unshift({
            name:data.result.name,
            id:data.result.id,
            url_name:data.result.url_name
          });

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
/*-------
 Setup Tag Box
-------*/

function setupTagbox(tagBox) {
  
  //Dropdown options
  tagBox.find('.showOptions').on('click', function () {
    tagBox.find(".tag-dropdown-block").css('display', 'grid');
  })

  $(document).mouseup(function (e) {
    e.stopPropagation();
    let option_is_clicked = false
    tagBox.find('.dropdown-option').each(function () {
      if (!($(this).has(e.target).length === 0)) {
        option_is_clicked = true;
      }
    })
    if (!option_is_clicked) {
      tagBox.find(".tag-dropdown-block").hide();
    }
  });

  tagBox.on('click', function() {
    if ($('.tag-container').hasClass('selection')) {
      if (tagBox.attr('data-selected') == '1') {
        tagBox.attr('data-selected', '0');
      } else {
        tagBox.attr('data-selected', '1');
      }
      updateListItem();
    }
  })

  //Edit Name
  tagBox.find('.dropdown-option.edit').on('click', function() {
    tagBox.addClass('edit-name');
  })

  tagBox.find('.tag-rename .icon-cancel').on('click', function() {
    tagBox.removeClass('edit-name');
    tagBox.find('.tag-name-editable').val(tagBox.find('.tag-name').html());
  })

  tagBox.find('.tag-rename .validate').on('click', function() {
    tagBox.find('.tag-rename form').submit();
  })

  tagBox.find('.tag-rename form').submit(function (e) {
    let name = tagBox.find('.tag-name').html();
    e.preventDefault();
    new_name = tagBox.find('.tag-rename .tag-name-editable').val();
    if (new_name != "") {
      let loadState = new TemporaryState();
      loadState.removeClass(tagBox.find('.tag-rename .validate'), 'icon-ok');
      loadState.changeHTML(tagBox.find('.tag-rename .validate'), "<i class='icon-spin6 animate-spin'> </i>");
      renameTag(tagBox.data('id'), new_name).then(() => {
        showMessage(str_tag_renamed.replace('%s1', name).replace('%s2', new_name));
        loadState.reverse();
        tagBox.removeClass('edit-name');
      }).catch((message) => {
        loadState.reverse();
        showError(message);
      })
    }
  })

  //Delete Tag
  tagBox.find('.dropdown-option.delete').on('click', function () {
    $.confirm({
      title: str_delete.replace("%s",tagBox.find('.tag-name').html()),
      buttons: {
        confirm: {
          text: str_yes_delete_confirmation,
          btnClass: 'btn-red',
          action: function () {
            removeTag(tagBox.data('id'), tagBox.find('.tag-name').html());
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
  tagBox.find('.dropdown-option.duplicate').on('click', function () {
    duplicateTag(tagBox.data('id'), tagBox.find('.tag-name').html()).then((data) => {
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
        data: {
          tag_id: id,
          pwg_token: pwg_token
        },
        success: function (raw_data) {
          data = jQuery.parseJSON(raw_data);
          
          if (data.stat === "ok") {
            $('.tag-box[data-id='+id+']').remove();
            //Update data
            dataTags = dataTags.filter((tag) => tag.id != id);
            showMessage(str_tag_deleted.replace('%s', name));
            updateBadge();
          } else {
            showError('A problem has occured')
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
      data: {
        tag_id: id, 
        new_name: new_name,
        pwg_token: pwg_token
      },
      success: function (raw_data) {
        data = jQuery.parseJSON(raw_data);
        if (data.stat === "ok") {
          $('.tag-box[data-id='+id+'] p, .tag-box[data-id='+id+'] .tag-dropdown-header b').html(data.result.name);
          $('.tag-box[data-id='+id+'] .tag-name-editable').attr('value', data.result.name);
          let u_view = 'index.php?/tags/'+id+'-'+data.result.url_name;
          $('.dropdown-option.view').attr('href', u_view);

          //Update the data
          index = dataTags.findIndex((tag) => tag.id == id);
          dataTags[index].name = data.result.name;
          dataTags[index].url_name = data.result.url_name;

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
      data: {
        tag_id : id,
        copy_name: copy_name, 
        pwg_token: pwg_token
      },
      success: function (raw_data) {
        data = jQuery.parseJSON(raw_data);
        if (data.stat === "ok") {
          newTag = createTagBox(data.result.id, data.result.name, data.result.url_name, data.result.count);
          newTag.insertAfter($('.tag-box[data-id='+id+']'));
          setupTagbox(newTag);

          //Update Data
          index = dataTags.findIndex((tag) => tag.id == id);
          dataTags.splice(index+1, 0, {
            name: data.result.name,
            id: data.result.id,
            url_name: data.result.url_name,
            counter : data.result.count
          });
          updateBadge()
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
maxItemDisplayed = 5;

$("#toggleSelectionMode").attr("checked", false)
$("#toggleSelectionMode").click(function () {
  selectionMode($(this).is(":checked"))
  $('.tag-info').hide()
});

function selectionMode(isSelection) {
  if (isSelection) {
    $(".in-selection-mode").addClass('show');
    $(".not-in-selection-mode").addClass('hide');
    $(".tag-container").addClass("selection");
    $(".tag-box").removeClass('edit-name');
  } else {
    $(".in-selection-mode").removeClass('show');
    $(".not-in-selection-mode").removeClass('hide');
    $(".tag-container").removeClass("selection");
    $(".tag-box").attr("data-selected", '0');
    updateListItem();
  }
}

function updateListItem() {

  let nowSelected = [];
  let selected = [];
  let shouldNotBeItem = [];
  let names = {};
  $('.tag-box[data-selected="1"]').each(function () {
    let id = $(this).attr('data-id');
    nowSelected.push(id);
    names[id] = $(this).find('.tag-name').html();
  });

  selected = $('.selection-mode-tag .tag-list').data('list');
  $('.selection-mode-tag .tag-list').attr('data-list', nowSelected);

  shouldNotBeItem = [...selected];
  shouldNotBeItem = shouldNotBeItem.filter(x => !nowSelected.includes(x));

  shouldNotBeItem.forEach(function(id) {
    $('.selection-mode-tag .tag-list div[data-id='+id+']').remove();
  })
  
  $('.selection-mode-tag .tag-list').html('');
  let i = 0;
  while(i < nowSelected.length && $('.selection-mode-tag .tag-list div').length < maxItemDisplayed) {
    let item = nowSelected[i++];
    let newItemStructure = $('<div data-id="'+item+'"><a class="icon-cancel"></a><p>'+names[item]+'</p> </div>');
    $('.selection-mode-tag .tag-list').prepend(newItemStructure);
    $('.selection-mode-tag .tag-list div[data-id='+item+'] a').on('click', function () {
      $('.tag-box[data-id='+item+']').attr('data-selected', '0');
      updateListItem();
    });
  }

  if (nowSelected.length > maxItemDisplayed) {
    $('.selection-other-tags').show();
    $('.selection-other-tags').html(str_and_others_tags.replace('%s', nowSelected.length - maxItemDisplayed))
  } else {
    $('.selection-other-tags').hide();
  }

  updateSelectionContent()
}

function updateMergeItems () {
  let ids = [];
  let names = [];
  $('.tag-box[data-selected="1"]').each(function () {
    ids.push($(this).attr('data-id'));
    names[$(this).attr('data-id')] = $(this).find('.tag-name').html();
  })

  $('#MergeOptionsChoices').html('');
  ids.forEach(id => {
    $('#MergeOptionsChoices').append(
      $('<option value="'+id+'">'+names[id]+'</option>')
    )
  })
}

mergeOption = false;

function updateSelectionContent() {
  number = $('.tag-box[data-selected="1"]').length;
  if (number == 0) {
    mergeOption = false;
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
    $('#nothing-selected').hide();
    $('#MergeSelectionMode').removeClass('unavailable');
    if (mergeOption) {
      $('#MergeOptionsBlock').show();
      $('.selection-mode-tag').hide();
      updateMergeItems();
    } else {
    $('#MergeOptionsBlock').hide();
    $('.selection-mode-tag').show();
    }
  }
    
}

$('#MergeSelectionMode').on('click', function() {
  mergeOption = true;
  updateSelectionContent();
});

$('#CancelMerge').on('click', function() {
  mergeOption = false;
  updateSelectionContent()
});

$('#selectAll').on('click', function() {
  if ($('.search-input').val() == '') {
    $('.tag-box').attr('data-selected', '1');
  } else {
    $('.tag-box').each(function() {
      if (isSearched($(this), $('.search-input').val())) {
        $(this).attr('data-selected', '1');
      }
    })
  }

  updateListItem();
});

$('#selectNone').on('click', function() {
  $('.tag-box').attr('data-selected', '0');
  updateListItem();
});

$('#selectInvert').on('click', function() {
  $('.tag-box').each(function() {
    if ($(this).attr('data-selected') == 1) {
      $(this).attr('data-selected', '0');
    } else {
      $(this).attr('data-selected', '1');
    }
  });
  updateListItem();
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
    title: str_delete_tags.replace("%s",tagListToString(names)),
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
  names = [];
  ids = [];

  $('.tag-box[data-selected=1]').each(function() {
    id = $(this).data('id');
    ids.push(id);
    names.push($(this).find('.tag-name').html());
  })

  $.alert({
    title : str_tags_deleted.replace("%s",tagListToString(names)),
    content: function() {
      return jQuery.ajax({
        url: "ws.php?format=json&method=pwg.tags.delete",
        type: "POST",
        data: {
          'pwg_token': pwg_token,
          'tag_id': ids
        },
        success: function (raw_data) {
          raw_data = raw_data.slice(raw_data.search('{'));
          if (JSON.parse(raw_data).stat = 'ok') {
            ids.forEach(function(id) {
              $('.tag-box[data-id='+id+']').remove();
            })
            updateListItem();

            // Update Data
            dataTags = dataTags.filter((tag) => !ids.includes(parseInt(tag.id)))

            updateBadge()
          } else {
            return raw_data;
          }
        },
        error: function(message) {
          return message;
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
    .replace('%s1', tagListToString(merge_name))
    .replace('%s2', destination_name)

  $.alert({
    title : str_message,
    content: function() {
      return jQuery.ajax({
        url: "ws.php?format=json&method=pwg.tags.merge",
        type: "POST",
        data: {
          'pwg_token': pwg_token,
          'destination_tag_id': destination_id,
          'merge_tag_id': merge_ids
        },
        success: function (raw_data) {
          raw_data = raw_data.slice(raw_data.search('{'));
          data = jQuery.parseJSON(raw_data);
          if (data.stat === "ok") {
            data.result.deleted_tag.forEach((id) => {
              if (data.result.destination_tag != id) {
                $('.tag-box[data-id='+id+']').remove();
                // Update data
                dataTags = dataTags.filter((tag) => id != tag.id);
              }
            })
            if (data.result.images_in_merged_tag.length > 0) {
              tagBox = $('.tag-box[data-id='+data.result.destination_tag+']')
              tagBox.find('.dropdown-option.view,'+ 
              '.dropdown-option.manage,'+
              '.tag-dropdown-header i').show();
              $('.tag-dropdown-header i').html(str_number_photos.replace('%d', data.result.images_in_merged_tag.length));

              // Update data
              index = dataTags.findIndex((tag) => tag.id == data.result.destination_tag);
              dataTags[index].counter = data.result.images_in_merged_tag.length;
            }
            $(".tag-box").attr("data-selected", '0');
            updateListItem();

            updateBadge()
          } else {
            return raw_data;
          }
        }
      })
    },
    ...jConfirm_alert_options
  });
}

function tagListToString(list) {
  if (list.length > 5) {
    return list.slice(0,5).join(', ') 
      + ' '
      + str_and_others_tags.replace('%s', list.length - 5);
  } else {
    return list.join(', ');
  }
}

/*-------
 Filter research
-------*/

var maxShown = 100;

$("#search-tag .search-input").on("input", function() {
  updatePaginationMenu();
  updatePage();

  if (dataTags.filter(isDataSearched).length == 0) {
    $('.emptyResearch').show();
  } else {
    $('.emptyResearch').hide();
  }
});

function isSearched(tagBox, stringSearch) {
  let name = tagBox.find("p").text().toLowerCase();
  if (name.startsWith(stringSearch.toLowerCase())) {
    return true;
  } else {
    return false;
  }
}

function isDataSearched(tagObj) {
  let name = tagObj.name;
  let stringSearch = $("#search-tag .search-input").val();
  if (name.startsWith(stringSearch.toLowerCase())) {
    return true;
  } else {
    return false;
  }
}

/*-------
 Show Info
-------*/
function showError(message) {
  $('.tag-error p').html(message);
  $('.tag-error').attr('title', message)
  $('.tag-info').hide()
  $('.tag-error').css('display', 'flex');
}

function showMessage(message) {
  $('.tag-message p').html(message);
  $('.tag-message').attr('title', message)
  $('.tag-info').hide()
  $('.tag-message').css('display', 'flex');
}


/*-------
 Pagination
-------*/
var per_page = $('.tag-container').data('per_page');
var pageItem = '<input type="radio" name="page" id="page-%d" data-page="%d" checked>'
  +'<label for="page-%d"></label>'

function updatePaginationMenu() {
  $('.tag-pagination-container').html('')
  nbPage = getNumberPages();
  for (let page = 1; page <= nbPage; page++) {
    let newTag = $(pageItem.replaceAll('%d', page))
    $('.tag-pagination-container').append(newTag);
  }
  $('#page-1').attr('checked', true);
  updateArrows();

  $('.tag-pagination-container input').change(() => {
    updatePage();
    updateArrows();
  })

  if (getNumberPages() == 1) {
    $('.tag-pagination').hide();
  } else {
    $('.tag-pagination').fadeIn();
  }
}

function updateArrows() {
  if ($('#page-1').is(':checked')) {
    $('.tag-pagination-arrow.left').addClass('unavailable');
  } else {
    $('.tag-pagination-arrow.left').removeClass('unavailable');
  }

  if ($('#page-'+getNumberPages()).is(':checked')) {
    $('.tag-pagination-arrow.rigth').addClass('unavailable');
  } else {
    $('.tag-pagination-arrow.rigth').removeClass('unavailable');
  }
}

function getNumberPages() {
  dataVisible = dataTags.filter(isDataSearched).length;
  return Math.floor(dataVisible / per_page) + 1;
}

function movePage(toRigth = true) {
  let page = $('.tag-pagination-container input:checked').data('page');
  if (toRigth) {
    if ((page + 1) <= getNumberPages()) {
      $('#page-'+page).attr('checked', false);
      page++;
      $('#page-'+page).prop("checked", true).trigger("click");
    }
  } else {
    if ((page - 1) >= 1) {
      $('#page-'+page).attr('checked', false);
      page--;
      $('#page-'+page).prop("checked", true).trigger("click");
    }
  }
  updateArrows();
}

function updatePage() {
  newPage = $('.tag-pagination-container input:checked').data('page');
  dataToDisplay = dataTags.filter(isDataSearched)
    .slice((newPage-1)*per_page, (newPage)*per_page);
  tagBoxes = $('.tag-box');

  $('.tag-box').animate({opacity:0}, 500).promise().then(() => {

    let displayTags = new Promise((resolve, reject) => {
      boxToRecycle = Math.min(dataToDisplay.length, tagBoxes.length);

      for (let i = 0; i < boxToRecycle; i++) {
        let tag = dataToDisplay[i];
        recycleTagBox($(tagBoxes[i]), tag.id, tag.name, tag.url_name)
      }

      if (dataToDisplay.length < tagBoxes.length) {
        for (let j = boxToRecycle; j < tagBoxes.length; j++) {
          $(tagBoxes[j]).remove();
        }
      } else if (dataToDisplay.length > tagBoxes.length) {
        for (let j = boxToRecycle; j < dataToDisplay.length; j++) {
          let tag = dataToDisplay[j];
          newTag = createTagBox(tag.id, tag.name, tag.url_name);
          newTag.css('opacity', 0);
          $('.tag-container').append(newTag);
          setupTagbox(newTag);
        }
      }

      resolve();
    })

    displayTags.then(() => {
      $('.tag-box').animate({opacity:1}, 500)   
    }) 
  });
}

$('.tag-pagination-arrow.rigth').on('click', () => {
  movePage();
  updatePage();
})

$('.tag-pagination-arrow.left').on('click', () => {
  movePage(false);
  updatePage();
})


updatePaginationMenu();