<?php include('include_config.php'); ?>
<?php
  if(!isset($_GET['n'])){
    $strWarning = 'You must have selected a node to view its configuration.';
  }else{
    $strNode = $_GET['n'];
  }
?>
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
      <?php
        if(isset($strNode)){
          echo '<div class="row">' . "\n";
          echo '  <div class="col-sm-12">' . "\n";
          echo '    <h1>' . $strNode . '<small>' . $arrDBConns[$strNode]['DBIP'] . '</small></h1>' . "\n";
          echo '    <p class="lead">The following settings are displayed via the <strong>SHOW ALL;</strong> command.</p>' . "\n";
          echo '  </div>' . "\n";
          echo '</div>' . "\n";

          $objResults = getDBSystemParam($strNode);
          $intRows = pg_numrows($objResults);
          echo '<div class="row">' . "\n";
          echo '  <div class="list-group">' . "\n";
          for($ri = 0; $ri < $intRows; $ri++){
            $row = pg_fetch_array($objResults, $ri);

            $strParamValue = $row[$strParam];
            // echo '<div class="row">' . "\n";
            // echo '  <div class="col-sm-6"><strong>' . $row['name'] . '</strong></div>' . "\n";
            // echo '  <div class="col-sm-6">' . $row['setting'] . '</div>' . "\n";
            // echo '</div>' . "\n";
            // echo '<div class="row">' . "\n";
            // echo '  <div class="col-sm-12">' . $row['description'] . '</div>' . "\n";
            // echo '</div>' . "\n";

            echo '    <div class="list-group-item">' . "\n";
            echo '      <h4 class="list-group-item-heading">' . $row['name'] . ' <small><i>:' . $row['description'] . '</i></small></h4>' . "\n";
            echo '      <p class="list-group-item-text">' . $row['setting'] . '</p>' . "\n";
            echo '    </div>' . "\n";
          }
            echo '  </div>' . "\n";
            echo '</div>' . "\n";

        }
      ?>

    </div><!-- /.container -->

    <?php include('include_footer.php'); ?>

    <!-- / END Page Content -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include('include_javascript.php'); ?>
  </body>
</html>
