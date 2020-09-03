{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{footer_script}
var data = {json_encode($data_cat)};
var categories = Object.values(data)
{literal}
var editLink = "admin.php?page=album-";
var colors = ["icon-red", "icon-blue", "icon-yellow", "icon-purple", "icon-green"];

$('.search-input').on('input', () => {
  updateSearch();
})

function updateSearch () {
  string = $('.search-input').val();
  $('.search-album-result').html("");
  $('.search-album-noresult').hide();
  if (string == '') {
    $('.search-album-ghost').fadeIn();
  } else {
    $('.search-album-ghost').hide();

    nbResult = 0;
    categories.forEach((c) => {
      if (c[0].toString().toLowerCase().search(string.toLowerCase()) != -1) {
        addAlbumResult(c);
        nbResult++;
      }
    })

    if (nbResult != 0) {
      resultAppear($('.search-album-result .search-album-elem').first());
    } else {
      $('.search-album-noresult').show();
    }
  }
}

function addAlbumResult (cat) {
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
}

function getHtmlPath (cat) {
  html = '';
  for (let i = 0; i < cat[1].length - 1; i++) {
    id = cat[1][i];
    c = data[id];
    html += '<a href="' + editLink + id + '">' + c[0] + '</a> <b>/</b> '
  }
  html += '<a href="' + editLink + cat[1][cat[1].length - 1] + '">' + cat[0] + '</a>';

  return html
}

function resultAppear(result) {
  result.fadeIn();
  if (result.next().length != 0) {
    setTimeout(() => {resultAppear(result.next().first())}, 50);
  }
}

updateSearch();
{/literal}
{/footer_script}

<div class="titrePage">
  <h2>{'Album search tool'|@translate}</h2>
</div>

<div class="search-album">
  <div class="search-album-label">{'Search'|@translate}</div>
  <div class="search-album-input-container" style="position:relative">
    <span class="icon-search search-icon"></span>
    <span class="icon-cancel search-cancel"></span>
    <input class='search-input' type="text" placeholder="{'Portraits...'|@translate}">
  </div>
</div>
<div class="search-album-help  icon-help-circled">{' Enter a term to search from album'|@translate}</div>

<div class="search-album-ghost">
  <div></div>
  <div></div>
  <div></div>
  <div></div>
</div>

<div class="search-album-elem-template" style="display:none">
  <div class="search-album-elem" style="display:none">
    <span class='search-album-icon'></span>
    <p class='search-album-name'></p>
    <div class="search-album-action-cont">
      <div class="search-album-action">
        <a class="icon-pencil search-album-edit">Edit album</a>
      </div>
    </div>
  </div>
</div>

<div class="search-album-result">

</div>

<div class="search-album-noresult">
  {"No album found"|@translate}
</div>

