#!/usr/bin/env php
<?php

require_once dirname(__FILE__) . "/../bootstrap.php";

if (4 != $argc) {
	fwrite(STDERR, "\nUsage: " . basename(__FILE__) . " <operation> <silo> <version>\nWhere: <operation> - 'upgrade' or 'downgrade'\n       <silo> - 'production' or 'testing'\n       <version> - migration identifier\n");
	exit(1);
}

$operation = $argv[1];
if ($operation != "upgrade" and $operation != "downgrade") {
	frwite(STDERR, "\nOperation can only be 'upgrade' or 'downgrade'\n");
	exit(1);
}

$silo = $argv[2];
if ($silo !== "production" and $silo != "testing") {
	fwrite(STDERR, "\nSilo can only be 'production' or 'testing'\n");
	exit(1);
}

$version = $argv[3];

try {
	DB::migrate($operation === "upgrade", $silo, $version);
} catch (DBMigrationException $e) {
	fwrite(STDERR, "\nMigration failed: {$e->getMessage()}\n");
	exit(1);
} catch (Exception $e) {
	fwrite(STDERR, "\nException: {$e->getMessage()}\n");
	exit(1);
}