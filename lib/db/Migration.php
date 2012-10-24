<?php

/**
 * Recipe for database scheme and/or content migration
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class Migration {

	const MIGRATIONS_DIR = "var/migrations/";
	const OPERATION_UPGRADE = "upgrade";
	const OPERATION_DOWNGRADE = "downgrade";

	/**
	 * Migration version number
	 * @var string
	 */
	private $version;

	/**
	 * Subsequent migration in chain
	 * @var Migration|NULL
	 */
	private $next;

	/**
	 * Migration instances cached by version number
	 * @var Migration[]
	 */
	private static $instances = array();

	/**
	 * Constructor
	 *
	 * @param string $version
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	protected function __construct($version) {
		$this->version = $version;
	}

	public static function get($version) {
		isset(self::$instances[$version]) or self::$instances[$version] = new Migration($version);
		return self::$instances[$version];
	}

	protected function performOperation($operation) {
		$pdo = DB::getPDO();

		// begin transaction if needed
		if ($pdo->inTransaction()) {
			$started = FALSE;
		} else {
			$started = TRUE;
			$pdo->beginTransaction();
		}

		// perform specific operation
		$dir = ROOT_DIR . Migration::MIGRATIONS_DIR . $this->version . DIRECTORY_SEPARATOR . $operation;
		$files = array();
		foreach (new DirectoryIterator($dir) as $entry) {
			$entry->isDir() or $files[] = $entry->getFilename();
		}
		foreach ($files as $file) {
			$sql = file_get_contents($file);
			if (!$pdo->exec($sql)) {
				$started and $pdo->rollBack();
				return FALSE;
			}
		}

		// perform operation on chained migration
		if ($this->next and !$this->next->performOperation($operation)) {
			$started and $pdo->rollBack();
			return FALSE;
		}

		// commit transaction if started
		$started and $pdo->commit();
	}

}