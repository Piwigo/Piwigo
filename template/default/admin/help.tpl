<table style="width:100%;">
  <!-- BEGIN cat -->
  <tr class="admin">
    <th >{cat.NAME}</th>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
      <!-- BEGIN illustration -->
      <div style="text-align:center;margin:auto;margin-bottom:10px;"><img src="{cat.illustration.SRC_IMG}" style="border:1px solid black;" alt=""/></div>
      {cat.illustration.CAPTION}
      <!-- END illustration -->
      <ul style="text-align:left; margin-right:10px;">
        <!-- BEGIN item -->
        <li style="margin-bottom:5px;">{cat.item.CONTENT}</li>
        <!-- END item -->
      </ul>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <!-- END cat -->
</table>
