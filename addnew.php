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
$commentable = $_POST['commentable'];

if (isset($_POST['rateable'])) { $rateable = 0; } else { $rateable = 1; }
if ($_POST['rating'] != "") { $rating = $_POST['rating']; } else { $rating = 0; }

if (isset($_POST['commentref']) && $_POST['commentref'] != 0) { $commentref = $_POST['commentref']; } else { $commentref=0; }

if ($_POST['cat'] == "other") {
	if (isset($_POST['section']) && $_POST['section'] != "") {
		$cat = $_POST['section']; $cm = TRUE;
	} else { $cat = "other"; }
} else { $cat = $_POST['cat']; }

$title = strip_tags(htmlentities($_POST['title']));
$intro = strip_tags(htmlentities($_POST['intro']),"<a>");

$transaction_key = $_POST['transaction_key'];
$sess_id = $_POST['session_id'];
$_REQUEST = array(NULL);

if (is_uploaded_file($_FILES['userfile']['tmp_name']) ) {

	if ( is_image($_FILES['userfile']['tmp_name']) ) {
		$rand = mt_rand();
		$uploadfilename = $rand . '-' . basename($_FILES['userfile']['name']);
		$uploadfile = $uploaddir . $uploadfilename;
		if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
      if(make_thumb($uploadfilename)) { $thumb = "thumb-"; } else { $thumb = NULL; }
      $image = $thumb.$uploadfilename;
		} else { $allowed = false; }
	}
}

if($allowed == true) {
	if (check_transaction_key($transaction_key)) {
		mysql_query('INSERT INTO '.$db_prefix.'data
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
	if ($cat == "chatbox") {
		header('Location:'.$hurl.'/chatbox');
	} else { header('Location:'.$hurl.'/'.$cat); }

} else { mysql_unbuffered_query('UPDATE '.$db_prefix.'data SET lastupd = "'.date($datefmt).'" WHERE id = "'.$commentref.'" LIMIT 1') or die('Could not update post time (don\'t worry, your post has gone through).');
header('Location:'.$hurl.'/show/'.$commentref); }

?>
