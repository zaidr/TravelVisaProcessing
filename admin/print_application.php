<?php 
// Just making it so the order selected will be saved in a session var like the application.php
// expects it to be. This page is only accessible though the admin pages, so its ok! 

session_start();
if (!isset($_GET["orderNum"])) { die("How'd you get here bro? Go back to the home page."); }
$_SESSION["orderNum"] = $_GET["orderNum"];
header("Location: ../application/application.php");
?>