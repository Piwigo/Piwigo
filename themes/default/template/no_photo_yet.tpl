<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="themes/Sylvia/theme.css">
<title>Piwigo, {'Welcome'|@translate}</title>
{literal}
<style type="text/css">
body {
margin: 0;
padding: 0;
background-color:#111;
}

P {text-align:center;}
TD {color:#888;}

#global {
position:absolute;
left: 50%;
top: 50%;
width: 700px;
height: 400px;
margin-top: -200px; /* height half */
margin-left: -350px; /* width half */

background-color: #eee;
background: #222222;
border:2px solid #FF3363;
}

#noPhotoWelcome {font-size:25px; color:#888;text-align:center; letter-spacing:1px; margin-top:30px;}
.bigButton {}

.bigButton {text-align:center; margin-top:120px;}

.bigButton a {
    background-color:#333;
    padding:10px;
    text-decoration:none;
    margin:0px 5px 0px 5px;
    -moz-border-radius:6px;
    -webkit-border-radius:6px;
    color:#ff7700;
    font-size:25px;
    letter-spacing:2px;
    padding:20px;
}

.bigButton a:hover {
    background-color:#444;
    outline:none;
    color:#ff3333;
    border:none;
}

#connectionBox {
    margin:0 auto;
    margin-top:70px;
}

#deactivate {
    position:absolute;
    bottom:10px;
    text-align:center;
    width:100%;

    font-style:normal;
}

#deactivate A {
    text-decoration:none;
    border:none;
}
</style>
{/literal}

</head>

<body>
<div id="global">

{if $step == 1}
<p id="noPhotoWelcome">{'Welcome to your Piwigo photo gallery!'|@translate}</p>

<form method="post" action="{$U_LOGIN}" id="quickconnect">
<table id="connectionBox">
  <tr>
    <td>{'Username'|@translate}</td>
    <td><input type="text" name="username"></td>
  </tr>
  <tr>
    <td>{'Password'|@translate}</td>
    <td><input type="text" name="password"></td>
  </tr>
</table>

<p><input class="submit" type="submit" name="login" value="{'Login'|@translate}"></p>
</form>


{else}
<p id="noPhotoWelcome">{$intro}</p>
<div class="bigButton"><a href="{$next_step_url}">{'I want to add photos'|@translate}</a></div>
<div id="deactivate"><a href="{$deactivate_url}">{'I will find my way by myself, please deactivate this message'|@translate}</a></div>
{/if}


</div>
</body>

</html>

