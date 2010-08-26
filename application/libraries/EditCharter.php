<?php

class EditCharter
{
  function __construct($params)
  {
    $this->CI =& get_instance();
    $this->CI->load->model('ppe_song_bpm');
    $this->CI->load->model('ppe_song_stop');
    $this->CI->load->library('SVGMaker');
  
    if (!in_array($params['cols'], array(APP_CHART_SIN_COLS, APP_CHART_DBL_COLS, APP_CHART_HDB_COLS)))
    {
      $e = sprintf("There must be either %d, %d, or %d columns in the chart!",
        APP_CHART_SIN_COLS, APP_CHART_HDB_COLS, APP_CHART_DBL_COLS);
      throw new Exception($e);
    }
    if (!in_array($params['kind'], array("classic", "rhythm")))
    {
      $e = "The notetype chosen is not valid!";
      throw new Exception($e);
    }
    $this->lb = APP_CHART_COLUMN_LEFT_BUFFER;
    $this->rb = APP_CHART_COLUMN_RIGHT_BUFFER;
    $this->aw = APP_CHART_ARROW_WIDTH;
    $this->bm = APP_CHART_BEAT_P_MEASURE;
    $this->kind = $params['kind'];
    
    # Have the rhythm skin use red as the quarter note if requested.
    if (array_key_exists('red4', $params) and $params['red4'] == "red")
    {
      $this->red4 = 1;
    }
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

    # What noteskin is being requested?
    if (array_key_exists('noteskin', $params) and $params['noteskin'])
    {
      $this->noteskin = $params['noteskin'];
    }
    else
    {
      $this->noteskin = 'stepmania';
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

  private function genXMLHeader($measures, $style)
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
    $link->setAttribute('href', sprintf('/css/svg/%s.css', $this->noteskin));
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

    $defs = new DOMDocument('1.0', 'utf-8');
    $defStr = $this->CI->load->file(HELPERPATH . sprintf("svg/%s.svg", $this->noteskin), true);
    $defs->loadXML($defStr);

    $svg->appendChild($this->xml->importNode($defs->firstChild, true));
    
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
    $m = $this->xml->createElement('g');
    $m->setAttribute('id', 'svgMeas');
    for ($i = 0; $i < $numcols; $i++)
    {
      $x = ($this->aw * $this->cols + $breather) * $i + $this->lb;
      $sx = $this->cols;
      for ($j = 0; $j < $this->mpcol * $spd; $j++)
      {
        $y = $beatheight * $j * $this->bm + $this->headheight;
        $tmp = $this->CI->svgmaker->genUse($x / $sx, $y, array('href' => "measure", 'transform' => "scale($sx 1)"));
        $use = $this->xml->importNode($tmp);
        $m->appendChild($use);
      }
    }
    $this->svg->appendChild($m);
  }
  
  private function genEditHeader($nd)
  {
    $lbuff = $this->lb;
    $sm = $this->CI->svgmaker;
    $g = $this->xml->createElement('g');
    $g->setAttribute('id', 'svgHead');
    
    if ($this->arcade)
    {
      $g->appendChild($this->xml->importNode($sm->genText($lbuff, 16, sprintf("%s %s - %d",
        $nd['song'], $nd['title'], $nd['diff'])), true));
    }
    else
    {
      $g->appendChild($this->xml->importNode($sm->genText($lbuff, 16, sprintf("%s %s Edit: %s - %d",
        $nd['song'], ucfirst(substr($nd['style'], 5)), $nd['title'], $nd['diff'])), true));
    }
    $g->appendChild($this->xml->importNode($sm->genText($lbuff, 32, $nd['author']), true));
    
    $g->appendChild($this->xml->importNode($sm->genText($lbuff, 64,
      "Steps: " . $nd['steps'][0] . ($nd['style'] === "pump-routine" ? "/" .$nd['steps'][1] : "")), true));
    $g->appendChild($this->xml->importNode($sm->genText($lbuff, 80,
      "Jumps: " . $nd['jumps'][0] . ($nd['style'] === "pump-routine" ? "/" .$nd['jumps'][1] : "")), true));
    
    $w = $this->cw + $lbuff + $this->rb;
    
    $g->appendChild($this->xml->importNode($sm->genText($lbuff + $w * 1, 64,
      "Holds: " . $nd['holds'][0] . ($nd['style'] === "pump-routine" ? "/" .$nd['holds'][1] : "")), true));
    $g->appendChild($this->xml->importNode($sm->genText($lbuff + $w * 1, 80,
      "Mines: " . $nd['mines'][0] . ($nd['style'] === "pump-routine" ? "/" .$nd['mines'][1] : "")), true));
    $g->appendChild($this->xml->importNode($sm->genText($lbuff + $w * 2, 64,
      "Trips: " . $nd['trips'][0] . ($nd['style'] === "pump-routine" ? "/" .$nd['trips'][1] : "")), true));
    $g->appendChild($this->xml->importNode($sm->genText($lbuff + $w * 2, 80,
      "Rolls: " . $nd['rolls'][0] . ($nd['style'] === "pump-routine" ? "/" .$nd['rolls'][1] : "")), true));
    $g->appendChild($this->xml->importNode($sm->genText($lbuff + $w * 3, 64,
      "Lifts: " . $nd['lifts'][0] . ($nd['style'] === "pump-routine" ? "/" .$nd['lifts'][1] : "")), true));
    $g->appendChild($this->xml->importNode($sm->genText($lbuff + $w * 3, 80,
      "Fakes: " . $nd['fakes'][0] . ($nd['style'] === "pump-routine" ? "/" .$nd['fakes'][1] : "")), true));
    $this->svg->appendChild($g);
  }
  
  private function genBPM($id)
  {
    $buff = $this->lb + $this->rb;
    $draw = $this->cols * $this->aw / 2;
    $m = $this->aw * $this->bm * $this->speedmod;
    $g = $this->xml->createElement('g');
    $g->setAttribute('id', 'svgBPMs');
    $sm = $this->CI->svgmaker;
    
    foreach ($this->CI->ppe_song_bpm->getBPMsBySongID($id) as $b)
    {
      $beat = $b->beat;
      $bpm = $b->bpm;
      $measure = $beat / $this->bm;
      $mpcol = $this->mpcol; # How many measures are in a column?
      $col = floor(floor($measure) / $mpcol); # Find the right column.
      $down = $measure % $mpcol + $measure - floor($measure); # Find the specific measure.
      
      $lx = ($buff + ($this->cols * $this->aw)) * $col + $this->lb;
      $ly = $down * $m + $this->headheight;
      
      $g->appendChild($this->xml->importNode($sm->genLine($lx + $draw, $ly,
        $lx + $draw + $draw, $ly, array('class' => 'bpm'))));
      
      if (isset($bpm))
      {
        $pos = strpos($bpm, ".");
        if ($pos !== false)
        {
          $bpm = trim(trim($bpm, '0'), '.');
        }
        $g->appendChild($this->xml->importNode($sm->genText($lx + $draw + $draw,
          $ly + $this->bm, $bpm, array('class' => 'bpm')), true));
      }
    }
    $this->svg->appendChild($g);
  }
  
  private function genStop($id)
  {
    $buff = $this->lb + $this->rb;
    $draw = $this->cols * $this->aw / 2;
    $m = $this->aw * $this->bm * $this->speedmod;
    $g = $this->xml->createElement('g');
    $g->setAttribute('id', 'svgStop');
    $sm = $this->CI->svgmaker;
    foreach ($this->CI->ppe_song_stop->getStopsBySongID($id) as $b)
    {
      $beat = $b->beat;
      $break = $b->break;
      $measure = $beat / $this->bm;
      $mpcol = $this->mpcol; # How many measures are in a column?
      $col = floor(floor($measure) / $mpcol); # Find the right column.
      $down = $measure % $mpcol + $measure - floor($measure); # Find the specific measure.
      
      $lx = ($buff + ($this->cols * $this->aw)) * $col + $this->lb;
      $ly = $down * $m + $this->headheight;
      
      $g->appendChild($this->xml->importNode($sm->genLine($lx, $ly,
        $lx + $draw, $ly, array('class' => 'stop'))));
      
      if (isset($break))
      {
        $break = rtrim(rtrim($break, '0'), '.') . "B";
        $break = ltrim($break, '0');
        $g->appendChild($this->xml->importNode($sm->genText($lx - $this->aw,
          $ly + $this->bm, $break, array('class' => 'stop')), true));
      }
    }
    $this->svg->appendChild($g);
  }
  
  private function prepArrows($counter)
  {
    $pre = ($counter === false ? '' : 'P' . $counter);
    if ($this->kind == "classic")
    {
      $dl = array('a' => $pre . 'arrow',  'c' => 'note_004', 't' => '');
      $ul = array('a' => $pre . 'arrow',  'c' => 'note_008', 't' => "rotate(90 %d %d)");
      $cn = array('a' => $pre . 'center', 'c' => 'note_016', 't' => '');
      $ur = array('a' => $pre . 'arrow',  'c' => 'note_008', 't' => "rotate(180 %d %d)");
      $dr = array('a' => $pre . 'arrow',  'c' => 'note_004', 't' => "rotate(270 %d %d)");
      $ret = array($dl, $ul, $cn, $ur, $dr);
      if ($this->cols == APP_CHART_DBL_COLS)
      {
        array_push($ret, $dl, $ul, $cn, $ur, $dr);
      }
      elseif ($this->cols == APP_CHART_HDB_COLS)
      {
        $ret = array($cn, $ur, $dr, $dl, $ul, $cn);
      }
      return $ret;
    }
    if ($this->kind == "rhythm")
    {
      $ret = array();
      $div = array('4th', '8th', '12th', '16th',
        '24th', '32nd', '48th', '64th', '192nd');
      foreach ($div as $d)
      {
        if (array_key_exists('red4', $this))
        {
          if (intval($d) == 4) $g = 'note_008';
          elseif (intval($d) == 8) $g = 'note_004';
          else $g = sprintf('note_%03d', intval($d));
        }
        else $g = sprintf('note_%03d', intval($d));
        $dl = array('a' => $pre . 'arrow',  'c' => $g, 't' => '');
        $ul = array('a' => $pre . 'arrow',  'c' => $g, 't' => "rotate(90 %d %d)");
        $cn = array('a' => $pre . 'center', 'c' => $g, 't' => '');
        $ur = array('a' => $pre . 'arrow',  'c' => $g, 't' => "rotate(180 %d %d)");
        $dr = array('a' => $pre . 'arrow',  'c' => $g, 't' => "rotate(270 %d %d)");
        $ret[$d] = array($dl, $ul, $cn, $ur, $dr);
        if ($this->cols == APP_CHART_DBL_COLS)
        {
          array_push($ret[$d], $dl, $ul, $cn, $ur, $dr);
        }
        elseif ($this->cols == APP_CHART_HDB_COLS)
        {
          $ret[$d] = array($cn, $ur, $dr, $dl, $ul, $cn);
        }
      }
      return $ret;
    }
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
    
    $sm = $this->CI->svgmaker;
    $nt = $this->xml->createElement('g');
    $nt->setAttribute('id', 'svgNote');
    
    $ucounter = 0;
    foreach ($notes as $player):
    
    $arrows = $this->prepArrows($style === "pump-routine" ? $ucounter : false);

    $mcounter = 0;    
    foreach ($player as $measure):
    
    $rcounter = 0;
    foreach ($measure as $row):
    
    $curbeat = intval(round($m * $rcounter / count($measure)));
      
    $arow = $this->kind == "classic" ? $arrows :
      $arrows[$this->getBeat(192 * $rcounter / count($measure))];
    
    $pcounter = 0;
    foreach (str_split($row) as $let): # For each note in the row
    
    $nx = (intval($mcounter / $this->mpcol) * $w) + $pcounter * $this->aw + $this->lb;
    $ny = $this->headheight + ($mcounter % $this->mpcol) * $m + $curbeat;
    
    # Stepchart part here.
    
    switch ($let)
    {
      case "1": # Tap note. Just add to the chart.
      {
        $opt = array('href' => $arow[$pcounter]['a'], 'class' => $arow[$pcounter]['c']);
        if ($arow[$pcounter]['a'] !== "L")
        {
          $opt['transform'] = sprintf($arow[$pcounter]['t'], $nx + 8, $ny + 8);
        }
        $nt->appendChild($this->xml->importNode($sm->genUse($nx, $ny, $opt)));
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
            
            $opt = array('href' => $bod, 'transform' => "scale(1 $sy)");
            $node = $this->xml->importNode($sm->genUse($ox, $hy / $sy, $opt));
            $nt->appendChild($node);
            
            # Place the tap.
            $opt = array('href' => $a['a'], 'class' => $a['c']);
            if ($arow[$pcounter]['a'] !== "L")
            {
              $opt['transform'] = sprintf($arow[$pcounter]['t'], $ox + 8, $oy + 8);
            }
            $nt->appendChild($this->xml->importNode($sm->genUse($ox, $oy, $opt)));
            
            $ox += $w;
            $hy = $this->headheight;
            while ($ox < $nx)
            {
              $range = $bot - $hy;
              $sy = $range / $this->aw;
              $opt = array('href' => $bod, 'transform' => "scale(1 $sy)");
              $node = $this->xml->importNode($sm->genUse($ox, $hy / $sy, $opt));
              $nt->appendChild($node);
              $ox += $w;
            }
            # Now we're on the same column as the tail.
            $bot = $ny + $this->aw / 2;
            $range = $bot - $hy;
            $sy = $range / $this->aw;
            $opt = array('href' => $bod, 'transform' => "scale(1 $sy)");
            $node = $this->xml->importNode($sm->genUse($nx, $hy / $sy, $opt));
            $nt->appendChild($node);
            $nt->appendChild($this->xml->importNode($sm->genUse($nx, $ny, array('href' => $end))));
          }
          else
          {
            if ($ny - $oy >= intval($this->aw / 2)) # Make this variable
            {
              $bot = $ny + $this->aw / 2;
              $hy = $oy + $this->aw / 2;
              $range = $bot - $hy;
              $sy = $range / $this->aw;
              $opt = array('href' => $bod, 'transform' => "scale(1 $sy)");
              $node = $this->xml->importNode($sm->genUse($nx, $hy / $sy, $opt));
              $nt->appendChild($node);
            }
            # Tail next
            $opt = array('href' => $end);
            $node = $this->xml->importNode($sm->genUse($nx, $ny, $opt));
            $nt->appendChild($node);
            # Tap note last.
            $opt = array('href' => $a['a'], 'class' => $a['c']);
            if ($arow[$pcounter]['a'] !== "L")
            {
              $opt['transform'] = sprintf($arow[$pcounter]['t'], $ox + 8, $oy + 8);
            }
            $nt->appendChild($this->xml->importNode($sm->genUse($ox, $oy, $opt)));
          }
        }
        break;
      }
      case "M": # Mine. Don't step on these!
      {
        $holds[$pcounter]['on'] = false;
        $tmp = ($style === "pump-routine" ? "P" . $ucounter: "");
        $opt = array('href' => $tmp . 'mine', 'class' => $arow[$pcounter]['c']);
        $nt->appendChild($this->xml->importNode($sm->genUse($nx, $ny, $opt)));
        break;
      }
      case "L": # Lift note. Can be placed in chart. No image yet.
      {
        $holds[$pcounter]['on'] = false;
        $tmp = ($style === "pump-routine" ? "P" . $ucounter: "");
        $opt = array('href' => $tmp . 'lift', 'class' => $arow[$pcounter]['c']);
        $nt->appendChild($this->xml->importNode($sm->genUse($nx, $ny, $opt)));
        break;
      }
      case "F": # Fake note. Officially in Pro 2.
      {
        $holds[$pcounter]['on'] = false;
        $tmp = ($style === "pump-routine" ? "P" . $ucounter: "");
        $opt = array('href' => $tmp . 'fake', 'class' => $arow[$pcounter]['c']);
        $nt->appendChild($this->xml->importNode($sm->genUse($nx, $ny, $opt)));
        break;
      }
    }
    
    $pcounter++;
    endforeach;
    
    $rcounter++;
    endforeach;
    
    $mcounter++;
    endforeach;
    
    $ucounter++;
    endforeach;
    $this->svg->appendChild($nt);
  }
  
  public function genChart($notedata)
  {
    $measures = count($notedata['notes'][0]);
    $this->genXMLHeader($measures, $notedata['style']);
    $this->genEditHeader($notedata);
    $this->genMeasures($measures);
    if ($this->showbpm) $this->genBPM($notedata['id']);
    if ($this->showstop) $this->genStop($notedata['id']);
    $this->genArrows($notedata['notes'], $notedata['style']);
    return $this->xml;
  }
}
