<?php
	require("./account_bar.php");
	$newID = $user->get_last_pic_id() + 1;
?>
<style>
#camera_buttons_bot, #effects, video, canvas {
	max-width: 640px;
	height: auto;
	border: 3px solid;
}
.buttpic {
	width: 50px;
	height: 50px;
	margin: 10px 10%;
	border: none;
}
#camera_buttons_bot {
	text-align: center;
	border: 3px solid;
	border-top: none;
	background-color: #cccccc;
    border-radius: 0px 0px 100px 100px;
}
#camera_buttons_top {
    text-align: center;
    border: 3px solid;
    border-bottom: none;
    background-color: #cccccc;
    border-radius: 100px 100px 0px 0px;
}
#cambox {
	width: 646px;
	margin: 0 auto;
}
#effects {
	border-radius: 10px;
	margin-top: 10px;
	background-color: #cccccc;
	overflow: hidden;
}
#effect_btns {
	text-align: center;
	cursor: pointer;
}
.effect_btn {
	padding: 10px 0px;
	width: calc(calc(100% - 2px)/3);
	display: inline-block;
	border-left: 1px solid;
	border-bottom: 1px solid;
	background-color: #7b7b7b;
}
.td_filter {
	display: flex;
}
.filters {
	flex-grow: 1;
}
#filter_btn {
	border-left: none;
}
#filter_box {
	width: 100%;
}
#mask_box {
	width: 100%;
	background-color: #cccccc;
	white-space: nowrap;
	overflow-x: scroll;
}
#frame_box {
	width: 100%;
	background-color: #cccccc;
	white-space: nowrap;
	overflow-x: scroll;
}
#overlay {
	/* background-color: pink; */
	/* opacity: 0.5; */
	overflow: hidden;
	position: absolute;
	top: 3px;
	left:3px;
}
#frame {
	/* background-color: pink;
	opacity: 0.5; */
	position: absolute;
	top: 3px;
	left:3px;
}
.mask {
	position: inherit;
}
</style>

<div style="margin-top: 100px;">
	<div id="cambox">
		<div id="camera_buttons_top">
			<button class="buttpic" id="rainbow" style="background: url(./imgs/resources/rainbow.svg)"></button>
			<button class="buttpic" id="upload" style="background: url(./imgs/resources/picture.svg)"></button>
			<!-- <button class="buttpic" id="xcancel" style="background: url(./imgs/resources/trash.png); background-size: cover"></button> -->
		</div>
		<div style="position: relative">
			<canvas id="canvas" style="display:none"></canvas>
			<div id="frame">
				<?php
					$frames = opendir(dirname(realpath(__FILE__)).'/imgs/frames/');
					while($frame = readdir($frames)){
						if($frame !== '.' && $frame !== '..' && $frame !== '.DS_Store'){
							echo '<img class="frames" id="'.$frame.'" style="display: none;" width="100%" height="100%" src="imgs/frames/'.$frame.'" border="0" />';
						}
					}
					closedir($frames);
				?>
			</div>
			<div id="overlay">
			</div>
			<div id="camera_buttons_bot">
				<button class="buttpic" id="save" onclick="savePic()" style="background: url(./imgs/resources/floppy.svg); background-size: cover;"></button>
				<button class="buttpic" id="snap" style="background: url(./imgs/resources/camera.svg)"></button>
				<button class="buttpic" id="cancel" style="background: url(./imgs/resources/trash.png); background-size: cover"></button>
			</div>
		</div>

		<p id="test" style="color: white">
				test
		</p>

		<script>

			function savePic() {
				// xhttp = new XMLHttpRequest();
				// xhttp.open("POST", "./save.php", true);
				// xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				// canvas = document.getElementById("canvas");
				// data = canvas.toDataURL("image/png");
				// // test = document.getElementById("test");
				// // test.innerHTML = "user=<?php //echo $_SESSION["account"]; ?>&photo=" + data;
				// // window.location.href=data;
				// xhttp.send("user=<?php //echo $_SESSION["account"]; ?>&photo=" + data);

				var stickerArray = [];

				stickers = document.getElementById("overlay").childNodes;
				stickers.forEach(function(sticker) {
					if (sticker.nodeName == "IMG") {
						scaleSize = sticker.style.transform;
						scaleSize = scaleSize.substr(6);
						scaleSize = scaleSize.substr(0, scaleSize.length-1);
						// stickerArray[sticker.id.toString()] = {top:sticker.offsetTop.toString(), left:sticker.offsetLeft.toString(), scale:scaleSize.toString()};
						
						stickerArray.push(top:sticker.offsetTop.toString(), left:sticker.offsetLeft.toString(), scale:scaleSize.toString());
					}
				});
				console.log(stickerArray);

				var json = {
					user: "<?php echo $_SESSION["account"]; ?>",
					// image: canvas.toDataURL("image/png"),
					masks: stickerArray,
					liked: 1
				}

				var xhr = new XMLHttpRequest();
				xhr.open('POST', 'save.php', true);
				xhr.setRequestHeader('Content-type', 'application/json');
				xhr.onreadystatechange = function (data) {
					if (xhr.readyState == 4 && xhr.status == 200) {
						console.log(xhr.responseText);
						// b = xhr.responseText + "";
						// b.replace(/<&#91;^>&#93;*>/g, "");
						// document.getElementById('like_id').innerHTML = '';
						// document.getElementById('like_id').innerHTML = b;
					}
				}
				xhr.send(JSON.stringify(json))
			}

			function toggleFrame(name) {
				frame = document.getElementById(name);
				visibility = frame.style.display;
				frames = document.querySelectorAll(".frames");
				frames.forEach(function(f) {
					f.style.display = "none";
				});
				if (visibility == "none") {
					frame.style.display = "block";
				} else {
					frame.style.display = "none";
				}
			}

			function scrollZoom(event) {
				var stckr = event.target;
				var scale = stckr.style.transform;
				scale = scale.substr(6);
				scale = scale.substr(0, scale.length-1);
				console.log(scale);
				if (event.deltaY > 0) {
					console.log("+");
					scale = scale * 1 + 0.1;
					stckr.style.transform = "scale(" + scale + ")";
				} else {
					console.log("-");
					scale = scale * 1 - 0.1;
					stckr.style.transform = "scale(" + scale + ")";
				}
				if (scale <= 0) {
					removeElement(event.target.id);
				}
			}

			function toggleMask(name) {
				if (!document.getElementById(name)) {
					var newMask = document.createElement("img");
					var att = document.createAttribute("class");
					att.value = "mask";
					newMask.setAttributeNode(att);
					att = document.createAttribute("id");
					att.value = name;
					newMask.setAttributeNode(att);
					att = document.createAttribute("onwheel");
					att.value = "scrollZoom(event)";
					newMask.setAttributeNode(att);
					att = document.createAttribute("style");
					att.value = "top: 0px; left: 0px; transform: scale(1);";
					newMask.setAttributeNode(att);
					att = document.createAttribute("draggable");
					att.value = "true";
					newMask.setAttributeNode(att);
					att = document.createAttribute("width");
					att.value = "auto";
					newMask.setAttributeNode(att);
					att = document.createAttribute("height");
					att.value = "200px";
					newMask.setAttributeNode(att);
					att = document.createAttribute("ondragstart");
					att.value = "startDragMask(event)";
					newMask.setAttributeNode(att);
					att = document.createAttribute("src");
					att.value = "imgs/masks/" + name;
					newMask.setAttributeNode(att);
					overlay.appendChild(newMask);
				}
			}

			function startDragMask(e) {
				e.preventDefault();
				targ = e.target;
				mask = document.getElementById(targ.id);
				startX = e.clientX;
				startY = e.clientY;
				startLeft = parseInt(mask.style.left, 10);
				startTop = parseInt(mask.style.top, 10);
				targ.hold = true;
				document.onmousemove = function(event) {dragMask(event)};
				return false;
			}

			function dragMask(e) {
				if (!targ.hold) {return};
				var nowX = e.clientX;
				var nowY = e.clientY;
				targ.style.left = startLeft + (nowX-startX)+'px';
				targ.style.top = startTop + (nowY-startY)+'px';
				return false;
			}

			function stopDrag() {
				if (targ.classList.contains("mask")) {
					overlayW = parseInt(overlay.style.width, 10);
					overlayH = parseInt(overlay.style.height, 10);
					maskW = targ.width;
					maskH = targ.height;
					maskL = parseInt(targ.style.left, 10);
					maskT = parseInt(targ.style.top, 10);
					if (maskL >= overlayW || maskL <= -maskW || maskT >= overlayH || maskT <= -maskH) {
						removeElement(targ.id);
						targ = document.querySelector("body");
					}
					targ.hold = false;
				}
			}

			function removeElement(id) {
				var elem = document.getElementById(id);
				elem.parentNode.removeChild(elem);
			}

		</script>

		<div id="effects">
			<div id="effect_btns">
				<div id="filter_btn" onclick="clickEffect('filter_btn')" class="effect_btn">
					<img width="auto" height="35px" src="./imgs/resources/filter.svg">
				</div><!-- 
				--><div id="mask_btn" onclick="clickEffect('mask_btn')" class="effect_btn">
					<img width="auto" height="35px" src="./imgs/resources/mask.svg">
				</div><!-- 
				--><div id="frame_btn" onclick="clickEffect('frame_btn')" class="effect_btn">
					<img width="auto" height="35px" src="./imgs/resources/frame.svg">
				</div>
			</div>
			<table id="filter_box" style="display: none">
				<tr>
					<td>Blur</td>
					<td class="td_filter"><input class="filters" min="0" max="20" value="0" step="1" oninput="applyFilter()" data-filter="blur" data-scale="px" type="range"></td>
				</tr>
				<tr>
					<td>Brightness</td>
					<td class="td_filter"><input class="filters" min="0" max="200" value="100" step="1" oninput="applyFilter()" data-filter="brightness" data-scale="%" type="range"></td>
				</tr>
				<tr>
					<td>Contrast</td>
					<td class="td_filter"><input class="filters" min="0" max="200" value="100" step="1" oninput="applyFilter()" data-filter="contrast" data-scale="%" type="range"></td>
				</tr>
				<tr>
					<td>Grayscale?</td>
					<td class="td_filter"><input class="filters" min="0" max="100" value="0" step="1" oninput="applyFilter()" data-filter="grayscale" data-scale="%" type="range"></td>
				</tr>
				<tr>
					<td>Hue Rotate</td>
					<td class="td_filter"><input id="hue" class="filters" min="0" max="360" value="0" step="1" oninput="applyFilter()" data-filter="hue-rotate" data-scale="deg" type="range"></td>
				</tr>
				<tr>
					<td>Invert</td>
					<td class="td_filter"><input class="filters" min="0" max="100" value="0" step="1" oninput="applyFilter()" data-filter="invert" data-scale="%" type="range"></td>
				</tr>
				<tr>
					<td>Opacity</td>
					<td class="td_filter"><input class="filters" min="0" max="100" value="100" step="1" oninput="applyFilter()" data-filter="opacity" data-scale="%" type="range"></td>
				</tr>
				<tr>
					<td>Saturate</td>
					<td class="td_filter"><input class="filters" min="1" max="100" value="100" step="1" oninput="applyFilter()" data-filter="saturate" data-scale="%" type="range"></td>
				</tr>
				<tr>
					<td>Sepia</td>
					<td class="td_filter"><input class="filters" min="0" max="100" value="0" step="1" oninput="applyFilter()" data-filter="sepia" data-scale="%" type="range"></td>
				</tr>
				<tr>
					<td>Fade delay</td>
					<td class="td_filter"><input style="flex-grow: 1" id="fadeDelay" min="0" max="0.99" value="0" step="0.01" data-scale="%" type="range"></td>
				</tr>
			</table>
			<div id="mask_box" style="display: none">
				<?php
					$masks = opendir(dirname(realpath(__FILE__)).'/imgs/masks/');
					$q = "'";
					while($mask = readdir($masks)){
						if($mask !== '.' && $mask !== '..' && $mask !== '.DS_Store'){
							echo '<img onclick="toggleMask('.$q.$mask.$q.')" width="auto" height="200px" src="imgs/masks/'.$mask.'" border="0" />';
						}
					}
					closedir($masks);
				?>
			</div>
			<div id="frame_box" style="display: none">
				<?php
					$frames = opendir(dirname(realpath(__FILE__)).'/imgs/frames/');
					while($frame = readdir($frames)){
						if($frame !== '.' && $frame !== '..' && $frame !== '.DS_Store'){
							echo '<img onclick="toggleFrame('.$q.$frame.$q.')" width="auto" height="200px" src="imgs/frames/'.$frame.'" border="0" />';
						}
					}
					closedir($frames);
				?>
			</div>
		</div>
	</div>
</div>


<script>

function clickEffect($button) {
	btns = document.querySelectorAll('.effect_btn');
	for (i = 0; i < btns.length; i++) {
		btns[i].setAttribute("style", "border-bottom:1px solid; background-color: #7b7b7b;");
	}
	document.getElementById($button).setAttribute("style", "border-bottom:none; background-color: #cccccc;");
	switch ($button) {
		case "filter_btn":
			document.getElementById("mask_box").setAttribute("style", "display:none;");
			document.getElementById("frame_box").setAttribute("style", "display:none;");
			document.getElementById("filter_box").setAttribute("style", "display:table;");
			break;
		case "mask_btn":
			document.getElementById("frame_box").setAttribute("style", "display:none;");
			document.getElementById("filter_box").setAttribute("style", "display:none;");
			document.getElementById("mask_box").setAttribute("style", "display:block;");
			break;
		case "frame_btn":
			document.getElementById("mask_box").setAttribute("style", "display:none;");
			document.getElementById("filter_box").setAttribute("style", "display:none;");
			document.getElementById("frame_box").setAttribute("style", "display:block;");
			break;
	}
}

activePic = 0;

activeRainbow = 0;
refreshIntervalId = 0;
document.querySelector('#rainbow').onclick = function() {
	if (activeRainbow == 0) {
		activeRainbow = 1;
		refreshIntervalId = setInterval(
			function() {
				if (document.querySelector('#hue').value == 360) {
					document.querySelector('#hue').value = 0;
				}
				val = parseInt(document.querySelector('#hue').value) + 1;
				document.querySelector('#hue').value = val;
				applyFilter();
			},
			1
		);
	} else {
		clearInterval(refreshIntervalId);
		activeRainbow = 0;
	}
}

canvas = document.getElementById('canvas');

filterControls = document.querySelectorAll('.filters');

function applyFilter() {
	var computedFilters = '';
	filterControls.forEach(function(item, index) {
		computedFilters += item.getAttribute('data-filter') + '(' + item.value + item.getAttribute('data-scale') + ') ';
	});
	canvas.style.filter = computedFilters;
};

window.onload = function() {
	
	document.onmouseup = stopDrag;
	targ = document.querySelector("body");

	
	

	navigator.getUserMedia  = navigator.getUserMedia ||
							navigator.webkitGetUserMedia ||
							navigator.mozGetUserMedia ||
							navigator.msGetUserMedia;

	
	video = document.createElement('video');
	video.setAttribute('autoplay',true);
	
	ctx = canvas.getContext('2d');
	fadeDelay = document.getElementById('fadeDelay');

	window.vid = video;
	
	function getWebcam() {
		navigator.getUserMedia({ video: true, audio: false }, function(stream) {
			video.srcObject = stream;
			track = stream.getTracks()[0];
		}, function(e) {
			console.error('Rejected!', e);
		});
	}
	
	getWebcam();
	
	var loopFrame;
	
	function loop() {
		// ctx.save();
		// ctx.restore();
		
		ctx.globalAlpha = 1 - Math.pow(fadeDelay.value, 1/3);
		ctx.drawImage(video, 0, 0, width, height);
		
		loopFrame = requestAnimationFrame(loop);
	}
	
	function startLoop() {
		ctx.translate(width, 0);
		ctx.scale(-1, 1);
		loopFrame = loopFrame || requestAnimationFrame(loop);
	}

	overlay = document.querySelector('#overlay');
	
	video.addEventListener('loadedmetadata',function(){
		width = canvas.width = video.videoWidth;
		height = canvas.height = video.videoHeight;
		document.getElementById("canvas").setAttribute("style", "display:block;");
		overlay.setAttribute("style", "width: " + width + "; height: " + height);
		document.querySelector('#frame').setAttribute("style", "width: " + width + "; height: " + height);
		startLoop();
	});
	
	document.querySelector('#snap').onclick = function() {
		activePic = 1;
		cancelAnimationFrame(loopFrame);
		if (refreshIntervalId != 0) {
			clearInterval(refreshIntervalId);
			active = 0;
		}
	}

	document.querySelector('#cancel').onclick = function() {
		if (activePic == 1) {
			activePic = 0;
			loopFrame = requestAnimationFrame(loop);
		}
	}

}

window.onunload = function() {}
</script>
</body>
</html>
