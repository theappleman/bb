<?php
	require_once 'functions.php';
	$id = $_GET['id'];
	$cat = $_GET['cat'];
	if ($id === FALSE or $cat === FALSE) { exit('No ID given.'); }
	$ipuser = 'SELECT ip,useragent
		FROM '.$db_prefix.'data
                WHERE id = '.$id.'
                LIMIT 1';
	$ipres = $db->fetch($ipuser,$cache_time);
	if ($ipres){
		foreach($ipres as $line){
			if ( !check_edit($line['ip'],$line['userconf']) { exit('Not your post.'); }
		}
	} else { exit('No such ID.'); }

	$box = NULL;
	$query = 'SELECT title,intro 
		FROM '.$db_prefix.'data 
		WHERE id = '.$id.'
		LIMIT 1';
	$result = $db->fetch($query,$cache_time);
	if($result){
		foreach($result as $line){
			$name = "form_form";
		        $box .= enclo_s('input','type="hidden" name="transaction_key" value="'.get_transaction_key().'"');
			$box .= enclo_s('input','type="hidden" name="id" value="'.$id.'"');
	    		$box .= enclose('p','Name: '.enclo_s('input','name="title" tabindex="1" accesskey="q" value="'.$line['title'].'"').'&nbsp;'.enclo_s('input','type="file" accesskey="s" name="userfile" tabindex="3" accept="'.$accept.'"').enclo_s('input','type="checkbox" name="image"'),'class="name"');
			$box .= enclose('textarea',nl2br($line['intro']),'name="intro" rows="5" columns="100" tabindex="2" accesskey="w"');
			$box .= enclose('div',enclo_s('input','type="submit" name="action" value="edit"').enclo_s('input','type="submit" name="action" value="delete"'),'class="foot"');
			$box = enclose('form',$box,'name="'.$name.'" action="'.$hurl.'/mod.php" method="post" enctype="multipart/form-data"');
			$box = enclose('div',$box,'class="entry"');
			$box .= enclose('script','var frmvalidator  = new Validator("'.$name.'");frmvalidator.addValidation("title","req","Name is required");frmvalidator.addValidation("title","maxlength=100","Name must be less than 100 characters");frmvalidator.addValidation("intro","req","Comment is required");frmvalidator.addValidation("intro","maxlength=1000","Comment must be less than 1000 characters");','type="text/javascript"');
			$box = enclose('div',$box,'id="edit"');
		}
	}

	echo finish_up(head(), $box);
?>
