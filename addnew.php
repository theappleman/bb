<?
// addnew.php
// adds new items to the database

require_once('functions.php');

$allowed = true;

if (isset($_POST['reset'])) { $date = date($datefmt); }
else {
	if ($_POST['date'] != "") { $date = htmlspecialchars($_POST['date']); }
	else { $date = date($datefmt); }
}

if (isset($_POST['moderated'])) { $moderated = 0; }	else { $moderated = 1; }
if (isset($_POST['sticky'])) { $sticky = 0; } else { $sticky = 1; }
if (isset($_POST['commentable'])) { $commentable = $_POST['commentable']; } else { $commentable = 0; }

if (isset($_POST['rateable'])) { $rateable = 0; } else { $rateable = 1; }

if (isset($_POST['commentref']) && $_POST['commentref'] != 0) { $commentref = $_POST['commentref']; } else { $commentref=0; }

if ($_POST['cat'] == "") { $allowed = false; }

if ($_POST['cat'] == "other") {
	if (isset($_POST['section']) && $_POST['section'] != "") {
		$cat = $_POST['section']; $cm = TRUE;
	} else { $cat = "other"; }
} else { $cat = $_POST['cat']; }

$title = htmlentities(strip_tags($_POST['title']));
$intro = htmlentities(strip_tags($_POST['intro']));

if ( preg_match("%\[URL=.*?\].*?\[/URL\]%i",$intro) ) { $allowed = false; }

$transaction_key = $_POST['transaction_key'];
$_REQUEST = array(NULL);

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
			(title,section, date,lastupd, intro, image, moderated, commentable, commentref,sticky,rateable)
			VALUES ("' . $title . '",
				"'. $cat .'",
				"'.$date.'",
				"'.$date.'",
				"' . $intro . '",
        "'.$image.'",
				"' . $moderated . '",
				"' . $commentable . '",
				"' . $commentref . '",
				"'.$sticky.'",
				"'.$rateable.'"
				)') or die('Sorry, there was a problem and your post could not be completed. ' .mysql_error() );
	} else { exit("Double post detected!"); }
} else { exit("There has been an error and you cannot post."); }

if ($commentref == 0) {
	$db->fetch('SELECT id,title,date,intro,commentable,rateable,rating,image
	FROM '.$db_prefix.'data
	WHERE section = "'.single_section($cat).'"
		AND moderated != 1
		AND rating >= -50
	ORDER BY sticky ASC,lastupd DESC, date DESC LIMIT 10',0,single_section($cat));
  header('Location:'.$hurl.'/'.single_section($cat));
} else { $db->exec('UPDATE '.$db_prefix.'data SET lastupd = "'.date($datefmt).'" WHERE id = "'.$commentref.'" LIMIT 1') or die('Could not update post time (don\'t worry, your post has gone through).');
$query2 = 'SELECT id,title,date,intro,rateable,rating,commentable,image
	FROM '.$db_prefix.'data
	WHERE moderated != 1
		AND commentref="'.$id.'"
		AND rating >= -50
	ORDER BY date ASC';
$result2 = $db->fetch($query2,0,$commentref."com");
header('Location:'.$hurl.'/show/'.$commentref); }

?>
