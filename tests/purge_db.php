#!/usr/bin/env php
<?php

require_once __DIR__ . "/../bootstrap.php";

DB::useSilo("testing");
DB::drop();
file_put_contents(__DIR__ . "/../var/migrations/version.testing.txt", "");
