{* $Id: /piwigo/trunk/plugins/SwiftThemeCreator/theme_creator.tpl 6327 2008-08-26T14:18:42.525002Z vdigital  $ *}
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
  {html_options name=template options=$template_options selected=$main.template_sel}
</td></tr>
<tr><td style="padding-right:20px;">
  {'New theme to be created'|@translate}
</td><td>
  <input type="text" maxlength="8" size="8" name="new_theme" id="new_theme" value="{$main.newtheme}" />
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
    <td style="padding-right:120px;">
    <label><input class="radio" type="radio" value="off" name="background"
    {if ($main.background == 'off')} checked="checked" {/if}/>
      {'No picture'|@translate}</label> {* No / 24H Random public picture / Fixed URL *}
    </td>
    <td colspan="2"></td>
  </tr>

  <tr>
    <td style="padding-right:120px;">
    <label>    <input class="radio" type="radio" value="random" name="background"
    {if ($main.background == 'random')} checked="checked" {/if}/>
      {'Random source'|@translate}</label>
    </td>
    <td colspan="2">
      {html_options style="margin: 0 0 0 10px;" name=src_category options=$src_category selected=$main.src_category}
    </td>
  </tr>


  <tr>
    <td style="padding-right:120px;">
    <label>    <input class="radio" type="radio" value="fixed" name="background"
    {if ($main.background == 'fixed')} checked="checked" {/if}/>
      {'Fixed path'|@translate}</label>
    </td>
    <td colspan="2">
      <input style="margin: 0 0 0 10px;" type="text" maxlength="255" size="70" name="picture_url" id="picture_url" value="{$main.picture_url}" />
    </td>
  </tr>

  <tr>
    <td style="padding-right:20px;">
    &nbsp;
    </td><td colspan="2"  style="padding-left:20px;">
      <label style="padding-right:50px;">
        <input type="checkbox" name="colorize" {if ($main.colorize)}checked="checked"{/if} />
        {'Colorize'|@translate} {* Colorize / Enhance Brightness / Reduce contrast *}
      </label>
      <label style="padding-right:50px;">
        <input type="checkbox" name="brightness" {if ($main.brightness)}checked="checked"{/if} />
        {'Enhance brightness'|@translate}
      </label>
      <label>
        <input type="checkbox" name="contrast" {if ($main.contrast)}checked="checked"{/if} />
        {'Reduce contrast'|@translate}
      </label>
    </td>
  </tr>
  
  <tr>
    <td style="padding-right:5px; text-align: right;">
      {'Width limit in pixels'|@translate} 
    </td><td colspan="2">
      <input style="margin: 0 10px 0 50px;" type="text" maxlength="5" size="8" name="picture_width" id="picture_width" value="{$main.picture_width}" />
     x 
      <input style="margin: 0 50px 0 10px;" type="text" maxlength="5" size="8" name="picture_height" id="picture_height" value="{$main.picture_height}" />
      {'(Height limit in pixels)'|@translate} 
    </td>
  </tr>

  <tr>
    <td style="padding-right:5px; text-align: right;">
      {'Display mode'|@translate} {* As is / truncated / resized *}
    </td><td colspan="2">
      {html_radios name='background_mode' class="radio" options=$background_mode_options selected=$main.background_mode}
    </td>
  </tr>
</table>
</fieldset>

<p>
<input name="reset" class="submit" type="submit" value="{'Reset'|@translate}" style="margin-right:250px;" /> &nbsp; 
<input name="submit" class="submit" type="submit" value="{'Generate'|@translate}" style="margin-right:50px;" /> &nbsp;
<input name="simulate" class="submit" type="submit" value="{'Simulate'|@translate}" />
</p>
</form>
<fieldset>
	<legend>{'Just a yoga preview... '|@translate}</legend>
<div style="background-color:{$main.color1}; width: 600px; overflow: hidden; margin: 5px auto 5px auto;
font-family:Univers,Helvetica,Optima,'Bitstream Vera Sans',sans-serif;">
  {* Preview header *}<div style="width: 600px; height: 80px; overflow: hidden;">
    {if ($main.background=='fixed')}
    <img src="{$main.templatedir}/header.jpg">
    <h2 style="position:relative;color:{$main.color2};top:-75px;left:5px;text-align:left;margin:0 auto 0 auto;">{'"Fixed header" preview'|@translate}</h2>
    {/if}
    {if ($main.background=='random')}
    <img src="{$main.templatedir}/header.jpg">
    <h2 style="position:relative;color:{$main.color2};top:-75px;left:5px;text-align:left;margin:0 auto 0 auto;">{'"Random header" preview'|@translate}</h2>
    {/if}
    {if ($main.background=='off')}
    <h2 style="position:relative;color:{$main.color2};top:25px;left:5px;text-align:left;margin:0 auto 0 auto;">{'"No header" preview'|@translate}</h2>
    {/if}
    </div>
  {* Preview menubar *}<div style="border:1px solid {$main.color3}; margin:2px 4px 0px 2px;
text-decoration:none; width: 120px; display:inline; background-color:{$main.color6};
float:left; padding:0pt; text-align:left; color:{$main.color2}; font-size:1em;">
    <dl style="margin: 0;">
      <dt style="background-image:url({$main.templatedir}/stc.png); text-align: center; height:18px;
font-weight:bold; font-style:normal; color:{$main.color3}; padding: 6px 0 0 0;">{'Preview'|@translate}</dt>
      <dd>
        <ul style="padding-left: 14px; font-size: 0.9em;">
          <li><a style="color:{$main.color3}; border:0;">{'Preview'|@translate}</a></li>
          <li>...</li>
        </ul>
        <p style="font-size: 0.9em;">{'Preview'|@translate}</p>
      </dd>
    </dl>
  </div>
  {* Preview content *}<div style="border:1px solid {$main.color3}; padding:0px;
background-color:{$main.color6}; margin: 2px 2px 0 132px; color:{$main.color2}; font-size:1em; width:462px;">
<h2 style="background-image:url({$main.templatedir}/stc.png); display:block; padding: 6px 0 0 20px;
font-size:1em; height:18px; letter-spacing:-1px; margin:0; position:relative; text-align: left;
font-style:normal; color:{$main.color2}; width: 442px; font-weight: normal;">{'home'|@translate}</h2>
  <ul style="padding-left:4px; padding-right:130px;">
    <li style="list-style:none;">
      <div style="border:1px solid {$main.color3}; margin:3px; padding:2px 0px 0px 2px;">
        <div style="width:114px; float:left; margin:2px 0pt 0pt 2px; text-align:left; color:{$main.color1};">
          <a style="border:0;">
          <img title="{'Preview'|@translate}" alt="{'Preview'|@translate}" src="{$main.templatedir}/header.jpg" style="width: 106px; height: 80px;"/>
          </a>
        </div>
        <div style="height:94px; font-size:90%; overflow:hidden;">
          <h3 style="background-image:url({$main.templatedir}/stc.png); text-align:center; height: 14px; margin-right: 0px; margin-bottom: 0px; line-height: 7px;">
          <a style="font-size: 10px; font-style:normal; border:0;">{'Preview'|@translate}</a>
          </h3>
          <p style="">{'Preview'|@translate}</p>
          <p/>
        </div>
      </div>
    </li>
  </ul>
  </div>
</div>
<span style='display:block;font-style:italic;color:#777;text-align:right;margin-right:25px;'>
{'...to get an idea of the expected result. Preview is based on yoga-like template only.'|@translate}</span>
</fieldset>