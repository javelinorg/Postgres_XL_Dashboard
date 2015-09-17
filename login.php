<?php include('include_config.php'); ?>
<?php
if(isset($_GET['r']) || isset($_SESSION['ERROR'])){
  $strInfo = $_SESSION['ERROR'];
  unset($_SESSION['ERROR']);
}
if(isset($_GET['r'])){
  if($_GET['r'] == 'req'){
    $strWarning = 'Login required to access ' . SITE_TITLE;
  }
}

if(isset($_POST['txtUser']) && isset($_POST['txtPassword'])){
  $strResponse = validateUser($_POST['txtUser'],$_POST['txtPassword']);
  if($strResponse != '1'){
    $strError = 'Odd. For some reason I had a difficult time validating your login.<br>Please try again, and good luck!';
  }else{
    $strSuccess = 'Congrats, you\'ve been authenticated!<br>What would you like to do today?';
    if(isset($_SESSION['REQUEST_URI']) && $_SESSION['REQUEST_URI'] != ''){
      header("Location: http://" . $_SERVER['HTTP_HOST'] . $_SESSION['REQUEST_URI']);
    }else{
      header("Location: http://" . $_SERVER['HTTP_HOST']);
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <?php include('include_head.php'); ?>

    <title><?php echo SITE_TITLE; ?></title>
    <?php include('include_css.php'); ?>
    <!-- bootstrap login -->
    <link href="css/bootstrap-login.css" rel="stylesheet">
  </head>
  <body>
    <!-- START Page Content -->
    <div class="container">
      <?php include('include_alerts.php'); ?>
      <form class="form-signin" method="post" action="login.php">
        <h2 class="form-signin-heading">Login:</h2>
        <label for="txtUser" class="sr-only">User Name</label>
        <input type="text" id="txtUser" name="txtUser" class="form-control" placeholder="User Name" required autofocus>
        <label for="txtPassword" class="sr-only">Password</label>
        <input type="password" id="txtPassword" name="txtPassword" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit"><i class="fa fa-sign-in"></i> Login</button>
      </form>
    </div><!-- /.container -->
    <?php include('include_footer.php'); ?>

    <!-- / END Page Content -->

    <!-- Placed at the end of the document so the pages load faster -->
    <?php include('include_javascript.php'); ?>
  </body>
</html>