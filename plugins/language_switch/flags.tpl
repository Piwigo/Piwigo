</ul>
<ul class="categoryActions">
<li class="menuf">
  <div>
    <ul>
      <li>
        <a rel="nofollow" href="#">
          <img class="flags" src="{$lang_switch.Active.img}" alt="{$lang_switch.Active.alt}" title="{$lang_switch.Active.alt}"/>
        </a>
<!--[if lte IE 6]>
        <a rel="nofollow" href="#">
          <img class="flags" src="{$lang_switch.Active.img}" alt="{$lang_switch.Active.alt}" title="{$lang_switch.Active.alt}"/>
          <table>
            <tr>
              <td>
<![endif]-->
        <ul class="flag-pan">

{foreach from=$lang_switch.flags key=code item=flag name=f}
          <li>
            <a rel="nofollow" href="{$SCRIPT_NAME}{$flag.url}">
              <img class="flags" src="{$flag.img}" alt="{$flag.alt}" title="{$flag.alt}"/>
            </a>
          </li>
{/foreach}

        </ul>
<!--[if lte IE 6]>
              </td>
            </tr>
          </table>
        </a>
<![endif]-->
      </li>
    </ul>
  </div>
</li>

{combine_css path="plugins/language_switch/language_switch-default.css"}

{if $themeconf.name eq 'Sylvia'}
{combine_css path="plugins/language_switch/language_switch-Sylvia.css"}
{/if}

{html_head}
<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}plugins/language_switch/language_switch-ie6.css">
<![endif]-->
{/html_head}