<?php
namespace OCP;

/**
 * Interface IL10N
 *
 * @package OCP
 * @since 6.0.0
 */
interface IL10N {
	/**
	 * Translating
	 * @param string $text The text we need a translation for
	 * @param array $parameters default:array() Parameters for sprintf
	 * @return \OC_L10N_String Translation or the same text
	 *
	 * Returns the translation. If no translation is found, $text will be
	 * returned.
	 * @since 6.0.0
	 */
	public function t($text, $parameters = array());

	/**
	 * Translating
	 * @param string $text_singular the string to translate for exactly one object
	 * @param string $text_plural the string to translate for n objects
	 * @param integer $count Number of objects
	 * @param array $parameters default:array() Parameters for sprintf
	 * @return \OC_L10N_String Translation or the same text
	 *
	 * Returns the translation. If no translation is found, $text will be
	 * returned. %n will be replaced with the number of objects.
	 *
	 * The correct plural is determined by the plural_forms-function
	 * provided by the po file.
	 * @since 6.0.0
	 *
	 */
	public function n($text_singular, $text_plural, $count, $parameters = array());

	/**
	 * Localization
	 * @param string $type Type of localization
	 * @param array $data parameters for this localization
	 * @param array $options currently supports following options:
	 * 			- 'width': handed into \Punic\Calendar::formatDate as second parameter
	 * @return string|false
	 *
	 * Returns the localized data.
	 *
	 * Implemented types:
	 *  - date
	 *    - Creates a date
	 *    - l10n-field: date
	 *    - params: timestamp (int/string)
	 *  - datetime
	 *    - Creates date and time
	 *    - l10n-field: datetime
	 *    - params: timestamp (int/string)
	 *  - time
	 *    - Creates a time
	 *    - l10n-field: time
	 *    - params: timestamp (int/string)
	 * @since 6.0.0 - parameter $options was added in 8.0.0
	 */
	public function l($type, $data, $options = array());


	/**
	 * The code (en, de, ...) of the language that is used for this OC_L10N object
	 *
	 * @return string language
	 * @since 7.0.0
	 */
	public function getLanguageCode();
}
