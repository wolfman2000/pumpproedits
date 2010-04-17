<?php
# This class is here to relieve EditCharter and allow better abstraction.
class SVGMaker
{
  function __construct()
  {
    $this->s = new DomDocument('1.0', 'utf-8');
  }
  
  // Make a general <use> tag.
  function genUse($x, $y, $options)
  {
    $base = APP_CHART_DEF_FILE;

    // Need to target both Safari and WebKit at once. This may have to stay.
    if (strpos($_SERVER['HTTP_USER_AGENT'], "WebKit") !== false)
    {
      $base = "";
    }

    $use = $this->s->createElement('use');
    if ($x > 0) $use->setAttribute('x', $x);
    if ($y > 0) $use->setAttribute('y', $y);
    
    if (array_key_exists('href', $options))
      $use->setAttribute('xlink:href', "$base#" . $options['href']);
    if (array_key_exists('class', $options) and strlen($options['class']) > 1)
      $use->setAttribute('class', $options['class']);
    if (array_key_exists('transform', $options))
      $use->setAttribute('transform', $options['transform']);
    return $use;
  }
}