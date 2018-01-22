<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/GroupedListDropdown/class.ctrlmmEntryGroupedListDropdownGUI.php');
require_once("./Services/Link/classes/class.ilLink.php");

/**
 * ctrlmmEntryRepositoryGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Timon Amstutz <timon.amstutz@ilub.unibe.ch>
 * @author  Michael Herren <mh@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryRepositoryGUI extends ctrlmmEntryGroupedListDropdownGUI {

	/**
	 * @var ctrlmmEntryRepository
	 */
	public $entry;
	/**
	 * @var int
	 */
	protected $nr_of_items = 0;


	/**
	 * Render main menu entry
	 *
	 * @param
	 *
	 * @return html
	 */
	public function setGroupedListContent() {
		$this->setFirstEntry();
		$this->setRecentlyVisitedEntries();
		$this->setRemoveEntryButton();
	}


	protected function setFirstEntry() {
		$icon = ilUtil::img(ilObject::_getIcon(ilObject::_lookupObjId(1), "tiny"));
		$str = $this->lng->txt('repository');
		$this->gl->addEntry($icon . " {$str} - " . $this->lng->txt("rep_main_page"), ilLink::_getStaticLink(1, 'root', true), "_top");
	}


	/**
	 * setItems
	 */
	protected function setRecentlyVisitedEntries() {
		$items = $this->ilNavigationHistory->getItems();
		reset($items);
		$this->nr_of_items = 0;
		$first = true;

		foreach ($items as $item) {
			if ($this->nr_of_items >= $this->entry->getMaxHistoryItems()) {
				break;
			}

			// do not list current item
			if (!isset($item["ref_id"]) || !isset($_GET["ref_id"])
				|| ($item["ref_id"] != $_GET["ref_id"]
					|| !$first)) {
				if ($this->nr_of_items == 0) {
					$this->gl->addGroupHeader($this->lng->txt("last_visited"), "ilLVNavEnt");
				}
				$obj_id = ilObject::_lookupObjId($item["ref_id"]);
				$this->nr_of_items ++;
				$icon = ilUtil::img(ilObject::_getIcon($obj_id, "tiny"));
				$ititle = ilUtil::shortenText(strip_tags($item["title"]), 50, true);
				$this->gl->addEntry($icon . " " . $ititle, $item["link"], "_top", "", "ilLVNavEnt");
			}
			$first = false;
		}
	}


	protected function setRemoveEntryButton() {
		if ($this->nr_of_items > 0) {
			$this->gl->addEntry("» " . $this->lng->txt("remove_entries"), "#", "", "return il.MainMenu.removeLastVisitedItems('"
				. $this->ctrl->getLinkTargetByClass("ilnavigationhistorygui", "removeEntries", "", true) . "');", "ilLVNavEnt");
		}
	}


	/**
	 * @return mixed
	 * @deprecated
	 */
	protected function checkAccess() {
		return false;
	}
}


