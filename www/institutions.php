<?php require_once('config.php');

define('ADD_NEW', 'Εισάγετε ένα ακόμα σχολείο');
define('ADD_NEW_POSITIONS', '0');
define('ADD_NEW_BUTTON', 'καταχώρηση');
define('ADD_NEW_TITLE', 'Καταχώρηση επόμενου');
define('UPD', 'Αλλαγή');
define('CANCEL', 'Άκυρο');

# Keep this here as long as it stands alone
require_once('db.php');
$db = new ODKDB() or die("Cannot connect to DB");

$changes = 0;
if ($_POST && $_POST['new_institution']) {
    $db->insert_institution($_POST['new_institution'], $_POST['new_positions']);
    unset($_POST['new_institution']);
    unset($_POST['new_positions']);
    $changes++;
}
if ($_POST && $_POST['upd_institution']) {
    $db->update_institution(
      $_POST['id'], $_POST['upd_institution'], $_POST['upd_positions']);
    unset($_POST['id']);
    unset($_POST['upd_institution']);
    unset($_POST['upd_positions']);
    $changes++;
}
if ($_POST && $_POST['delete_institution']) {
    $db->reset_jobs();
    $db->delete_institution_with_applications($_POST['delete_institution']);
    unset($_POST['delete_institution']);
    $changes++;
}
if ($changes > 0) header("Refresh:0");
?>

<head>
  <script type="text/javascript" src="static/js/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="static/bootstrap-3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="static/bootstrap-3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="static/odk.css">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo MENU_INSTITUTIONS; ?></title>
</head>
<html> <?php require("menu.php"); ?>

  <div class="container" id="main">
    <a class="btn btn-default pull-right glyphicon glyphicon-save"
        role="button"
        href="export_csv.php?export=institutions&filename=institutions.csv">
      <?php echo EXPORT_CSV; ?>
    </a>
    <h4 class="col-sm-12 bg-primary">
      <span class="col-sm-8"><?php echo HEAD_INSTITUTIONS; ?></span>
      <span class="col-sm-1"><?php echo HEAD_POSITIONS; ?></span>
      <span class="col-sm-3"><?php echo HEAD_ACTIONS; ?></span>
    </h4>

<?php
foreach ($db->next_institution() as $institution) {
    $id = $institution['institution_id'];
    $name = $institution['name'];
    $positions = $institution['positions'];
?>
    <form class="form-horizontal form-group" id="inst-<?php echo $id; ?>"
        action="./institutions.php">
      <h4 class="col-sm-12">
        <span class="col-sm-8"><?php echo $name; ?></span>
        <span class="col-sm-1"><?php echo $positions; ?></span>
        <button type="button" class="btn btn-warning" data-toggle="modal"
          data-target="#inst-<?php echo $id?>-upd">
          <span class="glyphicon glyphicon-pencil"></span>
        </button>
        <button type="submit" class="btn btn-danger" formmethod="post"
            name="delete_institution" value="<?php echo $id; ?>">
          <span class="glyphicon glyphicon-remove"></span>
        </button>
      </h4>
    </form>
    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"
      aria-labelledby="modal-<?php echo $id; ?>" id="inst-<?php echo $id?>-upd">
    <form class="form-horizontal form-group modal-dialog modal-content"
        id="inst-<?php echo $id?>-upd"action="./institutions.php">
      <div class="modal-body">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
          <div class="col-sm-12">
            <div class="col-sm-10">
              <input type="text" class="form-control" id="upd_institution"
                name="upd_institution" value="<?php echo $name; ?>"
                placeholder="<?php echo ADD_NEW; ?>" required>
            </div>
            <div class="col-sm-2">
              <input type="text" class="form-control" id="upd_positions"
                name="upd_positions" value="<?php echo $positions; ?>"
                placeholder="<?php echo ADD_NEW_POSITIONS; ?>" required>
            </div>
            <div>&nbsp;</div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success" formmethod="post">
              <?php echo UPD; ?>
            </button>
            <button type="button" class="btn btn-warning" name="cancel_upd"
               data-dismiss="modal">
              <?php echo CANCEL; ?>
            </button>
          </div>
        </div>
      </div>
    </form>
    </div>
<?php } ?>
  </div>

  <!-- Add new institution -->
  <div class="container bg-info">
    <h4><?php echo ADD_NEW_TITLE; ?></h4>
    <form class="form-horizontal form-group" id="school-new"
          action="./institutions.php">
      <div class="form-group">
        <div class="col-sm-12">
          <div class="col-sm-8">
            <input type="text" class="form-control" id="new_institution"
                   name="new_institution" required
                   placeholder="<?php echo ADD_NEW; ?>">
          </div>
          <div class="col-sm-1">
            <input type="text" class="form-control" id="new_positions"
                   name="new_positions" required
                   placeholder="<?php echo ADD_NEW_POSITIONS; ?>">
          </div>
          <div class="col-sm-3">
            <button type="submit" class="btn btn-success" formmethod="post">
              <?php echo ADD_NEW_BUTTON; ?>
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>

</html>