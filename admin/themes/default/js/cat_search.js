const RESULT_LIMIT = 100;
const editLink = "admin.php?page=album-";
const colors = ["icon-red", "icon-blue", "icon-yellow", "icon-purple", "icon-green"];

$(function() {
  $(".limit-album-reached").hide();
  
  $('#cat_search_input').on('input', () => {
    updateSearch();
  });
})

// Update the page according to the search field
function updateSearch () {
  let string = $('.search-input').val();
  $('.search-album-result').html("");
  $('.search-album-noresult').hide();
  $(".limit-album-reached").hide();
  if (string == '') {
    // help button unnecessary so do not show
    // $('.search-album-help').show();
    $('.search-album-ghost').show();
    $('.search-album-num-result').hide();
    hideSearchContainer();
  } else {
    $('.search-album-ghost').hide();
    $('.search-album-help').hide();
    $('.search-album-num-result').show();
    showSearchContainer();

    let nbResult = 0;

    nbResult = searchAlbumByName(data, string, nbResult);

    if (nbResult != 1) {
      if (nbResult >= RESULT_LIMIT) {
        $('.search-album-num-result').html(str_result_limit.replace('%d', nbResult));
      } else {
        $('.search-album-num-result').html(str_albums_found.replace('%d', nbResult));
      }
    } else {
      $('.search-album-num-result').html(str_album_found);
    }

    if (nbResult != 0) {
      resultAppear($('.search-album-result .search-album-elem').first());
    } else {
      $('.search-album-noresult').show();
    }
  }
}

function searchAlbumByName(categories, search, nbResult, children, name='') {
  for (const c of categories) {
    if (nbResult >= RESULT_LIMIT) {
      return nbResult;
    }

    let currentName = name + `<a href="${editLink + c.id}">${c.name}</a>` + ' / ';

    if (c.name.toString().toLowerCase().includes(search.toLowerCase())) {
      const haveChild = c.children && c.children.length ? true : false;
      nbResult++;
      addAlbumResult(c, nbResult, haveChild, currentName);
    }

    if (c.children && c.children.length) {
      nbResult = searchAlbumByName(c.children, search, nbResult, true, currentName);
    }

  }
  return nbResult;
}

// Add an album as a result in the page
function addAlbumResult (cat, nbResult, haveChildren, name) {
  const id = +cat.id;
  const template = $('.search-album-elem-template').html();
  const newCatNode = $(template);

  if (haveChildren) {
    newCatNode.find('.search-album-icon').addClass('icon-sitemap');
  } else {
    newCatNode.find('.search-album-icon').addClass('icon-folder-open');
  }

  const colorId = id%5;
  newCatNode.find('.search-album-icon').addClass(colors[colorId]);
  newCatNode.find('.search-album-name').html(name.slice(0, -2));

  const href = "admin.php?page=album-" + id;
  newCatNode.find('.search-album-edit').attr('href', href);

  $('.search-album-result').append(newCatNode);

  if(nbResult >= RESULT_LIMIT) {
    $(".limit-album-reached").show(1000);
    $('.limit-album-reached').html(str_result_limit.replace('%d', nbResult));
  }
}

// Make the results appear one after one [and limit results to 100]
function resultAppear(result) {
  result.fadeIn();
  if (result.next().length != 0) {
    setTimeout(() => {resultAppear(result.next().first())}, 50);
  }
}

function showSearchContainer() {
  $('.tree').hide();
  $('.album-search-result-container').show();
}

function hideSearchContainer() {
  $('.album-search-result-container').hide();
  $('.tree').fadeIn();
}
