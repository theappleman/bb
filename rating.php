<?
// rating.php
// change a rating

require_once('functions.php');

$cat = $_GET['cat'];
$id = $_GET['id'];
$key = $_GET['key'];
$_REQUEST = array(NULL);

if (check_transaction_key($key)) {
$result = $db->fetch('SELECT rating FROM '.$db_prefix.'data WHERE id = "'. $id . '"'),0);
	switch($cat) {
		case "raise":
			$rating = $result + 1;
			break;
		case "lower":
			$rating = $result - 1;
			break;
    default: exit("Sorry, can't do that.");
	}
	$db->exec('UPDATE '.$db_prefix.'data SET rating = "'. $rating .'" WHERE id = "'. $id . '"');
}

$r = $db->fetch('SELECT section,commentref FROM '.$db_prefix.'data WHERE id = "'. $id . '"');
list($section,$commentref) = mysql_fetch_array($r,MYSQL_BOTH);
if ($section == "comments") {
	header('Location:'.$hurl.'/show/'.$commentref);
} else { header('Location:'.$hurl.'/show/'.$id); }
?>
