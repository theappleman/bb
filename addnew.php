<?
// addnew.php
// adds new items to the database

require_once('functions.php');

$allowed = true;

$ip = $_SERVER['REMOTE_ADDR'];
$useragent = $_SERVER['HTTP_USER_AGENT'];

if (isset($_POST['reset'])) { $date = date($datefmt); }
else {
	if ($_POST['date'] != "") { $date = htmlspecialchars($_POST['date']); }
	else { $date = date($datefmt); }
}
if (isset($_POST['sticky'])) { $sticky = 0; } else { $sticky = 1; }
if (isset($_POST['commentable'])) { $commentable = $_POST['commentable']; } else { $commentable = 0; }

if (isset($_POST['commentref']) && $_POST['commentref'] != 0) { $commentref = $_POST['commentref']; } else { $commentref=0; }

if ($_POST['cat'] == "") { $allowed = false; }

if ($_POST['cat'] == "other") {
	if (isset($_POST['section']) && $_POST['section'] != "") {
		$cat = $_POST['section']; $cm = TRUE;
	} else { $cat = "other"; }
} else { $cat = $_POST['cat']; }

if ($_POST['title']) { $title = strip_tags($_POST['title']); } else { $allowed = false; $title = $_POST['title']; }
if ($_POST['intro']) { $intro = strip_tags($_POST['intro']); } else { $allowed = false; $intro = $_POST['intro']; }

if ( preg_match("%\[URL=.*?\].*?\[/URL\]%i",$intro) ) { $allowed = false; }

$transaction_key = $_POST['transaction_key'];
$_REQUEST[] = array();

if (is_uploaded_file($_FILES['userfile']['tmp_name']) && is_image($_FILES['userfile']['tmp_name']) ) {
		$rand = mt_rand();
		$uploadfilename = $rand . '-' . basename($_FILES['userfile']['name']);
		$uploadfile = $uploaddir . $uploadfilename;
		if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
      if(make_thumb($uploadfilename)) { $thumb = "thumb-"; } else { $thumb = NULL; }
      $image = $thumb.$uploadfilename;
		} else { $allowed = false; }
} else { $image = NULL; }

if($allowed == true) {
	if (check_transaction_key($transaction_key)) {
		$db->exec('INSERT INTO '.$db_prefix.'data
			(title,section, date,lastupd, intro, image, commentable, commentref,sticky)
			VALUES ("' . $title . '",
				"'. $cat .'",
				"'.$date.'",
				"'.$date.'",
				"' . $intro . '",
        "'.$image.'",
				"' . $commentable . '",
				"' . $commentref . '",
				"'.$sticky.'"
				)') or die('Sorry, there was a problem and your post could not be completed. ' .mysql_error() );
	} else { exit("Double post detected!"); }
} else { echo "$title - $intro - ";
exit("There has been an error and you cannot post.");}

if ($commentref == 0) {
	$db->fetch('SELECT title,date,intro,commentable,image
	FROM '.$db_prefix.'data
  WHERE id ="' . $db->last_id . '"
	WHERE
	LIMIT 1',0,$db->last_id);
  header('Location:'.$hurl.'/show/'.$db->last_id);
} else {
$db->exec('UPDATE '.$db_prefix.'data SET lastupd = "'.date($datefmt).'" WHERE id = "'.$commentref.'" LIMIT 1') or die('Could not update post time (don\'t worry, your post has gone through).');
$query2 = 'SELECT id,title,date,intro,commentable,image
	FROM '.$db_prefix.'data
	WHERE moderated != 1
		AND commentref="'.$commentref.'"
		AND rating >= -50
	ORDER BY date ASC';
$query3 = 'SELECT id,title,date,intro,image
  FROM '.$db_prefix.'data
  WHERE moderated != 1
    AND commentref="'.$line['id'].'"
    AND rating >= -50
  ORDER BY sticky ASC, lastupd DESC
  LIMIT 1';
$query4 = 'SELECT id FROM '.$db_prefix.'data WHERE commentref = "'.$id.'" AND moderated != "1" AND section = "comments"';

$db->fetch($query2,0,$commentref."com");
$db->fetch($query3,0,single_section($cat).$commentref."1com");
$db->fetch($query4,0,$commentref."coms");

header('Location:'.$hurl.'/show/'.$commentref); }

?>
