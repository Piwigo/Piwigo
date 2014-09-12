{footer_script require='jquery.bootstrap-tour'}{literal}

var tour = new Tour({
  name: "2_7_0",
  orphan: true,
  onEnd: function (tour) {window.location = "{/literal}{$ABS_U_ADMIN}{literal}admin.php?page=plugin-TakeATour&tour_ended=2_7_0"},
  template: "<div class='popover'>          <div class='arrow'></div>          <h3 class='popover-title'></h3>          <div class='popover-content'></div>          <div class='popover-navigation'>            <div class='btn-group'>              <button class='btn btn-sm btn-default' data-role='prev'>&laquo; {/literal}{'Prev'|@translate|@escape:'javascript'}{literal}</button>              <button class='btn btn-sm btn-default' data-role='next'>{/literal}{'Next '|@translate|@escape:'javascript'}{literal} &raquo;</button>            </div>            <button class='btn btn-sm btn-default' data-role='end'>{/literal}{'End tour'|@translate|@escape:'javascript'}{literal}</button>          </div>        </div>",
});
{/literal}{if $TAT_restart}tour.restart();{/if}{literal}

tour.addSteps([
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    title: "{/literal}{'2_7_0_title1'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'2_7_0_stp1'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugin-TakeATour",
    placement: "left",
    element: "#content",
    title: "{/literal}{'2_7_0_title2'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'2_7_0_stp2'|@translate|@escape:'javascript'}{literal}",
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=photos_add",
    placement: "top",
    title: "{/literal}{'2_7_0_title2b'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'2_7_0_stp2b'|@translate|@escape:'javascript'}{literal}",
  },
  {
    path: "{/literal}{$TAT_path}{$TAT_search}{literal}",
    placement: "left",
    element: "#content",
    title: "{/literal}{'2_7_0_title4'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'2_7_0_stp4'|@translate|@escape:'javascript'}{literal}"
  },
  {//5
    path: "{/literal}{$TAT_path}{literal}admin.php?page=photo-{/literal}{$TAT_image_id}{literal}",
    placement: "top",
    element: ".icon-calendar",
    title: "{/literal}{'2_7_0_title5'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'2_7_0_stp5'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=photo-{/literal}{$TAT_image_id}{literal}",
    placement: "top",
    element: "#catModify > fieldset:nth-child(2) > p:nth-child(5) > strong",
    title: "{/literal}{'2_7_0_title6'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'2_7_0_stp6'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=cat_list",
    element: "#autoOrderOpen",
    onShown: function (tour) {jQuery("#autoOrderOpen").trigger("click");},
    title: "{/literal}{'2_7_0_title7'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'2_7_0_stp7'|@translate|@escape:'javascript'}{literal}",
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=batch_manager&filter=prefilter-caddie",
    element: "#empty_caddie",
    placement: "right",
    title: "{/literal}{'2_7_0_title8'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'2_7_0_stp8'|@translate|@escape:'javascript'}{literal}",
    prev:4
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=batch_manager&filter=search-taken:2013..2015",
    element: "#filter_search input[name=q]",
    title: "{/literal}{'2_7_0_title9'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'2_7_0_stp9'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=batch_manager&filter=filesize-1..5",
    element: "#filter_filesize",
    placement: "top",
    title: "{/literal}{'2_7_0_title10'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'2_7_0_stp10'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugin-TakeATour",
    title: "{/literal}{'2_7_0_title11'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'2_7_0_stp11'|@translate|@escape:'javascript'}{literal}"
  }
]);

// Initialize the tour
tour.init();

// Start the tour
tour.start();

jQuery( "input[class='submit']" ).click(function() {
  if (tour.getCurrentStep()==5)
  {
    tour.goTo(6);
  }
});
{/literal}{/footer_script}