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
  // Generate the def files for the web browsers that require them.
  function genDefs($style = "nope")
  {
    $def = $this->s->createElement('defs');
    $point = 8.5;
    $radius = 6.5625;
    
    foreach (array(1, 2) as $num)
    {
      $g = $this->s->createElement('g');
      $g->setAttribute('id', 'beat' . $num);
      
      foreach (array(0, 16) as $y)
      {
        $g->appendChild($this->genRect(0, $y, 16, 16));
      }
      if ($num === 1)
      {
        $g->appendChild($this->genLine(0, 0.1, 16, 0.1));
      }
      $def->appendChild($g);
    }
    
    $g = $this->s->createElement('g');
    $g->setAttribute('id', 'measure');
    foreach (array(0, 32) as $y)
    {
      $g->appendChild($this->genUse(0, $y, array('href' => 'beat' . ($y > 0 ? 2 : 1))));
    }
    
    foreach (array(0.05, 15.95) as $x)
    {
      $g->appendChild($this->genLine($x, 0, $x, 64));
    }
    $def->appendChild($g);
    
    $pls = ($style !== "pump-routine" ? array('') : array('P0', 'P1'));
    foreach ($pls as $player)
    {
      
      
      // Now the arrows get defined.  First, the general corner arrow.
      
      $g = $this->s->createElement('g');
      $g->setAttribute('id', $player . 'arrow');
      $g->appendChild($this->genPath('m 1,2 v 12 c 0,0 0,1 1,1 h 12 c 0,0 1,0 1,-1 v -2 c 0,0 0,-1 -1,-1 '
      . 'h -6 l 7,-7 v -2 c 0,0 0,-1 -1,-1 h -2 l -7,7 v -6 c 0,0 0,-1 -1,-1 h -2 c 0,0 -1,0 -1,1 v 1'));
      
      $g->appendChild($this->genPath('m 14.25,4.75 l -3,-3'));
      $g->appendChild($this->genPath('m 11,8 l -3,-3'));
      $g->appendChild($this->genPath('m 7.75,11.25 l -3,-3'));
      
      $def->appendChild($g);
      
      // The center arrow has a different shape.
      
      $g = $this->s->createElement('g');
      $g->setAttribute('id', $player . 'center');
      $p = $this->s->createElement('path');
      $p->setAttribute('d', 'm 1,2 v 12 l 1,1 h 12 l 1,-1 V 2 L 14,1 H 2 z');
      $g->appendChild($p);
      
      foreach (array(4, 10) as $x)
      {
        $g->appendChild($this->genRect($x, 6, 2, 4, array('rx' => 0.5)));
      }
      $def->appendChild($g);
      
      // lift
      
      $g = $this->s->createElement('g');
      $g->setAttribute('id', $player . 'lift');
      
      $p = $this->s->createElement('path');
      $p->setAttribute('d', 'm 8,1 -7,7 2,2 3,-3 0,8 2,-2 2,2 0,-8 3,3 2,-2 z');
      $g->appendChild($p);
      $p = $this->s->createElement('path');
      $p->setAttribute('d', 'm 6,11 2,-2 2,2');
      $g->appendChild($p);
      $p = $this->s->createElement('path');
      $p->setAttribute('d', 'm 6,7 2,-2 2,2');
      $g->appendChild($p);
      
      $def->appendChild($g);
      
      // fake
      
      $g = $this->s->createElement('g');
      $g->setAttribute('id', $player . 'fake');
      
      $p = $this->s->createElement('path');
      $p->setAttribute('d', 'm 1,3 l 5,5 -5,5 2,2 5,-5 5,5 2,-2 -5,-5 5,-5 -2,-2 -5,5 -5,-5 z');
      $g->appendChild($p);
      
      $def->appendChild($g);
      
      // mine
      
      $g = $this->s->createElement('g');
      $g->setAttribute('id', $player . 'mine');
      
      foreach (array(7, 5, 3) as $r)
      {
        $c = $this->s->createElement('circle');
        $c->setAttribute('cx', 8);
        $c->setAttribute('cy', 8);
        $c->setAttribute('r', $r);
        $g->appendChild($c);
      }
      $def->appendChild($g);
      
    }
    foreach (array("hold", "roll") as $t)
    {
      $g = $this->s->createElement('g');
      $g->setAttribute('id', $t . '_bdy');
      $g->appendChild($this->genRect(1, 0, 14, 16));
      
      foreach (array(1, 15) as $x)
      {
        $g->appendChild($this->genLine($x, 0, $x, 16));
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
