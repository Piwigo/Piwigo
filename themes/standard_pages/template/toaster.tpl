{combine_script id='toaster_js' load='async' require='jquery' path='themes/standard_pages/js/toaster.js'}
{html_style}
.toast.template-pwg-toaster {
  display: none;
}

.toaster {
  position: fixed;
  right: 15px;
  max-width: 300px;
  top: 40px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  z-index: 9999;
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
  box-shadow: 0px 4px 10px 0px rgba(0, 0, 0, 0.15);
}

.toast i:before {
  font-size: 33px;
}

.light .toast.success {
  background-color: #D6FFCF;
  color: #4CA530;
}

.light .toast.error {
  background-color: #F8D7DC;
  color: #EB3D33;
}

.dark .toast.success {
  background-color: #4EA590;
  color: #AAF6E4;
}

.dark .toast.error {
  background-color: #BE4949;
  color: #FFC8C8;
}
{/html_style}
<div class="toaster" id="pwg_toaster">
  <div class="toast template-pwg-toaster" id="toast_template">
    <i class="toast_icon"></i>
    <p class="toast_text"></p>
  </div>
</div>