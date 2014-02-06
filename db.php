<?php
/*
 *	@file db.php
 *  @author David Meehan
 *  Database bridge between PHP and MySQL DBMS. Uses dynamic insert
 *	and retrieval functions to insert and select data from the databse.
 */
class DB
{
	private $connection;
	/**
     * Connects to the MiniSnowLoadMonitor database.
     * @return mysqli $connection - Result of connection attempt. 
     */
	function __construct() {
		$host = '127.0.0.1';
		$database = 'MiniSnowLoadMonitor';
		$username = 'root';
		$password = '';
		
		$conn = new mysqli($host, $username, $password, $database);
		if(mysqli_connect_errno()) {
			$this->connection = null;
		} else {
			$this->connection = $conn;
		}
	}
	
	/*
	 *	@param string $table - Table name to insert into
	 *	@param array $data - Associative array of data to add
	 *	Inserts an entry into table using the key=> value architecture
	 *	of the provided associative array to insert data into the db.
	 */
	function insertEntry($table, $data) {
		$db = $this->connection;
		$cols = "";
		$filler = "";
		$type = "";
		$list = array("");
		foreach($data as $key=>$value) {
			$cols .= $key . ", ";
			$filler .= "?,";
			$list[0] .= "s";
			$list[] = &$data[$key];
		}
		$cols = substr($cols, 0, strlen($cols)-2);
		$filler = substr($filler, 0, strlen($filler)-1);
		$query = "INSERT INTO $table ($cols) VALUES($filler)";
		echo("<h1>$query</h1>");
		$sth = $db->prepare($query);
		if($sth === false) {
			echo("Failed to prepare statement " . htmlspecialchars($db->error));
			return false;
		}
		$results = call_user_func_array(array($sth, 'bind_param'), $list); 
		if($results === false) {
			echo("Failed to prepare statement<br />");
			return false;
		}
		$result = $sth->execute();
		return $result;
	}
	
	/*
	 *	@param string $table - Table name to insert into
	 *	Gets all entries from the table specified by $table.
	 */
	function getAllEntries($tableName) {
		$db = $this->connection;
		$query = "SELECT * FROM $tableName";		
		$sth = $db->prepare($query);
		if($sth === false) {
			echo "Failed to prepare the SQL statement: " . htmlspecialchars($db->error);
			echo("<br /><h3>$query</h3><br />");
			return false;
		}
		$result = $sth->execute();
		if($result == false) {
			echo "Failed to execute: " . htmlspecialchars($sth->error);
			return false;
		}
		$meta = $sth->result_metadata();
		$fields = array();
		while ($field = $meta->fetch_field()) {
			$var = $field->name;
			$$var = null;
			$fields[$var] = &$$var;
		}
		$results = call_user_func_array(array($sth, 'bind_result'), $fields);
		if($results == false) {
			echo "Failed to execute: " . htmlspecialchars($sth->error);
		}
		$resultArray = array();
		$i = 0;
		
		while($sth->fetch()) {
			$resultArray[$i] = array();
			foreach($fields as $key => $value) {
				$resultArray[$i][$key] = $value;
			}
			$i = $i + 1;
		}
		return $resultArray;
	}
}
?>
