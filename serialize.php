<?php

$from = 'from.website.com';
$to = 'to.website.com';

$db_name = '';
$db_host = '';
$db_user = '';
$db_password = '';

mysql_connect($db_host,$db_user,$db_password);
mysql_select_db($db_name);

// Show all tables
$array = Array();

$tables = mysql_query("show tables from ".$db);
while ($table = mysql_fetch_row($tables)) {
  $array[] = $table[0];
}


foreach ($array as $table) {
  echo $table."\n";
  $fields = mysql_query("show columns from ".$table);
  $primary = mysql_query("SHOW KEYS FROM ".$table." WHERE Key_name = 'PRIMARY'");
  $primary_result = mysql_fetch_array($primary);
  $primary_key = $primary_result["Column_name"];
  
  while ($field = mysql_fetch_array($fields)) {
    echo "--".$field[0]."\n";
    $query = "select ".$field[0]." from ".$table;
    
    $get = mysql_query($query);
    while ($result = mysql_fetch_array($get)) {
      
      $data = @unserialize($result[$field[0]]);
      if ($data !== false) {
        $re_data = Array();
        
        $has_key = false;
        
        foreach ($data as $key => $val) {
          if (strpos($val,$from) !== false) {
            $has_key = true;
            $re_data[$key] = str_replace($from,$to,$val);
          } else {
            $has_key = false;
            $re_data[$key] = $val;
          }
        }
        
        $str = serialize($re_data);
        
        if ($has_key == false) {
          $query = "update ".$table." set ".$field[0]." = '".$str."' where ".$field[0]." = '".$result[$field[0]]."'";
          $query_show = "update ".$table." set ".$field[0]." = '".$str."' \nwhere ".$field[0]." = '".$result[$field[0]]."'";
          
          if (mysql_query($query)) {
            echo '<span style="color: #00aa00;">'.$query_show.'</span>';
          } else {
            echo '<span style="color: #ff0000;">'.$query_show.'</span>';
          }
          echo "\n\n";
        }
      } else {
        if (strpos($result[$field[0]],$from) !== false) {
          $str = str_replace($from,$to,$result[$field[0]]);
          $query = "update ".$table." set ".$field[0]." = '".$str."' where ".$field[0]." = '".$result[$field[0]]."'";
          $query_show = "update ".$table." set ".$field[0]." = '".$str."' where ".$field[0]." = '".$result[$field[0]]."'";
          
          if (mysql_query($query)) {
            echo '<span style="color: #00aa00;">'.$query_show.'</span>';
          } else {
            echo '<span style="color: #ff0000;">'.$query_show.'</span>';
          }
          echo "\n\n";
        }
      }
    }
  }
}
?>

