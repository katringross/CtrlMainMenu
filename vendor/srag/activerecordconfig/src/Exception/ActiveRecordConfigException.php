<?php

namespace srag\ActiveRecordConfig\CtrlMainMenu\Exception;

use ilException;

/**
 * Class ActiveRecordConfigException
 *
 * @package srag\ActiveRecordConfig\CtrlMainMenu\Exception
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class ActiveRecordConfigException extends ilException {

	/**
	 * ActiveRecordConfigException constructor
	 *
	 * @param string $message
	 * @param int    $code
	 *
	 * @access namespace
	 */
	public function __construct(/*string*/
		$message, /*int*/
		$code = 0) {
		parent::__construct($message, $code);
	}
}
