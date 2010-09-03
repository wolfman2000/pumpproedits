<?php
require "EditCharter.php";
class SongCharter extends EditCharter
{
	function __construct($params)
	{
		parent::__construct($params);
		$this->arcade = 1;
	}
	
	protected function genXMLHeader($measures, $style)
	{
		// Take advantage of the header already in play.
		parent::genXMLHeader($measures, $style);
		$node = $this->xml->getElementsByTagName("head")->item(0);
		
		$title = $this->xml->getElementsByTagName("title")->item(0);
		
		$link = $this->xml->getElementsByTagName("link")->item(0);
		
		$node->removeChild($title);
		
		$title = $this->xml->createElement('title');
		$text = "Arcade $style chart";
		$title->appendChild($this->xml->createTextNode($text));
		
		$node->insertBefore($title, $link);
	}
	
	protected function genEditHeader($nd)
	{
		parent::genEditHeader($nd);
		
		$str = sprintf("%s %s - %d", $nd['song'], $nd['title'], $nd['diff']);
		$txt = $this->xml->createTextNode($str);		
		$node = $this->xml->getElementById("editHead");
		$node->replaceChild($txt, $node->firstChild);
	}
  
  protected function genBPM($id)
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
  
  protected function genStop($id)
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
  
  protected function prepArrows($counter)
  {
    $pre = ($counter === false ? '' : 'P' . $counter);
    $ret = array();
    
    switch($this->kind)
    {
    	case "classic":
    	{
    		$dl = array('a' => "$pre%sDL", 'c' => 'note_004');
    		$ul = array('a' => "$pre%sUL", 'c' => 'note_008');
    		$cn = array('a' => "$pre%sCN", 'c' => 'note_016');
    		$ur = array('a' => "$pre%sUR", 'c' => 'note_008');
    		$dr = array('a' => "$pre%sDR", 'c' => 'note_004');
    		$ret = array($dl, $ul, $cn, $ur, $dr);
    		if ($this->cols == APP_CHART_DBL_COLS)
    		{
    			array_push($ret, $dl, $ul, $cn, $ur, $dr);
    		}
    		elseif ($this->cols == APP_CHART_HDB_COLS)
    		{
    			$ret = array($cn, $ur, $dr, $dl, $ul, $cn);
    		}
    		break;
    	}
	    case "flat":
    	{
    		$g = (array_key_exists('red4', $this) ? 'note_008' : 'note_004');
    		$dl = array('a' => "$pre%sDL", 'c' => $g);
			$ul = array('a' => "$pre%sUL", 'c' => $g);
			$cn = array('a' => "$pre%sCN", 'c' => $g);
			$ur = array('a' => "$pre%sUR", 'c' => $g);
			$dr = array('a' => "$pre%sDR", 'c' => $g);
			$ret = array($dl, $ul, $cn, $ur, $dr);
    		if ($this->cols == APP_CHART_DBL_COLS)
    		{
    			array_push($ret, $dl, $ul, $cn, $ur, $dr);
    		}
    		elseif ($this->cols == APP_CHART_HDB_COLS)
    		{
    			$ret = array($cn, $ur, $dr, $dl, $ul, $cn);
    		}
    		break;
    	}
	    case "rhythm":
    	{
    		$div = array('4th', '8th', '12th', '16th', '24th', '32nd', '48th', '64th', '192nd');
    		foreach ($div as $d)
    		{
    			if (array_key_exists('red4', $this))
    			{
    				if (intval($d) == 4) $g = 'note_008';
    				elseif (intval($d) == 8) $g = 'note_004';
    				else $g = sprintf('note_%03d', intval($d));
    			}
    			else $g = sprintf('note_%03d', intval($d));
    			$dl = array('a' => "$pre%sDL", 'c' => $g);
    			$ul = array('a' => "$pre%sUL", 'c' => $g);
    			$cn = array('a' => "$pre%sCN", 'c' => $g);
    			$ur = array('a' => "$pre%sUR", 'c' => $g);
    			$dr = array('a' => "$pre%sDR", 'c' => $g);
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
    		break;
    	}
	    default:
    	{
    		exit;
    	}
    }
    return $ret;
  }
  
  protected function getBeat($beat)
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
  
  protected function genArrows($notes, $style = "pump-single")
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
    $rCheck = ($style === "pump-routine" ? "P" . $ucounter : '');

    $mcounter = 0;    
    foreach ($player as $measure):
    
    $rcounter = 0;
    foreach ($measure as $row):
    
    $curbeat = intval(round($m * $rcounter / count($measure)));
    
    $arow = $this->kind == "rhythm" ? 
    $arrows[$this->getBeat(192 * $rcounter / count($measure))] : $arrows;
    
    $pcounter = 0;
    foreach (str_split($row) as $let): # For each note in the row
    
    $nx = (intval($mcounter / $this->mpcol) * $w) + $pcounter * $this->aw + $this->lb;
    $ny = $this->headheight + ($mcounter % $this->mpcol) * $m + $curbeat;
    
    
    $arr = $arow[$pcounter]['a'];
    # Stepchart part here.
    
    switch ($let)
    {
      case "1": # Tap note. Just add to the chart.
      {
        $opt = array('href' => sprintf($arr, 'arrow'), 'class' => $arow[$pcounter]['c']);
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
          $bod = "{$id}Body";
          $end = "{$id}End";
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
            
            $opt = array('href' => sprintf($arr, $bod), 'transform' => "scale(1 $sy)");
            $node = $this->xml->importNode($sm->genUse($ox, $hy / $sy, $opt));
            $nt->appendChild($node);
            
            # Place the tap.
            $opt = array('href' => sprintf($arr, $id), 'class' => $a['c']);
            $nt->appendChild($this->xml->importNode($sm->genUse($ox, $oy, $opt)));
            
            $ox += $w;
            $hy = $this->headheight;
            while ($ox < $nx)
            {
              $range = $bot - $hy;
              $sy = $range / $this->aw;
              $opt = array('href' => sprintf($arr, $bod), 'transform' => "scale(1 $sy)");
              $node = $this->xml->importNode($sm->genUse($ox, $hy / $sy, $opt));
              $nt->appendChild($node);
              $ox += $w;
            }
            # Now we're on the same column as the tail.
            $bot = $ny + $this->aw / 2;
            $range = $bot - $hy;
            $sy = $range / $this->aw;
            $opt = array('href' => sprintf($arr, $bod), 'transform' => "scale(1 $sy)");
            $node = $this->xml->importNode($sm->genUse($nx, $hy / $sy, $opt));
            $nt->appendChild($node);
            $opt = array('href' => sprintf($arr, $end), 'class' => $arow[$pcounter]['c']);
            $nt->appendChild($this->xml->importNode($sm->genUse($nx, $ny, $opt)));
          }
          else
          {
            if ($ny - $oy >= intval($this->aw / 2)) # Make this variable
            {
              $bot = $ny + $this->aw / 2;
              $hy = $oy + $this->aw / 2;
              $range = $bot - $hy;
              $sy = $range / $this->aw;
              $opt = array('href' => sprintf($arr, $bod), 'transform' => "scale(1 $sy)");
              $node = $this->xml->importNode($sm->genUse($nx, $hy / $sy, $opt));
              $nt->appendChild($node);
            }
            # Tail next
            $opt = array('href' => sprintf($arr, $end), 'class' => $arow[$pcounter]['c']);
            $node = $this->xml->importNode($sm->genUse($nx, $ny, $opt));
            $nt->appendChild($node);
            # Tap note last.
            $opt = array('href' => sprintf($arr, $id), 'class' => $a['c']);
            $nt->appendChild($this->xml->importNode($sm->genUse($ox, $oy, $opt)));
          }
        }
        break;
      }
      case "M": # Mine. Don't step on these!
      {
        $holds[$pcounter]['on'] = false;
        $opt = array('href' => sprintf($arr, 'mine'), 'class' => $arow[$pcounter]['c']);
        $nt->appendChild($this->xml->importNode($sm->genUse($nx, $ny, $opt)));
        break;
      }
      case "L": # Lift note. Can be placed in chart. No image yet.
      {
        $holds[$pcounter]['on'] = false;
        $opt = array('href' => sprintf($arr, 'lift'), 'class' => $arow[$pcounter]['c']);
        $nt->appendChild($this->xml->importNode($sm->genUse($nx, $ny, $opt)));
        break;
      }
      case "F": # Fake note. Officially in Pro 2.
      {
        $holds[$pcounter]['on'] = false;
        $opt = array('href' => sprintf($arr, 'fake'), 'class' => $arow[$pcounter]['c']);
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
