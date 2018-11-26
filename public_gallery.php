<?php require("./connect.php"); ?>
<html>
<head>
	<title>Camagru</title>
	<link rel="stylesheet" href="./css/style.css">
	<link href='https://fonts.googleapis.com/css?family=Bigelow Rules' rel='stylesheet'>
	<link href='https://fonts.googleapis.com/css?family=Black And White Picture' rel='stylesheet'>
	<link href="https://fonts.googleapis.com/css?family=Comfortaa" rel="stylesheet">
</head>
<body>
<div id="window">
<div id="camagru" onclick="window.location = './index.php'">
	<p style="margin: 0px">CAMAGRU</p>
</div>
<div id="pictures">
	<?php

		$gallery = new GALLERY();
		$images = $gallery->fetch_all_imgs();
		$pages = ceil(count($images)/5);

		echo '<div style="text-align: center">';
		echo '<div id="page_bar">';
		for ($x = 1; $x <= $pages; $x++) {
			$url = "window.location.href='./public_gallery.php?page=".$x."'";
			echo '<div ';
			if ($_GET["page"] == $x) {
				echo 'style="color:white"';
			}
			echo ' class="page_item" onclick="'.$url.'">'.$x.'</div>';
		}
		$url = "window.location.href='./public_gallery.php?page=all'";
		echo '<div ';
		if ($_GET["page"] == "all") {
			echo 'style="color:white"';
		}
		echo ' class="page_item" onclick="'.$url.'">All</div>';
		echo '</div>';
		echo '</div>';

		$x = 0;

		foreach ($images as $image) {
			$x++;
			if (isset($_GET["page"]) && $_GET["page"] != "all") {
				$max = ($_GET["page"] * 5);
				$min = $max - 4;
				if ($x > $max || $x < $min) {
					continue;
				}
			}

			echo '<div class="pic_container">';
			echo '<img class="pointer picture" id="'.$image["id"].'" onclick="gotoImg(this)" src="'.$image["addr"].'" border="0" />';
			echo '<div class="likes_and_comments">';
			echo '<div class="likes">';
			echo '<div class="num_likes">';
			if ($image["likes"] == NULL) {
				echo '0';
			} else {
				echo $image["likes"];
			}
			echo '</div>';
			echo '<img class="pointer" src="imgs/resources/';
			echo 'likes_me.png" liked="yes"';
			echo ' width="auto" height="30px" border="0" />';
			echo '</div>';
			echo '<div class="comments">';
			echo '<div class="num_comments">';
			if ($image["comments"] == NULL) {
				echo '0';
			} else {
				echo $image["comments"];
			}
			echo '</div>';
			echo '<img src="imgs/resources/comments.png" width="auto" height="30px" border="0" />';
			echo '</div>';
			echo '</div>';
			echo '</div>';

		}

	?>
</div>

<div id="detailed_image" style="display: none">
	<div id="image_box" visibility="false">
		<img id="exploded_image" pic_id="" src="">
		
		<div class="likes_and_comments">
			<div class="likes">
				<div class="num_likes">
				</div>
				<img class="pointer" src="imgs/resources/likes_me.png" liked="" width="auto" height="30px" border="0" />
			</div>
			<div class="comments">
				<div class="num_comments">
				</div>
				<img src="imgs/resources/comments.png" width="auto" height="30px" border="0" />
			</div>
		</div>

		<div id="comment_box">
			<div class="comment">
				<div class="username">
					Gareth
				</div>
				<div class="message">
					I love you, Princess
				</div>
			</div>
		</div>
	</div>
</div>

<?php require("./footer.php"); ?>

<script>

	$image_box = document.querySelector('#image_box');
	document.body.addEventListener('click', function (event) {
		if ($image_box.contains(event.target)) {
			document.querySelector('#image_box').setAttribute("visibility", "true");
			$detailed_image.style.display = "block";
		} else {
			$detailed_image = document.getElementById("detailed_image");
			if ($image_box.getAttribute("visibility") == "true") {
				$detailed_image.style.display = "none";
				$image_box.setAttribute("visibility", "false");
			}
		}
	});

	function gotoImg($img_elem) {
		$exploded_img = document.getElementById("exploded_image");
		$exploded_img.setAttribute("src", $img_elem.getAttribute("src"));
		$exploded_img.setAttribute("pic_id", $img_elem.id);

		$big_likes = document.getElementById("image_box").querySelector('.likes_and_comments').querySelector('.likes');
		$little_likes = $img_elem.parentElement.querySelector('.likes_and_comments').querySelector('.likes');
		$big_likes.querySelector('.pointer').setAttribute("src", $little_likes.querySelector('.pointer').getAttribute("src"));
		$big_likes.querySelector('.pointer').setAttribute("liked", $little_likes.querySelector('.pointer').getAttribute("liked"));
		$big_likes.querySelector('.num_likes').innerHTML = $little_likes.querySelector('.num_likes').innerHTML;
		$big_likes.parentElement.querySelector('.comments').querySelector('.num_comments').innerHTML = $little_likes.parentElement.querySelector('.comments').querySelector('.num_comments').innerHTML;


		var xhr = new XMLHttpRequest();
		xhr.open('GET', 'get_comments.php?id=' + $img_elem.id, true);
		xhr.setRequestHeader('Content-type', 'application/json');
		xhr.onreadystatechange = function () {
			if (xhr.readyState == 4 && xhr.status == 200) {
				document.getElementById('comment_box').innerHTML = xhr.responseText;
			}
		}
		xhr.send();
		$detailed_image = document.getElementById("detailed_image");
		$detailed_image.style.display = "block";
		setTimeout(function() {
			document.querySelector('#image_box').setAttribute("visibility", "true");
		}, 100);
	}

</script>
