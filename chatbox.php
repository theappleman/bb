<?
// chatbox.php
// view the chatbox and post

require_once('functions.php');

$cat = $_REQUEST['cat'];
$id = $_REQUEST['id'];
$page = $_REQUEST['page'];
$_REQUEST = array(NULL);

if ($id == "") { $id = "10"; }
if ($page != "") { $id = $page*$id . ', ' . $id; }
$query = 'SELECT id,title,date,intro,commentable,rateable,rating,image
	FROM '.$db_prefix.'data
	WHERE section = "'.$cat.'"
		AND moderated != 1
		AND date <= "'.date($datefmt).'"
		AND rating >= -50
	ORDER BY sticky ASC,lastupd DESC, date DESC ';
if ($id != "0") { $query .= ' LIMIT '.$id; }

$result = mysql_query($query);

$return = NULL;$body = NULL;$head = NULL;
$head .= enclose("title",$sitename.' '. $cat,"");
$head .= head($cat,$id);

$body .= enclose('div',$sitename,'id="head"');

while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$loop = NULL;
	$foot = NULL;
	$comments = NULL;
	$rate = NULL;

	$loop .= enclose('div',get_day($line['date']),'class="bigdate"');
	$loop .= enclose('div',enclose('a',html_entity_decode($line['title']),'href="'.$hurl.'/show/'.$line['id'].'"'),'class="title"');
	$loop .= enclose('div',$line['date'],'class="date"');
	if ($line['rateable'] != 1) { $loop .= enclose('div',chrate($line['id']),'class="rate"'); }
  $line['intro'] .= show_pic($line['image']);
	$loop .= enclose('div',nl2br(html_entity_decode($line['intro'])),'class="text"');
	if ($line['commentable'] >= 1) {
		if (comments($line['id']) != 1) { $comment = 's'; } else { $comment = NULL; }
		$foot .= enclose('a',comments($line['id']). ' comment'.$comment,'href="'.$hurl.'/show/'.$line['id'].'"');
	}
	$loop .= enclose('div',$foot,'class="foot"');
if (comments($line['id']) >= 1) {
	$query2 = 'SELECT id,title,date,intro,rateable,rating,image
		FROM '.$db_prefix.'data
		WHERE moderated != 1
			AND date <= "'.date($datefmt).'"
			AND commentref="'.$line['id'].'"
			AND rating >= -50
		ORDER BY sticky ASC, date DESC
		LIMIT 1';
	$result2 = mysql_query($query2);
	while ($line2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
		$nloop = NULL;
		$foot = NULL;
		$rate = NULL;
		$nloop .= enclose('div',get_day($line2['date']),'class="bigdate"');
		$nloop .= enclose('div',enclose('a',html_entity_decode($line2['title']),'href="'.$hurl.'/show/'.$line['id'].'"'),'class="title"');
		$nloop .= enclose('div',$line2['date'],'class="date"');
		if ($line2['rateable'] != 1) { $nloop .= enclose('div',chrate($line2['id']),'class="rate"'); }
    $line2['intro'] .= show_pic($line2['image']);
		$nloop .= enclose('div',nl2br(html_entity_decode($line2['intro'])),'class="text"');
    if ($line['commentable'] == 2) { $nloop .= enclose('div',enclose('a','Post a comment','href="'.$hurl.'/show/'.$line['id'].'"'),'class="foot"'); }
    else { $nloop .= enclose('div',enclose('a','No more comments','href="'.$hurl.'/show/'.$line['id'].'"'),'class="foot"'); }
		$comments .= enclose('div',$nloop,'class="entry"');
	}
	$loop .= enclose('div',$comments,'id="comments"');
}
	$body .= enclose('div',$loop,'class="entry"');
}
if ( !in_array($cat, $nochat) ) { $body .= postbox($cat,0); }
$return = finish_up($head,$body);
//$return = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . $return;
echo $return;
?>
