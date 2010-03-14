<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Piwigo, {'Welcome'|@translate}</title>
{literal}
<style type="text/css">
body {
margin: 0;
padding: 0;
background-color:#111;
}

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

.bigButton {text-align:center; margin-top:130px;}

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
}
</style>
{/literal}

</head>

<body>
<div id="global">
<p id="noPhotoWelcome">{'Welcome to your Piwigo photo gallery!'|@translate}</p>
<div class="bigButton"><a href="{$next_step_url}">{'Add Photos'|@translate}</a></div>
</div>
</body>

</html>

