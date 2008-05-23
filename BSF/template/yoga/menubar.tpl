{* $Id$ *}

<div id="menubar">
{if not empty($links)}
<dl id="mbLinks">
  <dt>{'Links'|@translate}</dt>
  <dd>
  <ul>
    {foreach from=$links item=link}
    <li>
    <a href="{$link.URL}"
      {if isset($link.new_window) }onclick="window.open(this.href, '{$link.new_window.NAME|@escape:'javascript'}','{$link.new_window.FEATURES|@escape:'javascript'}'); return false;"{/if}
    >
      {$link.LABEL}
    </a>
    </li>
    {/foreach}{*links*}
  </ul>
  </dd>
</dl>
{/if}{*links*}

  {if isset($U_START_FILTER)}
  <a href="{$U_START_FILTER}" title="{'start_filter_hint'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/start_filter.png" class="button" alt="{'start_filter_hint'|@translate}"></a>
  {/if}
  {if isset($U_STOP_FILTER)}
  <a href="{$U_STOP_FILTER}" title="{'stop_filter_hint'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/stop_filter.png" class="button" alt="{'stop_filter_hint'|@translate}"></a>
  {/if}

<dl id="mbCategories">
  <dt><a href="{$U_CATEGORIES}">{'Categories'|@translate}</a></dt>
  <dd>
    {$MENU_CATEGORIES_CONTENT}
  {if isset($U_UPLOAD)}
  <ul><li>
    <a href="{$U_UPLOAD}">{'upload_picture'|@translate}</a>
  </li></ul>
  {/if}
    <p class="totalImages">{$pwg->l10n_dec('%d element', '%d elements', $NB_PICTURE)}</p>
  </dd>
</dl>


{if not empty($related_tags)}
<dl id="mbTags">
  <dt>{'Related tags'|@translate}</dt>
  <dd>
    <ul id="menuTagCloud">
    {foreach from=$related_tags item=tag}
    <li>
    {if !empty($tag.add) }
      <a href="{$tag.add.URL}" 
        title="{$pwg->l10n_dec('%d element are also linked to current tags', '%d elements are also linked to current tags', $tag.add.COUNTER)}"
        rel="nofollow">
        <img src="{$ROOT_URL}{$themeconf.icon_dir}/add_tag.png" alt="+" />
      </a>
    {/if}
    <a href="{$tag.U_TAG}" class="{$tag.CLASS}" title="{'See elements linked to this tag only'|@translate}">{$tag.NAME}</a>
    </li>
    {/foreach}
    </ul>
  </dd>
</dl>
{/if}


<dl id="mbSpecial">
  <dt>{'special_categories'|@translate}</dt>
  <dd>
    <ul>
      {foreach from=$special_categories item=cat}
      <li><a href="{$cat.URL}" title="{$cat.TITLE}" {if isset($cat.REL)}{$cat.REL}{/if}>{$cat.NAME}</a></li>
      {/foreach}
    </ul>
  </dd>
</dl>


<dl id="mbMenu">
  <dt>{'title_menu'|@translate}</dt>
  <dd>
    <form action="{$ROOT_URL}qsearch.php" method="get" id="quicksearch">
      <p>
        <input type="text" name="q" id="qsearchInput" onfocus="if (value==qsearch_prompt) value='';" onblur="if (value=='') value=qsearch_prompt;" />
      </p>
    </form>
    <script type="text/javascript">var qsearch_prompt="{'qsearch'|@translate|@escape:'javascript'}"; document.getElementById('qsearchInput').value=qsearch_prompt;</script>

    <ul>
    {foreach from=$summaries item=sum}
      <li><a href="{$sum.U_SUMMARY}" title="{$sum.TITLE}" {if isset($sum.REL)}{$sum.REL}{/if}>{$sum.NAME}</a></li>
    {/foreach}
    </ul>
  </dd>
</dl>


<dl id="mbIdentification">
  <dt>{'identification'|@translate}</dt>
  <dd>
    {if isset($USERNAME)}
    <p>{'hello'|@translate}&nbsp;{$USERNAME}&nbsp;!</p>
    {/if}
    
  <ul>
    {if isset($U_REGISTER)}
    <li><a href="{$U_REGISTER}" title="{'Create a new account'|@translate}" rel="nofollow">{'Register'|@translate}</a></li>
    {/if}

    {if isset($U_IDENTIFY)}
    <li><a href="{$U_IDENTIFY}" rel="nofollow">{'Connection'|@translate}</a></li>
    {/if}

    {if isset($U_LOGOUT)}
    <li><a href="{$U_LOGOUT}">{'logout'|@translate}</a></li>
    {/if}

    {if isset($U_PROFILE)}
    <li><a href="{$U_PROFILE}" title="{'hint_customize'|@translate}">{'customize'|@translate}</a></li>
    {/if}

    {if isset($U_ADMIN)}
    <li><a href="{$U_ADMIN}" title="{'hint_admin'|@translate}">{'admin'|@translate}</a></li>
    {/if}
  </ul>
  
  {if isset($U_IDENTIFY)}
  <form method="post" action="{$U_IDENTIFY}" class="filter" id="quickconnect">
  <fieldset>
    <legend>{'Quick connect'|@translate}</legend>

    <label>
      {'Username'|@translate}
      <input type="text" name="username" size="15" value="">
    </label>

    <label>
      {'Password'|@translate}
      <input type="password" name="password" size="15">
    </label>

    {if $AUTHORIZE_REMEMBERING}
    <label>
      {'remember_me'|@translate}
      <input type="checkbox" name="remember_me" value="1">
    </label>
    {/if}
    <p>
     <input class="submit" type="submit" name="login" value="{'Submit'|@translate}">
    </p>
    
    <ul class="actions">
      <li><a href="{$U_LOST_PASSWORD}" title="{'Forgot your password?'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/lost_password.png" class="button" alt="{'Forgot your password?'|@translate}"></a></li>
      {if isset($U_REGISTER)}
      <li><a href="{$U_REGISTER}" title="{'Create a new account'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/register.png" class="button" alt="{'Register'|@translate}"/></a></li>
      {/if}
    </ul>

  </fieldset>
  </form>
    {/if}

  </dd>
</dl>
</div> <!-- menubar -->
