<?php

	require("./account_bar.php");
	// session_start();
	// $user = new USER();
	// $account = $user->get_acc_by_name($_SESSION["account"]);


	// $handle = opendir(dirname(realpath(__FILE__)).'/imgs/masks/');
	// while($file = readdir($handle)){
	// 	if($file !== '.' && $file !== '..' && $file !== '.DS_Store'){
	// 		echo '<img src="imgs/masks/'.$file.'" border="0" />';
	// 	}
	// }
?>
		<style>
		#camera_buttons, #effects, video, canvas {
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
		#camera_buttons {
			text-align: center;
			border: 3px solid;
			border-top: none;
			background-color: #cccccc;
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
		.overlay {
			background-color: pink;
			opacity: 0.5;
			position: absolute;
			top: 3px;
			left:3px;
		}
		#test {
			position: relative;
			/* top: 0px;
			left: 0px; */
		}
		</style>
		
		<div style="margin-top: 100px;">
			<div id="cambox">
				<div style="position: relative">
					<canvas id="canvas" style="display:none"></canvas>
					<div class="overlay">
						<!-- <img onclick="clickEvent(event)" id="test" ondragstart="return false" draggable="true" width="auto" height="200px" src="imgs/masks/154099275565129024-6.png"> -->
						<img id="test" style="top: 0px; left: 0px;" ondragstart="startDragMask(event)" draggable="true" width="auto" height="200px" src="imgs/masks/154099275565129024-6.png">
					</div>
					<div id="camera_buttons">
						<button class="buttpic" id="rainbow" style="background: url(./imgs/rainbow.svg)"></button>
						<button class="buttpic" id="snap" style="background: url(./imgs/camera.svg)"></button>
						<button class="buttpic" id="cancel" style="background: url(./imgs/trash.png); background-size: cover"></button>
					</div>
				</div>
				<script>

					var mouseDown = 0;
					document.body.onmousedown = function() { 
						mouseDown = 1;
					}
					document.body.onmouseup = function() {
						mouseDown = 0;
					}

					test = document.querySelector("#test");
					function startDragMask(e) {
						e.preventDefault();
						// e = Mouse click event.
						// var rect = e.target.getBoundingClientRect();
						targ = e.target;
						// var x = e.clientX - rect.left; //x position within the element.
						// var y = e.clientY - rect.top;  //y position within the element.
						startX = e.clientX;
						startY = e.clientY;
						startLeft = parseInt(test.style.left, 10);
						startTop = parseInt(test.style.top, 10);
						console.log("x:" + startX + " - y:" + startY);
						drag = true;
						document.onmousemove = function(event) {dragMask(event)};
						return false;
					}

					function dragMask(e) {
						if (!drag) {return};
						var nowX = e.clientX;
						var nowY = e.clientY;
						targ.style.left= startLeft + (nowX-startX)+'px';
						targ.style.top= startTop + (nowY-startY)+'px';
						return false;
					}

					function stopDrag() {
						drag=false;
					}
					// test.addEventListener("mousedown", function(e) {
					// 	console.log("mouse location:", e.clientX, e.clientY);
					// 	var rect = e.target.getBoundingClientRect();
					// 	var topp = window.getComputedStyle(test).top;
					// 	// console.log(parseInt(test.style.top));
					// 	// console.log(test.style.top);
					// 	console.log(topp);
					// 	// var a = test.style.top.substring(0, test.style.top.length - 2);
					// 	a = parseInt(topp, 10);
					// 	console.log(a);
					// 	test.style.top = a + 10;
					// });
				</script>
				<script>
					// test = document.querySelector("#test");
					// function clickEvent(e) {
					// 	// e = Mouse click event.
					// 	var rect = e.target.getBoundingClientRect();
					// 	var x = e.clientX - rect.left; //x position within the element.
					// 	var y = e.clientY - rect.top;  //y position within the element.
					// 	console.log("x:" + x + "- y:" + y);
					// }
					// test.addEventListener("mousedown", function(e) {
					// 	console.log("mouse location:", e.clientX, e.clientY);
					// 	var rect = e.target.getBoundingClientRect();
					// 	var topp = window.getComputedStyle(test).top;
					// 	// console.log(parseInt(test.style.top));
					// 	// console.log(test.style.top);
					// 	console.log(topp);
					// 	// var a = test.style.top.substring(0, test.style.top.length - 2);
					// 	a = parseInt(topp, 10);
					// 	console.log(a);
					// 	test.style.top = a + 10;
					// });
				</script>
				<div id="effects">
					<div id="effect_btns">
						<div id="filter_btn" onclick="clickEffect('filter_btn')" class="effect_btn">
							<img width="auto" height="35px" src="./imgs/filter.svg">
						</div><!-- 
						--><div id="mask_btn" onclick="clickEffect('mask_btn')" class="effect_btn">
							<img width="auto" height="35px" src="./imgs/mask.svg">
						</div><!-- 
						--><div id="frame_btn" onclick="clickEffect('frame_btn')" class="effect_btn">
							<img width="auto" height="35px" src="./imgs/frame.svg">
						</div>
					</div>
					<table id="filter_box" style="display: none">
						<!-- <tr>
							<th>
								<img width="auto" height="35px" class="cute_ghost" src="./imgs/filter.svg">
							</th>
							<th>
								Filters
							</th>
						</tr> -->
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
							while($mask = readdir($masks)){
								if($mask !== '.' && $mask !== '..' && $mask !== '.DS_Store'){
									echo '<img width="auto" height="200px" src="imgs/masks/'.$mask.'" border="0" />';
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
									echo '<img width="auto" height="200px" src="imgs/frames/'.$frame.'" border="0" />';
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

		var filterControls = document.querySelectorAll('.filters');
		function applyFilter() {
			var computedFilters = '';
			filterControls.forEach(function(item, index) {
				computedFilters += item.getAttribute('data-filter') + '(' + item.value + item.getAttribute('data-scale') + ') ';
			});
			canvas.style.filter = computedFilters;
		};

		window.onload = function() {
			
			document.onmouseup = stopDrag;

			
			

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

			overlays = document.querySelectorAll('.overlay');
			
			video.addEventListener('loadedmetadata',function(){
				width = canvas.width = video.videoWidth;
				height = canvas.height = video.videoHeight;
				document.getElementById("canvas").setAttribute("style", "display:block;");
				startLoop();
				for (i = 0; i < overlays.length; i++) {
					overlays[i].setAttribute("style", "width: " + width + "; height: " + height);
				}
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
