<?php
# This class is here to relieve EditCharter and allow better abstraction.
class SVGMaker
{
  function __construct()
  {
    $this->s = new DomDocument('1.0', 'utf-8');
  }
  
  // Make a general <use> tag.
  function genUse($x, $y, $options = array())
  {
    $base = "";

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
  
  // Make a text tag.
  function genText($x, $y, $st, $options = array())
  {
    $txt = $this->s->createElement('text');
    $txt->setAttribute('x', $x);
    $txt->setAttribute('y', $y);
    if (array_key_exists('class', $options) and strlen($options['class']) > 1)
      $txt->setAttribute('class', $options['class']);
    $txt->appendChild($this->s->createTextNode($st));
    return $txt;
  }
  
  // Make a line tag.
  function genLine($x1, $y1, $x2, $y2, $options = array())
  {
    $line = $this->s->createElement('line');
    $line->setAttribute('x1', $x1);
    $line->setAttribute('y1', $y1);
    $line->setAttribute('x2', $x2);
    $line->setAttribute('y2', $y2);
    if (array_key_exists('class', $options) and strlen($options['class']) > 1)
      $line->setAttribute('class', $options['class']);
    return $line;
  }
}