<?php
define('INSERT_TITLE', 'Αιτούντες');
define('ADD_NEW', 'Ονοματεπώνυμο');
define('ADD_NEW_POINTS', 'Μόρια');
define('ADD_NEW_BUTTON', 'καταχώρηση');
define('ADD_NEW_TITLE', 'Επόμενη αίτηση');
define('APPLICANTS', 'Αιτούντες');
define('POINTS', 'Μόρια');
define('ACTIONS', ' ');

# Keep this here as long as it stands alone
require_once('db.php');
$db = new ODKDB() or die("Cannot connect to DB");

// If something should change, change it and refresh the page
$changes = 0;
if ($_POST && $_POST['new_applicant'] && $_POST['new_points']) {
    $db->insert_applicant($_POST['new_applicant'], $_POST['new_points']);
    unset($_POST['new_applicant']);
    unset($_POST['new_points']);
    $changes++;
}
if ($_POST && $_POST['delete_applicant']) {
    $r = $db->delete_applicant($_POST['delete_applicant']);
    unset($_POST['delete_applicant']);
    $changes++;
}
if ($changes > 0) header("Refresh:0");
?>

<head>
  <script type="text/javascript" src="static/js/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="static/bootstrap-3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="static/bootstrap-3.3.7/css/bootstrap.min.css">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo INSERT_TITLE; ?></title>
</head>
<html>
  <div class="container">
    <h4 class="col-sm-12 bg-primary">
      <span class="col-sm-8"><?php echo APPLICANTS; ?></span>
      <span class="col-sm-1"><?php echo POINTS; ?></span>
      <span class="col-sm-3"><?php echo ACTIONS; ?></span>
    </h4>

<?php
foreach ($db->next_applicant() as $applicant) {
    $id = $applicant['applicant_id'];
    $name = $applicant['name'];
    $points = $applicant['points']
?>
    <form class="form-horizontal form-group" id="appl-<?php echo $id; ?>">
      <h4 class="col-sm-12">
        <span class="col-sm-8"><?php echo $name; ?></span>
        <span class="col-sm-1"><?php echo $points; ?></span>
        <button type="submit" class="btn btn-warning">
          <span class="glyphicon glyphicon-pencil"></span>
        </button>
        <button type="submit" class="btn btn-danger" formmethod="post"
            name="delete_applicant" value="<?php echo $id; ?>">
          <span class="glyphicon glyphicon-remove"></span>
        </button>
      </h4>
    </form>
<?php } ?>
  </div>

  <!-- Add new application -->
  <div class="container bg-info">
    <h4><?php echo ADD_NEW_TITLE; ?></h4>
    <form class="form-horizontal form-group" id="school-new"
          action="./applicants.php">
      <div class="form-group">
        <div class="col-sm-12">
          <div class="col-sm-8">
            <input type="text" class="form-control" id="new_applicant"
                   name="new_applicant"
                   placeholder="<?php echo ADD_NEW; ?>">
          </div>
          <div class="col-sm-1">
            <input type="text" class="form-control" id="new_points"
                   name="new_points"
                   placeholder="<?php echo ADD_NEW_POINTS; ?>">
          </div>
        </div>
        <h4 class="col-sm-12">Choices in order</h4>
<?php
for ($i = 1; $i <= 5; $i++) {
  $institutions = array();
  foreach ($db->next_institution() as $institution)
    array_push($institutions, $institution)
?>
        <div class="col-sm-12">
          <div class="col-sm-2"><label for="choice-<?php echo $i; ?>">
            Choice <?php echo $i; ?>:</label>
          </div>
          <div class="col-sm-8">
            <select class="form-control" id="choice-<?php echo $i; ?>">
              <option>Empty</option>
            <?php foreach ($institutions as $institution) { ?>
              <option><?php echo $institution['name']; ?></option>
            <?php } ?>
            </select>
          </div>
        </div>
<?php } ?>
        <div>&nbsp;</div>
        <div class="col-sm-12"><div class="col-sm-2">
          <button type="submit" class="btn btn-success" formmethod="post">
            <span class="glyphicon glyphicon-ok">&nbsp;<?php echo ADD_NEW_BUTTON; ?></span>
          </button>
        </div></div>
      </div>
    </form>
  </div>
</html>
