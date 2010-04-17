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
    
    // Now the arrows get defined.  Here: down left arrow
    
    $g = $this->s->createElement('g');
    $g->setAttribute('id', 'DLarrow');
    $p = $this->s->createElement('path');
    $p->setAttribute('d', 'm 1,2 v 12 c 0,0 0,1 1,1 h 12 c 0,0 1,0 1,-1 0,0 0,-1 -1,-1 '
      . 'H 7 L 15,5 V 2 C 15,2 15,1 14,1 H 11 L 3,9 V 2 C 3,2 3,1 2,1 2,1 1,1 1,2');
    $g->appendChild($p);
    
    $l = $this->s->createElement('line');
    $l->setAttribute('x1', 15);
    $l->setAttribute('x2', 11);
    $l->setAttribute('y1', 5);
    $l->setAttribute('y2', 1);
    $g->appendChild($l);
    
    $l = $this->s->createElement('line');
    $l->setAttribute('x1', 11);
    $l->setAttribute('x2', 7);
    $l->setAttribute('y1', 9);
    $l->setAttribute('y2', 5);
    $g->appendChild($l);
    
    $l = $this->s->createElement('line');
    $l->setAttribute('x1', 7);
    $l->setAttribute('x2', 3);
    $l->setAttribute('y1', 13);
    $l->setAttribute('y2', 9);
    $g->appendChild($l);
    
    $def->appendChild($g);
    
    // up left arrow
    
    $g = $this->s->createElement('g');
    $g->setAttribute('id', 'ULarrow');
    $p = $this->s->createElement('path');
    $p->setAttribute('d', 'M 1,14 V 2 C 1,2 1,1 2,1 h 12 c 0,0 1,0 1,1 0,0 0,1 -1,1 '
      . 'H 7 l 8,8 v 3 c 0,0 0,1 -1,1 H 11 L 3,6 v 8 c 0,0 0,1 -1,1 0,0 -1,0 -1,-1');
    $g->appendChild($p);
    
    $l = $this->s->createElement('line');
    $l->setAttribute('x1', 15);
    $l->setAttribute('x2', 11);
    $l->setAttribute('y1', 11);
    $l->setAttribute('y2', 15);
    $g->appendChild($l);
    
    $l = $this->s->createElement('line');
    $l->setAttribute('x1', 11);
    $l->setAttribute('x2', 7);
    $l->setAttribute('y1', 7);
    $l->setAttribute('y2', 11);
    $g->appendChild($l);
    
    $l = $this->s->createElement('line');
    $l->setAttribute('x1', 7);
    $l->setAttribute('x2', 3);
    $l->setAttribute('y1', 3);
    $l->setAttribute('y2', 7);
    $g->appendChild($l);
    
    $def->appendChild($g);
    
    // center arrow
    
    $g = $this->s->createElement('g');
    $g->setAttribute('id', 'CNarrow');
    $p = $this->s->createElement('path');
    $p->setAttribute('d', 'm 1,2 v 12 l 1,1 h 12 l 1,-1 V 2 L 14,1 H 2 z');
    $g->appendChild($p);
    
    foreach (array(4, 10) as $x)
    {
    
      $l = $this->s->createElement('rect');
      $l->setAttribute('x', $x);
      $l->setAttribute('y', 6);
      $l->setAttribute('height', 4);
      $l->setAttribute('width', 2);
      $l->setAttribute('rx', 0.5);
      $g->appendChild($l);
    }
    $def->appendChild($g);
    
    // up right arrow
    
    $g = $this->s->createElement('g');
    $g->setAttribute('id', 'URarrow');
    $p = $this->s->createElement('path');
    $p->setAttribute('d', 'M 15,14 V 2 C 15,2 15,1 14,1 H 2 C 2,1 1,1 1,2 1,2 1,3 2,3 '
      . 'h 7 l -8,8 v 3 c 0,0 0,1 1,1 h 3 l 8,-8 v 7 c 0,0 0,1 1,1 0,0 1,0 1,-1');
    $g->appendChild($p);
    
    $l = $this->s->createElement('line');
    $l->setAttribute('x1', 1);
    $l->setAttribute('x2', 5);
    $l->setAttribute('y1', 11);
    $l->setAttribute('y2', 15);
    $g->appendChild($l);
    
    $l = $this->s->createElement('line');
    $l->setAttribute('x1', 5);
    $l->setAttribute('x2', 9);
    $l->setAttribute('y1', 7);
    $l->setAttribute('y2', 11);
    $g->appendChild($l);
    
    $l = $this->s->createElement('line');
    $l->setAttribute('x1', 9);
    $l->setAttribute('x2', 13);
    $l->setAttribute('y1', 3);
    $l->setAttribute('y2', 7);
    $g->appendChild($l);
    
    $def->appendChild($g);
    
    // down right arrow
    
    $g = $this->s->createElement('g');
    $g->setAttribute('id', 'DRarrow');
    $p = $this->s->createElement('path');
    $p->setAttribute('d', 'm 15,2 v 12 c 0,0 0,1 -1,1 H 2 c 0,0 -1,0 -1,-1 0,0 0,-1 1,-1 '
      . 'H 9 L 1,5 V 2 C 1,2 1,1 2,1 h 3 l 8,8 V 2 c 0,0 0,-1 1,-1 0,0 1,0 1,1');
    $g->appendChild($p);
    
    $l = $this->s->createElement('line');
    $l->setAttribute('x1', 1);
    $l->setAttribute('x2', 5);
    $l->setAttribute('y1', 5);
    $l->setAttribute('y2', 1);
    $g->appendChild($l);
    
    $l = $this->s->createElement('line');
    $l->setAttribute('x1', 5);
    $l->setAttribute('x2', 9);
    $l->setAttribute('y1', 9);
    $l->setAttribute('y2', 5);
    $g->appendChild($l);
    
    $l = $this->s->createElement('line');
    $l->setAttribute('x1', 9);
    $l->setAttribute('x2', 13);
    $l->setAttribute('y1', 13);
    $l->setAttribute('y2', 9);
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
