<?php

namespace OC;

/**
 * Class ArrayParser
 *
 * @package OC
 */
class ArrayParser {
	const TYPE_NUM = 1;
	const TYPE_BOOL = 2;
	const TYPE_STRING = 3;
	const TYPE_ARRAY = 4;

	/**
	 * @param string $string
	 * @return array|bool|int|null|string
	 */
	public function parsePHP($string) {
		$string = $this->stripPHPTags($string);
		$string = $this->stripAssignAndReturn($string);
		return $this->parse($string);
	}

	/**
	 * @param string $string
	 * @return string
	 */
	private function stripPHPTags($string) {
		$string = trim($string);
		if (substr($string, 0, 5) === '<?php') {
			$string = substr($string, 5);
		}
		if (substr($string, -2) === '?>') {
			$string = substr($string, 0, -2);
		}
		return $string;
	}

	/**
	 * @param string $string
	 * @return string
	 */
	private function stripAssignAndReturn($string) {
		$string = trim($string);
		if (substr($string, 0, 6) === 'return') {
			$string = substr($string, 6);
		}
		if (substr($string, 0, 1) === '$') {
			list(, $string) = explode('=', $string, 2);
		}
		return $string;
	}

	/**
	 * @param string $string
	 * @return array|bool|int|null|string
	 */
	private function parse($string) {
		$string = trim($string);
		$string = trim($string, ';');
		switch ($this->getType($string)) {
			case self::TYPE_NUM:
				return $this->parseNum($string);
			case self::TYPE_BOOL:
				return $this->parseBool($string);
			case self::TYPE_STRING:
				return $this->parseString($string);
			case self::TYPE_ARRAY:
				return $this->parseArray($string);
		}
		return null;
	}

	/**
	 * @param string $string
	 * @return int
	 */
	private function getType($string) {
		$string = strtolower($string);
		$first = substr($string, 0, 1);
		$last = substr($string, -1, 1);
		$arrayFirst = substr($string, 0, 5);
		if (($first === '"' or $first === "'") and ($last === '"' or $last === "'")) {
			return self::TYPE_STRING;
		} elseif ($string === 'false' or $string === 'true') {
			return self::TYPE_BOOL;
		} elseif ($arrayFirst === 'array' and $last === ')') {
			return self::TYPE_ARRAY;
		} else {
			return self::TYPE_NUM;
		}
	}

	/**
	 * @param string $string
	 * @return string
	 */
	private function parseString($string) {
		return substr($string, 1, -1);
	}

	/**
	 * @param string $string
	 * @return int
	 */
	private function parseNum($string) {
		return intval($string);
	}

	/**
	 * @param string $string
	 * @return bool
	 */
	private function parseBool($string) {
		$string = strtolower($string);
		return $string === 'true';
	}

	/**
	 * @param string $string
	 * @return array
	 */
	private function parseArray($string) {
		$body = substr($string, 5);
		$body = trim($body);
		$body = substr($body, 1, -1);
		$items = $this->splitArray($body);
		$result = [];
		$lastKey = -1;
		foreach ($items as $item) {
			$item = trim($item);
			if ($item) {
				if (strpos($item, '=>')) {
					list($key, $value) = explode('=>', $item, 2);
					$key = $this->parse($key);
					$value = $this->parse($value);
				} else {
					$key = ++$lastKey;
					$value = $item;
				}

				if (is_numeric($key)) {
					$lastKey = $key;
				}
				$result[$key] = $value;
			}
		}
		return $result;
	}

	/**
	 * @param string $body
	 * @return array
	 * @throws \UnexpectedValueException
	 */
	private function splitArray($body) {
		$inSingleQuote = false; //keep track if we are inside quotes
		$inDoubleQuote = false;
		$bracketDepth = 0; //keep track if we are inside brackets
		$parts = [];
		$start = 0;
		$escaped = false; //keep track if we are after an escape character
		$skips = []; //keep track of the escape characters we need to remove from the result
		if (substr($body, -1, 1) !== ',') {
			$body .= ',';
		}

		$bodyLength = strlen($body);
		for ($i = 0; $i < $bodyLength; $i++) {
			$char = substr($body, $i, 1);
			if ($char === '\\') {
				if ($escaped) {
					array_unshift($skips, $i - 1);
				}
				$escaped = !$escaped;
			} else {
				if ($char === '"' and !$inSingleQuote) {
					if ($escaped) {
						array_unshift($skips, $i - 1);
					} else {
						$inDoubleQuote = !$inDoubleQuote;
					}
				} elseif ($char === "'" and !$inDoubleQuote) {
					if ($escaped) {
						array_unshift($skips, $i - 1);
					} else {
						$inSingleQuote = !$inSingleQuote;
					}
				} elseif (!$inDoubleQuote and !$inSingleQuote) {
					if ($char === '(') {
						$bracketDepth++;
					} elseif ($char === ')') {
						if ($bracketDepth <= 0) {
							throw new \UnexpectedValueException();
						} else {
							$bracketDepth--;
						}
					} elseif ($bracketDepth === 0 and $char === ',') {
						$part = substr($body, $start, $i - $start);
						foreach ($skips as $skip) {
							$part = substr($part, 0, $skip - $start) . substr($part, $skip - $start + 1);
						}
						$parts[] = $part;
						$start = $i + 1;
						$skips = [];
					}
				}
				$escaped = false;
			}
		}
		return $parts;
	}
}
