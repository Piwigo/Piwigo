<?php
if (! defined('MULTIVIEW_CONTROLLER') )
{
  if (pwg_get_session_var( 'purge_template', 0 ))
  {
    global $template;
    $template->delete_compiled_templates();
    FileCombiner::clear_combined_files();
    pwg_unset_session_var( 'purge_template' );
  }
}
?>
