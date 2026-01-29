<?php
if (isset($_POST["message"])) {
    echo json_encode(["reply" => "From post: " . $_POST["message"], "ping" => "pong"]);
}
if (isset($_GET["message"])) {
    echo json_encode(["reply" => "From get: " . $_GET["message"]]);
}
