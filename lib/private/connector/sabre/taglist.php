<?php

namespace OC\Connector\Sabre;

use Sabre\DAV;

/**
 * TagList property
 *
 * This property contains multiple "tag" elements, each containing a tag name.
 */
class TagList extends DAV\Property {
	const NS_OWNCLOUD = 'http://owncloud.org/ns';

    /**
     * tags
     *
     * @var array
     */
    private $tags;

    /**
     * @param array $tags
     */
    public function __construct(array $tags) {
        $this->tags = $tags;
    }

    /**
     * Returns the tags
     *
     * @return array
     */
    public function getTags() {

        return $this->tags;

    }

    /**
     * Serializes this property.
     *
     * @param DAV\Server $server
     * @param \DOMElement $dom
     * @return void
     */
    public function serialize(DAV\Server $server,\DOMElement $dom) {

        $prefix = $server->xmlNamespaces[self::NS_OWNCLOUD];

        foreach($this->tags as $tag) {

            $elem = $dom->ownerDocument->createElement($prefix . ':tag');
            $elem->appendChild($dom->ownerDocument->createTextNode($tag));

            $dom->appendChild($elem);
        }

    }

    /**
     * Unserializes this property from a DOM Element
     *
     * This method returns an instance of this class.
     * It will only decode tag values.
     *
     * @param \DOMElement $dom
	 * @param array $propertyMap
     * @return \OC\Connector\Sabre\TagList
     */
    static function unserialize(\DOMElement $dom, array $propertyMap) {

        $tags = array();
        foreach($dom->childNodes as $child) {
            if (DAV\XMLUtil::toClarkNotation($child)==='{' . self::NS_OWNCLOUD . '}tag') {
                $tags[] = $child->textContent;
            }
        }
        return new self($tags);

    }

}
