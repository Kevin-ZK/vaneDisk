<?php
/**
 * PHP OpenCloud library.
 * 
 * @copyright 2014 Rackspace Hosting, Inc. See LICENSE for information.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 */

namespace OpenCloud\Autoscale\Resource;

/**
 * Description of Webhook
 * 
 * @link 
 */
class Webhook extends AbstractResource
{
    
    public $id;
    public $name;
    public $metadata;
    public $links;
    
    protected static $json_name = 'webhook';
    protected static $url_resource = 'webhooks';
    
    public $createKeys = array(
        'name',
        'metadata'
    );
    
    public function createJson()
    {
        $object = new \stdClass;
        $object->name = $this->name;
        $object->metadata = $this->metadata;
       
        return $object;
    }
    
}