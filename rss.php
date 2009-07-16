<?
// rss.php
// displays rss feeds

$cat = $_GET['cat'];
$id = $_GET['id'];
$_REQUEST = array(NULL);

require_once('functions.php');

$query = sprintf("SELECT id,title,date,intro,section FROM %s ", "${db_prefix}data");

if ($id != "" && $cat == "comments" && $id != "0") {
	$query .= sprintf("WHERE section = 'comments'
				AND commentref = %d
				AND date <= %s
			ORDER BY date DESC", "$id", date($datefmt));
	$type = $id.$section;
} else {
	if($cat != "") {
		$query .= sprintf("
		WHERE section LIKE '%%%s%%'
			AND date <= '%s'", "$cat", date($datefmt));
	$type = $cat;
	} else {
		$query .= sprintf("WHERE date <= %s", date($datefmt));
		$type = "all";
	}
	$query .= " ORDER BY date DESC ";
	if ($id >= 1) {
		$query .= sprintf("LIMIT %d", "$id");
		$type .= $id;
	} else {
		$query .='LIMIT 20'; $type .= 20;
	}
}
if ($cache_time == "daily" || $cache_time > 60*60)
	$cache_time = 60*60;

$result = $db->fetch($query,$cache_time,"rss".$type);

$return = NULL;
$channel = NULL;

if ($link) {
	$show = '/';
} else {
	$show = '/show.php?id=';
}

$channel .= enclose('title',$sitename);
$channel .= enclose('link',$hurl);
$channel .= enclose('description',$meta_desc);
$channel .= enclose('language','en-gb');
$channel .= enclose('pubDate',date('r'));
$channel .= enclo_s('atom:link','href="'.$hurl.$_SERVER['REQUEST_URI'].'" rel="self" type="application/rss+xml"');
$nl = array("\r\n","\n","\r");
if ($result) {
	foreach($result as $line) {
		$item = NULL;
		if (preg_match("/\s?\@[a-z0-9]*?$/",$line['title']))
			list($line['title']) = get_name_md5($line['title']);
		$item .= enclose('title',str_replace($nl,"",$line['title']));
		$item .= enclose('author',str_replace($nl,"",$line['section']));
		$item .= enclose('description',fixup(str_replace($nl," ",$line['intro'])));
		$item .= enclose('pubDate',date('r',strtotime($line['date'])));
		if ($cat == "comments") {
			$perm = $id;
		} else {
			$perm = $line['id'];
		}
		$item .= enclose('link',$hurl.$show.$perm);
		$items .= enclose('item',$item);
	}
}

$channel = enclose('channel',$channel.$items);
$return = enclose('rss',$channel,'version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"');
header('Content-type: application/rss+xml');
echo $return;
?>
