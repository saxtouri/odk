<?php
  // Original PHP code by Chirp Internet: www.chirp.com.au
  // Please acknowledge use of this code by including this header.

  function cleanData(&$str)
  {
    if($str == 't') $str = 'TRUE';
    if($str == 'f') $str = 'FALSE';
    if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
      $str = "'$str";
    }
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }
?>
<?php require_once('db.php');
$db = new ODKDB() or die("Cannot connect to DB");
require_once('config.php');

$filename = $_GET['filename'] or "output.csv";

header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: text/csv");

$out = fopen("php://output", 'w');

switch($_GET['export']) {

case 'institutions':
  fputcsv($out, ['ID', HEAD_INSTITUTIONS, HEAD_POSITIONS], ',', '"');
  foreach ($db->next_institution() as $institution) {
      cleanData($institution['name']);
      fputcsv($out, array_values($institution), ',', '"');
  }
  break;

case 'applicants':
  $keys = ['ID', HEAD_APPLICANTS, HEAD_POINTS];
  for ($i=0; $i<APPL_NUMBER_OF_CHOICES; $i++)
    array_push($keys, CHOICE . " " . ($i + 1));
  fputcsv($out, $keys, ',', '"');
  $institutions = array();
  foreach ($db->next_institution() as $institution) array_push(
    $institutions, $institution);
  foreach ($db->next_applicant() as $applicant) {
    $applicant_id = $applicant['applicant_id'];
    $name = $applicant['name'];
    cleanData($name);
    $points = $applicant['points'];
    $values = [$applicant_id, $name, $points];
    foreach ($db->get_institutions_by_preference($applicant_id) as $preference) {
      cleanData($preference["name"]);
      array_push($values, $preference["name"]);
    }
    fputcsv($out, $values, ',', '"');
  }
break;

case 'resolve':
  fputcsv(
    $out, [HEAD_APPLICANTS, HEAD_INSTITUTIONS, HEAD_POINTS, CHOICE], ',', '"');
  foreach ($db->next_job() as $j) {
    $values = [
      $j["applicant_name"], $j["institution_name"],
      $j["points"], $j["preference"]
    ];
    fputcsv($out, $values , ',', '"');
  }
  foreach ($db->next_unpositioned() as $u) {
    $values = [$u["name"], '', $u["points"], ''];
    fputcsv($out, $values , ',', '"');
  }
break;
}

fclose($out);
?>