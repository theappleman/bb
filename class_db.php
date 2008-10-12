<?php
/*
* Filename.......: class_db.php
* Author.........: Troy Wolf [troy@troywolf.com]
* Last Modified..: Date: 2005/06/24 11:05:00
* Description....: A database class that provides methods to work with mysql,
                   postgres, and mssql databases. The class provides a common
                   interface to the various database types. A powerful
                   feature of the class is the ability to cache datasets to disk
                   using a Time-To-Live parameter. This can eliminate a lot of
                   unneccessary hits to your database!  Also, a database
                   connection is not created unless and until needed.
*/
class db {
  var $cnn_id;
  var $db_type;
  var $dir;
  var $name;
  var $filename;
  var $fso;
  var $sql;
  var $cnn;
  var $db;
  var $ttl;
  var $data_ts;
  var $server;
  var $log;
  var $rows_affected;
  var $last_id;

  /*
  The class constructor. You can set some defaults here if desired.
  */
  function db($cnn_id=0) {
    global $cache_dir,$cache_time;
    $this->log = "initialize db() called<br />";
    $this->cnn_id = $cnn_id;
    $this->dir = $cache_dir;
    $this->ttl = $cache_time;
    $this->data_ts = 0;
  }

  /*
  connect() method makes the actual server connection and selects a database
  only if needed. This saves database connections.  Multiple database types are
  supported. Enter your connection credentials in the switch statement below.

  This is a private function, but it is at the top of the class because you need
  to enter your connections.
  */
  function connect() {
    global $db_host, $db_user, $db_pass, $db_data;
    $this->log .= "connect() called<br />";
    switch($this->cnn_id) {
      /*
      You can define all the database connections you need in this
      switch statement.
      */
      case 0:
        $this->db_type = "mysql";
        $this->server = $db_host;
        $user 		    = $db_user;
        $pwd 		      = $db_pass;
        $this->db     = $db_data;
        break;
    }
    switch($this->db_type) {
      case "mysql":
        if (!$this->cnn = mysql_connect($this->server,$user,$pwd )) {
          $this->log .= "mysql_connect() failed<br />";
          $this->log .= mysql_error()."<br />";
          return false;
        }
        if (!mysql_select_db($this->db,$this->cnn)) {
          $this->log .= "Could not select database named ".$this->db."<br />";
          $this->log .= mysql_error()."<br />";
          return false;
        }
        break;
    }
    return true;
  }

  /*
  fetch() is used to retrieve a dataaset. fetch() determines whether to use the
  cache or not, and queries either the database or the cache file accordingly.
  */
  function fetch($sql="",$ttl=0,$name="") {
    $this->log .= "---------------------------------<br />fetch() called<br />";
    $this->rows_affected = 0;
    if (!$sql) {
      $this->log .= "OOPS: You need to pass a SQL statement!<br />";
      return false;
    }
    $this->sql = $sql;
    $this->ttl = $ttl;
    $this->name = $name;
    $this->log .= "SQL: ".$this->sql."<br />";
    $data = "";
    if ($this->ttl == "0") {
      return $data = $this->getFromDB();
    } else {
      if (strlen(trim($this->name)) == 0) { $this->name = MD5($this->sql); }
      $this->filename = $this->dir."db_".$this->name;
      $this->log .= "Filename: ".$this->name."<br />";
      $this->getFile_ts();
      if ($this->ttl == "daily") {
        if (date('Y-m-d',$this->data_ts) != date('Y-m-d',time())) {
          $this->log .= "cache has expired<br />";
          if (!$data = $this->getFromDB()) { return false; }
          if (!$this->saveToCache($data)) { return false; }
          return $data;
        } else {
          return $data = $this->getFromCache();
        }
      } else {
        if ((time() - $this->data_ts) >= $this->ttl) {
          $this->log .= "cache has expired<br />";
          if (!$data = $this->getFromDB()) { return false; }
          if (!$this->saveToCache($data)) { return false; }
          return $data;
        } else {
          return $data = $this->getFromCache();
        }
      }
    }
  }

  /*
  Use exec() to execute INSERT, UPDATE, DELETE statements.
  */
  function exec($sql="") {
    $this->log .= "---------------------------------<br />exec() called<br />";
    $this->rows_affected = 0;
    if (!$sql) {
      $this->log .= "OOPS: You need to pass a SQL statement!<br />";
      return false;
    }
    $this->sql = $sql;
    $this->log .= "SQL: ".$this->sql."<br />";
    if (!$this->cnn) { if (!$this->connect()) { return false; } }
    switch($this->db_type) {
      case "mysql":
        if (!$res = @mysql_query($this->sql, $this->cnn)) {
          $this->log .= "Query execution failed.<br />";
          $this->log .= mysql_error()."<br />";
          return false;
        }
        break;
    }

    /*
    Set last_id property (only applicable for INSERTS), and return
    number of rows affected by INSERT, UPDATE, DELETE.
    */
    switch($this->db_type) {
      case "mysql":
        $this->last_id = mysql_insert_id();
        $this->rows_affected = mysql_affected_rows($this->cnn);
        $this->log .= $this->rows_affected." rows affected<br />";
        return $this->rows_affected;
    }
  }

  /*
  fmt() is a helper function for formatting SQL statement strings.
  For strings values, it will escape embedded single ticks, replace emptry
  strings with 'NULL', and properly wrap the value in quotes. For numeric types,
  it will replace empty values with zero.
      val		:	value to format
      dtype	:	0 = string, 1 = numeric
  */
  function fmt($val,$dtype) {
    switch($dtype) {
      case 0:
        if(! $val && $val != "0") {
          $tmp = "null";
        } else {
          $tmp = "'".str_replace("'","''",$val)."'";
        }
        break;
      case 1:
        if(! $val) {
          $tmp = "0";
        } else {
          $tmp = $val;
        }
        break;
    }
    return $tmp;
  }

  /*
  fmt2() is the same as fmt() except it inserts a comma at the beginning
  of the return value and a space at the end. Useful in building SQL statements
  with multiple values.
  */
  function fmt2($val,$dtype) {
    switch($dtype) {
      case 0:
        if(! $val && $val != "0") {
          $tmp = ",null ";
        } else {
          $tmp = ",'".str_replace("'","''",$val)."' ";
        }
        break;
      case 1:
        if(! $val) {
          $tmp = ",0";
        } else {
          $tmp = ",".$val." ";
        }
        break;
    }
    return $tmp;
  }

  /*
  dump() produces an HTML table of the data. It is useful for debugging.
  This is also a good example of how to work with the data array.
  */
  function dump($data) {
    $this->log .= "dump() called<br />";
    if (!$data) {
      $this->log .= "dump(): no rows exist<br />";
      return false;
    }
    echo "<style>table.dump { font-family:Arial; font-size:8pt; }</style>";
    echo "<table class=\"dump\" border=\"1\" cellpadding=\"1\" cellspacing=\"0\">\n";
    echo "<tr>";
    echo "<th>#</th>";
    foreach($data[0] as $key=>$val) {
      echo "<th><b>";
      echo $key;
      echo "</b></th>";
    }
    echo "</tr>\n";
    $row_cnt = 0;
    foreach($data as $row) {
      $row_cnt++;
      echo "<tr align='center'>";
      echo "<td>".$row_cnt."</td>";
      foreach($row as $val) {
        echo "<td>";
        echo $val;
        echo "</td>";
      }
      echo"</tr>\n";
    }
    echo "</table>\n";
  }

  /*
  PRIVATE FUNCTIONS BELOW THIS POINT
  ------------------------------------------------------------------------------
  */

  function getFromDB() {
    $this->log .= "getFromDB() called<br />";
    if (!$this->cnn) {
      if (!$this->connect()) {
        $this->log .= "Database connection failed.<br />";
        return false;
      }
    }
    switch($this->db_type) {
      case "mysql":
        if (!$res = @mysql_query($this->sql, $this->cnn)) {
          $this->log .= "Query execution failed.<br />";
          $this->log .= mysql_error()."<br />";
          return false;
        }
        while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
          $data[] = $row;
        }
        break;
    }
    return $data;
  }

  function getFromCache() {
    $this->log .= "getFromCache() called<br />";
    if (!$x = @file_get_contents($this->filename)) {
      $this->log .= "Could not read ".$this->filename."<br />";
      return false;
    }
    if (!$data = unserialize($x)) {
      $this->log .= "getFromCache() failed<br />";
      return false;
    }
    return $data;
  }

  function saveToCache($data) {
    $this->log .= "saveToCache() called<br />";
    //create file pointer
    if (!$fp=@fopen($this->filename,"w")) {
      $this->log .= "Could not open ".$this->filename."<br />";
      return false;
    }
    //write to file
    if (!@fwrite($fp,serialize($data))) {
      $this->log .= "Could not write to ".$this->filename."<br />";
      fclose($fp);
      return false;
    }
    //close file pointer
    fclose($fp);
    return true;
  }

  function getFile_ts() {
    $this->log .= "getFile_ts() called<br />";
    if (!file_exists($this->filename)) {
      $this->data_ts = 0;
      $this->log .= $this->filename." does not exist<br />";
      return false;
    }
    $this->data_ts = filemtime($this->filename);
    return true;
  }

}

?>
