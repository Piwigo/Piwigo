<?php
define('MULTIVIEW_CONTROLLER', 1);
define('PHPWG_ROOT_PATH','../../');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

if (!is_admin() or !function_exists('multiview_user_init') )
{
  pwg_unset_session_var( 'multiview_as' );
  pwg_unset_session_var( 'multiview_theme' );
  pwg_unset_session_var( 'multiview_lang' );
  pwg_unset_session_var( 'multiview_show_queries' );
  pwg_unset_session_var( 'multiview_debug_l10n' );
?>

<script type="text/javascript">
  window.close();
</script>
<?php
  exit();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<?php

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

if ( isset($_GET['show_queries']) )
{
  if ( $_GET['show_queries']> 0 )
    pwg_set_session_var( 'multiview_show_queries', 1 );
  else
    pwg_unset_session_var( 'multiview_show_queries' );
  $refresh_main = true;
}

if ( isset($_GET['debug_l10n']) )
{
  if ( $_GET['debug_l10n']>0 )
    pwg_set_session_var( 'multiview_debug_l10n', 1 );
  else
    pwg_unset_session_var( 'multiview_debug_l10n' );
  $refresh_main = true;
}

$my_url = get_root_url().'plugins/'.basename(dirname(__FILE__)).'/'.basename(__FILE__);
$my_template = '';

$themes_html='Theme: <select onchange="document.location = this.options[this.selectedIndex].value;">';
foreach (get_pwg_themes() as $pwg_template)
{
  $selected = $pwg_template == pwg_get_session_var( 'multiview_theme', $user['template'].'/'.$user['theme'] ) ? 'selected="selected"' : '';
  $my_template = $selected == '' ? $my_template : $user['template'].'/theme/'.$user['theme'];
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

$show_queries_html='';
if (!$conf['show_queries'])
{
  $show_queries_html = '<br/>';
  if ( !pwg_get_session_var( 'multiview_show_queries', 0 ) )
    $show_queries_html.='<a href="'.$my_url.'?show_queries=1">Show SQL queries</a>';
  else
    $show_queries_html.='<a href="'.$my_url.'?show_queries=0">Hide SQL queries</a>';
}

$debug_l10n_html='';
if (!$conf['show_queries'])
{
  $debug_l10n_html = '<br/>';
  if ( !pwg_get_session_var( 'multiview_debug_l10n', 0 ) )
    $debug_l10n_html.='<a href="'.$my_url.'?debug_l10n=1">Debug language</a>';
  else
    $debug_l10n_html.='<a href="'.$my_url.'?debug_l10n=0">Revert debug language</a>';
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_pwg_charset() ?>">
<title>Controller</title>
<?php
// Controller will be displayed  with  the **real admin template** (without Any if it has been removed)
if ( $my_template !== '') {
  $my_template = get_root_url().'template/'.$my_template.'/theme.css';
  echo '<link rel="stylesheet" type="text/css" href="' . $my_template .'">';
}
?>

</head>
<body>
<div>
<script type="text/javascript">
if (window.opener==null) {
  window.close();
  document.write("<"+"h2>How did you get here ???<"+"/h2>");
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

<?php echo $show_queries_html; ?>
<?php echo $debug_l10n_html; ?>

<script type="text/javascript">
<?php
  if ($refresh_main) echo '
window.opener.location = window.opener.location;';
?>
</script>
</div>
</body>
</html>
