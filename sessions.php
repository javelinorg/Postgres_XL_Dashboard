<?php include('include_config.php'); ?>
<?php
  if(isset($_GET['n'])){
    if(isset($_GET['pid'])){
#      $strInfo = killDBConnection($_GET['db'],$_GET['pid']);
    }
    $strTable = getSessions($_GET['n']);
#    $strTable = '<table></table>';
  }else{
    $strInfo = 'Please select a Coordinator or Datanode from the dropdown.';
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
        <div class="btn-group">
          <a class="btn btn-primary" href="#" data-toggle="dropdown"><i class="fa fa-search"></i> Choose A Node</a>
          <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
          <ul class="dropdown-menu">
          <?php
            foreach($arrDBConns as $key => $value){
              if((strtolower($value['DBXLNode']) == 'coordinator') || (strtolower($value['DBXLNode']) == 'datanode')) {
                if(testPort($value['DBIP'], $value['DBPort'])){
                  echo '<li><a href="sessions.php?n=' . $key . '"><i class="fa fa-server fa-flip-horizontal"></i> ' . $key . '</a></li>' . "\n";
                }
              }
            }
          ?>
          </ul>
        </div>
        <?php
          if(isset($strTable)){
            echo '  <div class="btn-group">' . "\n";
            echo '    <a class="btn btn-primary" href="sessions.php?n=' . $_GET['n'] . '"><i class="fa fa-refresh"></i> Refresh in <span id="timer">60 seconds</span></a>' . "\n";
            echo '    <a class="btn btn-primary" id="timerpause" value="play"><i class="fa fa-pause"></i> Pause</a>' . "\n";
            echo '  </div>' . "\n";
            echo $strTable;
          }
        ?>

    </div><!-- /.container -->

    <?php include('include_footer.php'); ?>

    <!-- / END Page Content -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include('include_javascript.php'); ?>
  </body>
</html>
