<?php
error_reporting(1);


include("./classes/Downloader.php");

$videoURL = $_REQUEST['videoURL'];

$down = new Downloader($videoURL);

$response = $down->getResponse();


echo json_encode($response);
