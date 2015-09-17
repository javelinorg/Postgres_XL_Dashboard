<?php include('include_config.php'); ?>
<?php verifyUser(9); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('include_head.php'); ?>
    <title><?php echo SITE_TITLE; ?></title>
    <?php include('include_css.php'); ?>
        <!-- bootstrap carosel -->
    <link href="css/bootstrap-carousel.css" rel="stylesheet">
  </head>
  <body>
    <?php include('include_nav.php'); ?>
    <!-- START Page Content -->
    <div class="container">
      <div class="row">
        <div class="col-lg-4 text-center">
          <div class="panel panel-default">
            <div class="panel-heading"><strong>Coordinators</strong></div>
            <div class="panel-body">
              <?php
                foreach($arrDBConns as $key => $value){
                  if(strtolower($value['DBXLNode']) == 'coordinator'){
                    $strPanelColor = 'success';

                    // check the number of connections
                    $intConnections = getConnections($key);
                    $intMaxConnections = getDBSystemParam($key,'max_connections');
                    if(($intMaxConnections >= $intConnections) && ($intMaxConnections > 0)){
                      $perConnections = round((($intConnections / $intMaxConnections)*100),0);
                    }else{
                      $intConnections = '?';
                      $intMaxConnections = '?';
                      $perConnections = '???';
                      $strPanelColor = 'warning';
                    }
                    // display number of deadlocks
                    $intDeadlocks = getDeadlocks($key);
                    if($intDeadlocks != '0'){
                      $strPanelColor = 'warning';
                    }

                    // try to ping the server
                    if(testPing($value['DBIP'])){
                      $strPing = 'check';
                    }else{
                      $strPing = 'exclamation';
                      $strPanelColor = 'danger';
                    }
                    // check the listed port to see if it responds
                    if(testPort($value['DBIP'], $value['DBPort'])){
                      $strPort = 'check';
                    }else{
                      $strPort = 'exclamation';
                      $strPanelColor = 'danger';
                    }

                    echo '<div class="col-xs-6 text-center">' . "\n";
                    echo '  <div class="panel panel-default panel-' . $strPanelColor . '" style="">' . "\n";
                    echo '    <div class="row">' . "\n";
                    echo '      <div class="col-xs-12 text-center">';
                    if($strPort == 'check'){
                      echo '<a href="environment.php?n=' . $key . '"><i class="fa fa-cubes"></i> <strong>' . $key . '</strong></a></li></div>' . "\n";
                    }else{
                      echo '<i class="fa fa-cubes"></i> <strong>' . $key . '</strong></li></div>' . "\n";
                    }
                    echo '    </div>' . "\n";
                    echo '    <div class="row">' . "\n";
                    echo '      <div class="col-xs-7 text-right">Ping:</div>' . "\n";
                    echo '      <div class="col-xs-5 text-left"><i class="fa fa-' . $strPing . '"></i></div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '    <div class="row">' . "\n";
                    echo '      <div class="col-xs-7 text-right">Port:<small>(' . $value['DBPort'] . ')</small></div>' . "\n";
                    echo '      <div class="col-xs-5 text-left"><i class="fa fa-' . $strPort . '"></i></div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '    <div class="row">' . "\n";
                    if($strPort == 'check'){
                      echo '      <div class="col-xs-7 text-right"><a href="sessions.php?n=' . $key . '"><i class="fa fa-server fa-flip-horizontal"></i> Sessions</a></div>' . "\n";
                    }else{
                      echo '      <div class="col-xs-7 text-right"><i class="fa fa-server fa-flip-horizontal"></i> Sessions</div>' . "\n";
                    }

                    echo '      <div class="col-xs-5 text-left">' . $intConnections . '/' . $intMaxConnections . '</div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '    <div class="row">' . "\n";
                    echo '      <div class="col-xs-7 text-right">Deadlocks:</div>' . "\n";
                    echo '      <div class="col-xs-5 text-left">' . $intDeadlocks . '</div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '  </div>' . "\n";
                    echo '</div>' . "\n";
                  }
                }
              ?>
            </div>
          </div>
        </div>
        <div class="col-lg-4 text-center">
          <div class="panel panel-default">
            <div class="panel-heading"><strong>GTM Proxy</strong></div>
            <div class="panel-body">
              <?php
                foreach($arrDBConns as $key => $value){
                  if(strtolower($value['DBXLNode']) == 'gtm proxy'){
                    $strPanelColor = 'success';
                    // try to ping the server
                    if(testPing($value['DBIP'])){
                      $strPing = 'check';
                    }else{
                      $strPing = 'exclamation';
                      $strPanelColor = 'danger';
                    }
                    // check the listed port to see if it responds
                    if(testPort($value['DBIP'], $value['DBPort'])){
                      $strPort = 'check';
                    }else{
                      $strPort = 'exclamation';
                      $strPanelColor = 'danger';
                    }

                    echo '<div class="col-xs-12 text-center">' . "\n";
                    echo '  <div class="panel panel-default panel-' . $strPanelColor . '" style="">' . "\n";
                    echo '    <div class="row">' . "\n";
                    echo '      <div class="col-xs-12 text-center"><strong>' . $key . '</strong></div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '    <div class="row">' . "\n";
                    echo '      <div class="col-xs-7 text-right">Ping:</div>' . "\n";
                    echo '      <div class="col-xs-5 text-left"><i class="fa fa-' . $strPing . '"></i></div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '    <div class="row">' . "\n";
                    echo '      <div class="col-xs-7 text-right">Port:<small>(' . $value['DBPort'] . ')</small></div>' . "\n";
                    echo '      <div class="col-xs-5 text-left"><i class="fa fa-' . $strPort . '"></i></div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '  </div>' . "\n";
                    echo '</div>' . "\n";
                  }
                }
              ?>
            </div>
          </div>
        </div>
        <div class="col-lg-4 text-center">
          <div class="panel panel-default">
            <div class="panel-heading"><strong>GTM</strong></div>
            <div class="panel-body">
              <?php
                foreach($arrDBConns as $key => $value){
                  if((strtolower($value['DBXLNode']) == 'gtm') || (strtolower($value['DBXLNode']) == 'gtm standby')){
                    $strPanelColor = 'success';
                    // try to ping the server
                    if(testPing($value['DBIP'])){
                      $strPing = 'check';
                    }else{
                      $strPing = 'exclamation';
                      $strPanelColor = 'danger';
                    }
                    // check the listed port to see if it responds
                    if(testPort($value['DBIP'], $value['DBPort'])){
                      $strPort = 'check';
                    }else{
                      $strPort = 'exclamation';
                      $strPanelColor = 'danger';
                    }

                    echo '<div class="col-xs-6 text-center">' . "\n";
                    echo '  <div class="panel panel-default panel-' . $strPanelColor . '" style="">' . "\n";
                    echo '    <div class="row">' . "\n";
                    echo '      <div class="col-xs-12 text-center"><strong>' . $key . '</strong></div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '    <div class="row">' . "\n";
                    echo '      <div class="col-xs-7 text-right">Ping:</div>' . "\n";
                    echo '      <div class="col-xs-5 text-left"><i class="fa fa-' . $strPing . '"></i></div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '    <div class="row">' . "\n";
                    echo '      <div class="col-xs-7 text-right">Port:<small>(' . $value['DBPort'] . ')</small></div>' . "\n";
                    echo '      <div class="col-xs-5 text-left"><i class="fa fa-' . $strPort . '"></i></div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '  </div>' . "\n";
                    echo '</div>' . "\n";
                  }
                }
              ?>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 text-center">
          <div class="panel panel-default">
            <div class="panel-heading"><strong>Datanodes</strong></div>
            <div class="panel-body">
              <?php
                foreach($arrDBConns as $key => $value){
                  if(strtolower($value['DBXLNode']) == 'datanode'){
                    $strPanelColor = 'success';

                    // check the number of connections
                    $intConnections = getConnections($key);
                    $intMaxConnections = getDBSystemParam($key,'max_connections');
                    if(($intMaxConnections >= $intConnections) && ($intMaxConnections > 0)){
                      $perConnections = round((($intConnections / $intMaxConnections)*100),0);
                    }else{
                      $intConnections = '?';
                      $intMaxConnections = '?';
                      $perConnections = '???';
                      $strPanelColor = 'warning';
                    }
                    // display number of deadlocks
                    $intDeadlocks = getDeadlocks($key);
                    if($intDeadlocks != '0'){
                      $strPanelColor = 'warning';
                    }

                    // try to ping the server
                    if(testPing($value['DBIP'])){
                      $strPing = 'check';
                    }else{
                      $strPing = 'exclamation';
                      $strPanelColor = 'danger';
                    }
                    // check the listed port to see if it responds
                    if(testPort($value['DBIP'], $value['DBPort'])){
                      $strPort = 'check';
                    }else{
                      $strPort = 'exclamation';
                      $strPanelColor = 'danger';
                    }

                    echo '<div class="col-xs-6 col-md-3 text-center">' . "\n";
                    echo '  <div class="panel panel-default panel-' . $strPanelColor . '" style="">' . "\n";
                    echo '    <div class="row">' . "\n";
                    echo '      <div class="col-xs-12 text-center"><strong>' . $key . '</strong></div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '    <div class="row">' . "\n";
                    echo '      <div class="col-xs-7 text-right">Ping:</div>' . "\n";
                    echo '      <div class="col-xs-5 text-left"><i class="fa fa-' . $strPing . '"></i></div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '    <div class="row">' . "\n";
                    echo '      <div class="col-xs-7 text-right">Port:<small>(' . $value['DBPort'] . ')</small></div>' . "\n";
                    echo '      <div class="col-xs-5 text-left"><i class="fa fa-' . $strPort . '"></i></div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '    <div class="row">' . "\n";
                    if($strPort == 'check'){
                      echo '      <div class="col-xs-7 text-right"><a href="sessions.php?n=' . $key . '"><i class="fa fa-server fa-flip-horizontal"></i> Sessions</a></div>' . "\n";
                    }else{
                      echo '      <div class="col-xs-7 text-right"><i class="fa fa-server fa-flip-horizontal"></i> Sessions</div>' . "\n";
                    }
                    echo '      <div class="col-xs-5 text-left">' . $intConnections . '/' . $intMaxConnections . '</div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '    <div class="row">' . "\n";
                    echo '      <div class="col-xs-7 text-right">Deadlocks:</div>' . "\n";
                    echo '      <div class="col-xs-5 text-left">' . $intDeadlocks . '</div>' . "\n";
                    echo '    </div>' . "\n";
                    echo '  </div>' . "\n";
                    echo '</div>' . "\n";
                  }
                }
              ?>
            </div>
          </div>
        </div>
      </div>
      <!-- timer -->
      <div class="row">
        <div class="col-sm-12 text-center">
          <div class="btn-group">
          <a class="btn btn-primary" href="index.php"><i class="fa fa-refresh"></i> Refresh in <span id="timer">60 seconds</span></a>
          <a class="btn btn-primary" id="timerpause" value="play"><i class="fa fa-pause"></i> Pause</a>
          </div>
        </div>
      </div>
      <!-- /. timer -->
    </div><!-- /.container -->

     <!-- FOOTER -->
    <?php include('include_footer.php'); ?>

    <!-- / END Page Content -->



    <!-- Placed at the end of the document so the pages load faster -->
    <?php include('include_javascript.php'); ?>
  </body>
</html>
