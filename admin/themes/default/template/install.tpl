<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html lang="{$lang_info.code}" dir="{$lang_info.direction}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$T_CONTENT_ENCODING}">
<meta http-equiv="Content-script-type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="shortcut icon" type="image/x-icon" href="{$ROOT_URL}{$themeconf.icon_dir}/favicon.ico">

{foreach from=$themes item=theme}
{if isset($theme.local_head)}{include file=$theme.local_head}{/if}
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/themes/{$theme.id}/theme.css">
{/foreach}

<script type="text/javascript" src="themes/default/js/jquery.packed.js"></script>
{literal}
<script type="text/javascript">
$(function() {
    $option_selected = $('#dblayer option:selected').attr('value');
    if ($option_selected=='sqlite' || $option_selected=='pdo-sqlite') {
       $('input[name=dbhost],input[name=dbuser],input[name=dbpasswd]').parent().parent().hide();
    }
    if ($option_selected=='mysql') {
        $('#experimentalDbEngines').hide();
    }

    $('#dblayer').change(function() {
        $db = this;
        if ($db.value=='sqlite' || $db.value=='pdo-sqlite') {
           $('input[name=dbhost],input[name=dbuser],input[name=dbpasswd]').parent().parent().hide();
        } else {
           $('input[name=dbhost],input[name=dbuser],input[name=dbpasswd]').parent().parent().show();
        }

        if ($db.value=='mysql') {
            $('#experimentalDbEngines').hide();
        }
        else {
            $('#experimentalDbEngines').show();
        }
      });
  });

$(document).ready(function() {
  $("a.externalLink").click(function() {
    window.open($(this).attr("href"));
    return false;
  });
});

</script>

<style type="text/css">
body {
  background:url("admin/themes/roma/images/bottom-left-bg.jpg") no-repeat fixed left bottom #111111;
}

.content {
 background:url("admin/themes/roma/images/fillet.png") repeat-x scroll left top #222222;
 width: 800px;
 margin: auto;
 text-align: center;
 padding: 5px;
}

#headbranch  {
  background:url("admin/themes/roma/images/top-left-bg.jpg") no-repeat scroll left top transparent;
}

#theHeader {
  display: block;
  background:url("admin/themes/roma/images/piwigo_logo_sombre_214x100.png") no-repeat scroll 245px top transparent;
}

.content h2 {
  display:block;
  font-size:28px;
  height:104px;
  width:54%;
  color:#666666;
  letter-spacing:-1px;
  margin:0 30px 3px 20px;
  overflow:hidden;
  position:absolute;
  right:0;
  text-align:right;
  top:0;
  width:770px;
  text-align:right;
  text-transform:none; 
}

table.table2 {
  width: 100%;
  margin-bottom: 1em !important;
  border:0;
}

TD {
  text-align: left;
  padding: 0.1em 0.5em;
  height: 2.5em;
}

.infos {
  background-color:transparent;
  border:none;
  color:#999;
}

.sql_content, .infos a {
  color: #ff3363;
}

.config_creation_failed {
  text-align:left;
  border:3px solid #F20D00;
  color:#999;
  margin:20px;
  padding:0px 20px 5px 20px;
  background-image:url(admin/themes/default/icon/errors.png);
  background-repeat:no-repeat;
}

#experimentalDbEngines TD {border:2px solid #666;background-color:#444; color:#ccc;}
</style>
{/literal}
<title>Piwigo {$RELEASE} - {'Installation'|@translate}</title>
</head>

<body>
<div id="headbranch"></div> {* Dummy block for double background management *}
<div id="the_page">
<div id="theHeader"></div>
<div id="content" class="content">

<h2>Piwigo {$RELEASE} - {'Installation'|@translate}</h2>

{if isset($config_creation_failed)}
<div class="config_creation_failed">
  <p style="margin-left:30px;">
    <strong>{'Creation of config file local/config/database.inc.php failed.'|@translate}</strong>
  </p>
  <ul>
    <li>
      <p>{'You can download the config file and upload it to local/config directory of your installation.'|@translate}</p>
      <p style="text-align:center">
          <input type="button" value="{'Download the config file'|@translate}" onClick="window.open('{$config_url}');">
      </p>
    </li>
    <li>
      <p>{'An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'|@translate}</p>
      <textarea rows="15" cols="70">{$config_file_content}</textarea>
    </li>
  </ul>
</div>
{/if}

{if isset($errors)}
<div class="errors">
  <ul>
    {foreach from=$errors item=error}
    <li>{$error}</li>
    {/foreach}
  </ul>
</div>
{/if}

{if isset($infos)}
<div class="infos">
  <ul>
    {foreach from=$infos item=info}
    <li>{$info}</li>
    {/foreach}
  </ul>
</div>
{/if}

{if isset($install)}
<form method="POST" action="{$F_ACTION}" name="install_form">

  <table class="table2">
    <tr class="throw">
      <th colspan="2">{'Basic configuration'|@translate}</th>
    </tr>
    <tr>
      <td style="width: 30%">{'Default gallery language'|@translate}</td>
      <td>
    <select name="language" onchange="document.location = 'install.php?language='+this.options[this.selectedIndex].value;">
    {html_options options=$language_options selected=$language_selection}
    </select>
      </td>
    </tr>
  </table>
  <table class="table2">
    <tr class="throw">
      <th colspan="3">{'Database configuration'|@translate}</th>
    </tr>
    {if count($F_DB_ENGINES)>1}
    <tr>
      <td style="width: 30%;">{'Database type'|@translate}</td>
      <td>
	<select name="dblayer" id="dblayer">
	  {foreach from=$F_DB_ENGINES key=k item=v}
	  <option value="{$k}"
		  {if $k==$F_DB_LAYER and $v.available} selected="selected"{/if}
		  {if !$v.available} disabled="disabled"{/if}
		  >{$v.label}</option>
	  {/foreach}
	</select>    
      </td>
      <td>{'The type of database your piwigo data will be store in'|@translate}</td>
    {else}
    <td colspan="3">
    <input type="hidden" name="dbengine" value="{$F_DB_LAYER}">
    </td>
    {/if}
    </tr>
    <tr id="experimentalDbEngines">
      <td colspan="3">
      {'SQLite and PostgreSQL are currently in experimental state.'|@translate}
      <a href="http://piwigo.org/forum/viewtopic.php?id=15927" class="externalLink">{'Learn more'|@translate}</a>
      </td>
    </tr>
    <tr>
      <td style="width: 30%;">{'Host'|@translate}</td>
      <td align=center><input type="text" name="dbhost" value="{$F_DB_HOST}"></td>
      <td>{'localhost, sql.multimania.com, toto.freesurf.fr'|@translate}</td>
    </tr>
    <tr>
      <td>{'User'|@translate}</td>
      <td align=center><input type="text" name="dbuser" value="{$F_DB_USER}"></td>
      <td>{'user login given by your host provider'|@translate}</td>
    </tr>
    <tr>
      <td>{'Password'|@translate}</td>
      <td align=center><input type="password" name="dbpasswd" value=""></td>
      <td>{'user password given by your host provider'|@translate}</td>
    </tr>
    <tr>
      <td>{'Database name'|@translate}</td>
      <td align=center><input type="text" name="dbname" value="{$F_DB_NAME}"></td>
      <td>{'also given by your host provider'|@translate}</td>
    </tr>
    <tr>
      <td>{'Database table prefix'|@translate}</td>
      <td align=center><input type="text" name="prefix" value="{$F_DB_PREFIX}"></td>
      <td>{'database tables names will be prefixed with it (enables you to manage better your tables)'|@translate}</td>
    </tr>
  </table>

  <table class="table2">
    <tr class="throw">
      <th colspan="3">{'Admin configuration'|@translate}</th>
    </tr>
    <tr>
      <td style="width: 30%;">{'Webmaster login'|@translate}</td>
      <td align="center"><input type="text" name="admin_name" value="{$F_ADMIN}"></td>
      <td>{'It will be shown to the visitors. It is necessary for website administration'|@translate}</td>
    </tr>
    <tr>
      <td>{'Webmaster password'|@translate}</td>
      <td align="center"><input type="password" name="admin_pass1" value=""></td>
      <td>{'Keep it confidential, it enables you to access administration panel'|@translate}</td>
    </tr>
    <tr>
      <td>{'Password [confirm]'|@translate}</td>
      <td align="center"><input type="password" name="admin_pass2" value=""></td>
      <td>{'verification'|@translate}</td>
    </tr>
    <tr>
      <td>{'Webmaster mail address'|@translate}</td>
      <td align="center"><input type="text" name="admin_mail" value="{$F_ADMIN_EMAIL}"></td>
      <td>{'Visitors will be able to contact site administrator with this mail'|@translate}</td>
    </tr>
  </table>

  <table>
    <tr>
      <td style="text-align: center;">
        <input class="submit" type="submit" name="install" value="{'Start Install'|@translate}">
      </td>
    </tr>
  </table>
</form>
{else}
<p>
  <input type="button" name="Home" value="{'Visit Gallery'|@translate}" onClick="window.open('index.php');">
</p>

{if !isset($migration)}
<div class="infos">
  <ul>
    <li>{'Keep in touch with Piwigo project, subscribe to Piwigo Announcement Newsletter. You will receive emails when a new release is available (sometimes including a security bug fix, it\'s important to know and upgrade) and when major events happen to the project. Only a few emails a year.'|@translate}</li>
  </ul>
</div>

<p>
  <input type="button" name="subscribe" value="{'Subscribe %s'|@translate|@sprintf:$F_ADMIN_EMAIL}" onClick="window.open('{$SUBSCRIBE_BASE_URL}{$F_ADMIN_EMAIL}');">
</p>
{/if}
{/if}
</div> {* content *}
<div style="text-align: center">{$L_INSTALL_HELP}</div>
</div> {* the_page *}
</body>
</html>
