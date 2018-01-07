<?php require_once('db.php');
$db = new ODKDB() or die("Cannot connect to DB");
if ($_GET['type'] && $_GET['type']=='all') $db->reset();

header("Location: " . $_GET['source']);
?>