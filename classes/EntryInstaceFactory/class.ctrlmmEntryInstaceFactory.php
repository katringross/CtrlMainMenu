<?php

/**
 * Class ctrlmmEntryInstaceFactory
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ctrlmmEntryInstaceFactory {

	/**
	 * @var int
	 */
	protected $type_id = ctrlmmMenu::TYPE_LINK;
	/**
	 * @var int
	 */
	protected $entry_id = 0;
	/**
	 * @var string
	 */
	protected $class_name = '';
	/**
	 * @var array
	 */
	protected static $type_id_cache = array();
	/**
	 * @var array
	 */
	protected static $childs_cache = array();


	/**
	 * @param     $type_id
	 * @param int $entry_id
	 */
	protected function __construct($type_id, $entry_id = 0) {
		ctrlmmMenu::includeAllTypes();
		$this->setEntryId($entry_id);
		$this->setTypeId($type_id);
		$this->setClassName('ctrlmmEntry' . self::getClassAppendForValue($type_id));
	}


	/**
	 * @param $id
	 *
	 * @return ctrlmmEntry[]
	 */
	public static function getAllChildsForId($id) {
		if (! isset(self::$childs_cache[$id])) {
			global $ilDB;
			/**
			 * @var $ilDB ilDB
			 */
			$childs = array();
			$set = $ilDB->query('SELECT id FROM ' . ctrlmmEntry::TABLE_NAME . ' ' . ' WHERE parent = ' . $ilDB->quote($id, 'integer')
				. ' ORDER by position ASC');
			while ($rec = $ilDB->fetchObject($set)) {
				$childs[] = self::getInstanceByEntryId($rec->id);
			}
			if (count($childs) == 0 AND $id == 0) {
				$childs[] = self::getInstanceByTypeId(ctrlmmMenu::TYPE_ADMIN);
			}

			$childs = array_merge($childs, self::getPluginEntries($id));

			self::$childs_cache[$id] = $childs;
		}

		return self::$childs_cache[$id];
	}


	/**
	 * @param int $id
	 *
	 * @return array
	 */
	protected static function getPluginEntries($id = 0) {
		$childs = array();
		foreach (ilPluginAdmin::$active_plugins as $slot) {
			foreach ($slot as $hook) {
				foreach ($hook as $pls) {
					foreach ($pls as $pl) {
						$plugin_class = 'il' . $pl . 'Plugin';
						if (method_exists($plugin_class, 'getMenuEntries')) {
							$childs = array_merge($childs, $plugin_class::getMenuEntries($id));
						}
					}
				}
			}
		}

		return $childs;
	}


	/**
	 * @param $type_id
	 *
	 * @return \ctrlmmEntryInstaceFactory
	 */
	public static function getInstanceByTypeId($type_id) {
		return new self($type_id);
	}


	/**
	 * @param $entry_id
	 *
	 * @return \ctrlmmEntryInstaceFactory
	 */
	public static function getInstanceByEntryId($entry_id) {
		if (! isset(self::$type_id_cache[$entry_id])) {
			global $ilDB;

			/**
			 * @var $ilDB ilDB
			 */

			$sql = 'SELECT type FROM ui_uihk_ctrlmm_e WHERE id = ' . $ilDB->quote($entry_id, 'integer');
			$set = $ilDB->query($sql);
			$res = $ilDB->fetchObject($set);

			self::$type_id_cache[$entry_id] = $res->type;
		}

		return new self(self::$type_id_cache[$entry_id], $entry_id);
	}


	/**
	 * @return ctrlmmEntryCtrl
	 *
	 * TODO FSX add caching
	 */
	public function getObject() {
		/**
		 * @var $entry_class  ctrlmmEntryCtrl
		 */
		$entry_class = $this->getClassName();

		return new $entry_class($this->getEntryId());
	}


	/**
	 * @param null $parent_gui
	 *
	 * @return ctrlmmEntryCtrlGUI
	 */
	public function getGUIObject($parent_gui = NULL) {
		/**
		 * @var $entry_class  ctrlmmEntryCtrl
		 * @var $gui_class    ctrlmmEntryCtrlGUI
		 * @var $gui_object   ctrlmmEntryCtrlGUI
		 */
		$entry_class = $this->getClassName();
		$gui_class = $entry_class . 'GUI';

		$gui_object = new $gui_class($this->getObject(), $parent_gui);

		return $gui_object;
	}


	/**
	 * @param $parent_gui
	 *
	 * @return ctrlmmEntryCtrlFormGUI
	 */
	public function getFormObject($parent_gui) {
		/**
		 * @var $entry_class  ctrlmmEntryCtrl
		 * @var $gui_class    ctrlmmEntryCtrlFormGUI
		 * @var $gui_object   ctrlmmEntryCtrlFormGUI
		 */
		$entry_class = $this->getClassName();
		$gui_class = $entry_class . 'FormGUI';

		$gui_object = new $gui_class($parent_gui, $this->getObject());

		return $gui_object;
	}


	/**
	 * @param string $class_name
	 */
	public function setClassName($class_name) {
		$this->class_name = $class_name;
	}


	/**
	 * @return string
	 */
	public function getClassName() {
		return $this->class_name;
	}


	/**
	 * @param int $entry_id
	 */
	public function setEntryId($entry_id) {
		$this->entry_id = $entry_id;
	}


	/**
	 * @return int
	 */
	public function getEntryId() {
		return $this->entry_id;
	}


	/**
	 * @param int $type_id
	 */
	public function setTypeId($type_id) {
		$this->type_id = $type_id;
	}


	/**
	 * @return int
	 */
	public function getTypeId() {
		return $this->type_id;
	}


	/**
	 * @param $type_id
	 *
	 * @return ctrlmmEntry
	 */
	public static function getNewInstanceForTypeId($type_id) {
		ctrlmmMenu::includeAllTypes();
		$type = 'ctrlmmEntry' . self::getClassAppendForValue($type_id);
		$object = new $type(0);

		return $object;
	}


	/**
	 * @param $id
	 *
	 * @return string
	 */
	public static function getClassConstantForId($id) {
		$constants = array_flip(ctrlmmMenu::getAllTypeConstants());

		return $constants[$id];
	}


	/**
	 * @param $id
	 *
	 * @return string
	 */
	public static function getClassAppendForValue($id) {
		return ucfirst(strtolower(str_ireplace('TYPE_', '', self::getClassConstantForId($id))));
	}
}

?>
