<?php
/*
 *	@file index.php
 *	@author David Meehan
 *	Sample PHP application to test data transactions between the
 *	hardware and software systems. Currently processes the primary
 *	temperature values we are storing.
 */
 
include_once("db.php");
$temp = new TemperatureInputStream();

/*
 *	Handles and processes temperature data being streamed from the 
 *  hardware. If the four temperature fields are present in the form
 *  of a query string, add a new measurement entry to the database.
 *  Display all stations and measurements.
 */
class TemperatureInputStream
{
	/*
	 *	Build a new TemperatureInputStream object. Process any query
	 *  string data and dsiplay the html page.
	 */
	function __construct() {
		$this->displayGuide();
		$this->displayAddData();
		$this->displayHTML();
	}
	
	function displayAddData() {
		echo("<h3>Adding New Entry:</h3>");
		$this->processInput();
		echo("<hr />");
	}
	
	/*
	 *	Displays two tables containing all measurement and station
	 *  data currently loaded in the database.
	 */
	function displayHTML() {
		$db = new DB();
		// Tables to load
		$tables = array("stations", "measurements");
		// For each table in the db, display an html table for it
		foreach($tables as $table) {
			// Retrieves all the entries
			$entries = $db->getAllEntries($table);
			$table = "<h3>" . (ucfirst($table)) . ":</h3><table border=1>";
			// Add a row for the column headings (requires 1 entry)
			if(count($entries) > 0) {
				$table .= "<tr>";
				foreach($entries[0] as $key=>$value) {
					$table .= "<th>" . $key . "</th>";
				}
				$table .= "</tr>";
			}
			// Build the table
			foreach($entries as $entry) {
				$table .= "<tr><td>" . (implode("</td><td>", $entry)) . "</td></tr>";
			}
			echo($table . "</table><hr />");
		}
	}
	
	function displayGuide() {
		echo <<<_END
			<h3>Query String Guide</h3>
			Query strings follow the following format:<br />
			<ul><li>index.php?station_<#>=[field:value;field:value;field:value]&station_<#>=[field:value]</li></ul>
			
			<hr />
_END;
	}
	
	/*
	 *	Processes GET requests and add data to the database as needed.
	 *	Since we are only processing temperatures all other values
	 *	will be set to null. 
	 */
	function processInput() {
		// If get data exists
		if(COUNT($_GET) > 0) {
			// For each station
			foreach($_GET as $key=>$value) {
				$stationID = explode("_", $key)[1];
				// Build template data
				$temps = array(
					"measurement_timestamp" => date("Y-m-d H:i:s"),
					"temp_air" => null, "temp_roof" => null, "temp_pcb" => null, 
					"temp_scale" => null, "load_cell1" => null, "load_cell2" => null, 
					"load_cell3" => null, "battery_voltage" => null, "panel_voltage" => null,
					"charging" => null, "station_id" => $stationID
				);
				// Split fields
				$data = explode(";", substr($value, 1, strlen($value)-2));
				// Split and add data to the template IF the key exists
				for($i=0; $i<count($data); $i++) {
					$temp = explode(":", $data[$i]);
					if(array_key_exists($temp[0], $temps))
						$temps[$temp[0]] = $temp[1];
				}
				// Add the entry
				$this->addTemperatureData($temps);
			}
		}
	}
	
	/*
	 *	Creates a DB object and initiates the insert process.
	 */
	function addTemperatureData($data) {
		$db = new DB();
		$db->insertEntry("measurements", $data);
	}
}	
?>
