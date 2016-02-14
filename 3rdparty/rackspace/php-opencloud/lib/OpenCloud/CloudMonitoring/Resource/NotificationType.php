<?php
/**
 * PHP OpenCloud library.
 * 
 * @copyright 2014 Rackspace Hosting, Inc. See LICENSE for information.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 */

namespace OpenCloud\CloudMonitoring\Resource;

/**
 * NotificationType class.
 */
class NotificationType extends ReadOnlyResource
{
    private $id;
    private $address;
    private $fields;
    
    protected static $json_name = false;
    protected static $json_collection_name = 'values';
    protected static $url_resource = 'notification_types';
    
}