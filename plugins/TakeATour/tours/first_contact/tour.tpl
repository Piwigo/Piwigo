{footer_script require='jquery.bootstrap-tour'}{literal}

var tour = new Tour({
  name: "first_contact",
  orphan: true,
  onEnd: function (tour) {window.location = "admin.php?page=plugin-TakeATour&tour_ended=first_contact";},
});
{/literal}{if $TAT_restart}tour.restart();{/if}{literal}

tour.addSteps([
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    title: "{/literal}{'first_contact_title1'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp1'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    placement: "right",
    element: ".icon-plus-circled",
    reflex:true,
    title: "{/literal}{'first_contact_title2'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp2'|@translate}{literal}",
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=photos_add",
    placement: "bottom",
    element: ".selected_tab",
    title: "{/literal}{'first_contact_title3'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp3'|@translate}{literal}",
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=photos_add",
    placement: "left",
    element: "#albumSelection",
    title: "{/literal}{'first_contact_title4'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp4'|@translate}{literal}"
  },
  {//5
    path: "{/literal}{$TAT_path}{literal}admin.php?page=photos_add",
    placement: "top",
    element: "#uploadify",
    title: "{/literal}{'first_contact_title5'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp5'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=photos_add/,
    redirect:function (tour) {window.location = "admin.php?page=photos_add";},
    placement: "left",
    element: "#fileQueue",
    title: "{/literal}{'first_contact_title6'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp6'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=photos_add/,
    redirect:function (tour) {window.location = "admin.php?page=photos_add";},
    placement: "top",
    element: "#photosAddContent legend",
    title: "{/literal}{'first_contact_title7'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp7'|@translate}{literal}",
    prev:4
  },
  {
    path: /admin\.php\?page=photos_add/,
    redirect:function (tour) {window.location = "admin.php?page=photos_add";},
    placement: "bottom",
    element: "#batchLink",
    reflex:true,
    title: "{/literal}{'first_contact_title8'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp8'|@translate}{literal}",
    prev:4
  },
  {
    path: /admin\.php\?page=(photos_add|batch_manager&filter=prefilter-last_import|prefilter-caddie)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "top",
    element: "",
    title: "{/literal}{'first_contact_title9'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp9'|@translate}{literal}"
  },
  {//10
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "right",
    element: ".icon-flag",
    title: "{/literal}{'first_contact_title10'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp10'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "left",
    element: "#checkActions",
    title: "{/literal}{'first_contact_title11'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp11'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "top",
    element: "#action",
    title: "{/literal}{'first_contact_title12'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp12'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "bottom",
    element: "#tabsheet .normal_tab",
    title: "{/literal}{'first_contact_title13'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp13'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "top",
    element: "#TAT_FC_14",
    reflex:true,
    title: "{/literal}{'first_contact_title14'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp14'|@translate}{literal}",
    onNext:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";}
  },
  {//15
    path: /admin\.php\?page=photo-/,
    redirect:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},
    placement: "bottom",
    element: ".selected_tab",
    title: "{/literal}{'first_contact_title15'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp15'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=photo-/,
    redirect:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},
    placement: "top",
    element: "#TAT_FC_16",
    title: "{/literal}{'first_contact_title16'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp16'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=photo-/,
    redirect:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},
    placement: "top",
    element: "#TAT_FC_17",
    title: "{/literal}{'first_contact_title17'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp17'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=photo-/,
    redirect:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},
    placement: "top",
    title: "{/literal}{'first_contact_title18'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp18'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=cat_list",
    placement: "left",
    element: "#content",
    title: "{/literal}{'first_contact_title19'|@translate}{literal}",
    content: "{/literal}{if $TAT_FTP}{'first_contact_stp19'|@translate}{else}{'first_contact_stp19_b'|@translate}{/if}{literal}",
    onPrev: function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},

  },
  {//20
    path: "{/literal}{$TAT_path}{literal}admin.php?page=cat_list",
    placement: "top",
    element: "#categoryOrdering",
    title: "{/literal}{'first_contact_title20'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp20'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=cat_list",
    placement: "left",
    element: "#tabsheet:first-child",
    title: "{/literal}{'first_contact_title21'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp21'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=album-/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}";},
    placement: "top",
    element: ".selected_tab",
    title: "{/literal}{'first_contact_title22'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp22'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=album-/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}";},
    placement: "top",
    element: "#TAT_FC_23",
    title: "{/literal}{'first_contact_title23'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp23'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=album-/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}";},
    placement: "bottom",
    element: ".tabsheet",
    title: "{/literal}{'first_contact_title24'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp24'|@translate}{literal}"
  },
  {//25
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "left",
    element: "#content",
    title: "{/literal}{'first_contact_title25'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp25'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "top",
    element: "#selectStatus",
    title: "{/literal}{'first_contact_title26'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp26'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "top",
    element: "#selectStatus",
    title: "{/literal}{'first_contact_title27'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp27'|@translate}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "top",
    title: "{/literal}{'first_contact_title28'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp28'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    placement: "top",
    element: "",
    title: "{/literal}{'first_contact_title29'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp29'|@translate}{literal}"
  },
  {//30
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    placement: "right",
    element: "#gallery_title",
    title: "{/literal}{'first_contact_title30'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp30'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    placement: "right",
    element: "#page_banner",
    title: "{/literal}{'first_contact_title31'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp31'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    reflex: true,
    placement: "top",
    element: ".formButtons input",
    title: "{/literal}{'first_contact_title32'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp32'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    placement: "top",
    title: "{/literal}{'first_contact_stp33'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp33'|@translate}{literal}",
    prev:30
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=themes",
    placement: "top",
    element: "",
    title: "{/literal}{'first_contact_title34'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp34'|@translate}{literal}"
  },
  {//35
    path: "{/literal}{$TAT_path}{literal}admin.php?page=themes",
    placement: "top",
    element: "#TAT_FC_35",
    title: "{/literal}{'first_contact_title35'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp35'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=themes",
    placement: "top",
    element: "",
    title: "{/literal}{'first_contact_title36'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp36'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=themes",
    placement: "right",
    element: ".tabsheet",
    title: "{/literal}{'first_contact_title37'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp37'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugins",
    placement: "left",
    element: "",
    title: "{/literal}{'first_contact_title38'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp38'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugins",
    placement: "left",
    element: "#content",
    title: "{/literal}{'first_contact_title39'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp39'|@translate}{literal}"
  },
  {//40
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugins",
    placement: "bottom",
    element: "#TakeATour",
    title: "{/literal}{'first_contact_title40'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp40'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugins",
    placement: "right",
    element: ".tabsheet",
    title: "{/literal}{'first_contact_title41'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp41'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=languages",
    title: "{/literal}{'first_contact_title42'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp42'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugin-TakeATour",
    placement: "top",
    element: "",
    title: "{/literal}{'first_contact_title43'|@translate}{literal}",
    content: "{/literal}{'first_contact_stp43'|@translate}{literal}"
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