<!-- DEV TAG: not smarty migrated -->
<h2>{TITLE}</h2>

<!-- BEGIN groups -->
<fieldset>
  <legend>{lang:Categories authorized thanks to group associations}</legend>

  <ul>
    <!-- BEGIN category -->
    <li>{groups.category.NAME}</li>
    <!-- END category -->
  </ul>
</fieldset>
<!-- END groups -->

<fieldset>
  <legend>{lang:Other private categories}</legend>

  <form method="post" action="{F_ACTION}">
    {DOUBLE_SELECT}
  </form>
</fieldset>
