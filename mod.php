<?php
	require_once 'functions.php';
	$action = $_POST['action'];
	$id = $_POST['id'];
  $allowed = true;
	$transaction_key = $_POST['transaction_key'];

	if(!in_array($action,array('edit','delete'))) { exit('No valid action provided.'); }
	$ipuser = 'SELECT ip,useragent,image,commentref
	                FROM '.$db_prefix.'data
	                WHERE id = '.$id.'
	                LIMIT 1';
	$ipres = $db->fetch($ipuser,$cache_time);
	if ($ipres){
		foreach($ipres as $line){
			$image = $line['image'];
			if (levenshtein($line['ip'],$_SERVER['REMOTE_ADDR']) > 6 or levenshtein($line['useragent'],$_SERVER['HTTP_USER_AGENT']) > 50) { exit('Not your post.'); }
		}
	} else { exit('No such ID.'); }

	if ($action == 'edit') {
	$title = $_POST['title'];
	$intro = $_POST['intro'];
	if (is_uploaded_file($_FILES['userfile']['tmp_name']) && is_image($_FILES['userfile']['tmp_name']) ) {
		$rand = mt_rand();
		$uploadfilename = $rand . '-' . basename($_FILES['userfile']['name']);
		$uploadfile = $uploaddir . $uploadfilename;
		if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
		      if(make_thumb($uploadfilename)) { $thumb = "thumb-"; } else { $thumb = NULL; }
		      $image = $thumb.$uploadfilename;
		} else { $allowed = false; }
	}
	if (isset($_POST['image'])) { $image = NULL; }
	if ( preg_match("%\[URL=.*?\].*?\[/URL\]%i",$intro) ) { $allowed = false; }
	$nqry = 'UPDATE '.$db_prefix.'data SET title = "'.$title.'", intro = "'.$intro.'", image = "'.$image.'" WHERE id = '.$id.' LIMIT 1';

	if ($allowed == true) { if(check_transaction_key($transaction_key)){ $db->exec($nqry); } }
	if ($line['commentref'] == "0") {
	header('Location:'.$hurl.'/show/'.$id);
	} else {
	header('Location:'.$hurl.'/show/'.$line['commentref']);
	}
	}
	if ($action == 'delete'){
	if (isset($_POST['image'])) {
		$db->exec('DELETE
			FROM '.$db_prefix.'data
			WHERE id = '.$id.'
			LIMIT 1');
		header('Location:'.$hurl);
	} else { exit('Sorry, you have to tick the box.'); }
	}
?>
