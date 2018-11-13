<?php
require("./connect.php");

$headers = getallheaders();
if ($headers["Content-type"] == "application/json") {
    $info = json_decode(file_get_contents("php://input"), true);
    $user = new USER("name", $info["user"]);
    $imgdirname = "./imgs/users/" . $info["user"] . ($user->get_last_pic_id() + 1) . ".png";
    $img = substr($info["image"], 22);
    file_put_contents($imgdirname, base64_decode($img));

    $dst = imagecreatefrompng($imgdirname);

    if (isset($info["frame"])) {
        $src = imagecreatefrompng("./imgs/frames/" . $info["frame"]);
        list($orig_width, $orig_height) = getimagesize("./imgs/frames/" . $info["frame"]);
        list($tot_width, $tot_height) = getimagesize($imgdirname);
        imagecopyresampled(
            $dst, $src, 
            0,
            0,
            0,
            0,
            $tot_width,
            $tot_height,
            $orig_width,
            $orig_height
        );
        imagepng($dst, $imgdirname);
    }

    if (sizeof($info["masks"]) != 0) {
        foreach ($info["masks"] as $mask_details) {
            $dst = imagecreatefrompng($imgdirname);
            $src = imagecreatefrompng("./imgs/masks/" . $mask_details["file"]);
            list($orig_width, $orig_height) = getimagesize("./imgs/masks/" . $mask_details["file"]);
            imagecopyresampled(
                $dst, $src, 
                intval($mask_details["left"]),
                intval($mask_details["top"]),
                0,
                0,
                intval($mask_details["width"]),
                intval($mask_details["height"]),
                $orig_width,
                $orig_height
            );
            imagepng($dst, $imgdirname);
        }
    }

    $user->upload_picture($imgdirname);
}
?>