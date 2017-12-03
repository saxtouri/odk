<?php require_once('../db.php');

$db = new ODKDB() or die("Cannot connect to DB");
$db->debug = True;
$db->reset();

$db->insert_applicant(NULL);
$db->insert_applicant("Lalakis", 10);
$db->insert_applicant("Λαλάκης Βούρης");
$db->insert_applicant("Λαλάκης Βούρης", 127);

$db->insert_institution();
$db->insert_institution("126o Δημοτικό Σχολείο Άνω Ράχης https://www.schools.gr/~lele%kos&la=2?ds=2");

$db->insert_job(1);
$db->insert_job(1);
$db->insert_job(1);
$db->insert_job(1);
$db->insert_job(2);
$db->insert_job(2);

$db->insert_application(3, 1, 2);
$db->insert_application(3, 2, 1);
$db->insert_application(2, 1, 1);
$db->insert_application(2, 2, 2);
?>