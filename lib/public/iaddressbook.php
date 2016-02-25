<?php
namespace OCP {
	/**
	 * Interface IAddressBook
	 *
	 * @package OCP
	 * @since 5.0.0
	 */
	interface IAddressBook {

		/**
		 * @return string defining the technical unique key
		 * @since 5.0.0
		 */
		public function getKey();

		/**
		 * In comparison to getKey() this function returns a human readable (maybe translated) name
		 * @return mixed
		 * @since 5.0.0
		 */
		public function getDisplayName();

		/**
		 * @param string $pattern which should match within the $searchProperties
		 * @param array $searchProperties defines the properties within the query pattern should match
		 * @param array $options - for future use. One should always have options!
		 * @return array an array of contacts which are arrays of key-value-pairs
		 * @since 5.0.0
		 */
		public function search($pattern, $searchProperties, $options);
		//	// dummy results
		//	return array(
		//		array('id' => 0, 'FN' => 'Thomas Müller', 'EMAIL' => 'a@b.c', 'GEO' => '37.386013;-122.082932'),
		//		array('id' => 5, 'FN' => 'Thomas Tanghus', 'EMAIL' => array('d@e.f', 'g@h.i')),
		//	);

		/**
		 * @param array $properties this array if key-value-pairs defines a contact
		 * @return array an array representing the contact just created or updated
		 * @since 5.0.0
		 */
		public function createOrUpdate($properties);
		//	// dummy
		//	return array('id'    => 0, 'FN' => 'Thomas Müller', 'EMAIL' => 'a@b.c',
		//		     'PHOTO' => 'VALUE=uri:http://www.abc.com/pub/photos/jqpublic.gif',
		//		     'ADR'   => ';;123 Main Street;Any Town;CA;91921-1234'
		//	);

		/**
		 * @return mixed
		 * @since 5.0.0
		 */
		public function getPermissions();

		/**
		 * @param object $id the unique identifier to a contact
		 * @return bool successful or not
		 * @since 5.0.0
		 */
		public function delete($id);
	}
}
