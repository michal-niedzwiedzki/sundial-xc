#!/usr/bin/env php
<?php

require_once dirname(__FILE__) . "/../bootstrap.php";

DB::useSilo("testing");
DB::create();
