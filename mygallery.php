<?php require("./account_bar.php"); ?>

<div id="pictures">
	<?php

		$images = $user->fetch_my_imgs();
		$mylikes = $user->liked_pictures();
		$pages = ceil(count($images)/5);

		echo '<div style="text-align: center">';
		echo '<div id="page_bar">';
		for ($x = 1; $x <= $pages; $x++) {
			$url = "window.location.href='./mygallery.php?page=".$x."'";
			echo '<div ';
			if ($_GET["page"] == $x) {
				echo 'style="color:white"';
			}
			echo ' class="page_item" onclick="'.$url.'">'.$x.'</div>';
		}
		$url = "window.location.href='./mygallery.php?page=all'";
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
			echo '<img class="pointer" onclick="clickLike(this)" src="imgs/resources/';
			if (in_array($image["id"], $mylikes)) {
				echo 'likes_me.png" liked="yes"';
			} else {
				echo 'likes_me_not.png" liked="no"';
			}
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
		<img id="delete_me" onclick="deleteMe(this)" src="imgs/resources/trash.png" width="auto" height="30px" border="0" />
		<div class="likes_and_comments">
			<div class="likes">
				<div class="num_likes">
				</div>
				<img class="pointer" onclick="clickBigLike(this)" src="imgs/resources/likes_me.png" liked="" width="auto" height="30px" border="0" />
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
		<div id="add_comment">
			<textarea id="type_comment" placeholder="Write a comment..."></textarea>
			<img onclick="submitComment(this)" src="./imgs/resources/paper-plane.svg" width="42px" height="42px">
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

	function deleteMe($this) {
		if (confirm("Are you sure you want to delete this image?")) {
			$pic_id = $this.parentElement.querySelector('#exploded_image').getAttribute("pic_id");
			var xhr = new XMLHttpRequest();
			xhr.open('GET', 'delete.php?id=' + $pic_id, true);
			xhr.setRequestHeader('Content-type', 'application/json');
			xhr.onreadystatechange = function () {
				if (xhr.readyState == 4 && xhr.status == 200) {
					document.getElementById($pic_id).parentElement.style.display = "none";
					document.getElementById("detailed_image").style.display = "none";
					document.getElementById("image_box").setAttribute("visibility", "false");
				}
			}
			xhr.send();
		}
	}

	function clickBigLike($this) {
		$real_btn_id = $this.parentElement.parentElement.parentElement.querySelector('#exploded_image').getAttribute("pic_id");
		$real_btn = document.getElementById($real_btn_id).parentElement.querySelector('.likes_and_comments').querySelector('.likes').querySelector('.pointer');
		$real_btn.click();
		$parent = $this.parentElement;
		$childlike = $parent.querySelector('.num_likes');
		if ($this.getAttribute("liked") == "yes") {
			$this.setAttribute("liked", "no");
			$this.setAttribute("src", "imgs/resources/likes_me_not.png");
			$childlike.innerHTML -= 1;
		} else {
			$this.setAttribute("liked", "yes");
			$this.setAttribute("src", "imgs/resources/likes_me.png");
			$childlike.innerHTML = parseInt($childlike.innerHTML) + 1;
		}
	}

	function submitComment($this) {
		$textarea = $this.parentElement.querySelector('#type_comment');
		$picID = $this.parentElement.parentElement.querySelector('#exploded_image').getAttribute("pic_id");
		if ($textarea.value.length > 0) {
			var json = {
				user: "<?php echo $_SESSION["account"]; ?>",
				image: $picID,
				comment: $textarea.value
			}

			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'comment.php', true);
			xhr.setRequestHeader('Content-type', 'application/json');
			xhr.onreadystatechange = function (data) {
				if (xhr.readyState == 4 && xhr.status == 200) {
					var comments = document.getElementById('comment_box').innerHTML + '<div class="comment"><div class="username"><?php echo $_SESSION["account"]; ?></div><div class="message">'+$textarea.value+'</div></div>';
					document.getElementById('comment_box').innerHTML = comments;
					$textarea.value = "";
					$comm_num = $this.parentElement.parentElement.querySelector('.likes_and_comments').querySelector('.comments').querySelector('.num_comments');
					$comm_num.innerHTML = parseInt($comm_num.innerHTML) + 1;
				}
			}
			xhr.send(JSON.stringify(json));
		}
	}

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

	function clickLike($this) {
		$parent = $this.parentElement;
		$childlike = $parent.querySelector('.num_likes');
		if ($this.getAttribute("liked") == "yes") {
			$this.setAttribute("liked", "no");
			$this.setAttribute("src", "imgs/resources/likes_me_not.png");
			$childlike.innerHTML -= 1;
			$like = "subtract";
		} else {
			$this.setAttribute("liked", "yes");
			$this.setAttribute("src", "imgs/resources/likes_me.png");
			$childlike.innerHTML = parseInt($childlike.innerHTML) + 1;
			$like = "add";
		}
		$img = $parent.parentElement.parentElement.querySelector('.picture').id;
		var json = {
			user: "<?php echo $_SESSION["account"]; ?>",
			image: $img,
			like: $like
		}
		var xhr = new XMLHttpRequest();
		xhr.open('POST', 'like.php', true);
		xhr.setRequestHeader('Content-type', 'application/json');
		xhr.send(JSON.stringify(json));
	}
</script>
