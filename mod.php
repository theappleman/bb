<?php
	require_once 'functions.php';
	$action = $_POST['action'];
	$id = $_POST['id'];
	$transaction_key = $_POST['transaction_key'];

	if(!in_array($action,array('edit','delete'))) { exit('No valid action provided.'); }
	$ipuser = 'SELECT ip,useragent,image
	                FROM '.$db_prefix.'data
			                WHERE id = '.$id.'
					                LIMIT 1';
							        $ipres = $db->fetch($ipuser,$cache_time);
								        if ($ipres){
									                foreach($ipres as $line){
											                        $image = $line['image'];
														                        if (levenshtein($line['ip'],$_SERVER['REMOTE_ADDR']) > 25 or levenshtein($line['useragent'],$_SERVER['HTTP_USER_AGENT']) > 50) { exit('Not your post.'); }
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
	
	$nqry = 'UPDATE '.$db_prefix.'data SET title = "'.$title.'", intro = "'.$intro.'", image = "'.$image.'" WHERE id = '.$id.' LIMIT 1';

	if(check_transaction_key($transaction_key)){	$db->exec($nqry) or die($db->log); }
	header('Location:'.$hurl.'/show/'.$id);
	}
	if ($action == 'delete'){
		$db->exec('DELETE
			FROM '.$db_prefix.'data
			WHERE id = '.$id.'
			LIMIT 1');
		header('Location:'.$hurl);
	}
?>
