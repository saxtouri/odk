<?php
define('INSERT_TITLE', 'Σχολεία');
define('ADD_NEW', 'Εισάγετε ένα ακόμα σχολείο');
define('ADD_NEW_BUTTON', 'καταχώρηση');
define('ADD_NEW_TITLE', 'Καταχώρηση επόμενου σχολείου');

# Keep this here as long as it stands alone
require_once('db.php');
$db = new ODKDB() or die("Cannot connect to DB");
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
    <form class="form-horizontal form-group" id="inst-1">
      <h4 class="col-sm-12">Institution 1&nbsp;
        <button type="submit" class="btn btn-warning">
          <span class="glyphicon glyphicon-pencil"></span>
        </button>
        <button type="submit" class="btn btn-danger">
          <span class="glyphicon glyphicon-remove"></span>
        </button>
      </h4>
    </form>
    <form class="form-horizontal form-group" id="inst-2">
      <h4 class="col-sm-12">Institution 2&nbsp;
        <button type="submit" class="btn btn-warning">
          <span class="glyphicon glyphicon-pencil"></span>
        </button>
        <button type="submit" class="btn btn-danger">
          <span class="glyphicon glyphicon-remove"></span>
        </button>
      </h4>
    </form>
  </div>

  <!-- Add new instituion -->
<?php
if ($_POST && $_POST['new_institution']) {
    $db->insert_institution($_POST['new_institution']);
    unset($_POST['new_institution']);
}
?>
  <div class="container bg-info">
    <h4><?php echo ADD_NEW_TITLE; ?></h4>
    <form class="form-horizontal form-group" id="school-new"
          action="./institutions.php">
      <div class="form-group">
        <div class="col-sm-10">
          <input type="text" class="form-control" id="new_institution"
                 name="new_institution" placeholder="<?php echo ADD_NEW; ?>">
        </div>
        <div class="col-sm-2">
          <button type="submit" class="btn btn-success" formmethod="post">
            <span class="glyphicon glyphicon-ok">&nbsp;<?php echo ADD_NEW_BUTTON; ?></span>
          </button>
        </div>
      </div>
    </form>
  </div>

</html>