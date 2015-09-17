<?php
// template function built to
// loop through each database available
// to the specified connection $strDB
function loopDatabase($strDB){
  GLOBAL $arrDBConns;
  if(testPort($arrDBConns[$strDB]['DBIP'],$arrDBConns[$strDB]['DBPort'],.5)){
      $objConn = pg_connect('host=' . $arrDBConns[$strDB]['DBIP'] . ' port=' . $arrDBConns[$strDB]['DBPort'] . ' dbname=' . $arrDBConns[$strDB]['DBName'] . ' user=' . $arrDBConns[$strDB]['DBUser'] . ' password=' . $arrDBConns[$strDB]['DBPass']);
    if(!$objConn){
      return '<div class="alert alert-error">Error connecting to <strong>' . $strDB . '</strong>!</div>';
    }
    $strSQL = 'SELECT datname AS dbname
               FROM pg_database
               WHERE datistemplate = FALSE';
    $objResults = pg_exec($objConn, $strSQL);
    if(!$objResults){
      return '<div class="alert alert-error">Error getting database list from <strong>' . $strDB . '</strong>!</div>';
    }
    $intRows = pg_numrows($objResults);
    $intDatabases = $intRows;
    // Loop databases in the result set.
    for($ri = 0; $ri < $intRows; $ri++){
      $row = pg_fetch_array($objResults, $ri);
      // for each database create a second connection
      echo $row['dbname'];
    }
  }else{
    return '<div>Could not validate port is active on database server:' . $strDB . '. As a result, I could not connect and get your data.</div>';
  }
}

// loop through the databases and getting the top tables
// by calling another function which does the summerization of the tables
function getTopTableSizes($strDB, $intTop = 10){
  GLOBAL $arrDBConns;
  if(testPort($arrDBConns[$strDB]['DBIP'],$arrDBConns[$strDB]['DBPort'],.5)){
      $objConn = pg_connect('host=' . $arrDBConns[$strDB]['DBIP'] . ' port=' . $arrDBConns[$strDB]['DBPort'] . ' dbname=' . $arrDBConns[$strDB]['DBName'] . ' user=' . $arrDBConns[$strDB]['DBUser'] . ' password=' . $arrDBConns[$strDB]['DBPass']);
    if(!$objConn){
      $arrDBTableSizes = array('ERROR Connecting' => 1);
      return $arrDBTableSizes;
    }
    $strSQL = 'SELECT datname AS dbname
               FROM pg_database
               WHERE datistemplate = FALSE';
    $objResults = pg_exec($objConn, $strSQL);
    if(!$objResults){
      pg_close($objConn);
      $arrDBTableSizes = array('ERROR Connecting' => 1);
      return $arrDBTableSizes;
    }
    $intRows = pg_numrows($objResults);
    $intDatabases = $intRows;
    $arrDBTableSizes = array();

    // Loop databases in the result set.
    for($ri = 0; $ri < $intRows; $ri++){
      $row = pg_fetch_array($objResults, $ri);
      // for each database create a second connection
      $objDBResults = getDatabaseTableSizes($strDB, $row['dbname']);
      $intDBRows = pg_numrows($objDBResults);
      for($r = 0; $r < $intDBRows; $r++){
        $dbrow = pg_fetch_array($objDBResults, $r);
        $arrDBTableSizes[$row['dbname'] . '.' . $dbrow['schema_name'] . '.' . $dbrow['table_name']]['data'] = $dbrow['table_size'];
        $arrDBTableSizes[$row['dbname'] . '.' . $dbrow['schema_name'] . '.' . $dbrow['table_name']]['index'] = $dbrow['indexes_size'];
        $arrDBTableSizes[$row['dbname'] . '.' . $dbrow['schema_name'] . '.' . $dbrow['table_name']]['total'] = $dbrow['total_size'];
      }
    }
    foreach($arrDBTableSizes AS $key => $row){
      $arrDBTable[$key] = $row['total'];
    }
    array_multisort($arrDBTable, SORT_DESC, $arrDBTableSizes);

    return array_slice($arrDBTableSizes, 0, $intTop, true);
#    print_r($arrDBTableSizesarrDBTableSizesarrDBTableSizes);
  }else{
    $arrDBTableSizesarrDBTableSizes = array('ERROR Connecting' => 1);
    return $arrDBTableSizes;
  }
}

// loop through the databases and getting the total size
// by calling another function which does the summerization of the tables
function getDatabaseSizes($strDB){
  GLOBAL $arrDBConns;
  if(testPort($arrDBConns[$strDB]['DBIP'],$arrDBConns[$strDB]['DBPort'],.5)){
      $objConn = pg_connect('host=' . $arrDBConns[$strDB]['DBIP'] . ' port=' . $arrDBConns[$strDB]['DBPort'] . ' dbname=' . $arrDBConns[$strDB]['DBName'] . ' user=' . $arrDBConns[$strDB]['DBUser'] . ' password=' . $arrDBConns[$strDB]['DBPass']);
    if(!$objConn){
      $arrDBSizes = array('ERROR Connecting' => 1);
      return $arrDBSizes;
    }
    $strSQL = 'SELECT datname AS dbname
               FROM pg_database
               WHERE datistemplate = FALSE';
    $objResults = pg_exec($objConn, $strSQL);
    if(!$objResults){
      pg_close($objConn);
      $arrDBSizes = array('ERROR Connecting' => 1);
      return $arrDBSizes;
    }
    $intRows = pg_numrows($objResults);
    $intDatabases = $intRows;
    $arrDBSizes = array();

    // Loop databases in the result set.
    for($ri = 0; $ri < $intRows; $ri++){
      $row = pg_fetch_array($objResults, $ri);
      $intDatabaseSize = 0;
      // for each database create a second connection
      $objDBResults = getDatabaseTableSizes($strDB, $row['dbname']);
      $intDBRows = pg_numrows($objDBResults);
      for($r = 0; $r < $intDBRows; $r++){
        $dbrow = pg_fetch_array($objDBResults, $r);
        $intDatabaseSize = $intDatabaseSize + $dbrow['total_size'];
      }
      $arrDBSizes[$row['dbname']] = $intDatabaseSize;
    }
    return $arrDBSizes;
#    print_r($arrDBSizes);
  }else{
    $arrDBSizes = array('ERROR Connecting' => 1);
    return $arrDBSizes;
  }
}

// function to get schema, table, index sizes
function getDatabaseTableSizes($strDB, $strDBName, $strDBSchema = '', $strDBTable = ''){
  GLOBAL $arrDBConns;
  if(testPort($arrDBConns[$strDB]['DBIP'],$arrDBConns[$strDB]['DBPort'],.5)){
      $objConn = pg_connect('host=' . $arrDBConns[$strDB]['DBIP'] . ' port=' . $arrDBConns[$strDB]['DBPort'] . ' dbname=' . $strDBName . ' user=' . $arrDBConns[$strDB]['DBUser'] . ' password=' . $arrDBConns[$strDB]['DBPass']);
    if(!$objConn){
      return '<div class="alert alert-error">Error connecting to <strong>' . $strDB . '</strong>!</div>';
    }
    $strSQL = "SELECT schema_name
                     ,table_name
                     ,table_size
                     ,indexes_size
                     ,total_size
               FROM (SELECT ns.nspname AS schema_name
                           ,cl.relname AS table_name
                           ,pg_table_size(cl.oid) AS table_size
                           ,pg_indexes_size(cl.oid) AS indexes_size
                           ,pg_total_relation_size(cl.oid) AS total_size
                     FROM pg_class AS cl
                       INNER JOIN pg_catalog.pg_namespace AS ns
                         ON ns.oid = cl.relnamespace
                     WHERE cl.relkind = 'r'
                       AND ns.nspname NOT IN ('pg_catalog','information_schema')";
    if($strDBSchema != ''){
      $strSQL .= " AND ns.nspname = '" . $strDBSchema . "' ";
    }
    if($strDBTable != ''){
      $strSQL .= " AND cl.relname = '" . $strDBTable ."' ";
    }
    $strSQL .= ") AS ts
               ORDER BY ts.total_size DESC";
    $objResults = pg_exec($objConn, $strSQL);
    if(!$objResults){
      pg_close($objConn);
      return '<div class="alert alert-error">Error getting database list from <strong>' . $strDB . '</strong>!</div>';
    }
    return $objResults;
  }else{
    return '<div>Could not validate port is active on database server:' . $strDB . '. As a result, I could not connect and get your data.</div>';
  }
}

// will hit the coordinator and pull the results from pgxc_node table
function getCoordinatorNodeTable($strDB){
  GLOBAL $arrDBConns;
  $strTable = '';
  if(testPort($arrDBConns[$strDB]['DBIP'],$arrDBConns[$strDB]['DBPort'],.5)){
      $objConn = pg_connect('host=' . $arrDBConns[$strDB]['DBIP'] . ' port=' . $arrDBConns[$strDB]['DBPort'] . ' dbname=' . $arrDBConns[$strDB]['DBName'] . ' user=' . $arrDBConns[$strDB]['DBUser'] . ' password=' . $arrDBConns[$strDB]['DBPass']);
    if(!$objConn){
      return '<div class="alert alert-error">Error connecting to <strong>' . $strDB . '</strong>!</div>';
    }
    $strSQL = "SELECT *
               FROM pgxc_node
               ORDER BY node_type
                       ,node_name";
    $objResults = pg_exec($objConn, $strSQL);
    if(!$objResults){
      return '<div class="alert alert-error">Error getting database list from <strong>' . $strDB . '</strong>!</div>';
    }
    $intRows = pg_numrows($objResults);
    $intDatabases = $intRows;
    // Loop databases in the result set.
    for($ri = 0; $ri < $intRows; $ri++){
      $row = pg_fetch_array($objResults, $ri);
      // lets look into the enviroment and build a table

      // try to ping the server
      $strRowColor = 'success';
      if(testPing($row['node_host'])){
        $strPing = 'check';
      }else{
        $strPing = 'exclamation';
        $strRowColor = 'danger';
      }
      // check the listed port to see if it responds
      if(testPort($row['node_host'], $row['node_port'])){
        $strPort = 'check';
      }else{
        $strPort = 'exclamation';
        $strRowColor = 'danger';
      }
      $strTable .= '<tr class="' . $strRowColor . '">' . "\n";
      $strTable .= '  <td>' . $row['node_id'] . '</td>' . "\n";
      $strTable .= '  <td>' . $row['node_name'] . '</td>' . "\n";
      $strTable .= '  <td>';
      if($row['node_type'] == 'C'){
        $strTable .= 'Coordinator';
      }
      if($row['node_type'] == 'D'){
        $strTable .= 'Datanode';
      }
      $strTable .= '  </td>' . "\n";
      $strTable .= '  <td>' . $row['node_host'] . '</td>' . "\n";
      $strTable .= '  <td align="center"><i class="fa fa-' . $strPing . '"></i> </td>' . "\n";
      $strTable .= '  <td><i class="fa fa-' . $strPort . '"></i> ' . $row['node_port'] . '</td>' . "\n";
      $strTable .= '  <td align="center">' . $row['nodeis_primary'] . '</td>' . "\n";
      $strTable .= '  <td align="center">' . $row['nodeis_preferred'] . '</td>' . "\n";
      $strTable .= '</tr>' . "\n";
    }
    $strTableHeader = '<div class="table-responsive">' . "\n";
    $strTableHeader .= '<table class="table table-hover table-condensed table-striped" width="100%">' . "\n";
    $strTableHeader .= '<thead>' . "\n";
    $strTableHeader .= '<th>ID</th>' . "\n";
    $strTableHeader .= '<th>Node Name</th>' . "\n";
    $strTableHeader .= '<th>Type</th>' . "\n";
    $strTableHeader .= '<th>Hostname</th>' . "\n";
    $strTableHeader .= '<th align="center">Ping</th>' . "\n";
    $strTableHeader .= '<th align="center">Port</th>' . "\n";
    $strTableHeader .= '<th align="center">Is Primary?</th>' . "\n";
    $strTableHeader .= '<th align="center">Is Preferred?</th>' . "\n";
    $strTableHeader .= '</thead>' . "\n";
    $strTable .= '</table>' . "\n";
    $strTable .= '</div>' . "\n";
    return $strTableHeader . $strTable;
  }else{
    return '<div>Could not validate port is active on database server:' . $strDB . '. As a result, I could not connect and get your data.</div>';
  }
}

// a loop wrapper that builds the sessions table
// but unlike normal postgres you need to connect to each
// database and get each of their sessions
// this function does that!
function getSessions($strDB,$blnShowSQL = false){
  GLOBAL $arrDBConns;
  $intSessions = 0;
  $intDatabases = 0;
  $strInfo = '';
  $strTableHeader = '';
  $strTable = '';
  if(testPort($arrDBConns[$strDB]['DBIP'],$arrDBConns[$strDB]['DBPort'],.5)){
      $objConn = pg_connect('host=' . $arrDBConns[$strDB]['DBIP'] . ' port=' . $arrDBConns[$strDB]['DBPort'] . ' dbname=' . $arrDBConns[$strDB]['DBName'] . ' user=' . $arrDBConns[$strDB]['DBUser'] . ' password=' . $arrDBConns[$strDB]['DBPass']);
    if(!$objConn){
      return '<div class="alert alert-error">Error connecting to <strong>' . $strDB . '</strong>!</div>';
    }
    $strSQL = 'SELECT datname AS dbname
               FROM pg_database
               WHERE datistemplate = FALSE';
    $objResults = pg_exec($objConn, $strSQL);
    if(!$objResults){
      return '<div class="alert alert-error">Error getting database list from <strong>' . $strDB . '</strong>!</div>';
    }
    $intRows = pg_numrows($objResults);
    $intDatabases = $intRows;
    // Loop databases in the result set.
    for($ri = 0; $ri < $intRows; $ri++){
      $row = pg_fetch_array($objResults, $ri);
      // for each database create a second connection
      $strTable .= getDatabaseSessions($strDB, $row['dbname']);
    }

    $strInfo = '<div class="row">' . "\n";
    $strInfo .= '  <div class="span12">' . "\n";
    $strInfo .= '    <H3>Server ' . $strDB . '<small> has ' . $intDatabases . ' databases</small></h3>' . "\n";
    $strInfo .= '    <p>Here are the active connections.</p>' . "\n";
    $strInfo .= '  </div>' . "\n";
    $strInfo .= '</div>' . "\n";
    $strTableHeader = '<div class="table-responsive">' . "\n";
    $strTableHeader .= '<table class="table table-hover table-condensed table-striped" width="100%">' . "\n";
    $strTableHeader .= '<thead>' . "\n";
    $strTableHeader .= '<th>Proc PID</th>' . "\n";
    $strTableHeader .= '<th>Database<br>Name</th>' . "\n";
    $strTableHeader .= '<th>User<br>Name</th>' . "\n";
    $strTableHeader .= '<th>Client IP</th>' . "\n";
    $strTableHeader .= '<th>Connection Start</th>' . "\n";
    $strTableHeader .= '<th align="center">Query Duration<br>HH:MM:SS</th>' . "\n";
    $strTableHeader .= '<th>Current Query</th>' . "\n";
    $strTableHeader .= '</thead>' . "\n";
    $strTable .= '</table>' . "\n";
    $strTable .= '</div>' . "\n";

    if($blnShowSQL){
      $strTable .= '<div class="alert alert-info"><strong>The query to pull this info.</strong><br>' . str_replace("\n", '<br>',str_replace(' ', '&nbsp', $strSQL)) . '</div>';
    }
    return $strInfo . $strTableHeader . $strTable;
  }else{
    return '<div>Could not validate port is active on database server:' . $strDB . '</div>';
  }
}

// get the sessions for the specified database
function getDatabaseSessions($strDB, $strDBName, $blnShowSQL = false){
  GLOBAL $arrDBConns;
  $intSessions = 0;
  $intDatabases = 0;
  $strInfo = '';
  $strTableHeader = '';
  $strTable = '';
  if(testPort($arrDBConns[$strDB]['DBIP'],$arrDBConns[$strDB]['DBPort'],.5)){
      $objConn = pg_connect('host=' . $arrDBConns[$strDB]['DBIP'] . ' port=' . $arrDBConns[$strDB]['DBPort'] . ' dbname=' . $strDBName . ' user=' . $arrDBConns[$strDB]['DBUser'] . ' password=' . $arrDBConns[$strDB]['DBPass']);
    if(!$objConn){
      return '<div class="alert alert-error">Error connecting to <strong>' . $strDB . '</strong>!</div>';
    }
    $strSubSQL = '';
    if($arrDBConns[$strDB]['DBVersion'] == '9.2xl'){
      $strSubSQL = "SELECT b.datname
                          ,a.pid AS procpid
                          ,a.usesysid
                          ,u.usename
                          ,a.client_addr
                          ,a.client_port
                          ,a.backend_start
                          ,a.query_start
                          ,a.waiting
                          ,CASE
                            WHEN a.state = 'idle' THEN '<IDLE>'
                            ELSE a.query
                           END AS current_query
                    FROM pg_stat_activity AS a
                      LEFT JOIN pg_user AS u
                        ON a.usesysid = u.usesysid
          JOIN pg_database as b on a.datid = b.oid";
    }
    if($strSubSQL == ''){
      $strSubSQL = "SELECT 'We' AS datname
                          ,'have' AS procpid
                          ,'a' AS usename
                          ,'problam' AS client_addr
                          ,'with' AS client_port
                          ,CURRENT_TIMESTAMP AS backend_start
                          ,'' AS waiting
                          ,'Problem getting activity from " . $strDB . "." . $strDBName . " AS current_query";
    }
    // use the sub query and stick it into the FROM clause
    // this is designed to allow us to expand to other versions of postgres with slightly diffrent pg_stat_activity queries
    $strSQL = "SELECT datname
                     ,procpid
                     ,usesysid
                     ,usename
                     ,client_addr
                     ,client_port
                     ,TO_CHAR(backend_start, 'YYYY-MM-DD HH12:MIam') AS backend_start
                     ,TO_CHAR((extract(epoch from(now() - backend_start)) || ' second')::interval, 'HH24:MI:SS') AS strConnectionDuration
                     ,query_start
                     ,CASE
                       WHEN current_query != '<IDLE>' THEN TO_CHAR((extract(epoch from(now() - query_start)) || ' second')::interval, 'HH24:MI:SS')
                       ELSE ''
                      END AS strQueryDuration
                     ,CASE
                       WHEN current_query != '<IDLE>' THEN CAST(extract(epoch from(now() - query_start)) AS int)
                       ELSE 0
                      END AS dblQueryDuration
                     ,waiting
                     ,current_query
               FROM (" . $strSubSQL . ") AS t
               ORDER BY query_start desc";
    #echo $strSQL;
    $objDBResults = pg_exec($objConn, $strSQL);
    if(!$objDBResults){
      return '<div class="alert alert-error">Error will robinson! Had an issue pulling activity for <strong>' . $strDBName . '</strong></div>';
    }
    // All good lets loop through the activity for this database
    $intDBRows = pg_numrows($objDBResults);
    if($intDBRows == ''){
      $intDBRows = 0;
    }
    // Loop on rows in the result set.

    for($ri = 0; $ri < $intDBRows; $ri++) {
      $dbrow = pg_fetch_array($objDBResults, $ri);
      // add one to the sessions count
      $intSessions++;
      if(substr($dbrow["current_query"],0,30) != substr($strSQL,0,30)){
        $strTableRow = '<tr>' . "\n";
        // flag rows with connections not idle that have been running for a while (not good!)
        if($dbrow["current_query"] != '<IDLE>'){
          if(((int)$dbrow['dblqueryduration'] < 600)){
            $strTableRow = '<tr class="success">' . "\n";
          }
          if(((int)$dbrow['dblqueryduration'] >= 600) && ((int)$dbrow['dblqueryduration'] < 1800)){
            $strTableRow = '<tr class="warning">' . "\n";
          }
          if((int)$dbrow['dblqueryduration'] >= 1800){
            $strTableRow = '<tr class="danger">' . "\n";
          }
        }
        if($dbrow['current_query'] == '<IDLE> in transaction'){
          $strTableRow = '<tr class="info">' . "\n";
        }
        $strTable .= $strTableRow;
        // if the user is an admin, lets give them the option to kill processes
        if(verifyUser(9)){
          $strKillPID = '  <a href=\'sessions.php?n=' . $strDB . '&pid=' . $dbrow['procpid'] . '\' class=\'btn btn-default btn-sm\'>Yes. Kill the process now!</a>' . "\n";
          $strKillPID .= '  <br><br>' . "\n";
          $strKillPID .= '  <a href=\'#\' class=\'btn btn-default btn-sm\'>No Thanks. I\'ve changed my mind!</a>' . "\n";
          $strTable .= '  <td>' . "\n";
  #          $strTable .= '    <a class="btn btn-mini" href="db_activity.php?db=' . $_GET['db'] . '&pid=' . $dbrow['procpid'] . '"><i class="icon-trash"></i></a><br>' . "\n";
          if(isset($_GET['n'])){
            $strTable .= '<a href="#" rel="popover" class="btn btn-default btn-xs popup-marker" data-placement="right" data-html="true" title="Are you sure you want to kill PID ' . $dbrow['procpid'] . ' running on ' . $_GET['n'] . '?" data-content="' . $strKillPID . '">' . "\n";
            $strTable .= '<i class="fa fa-trash-o"></i>' . "\n";
            $strTable .= '</a>' . "\n";
          }

          $strTable .= '    <small>' . $dbrow['procpid'] . '</small>' . "\n";
          $strTable .= '  </td>' . "\n";
        }else{
          $strTable .= '  <td><small>' . $dbrow['procpid'] . '</small></td>';
        }
        $strTable .= '  <td>' . $dbrow["datname"] . '</td>' . "\n";
        $strTable .= '  <td>' . $dbrow["usename"] . '</td>' . "\n";
        $strTable .= '  <td>' . $dbrow["client_addr"] . '</td>' . "\n";
        $strTable .= '  <td>' . $dbrow["backend_start"] . '</td>' . "\n";
        $strTable .= '  <td align="center">' . $dbrow["strqueryduration"] . '</td>' . "\n";
        // if the session is not idle lets show the current query
        if($dbrow["current_query"] != '<IDLE>'){
          $strTable .= '  <td>' . $dbrow["current_query"] . '</td>' . "\n";
        }else{
          $strTable .= '  <td>&nbsp;</td>' . "\n";
        }
        $strTable .= '</tr>' . "\n";
      }
    }
    return $strTable;
  }else{
    return '<div>Could not validate port is active on database server:' . $strDB . '</div>';
  }
}



# will loop through each database on the connection and get the current active session
function getDeadlocks($strDB){
  GLOBAL $arrDBConns;
  if(testPort($arrDBConns[$strDB]['DBIP'],$arrDBConns[$strDB]['DBPort'],.5)){
      $objConn = pg_connect('host=' . $arrDBConns[$strDB]['DBIP'] . ' port=' . $arrDBConns[$strDB]['DBPort'] . ' dbname=' . $arrDBConns[$strDB]['DBName'] . ' user=' . $arrDBConns[$strDB]['DBUser'] . ' password=' . $arrDBConns[$strDB]['DBPass']);
    if(!$objConn){
      return 'unknown';
    }
    $strSQL = 'SELECT datname AS dbname
               FROM pg_database
               WHERE datistemplate = FALSE';
    $objResults = pg_exec($objConn, $strSQL);
    if(!$objResults){
      echo '<div class="alert alert-error">error getting database list from <strong>' . $strDB . '</strong>!</div>';
    }
    $intRows = pg_numrows($objResults);
    // Loop databases in the result set.
    $intCount = 0;
    for($r = 0; $r < $intRows; $r++) {
      $row = pg_fetch_array($objResults, $r);
      // for each database create a second connection
      $objDBConn = pg_connect('host=' . $arrDBConns[$strDB]['DBIP'] . ' port=' . $arrDBConns[$strDB]['DBPort'] . ' dbname=' . $row['dbname'] . ' user=' . $arrDBConns[$strDB]['DBUser'] . ' password=' . $arrDBConns[$strDB]['DBPass']);
      if(!$objDBConn){
        $intCount = 0;
      }else{
        $strSQL = 'SELECT deadlocks FROM pg_stat_database';
        $objDBResults = pg_exec($objDBConn, $strSQL);
        if(!$objDBResults){
          echo '<div class="alert alert-error">error will robinson!</div>';
        }
        $intRows = pg_numrows($objDBResults);
        for($ri = 0; $ri < $intRows; $ri++) {
          $dbrow = pg_fetch_array($objDBResults, $ri);
          $intCount = $intCount + $dbrow['deadlocks'];
        }
#        pg_close($objDBResults);
      }
    }
    return $intCount;
  }else{
    return '???';
  }
}

# will loop through each database on the connection and get the current active session
function getConnections($strDB){
  GLOBAL $arrDBConns;
  if(testPort($arrDBConns[$strDB]['DBIP'],$arrDBConns[$strDB]['DBPort'],.5)){
      $objConn = pg_connect('host=' . $arrDBConns[$strDB]['DBIP'] . ' port=' . $arrDBConns[$strDB]['DBPort'] . ' dbname=' . $arrDBConns[$strDB]['DBName'] . ' user=' . $arrDBConns[$strDB]['DBUser'] . ' password=' . $arrDBConns[$strDB]['DBPass']);
    if(!$objConn){
      return 'unknown';
    }
    $strSQL = 'SELECT datname AS dbname
               FROM pg_database
               WHERE datistemplate = FALSE';
    $objResults = pg_exec($objConn, $strSQL);
    if(!$objResults){
      echo '<div class="alert alert-error">error getting database list from <strong>' . $strDB . '</strong>!</div>';
      pg_close($objConn);
      return;
    }
    pg_close($objConn);
    $intRows = pg_numrows($objResults);
    // Loop databases in the result set.
    $intCount = 0;
    for($r = 0; $r < $intRows; $r++) {
      $row = pg_fetch_array($objResults, $r);
      // for each database create a second connection
      $objDBConn = pg_connect('host=' . $arrDBConns[$strDB]['DBIP'] . ' port=' . $arrDBConns[$strDB]['DBPort'] . ' dbname=' . $row['dbname'] . ' user=' . $arrDBConns[$strDB]['DBUser'] . ' password=' . $arrDBConns[$strDB]['DBPass']);
      if(!$objDBConn){
        $intCount = 0;
      }else{
        $strSQL = 'SELECT COUNT(*) AS intCount FROM pg_stat_activity';
        $objDBResults = pg_exec($objDBConn, $strSQL);
        if(!$objDBResults){
          echo '<div class="alert alert-error">error will robinson!</div>';
        }
        $intRows = pg_numrows($objDBResults);
        for($ri = 0; $ri < $intRows; $ri++) {
          $dbrow = pg_fetch_array($objDBResults, $ri);
          $intCount = $intCount + $dbrow['intcount'];
        }
//        pg_close($objDBResults);
      }
    }
    return $intCount;
  }else{
    return 'unknown';
  }
}

# Pass a connection name and the desired postgres parameter and this function will go get it
function getDBSystemParam($strDB, $strParam = ''){
    GLOBAL $arrDBConns;
    $objConn = @pg_connect('host=' . $arrDBConns[$strDB]['DBIP'] . ' port=' . $arrDBConns[$strDB]['DBPort'] . ' dbname=' . $arrDBConns[$strDB]['DBName'] . ' user=' . $arrDBConns[$strDB]['DBUser'] . ' password=' . $arrDBConns[$strDB]['DBPass']);
    if(!$objConn){
      return 'unknown';
    }
    if($strParam != ''){
      $strSQL = 'SHOW ' . pg_escape_string($strParam);
  #    echo $strSQL . "</br>";
      $objResults = pg_exec($objConn, $strSQL);
      if(!$objResults){
        echo '<div class="alert alert-error">error will robinson!</div>';
      }
      $intRows = pg_numrows($objResults);
      $strParamValue = '';
      for($ri = 0; $ri < $intRows; $ri++){
        $row = pg_fetch_array($objResults, $ri);
        $strParamValue = $row[$strParam];
      }
      pg_close($objConn);
      if($strParamValue == ''){
        $strParamValue = 'unknown';
      }
      return $strParamValue;
    }else{
      $strSQL = 'SHOW ALL';
      $objResults = pg_exec($objConn, $strSQL);
      if(!$objResults){
        echo '<div class="alert alert-error">error will robinson!</div>';
      }
      return $objResults;
    }
}

# give this function bytes and it will return the human readable size incrment
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
}

# test port conetctivity
function testPort($strServer,$intPort,$intTimeout=2){
  $intResponse = @fsockopen("$strServer", $intPort, $errno, $errstr, $intTimeout);

  if($intResponse){
    return true;
  }else{
    return false;
  }
}

# test simple ping
function testPing($strServer){
  $strResult = exec("ping $strServer", $outcome, $intStatus);
  if($intStatus == 0) {
    return true;
  }else{
    return false;
  }
}

# spit out simple array key and values info
# helpful for debuging
function printARRAY($strTitle, $arrArray){
  echo '<h4>' . $strTitle . '</h4>' . "\n";
  if(sizeof($arrArray) > 1){
    foreach($arrArray as $key=>$value){
      echo '<ul>' . "\n";
      echo '<li><strong>' . $key . '</strong>: ';
      if(is_array($value)){
        echo '<ul>' . "\n";
        printARRAY('Child of ' . $key, $value);
        echo '</ul>' . "\n";
        echo '</li>' . "\n";
      }else{
        echo $value . '</li>' . "\n";
      }
      echo '</ul>' . "\n";
    }
  }else{
    echo '<p>Empty array</p>';
  }
}

# simple user and password validation
# update this function if you want to integrate with some other
# auth system
function validateUser($strEmail,$strPassword){
  if($strEmail == SITE_ADMIN && $strPassword == SITE_PASSWORD){
    $_SESSION['userID'] = '1';
    $_SESSION['userTypeID'] = '9';
    $_SESSION['userFirstName'] = 'admin';
    return 1;
  }else{
    // bad login
    return 'error 01';
  }
}
function logout(){
  $_SESSION = array(); //destroy all of the session variables
  session_destroy();
}

# Validate the user is logged in
# if not, redirect them to login screen
function verifyUser($userTypeID,$buRedirect = true){
  if(isset($_SESSION['userID']) && isset($_SESSION['userTypeID'])){
    if($_SESSION['userTypeID'] >= $userTypeID){
      return true;
    }else{
      return false;
    }
  }else{
    if($buRedirect){
      $_SESSION['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
      $_SESSION['ERROR'] = 'You must be logged in to access ' . SITE_TITLE . '.';
      header('Location: login.php');
    }
  }
}
?>