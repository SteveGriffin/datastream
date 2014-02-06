CREATE DATABASE MiniSnowLoadMonitor;
use MiniSnowLoadMonitor;

CREATE TABLE stations
(
	station_id INT AUTO_INCREMENT NOT NULL,
	name VARCHAR(16),
	serial_number INT,
	latitude DECIMAL(10,6),
	longitude DECIMAL(10,6),
	zone_id INT NOT NULL,
	PRIMARY KEY(station_id)
);

CREATE TABLE measurements
(
	measurement_timestamp TIMESTAMP NOT NULL,
	temp_air FLOAT,
	temp_roof FLOAT,
	temp_pcb FLOAT,
	temp_scale FLOAT,
	load_cell1 FLOAT,
	load_cell2 FLOAT,
	load_cell3 FLOAT,
	battery_voltage FLOAT,
	panel_voltage FLOAT,
	charging BOOLEAN,
	station_id INT NOT NULL,
	PRIMARY KEY(measurement_timestamp),
	FOREIGN KEY(station_id) REFERENCES stations(station_id)
);

INSERT INTO stations VALUES
	(null, "My Station", 12345, -170.000010, 200.000000, 1),
	(null, "My Station2", 12346, -170.000020, 200.000000, 1),
	(null, "My Station3", 12347, -170.000030, 200.000000, 1),
	(null, "My Station4", 12348, -170.000040, 200.000000, 1),
	(null, "My Station5", 12349, -170.000050, 200.000000, 1)
	;


