<?php

namespace OCA\Files_Sharing\Exceptions;

/**
 * Expected path with a different root
 * Possible Error Codes:
 * 10 - Path not relative to data/ and point to the users file directory

 */
class BrokenPath extends \Exception {
}
