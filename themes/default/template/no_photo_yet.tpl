<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="themes/default/theme.css">
<title>Piwigo, {'Welcome'|@translate}</title>
{literal}
<style type="text/css">
body {
margin: 0;
padding: 0;
background-color:#f9f9f9;
}

P {text-align:center;}
TD {color:#888; letter-spacing:1px;}

#global {
position:absolute;
left: 50%;
top: 50%;
width: 700px;
height: 400px;
margin-top: -200px; /* height half */
margin-left: -350px; /* width half */

background-color: #f1f1f1;
border:2px solid #dddddd;
}

#noPhotoWelcome {font-size:25px; color:#555;text-align:center; letter-spacing:1px; margin-top:30px;}
.bigButton {}

.bigButton {text-align:center; margin-top:120px;}

.bigButton a {
    background-color:#666;
    padding:10px;
    text-decoration:none;
    margin:0px 5px 0px 5px;
    -moz-border-radius:6px;
    -webkit-border-radius:6px;
    color:#fff;
    font-size:25px;
    letter-spacing:2px;
    padding:20px;
}

.bigButton a:hover {
    background-color:#ff7700;
    outline:none;
    color:#fff;
    border:none;
}

#deactivate {
    position:absolute;
    bottom:10px;
    text-align:center;
    width:100%;

    font-style:normal;
    font-size:1.0em;
}

.submit {font-size:1.0em; letter-spacing:2px; font-weight:normal;}

#deactivate A {
    text-decoration:none;
    border:none;
    color:#f70;
}

#deactivate A:hover {
  border-bottom:1px dotted #f70;
}

#quickconnect {
    margin:0 auto;
    margin-top:60px;
    width:300px;
    color:#555;
    font-size:14px;
    letter-spacing:1px;
}

#quickconnect input[type="text"], #quickconnect input[type="password"] {
  width:300px;
  color:#555;
  font-size:20px;
  margin-top:3px;
  background-color:#ddd;
  border:2px solid #ccc;
  -moz-border-radius:5px;
  padding:2px;

}

#quickconnect input[type="text"]:focus, #quickconnect input[type="password"]:focus {
  background-color:#fff;
  border:2px solid #ff7700;
}

#quickconnect input[type="submit"] {
  font-size:14px;
  font-weight:bold;
  letter-spacing:2px;
  border:none;
  background-color:#666666;
  color:#fff;
  padding:5px;
  -moz-border-radius:5px;
}

#quickconnect input[type="submit"]:hover {
  background-color:#ff7700;
  color:white;
}
</style>
{/literal}

</head>

<body>
<div id="global">

{if $step == 1}
<p id="noPhotoWelcome">{'Welcome to your Piwigo photo gallery!'|@translate}</p>

<form method="post" action="{$U_LOGIN}" id="quickconnect">
{'Username'|@translate}
<br><input type="text" name="username">
<br>
<br>{'Password'|@translate}
<br><input type="password" name="password">

<p><input class="submit" type="submit" name="login" value="{'Login'|@translate}"></p>

</form>
<div id="deactivate"><a href="{$deactivate_url}">{'... or browse your empty gallery'|@translate}</a></div>


{else}
<p id="noPhotoWelcome">{$intro}</p>
<div class="bigButton"><a href="{$next_step_url}">{'I want to add photos'|@translate}</a></div>
<div id="deactivate"><a href="{$deactivate_url}">{'... or please deactivate this message, I will find my way by myself'|@translate}</a></div>
{/if}


</div>
</body>

</html>

