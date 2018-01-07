<?php require('config.php'); ?>
<nav class="navbar navbar-default navbar-static-top">
  <div class="container">
    <div class="navbar-header">
      <a class="btn navbar-brand" role="button"
        href="index.php"> <?php echo MENU_BRAND; ?></a>
      <a class="btn btn-info navbar-btn" role="button"
        href="institutions.php"> <?php echo MENU_INSTITUTIONS; ?></a>
      <a class="btn btn-info navbar-btn"
        href="applicants.php"> <?php echo MENU_APPLICANTS; ?></a>
      <a class="btn btn-info navbar-btn"
        href="resolve.php"> <?php echo MENU_RESOLVE; ?></a>
      <a class="btn btn-warning navbar-btn" role="button"
        href="reset.php?type=all&source=<?php echo $_SERVER['PHP_SELF']?>">
        <?php echo MENU_RESET_ALL; ?></a>
    </div>
  </div>
</nav>
