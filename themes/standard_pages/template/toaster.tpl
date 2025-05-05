{combine_script id='toaster_js' load='async' require='jquery' path='themes/standard_pages/js/toaster.js'}
{html_style}
.toast.template {
  display: none;
}

.toaster {
  position: absolute;
  right: 15px;
  max-width: 300px;
  top: 40px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.toast {
  display: flex;
  gap: 5px;
  padding: 10px;
  border-radius: 5px;
  align-items: center;
  font-size: 15px;
  width: fit-content;
  align-self: flex-end;
}

.toast i:before {
  font-size: 33px;
}

.toast.success {
  background-color:#4CA530;
  color:#D6FFCF;
}

.toast.error {
  background-color:#BE4949;
  color:#FFC8C8;
}
{/html_style}
<div class="toaster" id="pwg_toaster">
  <div class="toast template" id="toast_template">
    <i class="toast_icon"></i>
    <p class="toast_text"></p>
  </div>
</div>