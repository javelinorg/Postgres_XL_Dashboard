<?php include('include_config.php'); ?>
<?php
if(isset($_GET['r'])){
  if($_GET['r'] == 'ina'){
    $strInfo = '<div class="alert alert-error">You have been logged out due to session inactivity.</div>';
  }
}
logout();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head.php'); ?>
    <title>LOGOUT: <?php echo SITE_TITLE; ?></title>
    <?php include('include_css.php'); ?>
    <link href="css/bootstrap-login.css" rel="stylesheet">
  </head>
  <body>
    <?php include('nav.php'); ?>
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <?php include('include_alerts.php'); ?>
            <form class="form-signin" method="post" action="login.php">
              <h2 class="form-signin-heading"><?php echo SITE_TITLE; ?></h2>

              <button class="btn btn-large btn-primary" type="submit">Click here to log back in</button>
            </form>
        </div>
      </div>
    </div> <!-- /container -->
    <?php include('include_footer.php'); ?>
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include('include_javascript.php'); ?>
  </body>
</html>
