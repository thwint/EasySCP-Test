<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

/**
 * Benötigte Klasse für die Konfiguration laden
 *
 */

/**
 * Statische Klasse für Datenbankzugriffe.
 *
 */
class DB {
	private static $config;
	private static $connectid = false;
	private static $stmt = false;

	private static $abfragen = 0;
	private static $zeit = 0;

	/**
	 * Liefert die aktuelle Instanz der Datenbank verbindung zurück.
	 * Falls noch keine Datenbank verbindung besteht wird diese aufgebaut und dann die Instanz zurückgegeben.
	 * Zusätzlich werden einige Optionen für die Datenbank verbindung gesetzt:
	 * ATTR_PERSISTENT = Die Datenbank verbindung bleibt solange bestehen bis das Script sich beendet hat.
	 *
	 * @throws Exception
	 * @return object DB::$connectid Gibt das Objekt für die Datenbankverbindung zurück oder erstellt dieses falls es noch nicht vohanden ist.
	 */
	static public function getInstance(){
		self::$config = parse_ini_file('config/'.getenv('CONFIG'));
		if(!self::$connectid){
			try {
				self::$connectid = new PDO(
					'mysql:host='.self::$config['host'].';dbname='.self::$config['database'].';port='.self::$config['dbport'],
					self::$config['dbuser'],
					self::$config['dbpassword'],
					array(
						PDO::ATTR_PERSISTENT => true,
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
					)
				);
			}
			catch(PDOException $e){
				throw new Exception($e->getMessage());
			}
		}

		return self::$connectid;
	}

	/**
	 * Beendet die aktuelle Instanz der Datenbank verbindung.
	 */
	static public function closeInstance(){
		self::$connectid = null;
		self::$connectid = false;
		self::$stmt = null;
		self::$stmt = false;
	}

	/**
	 * Vorbereiten eines SQL Query.
	 * 
	 * @param string $sql_query Der Query den man vorbereiten möchte (Prepared Statement).
	 * @return object DB::$stmt Liefert das Objekt eines vorbereiteten (prepared) SQL Query zurück. Das Objekt wird für spezielle PDO Funktionen benötigt (z.b. "bindValue").
	 */
	static public function prepare($sql_query){
		$timer_start = microtime();
		self::$stmt = self::getInstance()->prepare($sql_query);
		self::$zeit += self::microtime_diff($timer_start,microtime());
		return self::$stmt;
	}

	/**
	 * Ausführen des vorbereiteten SQL Query mit optionalen Parametern/Daten.
	 *
	 * @param array $sql_param Eine Array von Parametern/Daten für den Query.
	 * @param bool $single Gibt an ob bei erfolg nur der erste Datensatz zurückgeliefert werden soll.
	 * @throws Exception
	 * @return mixed Wenn single = true wird der erste Datensatz zurückgegeben, ansonsten das Objekt für die Datenbank abfrage welches dann per fetch()/fetch_all() abgerufen werden kann.
	 *
	 * Die Anzahl der zurückgegebenen Zeilen kann mittels "Object->rowCount()" ermittelt werden.
	 */
	static public function execute($sql_param, $single = false){
		try {
			$timer_start = microtime();
			self::$stmt->execute($sql_param);
			self::$abfragen ++;
			self::$zeit += self::microtime_diff($timer_start,microtime());
			if ($single){
				return(self::$stmt->fetch());
			} else {
				return(self::$stmt);
			}
		}

		catch(PDOException $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Direktes ausführen eines SQL Query ohne optionale Parametern/Daten.
	 *
	 * @param string $sql_query Der Query den man ausführen möchte.
	 * @param bool $single Gibt an ob bei erfolg nur der erste Datensatz zurückgeliefert werden soll.
	 * @throws Exception
	 * @return mixed Wenn single = true wird der erste Datensatz zurückgegeben, ansonsten das Objekt für die Datenbank abfrage welche dann per fetch()/fetch_all()/foreach abgerufen werden kann.
	 *
	 * Die Anzahl der zurückgegebenen Zeilen kann mittels "Object->rowCount()" ermittelt werden.
	 */
	static public function query($sql_query, $single = false){
		try {
			$timer_start = microtime();
			self::$stmt = self::getInstance()->query($sql_query);
			self::$abfragen ++;
			self::$zeit += self::microtime_diff($timer_start,microtime());
			if ($single){
				return(self::$stmt->fetch());
			} else {
				return(self::$stmt);
			}
		}

		catch(PDOException $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Vorbereiten und Ausführen eines SQL Query mit optionalen Parametern/Daten.
	 *
	 * @param string $sql_query Der Query den man vorbereiten möchte (Prepared Statement).
	 * @param array $sql_param Eine Array von Parametern/Daten für den Query.
	 * @param bool $single Gibt an ob bei erfolg nur der erste Datensatz zurückgeliefert werden soll.
	 * @throws Exception
	 * @return mixed Wenn single = true wird der erste Datensatz zurückgegeben, ansonsten das Objekt für die Datenbank abfrage welches dann per fetch()/fetch_all() abgerufen werden kann.
	 */
	static public function query_new($sql_query, $sql_param, $single = false){
		try {
			$timer_start = microtime();
			self::$stmt = self::getInstance()->prepare($sql_query);
			self::$stmt->execute($sql_param);
			self::$abfragen ++;
			self::$zeit += self::microtime_diff($timer_start,microtime());
			if ($single){
				//return(self::$stmt->fetch(PDO::FETCH_ASSOC));
				return(self::$stmt->fetch());
			} else {
				return(self::$stmt);
			}
		}

		catch(PDOException $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
 	 * Anzahl der DB zugriffe
 	 *
 	 * @return string Gibt die Summe der bisher stattgefundenen Datenbank zugriffe zurück
 	 */
	static public function Abfragen(){
		return self::$abfragen;
	}

	/**
 	 * Zeit der DB Abfragen
 	 *
 	 * @return string Gibt die Zeit in ms zurück, welche für die bisher stattgefundenen Datenbank zugriffe benötigt wurde
 	 */
	static public function Zeit(){
		return self::$zeit;
	}

	/**
	 * Microtime Diff
	 *
	 * @param int $startzeit The start time
	 * @param int $endzeit The end time
	 * @return int Returns the time difference between startzeit and endzeit
	 */
	static private function microtime_diff($startzeit,$endzeit){
		$startzeit=explode(' ',$startzeit);
		$endzeit=explode(' ',$endzeit);
		return round($endzeit[0]-$startzeit[0]+$endzeit[1]-$startzeit[1],6);
	}

	static public function setDatabase(){
		self::$stmt = self::getInstance()->query('use '.self::$config['database']);
		self::$stmt->closeCursor();
	}
}
