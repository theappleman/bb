<?
// atom.php
// displays atom feeds

$cat = $_GET['cat'];
$id = $_GET['id'];
$_REQUEST = array(NULL);

require_once('functions.php');

$query = 'SELECT id,title,date,intro,section FROM '.$db_prefix.'data ';

if ($id != "" && $cat == "comments" && $id != "0") {
	$query .= 'WHERE section = "comments"
				AND commentref = "'.$id.'"
				AND date <= "'.date($datefmt).'"
			ORDER BY date DESC';
	$type = $id.$section;
} else {
	if($cat != "") {
		$query .= '
		WHERE section LIKE "%'.$cat.'%"
			AND date <= "'.date($datefmt).'"';
	$type = $cat;
	} else {
		$query .= 'WHERE date <= "'.date($datefmt).'"';
		$type = "all";
	}
	$query .= " ORDER BY date DESC ";
	if ($id >= 1) {
		$query .='LIMIT '.$id; $type .= $id;
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
$channel .= e_nclos('link','href="'.$hurl.'"');
$channel .= enclose('description',$meta_desc);
$channel .= enclose('language','en-gb');
$channel .= enclose('updated',date('r'));
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
		$item .= enclose('summary',
				 fixup(str_replace($nl," ",$line['intro'])));
		$item .= enclose('updated',date('r',strtotime($line['date'])));
		if ($cat == "comments") {
			$perm = $id;
		} else {
			$perm = $line['id'];
		}
		$item .= e_nclos('link','href="'.$hurl.$show.$perm.'"');
		$items .= enclose('enntry',$item);
	}
}

$channel .= $items;
$return = enclose('feed',$channel,
	'version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"');
header('Content-type: application/atom+xml');
echo $return;
?>
