{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}
{footer_script}
  $("h1").append(`<span class='badge-number'>`+{$nb_people}+`</span>`);
{/footer_script}

<div>
  <p>people settings</p>
</div>