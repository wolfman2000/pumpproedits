<?php
require "EditCharter.php";
class SongCharter extends EditCharter
{
	function __construct($params)
	{
		parent::__construct($params);
		$this->arcade = 1;
	}
	
	protected function genXMLHeader($measures, $notedata)
	{
		// Take advantage of the header already in play.
		parent::genXMLHeader($measures, $notedata);
		
		$str = "Arcade ${notedata['style']} chart — Pump Pro Edits";
		$txt = $this->xml->createTextNode($str);
		$node = $this->xml->getElementById("headTitle");
		$node->replaceChild($txt, $node->firstChild);
	}
	
	protected function genEditHeader($nd)
	{
		parent::genEditHeader($nd);
		
		$str = sprintf("%s %s - %d", $nd['sname'], $nd['title'], $nd['diff']);
		$txt = $this->xml->createTextNode($str);		
		$node = $this->xml->getElementById("editHead");
		$node->replaceChild($txt, $node->firstChild);
	}
  
  protected function genArrows($notes, $style = "single")
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
    
    $arrows = $this->prepArrows($style === "routine" ? $ucounter : false);
    $rCheck = ($style === "routine" ? "P" . $ucounter : '');

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
    $this->genXMLHeader($measures, $notedata);
    $this->genEditHeader($notedata);
    $this->genMeasures($measures);
    if ($this->showbpm) $this->genBPM($notedata['id']);
    if ($this->showstop) $this->genStop($notedata['id']);
    $this->genArrows($notedata['notes'], $notedata['style']);
    return $this->xml;
  }
}
