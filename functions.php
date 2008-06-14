<?
// functions.php
// contains function definitions and essential stuff

require_once('userconf.php');
require_once('class_db.php');
$db = new db();

function get_name_md5($line) {
	list($name,$address) = explode("@",$line);
	return array(chop($name),$address);
}

function make_post($title,$intro,$date,$image=NULL,$commentable=0,$id=0) {
  $post = NULL;
  $post .= enclose('div',get_day($date),'class="bigdate"');
  if ( preg_match("/\s?\@[a-z0-9]*?$/",$title) ) {
    list($title,$address) = get_name_md5($title);
    if ( !$image ) {
      $post .= enclose('div',enclo_s('img',"src=\"http://www.gravatar.com/avatar/".$address."?d=$hurl/black.jpg\""),'class="image"');
    } 
  }
  $post .= enclose('div',enclose('a',$title,'href="'.$hurl.'/show/'.$id.'"'),'class="title"');
  $post .= enclose('div',$date,'class="date"');
  if ($image) { $post .= enclose('div',fixup(show_pic($image)),'class="image"'); }
  $post .= enclose('div',fixup(nl2br($intro)),'class="text"');
  
  return $post;
} // unfinished

function postbox($cat,$id,$message="") {
global $hurl, $accept;
  $box = NULL;
  $name = "form_form";
  if ($cat == "comments") { $ct = 0; } else { $ct = 2; }
  if ($id != 0) { $box = enclo_s('input','type="hidden" name="commentref" value="'.$id.'"'); }
    $box .= enclo_s('input','type="hidden" name="cat" value="'.$cat.'"');
    $box .= enclo_s('input','type="hidden" name="moderated"');
    $box .= enclo_s('input','type="hidden" name="transaction_key" value="'.get_transaction_key().'"');
    $box .= enclo_s('input','type="hidden" name="commentable" value="'.$ct.'"');
    $box .= enclo_s('input','type="hidden" name="MAX_FILE_SIZE" value="2097152" ');
		$box .= enclose('p','Name: '.enclo_s('input','name="title" tabindex="1" accesskey="q"').'&nbsp;'.enclo_s('input','type="file" accesskey="s" name="userfile" tabindex="3"'),'class="name"');
		$box .= enclose('textarea',$message,'name="intro" rows="5" cols="100" tabindex="2" accesskey="w"');
		$box .= enclose('div',enclo_s('input','type="submit" tabindex=4" value="Lets go!"')/*.enclo_s('input','type="reset" value="Reset"')*/,'class="foot"');
		$box = enclose('form',$box,'name="'.$name.'" action="'.$hurl.'/addnew.php" method="post" enctype="multipart/form-data"');
		$box .= enclose('script','var frmvalidator  = new Validator("'.$name.'");
				frmvalidator.addValidation("title","req","Name is required");
        frmvalidator.addValidation("title","maxlength=100","Name must be less than 100 characters");
				frmvalidator.addValidation("intro","req","Comment is required");
        frmvalidator.addValidation("intro","maxlength=1000","Comment must be less than 1000 characters");','type="text/javascript"');
    return $box;
 }
function fixup($text) {
  global $patterns;
  foreach($patterns as $key=>$value) {
    $text = preg_replace($key,$value,$text);
  }
  $textrep = array(
    '%#(.*?)@(irc\.[^\s]+)%i'=>'[[irc://$2/$1|#$1@$2]]',
    '%\[\[([^\|]*?)\]\]%'=>'[[$1|-link-]]',
    '%\[\[(.*?)\|(.*?)\]\]%'=>'<a href="$1" title="$1" >$2</a>',
    '%\{\{(.*?)\|(.*?)\}\}%'=>'<a href="$2" class="highslide" rel="highslide" onclick="return hs.expand(this)" ><img src="$1" /></a>',
    '%\{\{(.*?)\}\}%'=>'<a href="$1" class="highslide" rel="highslide" onclick="return hs.expand(this)" ><img src="$1" /></a>',
    '%\s\s+%'=>' ',
    '%\*(.*?)\*%'=>'<b>*$1*</b>'
    );
  $text = preg_replace(array_keys($textrep),array_values($textrep),$text);
  return $text;
 }

 function finish_up($head,$body) {
  $body = enclose('div',$body,'id="content"') . menu();
  $head = enclose('head',$head,'');
  $body = enclose('body',$body,'');
  $finish = enclose('html',$head . $body,'xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"');
  return $finish;
  }

function show_pic($image) {
  if (!$image) { return ''; }
  global $uploaddir, $hurl, $snapcode;
  if ( is_image($uploaddir.$image)) {
    list($thumb,$rand) = explode('-',$image,2);
    if($thumb == "thumb" && is_image($uploaddir."thumb-" . $rand)) {
      $thumbname = "thumb-" . $rand;
      $filename = $rand;
    } else {
      $thumbname = $image;
      $filename = $image;
    }
    return "{{".$hurl."/uploaded/".$thumbname."|".$hurl."/uploaded/".$filename."}}";
  } else { if ($snapcode) { $snap = "class=\"snap_shots\"";}
  return "<a href=\"$hurl/uploaded/$image\" $snap >$image</a>"; }
}

function is_image($filename) {
	$type=@getimagesize($filename);
	$image_type = $type['mime'];
	if ($image_type == "image/gif" || $image_type == "image/jpeg" || $image_type == "image/png") {
		return $image_type;
	} else { return false; }
}

function make_thumb($filename) {
  global $width, $height, $uploaddir, $filtertype;
  $fullfile = $uploaddir . $filename;
  $thumbfile = $uploaddir . 'thumb-' . $filename;
	if ($image_type = is_image($fullfile)) {
		
    list($width_orig, $height_orig) = getimagesize($fullfile);
    
    if ($width > $width_orig && $height > $height_orig) { return false; }
		
    $ratio_orig = $width_orig/$height_orig;
		if ($width/$height > $ratio_orig) { $width = $height*$ratio_orig; }
    else { $height = $width/$ratio_orig; }
		
    $image_p = imagecreatetruecolor($width, $height);
    
		switch($image_type) {
			case 'image/gif': $image = imagecreatefromgif($fullfile);
				break;
			case 'image/jpeg': $image = imagecreatefromjpeg($fullfile);
				break;
			case 'image/png': $image = imagecreatefrompng($fullfile);
				break;
			default: exit("Somehow, there is an error");
		}
    
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
    
    switch($image_type) {
    case 'image/png': $image = imagepng($image_p,$thumbfile);
      break;
    case 'image/jpeg': $image = imagejpeg($image_p,$thumbfile);
      break;
    case 'image/gif': $image = imagegif($image_p,$thumbfile);
      break;
    default: exit("Somehow, there is an error");
    }
	} else { return false; }
  return true;
}

function head($cat="",$id="") {
  global $style, $sitename, $hurl;
	$meta = enclose('link','','rel="stylesheet" href="'.$hurl.'/'.$style.'.php" type="text/css" title="default"');
  $meta .= enclose('link','','rel="alternate" type="application/rss+xml" href="'.$hurl.'/rss/'.$cat.'/'.$id.'" title="' . $sitename . ' feed"');
  $meta .= enclose('script','','src="'.$hurl.'/ie7-standard-p.js" type="text/javascript"');
  $meta .= enclose('script','','src="'.$hurl.'/highslide.js" type="text/javascript"');
  $meta .= enclose('script','','src="'.$hurl.'/gen_validatorv2.js" type="text/javascript"');
  $meta .= enclose('script','hs.graphicsDir = "'.$hurl.'/graphics/";','type="text/javascript"');
  $meta .= '<meta name="robots" content="noindex, nofollow">';
	return $meta;
}

function get_day($id) {
	list($p,$l) = explode(" ",$id,2);
	list($year,$month,$day) = explode("-",$p,4);
	return $day;
}

function single_section($cat) {
	list($cm) = explode(",",$cat,2);
	return $cm;
}

function comments($id) {
	global $db,$db_prefix,$cache_time;
	$com_num = 0;
	$query = 'SELECT id FROM '.$db_prefix.'data WHERE commentref = "'.$id.'" AND section = "comments"';
	$result = $db->fetch($query,$cache_time,$id."coms");
	if ($result) { foreach ($result as $r) { $com_num += 1; } }
	return $com_num;
}

function menu() {
	global $page, $cat, $menu, $hurl, $db, $snapcode, $cache_time, $db_prefix, $nochat;
	$return = NULL;
	foreach ($menu as $key=>$link) {
		$sitemenu .= enclose('a',ucwords($key),'href="'.$link.'"');
		}
	$return .= enclose("div",'Links:'.$sitemenu,'class="mainmenu"');

	$rslt = $db->fetch('SELECT DISTINCT section FROM '.$db_prefix.'data',$cache_time,"sections");

	if ($rslt) {
    foreach($rslt as $ln) {
    $rry .= $ln['section'] .',';
    }
  }
  $array = explode(",",$rry);

	$rslt = NULL;
	foreach (array_unique($array) as $table) { 
		if (!preg_match("/_private$/",$table)) {
			$rslt .= enclose('a',$table,'href="'.$hurl.'/'.$table.'"'); 
			}
	}
	$return .= enclose('div','Pub:'.$rslt,'class="mainmenu"');

  if ( isset($cat) ) {
    if ( $page != "" && $page != "0" ) { $pages .= enclose('a','Previous','href="'.$hurl.'/'.$cat.'/p'.($page-1).'"').':'; }
   $pages .= 'Page:'.enclose('a','Next','href="'.$hurl.'/'.$cat.'/p'.($page+1).'"');
  }

  if ($pages) { $return .= enclose('div',$pages,'class=mainmenu'); }

	if ($snapcode != "") {
		$return .= enclose('script','','type="text/javascript" src="http://shots.snap.com/ss/'.$snapcode.'/snap_shots.js"');
	}
	return $return;
}

function get_transaction_key() { return uniqid('', true); }

function enclose($type,$content,$opts = "") {
	$return = NULL;
	$return .= '<'.$type;
	if ($opts != "") { $return .= ' '.$opts; }
	$return .= '>';
	$return .= $content;
	$return .= '</'.$type.'>';
	return $return;
}

function enclo_s($type,$opts) { return '<' . $type . ' ' . $opts . ' />'; }

function check_transaction_key($key) {
	global $db_prefix, $db;
    if (false === $db->exec('INSERT INTO '.$db_prefix.'transactions (transaction_key) VALUES ("'.$key.'")')) { return false; }
    else { return true; }
}

?>
