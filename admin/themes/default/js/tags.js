//Get the data
var dataTags = $('.tag-container').data('tags');

//Initiate Select
$('#select-100').prop('checked', true)

//Orphan tags
$('.tag-warning p a').on('click', () => {
  let url = $('.tag-warning p a').data('url');
  let tags = orphan_tag_names;
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
          window.location.href = url.replace(/amp;/g, '');
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
    .replace(/%name%/g, unescape(name))
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
  tagBox.attr('data-selected', 0)

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
  $('.badge-number').html(dataTags.length);
  if (dataTags.length == 0) {
    $(".tag-header #add-tag .add-tag-label").addClass("highlight");
  } else {
    $(".tag-header #add-tag .add-tag-label").removeClass("highlight");
  }
}

//Add a tag
$('.add-tag-container').on('click', function() {
  $('#add-tag').addClass('input-mode');
  $('#add-tag-input').focus();
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

//Call the API when rename a tag
$(".TagSubmit").on('click', function () {
  $('.TagSubmit').hide();
  $('.TagLoading').show();
  renameTag($(".RenameTagPopInContainer").find(".tag-property-input").attr("id"), $(".RenameTagPopInContainer").find(".tag-property-input").val()).then(() => {
    $('.TagSubmit').show();
    $('.TagLoading').hide();
    rename_tag_close();
  }).catch((message) => {
    $('.TagSubmit').show();
    $('.TagLoading').hide();
    console.error(message)
  })
});

/*-------
 Add a tag
-------*/

$('#add-tag').submit(function (e) {
  e.preventDefault();
  if ($('#add-tag-input').val() != "") {
    loadState = new TemporaryState();
    loadState.removeClass($('#add-tag .icon-validate'),'icon-plus');
    loadState.changeHTML($('#add-tag .icon-validate') , "<i class='icon-spin6 animate-spin'> </i>")
    loadState.changeAttribute($('#add-tag .icon-validate'), 'style','pointer-event:none')
    addTag($('#add-tag-input').val()).then(function () {
      showMessage(str_tag_created.replace('%s', $('#add-tag-input').val()));
      $('#add-tag-input').val("");
      $('#add-tag').removeClass('input-mode');
      $("#search-tag .search-input").trigger("input");
      loadState.reverse();
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
          updateSearchInfo();

          //Update the data
          dataTags.unshift({
            name:data.result.name,
            id:data.result.id,
            url_name:data.result.url_name
          });
          updateBadge();
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

  // Selection behaviour
  tagBox.on('click', function() {
    if ($('.tag-container').hasClass('selection')) {
      if (tagBox.attr('data-selected') == '1') {
        tagBox.attr('data-selected', '0');
        removeSelectedItem(tagBox.attr('data-id'));
      } else {
        tagBox.attr('data-selected', '1');
        addSelectedItem(tagBox.attr('data-id'));
      }
      updateSelectionContent();
    }
  })

  //Edit Name
  tagBox.find('.dropdown-option.edit').on('click', function() {
    console.log('SALUT');
    set_up_popin(tagBox.data('id'), tagBox.find('.tag-name').html());
    rename_tag_open()
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

function set_up_popin(id, tagName) {

  $(".RenameTagPopInContainer").find(".tag-property-input").attr("id", id);

  $(".AddIconTitle span").html(str_tag_rename.replace("%s", tagName))
  $(".ClosePopIn").on('click', function () {
    rename_tag_close()
  });
  $(".TagSubmit").html(str_yes_rename_confirmation);
  $(".RenameTagPopInContainer").find(".tag-property-input").val(tagName);
}

function rename_tag_close() {
  $("#RenameTag").fadeOut();
}

function rename_tag_open() {
  $("#RenameTag").fadeIn();
  $(".tag-property-input").first().focus();
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
            updateSearchInfo();
            updatePaginationMenu();
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
        console.log(data);
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
          updateBadge();
          updateSearchInfo();
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
var selected = [];
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
    $('.tag-select-message').slideUp();
    clearSelection();
  }
}

function clearSelection() {
  selected = [];
  $('.selection-mode-tag .tag-list').html('');
  $('.selection-other-tags').hide();
  updateSelectionContent();
}

function addSelectedItem(id) {
  if (!selected.includes(id)) {
    selected.push(id);

    if (selected.length > maxItemDisplayed) {
      $('.selection-other-tags').show();
      let numberDisplayed = $('.selection-mode-tag .tag-list div').length;
      $('.selection-other-tags').html(str_and_others_tags.replace('%s', selected.length - numberDisplayed))
    } else {
      $('.selection-other-tags').hide();
      if (dataTags.findIndex(tag => tag.id == id) > -1) {
        createSelectionItem(id, dataTags.find(tag => tag.id == id).name);
      }
    }
  }
}

function createSelectionItem(id, name) {
  let newItemStructure = $('<div data-id="'+id+'"><a class="icon-cancel"></a><p>'+name+'</p> </div>');
  $('.selection-mode-tag .tag-list').prepend(newItemStructure);
  $('.selection-mode-tag .tag-list div[data-id='+id+'] a').on('click', function () {
    removeSelectedItem(id);
  });
}

function removeSelectedItem(id) {
  if (selected.findIndex((tag) => tag == id) > -1) {

    selected = selected.filter((tag) => {return parseInt(tag) != parseInt(id)});

    $('.tag-box[data-id='+id+']').attr('data-selected', '0');
    if ($('.selection-mode-tag .tag-list div[data-id='+id+']').length != 0) {
      $('.selection-mode-tag .tag-list div[data-id='+id+']').remove();

      if (selected.length >= maxItemDisplayed) {
        let i = 0;
        isNotCreate = true
        while (i<selected.length && isNotCreate) {
            if ($('.selection-mode-tag .tag-list div[data-id='+selected[i]+']').length == 0) {
              isNotCreate = false;
              indexOfTag = dataTags.findIndex(tag => tag.id == selected[i])
              createSelectionItem(selected[i], dataTags[indexOfTag].name);
            }
            i++;
        }
      }
    } 

    let numberDisplayed = $('.selection-mode-tag .tag-list div').length;
    $('.selection-other-tags').html(str_and_others_tags.replace('%s', selected.length - numberDisplayed))
    if (selected.length - numberDisplayed <= 0) {
      $('.selection-other-tags').hide();
    }

    //Remove the selection message
    $('.tag-select-message').slideUp();
  }
}

function updateMergeItems () {
  $('#MergeOptionsChoices').html('');
  selected.forEach(id => {
    $('#MergeOptionsChoices').append(
      $('<option value="'+id+'">'+dataTags.find((tag) => tag.id == id).name+'</option>')
    )
  })
}

mergeOption = false;

function updateSelectionContent() {
  number = selected.length;
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
  selectAll(tagToDisplay())
  updateSelectionContent();
  if (selected.length < dataTags.length) {
    showSelectMessage(
      str_selection_done.replace('%d', $('.tag-box').length), 
      str_select_all_tag.replace('%d', dataTags.length), 
      function() {
      $('.tag-select-message a').html("");
      $('.tag-select-message div').html("<i class='icon-spin6 animate-spin'> </i>");
      setTimeout(() => {
        selectAll(dataTags).then(() => {
          updateSelectionContent();
          showSelectMessage(str_tag_selected.replace(/%d/g, selected.length), str_clear_selection, function() {
            selectNone();
            $('.tag-select-message').slideUp();
          })
        })
      }, 5);
    });
  }
});

function selectAll(data) {
  promises = [];
  data.forEach((tag) => {
    promises.push(new Promise((res, rej) => {
      $('.tag-box[data-id='+tag.id+']').attr('data-selected', 1);
      addSelectedItem(tag.id);
      res();
    }))
  })
  return Promise.all(promises);
}

function showSelectMessage(str1, str2, callback) {
  if (!$('.tag-select-message').is(':visible')) {
    $('.tag-select-message').slideDown({
      start: function () {
        $(this).css({
          display: "flex"
        })
      }
    });
  }

  $('.tag-select-message div').html(str1)
  $('.tag-select-message a').html(str2);
  $('.tag-select-message a').off('click');
  $('.tag-select-message a').on('click', callback);
}

$('#selectNone').on('click', function() {
  $('.tag-select-message').slideUp();
  selectNone();
});

function selectNone() {
  $('.tag-box').attr('data-selected', '0');
  clearSelection();
}

$('#selectInvert').on('click', function() {
  $('.tag-select-message').slideUp();
  selectInvert(tagToDisplay());
});

function selectInvert(data) {
  data.forEach((tag) => {
    tagBox = $('.tag-box[data-id='+tag.id+']');
    if (tagBox.attr('data-selected') == 1) {
      tagBox.attr('data-selected', '0');
      removeSelectedItem(tag.id)
    } else {
      tagBox.attr('data-selected', '1');
      addSelectedItem(tag.id)
    }
  })
  updateSelectionContent();
}

/*-------
 Actions in selection mode
-------*/

//Remove tags
$('#DeleteSelectionMode').on('click', function() {
  let names = [];
  selected.forEach(function (id) {
    names.push(dataTags.find((tag) => tag.id == id).name);
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
  let names = [];
  selected.forEach(function (id) {
    names.push(dataTags.find((tag) => tag.id == id).name);
  })

  $.alert({
    title : str_tags_deleted.replace("%s",tagListToString(names)),
    content: function() {
      return jQuery.ajax({
        url: "ws.php?format=json&method=pwg.tags.delete",
        type: "POST",
        data: {
          'pwg_token': pwg_token,
          'tag_id': selected
        },
        success: function (raw_data) {
          raw_data = raw_data.slice(raw_data.search('{'));
          if (JSON.parse(raw_data).stat = 'ok') {
            selected.forEach(function(id) {
              $('.tag-box[data-id='+id+']').remove();
            })

            // Update Data
            dataTags = dataTags.filter((tag) => !selected.includes(tag.id))

            clearSelection();
            updatePaginationMenu();
            updateBadge();
            updateSearchInfo();
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
  dest_id = $('#MergeOptionsChoices').val();
  mergeGroups(dest_id, selected)
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
            clearSelection();
            updatePaginationMenu();
            updateBadge()
            updateSearchInfo()
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
var searchTimeOut;
var delaySearchInput = 300;

$("#search-tag .search-input").on("input", function() {
  actualPage = 1;

  clearTimeout(searchTimeOut);
  searchTimeOut = setTimeout(() => {
    updatePaginationMenu();
    if (dataTags.filter(isDataSearched).length == 0) {
      $('.emptyResearch').show();
    } else {
      $('.emptyResearch').hide();
    }
  }, delaySearchInput);
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
  let name = tagObj.name.toLowerCase();
  let stringSearch = $("#search-tag .search-input").val();
  if (name.includes(stringSearch.toLowerCase())) {
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
var pageItem = '<a data-page="%d">%d</a>';
var pageEllipsis = '<span>...</span>';
var promisePending = false;
var delay = 100;
var updateAsk = false;

var actualPage = 1;

//Avoid 2 update at the same time
function askUpdatePage() {
  if (!promisePending) {
    promisePending = true;
    updatePage().then(promiseFinish);
  } else {
    updateAsk = true;
  }
}

function promiseFinish() {
  promisePending = false;
  if (updateAsk) {
    updateAsk = false;
    askUpdatePage();
  }
} 

function updatePaginationMenu() {
  $('.pagination-item-container').html('');

  actualPage = Math.min(actualPage, getNumberPages());

  if (getNumberPages() > 1) {
    $('.pagination-container').show();
    createPaginationMenu();
  } else {
    $('.pagination-container').hide();
  }

  updateArrows();
  askUpdatePage();

  //Remove the selection message
  $('.tag-select-message').slideUp();
}

function createPaginationMenu() {
  nbPage = getNumberPages();

  appendPaginationItem(1);

  if (actualPage > 2) {
    appendPaginationItem();
  }

  if (actualPage != 1 && actualPage != nbPage) {
    appendPaginationItem(actualPage)
  }

  if (actualPage < (nbPage - 1)) {
    appendPaginationItem();
  }

  appendPaginationItem(nbPage);
}

function appendPaginationItem(page = null) {
  if (page != null) {
    let newTag = $(pageItem.replace(/%d/g, page))
    $('.pagination-item-container').append(newTag);
    if (actualPage == page) {
      newTag.addClass('actual');
    }
    newTag.on('click', () => {
      actualPage = newTag.data('page');
      updatePaginationMenu();
    })
  } else {
    $('.pagination-item-container').append($(pageEllipsis));
  }
}

function updateArrows() {
  if (actualPage == 1) {
    $('.pagination-arrow.left').addClass('unavailable');
  } else {
    $('.pagination-arrow.left').removeClass('unavailable');
  }

  if (actualPage == getNumberPages()) {
    $('.pagination-arrow.rigth').addClass('unavailable');
  } else {
    $('.pagination-arrow.rigth').removeClass('unavailable');
  }
}

function getNumberPages() {
  dataVisible = dataTags.filter(isDataSearched).length;
  return Math.floor((dataVisible - 1) / per_page) + 1;
}

function movePage(toRigth = true) {
  $(".tag-box").removeClass("edit-name");
  if (toRigth) {
    if (actualPage < getNumberPages()) {
      actualPage++;
      updatePaginationMenu();
    }
  } else {
    if (actualPage > 1) {
      actualPage--;
      updatePaginationMenu();
    }
  }
}

function updatePage() {
  return new Promise((resolve, reject) => {
    newPage = actualPage;
    dataToDisplay = tagToDisplay();
    tagBoxes = $('.tag-box');
    $('.pageLoad').fadeIn();;
    $('.tag-box').animate({opacity:0}, 500).promise().then(() => {

      let displayTags = new Promise((res, rej) => {
        boxToRecycle = Math.min(dataToDisplay.length, tagBoxes.length);

        for (let i = 0; i < boxToRecycle; i++) {
          let tag = dataToDisplay[i];
          recycleTagBox($(tagBoxes[i]), tag.id, tag.name, tag.url_name, tag.counter)
        }

        if (dataToDisplay.length < tagBoxes.length) {
          for (let j = boxToRecycle; j < tagBoxes.length; j++) {
            $(tagBoxes[j]).remove();
          }
        } else if (dataToDisplay.length > tagBoxes.length) {
          for (let j = boxToRecycle; j < dataToDisplay.length; j++) {
            let tag = dataToDisplay[j];
            newTag = createTagBox(tag.id, tag.name, tag.url_name, tag.counter);
            newTag.css('opacity', 0);
            $('.tag-container').append(newTag);
            setupTagbox(newTag);
          }
        }

        //Select selected tags
        selected.forEach((id) => {
          $('.tag-box[data-id='+id+']').attr('data-selected', 1);
        })

        res();
      })

      displayTags.then(() => {
        $('.pageLoad').fadeOut();
        $('.tag-box').animate({opacity:1}, 500);
        if (getNumberPages() > 1) {
          $('.tag-pagination').animate({opacity:1}, 500);
        }
        updateSearchInfo();
        resolve()
      }) 
    });
  })
}

function tagToDisplay() {
  return dataTags.filter(isDataSearched)
      .slice((actualPage-1)*per_page, (actualPage)*per_page);
} 

$('.pagination-arrow.rigth').on('click', () => {
  movePage();
})

$('.pagination-arrow.left').on('click', () => {
  movePage(false);
})

if (getNumberPages() > 1) {
  $('.pagination-container').show();
  createPaginationMenu();
  updateArrows();
} else {
  $('.pagination-container').hide();
}

$('.pagination-per-page a').on('click',function () {
  per_page = parseInt($(this).html());
  updatePaginationMenu();
  $(".pagination-per-page .selected").removeClass("selected");
  $(this).addClass("selected");
  $.cookie("pwg_tags_per_page", per_page);
})

function updateSearchInfo () {
  if ($('.search-input').val() != '') { 
    let number = dataTags.filter(isDataSearched).length;   
    if (number > 1) {
      $('.search-info').html(str_tags_found.replace('%d', number));
    } else {
      $('.search-info').html(str_tag_found.replace('%d', number));
    }
  } else {
    $('.search-info').html('');
  }
}

$(function () {
  function setPagination() {
    let test = $.cookie("pwg_tags_per_page");
    $(".pagination-per-page .selected").removeClass("selected");
    $("#"+test).trigger("click");
  }
  
  setPagination()
})