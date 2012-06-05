<?php

require_once "Spreadsheet/Excel/Writer.php";

class cBackup {

	/**
	 * Array of all table names
	 * @var string[]
	 */
	protected $allTables;

	/**
	 * Spreadsheet
	 * @var Spreadsheet_Excel_Writer
	 */
	public $workbook;

	/**
	 * Constructor
	 *
	 * Initializes spreadsheet object and output file.
	 * Prepares list of know database tables.
	 *
	 * @author unknown
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function __construct() {
		$user = cMember::getCurrent();

		$this->workbook = new Spreadsheet_Excel_Writer();
		$this->workbook->setTempDir('/tmp');

		// TODO: The following should be dynamically generated
		$this->allTables = array(DB::LISTINGS, DB::PERSONS, DB::MEMBERS, DB::TRADES, DB::LOGINS, DB::LOGGING, DB::CATEGORIES, DB::FEEDBACK, DB::REBUTTAL, DB::NEWS);
		$this->workbook->send('export_'. $user->member_id .'.xls');
	}

	/**
	 * Print table headers to spreadsheet
	 *
	 * @param string $tableName
	 * @param Spreadsheet_Excel_Writer $worksheet
	 * @return string[] field names
	 * @author unknown
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function PrintHeaders($tableName, Spreadsheet_Excel_Writer $worksheet) {
		// fetch table description
		$sql = "DESC :tableName";
		$out = PDOHelper::fetchAll($sql, array("tableName" => $tableName));

		// write to spreadsheet
		$fields = array();
		foreach ($out as $i => $row) {
			$worksheet->write(0, $i, $row[0]);
			$fields[$i] = $row[0];
		}
		return $fields;
	}

	/**
	 * Print all tables to spreadsheet
	 *
	 * @author unknown
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function BackupAll() {
		foreach ($this->all_tables as $tableName) {
			$worksheet = $this->workbook->addWorksheet($table_name);
			$fieldNames = $this->PrintHeaders($tableName, $worksheet);
			$sql = "SELECT * FROM :tableName";
			$out = PDOHelper::fetchAll($sql, array("tableName" => $tableName));
			foreach ($out as $rowNum => $row) {
				$colNum = 0;
				foreach ($row as $cell) {
					$worksheet->write($rowNum + 1, $colNum + 1, $cell);
				}
			}
		}
		$this->workbook->close();
	}

}

?>