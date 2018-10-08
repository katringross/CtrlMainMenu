<?php

namespace srag\Plugins\CtrlMainMenu\EntryTypes\Statusbox;

use ctrlmmEntryGUI;
use ilUtil;
use srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig;
use srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * ctrlmmEntryStatusboxGUI
 *
 * @package srag\Plugins\CtrlMainMenu\EntryTypes\Statusbox
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryStatusboxGUI extends ctrlmmEntryGUI {

	/**
	 * @var ctrlmmEntryStatusbox
	 */
	public $entry;


	/**
	 * @param string $entry_div_id
	 *
	 * @return string
	 */
	public function renderEntry($entry_div_id = '') {
		unset($entry_div_id);
		self::dic()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/css/statusbox.css');

		$this->html = self::plugin()->template('tpl.menu_statusbox.html', false, true);
		$this->html->setVariable('ICON', ilUtil::getImagePath('icon_mail_s.png'));
		$this->html->setVariable('CSS_ID', 'ctrl_mm_e_' . $this->entry->getId());
		$this->html->setVariable('LINK', $this->entry->getLink());
		$this->html->setVariable('CSS_PREFIX', ctrlmmMenu::getCssPrefix());
		$this->html->setVariable('NEWMAIL', $this->entry->getNewMailCount());
		$this->html->setVariable('TARGET', $this->entry->getTarget());
		$this->html->setVariable('STATE', ($this->entry->isActive() ? ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_ACTIVE) : ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_INACTIVE)));

		return $this->html->get();
	}
}
