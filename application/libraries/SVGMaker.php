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
  
  // Generate the def files for the web browsers that require them.
  function genDefs()
  {
    $def = $svg = $this->s->createElement('defs');
    $point = 8.5;
    $radius = 6.5625;
    
    foreach (array(1, 2) as $num)
    {
      $g = $this->s->createElement('g');
      $g->setAttribute('id', 'beat' . $num);
      
      foreach (array(0, 16) as $y)
      {
        $r = $this->s->createElement('rect');
        $r->setAttribute('x', 0);
        $r->setAttribute('y', $y);
        $r->setAttribute('height', 16);
        $r->setAttribute('width', 16);
        $g->appendChild($r);
      }
      if ($num === 1)
      {
        $l = $this->s->createElement('line');
        $l->setAttribute('x1', 0);
        $l->setAttribute('x2', 16);
        $l->setAttribute('y1', 0.1);
        $l->setAttribute('y2', 0.1);
        $g->appendChild($l);
      }
      $def->appendChild($g);
    }
    
    $g = $this->s->createElement('g');
    $g->setAttribute('id', 'measure');
    foreach (array(0, 32) as $y)
    {
      $u = $this->s->createElement('use');
      $u->setAttribute('x', 0);
      $u->setAttribute('y', $y);
      $u->setAttribute('xlink:href', '#beat' . ($y > 0 ? 2 : 1));
      $g->appendChild($u);
    }
    
    foreach (array(0.05, 15.95) as $x)
    {
      $l = $this->s->createElement('line');
      $l->setAttribute('x1', $x);
      $l->setAttribute('x2', $x);
      $l->setAttribute('y1', 0);
      $l->setAttribute('y2', 64);
      $g->appendChild($l);
    }
    $def->appendChild($g);
    
    // Now the arrows get defined.  Here: left arrow
    
    $g = $this->s->createElement('g');
    $g->setAttribute('id', 'Larrow');
    $p = $this->s->createElement('path');
    $p->setAttribute('d', 'm 1,8 7,7 2,-2 -3,-3 8,0 -2,-2 2,-2 -8,0 3,-3 -2,-2 z');
    $g->appendChild($p);
    
    $l = $this->s->createElement('path');
    $l->setAttribute('d', 'm 11,10 -2,-2 2,-2');
    $g->appendChild($l);
    
    $l = $this->s->createElement('path');
    $l->setAttribute('d', 'm 7,10 -2,-2 2,-2');
    $g->appendChild($l);
    
    $def->appendChild($g);
    
    // down arrow
    
    $g = $this->s->createElement('g');
    $g->setAttribute('id', 'Darrow');
    $p = $this->s->createElement('path');
    $p->setAttribute('d', 'm 8,15 7,-7 -2,-2 -3,3 0,-8 -2,2 -2,-2 0,8 -3,-3 -2,2 z');
    $g->appendChild($p);
    
    $l = $this->s->createElement('path');
    $l->setAttribute('d', 'm 10,5 -2,2 -2,-2');
    $g->appendChild($l);
    
    $l = $this->s->createElement('path');
    $l->setAttribute('d', 'm 10,9 -2,2 -2,-2');
    $g->appendChild($l);
    
    $def->appendChild($g);
    
    // up arrow
    
    $g = $this->s->createElement('g');
    $g->setAttribute('id', 'Uarrow');
    $p = $this->s->createElement('path');
    $p->setAttribute('d', 'm 8,1 -7,7 2,2 3,-3 0,8 2,-2 2,2 0,-8 3,3 2,-2 z');
    $g->appendChild($p);
    
    $l = $this->s->createElement('path');
    $l->setAttribute('d', 'm 6,11 2,-2 2,2');
    $g->appendChild($l);
    
    $l = $this->s->createElement('path');
    $l->setAttribute('d', 'm 6,7 2,-2 2,2');
    $g->appendChild($l);
    
    $def->appendChild($g);
    
    // right arrow
    
    $g = $this->s->createElement('g');
    $g->setAttribute('id', 'Rarrow');
    $p = $this->s->createElement('path');
    $p->setAttribute('d', 'm 15,8 -7,-7 -2,2 3,3 -8,0 2,2 -2,2 8,0 -3,3 2,2 z');
    $g->appendChild($p);
    
    $l = $this->s->createElement('path');
    $l->setAttribute('d', 'm 5,6 2,2 -2,2');
    $g->appendChild($l);
    
    $l = $this->s->createElement('path');
    $l->setAttribute('d', 'm 9,6 2,2 -2,2');
    $g->appendChild($l);
    
    $def->appendChild($g);
    
    // mine
    
    $g = $this->s->createElement('mine');
    $g->setAttribute('id', 'mine');
    
    foreach (array(7, 5, 3) as $r)
    {
      $c = $this->s->createElement('circle');
      $c->setAttribute('cx', 8);
      $c->setAttribute('cy', 8);
      $c->setAttribute('r', $r);
      $g->appendChild($c);
    }
    $def->appendChild($g);
    
    foreach (array("hold", "roll") as $t)
    {
      $g = $this->s->createElement('g');
      $g->setAttribute('id', $t . '_bdy');
      $r = $this->s->createElement('rect');
      $r->setAttribute('x', 1);
      $r->setAttribute('y', 0);
      $r->setAttribute('width', 14);
      $r->setAttribute('height', 16);
      $g->appendChild($r);
      
      foreach (array(1, 15) as $x)
      {
        $l = $this->s->createElement('line');
        $l->setAttribute('x1', $x);
        $l->setAttribute('y1', 0);
        $l->setAttribute('x2', $x);
        $l->setAttribute('y2', 16);
        $g->appendChild($l);
      }
      $def->appendChild($g);
      $g = $this->s->createElement('g');
      $g->setAttribute('id', $t . '_end');
      $p = $this->s->createElement('path');
      $p->setAttribute('d', 'm 1,0 v 13 c 0,0 0,2 2,2 h 10 c 0,0 2,0 2,-2 v -13');
      $g->appendChild($p);
      $def->appendChild($g);
    }
    
    return $def;
  }
}