<?php
define('MULTIVIEW_CONTROLLER', 1);
define('PHPWG_ROOT_PATH','../../');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

if (!is_admin() or !function_exists('multiview_user_init') )
{
  pwg_unset_session_var( 'multiview_as' );
  pwg_unset_session_var( 'multiview_theme' );
  pwg_unset_session_var( 'multiview_lang' );
?>
<script type="text/javascript">
  window.close();
</script>
<?php
  exit();
}

$refresh_main = false;

if ( isset($_GET['view_guest']) )
{
  pwg_set_session_var( 'multiview_as', $conf['guest_id'] );
  $refresh_main = true;
}
elseif ( isset($_GET['view_admin']) )
{
  pwg_unset_session_var('multiview_as');
  $refresh_main = true;
}
$view_as = pwg_get_session_var( 'multiview_as', 0 );


if ( isset($_GET['theme']) )
{
  pwg_set_session_var( 'multiview_theme', $_GET['theme'] );
  $refresh_main = true;
}

if ( isset($_GET['lang']) )
{
  pwg_set_session_var( 'multiview_lang', $_GET['lang'] );
  $refresh_main = true;
}

$my_url = get_root_url().'plugins/'.basename(dirname(__FILE__)).'/'.basename(__FILE__);

$themes_html='Theme: <select onchange="document.location = this.options[this.selectedIndex].value;">';
foreach (get_pwg_themes() as $pwg_template)
{
  $selected = $pwg_template == pwg_get_session_var( 'multiview_theme', $user['template'].'/'.$user['theme'] ) ? 'selected="selected"' : '';
  $themes_html .=
    '<option value="'
    .$my_url.'?theme='.$pwg_template
    .'" '.$selected.'>'
    .$pwg_template
    .'</option>';
}
$themes_html .= '</select>';

$lang_html='Language: <select onchange="document.location = this.options[this.selectedIndex].value;">';
foreach (get_languages() as $language_code => $language_name)
{
  $selected = $language_code == pwg_get_session_var( 'multiview_lang', $user['language'] ) ? 'selected="selected"' : '';
  $lang_html .=
    '<option value="'
    .$my_url.'?lang='.$language_code
    .'" '.$selected.'>'
    .$language_name
    .'</option>';
}
$lang_html .= '</select>';
?>

<html>
<head>
<title>Controller</title>
</head>

<body>

<script type="text/javascript">
if (window.opener==null) {
  window.close();
  document.write("<h2>How did you get here ???</h2>");
}
</script>

View as:
<?php
  if ($view_as)
    echo '<a href="'.$my_url.'?view_admin">admin</a>';
  else
    echo '<a href="'.$my_url.'?view_guest">guest</a>';
?>

<br />
<?php echo $themes_html; ?>

<br />
<?php echo $lang_html; ?>


<script type="text/javascript">
<?php
  if ($refresh_main) echo '
window.opener.location = window.opener.location;';
?>
</script>

</body>

</html>