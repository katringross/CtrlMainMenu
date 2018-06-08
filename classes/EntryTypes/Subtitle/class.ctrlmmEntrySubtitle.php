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
 * ctrlmmEntrySubtitle
 *
 * @author         Martin Studer <ms@studer-raimann.ch>
 *
 * @version        1.0.0
 */
class ctrlmmEntrySubtitle extends ctrlmmEntry {

	/**
	 * @var bool
	 */
	protected $restricted = true;
	/**
	 * @var bool
	 */
	protected $show_with_no_children = false;


	/**
	 * @return bool
	 */
	public function isActive() {
		return false;
	}


	public function __construct($primary_key = 0) {
		$this->setTypeId(ctrlmmMenu::TYPE_SUBTITLE);

		parent::__construct($primary_key);
	}


	/**
	 * @return boolean
	 */
	public function getShowWithNoChildren() {
		return $this->show_with_no_children;
	}


	/**
	 * @param boolean $show_with_no_children
	 */
	public function setShowWithNoChildren($show_with_no_children) {
		$this->show_with_no_children = $show_with_no_children;
	}
}