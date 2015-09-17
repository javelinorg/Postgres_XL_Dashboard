<?php include('include_config.php'); ?>
<?php
if(!isset($_GET['dbh'])){
  echo 'ERROR,1' . "\n";
}else{
  $strDB = $_GET['dbh'];
  $objConn = pg_connect('host=' . $arrDBConns[$strDB]['DBIP'] . ' port=' . $arrDBConns[$strDB]['DBPort'] . ' dbname=' . $arrDBConns[$strDB]['DBName'] . ' user=' . $arrDBConns[$strDB]['DBUser'] . ' password=' . $arrDBConns[$strDB]['DBPass']);
  $strSQL = "SELECT datname
               FROM pg_database
               WHERE datistemplate = false
                 AND datname NOT IN ('postgres', 'information_schema', 'pg_catalog')";

  $objResults = pg_query($objConn, $strSQL);
  if(!$objResults){
    $strInfo = 'error will robinson! Could not find any databases on ' . $_GET['dbh'] . '.' . "\n";
  }else{
    $intDatabases = pg_numrows($objResults);
    $arrParentChild = array();
    $arrParentChild[] = array('id'=>'h0', 'parent'=>'00', 'name'=>$_GET['dbh'], 'description'=>$strDB . ' Parent');
    for($intDB = 0; $intDB < $intDatabases; $intDB++) {
      $row = pg_fetch_array($objResults, $intDB);
      $intDBID = $intDB + 1;
      $arrParentChild[] = array('id'=>'d'.$intDBID, 'parent'=>'h0', 'name'=>$row['datname'], 'description'=>'Database: ' . $row['datname']);
# get databases
      $objDBConn = pg_connect('host=' . $arrDBConns[$strDB]['DBIP'] . ' port=' . $arrDBConns[$strDB]['DBPort'] . ' dbname=' . $row['datname'] . ' user=' . $arrDBConns[$strDB]['DBUser'] . ' password=' . $arrDBConns[$strDB]['DBPass']);

      $strSSQL = "SELECT DISTINCT ns.nspname AS schema_name
                    FROM pg_class AS cl
                      INNER JOIN pg_catalog.pg_namespace AS ns
                        ON ns.oid = cl.relnamespace
                    WHERE cl.relkind = 'r'
                      AND ns.nspname NOT IN ('information_schema', 'pg_catalog')";
      $objSResults = pg_query($objDBConn, $strSSQL);
      if(!$objSResults){
        $arrParentChild[] = array('id'=>'s0', 'parent'=>'d'.$intDBID, 'name'=>'error pulling' . $_GET['dbh'] . '.' . $row['datname'] . ' table sizes', 'description'=>$strDB . ' Error' , size=>'0');
      }else{
        $intSchemas = pg_numrows($objSResults);
        $intSID = 1;
        for($intS = 0; $intS < $intSchemas; $intS++) {
          $rowS = pg_fetch_array($objSResults, $intS);
          $intSID++;
          $arrParentChild[] = array('id'=>'d'.$intDBID.'s'.$intSID, 'parent'=>'d'.$intDBID, 'name'=>$rowS['schema_name'], 'description'=>$rowS['schema_name']);

# get tables
          $intSizeLimit = 1;
          $strTSQL = "(SELECT cl.relname AS table_name
                          ,pg_table_size(cl.oid) AS table_size
                          ,pg_indexes_size(cl.oid) AS indexes_size
                          ,pg_total_relation_size(cl.oid) AS total_size
                          ,pg_size_pretty(pg_total_relation_size(cl.oid)) AS human_total_size
                      FROM pg_class AS cl
                        INNER JOIN pg_catalog.pg_namespace AS ns
                          ON ns.oid = cl.relnamespace
                      WHERE cl.relkind = 'r'
                        AND ns.nspname = '" . $rowS['schema_name'] . "'
                        AND pg_total_relation_size(cl.oid) >= " . $intSizeLimit * 1024 * 1024 * 1024 . ")
                      UNION
                      (SELECT COUNT(cl.relname)::varchar || '_tables_less_than_" . $intSizeLimit . "GB' AS table_name
                          ,SUM(pg_table_size(cl.oid))::bigint AS table_size
                          ,SUM(pg_indexes_size(cl.oid))::bigint AS indexes_size
                          ,SUM(pg_total_relation_size(cl.oid))::bigint AS total_size
                          ,pg_size_pretty(SUM(pg_total_relation_size(cl.oid))::bigint) AS human_total_size
                      FROM pg_class AS cl
                        INNER JOIN pg_catalog.pg_namespace AS ns
                          ON ns.oid = cl.relnamespace
                      WHERE cl.relkind = 'r'
                        AND ns.nspname = '" . $rowS['schema_name'] . "'
                        AND pg_total_relation_size(cl.oid) < " . $intSizeLimit * 1024 * 1024 * 1024 . ")";
          $objTResults = pg_query($objDBConn, $strTSQL);
          if(!$objTResults){
            $arrParentChild[] = array('id'=>'t0', 'parent'=>'s'.$intSID, 'name'=>'error pulling ' . $_GET['dbh'] . '.' . $row['datname'] . ' table sizes', 'description'=>$strDB . ' Error', size=>'0');
          }else{
            $intTables = pg_numrows($objTResults);
            $intTID = 1;
            for($intT = 0; $intT < $intTables; $intT++) {
              $rowT = pg_fetch_array($objTResults, $intT);
              $intTID++;
              if($rowT['total_size'] == ''){
                $intTotalSize = 0;
              }else{
                $intTotalSize = $rowT['total_size'];
              }
#echo $row['datname'] . '.' . $rowS['schema_name'] . '.' . $rowT['table_name'] . "\n";
              $arrParentChild[] = array('id'=>'d' . $intDBID . 's'.$intSID . 't'.$intTID, 'parent'=>'d' . $intDBID . 's'.$intSID, 'name'=>$rowT['table_name'], 'description'=>$strDB . '.' . $rowS['schema_name'] . '.' . $rowT['table_name'], 'size'=>$intTotalSize);
              $arrParentChild[] = array('id'=>'d' . $intDBID . 's'.$intSID . 'td'.$intTID, 'parent'=>'d' . $intDBID . 's'.$intSID . 't'.$intTID, 'name'=>$rowT['table_name'] . ' Data', 'description'=>'Data size for: ' . $strDB . '.' . $rowS['schema_name'] . '.' . $rowT['table_name'], 'size'=>$rowT['table_size']);
              $arrParentChild[] = array('id'=>'d' . $intDBID . 's'.$intSID . 'ti'.$intTID, 'parent'=>'d' . $intDBID . 's'.$intSID . 't'.$intTID, 'name'=>$rowT['table_name'] . ' Index', 'description'=>'Index size for: ' . $strDB . '.' . $rowS['schema_name'] . '.' . $rowT['table_name'], 'size'=>$rowT['indexes_size']);
            }
          }
# end tables
        }
      }
      pg_close($objDBConn);
# end databases
    }
  }
#print_r($arrParentChild);
header('Content-Type: application/json; charset="utf-8"');

/**
 * Helper function
 *
 * @param array   $d   flat data, implementing a id/parent id (adjacency list) structure
 * @param mixed   $r   root id, node to return
 * @param string  $pk  parent id index
 * @param string  $k   id index
 * @param string  $c   children index
 * @return array
 */
function makeRecursive($d, $r = 0, $pk = 'parent', $k = 'id', $c = 'children') {
  $m = array();
  foreach ($d as $e) {
    isset($m[$e[$pk]]) ?: $m[$e[$pk]] = array();
    isset($m[$e[$k]]) ?: $m[$e[$k]] = array();
    $m[$e[$pk]][] = array_merge($e, array($c => &$m[$e[$k]]));
  }
  return $m[$r][0];
}
#print_r(makeRecursive($arrParentChild,'00'));
echo json_encode(makeRecursive($arrParentChild,'00'),JSON_NUMERIC_CHECK);
/*
echo json_encode(makeRecursive(array(
  array('id' => 5273, 'parent' => 0,    'name' => 'John Doe'),
  array('id' => 6032, 'parent' => 5273, 'name' => 'Sally Smith'),
  array('id' => 6034, 'parent' => 6032, 'name' => 'Mike Jones'),
  array('id' => 6035, 'parent' => 6034, 'name' => 'Jason Williams'),
  array('id' => 6036, 'parent' => 5273, 'name' => 'Sara Johnson'),
  array('id' => 6037, 'parent' => 5273, 'name' => 'Dave Wilson'),
  array('id' => 6038, 'parent' => 6037, 'name' => 'Amy Martin'),
)));
*/

#pg_close($objConn);
}
?>
