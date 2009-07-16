<?
// atom.php
// displays atom feeds

$cat = $_GET['cat'];
$id = $_GET['id'];
$_REQUEST = array(NULL);

require_once('functions.php');

$query = sprintf("SELECT id,title,date,intro,section FROM %s ", "${db_prefix}data");

if ($id != "" && $cat == "comments" && $id != "0") {
	$query .= sprintf("WHERE section = 'comments'
				AND commentref = %d
				AND date <= "'.date($datefmt).'"
			ORDER BY date DESC", "$id");
	$type = $id.$section;
} else {
	if($cat != "") {
		$query .= sprintf("
		WHERE section LIKE '%%%s%%'
			AND date <= %s", "$cat", date($datefmt));
	$type = $cat;
	} else {
		$query .= sprintf("WHERE date <= %s", date($datefmt));
		$type = "all";
	}
	$query .= " ORDER BY date DESC ";
	if ($id >= 1) {
		$query .= sprintf("LIMIT %d", "$id"); $type .= $id;
	} else {
		$query .='LIMIT 20'; $type .= 20;
	}
}
if ($cache_time == "daily" || $cache_time > 60*60)
	$cache_time = 60*60;

$result = $db->fetch($query,$cache_time,"rss".$type);

$nl = array("\r\n","\n","\r");
$return = NULL;
$channel = NULL;

if ($link) {
	$show = '/';
} else {
	$show = '/show.php?id=';
}

$channel .= enclose('title',$sitename);
$channel .= enclo_s('link','href="'.$hurl.'"');
$channel .= enclose('subtitle',$meta_desc);
$channel .= enclose('updated',date('c'));
$channel .= enclo_s('atom:link',
	'href="'.$hurl.$_SERVER['REQUEST_URI']
	.'" rel="self" type="application/atom+xml"');
if ($result) {
	foreach($result as $line) {
		$item = NULL;
		if (preg_match("/\s?\@[a-z0-9]*?$/",$line['title']))
			list($line['title']) = get_name_md5($line['title']);
		/*
		 * The above is deprecated, but left here to prevent email
		 * addresses from appearing in the feed
		 */
		$item .= enclose('title',str_replace($nl,"",$line['title']));
		$item .= enclose('author',str_replace($nl,"",$line['section']));
		$item .= enclose('content',
				 fixup(str_replace($nl," ",$line['intro'])));
		$item .= enclose('updated',date('c',strtotime($line['date'])));
		if ($cat == "comments") {
			$perm = $id;
		} else {
			$perm = $line['id'];
		}
		$item .= enclo_s('link','href="'.$hurl.$show.$perm.'"');
		$items .= enclose('entry',$item);
	}
}

$channel .= $items;
$return = enclose('feed',$channel,
	'xmlns="http://www.w3.org/2005/Atom"');
header('Content-type: application/atom+xml');
echo $return;
?>
