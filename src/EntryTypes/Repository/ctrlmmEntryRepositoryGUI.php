<?php

namespace srag\Plugins\CtrlMainMenu\EntryTypes\Repository;

use ilLink;
use ilNavigationHistoryGUI;
use ilObject;
use ilUtil;
use srag\Plugins\CtrlMainMenu\GroupedListDropdown\ctrlmmEntryGroupedListDropdownGUI;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * ctrlmmEntryRepositoryGUI
 *
 * @package srag\Plugins\CtrlMainMenu\EntryTypes\Repository
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
	 * @return string html
	 */
	public function setGroupedListContent() {
		$this->setFirstEntry();
		$this->setRecentlyVisitedEntries();
		$this->setRemoveEntryButton();
	}


	protected function setFirstEntry() {
		$icon = ilUtil::img(ilObject::_getIcon(ilObject::_lookupObjId(1), "tiny"));
		$str = self::dic()->language()->txt('repository');
		$this->gl->addEntry($icon . " {$str} - " . self::dic()->language()->txt("rep_main_page"), ilLink::_getStaticLink(1, 'root', true), "_top");
	}


	/**
	 * setItems
	 */
	protected function setRecentlyVisitedEntries() {
		$items = self::dic()->history()->getItems();
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
					$this->gl->addGroupHeader(self::dic()->language()->txt("last_visited"), "ilLVNavEnt");
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
			$this->gl->addEntry("» " . self::dic()->language()->txt("remove_entries"), "#", "", "return il.MainMenu.removeLastVisitedItems('"
				. self::dic()->ctrl()->getLinkTargetByClass(ilNavigationHistoryGUI::class, "removeEntries", "", true) . "');", "ilLVNavEnt");
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
