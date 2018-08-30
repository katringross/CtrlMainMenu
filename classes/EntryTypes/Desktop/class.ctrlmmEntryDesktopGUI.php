<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ctrlmmEntryDesktopGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryDesktopGUI extends ctrlmmEntryGroupedListDropdownGUI {

	const F_SHOW_LOGOUT = 'show_logout';
	const F_DISABLE_ACTIVE = 'disable_active';
	const CLASS_DISABLED = 'disabled';
	/**
	 * @var ctrlmmEntryDesktop
	 */
	public $entry;
	/**
	 * @var bool
	 */
	protected $mail = false;
	/**
	 * @var bool
	 */
	protected $contacts = false;


	/**
	 * @param ctrlmmEntry $entry
	 * @param null        $parent_gui
	 */
	public function __construct(ctrlmmEntry $entry, $parent_gui = NULL) {
		parent::__construct($entry, $parent_gui);
		$this->mail = ($this->rbacsystem->checkAccess('internal_mail', ilMailGlobalServices::getMailObjectRefId()) AND $this->usr->getId()
			!= ANONYMOUS_USER_ID);
		$this->contacts = ilBuddySystem::getInstance()->isEnabled();
	}


	/**
	 * @return string
	 */
	protected function getAdditionalClasses($class = '') {
		$classes = array( $class );
		if ($this->entry->isDisableActive()) {
			$classes[] = self::CLASS_DISABLED;
		}

		return implode(' ', $classes);
	}


	/**
	 * @param ctrlmmGLEntry $ctrlmmGLEntry
	 */
	protected function addGLEntry(ctrlmmGLEntry $ctrlmmGLEntry) {
		$this->gl->addEntry($ctrlmmGLEntry->getTitle(), $ctrlmmGLEntry->getLink(), '_top', '', implode(' ', $ctrlmmGLEntry->getClasses($this->entry->isDisableActive())), $ctrlmmGLEntry->getId(), ilHelp::getMainMenuTooltip($ctrlmmGLEntry->getId()), 'left center', 'right center', false);
	}


	/**
	 * Render main menu entry
	 *
	 * @return string html
	 */
	public function setGroupedListContent() {
		// Overview
		$ctrlmmGLEntry = new ctrlmmGLEntry();
		$ctrlmmGLEntry->setId('mm_pd_sel_items');
		$ctrlmmGLEntry->setTitle($this->lng->txt('overview'));
		$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToSelectedItems');
		$this->addGLEntry($ctrlmmGLEntry);

		// my groups and courses, if both is available
		if ($this->settings->get('disable_my_offers') == 0 AND $this->settings->get('disable_my_memberships') == 0) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_crs_grp');
			$ctrlmmGLEntry->setTitle($this->lng->txt('my_courses_groups'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToMemberships');
			$this->addGLEntry($ctrlmmGLEntry);
		}

		// bookmarks
		if (!$this->ilias->getSetting('disable_bookmarks')) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_bookm');
			$ctrlmmGLEntry->setTitle($this->lng->txt('bookmarks'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToBookmarks');
			$this->addGLEntry($ctrlmmGLEntry);
		}

		// private notes
		if (!$this->ilias->getSetting('disable_notes')) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_notes');
			$ctrlmmGLEntry->setTitle($this->lng->txt('notes_and_comments'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToNotes');
			$this->addGLEntry($ctrlmmGLEntry);
		}

		// news
		if ($this->settings->get('block_activated_news')) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_news');
			$ctrlmmGLEntry->setTitle($this->lng->txt('news'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToNews');
			$this->addGLEntry($ctrlmmGLEntry);
		}

		// overview is always active
		$this->gl->addSeparator();

		$separator = false;

		if (!$this->settings->get('disable_personal_workspace')) {
			// workspace
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_wsp');
			$ctrlmmGLEntry->setTitle($this->lng->txt('personal_workspace'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToWorkspace');
			$this->addGLEntry($ctrlmmGLEntry);

			$separator = true;
		}

		// portfolio
		if ($this->settings->get('user_portfolios')) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_port');
			$ctrlmmGLEntry->setTitle($this->lng->txt('portfolio'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToPortfolio');
			$this->addGLEntry($ctrlmmGLEntry);

			$separator = true;
		}

		// skills
		$skmg_set = new ilSetting('skmg');
		if ($skmg_set->get('enable_skmg')) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_skill');
			$ctrlmmGLEntry->setTitle($this->lng->txt('skills'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToSkills');
			$this->addGLEntry($ctrlmmGLEntry);

			$separator = true;
		}

		// Learning Progress
		if (ilObjUserTracking::_enabledLearningProgress() AND (ilObjUserTracking::_hasLearningProgressOtherUsers()
				OR ilObjUserTracking::_hasLearningProgressLearner())) {
			//$this->tabs->addTarget('learning_progress', $this->ctrl->getLinkTargetByClass(ilLearningProgressGUI::class)); // TODO: Translate
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_lp');
			$ctrlmmGLEntry->setTitle($this->lng->txt('learning_progress'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToLP');
			$this->addGLEntry($ctrlmmGLEntry);

			$separator = true;
		}

		if ($separator) {
			$this->gl->addSeparator();
		}

		$separator = false;

		// bugfix mantis 23101
		// badges
		if(ilBadgeHandler::getInstance()->isActive())
		{
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_contacts');
			$ctrlmmGLEntry->setTitle($this->lng->txt('obj_bdga'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToBadges');
			$this->addGLEntry($ctrlmmGLEntry);

			$separator = true;
		}

		if ($separator) {
			$this->gl->addSeparator();
		}

		$separator = false;

		// calendar
		$settings = ilCalendarSettings::_getInstance();
		if ($settings->isEnabled()) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_cal');
			$ctrlmmGLEntry->setTitle($this->lng->txt('calendar'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToCalendar');
			$this->addGLEntry($ctrlmmGLEntry);

			$separator = true;
		}

		// mail
		if ($this->mail) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_mail');
			$ctrlmmGLEntry->setTitle($this->lng->txt('mail'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilMailGUI::class);
			$this->addGLEntry($ctrlmmGLEntry);

			$separator = true;
		}

		// contacts
		if ($this->contacts) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_contacts');
			$ctrlmmGLEntry->setTitle($this->lng->txt('mail_addressbook'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToContacts');
			$this->addGLEntry($ctrlmmGLEntry);

			$separator = true;
		}

		if ($separator) {
			$this->gl->addSeparator();
		}

		// profile
		$ctrlmmGLEntry = new ctrlmmGLEntry();
		$ctrlmmGLEntry->setId('mm_pd_profile');
		$ctrlmmGLEntry->setTitle($this->lng->txt('personal_profile'));
		$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToProfile');
		$this->addGLEntry($ctrlmmGLEntry);

		// settings
		$ctrlmmGLEntry = new ctrlmmGLEntry();
		$ctrlmmGLEntry->setId('mm_pd_sett');
		$ctrlmmGLEntry->setTitle($this->lng->txt('personal_settings'));
		$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToSettings');
		$this->addGLEntry($ctrlmmGLEntry);

		if ($this->entry->getShowLogout()) {
			$this->gl->addSeparator();
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_logout');
			$ctrlmmGLEntry->setTitle($this->lng->txt('logout'));
			$ctrlmmGLEntry->setLink('logout.php');
			$this->addGLEntry($ctrlmmGLEntry);
		}
	}



	//
	// FORM
	//

	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		parent::initForm($mode);
		$te = new ilCheckboxInputGUI($this->pl->txt(self::F_SHOW_LOGOUT), self::F_SHOW_LOGOUT);
		$this->form->addItem($te);

		$te = new ilCheckboxInputGUI($this->pl->txt(self::F_DISABLE_ACTIVE), self::F_DISABLE_ACTIVE);
		$this->form->addItem($te);
	}


	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values[self::F_SHOW_LOGOUT] = $this->entry->getShowLogout();
		$values[self::F_DISABLE_ACTIVE] = $this->entry->isDisableActive();
		$this->form->setValuesByArray($values);
	}


	public function createEntry() {
		$show_logout = (bool)$this->form->getInput(self::F_SHOW_LOGOUT);
		$disable_active = (bool)$this->form->getInput(self::F_DISABLE_ACTIVE);
		$this->entry->setShowLogout($show_logout);
		$this->entry->setDisableActive($disable_active);
		parent::createEntry();
	}
}

/**
 * Class ctrlmmGLEntry
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ctrlmmGLEntry {

	const CLASS_DISABLED = 'disabled ctrlmm_disabled';
	/**
	 * @var string
	 */
	protected $id = '';
	/**
	 * @var string
	 */
	protected $title = '';
	/**
	 * @var string
	 */
	protected $link = '';
	/**
	 * @var string
	 */
	protected $target = '_top';
	/**
	 * @var string
	 */
	protected $onclick = '';
	/**
	 * @var array
	 */
	protected $classes = array();


	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return string
	 */
	public function getLink() {
		return $this->link;
	}


	/**
	 * @param string $link
	 */
	public function setLink($link) {
		$this->link = $link;
	}


	/**
	 * @return string
	 */
	public function getTarget() {
		return $this->target;
	}


	/**
	 * @param string $target
	 */
	public function setTarget($target) {
		$this->target = $target;
	}


	/**
	 * @return string
	 */
	public function getOnclick() {
		return $this->onclick;
	}


	/**
	 * @param string $onclick
	 */
	public function setOnclick($onclick) {
		$this->onclick = $onclick;
	}


	/**
	 * @param bool $disable_active
	 *
	 * @return array
	 */
	public function getClasses($disable_active) {
		$this->checkClasses($disable_active);

		return $this->classes;
	}


	/**
	 * @param array $classes
	 */
	public function setClasses($classes) {
		$this->classes = $classes;
	}


	/**
	 * @param bool $disable_active
	 */
	protected function checkClasses($disable_active) {
		if ($disable_active) {
			$real_link = str_replace('&amp;', '&', $this->getLink());
			$same_link = ($real_link == ltrim($_SERVER['REQUEST_URI'], "/"));
			if ($same_link) {
				$this->classes[] = self::CLASS_DISABLED;
			}

			$active = '';
			switch ($_GET['cmdClass']) {
				case 'ilbookmarkadministrationgui':
					$active = 'bookm';
					break;
				case 'ilpdnotesgui':
					$active = 'notes';
					break;
				case 'ilpdnewsgui':
					$active = 'news';
					break;
				case 'ilobjectownershipmanagementgui':
				case 'ilobjworkspacerootfoldergui':
				case 'ilpersonalworkspacegui':
					$active = 'wsp';
					break;
				case 'ilobjportfoliogui':
					$active = 'port';
					break;
				case 'illearningprogressgui':
				case 'illplistofobjectsgui':
				case 'illplistofprogressgui':
					$active = 'lp';
					break;
				case 'ilcalendarpresentationgui':
				case 'ilcalendarinboxgui':
				case 'ilcalendardaygui':
				case 'ilcalendarweekgui':
				case 'ilcalendarmonthgui':
				case 'ilcalendarcategorygui':
				case 'ilcalendarusersettingsgui':
					$active = 'cal';
					break;
				case 'ilmailoptionsgui':
				case 'ilmailformgui':
				case 'ilmailfoldergui':
					$active = 'mail';
					break;
				case 'ilmailaddressbookgui':
					$active = 'contacts';
					break;
				case 'ilpersonalprofilegui':
					$active = 'profile';
					break;
				case 'ilpersonalsettingsgui':
					$active = 'sett';
					break;
				case 'ilusabilitypersonaldesktopgui':
					$active = 'crs_grp';
					break;
			}

			if ("mm_pd_{$active}" == $this->getId()) {
				$this->classes[] = self::CLASS_DISABLED;
			}
		}
	}
}