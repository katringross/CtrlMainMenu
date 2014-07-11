<?php

/**
 * Class ctrlmmChecker
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 2.0.02
 */
class ctrlmmChecker {

	/**
	 * @var array
	 */
	protected $classes = array();


	/**
	 * @param $gui_classes
	 */
	public static function check($gui_classes) {
		new self($gui_classes);
	}


	/**
	 * @param $gui_classes
	 */
	private function __construct($gui_classes) {
		$this->initILIAS();
		$this->setClasses(explode(',', $gui_classes));
		$this->printJson();
	}


	protected function printJson() {
		global $ilCtrl;
		/**
		 * @var $ilCtrl ilCtrl
		 */
		header('Content-Type: application/json');
		echo json_encode(array( 'status' => $ilCtrl->checkTargetClass($this->getClasses()) ));
	}


	//
	// Setter & Getter
	//
	/**
	 * @param array $classes
	 */
	public function setClasses($classes) {
		$this->classes = $classes;
	}


	/**
	 * @return array
	 */
	public function getClasses() {
		return $this->classes;
	}


	//
	// Helpers
	//
	private function initILIAS() {
		$path = stristr(__FILE__, 'Customizing', true);
		chdir($path);
		require_once('include/inc.header.php');
	}


	private static function includes() {
	}
}

?>