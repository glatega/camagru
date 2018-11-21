<?php
require("./connect.php");

if (isset($_GET["id"])) {
	$gallery = new GALLERY();
	$comments = $gallery->get_img_comments($_GET["id"]);
	// print_r($comments);
	foreach ($comments as $comment) {
		echo '<div class="comment"><div class="username">'.$comment["name"].'</div><div class="message">'.$comment["message"].'</div></div>';
	}
}
?>
