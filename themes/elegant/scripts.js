(function() {
  var session_storage = window.sessionStorage || {};

  var menubar=jQuery("#menubar"),
      menuswitcher=jQuery("#menuSwitcher"),
      content=jQuery("#the_page > .content"),
      pcontent=jQuery("#content");

  function hideMenu(delay) {
    menubar.hide(delay);
    menuswitcher.addClass("menuhidden").removeClass("menushown");
    content.addClass("menuhidden").removeClass("menushown");
    pcontent.addClass("menuhidden").removeClass("menushown");
    session_storage['page-menu'] = 'hidden';
  }

  function showMenu(delay) {
    menubar.show(delay);
    menuswitcher.addClass("menushown").removeClass("menuhidden");
    content.addClass("menushown").removeClass("menuhidden");
    pcontent.addClass("menushown").removeClass("menuhidden");
    session_storage['page-menu'] = 'visible';
  }

  jQuery(function(){
    if (menubar.length == 1 && p_main_menu!="disabled") {
      menuswitcher.html('<div class="switchArrow">&nbsp;</div>');

      if (session_storage['page-menu'] == undefined && p_main_menu == 'off') {
        session_storage['page-menu'] = 'hidden';
      }

      if (session_storage['page-menu'] == 'hidden') {
        hideMenu(0);
      }
      else {
        showMenu(0);
      }

      menuswitcher.click(function(e){
        if (menubar.is(":hidden")) {
          showMenu(0);
        }
        else {
          hideMenu(0);
        }
        e.preventDefault();
      });
    }
    else if (menubar.length == 1 && p_main_menu=="disabled") {
      showMenu(0);
    }
  });
}());