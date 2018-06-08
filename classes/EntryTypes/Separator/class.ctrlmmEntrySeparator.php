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
 * ctrlmmEntrySeparator
 *
 * @author         Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version        2.0.02
 */
class ctrlmmEntrySeparator extends ctrlmmEntry {

	/**
	 * @var bool
	 */
	protected $restricted = false;


	/**
	 * @return bool
	 */
	public function isActive() {
		return false;
	}


	public function __construct($primary_key = 0) {
		$this->setTypeId(ctrlmmMenu::TYPE_SEPARATOR);
		parent::__construct($primary_key);
	}


	/**
	 * @var int
	 */
	//protected $type = ctrlmmMenu::TYPE_SEPARATOR;
}