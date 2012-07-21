<?php

require_once dirname(__FILE__) . "/../../bootstrap.php";

ini_set('display_errors', "On");
error_reporting(E_ALL | E_STRICT);

Cron::get(ENV)->runNextJob();
