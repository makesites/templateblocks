<?php
/**
 * TEMPLATE BLOCKS
 * Copyright (C) 2008 Makis Tracend
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * http://www.gnu.org/licenses/gpl-2.0.txt
 * 
 * Class: MySQL DataBase Access
 */


class Database {
  # Define class varriables
  public $dbhost;
  public $dbuser;
  public $dbpass;
  public $dbname;
  public $connection;
  public $numrows;

  # Call constructor
  function __construct() {
    global $config;

    $this->dbhost			= $config['Database_server'];
    $this->dbname			= $config['Database_name'];
    $this->dbuser			= $config['Database_user'];
    $this->dbpass			= $config['Database_password'];
    $this->table_prefix		= $config['Database_table_prefix'];
    $this->suppress_errors	= 0;
    $this->connection		= 0;
    $this->query_count		= 0;
    $this->query_strings	= '';

  }


  function openConnection(){
    //Connect to the database
    $this->connection = @mysql_connect($this->dbhost, $this->dbuser, $this->dbpass) or die('Could not connect: ' . mysql_error());
	//Select the database connection for use
	mysql_select_db($this->dbname,$this->connection) or die('could not select database'); 
  }


  function closeConnection(){
    mysql_close($this->connection);
  }

  function testConnection(){
    return @mysql_connect($this->dbhost, $this->dbuser, $this->dbpass); 
  }

  function select($fields, $table, $where=false, $order=false){
	$this->openConnection();

    # Set Query for select.
	$query = 'SELECT ' . $fields . ' FROM ' . $table;
	if( $where ) {
	  $query .= ' WHERE';
	  foreach( $where as $k => $v ){
		$query .= ' ' . $k . '="' . $v . '"';
	  }
	}
	if( $order ) {
	  $query .= ' ORDER BY ' . $order;
	
	}
	//Run the query and store the results
	$result=mysql_query($query)or die('query failed'. mysql_error());
	//Retrieve the number of rows in the result

	$this->numrows=mysql_num_rows($result);
	if($this->numrows>0){
	  //Place all of the data into an array
	  for( $x=0; $x<$this->numrows; $x++ ){
	    $returndata[]=mysql_fetch_assoc($result);
	  }
	  //Return the results
	  return $returndata;
	}
    // Free resultset
	mysql_free_result($result);
	
  }

  function execute($type, $table, $fields){
    $this->openConnection();

    switch( $type ){
      case 'add':
		$query = 'INSERT INTO ' . $table . ' ' . $fields;
		break;
	  case 'save':
		$query = 'UPDATE ' . $table . ' SET';
	    foreach( $fields as $k => $v ){
		  $query .= ' ' . $k . '="' . $v . '" ';
	      if($v != end($fields)){ $query .= ','; }
	    }
		$query .= ' WHERE id=' . $fields['id'];
		break;
      case 'delete':
		$query = 'DELETE FROM ' . $table . ' WHERE id=' . $fields['id'];
		break;
    }
	//Run the query
	$result=mysql_query($query);
	//If we were successful, return a 1, if not, return a 0
	if($result){
	  return 1;
	}else{
	  return 0;
	}
  }

  function runSQL($query){
    $this->openConnection();
	$result=mysql_query($query);
	//if ($result) {
    //  echo "success";
    //} 
  }

  function error($error_string) {
    if( !$this->suppress_errors ){
      $block_title	= 'Database Error';
      $block_content	= $error_string;
      $tblocks->page($block_title, $block_content);
    }
  }

}

?>
