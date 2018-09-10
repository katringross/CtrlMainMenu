<?php

namespace srag\Plugins\CtrlMainMenu\EntryTypes\Dropdown;

use ilCheckboxInputGUI;
use srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry;
use srag\Plugins\CtrlMainMenu\GroupedListDropdown\ctrlmmEntryGroupedListDropdownGUI;
use srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * ctrlmmEntryDropdownGUI
 *
 * @package srag\Plugins\CtrlMainMenu\EntryTypes\Dropdown
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryDropdownGUI extends ctrlmmEntryGroupedListDropdownGUI {

	const DOWN_ARROW_DARK = 'mm_down_arrow.png'; // ilAdvancedSelectionListGUI::DOWN_ARROW_DARK
	/**
	 * @var ctrlmmEntryDropdown
	 */
	protected $entry;


	/**
	 * @return string
	 */
	public function renderEntry($entry_div_id = '') {
		unset($entry_div_id);
		if (!$this->entry->hasVisibleChilds()) {
			return '';
		}

		return parent::renderEntry();
	}


	/**
	 * Render main menu entry
	 *
	 * @param
	 *
	 * @return string html
	 */
	protected function setGroupedListContent() {
		$entries = $this->entry->getEntries();
		foreach ($entries as $key => $entry) {
			/**
			 * @var ctrlmmEntry $entry
			 */
			if ($entry->checkPermission()) {
				switch ($entry->getTypeId()) {
					case ctrlmmMenu::TYPE_SUBTITLE:
						// only add subtitle if there is a next entry or the option is show with no children is set
						$next_element = (isset($entries[$key ++])) ? $entries[$key ++] : NULL;
						if ($entry->getShowWithNoChildren() || (isset($next_element) && $next_element->checkPermission())) {
							$this->gl->addGroupHeader($entry->getTitle(), $entry->getLink(), $entry->getTarget(), '', '', 'mm_pd_sel_items'
								. $entry->getId(), '', 'left center', 'right center', false);
						}
						break;
					default:
						$this->gl->addEntry($entry->getTitle(), $entry->getLink(), $entry->getTarget(), '', '', 'mm_pd_sel_items'
							. $entry->getId(), '', 'left center', 'right center', false);
				}
			}
		}
	}


	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		parent::initForm($mode);
		$use_image = new ilCheckboxInputGUI(self::plugin()->translate('use_image'), 'use_image');
		$this->form->addItem($use_image);

		$use_user_image = new ilCheckboxInputGUI(self::plugin()->translate('use_user_image'), 'use_user_image');
		$this->form->addItem($use_user_image);
	}


	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values['use_image'] = $this->entry->getUseImage();
		$values['use_user_image'] = $this->entry->getUseUserImage();
		$this->form->setValuesByArray($values);

		return $values;
	}


	public function createEntry() {
		parent::createEntry();
		$this->entry->setUseImage($this->form->getInput('use_image'));
		$this->entry->setUseUserImage($this->form->getInput('use_user_image'));
		$this->entry->update();
	}
}
