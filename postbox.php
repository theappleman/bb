<?php
  require_once 'functions.php';

  $cat = $_GET['cat'];
  $id = $_GET['id'];
  if (!$cat or $id == "") { exit(); }

  $box = NULL;
  $name = "form_form";
  if ($cat == "comments") { $ct = 0; } else { $ct = 2; }
  if ($id != 0) { $box = enclo_s('input','type="hidden" name="commentref" value="'.$id.'"'); }
    $box .= enclo_s('input','type="hidden" name="cat" value="'.$cat.'"');
    $box .= enclo_s('input','type="hidden" name="moderated"');
    $box .= enclo_s('input','type="hidden" name="transaction_key" value="'.get_transaction_key().'"');
    $box .= enclo_s('input','type="hidden" name="commentable" value="'.$ct.'"');
		$box .= enclose('p','Name: '.enclo_s('input','name="title" tabindex="1" accesskey="q"').'&nbsp;'.enclo_s('input','type="file" accesskey="s" name="userfile" tabindex="3" accept="'.$accept.'"'),'class="name"');
		$box .= enclose('textarea','','name="intro" rows="5" columns="100" tabindex="2" accesskey="w"');
		$box .= enclose('div',enclo_s('input','type="submit" value="Lets go!"')/*.enclo_s('input','type="reset" value="Reset"')*/,'class="foot"');
		$box = enclose('form',$box,'name="'.$name.'" action="'.$hurl.'/addnew.php" method="post" enctype="multipart/form-data"');
		$box = enclose('div',$box,'class="entry"');
		$box .= enclose('script','var frmvalidator  = new Validator("'.$name.'");
				frmvalidator.addValidation("title","req","Name is required");
        frmvalidator.addValidation("title","maxlength=100","Name must be less than 100 characters");
				frmvalidator.addValidation("intro","req","Comment is required");
        frmvalidator.addValidation("intro","maxlength=1000","Comment must be less than 1000 characters");','type="text/javascript"');
		$box = enclose('div',$box,'id="postbox"');
		echo finish_up(head(), $box);
?>
