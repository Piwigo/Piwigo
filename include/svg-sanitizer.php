<?php

require_once( __DIR__ . '/svg-sanitizer/data/AttributeInterface.php' );
require_once( __DIR__ . '/svg-sanitizer/data/TagInterface.php' );
require_once( __DIR__ . '/svg-sanitizer/data/AllowedAttributes.php' );
require_once( __DIR__ . '/svg-sanitizer/data/AllowedTags.php' );
require_once( __DIR__ . '/svg-sanitizer/data/XPath.php' );
require_once( __DIR__ . '/svg-sanitizer/ElementReference/Resolver.php' );
require_once( __DIR__ . '/svg-sanitizer/ElementReference/Subject.php' );
require_once( __DIR__ . '/svg-sanitizer/ElementReference/Usage.php' );
require_once( __DIR__ . '/svg-sanitizer/Exceptions/NestingException.php' );
require_once( __DIR__ . '/svg-sanitizer/Helper.php' );
require_once( __DIR__ . '/svg-sanitizer/Sanitizer.php' );

use enshrined\svgSanitize\Sanitizer;

// Create a new sanitizer instance
global $sanitizer;
$sanitizer = new Sanitizer();


function validate_svg(string $svg_content): string
{
  global $sanitizer;
  try
  {
    $sanitize_status = $sanitizer->sanitize($svg_content);
    $issues = $sanitizer->getXmlIssues();
    return ($issues) ? $issues[0]['message'] : '';
  }
  catch(Exception $e)
  {
    return 'Exception during scan: '.$e->getMessage();
  }
}