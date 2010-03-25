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
		$addin = $db->prepare('INSERT INTO "'. $db->quote("${db_prefix}data") .'"
			(title,section,date,lastupd,intro,image,commentable,commentref,sticky)
			VALUES (?,?,?,?,?,?,?,?,?)');
		$addin->execute(array("$title", "$cat", "$date",
				"$date", "$intro", "$image", "$commentable",
				"$commentref", "$sticky")
		) or die('Sorry, there was a problem and your post could not be completed.');
	} else {
		exit("Double post detected!");
	}
} else {
	exit("There has been an error and you cannot post.");
}

if($link) {
	$show = '/';
} else {
	$show = '/show.php?id=';
}
if ($commentref == 0) {
	header('Location:'.$hurl.$show.$db->lastInsertId());
} else {
	$utime = $db->prepare('UPDATE "'. $db->quote("${db_prefix}data") .'" SET lastupd = ? WHERE id = ?');
	$utime->execute(array(date($datefmt), $commentref));

	/* TODO: redirect to parent cat, not "comments" */
	header('Location:'.$hurl.$show.$commentref);
}

?>
