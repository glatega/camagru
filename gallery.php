<?php

require("./account_bar.php");



?>

<style>

@media screen and (min-width: 600px) {
	.pic_container {
		width: 40%;
	}
}

@media screen and (min-width: 1000px) {
	.pic_container {
		width: 20%;
	}
}

#pictures {
	text-align: center;
}
.pic_container {
	margin: 10px;
	display: inline-block;
}
.picture {
	width: 100%;
}
.likes_and_comments {
	text-align: center;
	height: 50px;
	background-color: #9a4625;
}
.likes {
	float: left;
	margin: 10px;
}
.comments {
	float: right;
	margin: 10px;
}
.num_likes {
	float: right;
    line-height: 30px;
	margin-left: 10px;
	font-family: sans-serif;
    font-weight: bold;
    color: #ffc85d;
}
.num_comments {
	float: left;
    line-height: 30px;
	margin-right: 10px;
	font-family: sans-serif;
    font-weight: bold;
    color: #ffc85d;
}
.pointer {
	cursor: pointer;
}
</style>

<div id="pictures">
	<?php

		$gallery = new GALLERY();
		$images = $gallery->fetch_all_imgs();
		$mylikes = $user->liked_pictures();

		foreach ($images as $image) {

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

<style>
#detailed_image {
    position: absolute;
    width: 100%;
    height: 100%;
    padding-top: 10vh;
    top: 91px;
    text-align: center;
    background-color: rgba(0, 0, 0, 0.8);
}
#image_box {
    display: inline-block;
    background-color: #a07c6d;
}
.username {
	font-weight: bold;
    text-decoration: underline;
}
.comment {
	margin: 10px;
    background-color: #eaa972;
	text-align: left;
    padding: 5px;
}
.message {
	margin: 5px;
}
#add_comment {
	margin: 5px;
}
#type_comment {
	width: calc(100% - 42px);
    height: 42px;
	float: left;
	border-radius: 42px;
    padding-left: 15px;
	outline: none;
}
</style>

<div id="detailed_image" style="display: none">
	<div id="image_box" visibility="false">
		<img id="exploded_image" pic_id="" src="">
		
		<div class="likes_and_comments">
			<div class="likes">
				<div class="num_likes">
				</div>
				<img class="pointer" onclick="clickBigLike(this)" src="" liked="" width="auto" height="30px" border="0" />
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
