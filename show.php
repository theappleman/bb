<?
// show.php
// show a post

require_once('functions.php');

$id = $_REQUEST['id'];
$_REQUEST = array(NULL);

$query = 'SELECT title,date,intro,commentable,image
	FROM '.$db_prefix.'data
	WHERE id ="' . $id . '"
	LIMIT 1';
$result = $db->fetch($query,$cache_time,$id);
$query2 = 'SELECT id,title,date,intro,image
	FROM '.$db_prefix.'data
	WHERE commentref="'.$id.'"
	ORDER BY date ASC';
$result2 = $db->fetch($query2,$cache_time,$id."com");
$com_num = 0;

$return = NULL;$head = NULL;$body = NULL;$comments = NULL;$bot = NULL;$box = NULL;$script=NULL;
$head .= enclose('title',$sitename,'');
$head .= head("comments",$id);

$body .= enclose('div',$sitename,'id="head"');
if ($result) {
  foreach($result as $line) {
  $commnum = comments($id);
  $entry .= enclose('div',get_day($line['date']),'class="bigdate"');
  if ( preg_match("/\s?[^@]+\@.*?$/",$line['title']) ) {
  $name = explode(" ",$line['title']);
  $address = array_pop($name);
  $line['title'] = implode(" ",$name);
  if (!$line['title']) { $line['title'] = preg_replace("/([^@]+)\@.*?$/","$1",$address);  }              
	if ( !$line['image'] ) {
  		$entry .= enclose('div',enclo_s('img',"src=\"http://www.gravatar.com/avatar/".md5(strtolower($address))."?d=$hurl/black.jpg\""),'class="image"');
	}
  }
  $entry .= enclose('div',enclose('a',$line['title'],'href="'.$hurl.'/show/'.$id.'"'),'class="title"');
  $entry .= enclose('div',fixup(show_pic($line['image'])),'class="image"');
  $entry .= enclose('div',$line['date'],'class="date"');
  $entry .= enclose('div',fixup(nl2br($line['intro'])),'class="text"');
  if ($line['commentable'] >= 1) {
    if ($commnum != 1) { $comment = 's'; } else { $comment = NULL; }
    $foot .= enclose('a',$commnum. ' comment'.$comment,'href="'.$hurl.'/show/'.$id.'"');
  }
  $entry .= enclose('div',$foot,'class="foot"');
  $body .= enclose('div',$entry,'class="entry"');

  if ($line['commentable'] >= 1) {
    if ($result2) { 
    	foreach($result2 as $line2) {
      		$loop = NULL;
      		$foot = NULL;
                $rate = NULL;
                $com_num += 1;
                $loop .= enclose('div',get_day($line2['date']),'class="bigdate"');
		if ( preg_match("/\s?[^@]+\@.*?$/",$line2['title']) ) {
			$name = explode(" ",$line2['title']);
			$address = array_pop($name);
			$line2['title'] = implode(" ",$name);
			if (!$line2['title']) { $line2['title'] = preg_replace("/([^@]+)\@.*?/","$1",$address);  }              
			if ( !$line2['image'] ) {
				$loop .= enclose('div',enclo_s('img',"src=\"http://www.gravatar.com/avatar/".md5(strtolower($address))."?d=$hurl/black.jpg\""),'class="image"');
			}
		}
                $loop .= enclose('div',enclose('a',$line2['title'],''),'class="title"');
                $loop .= enclose('div',fixup(show_pic($line2['image'])),'class="image"');
                $loop .= enclose('div',nl2br(fixup($line2['intro'])),'class="text"');
                $loop .= enclose('div',$line2['date'],'class="foot"');
                $comments .= enclose('div',$loop,'class="entry"');
	}
    	}
    $body .= enclose('div',$comments,'id="comments"');
    $bot .= enclose('div',$com_num,'class="bigdate"');
    if ($com_num != 1) { $pl = 's'; } else { $pl = NULL; }
    $bot .= enclose('div','comment'.$pl,'class="title"');
    $bot .= enclose('div',enclose('a','Comments through RSS feed','href="'.$hurl.'/rss/comments/'.$id.'"'),'class="text"');
    if ($line['commentable'] == 2) { 
      $bot .= enclose('div', postbox("comments",$id) ,'class="foot"'); 
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
