<?php include 'userconf.php'; ?>
html {
	font-family: courier;
	margin:0;
	padding:0;
}
body {
	background-color: #000000;
	font-size: 16;
	background-repeat: no-repeat;
	background-attachment: fixed;
	background-position: top center;
	color: #00ff00;
}
div#head {
	text-align: center;
	font-size: 56;
	font-weight: bold;
	font-variant: small-caps;
	color: #ffffff;
}
a {
	color: #ffff00;
	text-decoration: none;
}
a:hover {
	color: #000000;
	text-decoration: none;
	background-color: #ffff00;
  cursor: crosshair;
}
div.mainmenu {
	#margin-right: 50px;
	font-variant: small-caps;
	text-align: center;
	clear: both;
}
div#content {
	margin: 10px;
}
div.entry {
	background-color: #000000;
	clear: both;
	margin-bottom: 10px;
	border-bottom: solid thin #555555;
}
div.bigdate {
	font-size: 36;
	padding: 5px;
	float: left;
}
div.image {
	float: left;
}
div.title {
	font-size: 24;
	font-variant: small-caps;
	display: inline;
}
div.date {
	display: inline;
}
div.text {
	text-align:right;
	text-indent: 1em;
}
div.foot {
	text-align: center;
	letter-spacing: 2;
	font-variant: small-caps;
	word-spacing: 2;
	clear: both;
	font-size: 8;
}
div#comments {
	margin-left: 25px;
}
textarea {
	margin-left: 5%;
	margin-right: 5%;
	width: 90%;
}
p.name {
	text-align: center;
	margin-bottom: 0px
}
div.rate {
	display: inline;
}

.highslide {
  outline: none;
}
.highslide img {
	border: 2px solid gray;
}
.highslide-active-anchor img {
	visibility: hidden;
}
.highslide:hover img {
	border: 2px solid white;
}
.highslide-wrapper {
	background: black;
}
.highslide-image {
	border: 5px solid #444444;
}
.highslide-image-blur {
}
.highslide-caption {
  display: none;
  font-family: Verdana, Helvetica;
  font-size: 10pt;
  border: 5px solid #444444;
  border-top: none;
  padding: 5px;
  background-color: gray;
}
.highslide-loading {
  display: block;
  color: white;
	font-size: 9px;
	font-weight: bold;
	text-transform: uppercase;
  text-decoration: none;
	padding: 3px;
	border-top: 1px solid white;
	border-bottom: 1px solid white;
  background-color: black;
}
a.highslide-credits,
a.highslide-credits i {
  padding: 2px;
  color: silver;
  text-decoration: none;
	font-size: 10px;
}
a.highslide-credits:hover,
a.highslide-credits:hover i {
  color: white;
  background-color: gray;
}
a.highslide-full-expand {
	background: url(<? echo $hurl; ?>/graphics/fullexpand.gif) no-repeat;
	display: block;
	margin: 0 10px 10px 0;
	width: 34px;
	height: 34px;
}
.highslide-display-block {
  display: block;
}
.highslide-display-none {
  display: none;
}
