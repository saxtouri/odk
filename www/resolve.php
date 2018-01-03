<?php
define('PAGE_TITLE', 'Προτεινόμενη κατανομή κενών');
define('APPLICANTS', 'Αιτούντες');
define('INSTITUTIONS', 'Σχολεία');
define('POINTS', 'Μόρια');
define('CHOICE', 'Προτίμηση');

# Keep this here as long as it stands alone
require_once('db.php');
$db = new ODKDB() or die("Cannot connect to DB");
?>
<head>
  <script type="text/javascript" src="static/js/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="static/bootstrap-3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="static/bootstrap-3.3.7/css/bootstrap.min.css">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo PAGE_TITLE; ?></title>
</head>
<html>
  <div class="container">
    <h4 class="col-sm-12 bg-primary">
      <span class="col-sm-5"><?php echo APPLICANTS; ?></span>
      <span class="col-sm-5"><?php echo INSTITUTIONS; ?></span>
      <span class="col-sm-1"><?php echo POINTS; ?></span>
      <span class="col-sm-1"><?php echo CHOICE; ?></span>
    </h4>
<?php
$institutions = $db->get_institution_positions();
$positioned = array();
foreach ($db->get_applicants_institutions() as $r) {
  $applicant_id = $r["applicant_id"];
  if ($positioned[$applicant_id]) continue;
  $institution_id = $r["institution_id"];
  $institution = $institutions[$institution_id];
  if ($institution && $institution["positions"] > 0) {
    $institution_name = $institution["name"];
    $applicant_name = $r["name"];
    $applicant_points = $r["points"];
    $applicant_preference = $r["preference"];
    $institutions[$institution_id]["positions"]--;
    $positioned[$applicant_id] = $institution_id;
    $db->insert_job($institution_id, $applicant_id);
?>
    <h4 class="col-sm-12">
      <span class="col-sm-5"><?php echo $applicant_name; ?></span>
      <span class="col-sm-5"><?php echo $institution_name; ?></span>
      <span class="col-sm-1"><?php echo $applicant_points; ?></span>
      <span class="col-sm-1"><?php echo $applicant_preference; ?></span>
    </h4>
<?php
  }
}
foreach ($db->next_unpositioned() as $applicant) {
?>
    <h4 class="col-sm-12">
      <span class="col-sm-5"><?php echo $applicant["name"]; ?></span>
      <span class="col-sm-5"></span>
      <span class="col-sm-1"><?php echo $applicant["points"]; ?></span>
      <span class="col-sm-1"></span>
    </h4>
<?php
}
?>
</div>
</html>