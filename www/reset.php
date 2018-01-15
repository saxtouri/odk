<?php require_once('db.php');
$db = new ODKDB() or die("Cannot connect to DB");
if (array_key_exists('type', $_GET) && $_GET['type']=='all') $db->reset();

header("Location: " . $_GET['source']);
?>