<?php

/**
 * This class provides functions for converting CSS styles into inline style attributes in your HTML code.
 *
 * For more information, please see the README.md file.
 *
 * @author Cameron Brooks
 * @author Jaime Prado
 */
class Emogrifier {
    /**
     * @var string
     */
    const ENCODING = 'UTF-8';

    /**
     * @var integer
     */
    const CACHE_KEY_CSS = 0;

    /**
     * @var integer
     */
    const CACHE_KEY_SELECTOR = 1;

    /**
     * @var integer
     */
    const CACHE_KEY_XPATH = 2;

    /**
     * for calculating nth-of-type and nth-child selectors
     *
     * @var integer
     */
    const INDEX = 0;

    /**
     * for calculating nth-of-type and nth-child selectors
     *
     * @var integer
     */
    const MULTIPLIER = 1;

    /**
     * @var string
     */
    const ID_ATTRIBUTE_MATCHER = '/(\\w+)?\\#([\\w\\-]+)/';

    /**
     * @var string
     */
    const CLASS_ATTRIBUTE_MATCHER = '/(\\w+|[\\*\\]])?((\\.[\\w\\-]+)+)/';

    /**
     * @var string
     */
    private $html = '';

    /**
     * @var string
     */
    private $css = '';

    /**
     * @var array<string>
     */
    private $unprocessableHtmlTags = array('wbr');

    /**
     * @var array<array>
     */
    private $caches = array(
        self::CACHE_KEY_CSS => array(),
        self::CACHE_KEY_SELECTOR => array(),
        self::CACHE_KEY_XPATH => array(),
    );

    /**
     * the visited nodes with the XPath paths as array keys
     *
     * @var array<\DOMNode>
     */
    private $visitedNodes = array();

    /**
     * the styles to apply to the nodes with the XPath paths as array keys for the outer array and the attribute names/values
     * as key/value pairs for the inner array
     *
     * @var array<array><string>
     */
    private $styleAttributesForNodes = array();

    /**
     * This attribute applies to the case where you want to preserve your original text encoding.
     *
     * By default, emogrifier translates your text into HTML entities for two reasons:
     *
     * 1. Because of client incompatibilities, it is better practice to send out HTML entities rather than unicode over email.
     *
     * 2. It translates any illegal XML characters that DOMDocument cannot work with.
     *
     * If you would like to preserve your original encoding, set this attribute to TRUE.
     *
     * @var boolean
     */
    public $preserveEncoding = FALSE;

    /**
     * The constructor.
     *
     * @param string $html the HTML to emogrify, must be UTF-8-encoded
     * @param string $css the CSS to merge, must be UTF-8-encoded
     */
    public function __construct($html = '', $css = '') {
        $this->setHtml($html);
        $this->setCss($css);
    }

    /**
     * The destructor.
     */
    public function __destruct() {
        $this->purgeVisitedNodes();
    }

    /**
     * Sets the HTML to emogrify.
     *
     * @param string $html the HTML to emogrify, must be UTF-8-encoded
     *
     * @return void
     */
    public function setHtml($html = '') {
        $this->html = $html;
    }

    /**
     * Sets the CSS to merge with the HTML.
     *
     * @param string $css the CSS to merge, must be UTF-8-encoded
     *
     * @return void
     */
    public function setCss($css = '') {
        $this->css = $css;
    }

    /**
     * Clears all caches.
     *
     * @return void
     */
    private function clearAllCaches() {
        $this->clearCache(self::CACHE_KEY_CSS);
        $this->clearCache(self::CACHE_KEY_SELECTOR);
        $this->clearCache(self::CACHE_KEY_XPATH);
    }

    /**
     * Clears a single cache by key.
     *
     * @param integer $key the cache key, must be CACHE_KEY_CSS, CACHE_KEY_SELECTOR or CACHE_KEY_XPATH
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    private function clearCache($key) {
        $allowedCacheKeys = array(self::CACHE_KEY_CSS, self::CACHE_KEY_SELECTOR, self::CACHE_KEY_XPATH);
        if (!in_array($key, $allowedCacheKeys, TRUE)) {
            throw new InvalidArgumentException('Invalid cache key: ' . $key, 1391822035);
        }

        $this->caches[$key] = array();
    }

    /**
     * Purges the visited nodes.
     *
     * @return void
     */
    private function purgeVisitedNodes() {
        $this->visitedNodes = array();
        $this->styleAttributesForNodes = array();
    }

    /**
     * Marks a tag for removal.
     *
     * There are some HTML tags that DOMDocument cannot process, and it will throw an error if it encounters them.
     * In particular, DOMDocument will complain if you try to use HTML5 tags in an XHTML document.
     *
     * Note: The tags will not be removed if they have any content.
     *
     * @param string $tagName the tag name, e.g., "p"
     *
     * @return void
     */
    public function addUnprocessableHtmlTag($tagName) {
        $this->unprocessableHtmlTags[] = $tagName;
    }

    /**
     * Drops a tag from the removal list.
     *
     * @param string $tagName the tag name, e.g., "p"
     *
     * @return void
     */
    public function removeUnprocessableHtmlTag($tagName) {
        $key = array_search($tagName, $this->unprocessableHtmlTags, TRUE);
        if ($key !== FALSE) {
            unset($this->unprocessableHtmlTags[$key]);
        }
    }

    /**
     * Applies the CSS you submit to the HTML you submit.
     *
     * This method places the CSS inline.
     *
     * @return string
     *
     * @throws \BadMethodCallException
     */
    public function emogrify() {
        if ($this->html === '') {
            throw new BadMethodCallException('Please set some HTML first before calling emogrify.', 1390393096);
        }

        $xmlDocument = $this->createXmlDocument();
        $xpath = new DOMXPath($xmlDocument);
        $this->clearAllCaches();

        // before be begin processing the CSS file, parse the document and normalize all existing CSS attributes (changes 'DISPLAY: none' to 'display: none');
        // we wouldn't have to do this if DOMXPath supported XPath 2.0.
        // also store a reference of nodes with existing inline styles so we don't overwrite them
        $this->purgeVisitedNodes();

        $nodesWithStyleAttributes = $xpath->query('//*[@style]');
        if ($nodesWithStyleAttributes !== FALSE) {
            $callback = function($m) { return strtolower($m[0]); };

            /** @var $nodeWithStyleAttribute \DOMNode */
            foreach ($nodesWithStyleAttributes as $node) {
                $normalizedOriginalStyle = preg_replace_callback(
                    '/[A-z\\-]+(?=\\:)/S',
                    $callback,
                    $node->getAttribute('style')
                );

                // in order to not overwrite existing style attributes in the HTML, we have to save the original HTML styles
                $nodePath = $node->getNodePath();
                if (!isset($this->styleAttributesForNodes[$nodePath])) {
                    $this->styleAttributesForNodes[$nodePath] = $this->parseCssDeclarationBlock($normalizedOriginalStyle);
                    $this->visitedNodes[$nodePath] = $node;
                }

                $node->setAttribute('style', $normalizedOriginalStyle);
            }
        }

        // grab any existing style blocks from the html and append them to the existing CSS
        // (these blocks should be appended so as to have precedence over conflicting styles in the existing CSS)
        $css = $this->css;
        $styleNodes = $xpath->query('//style');
        if ($styleNodes !== FALSE) {
            /** @var $styleNode \DOMNode */
            foreach ($styleNodes as $styleNode) {
                // append the css
                $css .= "\n\n" . $styleNode->nodeValue;
                // remove the <style> node
                $styleNode->parentNode->removeChild($styleNode);
            }
        }

        // filter the CSS
        $search = array(
            // get rid of css comment code
            '/\\/\\*.*\\*\\//sU',
            // strip out any import directives
            '/^\\s*@import\\s[^;]+;/misU',
            // strip any empty media enclosures
            '/^\\s*@media\\s[^{]+{\\s*}/misU',
            // strip out all media rules that are not 'screen' or 'all' (these don't apply to email)
            '/^\\s*@media\\s+((aural|braille|embossed|handheld|print|projection|speech|tty|tv)\\s*,*\\s*)+{.*}\\s*}/misU',
            // get rid of remaining media type rules
            '/^\\s*@media\\s[^{]+{(.*})\\s*}/misU',
        );

        $replace = array(
            '',
            '',
            '',
            '',
            '\\1',
        );

        $css = preg_replace($search, $replace, $css);

        $cssKey = md5($css);
        if (!isset($this->caches[self::CACHE_KEY_CSS][$cssKey])) {
            // process the CSS file for selectors and definitions
            preg_match_all('/(?:^|[^{}])\\s*([^{]+){([^}]*)}/mis', $css, $matches, PREG_SET_ORDER);

            $allSelectors = array();
            foreach ($matches as $key => $selectorString) {
                // if there is a blank definition, skip
                if (!strlen(trim($selectorString[2]))) {
                    continue;
                }

                // else split by commas and duplicate attributes so we can sort by selector precedence
                $selectors = explode(',', $selectorString[1]);
                foreach ($selectors as $selector) {
                    // don't process pseudo-elements and behavioral (dynamic) pseudo-classes; ONLY allow structural pseudo-classes
                    if (strpos($selector, ':') !== FALSE && !preg_match('/:\\S+\\-(child|type)\\(/i', $selector)) {
                        continue;
                    }

                    $allSelectors[] = array('selector' => trim($selector),
                                             'attributes' => trim($selectorString[2]),
                                             // keep track of where it appears in the file, since order is important
                                             'line' => $key,
                    );
                }
            }

            // now sort the selectors by precedence
            usort($allSelectors, array($this,'sortBySelectorPrecedence'));

            $this->caches[self::CACHE_KEY_CSS][$cssKey] = $allSelectors;
        }

        foreach ($this->caches[self::CACHE_KEY_CSS][$cssKey] as $value) {
            // query the body for the xpath selector
            $nodesMatchingCssSelectors = $xpath->query($this->translateCssToXpath(trim($value['selector'])));

            /** @var $node \DOMNode */
            foreach ($nodesMatchingCssSelectors as $node) {
                // if it has a style attribute, get it, process it, and append (overwrite) new stuff
                if ($node->hasAttribute('style')) {
                    // break it up into an associative array
                    $oldStyleDeclarations = $this->parseCssDeclarationBlock($node->getAttribute('style'));
                    $newStyleDeclarations = $this->parseCssDeclarationBlock($value['attributes']);

                    // new styles overwrite the old styles (not technically accurate, but close enough)
                    $combinedArray = array_merge($oldStyleDeclarations, $newStyleDeclarations);
                    $style = '';
                    foreach ($combinedArray as $attributeName => $attributeValue) {
                        $style .= (strtolower($attributeName) . ':' . $attributeValue . ';');
                    }
                } else {
                    // otherwise create a new style
                    $style = trim($value['attributes']);
                }
                $node->setAttribute('style', $style);
            }
        }

        // now iterate through the nodes that contained inline styles in the original HTML
        foreach ($this->styleAttributesForNodes as $nodePath => $styleAttributesForNode) {
            $node = $this->visitedNodes[$nodePath];
            $currentStyleAttributes = $this->parseCssDeclarationBlock($node->getAttribute('style'));

            $combinedArray = array_merge($currentStyleAttributes, $styleAttributesForNode);
            $style = '';
            foreach ($combinedArray as $attributeName => $attributeValue) {
                $style .= (strtolower($attributeName) . ':' . $attributeValue . ';');
            }

            $node->setAttribute('style', $style);
        }

        // This removes styles from your email that contain display:none.
        // We need to look for display:none, but we need to do a case-insensitive search. Since DOMDocument only supports XPath 1.0,
        // lower-case() isn't available to us. We've thus far only set attributes to lowercase, not attribute values. Consequently, we need
        // to translate() the letters that would be in 'NONE' ("NOE") to lowercase.
        $nodesWithStyleDisplayNone = $xpath->query('//*[contains(translate(translate(@style," ",""),"NOE","noe"),"display:none")]');
        // The checks on parentNode and is_callable below ensure that if we've deleted the parent node,
        // we don't try to call removeChild on a nonexistent child node
        if ($nodesWithStyleDisplayNone->length > 0) {
            /** @var $node \DOMNode */
            foreach ($nodesWithStyleDisplayNone as $node) {
                if ($node->parentNode && is_callable(array($node->parentNode,'removeChild'))) {
                    $node->parentNode->removeChild($node);
                }
            }
        }

        if ($this->preserveEncoding) {
            return mb_convert_encoding($xmlDocument->saveHTML(), self::ENCODING, 'HTML-ENTITIES');
        } else {
            return $xmlDocument->saveHTML();
        }
    }

    /**
     * Creates a DOMDocument instance with the current HTML.
     *
     * @return \DOMDocument
     */
    private function createXmlDocument() {
        $xmlDocument = new DOMDocument;
        $xmlDocument->encoding = self::ENCODING;
        $xmlDocument->strictErrorChecking = FALSE;
        $xmlDocument->formatOutput = TRUE;
        $libxmlState = libxml_use_internal_errors(TRUE);
        $xmlDocument->loadHTML($this->getUnifiedHtml());
        libxml_clear_errors();
        libxml_use_internal_errors($libxmlState);
        $xmlDocument->normalizeDocument();

        return $xmlDocument;
    }

    /**
     * Returns the HTML with the non-ASCII characters converts into HTML entities and the unprocessable HTML tags removed.
     *
     * @return string the unified HTML
     *
     * @throws \BadMethodCallException
     */
    private function getUnifiedHtml() {
        if (!empty($this->unprocessableHtmlTags)) {
            $unprocessableHtmlTags = implode('|', $this->unprocessableHtmlTags);
            $bodyWithoutUnprocessableTags = preg_replace('/<\\/?(' . $unprocessableHtmlTags . ')[^>]*>/i', '', $this->html);
        } else {
            $bodyWithoutUnprocessableTags = $this->html;
        }

        return mb_convert_encoding($bodyWithoutUnprocessableTags, 'HTML-ENTITIES', self::ENCODING);
    }

    /**
     * @param array $a
     * @param array $b
     *
     * @return integer
     */
    private function sortBySelectorPrecedence(array $a, array $b) {
        $precedenceA = $this->getCssSelectorPrecedence($a['selector']);
        $precedenceB = $this->getCssSelectorPrecedence($b['selector']);

        // We want these sorted in ascending order so selectors with lesser precedence get processed first and
        // selectors with greater precedence get sorted last.
        // The parenthesis around the -1 are necessary to avoid a PHP_CodeSniffer warning about missing spaces around
        // arithmetic operators.
        // @see http://forge.typo3.org/issues/55605
        $precedenceForEquals = ($a['line'] < $b['line'] ? (-1) : 1);
        $precedenceForNotEquals = ($precedenceA < $precedenceB ? (-1) : 1);
        return ($precedenceA === $precedenceB) ? $precedenceForEquals : $precedenceForNotEquals;
    }

    /**
     * @param string $selector
     *
     * @return integer
     */
    private function getCssSelectorPrecedence($selector) {
        $selectorKey = md5($selector);
        if (!isset($this->caches[self::CACHE_KEY_SELECTOR][$selectorKey])) {
            $precedence = 0;
            $value = 100;
            // ids: worth 100, classes: worth 10, elements: worth 1
            $search = array('\\#','\\.','');

            foreach ($search as $s) {
                if (trim($selector == '')) {
                    break;
                }
                $number = 0;
                $selector = preg_replace('/' . $s . '\\w+/', '', $selector, -1, $number);
                $precedence += ($value * $number);
                $value /= 10;
            }
            $this->caches[self::CACHE_KEY_SELECTOR][$selectorKey] = $precedence;
        }

        return $this->caches[self::CACHE_KEY_SELECTOR][$selectorKey];
    }

    /**
     * Right now, we support all CSS 1 selectors and most CSS2/3 selectors.
     *
     * @see http://plasmasturm.org/log/444/
     *
     * @param string $cssSelector
     *
     * @return string
     */
    private function translateCssToXpath($cssSelector) {
        $cssSelector = trim($cssSelector);
        $xpathKey = md5($cssSelector);
        if (!isset($this->caches[self::CACHE_KEY_XPATH][$xpathKey])) {
            // returns an Xpath selector
            $search = array(
                // Matches any element that is a child of parent.
                '/\\s+>\\s+/',
                // Matches any element that is an adjacent sibling.
                '/\\s+\\+\\s+/',
                // Matches any element that is a descendant of an parent element element.
                '/\\s+/',
                // first-child pseudo-selector
                '/([^\\/]+):first-child/i',
                // last-child pseudo-selector
                '/([^\\/]+):last-child/i',
                // Matches element with attribute
                '/(\\w)\\[(\\w+)\\]/',
                // Matches element with EXACT attribute
                '/(\\w)\\[(\\w+)\\=[\'"]?(\\w+)[\'"]?\\]/',
            );
            $replace = array(
                '/',
                '/following-sibling::*[1]/self::',
                '//',
                '*[1]/self::\\1',
                '*[last()]/self::\\1',
                '\\1[@\\2]',
                '\\1[@\\2="\\3"]',
            );

            $cssSelector = '//' . preg_replace($search, $replace, $cssSelector);

            $cssSelector = preg_replace_callback(self::ID_ATTRIBUTE_MATCHER, array($this, 'matchIdAttributes'), $cssSelector);
            $cssSelector = preg_replace_callback(self::CLASS_ATTRIBUTE_MATCHER, array($this, 'matchClassAttributes'), $cssSelector);

            // Advanced selectors are going to require a bit more advanced emogrification.
            // When we required PHP 5.3, we could do this with closures.
            $cssSelector = preg_replace_callback(
                '/([^\\/]+):nth-child\\(\s*(odd|even|[+\-]?\\d|[+\\-]?\\d?n(\\s*[+\\-]\\s*\\d)?)\\s*\\)/i',
                array($this, 'translateNthChild'), $cssSelector
            );
            $cssSelector = preg_replace_callback(
                '/([^\\/]+):nth-of-type\\(\s*(odd|even|[+\-]?\\d|[+\\-]?\\d?n(\\s*[+\\-]\\s*\\d)?)\\s*\\)/i',
                array($this, 'translateNthOfType'), $cssSelector
            );

            $this->caches[self::CACHE_KEY_SELECTOR][$xpathKey] = $cssSelector;
        }
        return $this->caches[self::CACHE_KEY_SELECTOR][$xpathKey];
    }

    /**
     * @param array $match
     *
     * @return string
     */
    private function matchIdAttributes(array $match) {
        return (strlen($match[1]) ? $match[1] : '*') . '[@id="' . $match[2] . '"]';
    }

    /**
     * @param array $match
     *
     * @return string
     */
    private function matchClassAttributes(array $match) {
        return (strlen($match[1]) ? $match[1] : '*') . '[contains(concat(" ",@class," "),concat(" ","' .
            implode(
                '"," "))][contains(concat(" ",@class," "),concat(" ","',
                explode('.', substr($match[2], 1))
            ) . '"," "))]';
    }

    /**
     * @param array $match
     *
     * @return string
     */
    private function translateNthChild(array $match) {
        $result = $this->parseNth($match);

        if (isset($result[self::MULTIPLIER])) {
            if ($result[self::MULTIPLIER] < 0) {
                $result[self::MULTIPLIER] = abs($result[self::MULTIPLIER]);
                return sprintf('*[(last() - position()) mod %u = %u]/self::%s', $result[self::MULTIPLIER], $result[self::INDEX], $match[1]);
            } else {
                return sprintf('*[position() mod %u = %u]/self::%s', $result[self::MULTIPLIER], $result[self::INDEX], $match[1]);
            }
        } else {
            return sprintf('*[%u]/self::%s', $result[self::INDEX], $match[1]);
        }
    }

    /**
     * @param array $match
     *
     * @return string
     */
    private function translateNthOfType(array $match) {
        $result = $this->parseNth($match);

        if (isset($result[self::MULTIPLIER])) {
            if ($result[self::MULTIPLIER] < 0) {
                $result[self::MULTIPLIER] = abs($result[self::MULTIPLIER]);
                return sprintf('%s[(last() - position()) mod %u = %u]', $match[1], $result[self::MULTIPLIER], $result[self::INDEX]);
            } else {
                return sprintf('%s[position() mod %u = %u]', $match[1], $result[self::MULTIPLIER], $result[self::INDEX]);
            }
        } else {
            return sprintf('%s[%u]', $match[1], $result[self::INDEX]);
        }
    }

    /**
     * @param array $match
     *
     * @return array
     */
    private function parseNth(array $match) {
        if (in_array(strtolower($match[2]), array('even','odd'))) {
            $index = strtolower($match[2]) == 'even' ? 0 : 1;
            return array(self::MULTIPLIER => 2, self::INDEX => $index);
        } elseif (stripos($match[2], 'n') === FALSE) {
            // if there is a multiplier
            $index = intval(str_replace(' ', '', $match[2]));
            return array(self::INDEX => $index);
        } else {
            if (isset($match[3])) {
                $multipleTerm = str_replace($match[3], '', $match[2]);
                $index = intval(str_replace(' ', '', $match[3]));
            } else {
                $multipleTerm = $match[2];
                $index = 0;
            }

            $multiplier = str_ireplace('n', '', $multipleTerm);

            if (!strlen($multiplier)) {
                $multiplier = 1;
            } elseif ($multiplier == 0) {
                return array(self::INDEX => $index);
            } else {
                $multiplier = intval($multiplier);
            }

            while ($index < 0) {
                $index += abs($multiplier);
            }

            return array(self::MULTIPLIER => $multiplier, self::INDEX => $index);
        }
    }

    /**
     * Parses a CSS declaration block into property name/value pairs.
     *
     * Example:
     *
     * The declaration block
     *
     *   "color: #000; font-weight: bold;"
     *
     * will be parsed into the following array:
     *
     *   "color" => "#000"
     *   "font-weight" => "bold"
     *
     * @param string $cssDeclarationBlock the CSS declaration block without the curly braces, may be empty
     *
     * @return array the CSS declarations with the property names as array keys and the property values as array values
     */
    private function parseCssDeclarationBlock($cssDeclarationBlock) {
        $properties = array();

        $declarations = explode(';', $cssDeclarationBlock);
        foreach ($declarations as $declaration) {
            $matches = array();
            if (!preg_match('/ *([a-z\-]+) *: *([^;]+) */', $declaration, $matches)) {
                continue;
            }
            $propertyName = $matches[1];
            $propertyValue = $matches[2];
            $properties[$propertyName] = $propertyValue;
        }

        return $properties;
    }
}
