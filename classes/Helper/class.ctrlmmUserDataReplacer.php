<?php

/**
 * Class ctrlmmUserDataReplacer
 *
 * @author Michael Herren <mh@studer-raimann.ch>
 */
class ctrlmmUserDataReplacer {

	const OPEN_TAG = "[";
	const CLOSE_TAG = "]";
	/**
	 * @var array
	 */
	protected static $values = array();


	/**
	 * @param $input_string string
	 *
	 * @return string
	 */
	public static function parse($input_string) {
		self::loadData();

		foreach (self::$values as $key => $value) {
			$input_string = str_replace(self::OPEN_TAG . $key . self::CLOSE_TAG, $value, $input_string);
		}

		return $input_string;
	}


	protected static function loadData() {
		global $DIC;
		$ilUser = $DIC->user();
		if (!self::$values) {
			self::$values['user_id'] = $ilUser->getId();
			self::$values['user_name'] = $ilUser->getLogin();
			//self::$values['user_session_id'] = session_id();
			self::$values['user_matriculation'] = $ilUser->getMatriculation();

			self::$values['user_email'] = $ilUser->getEmail();
			self::$values['user_language'] = $ilUser->getCurrentLanguage();
			self::$values['user_country'] = $ilUser->getCountry();
			self::$values['user_department'] = $ilUser->getDepartment();
			self::$values['user_firstname'] = $ilUser->getFirstname();
			self::$values['user_lastname'] = $ilUser->getLastname();

			foreach (self::$values as $key => $value) {
				self::$values[$key] = urlencode($value);
			}

			$user_field_definitions = ilUserDefinedFields::_getInstance();
			$fds = $user_field_definitions->getDefinitions();

			$user_fields = $ilUser->getUserDefinedData();

			foreach ($fds as $k => $f) {
				// prefixes needed for ilias!
				self::$values["f_" . self::escapeGetParameterKeys($f['field_name'])] = urlencode($user_fields['f_' . $f['field_id']]);
			}
		}
	}


	/**
	 * @param $value string
	 *
	 * @return string
	 */
	public static function escapeGetParameterKeys($value) {
		return str_replace(array( '=', '&', '@' ), array( '', '', '' ), $value);
	}


	/**
	 * @return array
	 */
	public static function getDropdownData() {
		self::loadData();

		$out = array();
		foreach (self::$values as $key => $value) {
			$out[self::OPEN_TAG . $key . self::CLOSE_TAG] = $key;
		}

		return $out;
	}
}
