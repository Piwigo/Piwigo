{footer_script require='jquery.bootstrap-tour'}{literal}
// Instance the tour
var tour = new Tour({
  name: "scaling",
  orphan: true,
  onEnd: function (tour) {window.location = "admin.php?page=plugin-TakeATour&tour_ended=scaling";},
});
{/literal}{if $TAT_restart}tour.restart();{/if}{literal}
// Add your steps. Not too many, you don't really want to get your users sleepy
tour.addSteps([
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    title: "{/literal}{'Welcome on the /'Scaling/' Tour'|@translate}{literal}",
    content: "{/literal}{'This tour will show you how to configure your Piwigo according to your server resources. This tour is for beginners and for advanced user, so you can skip technical steps if you want.'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    title: "{/literal}{'Servers'|@translate}{literal}",
    content: "{/literal}{'On free hosting and shared hosting, multiple websites are on the same physical server, so resources are shared. So your hosting provider may restrictions for CPU and memory consumptions.<br>For dedicated servers, you will be able to adjust the resource consumption of your Piwigo.'|@translate}{literal}"
  },

  {
    path: "{/literal}{$TAT_path}{literal}admin.phpSIZE",
    content: "{/literal}{'The main resource consumption is the generation of resized pictures. To lower that, you can disable some size. Size are for plugins and themes to display the best size according to the user screen, and for the users who can prefer lower resolution photos (bandwidth etc)'|@translate}{literal}"
  }

  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=batch_manager",
    title: "{/literal}{'Resized picture generation'|@translate}{literal}",
    content: "{/literal}{'Piwigo generates on the demand and on the fly the resized pictures: so the first user browsing the gallery after an upload will trigger the generation of the thumbnails, for instance. At that moment the server ressources might be too hight for some hosting. Then those pictures generated are stored, so no further picture generation will be done again.<br>After a big upload, you might prefer to trigger the generation of those resized pictures yourself: instead of having a small long term resource consumption; the server will have a short peak of computation. According to your hosting, it might be better to generate once, quickly. Choose the /"Generate/" action in the Batch Manager to do so.<br>For advanced users, and if you have a FTP access, you can upload resized pictures
generated on your computer in the _data/i folder: be careful when naming the files.'|@translate}{literal}"
  },

  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    title: "{/literal}{'Graphic library'|@translate}{literal}",
    content: "{/literal}{''|@translate}{literal}"
  },

  {
    path: "{/literal}{$TAT_path}{literal}admin.phpHISTORY",
    title: "{/literal}{'History'|@translate}{literal}",
    content: "{/literal}{'Some hosting has a limitation for how much data can be stored in the database. The history data can become huge if you record guest visits and don/'t purge the history. So you can disable it or disable for guests, but check before the available plugins!<br>So if you get an error about a "piwigo_history" "table", it/'s probably about a needed purge of the history.'|@translate}{literal}"
  },

  {
    path: "{/literal}{$TAT_path}{literal}admin.phpPLUGINS",
    title: "{/literal}{'Local Configuration'|@translate}{literal}",
    content: "{/literal}{'Please enable the Local Files Editor for the next step.'|@translate}{literal}"
  },

  {
    path: "{/literal}{$TAT_path}{literal}admin.phpLFE",
    title: "{/literal}{'Local Configuration'|@translate}{literal}",
    content: "{/literal}{'Piwigo has a Local Configuration, which is in fact a list of variables not present in the Graphic Interface. The Default Configuration file has every variables available in it, the default values for them and an explicative text for each of them.<br>To set your own values, use that page which is a text editor for Local Configuration file: the values in the local config override the default config.<br>The workflow is quite simple...|@translate}{literal}"
  },

  {
    path: "{/literal}{$TAT_path}{literal}admin.phpLFE",
    placement: "left",
    element: "LIEN",
    title: "{/literal}{'Servers'|@translate}{literal}",
    content: "{/literal}{'Browse the default config, and when you have found some interesting variable, copy/paste here and change the value.'|@translate}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.phpLFE",
    title: "{/literal}{'Local Configuration'|@translate}{literal}",
    content: "{/literal}{'Some variable you could change for scaling up or down your Piwigo:<ul><li>$conf['template_compile_check'] : This tells Smarty whether to check for recompiling or not. Recompiling does not need to happen unless a template is changed. false results in better performance.</li><li>$conf['compiled_template_cache_language'] : if true, some language strings are replaced during template compilation (instead of template output). This results in better performance. However any change in the language file will not be propagated until you purge  the compiled templates from the admin / maintenance menu</li><li>$conf['template_combine_files'] : if true -defaukt value-, it activates merging of javascript / css files in order to reduce a lot the loading of the server due to multiple requests.</li><li>$conf['max_requests'] : maximum Ajax requests at once, for thumbnails on-the-fly generation. Increase that number (3 by default) if your server can handle the resource consumption due to the generation of the thumbnails, and if you want a better user experience.</li></ul>'|@translate}{literal}",
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.phpLFE",
    title: "{/literal}{'Finished'|@translate}{literal}",
    content: "{/literal}{'There are other variables you might tune, some features you could disable (ratings, comments etc), custom theme you could do to remove some information but the thing is Piwigo is already flexible and powerful. The only critical point which might be raised, is the generation of resized pictures: go back in this tour to remember what I told you.<br>Now you can end this tour, and I hope to see you soon.'|@translate}{literal}"
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