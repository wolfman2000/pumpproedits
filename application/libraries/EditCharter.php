<?php

class EditCharter
{
  function __construct($params)
  {
    if (!in_array($params['cols'], array(APP_CHART_SIN_COLS, APP_CHART_DBL_COLS)))
    {
      $e = sprintf("There must be either %d or %d columns in the chart!",
        APP_CHART_SIN_COLS, APP_CHART_DBL_COLS);
      throw new Exception($e);
    }
    $this->lb = APP_CHART_COLUMN_LEFT_BUFFER;
    $this->rb = APP_CHART_COLUMN_RIGHT_BUFFER;
    $this->aw = APP_CHART_ARROW_WIDTH;
    $this->bm = APP_CHART_BEAT_P_MEASURE;
    
    if (array_key_exists('nobpm', $params) and $params['nobpm'])
    {
      $this->showbpm = 0;
    }
    else
    {
      $this->showbpm = 1;
    }
    if (array_key_exists('nostop', $params) and $params['nostop'])
    {
      $this->showstop = 0;
    }
    else
    {
      $this->showstop = 1;
    }
    
    # Is the header supposed to be arcade style?
    
    if (array_key_exists('arcade', $params) and $params['arcade'])
    {
      $this->arcade = 1;
    }
    else
    {
      $this->arcade = 0;
    }
    
    # How much of a zoom is there for the chart?
    if (array_key_exists('scale', $params) and $params['scale'])
    {
      $this->scale = $params['scale'];
    }
    else
    {
      $this->scale = 1;
    }
    
    $this->headheight = APP_CHART_HEADER_HEIGHT;
    $this->footheight = APP_CHART_FOOTER_HEIGHT;
    if (array_key_exists('footer_height', $params))
    {
      $this->footheight = $params['footer_height'];
    }
    
    $this->speedmod = APP_CHART_SPEED_MOD;
    if (array_key_exists('speed_mod', $params))
    {
      $this->speedmod = $params['speed_mod'];
    }
    
    $this->mpcol = APP_CHART_MEASURE_COL;
    if (array_key_exists('mpcol', $params))
    {
      $this->mpcol = $params['mpcol'];
    }
    
    $this->cols = $params['cols'];
    $this->cw = $this->cols * $this->aw;
    
    $tmp = new DomImplementation;
    $dtd = $tmp->createDocumentType("html");
    $this->xml = $tmp->createDocument("", "", $dtd);
    $this->xml->encoding = "UTF-8";
    $this->xml->preserveWhiteSpace = false;
    $this->xml->formatOutput = true; # May change this.
  }

  private function genUseNode($x, $y, $id, $class = '', $sx = 1, $sy = 1)
  {
    $base = APP_CHART_DEF_FILE;

    // Need to target both Safari and WebKit at once. This may have to stay.
    if (strpos($_SERVER['HTTP_USER_AGENT'], "WebKit") !== false)
    {
      $base = "";
    }

    $use = $this->xml->createElement('use');
    if ($x > 0) $use->setAttribute('x', $x);
    if ($y > 0) $use->setAttribute('y', $y);
    $use->setAttribute('xlink:href', "$base#$id");
    if (strlen($class) > 1) $use->setAttribute('class', "$class");
    if (!($sx === 1 and $sy === 1))
    {
      $use->setAttribute('transform', "scale($sx $sy)");
    }  
    return $use;
  }
  
  private function genSVGNode($x, $y, $id, $class = '', $sx = 1, $sy = 1)
  {
    $svg = $this->xml->createElement('svg');
    $svg->setAttribute('x', $x);
    $svg->setAttribute('y', $y);
    $svg->appendChild($this->genUseNode(0, 0, $id, $class, $sx, $sy));
    return $svg;
  }

  private function genXMLHeader($measures)
  {
    // Place the surrounding HTML in first.
    $html = $this->xml->createElement('html');
    $html->setAttribute('xmlns', 'http://www.w3.org/1999/xhtml');
    $head = $this->xml->createElement('head');
    $title = $this->xml->createElement('title');
    $title->appendChild($this->xml->createTextNode("The Chart"));
    $link = $this->xml->createElement('link');
    $link->setAttribute('type', 'text/css');
    $link->setAttribute('rel', 'stylesheet');
    $link->setAttribute('href', '/css/_svg.css');
    $head->appendChild($title);
    $head->appendChild($link);
    $html->appendChild($head);
    $body = $this->xml->createElement('body');
    
    $svg = $this->xml->createElement('svg');
    $svg->setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    $svg->setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
    $svg->setAttribute('version', 1.1);
    
    $body->appendChild($svg);
    $html->appendChild($body);
    
    // Calculate the width of the outer svg.
    $numcols = ceil($measures / $this->mpcol);
    $breather = $this->lb + $this->rb;
    $width = ($this->aw * $this->cols + $breather) * $numcols + $breather;
    $svg->setAttribute('width', $width * $this->scale);
    
    // Calculate the height of the outer svg.
    $beatheight = APP_CHART_BEAT_HEIGHT;
        
    $height = $beatheight * $this->bm * $this->speedmod * $this->mpcol;
    $height += $this->headheight + $this->footheight;
    $svg->setAttribute('height', $height * $this->scale);
    $this->svgheight = $height;
    
    // Again, I need to target both Safari and Chrome.
    if (strpos($_SERVER['HTTP_USER_AGENT'], "WebKit") !== false)
    {
      $svg->appendChild($this->genDefs());
    }
    
    $this->xml->appendChild($html);
    
    $g = $this->xml->createElement('g');
    $g->setAttribute('transform', "scale($this->scale)");
    $svg->appendChild($g);
    
    $this->svg = $g; # Will be used for arrow placements.
    
  }
  
  private function genMeasures($measures)
  {
    $numcols = ceil($measures / $this->mpcol); // mpcol is measures per column
    $beatheight = APP_CHART_BEAT_HEIGHT; // default beat height
    $spd = $this->speedmod; // speed mod: also affects columns.
    $breather = $this->lb + $this->rb;
    for ($i = 0; $i < $numcols; $i++)
    {
      $x = ($this->aw * $this->cols + $breather) * $i + $this->lb;
      $sx = $this->cols;
      for ($j = 0; $j < $this->mpcol * $spd; $j++)
      {
        $y = $beatheight * $j * $this->bm + $this->headheight;
        $use = $this->genSVGNode($x, $y, "measure", '', $sx);
        $this->svg->appendChild($use);
      }
    }
  }
  
  private function genTxtNode($x, $y, $st, $class = '')
  {
    $txt = $this->xml->createElement('text');
    $txt->setAttribute('x', $x);
    $txt->setAttribute('y', $y);
    if (strlen($class) > 1) $txt->setAttribute('class', $class);
    $txt->appendChild($this->xml->createTextNode($st));
    $this->svg->appendChild($txt);
  }
  
  private function genEditHeader($nd)
  {
    $lbuff = $this->lb;
    
    if ($this->arcade)
    {
      $this->genTxtNode($lbuff, 16, sprintf("%s %s - %d",
        $nd['song'], $nd['title'], $nd['diff']));
    }
    else
    {
      $this->genTxtNode($lbuff, 16, sprintf("%s %s Edit: %s - %d",
        $nd['song'], ucfirst(substr($nd['style'], 6)), $nd['title'], $nd['diff']));
    }
    $this->genTxtNode($lbuff, 32, $nd['author']);
    
    $this->genTxtNode($lbuff, 64,
      "Steps: " . $nd['steps'][0]);
    $this->genTxtNode($lbuff, 80,
      "Jumps: " . $nd['jumps'][0]);
    
    $w = $this->cw + $lbuff + $this->rb;
    
    $this->genTxtNode($lbuff + $w * 1, 64,
      "Holds: " . $nd['holds'][0]);
    $this->genTxtNode($lbuff + $w * 1, 80,
      "Mines: " . $nd['mines'][0]);
    $this->genTxtNode($lbuff + $w * 2, 64,
      "Trips: " . $nd['trips'][0]);
    $this->genTxtNode($lbuff + $w * 2, 80,
      "Rolls: " . $nd['rolls'][0]);
  }
  
  private function genBPM($id)
  {
    $buff = $this->lb + $this->rb;
    $draw = $this->cols * $this->aw / 2;
    $m = $this->aw * $this->bm * $this->speedmod;
    $CI =& get_instance();
    $CI->load->model('itg_song_bpm');
    
    foreach ($CI->itg_song_bpm->getBPMsBySongID($id) as $b)
    {
      $beat = $b->beat;
      $bpm = $b->bpm;
      $measure = $beat / $this->bm;
      $mpcol = $this->mpcol; # How many measures are in a column?
      $col = floor(floor($measure) / $mpcol); # Find the right column.
      $down = $measure % $mpcol + $measure - floor($measure); # Find the specific measure.
      
      $lx = ($buff + ($this->cols * $this->aw)) * $col + $this->lb;
      $ly = $down * $m + $this->headheight;
      
      $line = $this->xml->createElement('line');
      $line->setAttribute('x1', $lx + $draw);
      $line->setAttribute('y1', $ly + 0.2);
      $line->setAttribute('x2', $lx + $draw + $draw);
      $line->setAttribute('y2', $ly + 0.2);
      $line->setAttribute('class', 'bpm');
      $this->svg->appendChild($line);
      
      if (isset($bpm))
      {
        $pos = strpos($bpm, ".");
        if ($pos !== false)
        {
          $bpm = trim(trim($bpm, '0'), '.');
        }
        $this->genTxtNode($lx + $draw + $draw, $ly + $this->bm, $bpm, 'bpm');
      }
    }
  }
  
  private function genStop($id)
  {
    $buff = $this->lb + $this->rb;
    $draw = $this->cols * $this->aw / 2;
    $m = $this->aw * $this->bm * $this->speedmod;
    $CI =& get_instance();
    $CI->load->model('itg_song_stop');
    foreach ($CI->itg_song_stop->getStopsBySongID($id) as $b)
    {
      $beat = $b->beat;
      $break = $b->break;
      $measure = $beat / $this->bm;
      $mpcol = $this->mpcol; # How many measures are in a column?
      $col = floor(floor($measure) / $mpcol); # Find the right column.
      $down = $measure % $mpcol + $measure - floor($measure); # Find the specific measure.
      
      $lx = ($buff + ($this->cols * $this->aw)) * $col + $this->lb;
      $ly = $down * $m + $this->headheight;
      
      $line = $this->xml->createElement('line');
      $line->setAttribute('x1', $lx);
      $line->setAttribute('y1', $ly + 0.2);
      $line->setAttribute('x2', $lx + $draw);
      $line->setAttribute('y2', $ly + 0.2);
      $line->setAttribute('class', 'stop');
      $this->svg->appendChild($line);
      
      if (isset($break))
      {
        $break = rtrim(rtrim($break, '0'), '.') . "B";
        $break = ltrim($break, '0');
        $this->genTxtNode($lx - $this->aw, $ly + $this->bm, $break, 'stop');
      }
    }
  }
  
  private function prepArrows($counter = 0)
  {
    $pre = 'note';
    $ret = array();
    $div = array('4th', '8th', '12th', '16th',
      '24th', '32nd', '48th', '64th', '192nd');
    foreach ($div as $f)
    {
      if (intval($f) == 4) $g = $pre . '_008';
      elseif (intval($f) == 8) $g = $pre . '_004';
      else $g = sprintf('%s_%03d', $pre, intval($f));

      $l = array('a' => 'L', 'c' => $g);
      $d = array('a' => 'D', 'c' => $g);
      $u = array('a' => 'U', 'c' => $g);
      $r = array('a' => 'R', 'c' => $g);
      $ret[$f] = array($l, $d, $u, $r);
      if ($this->cols == APP_CHART_DBL_COLS)
      {
        array_push($ret[$f], $l, $d, $u, $r);
      }
    }
    return $ret;
  }
  
  private function getBeat($beat)
  {
    switch ($beat % 48)
    {
      case 0: return '4th';
      case 24: return '8th';
      case 16: case 32: return '12th';
      case 12: case 36: return '16th';
      case 8: case 40: return '24th';
      case 6: case 18: case 30: case 42: return '32nd';
      case 4: case 20: case 28: case 44: return '48th';
      case 3: case 9: case 15: case 21:
      case 27: case 33: case 39: case 45: return '64th';
      default: return '192nd';
    }
  }
  
  private function genArrows($notes, $style = "pump-single")
  {
    for ($i = 0; $i < $this->cols; $i++)
    {
      $holds[] = array('on' => false, 'hold' => true, 'x' => 0, 'y' => 0, 'beat' => 0);
    }
    $w = $this->cw + $this->lb + $this->rb; # width + buffers.
    $m = $this->aw * $this->bm * $this->speedmod; # height of measure block
    
    $ucounter = 1;
    foreach ($notes as $player):
    
    $arrows = $this->prepArrows($style === "pump-routine" ? $ucounter : 0);

    $mcounter = 0;    
    foreach ($player as $measure):
    
    $rcounter = 0;
    foreach ($measure as $row):
    
    $curbeat = intval(round($m * $rcounter / count($measure)));
      
    $arow = $arrows[$this->getBeat(192 * $rcounter / count($measure))];
    
    $pcounter = 0;
    foreach (str_split($row) as $let): # For each note in the row
    
    $nx = (intval($mcounter / $this->mpcol) * $w) + $pcounter * $this->aw + $this->lb;
    $ny = $this->headheight + ($mcounter % $this->mpcol) * $m + $curbeat;
    
    # Stepchart part here.
    
    switch ($let)
    {
      case "1": # Tap note. Just add to the chart.
      {
        $id = $arow[$pcounter]['a'] . "arrow";
        $cl = $arow[$pcounter]['c'];
        $this->svg->appendChild($this->genUseNode($nx, $ny, $id, $cl));
        break;
      }
      case "2": case "4": # Start of hold/roll. Minor differences.
      {
        $holds[$pcounter]['on'] = true;
        $holds[$pcounter]['roll'] = $let == "2" ? false : true;
        $holds[$pcounter]['x'] = $nx;
        $holds[$pcounter]['y'] = $ny;
        $holds[$pcounter]['beat'] = $arow;
        break;
      }
      case "3": # End of hold/roll. VERY complicated!
      {
        if ($holds[$pcounter]['on'])
        {
          $id = $holds[$pcounter]['roll'] ? "roll" : "hold";
          $bod = "{$id}_bdy";
          $end = "{$id}_end";
          $a = $holds[$pcounter]['beat'][$pcounter];
          
          $ox = $holds[$pcounter]['x'];
          $oy = $holds[$pcounter]['y'];
          
          # First: check if tap note was on previous column.
          if ($holds[$pcounter]['x'] < $nx)
          {
            # Body goes first.
            
            # Calculate the scale for the hold.
            $bot = $this->svgheight - $this->aw;
            $hy = $oy + $this->aw / 2;
            $range = $bot - $hy;
            $sy = $range / $this->aw;
            
            $node = $this->genSVGNode($ox, $hy, $bod, '', 1, $sy);
            $this->svg->appendChild($node);
            # Place the tap.
            $this->svg->appendChild($this->genUseNode($ox, $oy, $a['a'] . "arrow", $a['c']));
            
            $ox += $w;
            $hy = $this->headheight;
            while ($ox < $nx)
            {
              $range = $bot - $hy;
              $sy = $range / $this->aw;
              $this->svg->appendChild($this->genSVGNode($ox, $hy, $bod, '', 1, $sy));
              $ox += $w;
            }
            # Now we're on the same column as the tail.
            $bot = $ny + $this->aw / 2;
            $range = $bot - $hy;
            $sy = $range / $this->aw;
            $this->svg->appendChild($this->genSVGNode($nx, $hy, $bod, '', 1, $sy));
            $this->svg->appendChild($this->genUseNode($nx, $ny, $end));
          }
          else
          {
            if ($ny - $oy >= intval($this->aw / 2)) # Make this variable
            {
              $bot = $ny + $this->aw / 2;
              $hy = $oy + $this->aw / 2;
              $range = $bot - $hy;
              $sy = $range / $this->aw;
              $this->svg->appendChild($this->genSVGNode($nx, $hy, $bod, '', 1, $sy));
            }
            # Tail next
            $this->svg->appendChild($this->genUseNode($nx, $ny, $end));
            # Tap note last.
            $this->svg->appendChild($this->genUseNode($ox, $oy, $a['a'] . "arrow", $a['c']));
          } 
        }
        else # Throw an error at some point.
        {
          $id = $arow[$pcounter]['a'] . "arrow";
          $cl = $arow[$pcounter]['c'];
          $this->svg->appendChild($this->genUseNode($nx, $ny, $id, $cl));
        }
        break;
      }
      case "M": # Mine. Don't step on these!
      {
        $holds[$pcounter]['on'] = false;
        $this->svg->appendChild($this->genUseNode($nx, $ny, "mine"));
        break;
      }
    }
    
    $pcounter++;
    endforeach;
    
    $rcounter++;
    endforeach;
    
    $mcounter++;
    endforeach;
    
    break; // lazy fix.
    endforeach;
  }
  
  /**
   * Generate the definitions in a separate function for ease of use.
   */
  private function genDefs()
  {
    $def = $svg = $this->xml->createElement('defs');
    $point = 8.5;
    $radius = 6.5625;
    
    foreach (array('004', '008', '012', '016', '024', '032', '048', '064', '192') as $rg)
    {
      $node = $this->xml->createElement('radialGradient');
      $node->setAttribute('id', 'grad_' . $rg);
      
      foreach (array('cx', 'cy', 'fx', 'fy') as $at)
      {
        $node->setAttribute($at, $point);
      }
      $node->setAttribute('r', $radius);
      $node->setAttribute('gradientUnits', 'userSpaceOnUse');
      foreach (array(0, 1) as $so)
      {
        $stop = $this->xml->createElement('stop');
        $stop->setAttribute('offset', $so);
        $node->appendChild($stop);
      }
      $def->appendChild($node);
    }
    
    foreach (array(1, 2) as $num)
    {
      $g = $this->xml->createElement('g');
      $g->setAttribute('id', 'beat' . $num);
      
      foreach (array(0, 16) as $y)
      {
        $r = $this->xml->createElement('rect');
        $r->setAttribute('x', 0);
        $r->setAttribute('y', $y);
        $r->setAttribute('height', 16);
        $r->setAttribute('width', 16);
        $g->appendChild($r);
      }
      if ($num === 1)
      {
        $l = $this->xml->createElement('line');
        $l->setAttribute('x1', 0);
        $l->setAttribute('x2', 16);
        $l->setAttribute('y1', 0.1);
        $l->setAttribute('y2', 0.1);
        $g->appendChild($l);
      }
      $def->appendChild($g);
    }
    
    $g = $this->xml->createElement('g');
    $g->setAttribute('id', 'measure');
    foreach (array(0, 32) as $y)
    {
      $u = $this->xml->createElement('use');
      $u->setAttribute('x', 0);
      $u->setAttribute('y', $y);
      $u->setAttribute('xlink:href', '#beat' . ($y > 0 ? 2 : 1));
      $g->appendChild($u);
    }
    
    foreach (array(0.05, 15.95) as $x)
    {
      $l = $this->xml->createElement('line');
      $l->setAttribute('x1', $x);
      $l->setAttribute('x2', $x);
      $l->setAttribute('y1', 0);
      $l->setAttribute('y2', 64);
      $g->appendChild($l);
    }
    $def->appendChild($g);
    
    // Now the arrows get defined.  Here: left arrow
    
    $g = $this->xml->createElement('g');
    $g->setAttribute('id', 'Larrow');
    $p = $this->xml->createElement('path');
    $p->setAttribute('d', 'm 1,8 7,7 2,-2 -3,-3 8,0 -2,-2 2,-2 -8,0 3,-3 -2,-2 z');
    $g->appendChild($p);
    
    $l = $this->xml->createElement('path');
    $l->setAttribute('d', 'm 11,10 -2,-2 2,-2');
    $g->appendChild($l);
    
    $l = $this->xml->createElement('path');
    $l->setAttribute('d', 'm 7,10 -2,-2 2,-2');
    $g->appendChild($l);
    
    $def->appendChild($g);
    
    // down arrow
    
    $g = $this->xml->createElement('g');
    $g->setAttribute('id', 'Darrow');
    $p = $this->xml->createElement('path');
    $p->setAttribute('d', 'm 8,15 7,-7 -2,-2 -3,3 0,-8 -2,2 -2,-2 0,8 -3,-3 -2,2 z');
    $g->appendChild($p);
    
    $l = $this->xml->createElement('path');
    $l->setAttribute('d', 'm 10,5 -2,2 -2,-2');
    $g->appendChild($l);
    
    $l = $this->xml->createElement('path');
    $l->setAttribute('d', 'm 10,9 -2,2 -2,-2');
    $g->appendChild($l);
    
    $def->appendChild($g);
    
    // up arrow
    
    $g = $this->xml->createElement('g');
    $g->setAttribute('id', 'Uarrow');
    $p = $this->xml->createElement('path');
    $p->setAttribute('d', 'm 8,1 -7,7 2,2 3,-3 0,8 2,-2 2,2 0,-8 3,3 2,-2 z');
    $g->appendChild($p);
    
    $l = $this->xml->createElement('path');
    $l->setAttribute('d', 'm 6,11 2,-2 2,2');
    $g->appendChild($l);
    
    $l = $this->xml->createElement('path');
    $l->setAttribute('d', 'm 6,7 2,-2 2,2');
    $g->appendChild($l);
    
    $def->appendChild($g);
    
    // right arrow
    
    $g = $this->xml->createElement('g');
    $g->setAttribute('id', 'Rarrow');
    $p = $this->xml->createElement('path');
    $p->setAttribute('d', 'm 15,8 -7,-7 -2,2 3,3 -8,0 2,2 -2,2 8,0 -3,3 2,2 z');
    $g->appendChild($p);
    
    $l = $this->xml->createElement('path');
    $l->setAttribute('d', 'm 5,6 2,2 -2,2');
    $g->appendChild($l);
    
    $l = $this->xml->createElement('path');
    $l->setAttribute('d', 'm 9,6 2,2 -2,2');
    $g->appendChild($l);
    
    $def->appendChild($g);
    
    // mine
    
    $g = $this->xml->createElement('mine');
    $g->setAttribute('id', 'mine');
    
    foreach (array(7, 3.5) as $r)
    {
      $c = $this->xml->createElement('circle');
      $c->setAttribute('cx', 8);
      $c->setAttribute('cy', 8);
      $c->setAttribute('r', $r);
      $g->appendChild($c);
    }
    $def->appendChild($g);
    
    foreach (array("hold", "roll") as $t)
    {
      $g = $this->xml->createElement('g');
      $g->setAttribute('id', $t . '_bdy');
      $r = $this->xml->createElement('rect');
      $r->setAttribute('x', 1);
      $r->setAttribute('y', 0);
      $r->setAttribute('width', 14);
      $r->setAttribute('height', 16);
      $g->appendChild($r);
      
      foreach (array(1, 15) as $x)
      {
        $l = $this->xml->createElement('line');
        $l->setAttribute('x1', $x);
        $l->setAttribute('y1', 0);
        $l->setAttribute('x2', $x);
        $l->setAttribute('y2', 16);
        $g->appendChild($l);
      }
      $def->appendChild($g);
      $g = $this->xml->createElement('g');
      $g->setAttribute('id', $t . '_end');
      $p = $this->xml->createElement('path');
      $p->setAttribute('d', 'm 1,0 v 13 c 0,0 0,2 2,2 h 10 c 0,0 2,0 2,-2 v -13');
      $g->appendChild($p);
      $def->appendChild($g);
    }
    
    return $def;
  }
  
  public function genChart($notedata)
  {
    $measures = count($notedata['notes'][0]);
    $this->genXMLHeader($measures);
    $this->genEditHeader($notedata);
    $this->genMeasures($measures);
    if ($this->showbpm) $this->genBPM($notedata['id']);
    if ($this->showstop) $this->genStop($notedata['id']);
    $this->genArrows($notedata['notes'], $notedata['style']);
    return $this->xml;
  }
}
