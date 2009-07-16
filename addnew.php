<?
// addnew.php
// adds new items to the database

if ($_POST['cat'] == "other")
{
	if (isset($_POST['section']) && $_POST['section'] != "") {
		$cat = $_POST['section']; $cm = TRUE;
	} else {
		$cat = "other";
	}
} else {
	$cat = $_POST['cat'];
}

if ($cat === false)
	die("I need a category.");

require_once('functions.php');

$allowed = true;

if (isset($_POST['reset'])) {
	$date = date($datefmt);
} else {
	if ($_POST['date'] != "") {
		$date = htmlspecialchars($_POST['date']);
	} else {
		$date = date($datefmt);
	}
}
if (isset($_POST['sticky'])) {
	$sticky = 0;
} else {
	$sticky = 1;
}
if (isset($_POST['commentable'])) {
	$commentable = $_POST['commentable'];
} else {
	if ($cat == "comments") { $commentable = 0; } else { $commentable = 2; }
}

if (isset($_POST['commentref']) && $_POST['commentref'] != 0) {
	$commentref = $_POST['commentref'];
} else {
	$commentref = 0;
}

if ($_POST['cat'] == "") {
	$allowed = false;
}

if ($_POST['title']) {
	$title = strip_tags(htmlspecialchars($_POST['title']));
} else { $title = "Anonymous"; }

if ($_POST['intro']) {
	$intro = strip_tags(htmlspecialchars($_POST['intro']));
} else {
	$allowed = false;
	$intro = $_POST['intro'];
}

if ($title == "" or $intro == "") {
	$allowed = false;
}
if (preg_match("/^PRIVATE\s?/",$intro)) {
	if (!preg_match("/_private$/",$cat)) {
		$cat .= "_private";
	}
}
else {
	if (preg_match("%\[URL=.*?\].*?\[/URL\]%i",$intro)) {
		$allowed = false;
	} // Use of URL BB-code
	if (preg_match("%a href%i",$intro)) {
		$allowed = false;
	} // Use of HTML anchor
	if (preg_match("%http%i",$intro) and $intro == fixup($intro)) {
		$allowed = false;
	} // Use of the string "http" outside of wiki-like url syntax
	if (preg_match("/google.us/i",$intro)) {
		$allowed = false;
	} // Use of the string "google.us"
}

$transaction_key = $_POST['transaction_key'];
$_REQUEST[] = array();
// Assuming nothing has gone horribly wrong above, handle the upload.
if ($allowed == true) {
	if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
		if ($type = is_image($_FILES['userfile']['tmp_name'])) {
			$rand = mt_rand();
			switch($type) {
			case "image/jpeg": 	$type = ".jpg";
						break;

			case "image/gif": 	$type=".gif";
						break;

			case "image/png": 	$type=".png";
						break;
			}
			$uploadfilename = $rand.$type;
			$uploadfile = $uploaddir . $uploadfilename;
			if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
				if(make_thumb($uploadfilename)) {
					$thumb = "thumb-";
				} else {
					$thumb = NULL;
				}
				$image = $thumb.$uploadfilename;
			} else {
				$allowed = false;
			}
		} else {
			$uploadfilename = date("YmdHis") . '-' . str_replace(" ","_",$_FILES['userfile']['name']);
			$uploadfile = $uploaddir . $uploadfilename;
			move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
			$image = $uploadfilename;
		}
	} else {
		$image = NULL;
	}
}

if ($allowed == true) {
	if (check_transaction_key($transaction_key)) {
		$db->exec(sprintf("INSERT INTO '%s'
			(title, section, date, lastupd, intro, image, commentable, commentref, sticky)
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s', %d, %d, %d)",
				"${db_prefix}data", "$title", "$cat", "$date",
				"$date", "$intro", "$image", "$commentable",
				"$commentref", "$sticky")
		) or die('Sorry, there was a problem and your post could not be completed.<br />' .$db->log);
	} else {
		exit("Double post detected!");
	}
} else {
	echo "$title - $intro - ";
	exit("There has been an error and you cannot post.");
}

if($link) {
	$show = '/';
} else {
	$show = '/show.php?id=';
}
if ($commentref == 0) {
	$db->fetch(sprintf("SELECT title,date,intro,commentable,image
	FROM' %s'
	WHERE id = '%d'
	LIMIT 1", "${db_prefix}data", "$db->last_id"),1,$db->last_id);

	$db->fetch(sprintf("SELECT id,title,date,intro,commentable,image
	FROM '%s'
	WHERE section = '%s'
	ORDER BY sticky ASC, lastupd DESC, date DESC
	LIMIT 10", "${db_prefix}data", "$cat"),1,$cat);

	$db->fetch(sprintf("SELECT DISTINCT section FROM '%s'", "${db_prefix}data"),1,"sections");
	header('Location:'.$hurl.$show.$db->last_id);
} else {
	$db->exec(sprintf("UPDATE '%s' SET lastupd = '%s' WHERE id = '%d'",
			"${db_prefix}data", date($datefmt), "$commentref"))
		or die("Could not update post time (don't worry, your post has gone through).");

	$query2 = sprintf("SELECT id,title,date,intro,commentable,image
		FROM '%s'
		WHERE commentref = '%d'
		ORDER BY date ASC", "${db_prefix}data", "$commentref");
	$query3 = sprintf("SELECT id,title,date,intro,image
		FROM '%s'
		WHERE commentref = '%d'
		ORDER BY sticky ASC, lastupd DESC
		LIMIT 1", "${db_prefix}data", "$commentref");
	$query4 = sprintf("SELECT id
		FROM '%s'
		WHERE commentref = '%d'
			AND section = 'comments'",
		"${db_prefix}data", "$commentref");

	$db->fetch($query2,1,$commentref."com");
	$db->fetch($query3,1,$commentref."1com");
	$db->fetch($query4,1,$commentref."coms");

	$res = $db->fetch(sprintf("SELECT section
		FROM '%s'
		WHERE id = '%d'
		LIMIT 1", "${db_prefix}data", "$commentref"));
	if ($res) {
		foreach ($res as $line) {
			$sec = $line['section'];
			$db->fetch(sprintf("SELECT id,title,date,intro,commentable,image
				FROM '%s'
				WHERE section = '%s'
				ORDER BY sticky ASC, lastupd DESC, date DESC
				LIMIT 10", "${db_prefix}data", "sec"),1,$sec);
		}
	}
	/* redirect to parent cat, not "comments" */
	header('Location:'.$hurl.$show.$commentref);
}

?>
