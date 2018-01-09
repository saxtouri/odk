<?php require_once('config.php');
define('ADD_NEW', 'Ονοματεπώνυμο');
define('ADD_NEW_POINTS', 'Μόρια');
define('ADD_NEW_BUTTON', 'καταχώρηση');
define('ADD_NEW_TITLE', 'Επόμενη αίτηση');
define('CHOICES_TITLE', 'Σειρά προτιμήσεων');
define('EMPTY_CHOICE', 'Καμία επιλογή');
define('UPD', 'Αλλαγή');
define('CANCEL', 'Άκυρο');


# Keep this here as long as it stands alone
require_once('db.php');
$db = new ODKDB() or die("Cannot connect to DB");

// If something should change, change it and refresh the page
$changes = 0;
if ($_POST && $_POST['new_applicant'] && $_POST['new_points']) {
  $applicant_id = $db->insert_applicant(
    $_POST['new_applicant'], $_POST['new_points']);
  if ($applicant_id) {
    for ($i = 1; $i <= APPL_NUMBER_OF_CHOICES; $i++) {
      $institution_id = $_POST['choice-' . $i];
      if (!$institution_id) break;
      $db->insert_application($applicant_id, $institution_id, $i);
    }
  }
  unset($_POST['new_applicant']);
  unset($_POST['new_points']);
  $changes++;
}
if ($_POST && $_POST['upd_applicant']) {
  $applicant_id = $_POST['upd_applicant_id'];
  $applicant_name = $_POST['upd_applicant'];
  $applicant_points = $_POST['upd_points'];
  $db->start_transaction();
  $r = $db->update_applicant($applicant_id, $applicant_name, $applicant_points);
  $db->clean_applications($applicant_id);
  for ($i = 1; $i <= APPL_NUMBER_OF_CHOICES; $i++) {
    $institution_id = $_POST['choice-' . $i];
    if (!$institution_id) break;
    $db->insert_application($applicant_id, $institution_id, $i);
  }
  $db->end_transaction($r);
}
if ($_POST && $_POST['delete_applicant']) {
  $db->reset_jobs();
  $db->delete_applicant_with_applications($_POST['delete_applicant']);
  unset($_POST['delete_applicant']);
  $changes++;
}
if ($changes > 0) header("Refresh:0");

$institutions = array();
foreach ($db->next_institution() as $institution)
  array_push($institutions, $institution);

function show_choices($applicant_id=NULL) {
  global $db, $institutions;
  $preferences = array();
  if ($applicant_id)
    $preferences = $db->get_institutions_by_preference($applicant_id);
  for ($i = 1; $i <= APPL_NUMBER_OF_CHOICES; $i++) {
    $pref = NULL;
?>
        <div class="col-sm-12">
          <div class="col-sm-2"><label for="choice-<?php echo $i; ?>">
            <?php echo CHOICE . " " . $i; ?>:</label>
          </div>
          <div class="col-sm-8">
            <select class="form-control" id="choice-<?php echo $i; ?>"
                name="choice-<?php echo $i; ?>">
            <?php
            if ($preferences[$i]) {
              $pref = $preferences[$i];
            ?>
              <option value="<?php echo $pref['institution_id']; ?>">
                <?php echo $pref['name']; ?>
              </option>
            <?php } ?>
              <option value=""><?php echo EMPTY_CHOICE; ?></option>
            <?php
            foreach ($institutions as $institution) {
              if ($pref
              && $institution['institution_id'] == $pref['institution_id'])
                continue;?>
              <option value="<?php echo $institution['institution_id']; ?>">
                <?php echo $institution['name']; ?>
              </option>
            <?php } ?>
            </select>
          </div>
        </div>
<?php
  }
} ?>

<head>
  <script type="text/javascript" src="static/js/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="static/bootstrap-3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="static/bootstrap-3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="static/odk.css">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo MENU_APPLICANTS; ?></title>
</head>
<html> <?php require("menu.php"); ?>
  <div class="container">
    <a class="btn btn-default pull-right glyphicon glyphicon-save"
        role="button"
        href="export_csv.php?export=applicants&filename=applicants.csv">
      <?php echo EXPORT_CSV; ?>
    </a>
    <h4 class="col-sm-12 bg-primary">
      <span class="col-sm-8"><?php echo HEAD_APPLICANTS; ?></span>
      <span class="col-sm-1"><?php echo HEAD_POINTS; ?></span>
      <span class="col-sm-3"><?php echo HEAD_ACTIONS; ?></span>
    </h4>

<?php
foreach ($db->next_applicant() as $applicant) {
    $id = $applicant['applicant_id'];
    $name = $applicant['name'];
    $points = $applicant['points'];
?>
    <form class="form-horizontal form-group" id="appl-<?php echo $id; ?>">
      <h4 class="col-sm-12">
        <span class="col-sm-8"><?php echo $name; ?></span>
        <span class="col-sm-1"><?php echo $points; ?></span>
        <button type="button" class="btn btn-warning" data-toggle="modal"
          data-target="#appl-<?php echo $id?>-upd">
          <span class="glyphicon glyphicon-pencil"></span>
        </button>
        <button type="submit" class="btn btn-danger" formmethod="post"
            name="delete_applicant" value="<?php echo $id; ?>">
          <span class="glyphicon glyphicon-remove"></span>
        </button>
      </h4>
    </form>

    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"
      aria-labelledby="modal-<?php echo $id; ?>" id="appl-<?php echo $id?>-upd">
      <form class="form-horizontal form-group modal-dialog modal-content"
          id="appl-upd" action="./applicants.php">
        <div class="form-group modal-body">
          <div class="col-sm-12">
            <div class="col-sm-8">
              <input type="hidden" id="upd_applicant_id" name="upd_applicant_id"
                value="<?php echo $id ?>">
              <input type="text" class="form-control" id="upd_applicant"
                     name="upd_applicant" value="<?php echo $name; ?>"
                     placeholder="<?php echo ADD_NEW; ?>" required>
            </div>
            <div class="col-sm-1">
              <input type="text" class="form-control" id="upd_points"
                     name="upd_points" value="<?php echo $points; ?>"
                     placeholder="<?php echo ADD_NEW_POINTS; ?>" required>
            </div>
          </div>
          <h4 class="col-sm-12"><?php echo CHOICES_TITLE; ?></h4>
          <?php show_choices($id); ?>
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
      </form>
    </div>
<?php } ?>
  </div>

  <!-- Add new application -->
  <div class="container bg-info">
    <h4><?php echo ADD_NEW_TITLE; ?></h4>
    <form class="form-horizontal form-group" id="appl-new"
          action="./applicants.php">
      <div class="form-group">
        <div class="col-sm-12">
          <div class="col-sm-8">
            <input type="text" class="form-control" id="new_applicant"
                   name="new_applicant" required
                   placeholder="<?php echo ADD_NEW; ?>">
          </div>
          <div class="col-sm-1">
            <input type="text" class="form-control" id="new_points"
                   name="new_points" required
                   placeholder="<?php echo ADD_NEW_POINTS; ?>">
          </div>
        </div>
        <h4 class="col-sm-12"><?php echo CHOICES_TITLE; ?></h4>
        <?php show_choices(); ?>
        <div class="col-sm-2">
          <button type="submit" class="btn btn-success" formmethod="post">
             <?php echo ADD_NEW_BUTTON; ?>
          </button>
        </div>
      </div>
    </form>
  </div>
</html>
