<?
// show.php
// show a post

require_once('functions.php');

$id = $_REQUEST['id'];
$_REQUEST = array(NULL);

$query = 'SELECT title,date,intro,commentable,image,ip,useragent
	FROM '.$db_prefix.'data
	WHERE id ="' . $id . '"
	LIMIT 1';
$result = $db->fetch($query,$cache_time,$id);
$query2 = 'SELECT id,title,date,intro,image,ip,useragent
	FROM '.$db_prefix.'data
	WHERE moderated != 1
		AND commentref="'.$id.'"
	ORDER BY date ASC';
$result2 = $db->fetch($query2,$cache_time,$id."com");
$com_num = 0;

$return = NULL;$head = NULL;$body = NULL;$comments = NULL;$bot = NULL;$box = NULL;$script=NULL;
$head .= enclose('title',$sitename,'');
$head .= head("comments",$id);

$body .= enclose('div',$sitename,'id="head"');
if ($result) {
  foreach($result as $line) {
  $entry .= enclose('div',get_day($line['date']),'class="bigdate"');
  $entry .= enclose('div',enclose('a',html_entity_decode($line['title']),'href="'.$hurl.'/show/'.$id.'"'),'class="title"');
  $entry .= enclose('div',fixup(show_pic($line['image'])),'class="image"');
  $entry .= enclose('div',$line['date'],'class="date"');
  $entry .= enclose('div',nl2br(fixup(html_entity_decode($line['intro']))),'class="text"');
  if ($line['commentable'] >= 1) {
    if (comments($id) != 1) { $comment = 's'; } else { $comment = NULL; }
    $foot .= enclose('a',comments($id). ' comment'.$comment,'href="'.$hurl.'/show/'.$id.'"');
  }
  if ( check_edit($line['ip'],$line['userconf']) ) { $edit = enclose('a','edit','href="'.$hurl.'/e/'.$id.'#edit" onclick="return hs.htmlExpand(this, { objectType: \'ajax\'} )"'); } else { $edit = '&nbsp;'; }
  $entry .= enclose('div',$edit,'class="foot"');
  $body .= enclose('div',$entry,'class="entry"');

  if ($line['commentable'] >= 1) {
    if ($result2) { 
    	foreach($result2 as $line2) {
      		$loop = NULL;
      		$foot = NULL;
                $rate = NULL;
                $com_num += 1;
                $loop .= enclose('div',get_day($line2['date']),'class="bigdate"');
                $loop .= enclose('div',enclose('a',html_entity_decode($line2['title']),''),'class="title"');
                $loop .= enclose('div',$line2['date'],'class="date"');
                $loop .= enclose('div',fixup(show_pic($line2['image'])),'class="image"');
                $loop .= enclose('div',nl2br(fixup(html_entity_decode($line2['intro']))),'class="text"');
		if ( check_edit($line2['ip'],$line2['userconf']) ) { $edit = enclose('a','edit','href="'.$hurl.'/e/'.$line2['id'].'#edit" onclick="return hs.htmlExpand(this, { objectType: \'ajax\'} )"'); } else { $edit = '&nbsp;'; }
                $loop .= enclose('div',$edit,'class="foot"');
                $comments .= enclose('div',$loop,'class="entry"');
	}
    	}
    $body .= enclose('div',$comments,'id="comments"');
    $bot .= enclose('div',$com_num,'class="bigdate"');
    if ($com_num != 1) { $pl = 's'; } else { $pl = NULL; }
    $bot .= enclose('div','comment'.$pl,'class="title"');
    $bot .= enclose('div',enclose('a','Comments through RSS feed','href="'.$hurl.'/rss/comments/'.$id.'"'),'class="text"');
    if ($line['commentable'] == 2) { 
	$bot .= enclose('div',enclose('a','Post a comment','href="'.$hurl.'/p/comments/'.$id.'#postbox" onclick="return hs.htmlExpand(this, { objectType: \'ajax\'} )"'),'class="foot"'); 
    }
    else { $bot .= enclose('div',enclose('a','No more comments',''),'class="foot"'); }
    $body .= enclose('div',$bot,'class="entry"');
  }
  }
}
$return = finish_up($head,$body);
//$return = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . $return;
echo $return;
?>
