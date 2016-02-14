<?php
/**
 * PHP OpenCloud library.
 * 
 * @copyright 2014 Rackspace Hosting, Inc. See LICENSE for information.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 */

namespace OpenCloud\CloudMonitoring\Resource;

/**
 * ReadonlyResource class.
 * 
 * @extends AbstractResource
 */
class ReadonlyResource extends AbstractResource
{
    
    public function create($params = array()) 
    { 
        return $this->noCreate(); 
    }

    public function update($params = array()) 
    { 
        return $this->noUpdate(); 
    }

    public function delete($params = array()) 
    { 
        return $this->noDelete(); 
    }

}