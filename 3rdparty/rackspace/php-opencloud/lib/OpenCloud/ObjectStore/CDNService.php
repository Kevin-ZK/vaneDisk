<?php
/**
 * PHP OpenCloud library.
 * 
 * @copyright 2014 Rackspace Hosting, Inc. See LICENSE for information.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 */

namespace OpenCloud\ObjectStore;

use OpenCloud\OpenStack;

/**
 * This is the CDN version of the ObjectStore service. 
 */
class CDNService extends AbstractService
{
    const DEFAULT_NAME = 'cloudFilesCDN';
    const DEFAULT_TYPE = 'rax:object-cdn';
}
