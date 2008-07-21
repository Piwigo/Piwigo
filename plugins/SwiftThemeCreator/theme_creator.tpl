{* $Id$ *}
<div class="titrePage">
  <h2>{'Swift Theme Creator'|@translate}</h2>
</div>

<p>
{'The Swift Theme Creator is powerful webmaster tool to create a new theme in few seconds.
You can update later the result as you need.'|@translate}
</p>
<form method="post" action="" class="general">

<fieldset>
	<legend>{'Template selection '|@translate}</legend>
<table><tr><td style="padding-right:20px;">
  {'Template'|@translate}
</td><td>
  {html_options name=template options=$template_options selected=$main.template_options}
</td></tr>
<tr><td style="padding-right:20px;">
  {'New theme to be created'|@translate}
</td><td>
  <input type="text" maxlength="8" size="8" name="new_theme" id="new_theme" value="{$main.new_theme}" />
</td></tr>
</table>
</fieldset>

<script type="text/javascript">
  $(document).ready(function() {ldelim}
    $('#demo').hide();
    var f = $.farbtastic('#picker');
    var p = $('#picker').css('opacity', 1);
    var selected;
    $('.colorwell')
      .each(function () {ldelim} f.linkTo(this); $(this).css('opacity', 0.50); })
      .focus(function() {ldelim}
        if (selected) {ldelim}
          $(selected).css('opacity', 0.90).removeClass('colorwell-selected');
        }
        f.linkTo(this);
        p.css('opacity', 1);
        $(selected = this).css('opacity', 1).addClass('colorwell-selected');
      });
  });
</script>

<fieldset>
	<legend>{'Colours selection '|@translate}</legend>
<table>
<tr><td style="padding-right:20px;">
  {'Main background'|@translate}  
</td><td>
  <input type="text" id="color1" name="color1" class="colorwell" value="{$main.color1}" />
</td><td rowspan="5" style="padding-left:80px;">
<div id="picker" style="float: right;"></div>
</td>
</tr><tr><td style="padding-right:20px;">
  {'Default text'|@translate}
</td><td>
  <input type="text" id="color2" name="color2" class="colorwell" value="{$main.color2}" />
</td></tr><tr><td style="padding-right:20px;">
  {'Internal links'|@translate}
</td><td>
  <input type="text" id="color3" name="color3" class="colorwell" value="{$main.color3}" />
</td></tr><tr><td style="padding-right:20px;">
	{'Hover links'|@translate}
</td><td>
  <input type="text" id="color4" name="color4" class="colorwell" value="{$main.color4}" />
</td></tr><tr><td style="padding-right:20px;">
  {'External links'|@translate}
</td><td>
  <input type="text" id="color5" name="color5" class="colorwell" value="{$main.color5}" />
</td></tr>
</table>
</fieldset>

<fieldset>
	<legend>{'Header background selection '|@translate}</legend>
<table>
  <tr>
    <td style="padding-right:20px;">
      {'Use of a picture background'|@translate} {* No / 24H Random public picture / Fixed RRL *}
    </td>
    
    <td colspan="2">
    <label>
    <input class="radio" type="radio" value="off" name="background"
    {if ($main.background == 'off')}
      checked="checked"
    {/if}/>
    {$background_options.off}
    </label>
    </td>
  </tr>

  <tr>
    <td style="padding-right:20px;">
      {'Source category'|@translate}
    </td><td>
    <label>
    <input class="radio" type="radio" value="random" name="background"
    {if ($main.background == 'random')}
      checked="checked"
    {/if}/>
    {$background_options.random}
    </label>
    </td><td>
      {html_options style="margin: 0 0 0 10px;" name=src_category options=$src_category select=$main.src_category}
    </td>
  </tr>

  <tr>
    <td style="padding-right:20px;">
      {'Picture address (URL)'|@translate}
    </td><td>
    <label>
    <input class="radio" type="radio" value="fixed" name="background"
    {if ($main.background == 'fixed')}
      checked="checked"
    {/if}/>
    {$background_options.fixed}
    </label>
    </td><td>
      <input style="margin: 0 0 0 10px;" type="text" maxlength="255" size="70" name="picture_url" id="picture_url" value="{$main.picture_url}" />
    </td>
  </tr>

  <tr>
    <td style="padding-right:20px;">
      {'Width limit in pixels'|@translate} 
    </td><td colspan="2">
      <input style="margin: 0 10px 0 50px;" type="text" maxlength="5" size="8" name="picture_width" id="picture_width" value="{$main.picture_width}" />
     x 
      <input style="margin: 0 50px 0 10px;" type="text" maxlength="5" size="8" name="picture_height" id="picture_height" value="{$main.picture_height}" />
      {'(Height limit in pixels)'|@translate} 
    </td>
  </tr>

  <tr>
    <td style="padding-right:20px;">
      {'Display mode'|@translate} {* As is / truncated / resized *}
    </td><td colspan="2">
      {html_radios name='background_mode' class="radio" options=$background_mode_options selected=$main.background_mode}
    </td>
  </tr>
</table>
</fieldset>

<p><input name="submit" class="submit" type="submit" value="{'Submit'|@translate}" /></p>
</form>