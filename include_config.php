<?php include('include_constants.php'); ?>
<?php include('include_functions.php'); ?>
<?php
// Start and manage sessions
session_start();
// this will destroy your session and request you log in again if your inactivity is longer than 30 min
if(isset($_SESSION['userID'])){
  if(isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)){
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time
    session_destroy();   // destroy session data in storage
    // redirect person to logout page
    header("Location: logout.php?r=ina");
  }
}
// update your session activity... with each request
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

// # Here are some system db connections
$arrDBConns = array('test_coord1' => array('DBType' => 'Postgres'
                                 ,'DBVersion' => '9.2xl'
                                 ,'DBXLNode' => 'Coordinator'
                                 ,'DBIP' => '10.6.2.31'
                                 ,'DBPort' => 5432
                                 ,'DBName' => 'postgres'
                                 ,'DBUser' => 'javelin_user'
                                 ,'DBPass' => 'not_my_password')
                  ,'test_coord2' => array('DBType' => 'Postgres'
                                 ,'DBVersion' => '9.2xl'
                                 ,'DBXLNode' => 'Coordinator'
                                 ,'DBIP' => '10.6.2.33'
                                 ,'DBPort' => 5432
                                 ,'DBName' => 'postgres'
                                 ,'DBUser' => 'javelin_user'
                                 ,'DBPass' => 'not_my_password')
                  ,'test_dn01' => array('DBType' => 'Postgres'
                                 ,'DBVersion' => '9.2xl'
                                 ,'DBXLNode' => 'Datanode'
                                 ,'DBIP' => '10.6.2.31'
                                 ,'DBPort' => 15432
                                 ,'DBName' => 'postgres'
                                 ,'DBUser' => 'javelin_user'
                                 ,'DBPass' => 'not_my_password')
                  ,'test_dn02' => array('DBType' => 'Postgres'
                                 ,'DBVersion' => '9.2xl'
                                 ,'DBXLNode' => 'Datanode'
                                 ,'DBIP' => '10.6.2.32'
                                 ,'DBPort' => 15432
                                 ,'DBName' => 'postgres'
                                 ,'DBUser' => 'javelin_user'
                                 ,'DBPass' => 'not_my_password')
                  ,'test_dn03' => array('DBType' => 'Postgres'
                                 ,'DBVersion' => '9.2xl'
                                 ,'DBXLNode' => 'Datanode'
                                 ,'DBIP' => '10.6.2.33'
                                 ,'DBPort' => 15432
                                 ,'DBName' => 'postgres'
                                 ,'DBUser' => 'javelin_user'
                                 ,'DBPass' => 'not_my_password')
                  ,'test_dn04' => array('DBType' => 'Postgres'
                                 ,'DBVersion' => '9.2xl'
                                 ,'DBXLNode' => 'Datanode'
                                 ,'DBIP' => '10.6.2.34'
                                 ,'DBPort' => 15432
                                 ,'DBName' => 'postgres'
                                 ,'DBUser' => 'javelin_user'
                                 ,'DBPass' => 'not_my_password')
                  ,'test_gtm_proxy_01' => array('DBXLNode' => 'GTM Proxy'
                                 ,'DBIP' => '10.6.2.31'
                                 ,'DBPort' => 8989)
                  ,'test_gtm_proxy_02' => array('DBXLNode' => 'GTM Proxy'
                                 ,'DBIP' => '10.6.2.33'
                                 ,'DBPort' => 8989)
                  ,'test_gtm' => array('DBXLNode' => 'GTM'
                                 ,'DBIP' => '10.6.2.34'
                                 ,'DBPort' => 6668));
?>