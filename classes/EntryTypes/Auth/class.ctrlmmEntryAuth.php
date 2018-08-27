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

/**
 * ctrlmmEntryAuth
 *
 * @author         Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version        2.0.02
 */
class ctrlmmEntryAuth extends ctrlmmEntry {

	/**
	 * @var bool
	 */
	protected $restricted = true;
	/**
	 * @var int
	 */
	//protected $type = ctrlmmMenu::TYPE_AUTH;

	/**
	 * @var bool
	 */
	protected $show_name = true;
	/**
	 * @var bool
	 */
	protected $show_login_text = true;
	/**
	 * @var ilObjUser
	 */
	protected $ilUser;
	protected $lng;


	/**
	 * @param int $id
	 */
	public function __construct($id = 0) {
		global $DIC;
		$this->ilUser = $DIC->user();
		$this->lng = $DIC->language();

		$this->setTypeId(ctrlmmMenu::TYPE_AUTH);

		parent::__construct($id);
	}


	/**
	 * @return bool
	 */
	public function isActive() {
		return false;
	}


	/**
	 * @return bool
	 */
	public function isLoggedIn() {
		return ($this->ilUser->getId() != 13 AND $this->ilUser->getId());
	}


	/**
	 * @return string
	 */
	public function getUsername() {
		return $this->ilUser->getFirstname() . ' ' . $this->ilUser->getLastname();
	}


	/**
	 * @return string
	 */
	public function getLink() {
		if ($this->isLoggedIn()) {
			return 'logout.php?lang=' . $this->ilUser->getLanguage();
		} else {
			$target_str = '';
			$language = $this->ilUser->getLanguage();

			return 'login.php?target=' . $target_str . '&client_id=' . rawurlencode(CLIENT_ID) . '&cmd=force_login&lang=' . $language;
		}
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		if ($this->isLoggedIn()) {
			return $this->lng->txt('logout');
		} else {
			return $this->lng->txt('log_in');
		}
	}


	/**
	 * @param boolean $show_name
	 */
	public function setShowName($show_name) {
		$this->show_name = $show_name;
	}


	/**
	 * @return boolean
	 */
	public function getShowName() {
		return $this->show_name;
	}


	/**
	 * @param boolean $show_login_text
	 */
	public function setShowLoginText($show_login_text) {
		$this->show_login_text = $show_login_text;
	}


	/**
	 * @return boolean
	 */
	public function getShowLoginText() {
		return $this->show_login_text;
	}
}