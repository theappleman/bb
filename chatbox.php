<?
// chatbox.php
// view the chatbox and post

require_once('functions.php');

$cat = $_REQUEST['cat'];
$id = $_REQUEST['id'];
$page = $_REQUEST['page'];
$_REQUEST = array(NULL);

if ($id == "") { $id = "10"; }
if ($page != "") { $id = $id . ' OFFSET ' . $page*$id; }
$query = 'SELECT id,title,date,intro,commentable,image
	FROM '.$db_prefix.'data WHERE section = "'.$cat.'"
  ORDER BY sticky ASC,lastupd DESC, date DESC ';
if ($id != "0") { $query .= ' LIMIT '.$id; }

$result = $db->fetch($query,$cache_time,$cat.$page);

$return = NULL;$body = NULL;$head = NULL;
$head .= enclose("title",$sitename.' '. $cat,"");
$head .= head($cat,$id);

$body .= enclose('div',$sitename,'id="head"');

if(!in_array($cat, $nochat)) { $body .= enclose('div',postbox($cat,0),'class="entry"'); }

if ($result) {
  foreach($result as $line) {
    $loop = NULL; $foot = NULL; $comments = NULL; $rate = NULL;
    $commnum = comments($line['id']);
    $loop .= enclose('div',get_day($line['date']),'class="bigdate"');
    $loop .= enclose('div',enclose('a',$line['title'],'href="'.$hurl.'/show/'.$line['id'].'"'),'class="title"');
    $loop .= enclose('div',$line['date'],'class="date"');
    $loop .= enclose('div',fixup(show_pic($line['image'])),'class="image"');
    $loop .= enclose('div',fixup(nl2br($line['intro'])),'class="text"');
    if ($line['commentable'] == 2) {
        $loop .= enclose('div',
          enclose('a','Post a comment','href="'.$hurl.'/show/'.$line['id'].'"'),
        'class="foot"');
      }
      else { $loop .= enclose('div',enclose('a','No more comments','href="'.$hurl.'/show/'.$line['id'].'"'),'class="foot"'); }
    if ($commnum >= 1) {
      $query2 = 'SELECT id,title,date,intro,image
        FROM '.$db_prefix.'data
        WHERE commentref="'.$line['id'].'"
        ORDER BY sticky ASC, date DESC
        LIMIT 1';
      $result2 = $db->fetch($query2,$cache_time,$line['id']."1com");
      foreach($result2 as $line2) {
        $nloop = NULL;$foot = NULL;$rate = NULL;
        $nloop .= enclose('div',get_day($line2['date']),'class="bigdate"');
        $nloop .= enclose('div',enclose('a',$line2['title'],'href="'.$hurl.'/show/'.$line['id'].'"'),'class="title"');
        $nloop .= enclose('div',$line2['date'],'class="date"');
        $nloop .= enclose('div',fixup(show_pic($line2['image'])),'class="image"');
        $nloop .= enclose('div',fixup(nl2br($line2['intro'])),'class="text"');
        if ($commnum != 1) { $comment = 's'; } else { $comment = NULL; }
        $nloop .= enclose('div',enclose('a',$commnum. ' comment'.$comment,'href="'.$hurl.'/show/'.$line['id'].'"'),'class="foot"');
        $comments .= enclose('div',$nloop,'class="entry"');
      } // foreach
      $loop .= enclose('div',$comments,'id="comments"');
    } // if
    $body .= enclose('div',$loop,'class="entry"');
  } // foreach
} // if
$return = finish_up($head,$body);
//$return = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . $return;
echo $return;
?>
