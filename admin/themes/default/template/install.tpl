<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html lang="{$lang_info.code}" dir="{$lang_info.direction}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$T_CONTENT_ENCODING}">
<meta http-equiv="Content-script-type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="shortcut icon" type="image/x-icon" href="{$ROOT_URL}{$themeconf.icon_dir}/favicon.ico">

{get_combined_css}
{foreach from=$themes item=theme}
{if $theme.load_css}
{combine_css path="admin/themes/`$theme.id`/theme.css" order=-10}
{/if}
{/foreach}

<!--[if IE 7]>
  <link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/themes/default/fix-ie7.css">
<![endif]-->

<!-- BEGIN get_combined_scripts -->
{get_combined_scripts load='header'}
<!-- END get_combined_scripts -->

{combine_script id='jquery' path='themes/default/js/jquery.min.js'}
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

  $("#admin_mail").keyup(function() {
    $(".adminEmail").text($(this).val());
  });
});

</script>

<style type="text/css">
body {
  font-size:12px;
}

.content {
 width: 800px;
 margin: auto;
 text-align: center;
 padding:0;
 background-color:transparent !important;
 border:none;
}

#theHeader {
  display: block;
  background:url("admin/themes/clear/images/piwigo_logo_big.png") no-repeat scroll center 20px transparent;
  height:100px;
}

fieldset {
  margin-top:20px;
  background-color:#f1f1f1;
}

legend {
  font-weight:bold;
  letter-spacing:2px;
}

.content h2 {
  display:block;
  font-size:20px;
  text-align:center;
  /* margin-top:5px; */
}

table.table2 {
  width: 100%;
  border:0;
}

table.table2 td {
  text-align: left;
  padding: 5px 2px;
}

table.table2 td.fieldname {
  font-weight:normal;
}

table.table2 td.fielddesc {
  padding-left:10px;
  font-style:italic;
}

input[type="submit"], input[type="button"], a.bigButton {
  font-size:14px;
  font-weight:bold;
  letter-spacing:2px;
  border:none;
  background-color:#666666;
  color:#fff;
  padding:5px;
  -moz-border-radius:5px;
}

input[type="submit"]:hover, input[type="button"]:hover, a.bigButton:hover {
  background-color:#ff7700;
  color:white;
}

input[type="text"], input[type="password"], select {
  background-color:#ddd;
  border:2px solid #ccc;
  -moz-border-radius:5px;
  padding:2px;
}

input[type="text"]:focus, input[type="password"]:focus, select:focus {
  background-color:#fff;
  border:2px solid #ff7700;
}

.sql_content, .infos a {
  color: #ff3363;
}

.errors {
  padding-bottom:5px;
}

</style>
{/literal}

{combine_script id='jquery.cluetip' load='async' require='jquery' path='themes/default/js/plugins/jquery.cluetip.packed.js'}

{footer_script require='jquery.cluetip'}
jQuery().ready(function(){ldelim}
	jQuery('.cluetip').cluetip({ldelim}
		width: 300,
		splitTitle: '|',
		positionBy: 'bottomTop'
	});
});
{/footer_script}


<title>Piwigo {$RELEASE} - {'Installation'|@translate}</title>
</head>

<body>
<div id="the_page">
<div id="theHeader"></div>
<div id="content" class="content">

<h2>{'Version'|@translate} {$RELEASE} - {'Installation'|@translate}</h2>

{if isset($config_creation_failed)}
<div class="errors">
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

<fieldset>
  <legend>{'Basic configuration'|@translate}</legend>

  <table class="table2">
    <tr>
      <td style="width: 30%">{'Default gallery language'|@translate}</td>
      <td>
    <select name="language" onchange="document.location = 'install.php?language='+this.options[this.selectedIndex].value;">
    {html_options options=$language_options selected=$language_selection}
    </select>
      </td>
    </tr>
  </table>
</fieldset>

<fieldset>
  <legend>{'Database configuration'|@translate}</legend>

  <table class="table2">
    {if count($F_DB_ENGINES)>1}
    <tr>
      <td style="width: 30%;" class="fieldname">{'Database type'|@translate}</td>
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
      <td class="fielddesc">{'The type of database your piwigo data will be store in'|@translate}</td>
    {else}
    <td colspan="3">
    <input type="hidden" name="dbengine" value="{$F_DB_LAYER}">
    </td>
    {/if}
    </tr>
    <tr id="experimentalDbEngines">
      <td colspan="3">
<div class="warnings">
      {'SQLite and PostgreSQL are currently in experimental state.'|@translate}
      <a href="http://piwigo.org/forum/viewtopic.php?id=15927" class="externalLink">{'Learn more'|@translate}</a>
</div>
      </td>
    </tr>
    <tr>
      <td style="width: 30%;" class="fieldname">{'Host'|@translate}</td>
      <td><input type="text" name="dbhost" value="{$F_DB_HOST}"></td>
      <td class="fielddesc">{'localhost, sql.multimania.com, toto.freesurf.fr'|@translate}</td>
    </tr>
    <tr>
      <td class="fieldname">{'User'|@translate}</td>
      <td><input type="text" name="dbuser" value="{$F_DB_USER}"></td>
      <td class="fielddesc">{'user login given by your host provider'|@translate}</td>
    </tr>
    <tr>
      <td class="fieldname">{'Password'|@translate}</td>
      <td><input type="password" name="dbpasswd" value=""></td>
      <td class="fielddesc">{'user password given by your host provider'|@translate}</td>
    </tr>
    <tr>
      <td class="fieldname">{'Database name'|@translate}</td>
      <td><input type="text" name="dbname" value="{$F_DB_NAME}"></td>
      <td class="fielddesc">{'also given by your host provider'|@translate}</td>
    </tr>
    <tr>
      <td class="fieldname">{'Database table prefix'|@translate}</td>
      <td><input type="text" name="prefix" value="{$F_DB_PREFIX}"></td>
      <td class="fielddesc">{'database tables names will be prefixed with it (enables you to manage better your tables)'|@translate}</td>
    </tr>
  </table>

</fieldset>
<fieldset>
  <legend>{'Admin configuration'|@translate}</legend>

  <table class="table2">
    <tr>
      <td style="width: 30%;" class="fieldname">{'Webmaster login'|@translate}</td>
      <td><input type="text" name="admin_name" value="{$F_ADMIN}"></td>
      <td class="fielddesc">{'It will be shown to the visitors. It is necessary for website administration'|@translate}</td>
    </tr>
    <tr>
      <td class="fieldname">{'Webmaster password'|@translate}</td>
      <td><input type="password" name="admin_pass1" value=""></td>
      <td class="fielddesc">{'Keep it confidential, it enables you to access administration panel'|@translate}</td>
    </tr>
    <tr>
      <td class="fieldname">{'Password [confirm]'|@translate}</td>
      <td><input type="password" name="admin_pass2" value=""></td>
      <td class="fielddesc">{'verification'|@translate}</td>
    </tr>
    <tr>
      <td class="fieldname">{'Webmaster mail address'|@translate}</td>
      <td><input type="text" name="admin_mail" id="admin_mail" value="{$F_ADMIN_EMAIL}"></td>
      <td class="fielddesc">{'Visitors will be able to contact site administrator with this mail'|@translate}</td>
    </tr>
    <tr>
      <td>{'Options'|@translate}</options>
      <td colspan="2">
<label>
<input type="checkbox" name="newsletter_subscribe"{if $F_NEWSLETTER_SUBSCRIBE} checked="checked"{/if}>
<span class="cluetip" title="{'Piwigo Announcements Newsletter'|@translate}|{'Keep in touch with Piwigo project, subscribe to Piwigo Announcement Newsletter. You will receive emails when a new release is available (sometimes including a security bug fix, it\'s important to know and upgrade) and when major events happen to the project. Only a few emails a year.'|@translate|htmlspecialchars|nl2br}">{'Subscribe %s to Piwigo Announcements Newsletter'|@translate|@sprintf:$EMAIL}</span>
</label>
<br>
      </td>
    </tr>
  </table>

</fieldset>

  <div style="text-align:center; margin:20px 0 10px 0">
    <input class="submit" type="submit" name="install" value="{'Start Install'|@translate}">
  </div>
</form>
{else}
<p>
  <a class="bigButton" href="index.php">{'Visit Gallery'|@translate}</a>
</p>
{/if}
</div> {* content *}
<div style="text-align: center">{$L_INSTALL_HELP}</div>
</div> {* the_page *}

<!-- BEGIN get_combined_scripts -->
{get_combined_scripts load='footer'}
<!-- END get_combined_scripts -->

</body>
</html>
