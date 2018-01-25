<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2009 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntry.php');

/**
 * Application class for ctrlmmEntryCtrl Object.
 *
 * @author         Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version        2.0.02
 */
class ctrlmmEntryCtrl extends ctrlmmEntry {

	const DEBUG = false;
	const PARAM_NAME = 'param_name';
	const PARAM_VALUE = 'param_value';
	/**
	 * @var string
	 */
	protected $gui_class = '';
	/**
	 * @var string
	 */
	protected $cmd = '';
	/**
	 * @var string
	 */
	protected $additions = '';
	/**
	 * @var int
	 */
	protected $ref_id = NULL;
	/**
	 * @var array
	 */
	protected $get_params = array();
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;


	/**
	 * @param int $id
	 */
	function __construct($id = 0) {
		global $DIC;

		$this->setTypeId(ctrlmmMenu::TYPE_CTRL);
		$this->restricted = false;
		$this->ctrl = $DIC->ctrl();

		parent::__construct($id);
	}


	/**
	 * @return bool
	 */
	public function isActive() {
		if (!$this->isActiveStateCached()) {
			$this->setCachedActiveState(false);
			$classes = array();
			foreach (explode(',', $this->getGuiClass()) as $classname) {
				$classes[] = strtolower($classname);
			}
			foreach ($this->ctrl->getCallHistory() as $class) {
				$strtolower = strtolower($class['class']);
				if (in_array($strtolower, $classes)) {
					$this->setCachedActiveState(true);
					break;
				}
			}
		}

		return $this->getCachedActiveState();
	}


	/**
	 * @return null|string
	 */
	protected function getError() {
		if (!$this->checkCtrl()) {
			return 'ilCtrl-Error';
		}

		return NULL;
	}


	/**
	 * @return bool
	 * @throws \Exception
	 */
	protected function checkCtrl() {
		$gui_classes = @explode(',', $this->getGuiClass());
		try {
			$this->ctrl->getLinkTargetByClass($gui_classes, $this->getCmd());
		} catch (Exception $e) {
			if (self::DEBUG) {
				throw $e;
			}

			return false;
		}

		return true;
	}


	/**
	 * @return string
	 */
	public function getLink() {
		if (!$this->checkCtrl()) {
			return NULL;
		}
		$gui_classes = @explode(',', $this->getGuiClass());

		$link = $this->ctrl->getLinkTargetByClass($gui_classes, $this->getCmd());
		if ($this->getAdditions()) {
			$link .= '&' . $this->getAdditions();
		}
		if ($this->getRefId()) {
			$link .= '&ref_id=' . $this->getRefId();
		}

		if (is_array($this->getGetParams())) {
			foreach ($this->getGetParams() as $entry) {
				if ($entry[self::PARAM_NAME] != "") {
					$link .= '&' . $entry[self::PARAM_NAME] . '=' . ctrlmmUserDataReplacer::parse($entry[self::PARAM_VALUE]);
				}
			}
		}

		return $link;
	}


	/**
	 * @param string $cmd
	 */
	public function setCmd($cmd) {
		$this->cmd = $cmd;
	}


	/**
	 * @return string
	 */
	public function getCmd() {
		return $this->cmd;
	}


	/**
	 * @param string $gui_class
	 */
	public function setGuiClass($gui_class) {
		$this->gui_class = $gui_class;
	}


	/**
	 * @return string
	 */
	public function getGuiClass() {
		return $this->gui_class;
	}


	/**
	 * @param string $additions
	 */
	public function setAdditions($additions) {
		$this->additions = $additions;
	}


	/**
	 * @return string
	 */
	public function getAdditions() {
		return $this->additions;
	}


	/**
	 * @param int $ref_id
	 */
	public function setRefId($ref_id) {
		$this->ref_id = $ref_id;
	}


	/**
	 * @return int
	 */
	public function getRefId() {
		return $this->ref_id;
	}


	/**
	 * @return mixed
	 */
	public function getGetParams() {
		return $this->get_params;
	}


	/**
	 * @param mixed $get_params
	 */
	public function setGetParams($get_params) {
		$this->get_params = $get_params;
	}
}