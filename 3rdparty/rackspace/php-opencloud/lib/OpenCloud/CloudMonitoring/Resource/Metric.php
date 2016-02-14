<?php
/**
 * PHP OpenCloud library.
 * 
 * @copyright 2014 Rackspace Hosting, Inc. See LICENSE for information.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 */

namespace OpenCloud\CloudMonitoring\Resource;

/**
 * Metric class.
 */
class Metric extends ReadOnlyResource
{
    protected static $json_name = 'metrics';
    protected static $json_collection_name = 'values';
    protected static $url_resource = 'metrics';
}