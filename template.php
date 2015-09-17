<?php include('include_config.php'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <?php include('include_head.php'); ?>

    <title><?php echo SITE_TITLE; ?></title>
    <?php include('include_css.php'); ?>
  </head>
  <body>
    <?php include('include_nav.php'); ?>

    <!-- START page content -->
    <div class="container">
      <?php include('include_alerts.php'); ?>
      <h1>My Template</h1>
      <p class="lead">This is a template to be used when creating a new page.</p>
    </div><!-- /.container -->

    <?php include('include_footer.php'); ?>

    <!-- / END Page Content -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include('include_javascript.php'); ?>
  </body>
</html>
