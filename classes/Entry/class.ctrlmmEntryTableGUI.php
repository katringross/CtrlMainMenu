<?php
require_once('./Services/Table/classes/class.ilTable2GUI.php');
require_once('class.ctrlmmEntry.php');
require_once('./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/EntryInstaceFactory/class.ctrlmmEntryInstaceFactory.php');

/**
 * TableGUI ctrlmmEntryTableGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryTableGUI extends ilTable2GUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilCtrlMainMenuPlugin
	 */
	protected $pl;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;


	/**
	 * @param ilCtrlMainMenuConfigGUI $a_parent_obj
	 * @param string                  $a_parent_cmd
	 * @param int                     $parent_id
	 */
	public function __construct(ilCtrlMainMenuConfigGUI $a_parent_obj, $a_parent_cmd, $parent_id = 0) {
		global $DIC;
		$this->pl = ilCtrlMainMenuPlugin::getInstance();
		$this->ctrl = $DIC->ctrl();
		$this->tabs = $DIC->tabs();

		$this->setId('mm_entry_list');
		parent::__construct($a_parent_obj, $a_parent_cmd);
		$this->setRowTemplate('tpl.entry_list.html', $this->pl->getDirectory());
		$this->setTitle($this->pl->txt('list_title'));
		//
		// Columns
		$this->addColumn('', 'position', '20px');
		$this->addColumn($this->pl->txt('common_title'), 'title', 'auto');
		$this->addColumn($this->pl->txt('common_type'), 'type', 'auto');
		$this->addColumn($this->pl->txt('common_actions'), 'actions', '100px');
		// ...
		// Header
		$button = ilLinkButton::getInstance();
		$button->setCaption($this->pl->txt('add_new'), false);
		$button->setUrl($this->ctrl->getLinkTarget($a_parent_obj, 'selectEntryType'));
		$DIC->toolbar()->addButtonInstance($button);
		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));
		if (ctrlmmMenu::isOldILIAS()) {
			$this->addCommandButton('saveSortingOld', $this->pl->txt('save_sorting'));
		} else {
			$this->addCommandButton('saveSorting', $this->pl->txt('save_sorting'));
		}
		// $this->setExternalSorting(true);
		// $this->setExternalSegmentation(true);s
		$this->setLimit(500);

		ctrlmmMenu::includeAllTypes();
		$this->setData(ctrlmmEntryInstaceFactory::getAllChildsForIdAsArray($parent_id));
	}


	/**
	 * @var int
	 */
	protected static $num = 1;


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		/**
		 * @var $obj ctrlmmEntry
		 */
		$obj = ctrlmmEntryInstaceFactory::getInstanceByEntryId($a_set['id'])->getObject();

		if ($obj->getTypeId() == ctrlmmMenu::TYPE_SEPARATOR) {
			$this->tpl->setVariable('CLASS', 'ctrlmmSeparator');
		}

		$this->tpl->setVariable('TITLE', $obj->getTitleInAdministration() . ' ' . ($obj->checkPermission() ? '' : '*'));
		$this->tpl->setVariable('TYPE', ctrlmmEntryInstaceFactory::getClassAppendForValue($obj->getTypeId()));
		$this->ctrl->setParameter($this->parent_obj, 'entry_id', $obj->getId());
		if (ctrlmmMenu::isOldILIAS()) {
			$this->tpl->setVariable('ID_OLD', $obj->getId());
			$this->tpl->setVariable('POSITION', self::$num);
			self::$num ++;
		} else {
			$this->tpl->setVariable('ID_NEW', $obj->getId());
		}
		if (!$obj->getPlugin()) {

			$actions = new ilAdvancedSelectionListGUI();
			$actions->setId('actions_' . $obj->getId());
			$actions->setListTitle($this->pl->txt('common_actions'));
			if ($obj->getTypeId() != ctrlmmMenu::TYPE_SEPARATOR) {
				$actions->addItem($this->pl->txt('common_edit'), 'edit', $this->ctrl->getLinkTarget($this->parent_obj, 'editEntry'));
				//				$actions->addItem($this->pl->txt('common_edit'), 'edit', $this->ctrl->getLinkTargetByClass('ctrlmmEntryGUI', 'edit')); FSX TODO REFACTORING
			}
			if ($obj->getTypeId() != ctrlmmMenu::TYPE_ADMIN) {
				$actions->addItem($this->pl->txt('common_delete'), 'delete', $this->ctrl->getLinkTarget($this->parent_obj, 'deleteEntry'));
			}
			if ($obj->getTypeId() == ctrlmmMenu::TYPE_DROPDOWN) {
				$actions->addItem($this->pl->txt('edit_childs'), 'edit_childs', $this->ctrl->getLinkTarget($this->parent_obj, 'editChilds'));
			}
			$this->tpl->setVariable('ACTIONS', $actions->getHTML());
		}
	}
}

