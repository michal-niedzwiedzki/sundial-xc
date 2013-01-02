<?php

/**
 * Config filter
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
abstract class ConfigFilter {

	abstract public function process(stdClass $o);

}