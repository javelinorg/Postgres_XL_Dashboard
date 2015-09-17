<?php include('include_config.php'); ?>
<?php
//OUPUT HEADERS
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"database_sizes\";" );
header("Content-Transfer-Encoding: binary");

echo 'database,size' . "\n";

if(!isset($_GET['dbh'])){
  echo 'ERROR,1' . "\n";
}else{
  $arrDBSizes = getDatabaseSizes($_GET['dbh']);
#  print_r($arrDBSizes);
  foreach($arrDBSizes as $key => $value){
    echo $key . ',' . $value . "\n";
  }
}



?>