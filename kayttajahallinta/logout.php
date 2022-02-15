<?php
/* logout.php slackista*/
if (!session_id()) session_start();
/* Vaihtoehtoisia tapoja:
if(session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION)) session_start();
if(session_id() === "") session_start(); */
$_SESSION = array();
session_destroy();
header('location: ../etusivu.php');
?>