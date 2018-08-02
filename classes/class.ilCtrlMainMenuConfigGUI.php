<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * CtrlMainMenu Configuration
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Michael Herren <mh@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ilCtrlMainMenuConfigGUI extends ilPluginConfigGUI {

	const CMD_ADD_ENTRY = 'addEntry';
	const CMD_CACHE_SETTINGS = 'cacheSettings';
	const CMD_CLEAR_CACHE = 'clearCache';
	const CMD_CSS_SETTINGS = 'cssSettings';
	const CMD_CONFIGURE = 'configure';
	const CMD_CREATE_ENTRY = 'createEntry';
	const CMD_CREATE_OBJECT = 'createObject';
	const CMD_CREATE_OBJECT_AND_STAY = 'createObjectAndStay';
	const CMD_DELETE_OBJECT = 'deleteObject';
	const CMD_EDIT_CHILDS = 'editChilds';
	const CMD_EDIT_ENTRY = 'editEntry';
	const CMD_DELETE_ENTRY = 'deleteEntry';
	const CMD_RESET_PARENT = 'resetParent';
	const CMD_SAVE = 'save';
	const CMD_SAVE_SORTING = 'saveSorting';
	const CMD_SELECT_ENTRY_TYPE = 'selectEntryType';
	const CMD_UPDATE_CACHE_SETTINGS = 'updateCacheSettings';
	const CMD_UPDATE_OBJECT = 'updateObject';
	const CMD_UPDATE_OBJECT_AND_STAY = 'updateObjectAndStay';
	const TAB_CACHE = 'cache';
	const TAB_CSS = 'css';
	const TAB_DROPDOWN = 'child_admin';
	const TAB_MAIN = 'mm_admin';
	/**
	 *
	 * @var array
	 */
	protected $fields = array();
	/**
	 * @var ilCtrlMainMenuPlugin
	 */
	protected $pl;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilToolbarGUI
	 */
	protected $toolbar;
	/**
	 * @var ilLanguage
	 */
	protected $lng;
	/**
	 * @var ilPropertyFormGUI
	 */
	protected $form;


	public function __construct() {
		global $DIC;
		$this->ctrl = $DIC->ctrl();
		$this->tpl = $DIC->ui()->mainTemplate();
		$this->tabs = $DIC->tabs();
		$this->pl = ilCtrlMainMenuPlugin::getInstance();
		$this->toolbar = $DIC->toolbar();
		$this->lng = $DIC->language();
		if ($_GET['rl']) {
			$this->pl->updateLanguages();
		}
		$this->tpl->addJavaScript($this->pl->getDirectory() . '/templates/js/sortable.js');

		ctrlmmMenu::includeAllTypes();
	}


	public function executeCommand() {
		$next_class = $this->ctrl->getNextClass();
		switch ($next_class) {
			case'ctrlmmentrygui':
				$entrygui = ctrlmmEntryInstaceFactory::getInstanceByEntryId($_GET['entry_id'])->getGUIObject($this);
				$this->ctrl->forwardCommand($entrygui);
				break;
			default:
				parent::executeCommand(); // TODO: Change the autogenerated stub
				break;
		}
	}


	/**
	 * @return array
	 */
	public function getFields() {
		$this->fields = array(
			'css_prefix' => array(
				'type' => ilTextInputGUI::class,
			),
			'css_active' => array(
				'type' => ilTextInputGUI::class,
			),
			'css_inactive' => array(
				'type' => ilTextInputGUI::class,
			),
			'doubleclick_prevention' => array(
				'type' => ilCheckboxInputGUI::class,
			),
			'simple_form_validation' => array(
				'type' => ilCheckboxInputGUI::class,
			),
			'replace_full_header' => array(
				'type' => ilCheckboxInputGUI::class,
			),
		);

		return $this->fields;
	}


	/**
	 * Handles all commmands, default is self::CMD_CONFIGURE
	 */
	function performCommand($cmd) {
		$this->ctrl->setParameter($this, 'parent_id', $_GET['parent_id'] ? $_GET['parent_id'] : 0);
		if ($_GET['parent_id'] > 0) {
			$this->tabs->addTab(self::TAB_MAIN, $this->pl->txt('back_to_main'), $this->ctrl->getLinkTarget($this, self::CMD_RESET_PARENT));
			$this->tabs->addTab(self::TAB_DROPDOWN, $this->pl->txt('tabs_title_childs'), $this->ctrl->getLinkTarget($this, self::CMD_CONFIGURE));
			$this->tabs->activateTab(self::TAB_DROPDOWN);
		} else {
			$this->tabs->addTab(self::TAB_MAIN, $this->pl->txt('tab_main'), $this->ctrl->getLinkTarget($this, self::CMD_CONFIGURE));
			$this->tabs->activateTab(self::TAB_MAIN);
		}
		$this->tabs->addTab(self::TAB_CSS, $this->pl->txt('css_settings'), $this->ctrl->getLinkTarget($this, self::CMD_CSS_SETTINGS));
		$this->tabs->addTab(self::TAB_CACHE, $this->pl->txt('cache_settings'), $this->ctrl->getLinkTarget($this, self::CMD_CACHE_SETTINGS));
		switch ($cmd) {
			case self::CMD_CONFIGURE:
			case self::CMD_SAVE:
			case self::CMD_SAVE_SORTING:
			case self::CMD_ADD_ENTRY:
			case self::CMD_CREATE_ENTRY:
			case self::CMD_SELECT_ENTRY_TYPE:
			case self::CMD_CLEAR_CACHE:
			case self::CMD_RESET_PARENT:
			case self::CMD_CSS_SETTINGS:
			case self::CMD_EDIT_ENTRY:
			case self::CMD_DELETE_ENTRY:
			case self::CMD_EDIT_CHILDS:
			case self::CMD_UPDATE_CACHE_SETTINGS:
			case self::CMD_CREATE_OBJECT:
			case self::CMD_CREATE_OBJECT_AND_STAY:
			case self::CMD_UPDATE_OBJECT:
			case self::CMD_UPDATE_OBJECT_AND_STAY:
			case self::CMD_DELETE_OBJECT:
				$this->$cmd();
				break;
			default:
				$this->$cmd();
				break;
		}
	}


	public function clearCache() {
		ilGlobalCache::flushAll();
		ilUtil::sendInfo($this->pl->txt('cache_cleared'), true);
		$this->ctrl->redirect($this, self::CMD_CACHE_SETTINGS);
	}


	protected function cacheSettings() {
		$button = ilLinkButton::getInstance();
		$button->setCaption($this->pl->txt('clear_cache'), false);
		$button->setUrl($this->ctrl->getLinkTarget($this, self::CMD_CLEAR_CACHE));
		$this->toolbar->addButtonInstance($button);
		$this->tabs->activateTab(self::TAB_CACHE);
		$form = new ilPropertyFormGUI();
		$form->setTitle($this->pl->txt('cache_settings'));
		$form->setFormAction($this->ctrl->getFormAction($this));

		$cb = new ilCheckboxInputGUI($this->pl->txt('activate_cache'), 'activate_cache');
		$cb->setInfo($this->pl->txt('activate_cache_info'));
		$form->addItem($cb);
		$form->setValuesByArray(array( 'activate_cache' => ilCtrlMainMenuConfig::getConfigValue('activate_cache') ));
		$form->addCommandButton(self::CMD_UPDATE_CACHE_SETTINGS, $this->pl->txt('update_cache_settings'));

		$this->tpl->setContent($form->getHTML());
	}


	protected function updateCacheSettings() {
		ilCtrlMainMenuConfig::set('activate_cache', $_POST['activate_cache']);
		ilUtil::sendInfo($this->pl->txt('cache_settings_updated'), true);
		$this->ctrl->redirect($this, self::CMD_CACHE_SETTINGS);
	}


	protected function cssSettings() {
		$this->tabs->activateTab(self::TAB_CSS);
		$this->initConfigurationForm();
		$this->getValues();
		$this->tpl->setContent($this->form->getHTML());
	}


	public function editChilds() {
		$this->ctrl->setParameter($this, 'parent_id', $_GET['entry_id']);
		$this->ctrl->redirect($this, self::CMD_CONFIGURE);
	}


	public function configure() {
		$table = new ctrlmmEntryTableGUI($this, self::CMD_CONFIGURE, $_GET['parent_id'] ? $_GET['parent_id'] : 0);
		$this->tpl->setContent($table->getHTML());
	}


	public function resetParent() {
		$this->ctrl->setParameter($this, 'parent_id', 0);
		$this->ctrl->redirect($this, self::CMD_CONFIGURE);
	}


	public function saveSorting() {
		foreach ($_POST['position'] as $k => $v) {
			$obj = ctrlmmEntryInstaceFactory::getInstanceByEntryId($v)->getObject();
			if ($obj instanceof ctrlmmEntry) {
				$obj->setPosition($k);
				$obj->update();
			}
		}
		ilUtil::sendSuccess($this->pl->txt('sorting_saved'));
		$this->ctrl->redirect($this);
	}


	public function saveSortingOld() {
		foreach ($_POST['id'] as $k => $v) {
			$obj = ctrlmmEntryInstaceFactory::getInstanceByEntryId($k)->getObject();
			$obj->setPosition($v);
			$obj->update();
		}
		ilUtil::sendSuccess($this->pl->txt('sorting_saved'));
		$this->ctrl->redirect($this);
	}


	public function selectEntryType() {
		$select = new ilPropertyFormGUI();
		$select->setFormAction($this->ctrl->getFormAction($this));
		$select->setTitle($this->pl->txt('select_type'));
		$se = new ilSelectInputGUI($this->pl->txt('common_type'), 'type');
		$se->setOptions(ctrlmmMenu::getAllTypesAsArray(true, $_GET['parent_id']));
		$select->addItem($se);
		$select->addCommandButton(self::CMD_ADD_ENTRY, $this->pl->txt('common_select'));
		$select->addCommandButton(self::CMD_CONFIGURE, $this->pl->txt('common_cancel'));
		$this->tpl->setContent($select->getHTML());
	}


	public function addEntry() {
		/**
		 * @var ctrlmmEntryCtrlGUI $entry_gui
		 */
		$type_id = $_POST['type'] ? $_POST['type'] : $_GET['type'];
		$this->ctrl->setParameter($this, 'type', $type_id);
		$entry_gui = ctrlmmEntryInstaceFactory::getInstanceByTypeId($type_id)->getGUIObject($this);
		$entry_gui->initForm();
		$entry_gui->setFormValuesByArray();
		$this->tpl->setContent($entry_gui->form->getHTML());
	}


	public function createObjectAndStay() {
		$this->createObject(false);
		$this->editEntry();
	}


	public function createObject($redirect = true) {
		$type_id = $_POST['type'] ? $_POST['type'] : $_GET['type'];
		$entry_gui = ctrlmmEntryInstaceFactory::getInstanceByTypeId($type_id)->getGUIObject($this);
		$entry_gui->initForm();
		if ($entry_gui->form->checkInput()) {
			$entry_gui->createEntry();
			ilUtil::sendSuccess($this->pl->txt('entry_added'), $redirect);
			if ($redirect) {
				$this->ctrl->redirect($this);
			}
		}
		$entry_gui->form->setValuesByPost();
		$this->tpl->setContent($entry_gui->form->getHTML());
	}


	public function editEntry() {
		/**
		 * @var ctrlmmEntryCtrlGUI     $entry_gui
		 * @var ctrlmmEntryCtrlFormGUI $entry_formgui
		 */
		$this->ctrl->saveParameter($this, 'entry_id');
		$entry_gui = ctrlmmEntryInstaceFactory::getInstanceByEntryId($_GET['entry_id'])->getGUIObject($this);
		$entry_gui->initForm('update');
		$entry_gui->setFormValuesByArray();
		$this->tpl->setContent($entry_gui->form->getHTML());
	}


	public function updateObjectAndStay() {
		$this->updateObject(false);
		$this->editEntry();
	}


	/**
	 * @param bool $redirect
	 */
	public function updateObject($redirect = true) {
		/**
		 * @var ctrlmmEntryCtrlGUI $entry_gui
		 */
		$entry_gui = ctrlmmEntryInstaceFactory::getInstanceByEntryId($_GET['entry_id'])->getGUIObject($this);
		$entry_gui->initForm('update');
		if ($entry_gui->form->checkInput()) {
			$entry_gui->createEntry();
			ilUtil::sendSuccess($this->pl->txt('entry_updated'), $redirect);
			if ($redirect) {
				$this->ctrl->redirect($this);
			}
		}
		$entry_gui->form->setValuesByPost();
		$this->tpl->setContent($entry_gui->form->getHTML());
	}


	public function deleteEntry() {
		$entry = ctrlmmEntryInstaceFactory::getInstanceByEntryId($_GET['entry_id'])->getObject();
		$conf = new ilConfirmationGUI();
		ilUtil::sendQuestion($this->pl->txt('qst_delete_entry'));
		$conf->setFormAction($this->ctrl->getFormAction($this));
		$conf->setConfirm($this->pl->txt('common_delete'), self::CMD_DELETE_OBJECT);
		$conf->setCancel($this->pl->txt('common_cancel'), self::CMD_CONFIGURE);
		$conf->addItem('entry_id', $_GET['entry_id'], $entry->getTitle());
		$this->tpl->setContent($conf->getHTML());
	}


	public function deleteObject() {
		/**
		 * @var ctrlmmEntry $entry
		 */
		$entry = ctrlmmEntryInstaceFactory::getInstanceByEntryId($_POST['entry_id'])->getObject();
		$entry->delete();

		ilUtil::sendSuccess($this->pl->txt('entry_deleted'));
		$this->ctrl->redirect($this, self::CMD_CONFIGURE);
	}


	//
	// Default Configuration
	//
	public function getValues() {
		foreach ($this->getFields() as $key => $item) {
			$values[$key] = ilCtrlMainMenuConfig::getConfigValue($key);
			if (is_array($item['subelements'])) {
				foreach ($item['subelements'] as $subkey => $subitem) {
					$values[$key . '_' . $subkey] = ilCtrlMainMenuConfig::getConfigValue($key . '_' . $subkey);
				}
			}
		}
		$this->form->setValuesByArray($values);
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	public function initConfigurationForm() {
		$this->form = new ilPropertyFormGUI();
		foreach ($this->getFields() as $key => $item) {
			$field = new $item['type']($this->pl->txt($key), $key);
			if ($item['info']) {
				$field->setInfo($this->pl->txt($key . '_info'));
			}
			if (is_array($item['subelements'])) {
				foreach ($item['subelements'] as $subkey => $subitem) {
					$subfield = new $subitem['type']($this->pl->txt($key . '_' . $subkey), $key . '_' . $subkey);
					if ($subitem['info']) {
						$subfield->setInfo($this->pl->txt($key . '_info'));
					}
					$field->addSubItem($subfield);
				}
			}
			$this->form->addItem($field);
		}
		$this->form->addCommandButton(self::CMD_SAVE, $this->lng->txt('save'));
		$this->form->setTitle($this->pl->txt('common_configuration'));
		$this->form->setFormAction($this->ctrl->getFormAction($this));

		return $this->form;
	}


	public function save() {
		$this->initConfigurationForm();
		if ($this->form->checkInput()) {
			foreach ($this->getFields() as $key => $item) {
				ilCtrlMainMenuConfig::set($key, $this->form->getInput($key));
				if (is_array($item['subelements'])) {
					foreach ($item['subelements'] as $subkey => $subitem) {
						ilCtrlMainMenuConfig::set($key . '_' . $subkey, $this->form->getInput($key . '_' . $subkey));
					}
				}
			}
			ilUtil::sendSuccess($this->pl->txt('conf_saved'), true);
			$this->ctrl->redirect($this, self::CMD_CSS_SETTINGS);
		} else {
			$this->form->setValuesByPost();
			$this->tpl->setContent($this->form->getHtml());
		}
	}
}
