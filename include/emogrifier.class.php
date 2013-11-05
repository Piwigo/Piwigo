<?php
/*
UPDATES

    2008-08-10  Fixed CSS comment stripping regex to add PCRE_DOTALL (changed from '/\/\*.*\*\//U' to '/\/\*.*\*\//sU')
    2008-08-18  Added lines instructing DOMDocument to attempt to normalize HTML before processing
    2008-10-20  Fixed bug with bad variable name... Thanks Thomas!
    2008-03-02  Added licensing terms under the MIT License
                Only remove unprocessable HTML tags if they exist in the array
    2009-06-03  Normalize existing CSS (style) attributes in the HTML before we process the CSS.
                Made it so that the display:none stripper doesn't require a trailing semi-colon.
    2009-08-13  Added support for subset class values (e.g. "p.class1.class2").
                Added better protection for bad css attributes.
                Fixed support for HTML entities.
    2009-08-17  Fixed CSS selector processing so that selectors are processed by precedence/specificity, and not just in order.
    2009-10-29  Fixed so that selectors appearing later in the CSS will have precedence over identical selectors appearing earlier.
    2009-11-04  Explicitly declared static functions static to get rid of E_STRICT notices.
    2010-05-18  Fixed bug where full url filenames with protocols wouldn't get split improperly when we explode on ':'... Thanks Mark!
                Added two new attribute selectors
    2010-06-16  Added static caching for less processing overhead in situations where multiple emogrification takes place
    2010-07-26  Fixed bug where '0' values were getting discarded because of php's empty() function... Thanks Scott!
    2010-09-03  Added checks to invisible node removal to ensure that we don't try to remove non-existent child nodes of parents that have already been deleted
    2011-04-08  Fixed errors in CSS->XPath conversion for adjacent sibling selectors and id/class combinations... Thanks Bob V.!
    2011-06-08  Fixed an error where CSS @media types weren't being parsed correctly... Thanks Will W.!
    2011-08-03  Fixed an error where an empty selector at the beginning of the CSS would cause a parse error on the next selector... Thanks Alexei T.!
    2011-10-13  Fully fixed a bug introduced in 2011-06-08 where selectors at the beginning of the CSS would be parsed incorrectly... Thanks Thomas A.!
    2011-10-26  Added an option to allow you to output emogrified code without extended characters being turned into HTML entities.
                Moved static references to class attributes so they can be manipulated.
                Added the ability to clear out the (formerly) static cache when CSS is reloaded.
    2011-12-22  Fixed a bug that was overwriting existing inline styles from the original HTML... Thanks Sagi L.!
    2012-01-31  Fixed a bug that was introduced with the 2011-12-22 revision... Thanks Sagi L. and M. Bąkowski!
                Added extraction of <style> blocks within the HTML due to popular demand.
                Added several new pseudo-selectors (first-child, last-child, nth-child, and nth-of-type).
    2012-02-07  Fixed some recent code introductions to use class constants rather than global constants.
                Fixed some recent code introductions to make it cleaner to read.
    2012-05-01  Made removal of invisible nodes operate in a case-insensitive manner... Thanks Juha P.!
    2013-10-10  Add preserveStyleTag option
*/

define('CACHE_CSS', 0);
define('CACHE_SELECTOR', 1);
define('CACHE_XPATH', 2);

class Emogrifier {

    // for calculating nth-of-type and nth-child selectors
    const INDEX = 0;
    const MULTIPLIER = 1;

    private $html = '';
    private $css = '';
    private $unprocessableHTMLTags = array('wbr');
    private $caches = array();

    // this attribute applies to the case where you want to preserve your original text encoding.
    // by default, emogrifier translates your text into HTML entities for two reasons:
    // 1. because of client incompatibilities, it is better practice to send out HTML entities rather than unicode over email
    // 2. it translates any illegal XML characters that DOMDocument cannot work with
    // if you would like to preserve your original encoding, set this attribute to true.
    public $preserveEncoding = false;
    
    // by default, emogrifier removes <style> tags, set preserveStyleTag to true to keep them
    public $preserveStyleTag = false;

    public function __construct($html = '', $css = '') {
        $this->html = $html;
        $this->css  = $css;
        $this->clearCache();
    }

    public function setHTML($html = '') { $this->html = $html; }
    public function setCSS($css = '') {
        $this->css = $css;
        $this->clearCache(CACHE_CSS);
    }

    public function clearCache($key = null) {
        if (!is_null($key)) {
            if (isset($this->caches[$key])) $this->caches[$key] = array();
        } else {
            $this->caches = array(
                CACHE_CSS       => array(),
                CACHE_SELECTOR  => array(),
                CACHE_XPATH     => array(),
            );
        }
    }

    // there are some HTML tags that DOMDocument cannot process, and will throw an error if it encounters them.
    // in particular, DOMDocument will complain if you try to use HTML5 tags in an XHTML document.
    // these functions allow you to add/remove them if necessary.
    // it only strips them from the code (does not remove actual nodes).
    public function addUnprocessableHTMLTag($tag) { $this->unprocessableHTMLTags[] = $tag; }
    public function removeUnprocessableHTMLTag($tag) {
        if (($key = array_search($tag,$this->unprocessableHTMLTags)) !== false)
            unset($this->unprocessableHTMLTags[$key]);
    }

    // applies the CSS you submit to the html you submit. places the css inline
    public function emogrify() {
        $body = $this->html;

        // remove any unprocessable HTML tags (tags that DOMDocument cannot parse; this includes wbr and many new HTML5 tags)
        if (count($this->unprocessableHTMLTags)) {
            $unprocessableHTMLTags = implode('|',$this->unprocessableHTMLTags);
            $body = preg_replace("/<\/?($unprocessableHTMLTags)[^>]*>/i",'',$body);
        }

        $encoding = mb_detect_encoding($body);
        $body = mb_convert_encoding($body, 'HTML-ENTITIES', $encoding);

        $xmldoc = new DOMDocument;
        $xmldoc->encoding = $encoding;
        $xmldoc->strictErrorChecking = false;
        $xmldoc->formatOutput = true;
        $xmldoc->loadHTML($body);
        $xmldoc->normalizeDocument();

        $xpath = new DOMXPath($xmldoc);

        // before be begin processing the CSS file, parse the document and normalize all existing CSS attributes (changes 'DISPLAY: none' to 'display: none');
        // we wouldn't have to do this if DOMXPath supported XPath 2.0.
        // also store a reference of nodes with existing inline styles so we don't overwrite them
        $vistedNodes = $vistedNodeRef = array();
        $nodes = @$xpath->query('//*[@style]');
        foreach ($nodes as $node) {
            $normalizedOrigStyle = preg_replace('/[A-z\-]+(?=\:)/Se',"strtolower('\\0')", $node->getAttribute('style'));

            // in order to not overwrite existing style attributes in the HTML, we have to save the original HTML styles
            $nodeKey = md5($node->getNodePath());
            if (!isset($vistedNodeRef[$nodeKey])) {
                $vistedNodeRef[$nodeKey] = $this->cssStyleDefinitionToArray($normalizedOrigStyle);
                $vistedNodes[$nodeKey]   = $node;
            }

            $node->setAttribute('style', $normalizedOrigStyle);
        }

        // grab any existing style blocks from the html and append them to the existing CSS
        // (these blocks should be appended so as to have precedence over conflicting styles in the existing CSS)
        $css = $this->css;
        $nodes = @$xpath->query('//style');
        foreach ($nodes as $node) {
            // append the css
            $css .= "\n\n{$node->nodeValue}";
            // remove the <style> node
            if (!$this->preserveStyleTag) {
                $node->parentNode->removeChild($node);
            }
        }

        // filter the CSS
        $search = array(
            '/\/\*.*\*\//sU', // get rid of css comment code
            '/^\s*@import\s[^;]+;/misU', // strip out any import directives
            '/^\s*@media\s[^{]+{\s*}/misU', // strip any empty media enclosures
            '/^\s*@media\s+((aural|braille|embossed|handheld|print|projection|speech|tty|tv)\s*,*\s*)+{.*}\s*}/misU', // strip out all media types that are not 'screen' or 'all' (these don't apply to email)
            '/^\s*@media\s[^{]+{(.*})\s*}/misU', // get rid of remaining media type enclosures
        );

        $replace = array(
            '',
            '',
            '',
            '',
            '\\1',
        );

        $css = preg_replace($search, $replace, $css);

        $csskey = md5($css);
        if (!isset($this->caches[CACHE_CSS][$csskey])) {

            // process the CSS file for selectors and definitions
            preg_match_all('/(^|[^{}])\s*([^{]+){([^}]*)}/mis', $css, $matches, PREG_SET_ORDER);

            $all_selectors = array();
            foreach ($matches as $key => $selectorString) {
                // if there is a blank definition, skip
                if (!strlen(trim($selectorString[3]))) continue;

                // else split by commas and duplicate attributes so we can sort by selector precedence
                $selectors = explode(',',$selectorString[2]);
                foreach ($selectors as $selector) {

                    // don't process pseudo-elements and behavioral (dynamic) pseudo-classes; ONLY allow structural pseudo-classes
                    if (strpos($selector, ':') !== false && !preg_match('/:\S+\-(child|type)\(/i', $selector)) continue;

                    $all_selectors[] = array('selector' => trim($selector),
                                             'attributes' => trim($selectorString[3]),
                                             'line' => $key, // keep track of where it appears in the file, since order is important
                    );
                }
            }

            // now sort the selectors by precedence
            usort($all_selectors, array($this,'sortBySelectorPrecedence'));

            $this->caches[CACHE_CSS][$csskey] = $all_selectors;
        }

        foreach ($this->caches[CACHE_CSS][$csskey] as $value) {

            // query the body for the xpath selector
            $nodes = $xpath->query($this->translateCSStoXpath(trim($value['selector'])));

            foreach($nodes as $node) {
                // if it has a style attribute, get it, process it, and append (overwrite) new stuff
                if ($node->hasAttribute('style')) {
                    // break it up into an associative array
                    $oldStyleArr = $this->cssStyleDefinitionToArray($node->getAttribute('style'));
                    $newStyleArr = $this->cssStyleDefinitionToArray($value['attributes']);

                    // new styles overwrite the old styles (not technically accurate, but close enough)
                    $combinedArr = array_merge($oldStyleArr,$newStyleArr);
                    $style = '';
                    foreach ($combinedArr as $k => $v) $style .= (strtolower($k) . ':' . $v . ';');
                } else {
                    // otherwise create a new style
                    $style = trim($value['attributes']);
                }
                $node->setAttribute('style', $style);
            }
        }

        // now iterate through the nodes that contained inline styles in the original HTML
        foreach ($vistedNodeRef as $nodeKey => $origStyleArr) {
            $node = $vistedNodes[$nodeKey];
            $currStyleArr = $this->cssStyleDefinitionToArray($node->getAttribute('style'));

            $combinedArr = array_merge($currStyleArr, $origStyleArr);
            $style = '';
            foreach ($combinedArr as $k => $v) $style .= (strtolower($k) . ':' . $v . ';');

            $node->setAttribute('style', $style);
        }

        // This removes styles from your email that contain display:none.
        // We need to look for display:none, but we need to do a case-insensitive search. Since DOMDocument only supports XPath 1.0,
        // lower-case() isn't available to us. We've thus far only set attributes to lowercase, not attribute values. Consequently, we need
        // to translate() the letters that would be in 'NONE' ("NOE") to lowercase.
        $nodes = $xpath->query('//*[contains(translate(translate(@style," ",""),"NOE","noe"),"display:none")]');
        // The checks on parentNode and is_callable below ensure that if we've deleted the parent node,
        // we don't try to call removeChild on a nonexistent child node
        if ($nodes->length > 0)
            foreach ($nodes as $node)
                if ($node->parentNode && is_callable(array($node->parentNode,'removeChild')))
                        $node->parentNode->removeChild($node);

        if ($this->preserveEncoding) {
            return mb_convert_encoding($xmldoc->saveHTML(), $encoding, 'HTML-ENTITIES');
        } else {
            return $xmldoc->saveHTML();
        }
    }

    private function sortBySelectorPrecedence($a, $b) {
        $precedenceA = $this->getCSSSelectorPrecedence($a['selector']);
        $precedenceB = $this->getCSSSelectorPrecedence($b['selector']);

        // we want these sorted ascendingly so selectors with lesser precedence get processed first and
        // selectors with greater precedence get sorted last
        return ($precedenceA == $precedenceB) ? ($a['line'] < $b['line'] ? -1 : 1) : ($precedenceA < $precedenceB ? -1 : 1);
    }

    private function getCSSSelectorPrecedence($selector) {
        $selectorkey = md5($selector);
        if (!isset($this->caches[CACHE_SELECTOR][$selectorkey])) {
            $precedence = 0;
            $value = 100;
            $search = array('\#','\.',''); // ids: worth 100, classes: worth 10, elements: worth 1

            foreach ($search as $s) {
                if (trim($selector == '')) break;
                $num = 0;
                $selector = preg_replace('/'.$s.'\w+/','',$selector,-1,$num);
                $precedence += ($value * $num);
                $value /= 10;
            }
            $this->caches[CACHE_SELECTOR][$selectorkey] = $precedence;
        }

        return $this->caches[CACHE_SELECTOR][$selectorkey];
    }

    // right now we support all CSS 1 selectors and most CSS2/3 selectors.
    // http://plasmasturm.org/log/444/
    private function translateCSStoXpath($css_selector) {

        $css_selector = trim($css_selector);
        $xpathkey = md5($css_selector);
        if (!isset($this->caches[CACHE_XPATH][$xpathkey])) {
            // returns an Xpath selector
            $search = array(
                               '/\s+>\s+/', // Matches any element that is a child of parent.
                               '/\s+\+\s+/', // Matches any element that is an adjacent sibling.
                               '/\s+/', // Matches any element that is a descendant of an parent element element.
                               '/([^\/]+):first-child/i', // first-child pseudo-selector
                               '/([^\/]+):last-child/i', // last-child pseudo-selector
                               '/(\w)\[(\w+)\]/', // Matches element with attribute
                               '/(\w)\[(\w+)\=[\'"]?(\w+)[\'"]?\]/', // Matches element with EXACT attribute
                               '/(\w+)?\#([\w\-]+)/e', // Matches id attributes
                               '/(\w+|[\*\]])?((\.[\w\-]+)+)/e', // Matches class attributes

            );
            $replace = array(
                               '/',
                               '/following-sibling::*[1]/self::',
                               '//',
                               '*[1]/self::\\1',
                               '*[last()]/self::\\1',
                               '\\1[@\\2]',
                               '\\1[@\\2="\\3"]',
                               "(strlen('\\1') ? '\\1' : '*').'[@id=\"\\2\"]'",
                               "(strlen('\\1') ? '\\1' : '*').'[contains(concat(\" \",@class,\" \"),concat(\" \",\"'.implode('\",\" \"))][contains(concat(\" \",@class,\" \"),concat(\" \",\"',explode('.',substr('\\2',1))).'\",\" \"))]'",
            );

            $css_selector = '//'.preg_replace($search, $replace, $css_selector);

            // advanced selectors are going to require a bit more advanced emogrification
            // if we required PHP 5.3 we could do this with closures
            $css_selector = preg_replace_callback('/([^\/]+):nth-child\(\s*(odd|even|[+\-]?\d|[+\-]?\d?n(\s*[+\-]\s*\d)?)\s*\)/i', array($this, 'translateNthChild'), $css_selector);
            $css_selector = preg_replace_callback('/([^\/]+):nth-of-type\(\s*(odd|even|[+\-]?\d|[+\-]?\d?n(\s*[+\-]\s*\d)?)\s*\)/i', array($this, 'translateNthOfType'), $css_selector);

            $this->caches[CACHE_SELECTOR][$xpathkey] = $css_selector;
        }
        return $this->caches[CACHE_SELECTOR][$xpathkey];
    }

    private function translateNthChild($match) {

        $result = $this->parseNth($match);

        if (isset($result[self::MULTIPLIER])) {
            if ($result[self::MULTIPLIER] < 0) {
                $result[self::MULTIPLIER] = abs($result[self::MULTIPLIER]);
                return sprintf("*[(last() - position()) mod %u = %u]/self::%s", $result[self::MULTIPLIER], $result[self::INDEX], $match[1]);
            } else {
                return sprintf("*[position() mod %u = %u]/self::%s", $result[self::MULTIPLIER], $result[self::INDEX], $match[1]);
            }
        } else {
            return sprintf("*[%u]/self::%s", $result[self::INDEX], $match[1]);
        }
    }

    private function translateNthOfType($match) {

        $result = $this->parseNth($match);

        if (isset($result[self::MULTIPLIER])) {
            if ($result[self::MULTIPLIER] < 0) {
                $result[self::MULTIPLIER] = abs($result[self::MULTIPLIER]);
                return sprintf("%s[(last() - position()) mod %u = %u]", $match[1], $result[self::MULTIPLIER], $result[self::INDEX]);
            } else {
                return sprintf("%s[position() mod %u = %u]", $match[1], $result[self::MULTIPLIER], $result[self::INDEX]);
            }
        } else {
            return sprintf("%s[%u]", $match[1], $result[self::INDEX]);
        }
    }

    private function parseNth($match) {

        if (in_array(strtolower($match[2]), array('even','odd'))) {
            $index = strtolower($match[2]) == 'even' ? 0 : 1;
            return array(self::MULTIPLIER => 2, self::INDEX => $index);
        // if there is a multiplier
        } else if (stripos($match[2], 'n') === false) {
            $index = intval(str_replace(' ', '', $match[2]));
            return array(self::INDEX => $index);
        } else {

            if (isset($match[3])) {
                $multiple_term = str_replace($match[3], '', $match[2]);
                $index = intval(str_replace(' ', '', $match[3]));
            } else {
                $multiple_term = $match[2];
                $index = 0;
            }

            $multiplier = str_ireplace('n', '', $multiple_term);

            if (!strlen($multiplier)) $multiplier = 1;
            elseif ($multiplier == 0) return array(self::INDEX => $index);
            else $multiplier = intval($multiplier);

            while ($index < 0) $index += abs($multiplier);

            return array(self::MULTIPLIER => $multiplier, self::INDEX => $index);
        }
    }

    private function cssStyleDefinitionToArray($style) {
        $definitions = explode(';',$style);
        $retArr = array();
        foreach ($definitions as $def) {
            if (empty($def) || strpos($def, ':') === false) continue;
            list($key,$value) = explode(':',$def,2);
            if (empty($key) || strlen(trim($value)) === 0) continue;
            $retArr[trim($key)] = trim($value);
        }
        return $retArr;
    }
}