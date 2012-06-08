<?php

/**
 * Interface of a filter executed before and after dispatch to controller
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
interface DispatchFilter {

	/**
	 * Processing taking place before dispatch
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function before();

	/**
	 * Processing taking place after dispatch
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function after();

}