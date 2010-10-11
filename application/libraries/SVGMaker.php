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
    $use = $this->s->createElement('use');
    if ($x > 0) $use->setAttribute('x', $x);
    if ($y > 0) $use->setAttribute('y', $y);
    
    if (array_key_exists('href', $options))
      $use->setAttribute('xlink:href', "#" . $options['href']);
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
  
    if (array_key_exists('id', $options) and strlen($options['id']) > 1)
	{
		$txt->setAttribute('xml:id', $options['id']);
	}
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
  
  // Make a rect tag.
  function genRect($x, $y, $w, $h, $options = array())
  {
    $rect = $this->s->createElement('rect');
    $rect->setAttribute('x', $x);
    $rect->setAttribute('y', $y);
    $rect->setAttribute('width', $w);
    $rect->setAttribute('height', $h);
    if (array_key_exists('rx', $options))
      $rect->setAttribute('rx', $options['rx']);
    return $rect;
  }
  
  
  // Make a path tag.
  function genPath($m, $options = array())
  {
    $path = $this->s->createElement('path');
    $path->setAttribute('d', $m);
    return $path;
  }
}
