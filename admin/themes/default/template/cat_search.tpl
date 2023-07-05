{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{footer_script}
$(document).ready(() => {
  $("h1").append("<span class='badge-number'>"+{$nb_cats}+"</span>");
});
var data = {json_encode($data_cat)};
/* 
  Here data is an associative array id => category under this form 
  [0] : name
  [1] : array of id, path to find this album (root to album)
  [2] : 1 = private or 0 = public
*/

// Numeric array of all categories
var categories = Object.values(data);

const RESULT_LIMIT = 100;

var str_albums_found = '{"<b>%d</b> albums found"|translate}';
var str_album_found = '{"<b>1</b> album found"|translate}';
var str_result_limit = '{"<b>%d+</b> albums found, try to refine the search"|translate|escape:javascript}';

{literal}
var editLink = "admin.php?page=album-";
var colors = ["icon-red", "icon-blue", "icon-yellow", "icon-purple", "icon-green"];

$(".limit-album-reached").hide();

$('.search-input').on('input', () => {
  updateSearch();
})

// Update the page according to the search field
function updateSearch () {
  string = $('.search-input').val();
  $('.search-album-result').html("");
  $('.search-album-noresult').hide();
  $(".limit-album-reached").hide();
  if (string == '') {
    // help button unnecessary so do not show
    // $('.search-album-help').show();
    $('.search-album-ghost').show();
    $('.search-album-num-result').hide();
  } else {
    $('.search-album-ghost').hide();
    $('.search-album-help').hide();
    $('.search-album-num-result').show();

    nbResult = 0;
    categories.forEach((c) => {
      if (c[0].toString().toLowerCase().search(string.toLowerCase()) != -1 && nbResult < RESULT_LIMIT) {
        nbResult++;
        addAlbumResult(c, nbResult);
      }
    })

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

// Add an album as a result in the page
function addAlbumResult (cat, nbResult) {
  id = cat[1][cat[1].length - 1];
  template = $('.search-album-elem-template').html();
  newCatNode = $(template);

  hasChildren = false;
  categories.forEach((c) => {
    for (let i = 0; i < c[1].length - 1; i++) {
      if (c[1][i] == id) {
        hasChildren = true;
      }
    }
  })

  if (hasChildren) {
    newCatNode.find('.search-album-icon').addClass('icon-sitemap');
  } else {
    newCatNode.find('.search-album-icon').addClass('icon-folder-open');
  }

  colorId = id%5;
  newCatNode.find('.search-album-icon').addClass(colors[colorId]);

  newCatNode.find('.search-album-name').html(getHtmlPath(cat));

  href = "admin.php?page=album-" + id;
  newCatNode.find('.search-album-edit').attr('href', href);

  $('.search-album-result').append(newCatNode);

  if(nbResult >= RESULT_LIMIT) {
    $(".limit-album-reached").show(1000);
    $('.limit-album-reached').html(str_result_limit.replace('%d', nbResult));
  }
}

// Get the path "PARENT / parent / album" with link to the edition of all albums
function getHtmlPath (cat) {
  html = '';
  for (let i = 0; i < cat[1].length - 1; i++) {
    id_bis = cat[1][i];
    c = data[id_bis];
    html += '<a href="' + editLink + id_bis + '">' + c[0] + '</a> <b>/</b> '
  }
  html += '<a href="' + editLink + cat[1][cat[1].length - 1] + '">' + cat[0] + '</a>';

  return html
}

// Make the results appear one after one [and limit results to 100]
function resultAppear(result) {
  result.fadeIn();
  if (result.next().length != 0) {
    setTimeout(() => {resultAppear(result.next().first())}, 50);
  }
}

updateSearch();
$('.search-input').focus();
{/literal}
{/footer_script}

<div class="search-album">
  <div class="search-album-cont">
    <div class="search-album-label">{'Search albums'|@translate}</div>
    <div class="search-album-input-container" style="position:relative">
      <span class="icon-search search-icon"></span>
      <span class="icon-cancel search-cancel"></span>
      <input class='search-input' type="text" placeholder="{$placeholder|escape:html}">
    </div>
    <span class="search-album-help icon-help-circled" title="{'Enter a term to search for album'|@translate}"></span>
    <span class="search-album-num-result"></span>
  </div>
</div>

<div class="search-album-ghost">
  <span>{'No research in progress'|@translate}</span>
</div>

<div class="search-album-elem-template" style="display:none">
  <div class="search-album-elem" style="display:none">
    <span class='search-album-icon'></span>
    <p class='search-album-name'></p>
    <div class="search-album-action-cont">
      <div class="search-album-action">
        <a class="icon-pencil search-album-edit">{'Edit album'|translate}</a>
      </div>
    </div>
  </div>
</div>

<div class="search-album-result">

</div>
<div class="search-album-elem limit-album-reached"></div>

<div class="search-album-noresult">
  {'No albums found'|translate}
</div>

<style>
.limit-album-reached {
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>

