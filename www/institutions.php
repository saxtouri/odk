<?php
define('INSERT_TITLE', 'Σχολεία');
define('ADD_NEW', 'Εισάγετε ένα ακόμα σχολείο');
define('ADD_NEW_POSITIONS', '0');
define('ADD_NEW_BUTTON', 'καταχώρηση');
define('ADD_NEW_TITLE', 'Καταχώρηση επόμενου');
define('INSTITUTIONS', 'Σχολεία');
define('POSITIONS', 'Κενά');
define('ACTIONS', ' ');

# Keep this here as long as it stands alone
require_once('db.php');
$db = new ODKDB() or die("Cannot connect to DB");

$changes = 0;
if ($_POST && $_POST['new_institution']) {
    $institution = $_POST['new_institution'];
    $positions = $_POST['new_positions'];
    $institution_id = $db->insert_institution($institution, $positions);
    if ($institution_id && $positions > 0) $db->insert_job($institution_id);
    unset($_POST['new_institution']);
    unset($_POST['new_positions']);
    $changes++;
}
if ($_POST && $_POST['delete_institution']) {
    $r = $db->delete_institution($_POST['delete_institution']);
    unset($_POST['delete_institution']);
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
      <span class="col-sm-8"><?php echo INSTITUTIONS; ?></span>
      <span class="col-sm-1"><?php echo POSITIONS; ?></span>
      <span class="col-sm-3"><?php echo ACTIONS; ?></span>
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
        <button type="submit" class="btn btn-warning" name="lala">
          <span class="glyphicon glyphicon-pencil"></span>
        </button>
        <button type="submit" class="btn btn-danger" formmethod="post"
            name="delete_institution" value="<?php echo $id; ?>">
          <span class="glyphicon glyphicon-remove"></span>
        </button>
      </h4>
    </form>
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
        </div>
        <div class="col-sm-3">
          <button type="submit" class="btn btn-success" formmethod="post">
            <span class="glyphicon glyphicon-ok">&nbsp;<?php echo ADD_NEW_BUTTON; ?></span>
          </button>
        </div>
      </div>
    </form>
  </div>

</html>