{footer_script require='jquery.bootstrap-tour'  load="async"}{literal}

var tour = new Tour({
  name: "first_contact",
  orphan: true,
  onEnd: function (tour) {window.location = "{/literal}{$ABS_U_ADMIN}{literal}admin.php?page=plugin-TakeATour&tour_ended=first_contact"},
  template: "<div class='popover'>          <div class='arrow'></div>          <h3 class='popover-title'></h3>          <div class='popover-content'></div>          <div class='popover-navigation'>            <div class='btn-group'>              <button class='btn btn-sm btn-default' data-role='prev'>&laquo; {/literal}{'Prev'|@translate|@escape:'javascript'}{literal}</button>              <button class='btn btn-sm btn-default' data-role='next'>{/literal}{'Next '|@translate|@escape:'javascript'}{literal} &raquo;</button>            </div>            <button class='btn btn-sm btn-default' data-role='end'>{/literal}{'End tour'|@translate|@escape:'javascript'}{literal}</button>          </div>        </div>",
});
{/literal}{if $TAT_restart}tour.restart();{/if}{literal}

tour.addSteps([
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    title: "{/literal}{'first_contact_title1'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp1'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    placement: "right",
    element: "a[href='./admin.php?page=photos_add']",
    reflex:true,
    title: "{/literal}{'first_contact_title2'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp2'|@translate|@escape:'javascript'}{literal}",
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=photos_add",
    placement: "bottom",
    element: ".selected_tab",
    title: "{/literal}{'first_contact_title3'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp3'|@translate|@escape:'javascript'}{literal}",
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=photos_add",
    placement: "right",
    element: "#albumSelection",
    title: "{/literal}{'first_contact_title4'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp4'|@translate|@escape:'javascript'}{literal}"
  },
  {//5
    path: "{/literal}{$TAT_path}{literal}admin.php?page=photos_add",
    placement: "top",
    element: ".plupload_add",
    title: "{/literal}{'first_contact_title5'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp5'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=photos_add",
    placement: "top",
    element: ".plupload_start",
    title: "{/literal}{'first_contact_title6'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp6'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=photos_add",
    placement: "top",
    element: "#afterUploadActions",
    title: "{/literal}{'first_contact_title7'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp7'|@translate|@escape:'javascript'}{literal}",
    prev:3,
    onPrev: function (tour) {window.location.reload()}
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=photos_add",
    placement: "top",
    element: ".batchLink",
    reflex:true,
    title: "{/literal}{'first_contact_title8'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp8'|@translate|@escape:'javascript'}{literal}",
  },
  {
    path: /admin\.php\?page=(photos_add|batch_manager&filter=prefilter-last_import|batch_manager&filter=prefilter-caddie)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "top",
    element: "select[name='filter_prefilter']",
    title: "{/literal}{'first_contact_title9'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp9'|@translate|@escape:'javascript'}{literal}",
    prev:3,
    onPrev: function (tour) {window.location = "{/literal}{$ABS_U_ADMIN}{literal}admin.php?page=photos_add"}
 },
  {//10
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "right",
    element: "a[href='./admin.php?page=batch_manager&filter=prefilter-caddie']",
    title: "{/literal}{'first_contact_title10'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp10'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "left",
    element: "#checkActions",
    title: "{/literal}{'first_contact_title11'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp11'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "top",
    element: "#action",
    title: "{/literal}{'first_contact_title12'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp12'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "bottom",
    element: "#tabsheet .normal_tab",
    title: "{/literal}{'first_contact_title13'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp13'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "top",
    element: "#TAT_FC_14",
    reflex:true,
    title: "{/literal}{'first_contact_title14'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp14'|@translate|@escape:'javascript'}{literal}",
    onNext:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";}
  },
  {//15
    path: /admin\.php\?page=photo-/,
    redirect:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},
    placement: "bottom",
    element: ".selected_tab",
    title: "{/literal}{'first_contact_title15'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp15'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=photo-/,
    redirect:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},
    placement: "top",
    element: "#TAT_FC_16",
    title: "{/literal}{'first_contact_title16'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp16'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=photo-/,
    redirect:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},
    placement: "top",
    element: "#TAT_FC_17",
    title: "{/literal}{'first_contact_title17'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp17'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=photo-/,
    redirect:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},
    placement: "top",
    title: "{/literal}{'first_contact_title18'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp18'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=cat_list",
    title: "{/literal}{'first_contact_title19'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{if $TAT_FTP}{'first_contact_stp19'|@translate|@escape:'javascript'}{else}{'first_contact_stp19_b'|@translate|@escape:'javascript'}{/if}{literal}",
    onPrev: function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},

  },
  {//20
    path: "{/literal}{$TAT_path}{literal}admin.php?page=cat_list",
    placement: "top",
    element: "#categoryOrdering",
    title: "{/literal}{'first_contact_title20'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp20'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=cat_list",
    placement: "left",
    element: "#tabsheet:first-child",
    title: "{/literal}{'first_contact_title21'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp21'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+(|-properties)$/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}";},
    placement: "top",
    element: ".selected_tab",
    title: "{/literal}{'first_contact_title22'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp22'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+(|-properties)$/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}";},
    placement: "top",
    element: "#TAT_FC_23",
    title: "{/literal}{'first_contact_title23'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp23'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+(|-properties)$/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}";},
    placement: "bottom",
    element: "li.normal_tab:nth-child(3) > a:nth-child(1)",
    reflex: true,
    title: "{/literal}{'first_contact_title24'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp24'|@translate|@escape:'javascript'}{literal}"
  },
  {//25
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "left",
    element: "#content",
    title: "{/literal}{'first_contact_title25'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp25'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "top",
    element: "#selectStatus",
    title: "{/literal}{'first_contact_title26'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp26'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "top",
    element: "#selectStatus",
    title: "{/literal}{'first_contact_title27'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp27'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "top",
    title: "{/literal}{'first_contact_title28'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp28'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    placement: "top",
    element: "",
    title: "{/literal}{'first_contact_title29'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp29'|@translate|@escape:'javascript'}{literal}"
  },
  {//30
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    placement: "right",
    element: "#gallery_title",
    title: "{/literal}{'first_contact_title30'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp30'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    placement: "right",
    element: "#page_banner",
    title: "{/literal}{'first_contact_title31'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp31'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    reflex: true,
    placement: "top",
    element: ".formButtons input",
    title: "{/literal}{'first_contact_title32'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp32'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    placement: "bottom",
    element: "li.normal_tab:nth-child(6) > a:nth-child(1)",
    title: "{/literal}{'first_contact_title33'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp33'|@translate|@escape:'javascript'}{literal}",
    prev:30
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=themes",
    placement: "top",
    element: "",
    title: "{/literal}{'first_contact_title34'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp34'|@translate|@escape:'javascript'}{literal}"
  },
  {//35
    path: "{/literal}{$TAT_path}{literal}admin.php?page=themes",
    placement: "top",
    element: "#TAT_FC_35",
    title: "{/literal}{'first_contact_title35'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp35'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=themes",
    placement: "top",
    element: "",
    title: "{/literal}{'first_contact_title36'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp36'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=themes",
    placement: "right",
    element: ".tabsheet",
    title: "{/literal}{'first_contact_title37'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp37'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugins",
    placement: "left",
    element: "",
    title: "{/literal}{'first_contact_title38'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp38'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugins",
    placement: "left",
    element: "#content",
    title: "{/literal}{'first_contact_title39'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp39'|@translate|@escape:'javascript'}{literal}"
  },
  {//40
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugins",
    placement: "bottom",
    element: "#TakeATour",
    title: "{/literal}{'first_contact_title40'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp40'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugins",
    placement: "right",
    element: ".tabsheet",
    title: "{/literal}{'first_contact_title41'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp41'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=languages",
    title: "{/literal}{'first_contact_title42'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp42'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    title: "{/literal}{'first_contact_title43'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'first_contact_stp43'|@translate|@escape:'javascript'}{literal}"
  }
]);

// Initialize the tour
tour.init();

// Start the tour
tour.start();
{/literal}{/footer_script}