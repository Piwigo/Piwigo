/* page */
body {
  color:#fff;
}

html, body, #bodyTable {
  background:#111;
}
#contentTable {
  width:600px;
}

/* main block */
#header {
  background:#444;
  background-image:radial-gradient(ellipse at center, #555, #333);
  border:1px solid #000;
  border-top:4px solid #f36;
  text-align:center;
  text-shadow:1px 1px 0px #000;
}
#header #title {
  color:#eee;
}
#header #subtitle {
  color:#C9224C;
}
#content {
  background:#111;
  border-width:1px;
  border-style:solid;
  border-color:#666 #000;
  box-shadow:inset 0 0 20px #333;
}
#footer {
  background:#333;
  border:1px solid #000;
  border-bottom:2px solid #f36;
}

/* links */
a {
  color:#f36;
  text-decoration:none;
}
a:hover {
  text-decoration:underline;
}

/* images */
img.photo {
  border:10px solid #666;
}
img.photo:hover {
  border-color:#999;
}

h1, h2, h3, h4, h5 {
  color:#bbb;
}

/* paragraphs */
blockquote {
  border-left:2px solid #aaa;
  border-radius:2px;
}

/* tables */
#content table td {
  border-bottom:1px solid #999;
}
#content table th {
  background:#666;
  border-right:1px solid #aaa;
}
#content table tfoot td {
  background:#444;
  color:#aaa;
  border-right:1px solid #aaa;
}

/* line */
hr {
  border-color:#555;
}
