{footer_script require='jquery.bootstrap-tour'}{literal}

var tour = new Tour({
  name: "privacy",
  orphan: true,
  onEnd: function (tour) {window.location = "{/literal}{$ABS_U_ADMIN}{literal}admin.php?page=plugin-TakeATour&tour_ended=privacy"},
  template: "<div class='popover'>          <div class='arrow'></div>          <h3 class='popover-title'></h3>          <div class='popover-content'></div>          <div class='popover-navigation'>            <div class='btn-group'>              <button class='btn btn-sm btn-default' data-role='prev'>&laquo; {/literal}{'Prev'|@translate|@escape:'javascript'}{literal}</button>              <button class='btn btn-sm btn-default' data-role='next'>{/literal}{'Next '|@translate|@escape:'javascript'}{literal} &raquo;</button>            </div>            <button class='btn btn-sm btn-default' data-role='end'>{/literal}{'End tour'|@translate|@escape:'javascript'}{literal}</button>          </div>        </div>",
});
{/literal}{if $TAT_restart}tour.restart();{/if}{literal}

tour.addSteps([
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    title: "{/literal}{'privacy_title1'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp1'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    placement: "bottom",
    element: ".icon-help-circled",
    title: "{/literal}{'privacy_title2'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp2'|@translate|@escape:'javascript'}{literal}",
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=help&section=permissions",
    placement: "top",
    element: "#helpContent",
    title: "{/literal}{'privacy_title3'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp3'|@translate|@escape:'javascript'}{literal}",
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=help&section=permissions",
    placement: "top",
    element: "#helpContent",
    title: "{/literal}{'privacy_title4'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp4'|@translate|@escape:'javascript'}{literal}"
  },
  {//5
    path: "{/literal}{$TAT_path}{literal}admin.php?page=help&section=groups",
    placement: "top",
    element: "#helpContent>p:first",
    title: "{/literal}{'privacy_title5'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp5'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=photos_add",
    placement: "top",
    element: "#showPermissions",
    title: "{/literal}{'privacy_title6'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp6'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=batch_manager&filter=prefilter-last_import",
    placement: "top",
    element: "",
    title: "{/literal}{'privacy_title7'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp7'|@translate|@escape:'javascript'}{literal}",
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=batch_manager&filter=prefilter-last_import",
    placement: "top",
    element: ".thumbnails",
    title: "{/literal}{'privacy_title8'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp8'|@translate|@escape:'javascript'}{literal}",
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=batch_manager&filter=prefilter-last_import",
    placement: "top",
    element: "#action",
    title: "{/literal}{'privacy_title9'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp9'|@translate|@escape:'javascript'}{literal}"
  },
  {//10
    path: "{/literal}{$TAT_path}{literal}admin.php?page=cat_list",
    placement: "top",
    title: "{/literal}{'privacy_title10'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp10'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}";},
    placement: "bottom",
    element: ".normal_tab .icon-lock",
    reflex:true,
    title: "{/literal}{'privacy_title11'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp11'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "top",
    element: "#categoryPermissions",
    title: "{/literal}{'privacy_title12'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp12'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "bottom",
    element: "input[value='private']",
    reflex:true,
    title: "{/literal}{'privacy_title13'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp13'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "top",
    element: "#privateOptions",
    title: "{/literal}{'privacy_title14'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp14'|@translate|@escape:'javascript'}{literal}",
  },
  {
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    title: "{/literal}{'first_contact_title27'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp27'|@translate|@escape:'javascript'}{literal}",
  },
  {//15
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    element: "a[href='./admin.php?page=cat_options']",
    reflex:true,
    title: "{/literal}{'privacy_title15'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp15'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=cat_options",
    placement: "top",
    element: ".doubleSelect",
    title: "{/literal}{'privacy_title16'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp16'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=group_list",
    title: "{/literal}{'privacy_title17'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp17'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=group_list",
    placement: "right",
    element: "a[href='./admin.php?page=user_list']",
    title: "{/literal}{'privacy_title18'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp18'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=user_list",
    placement: "top",
    element: "#userList",
    title: "{/literal}{'privacy_title19'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp19'|@translate|@escape:'javascript'}{literal}",

  },
  {//20
    path: "{/literal}{$TAT_path}{literal}admin.php",
    title: "{/literal}{'privacy_title20'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp20'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    title: "{/literal}{'privacy_title21'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp21'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    title: "{/literal}{'privacy_title22'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp22'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    title: "{/literal}{'privacy_title24'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp24'|@translate|@escape:'javascript'}{literal}"
  }
]);

// Initialize the tour
tour.init();

// Start the tour
tour.start();

jQuery( "p.albumActions a" ).click(function() {
  if (tour.getCurrentStep()==9)
  {
    tour.goTo(10);
  }
});

{/literal}{/footer_script}
{html_style}
#step-21 {
  max-width:476px;
}
#step-22 {
  max-width:376px;
}
{/html_style}