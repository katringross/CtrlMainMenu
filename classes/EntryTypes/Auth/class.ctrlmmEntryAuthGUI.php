<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntryGUI.php');
require_once('./Services/Search/classes/class.ilMainMenuSearchGUI.php');

/**
 * ctrlmmEntryAuthGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryAuthGUI extends ctrlmmEntryGUI {

	/**
	 * @var ctrlmmEntryAuth
	 */
	public $entry;


	/**
	 * @param string $entry_div_id
	 *
	 * @return string
	 */
	public function renderEntry($entry_div_id = '') {
		unset($entry_div_id);
		$this->tpl->addCss('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/templates/css/login.css');
		$this->html = $this->pl->getVersionTemplate('tpl.ctrl_menu_entry.html', true, true);
		$this->html->setVariable('TITLE', $this->entry->getTitle());
		$this->html->setVariable('CSS_ID', 'ctrl_mm_e_' . $this->entry->getId());
		$this->html->setVariable('LINK', $this->entry->getLink());

		$this->html->setVariable('CSS_PREFIX', ctrlmmMenu::getCssPrefix());
		$this->html->setVariable('TARGET', $this->entry->getTarget());
		$this->html->setVariable('STATE', ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_INACTIVE));
		$this->html->setVariable('CTRLMM_CLASS', $this->entry->isLoggedIn() ? 'ctrlMMLoggedIn' : 'ctrlMMLoggedout');

		if ($this->entry->isLoggedIn()) {
			$this->html->setVariable('NONLINK', $this->entry->getUsername());
		} else {
			$this->html->setVariable('NONLINK', $this->lng->txt('not_logged_in'));
		}

		return $this->html->get();
	}
}


