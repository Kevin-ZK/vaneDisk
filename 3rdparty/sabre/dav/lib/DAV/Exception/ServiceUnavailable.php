<?php

namespace Sabre\DAV\Exception;

use Sabre\DAV;

/**
 * ServiceUnavailable
 *
 * This exception is thrown in case the service
 * is currently not available (e.g. down for maintenance).
 *
 * @copyright Copyright (C) 2007-2015 fruux GmbH (https://fruux.com/).
 * @license http://sabre.io/license/ Modified BSD License
 */
class ServiceUnavailable extends DAV\Exception {

    /**
     * Returns the HTTP statuscode for this exception
     *
     * @return int
     */
    function getHTTPCode() {

        return 503;

    }

}
