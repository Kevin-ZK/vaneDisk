<?php

/**
 * SabreDAV base exception
 *
 * This is SabreDAV's base exception file, use this to implement your own exception.
 *
 * @copyright Copyright (C) 2007-2015 fruux GmbH (https://fruux.com/).
 * @license http://sabre.io/license/ Modified BSD License
 */

namespace Sabre\DAV;

/**
 * Main Exception class.
 *
 * This class defines a getHTTPCode method, which should return the appropriate HTTP code for the Exception occurred.
 * The default for this is 500.
 *
 * This class also allows you to generate custom xml data for your exceptions. This will be displayed
 * in the 'error' element in the failing response.
 */
class Exception extends \Exception {

    /**
     * Returns the HTTP statuscode for this exception
     *
     * @return int
     */
    function getHTTPCode() {

        return 500;

    }

    /**
     * This method allows the exception to include additional information into the WebDAV error response
     *
     * @param Server $server
     * @param \DOMElement $errorNode
     * @return void
     */
    function serialize(Server $server,\DOMElement $errorNode) {


    }

    /**
     * This method allows the exception to return any extra HTTP response headers.
     *
     * The headers must be returned as an array.
     *
     * @param Server $server
     * @return array
     */
    function getHTTPHeaders(Server $server) {

        return [];

    }

}
