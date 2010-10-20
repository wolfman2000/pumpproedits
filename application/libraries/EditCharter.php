<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class EditCharter
{
	function __construct($params)
	{
		$this->CI =& get_instance();
		$this->CI->load->model('ppe_song_bpm');
		$this->CI->load->model('ppe_song_stop');
		$this->CI->load->model('ppe_note_style');
		$this->CI->load->model('ppe_note_skin');
		$this->CI->load->library('SVGMaker');
		
		$this->eid = (array_key_exists('eid', $params) ? $params['eid'] : null);
		$tmp = array(APP_CHART_SIN_COLS, APP_CHART_DBL_COLS, APP_CHART_HDB_COLS);
		
		if (!in_array($params['cols'], $tmp))
		{
			$e = sprintf("There must be either %d, %d, or %d columns in the chart!",
				APP_CHART_SIN_COLS, APP_CHART_HDB_COLS, APP_CHART_DBL_COLS);
			throw new Exception($e);
		}
		
		$kinds = $this->CI->ppe_note_style->getNoteStyles(1);
		if (!in_array($params['kind'], $kinds))
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
		# What noteskin is being requested?
		if (array_key_exists('noteskin', $params) and $params['noteskin'])
		{
			$this->noteskin = $params['noteskin'];
		}
		else
		{
			$this->noteskin = 'arcade';
		}
		
		if (!in_array($this->noteskin, $this->CI->ppe_note_skin->getNoteSkins(1)))
		{
			$this->noteskin = 'original'; # don't feel this should error out.
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
	
	protected function genXMLHeader($measures, $nd)
	{
		// Place the surrounding HTML in first.
		$html = $this->xml->createElement('html');
		$html->setAttribute('xmlns', 'http://www.w3.org/1999/xhtml');
		$head = $this->xml->createElement('head');
		$title = $this->xml->createElement('title');
		$title->setAttribute('xml:id', 'headTitle');
		$str = sprintf("%s's %s edit “%s” (%d) for %s — Pump Pro Edits",
			$nd['author'], ucfirst($nd['style']), $nd['title'],
			$nd['diff'], $nd['sname']);
		$title->appendChild($this->xml->createTextNode($str));
		$link = $this->xml->createElement('link');
		$link->setAttribute('type', 'text/css');
		$link->setAttribute('rel', 'stylesheet');
		$link->setAttribute('href', sprintf('/css/svg/%s.css', $this->noteskin));
		$head->appendChild($title);
		$head->appendChild($link);
		$html->appendChild($head);
		$body = $this->xml->createElement('body');
		$body->setAttribute('xml:id', 'body');
		
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
		$defStr = $this->CI->load->file(HELPERPATH . 
				sprintf("svg/%s.svg", $this->noteskin), true);
		$defs->loadXML($defStr);
		
		$svg->appendChild($this->xml->importNode($defs->firstChild, true));
		
		$this->xml->appendChild($html);
		
		$g = $this->xml->createElement('g');
		$g->setAttribute('transform', "scale($this->scale)");
		$svg->appendChild($g);
		
		$this->svg = $g; # Will be used for arrow placements.
		
	}
	
	protected function genMeasures($measures)
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
				$tmp = $this->CI->svgmaker->genUse($x / $sx, $y, 
					array('href' => "measure", 'transform' => "scale($sx 1)"));
				$use = $this->xml->importNode($tmp);
				$m->appendChild($use);
			}
		}
		$this->svg->appendChild($m);
	}
	
	protected function genEditHeader($nd)
	{
		$lbuff = $this->lb;
		$sm = $this->CI->svgmaker;
		$g = $this->xml->createElement('g');
		$g->setAttribute('id', 'svgHead');
		
		$options = array("id" => "editHead");
		$isR = ($nd['style'] === "routine");
		
		$g->appendChild($this->xml->importNode($sm->genText($lbuff, 16, 
			sprintf("%s %s Edit: %s - %d",
				$nd['sname'], ucfirst($nd['style']), $nd['title'], 
				$nd['diff']), $options), true));
		
		$g->appendChild($this->xml->importNode($sm->genText($lbuff, 32, 
			$nd['author']), true));
		
		$g->appendChild($this->xml->importNode($sm->genText($lbuff, 64,
			"Steps: " . $nd['ysteps'] . ($isR ? "/" .$nd['msteps'] : "")), true));
		$g->appendChild($this->xml->importNode($sm->genText($lbuff, 80,
			"Jumps: " . $nd['yjumps'] . ($isR ? "/" .$nd['mjumps'] : "")), true));
		
		$w = $this->cw + $lbuff + $this->rb;
		
		$g->appendChild($this->xml->importNode($sm->genText($lbuff + $w * 1, 64,
			"Holds: " . $nd['yholds'] . ($isR ? "/" .$nd['mholds'] : "")), true));
		$g->appendChild($this->xml->importNode($sm->genText($lbuff + $w * 1, 80,
			"Mines: " . $nd['ymines'] . ($isR ? "/" .$nd['mmines'] : "")), true));
		$g->appendChild($this->xml->importNode($sm->genText($lbuff + $w * 2, 64,
			"Trips: " . $nd['ytrips'] . ($isR ? "/" .$nd['mtrips'] : "")), true));
		$g->appendChild($this->xml->importNode($sm->genText($lbuff + $w * 2, 80,
			"Rolls: " . $nd['yrolls'] . ($isR ? "/" .$nd['mrolls'] : "")), true));
		$g->appendChild($this->xml->importNode($sm->genText($lbuff + $w * 3, 64,
			"Lifts: " . $nd['ylifts'] . ($isR ? "/" .$nd['mlifts'] : "")), true));
		$g->appendChild($this->xml->importNode($sm->genText($lbuff + $w * 3, 80,
			"Fakes: " . $nd['yfakes'] . ($isR ? "/" .$nd['mfakes'] : "")), true));
		$this->svg->appendChild($g);
	}
	
	protected function getBPMData($id)
	{
		return $this->CI->ppe_song_bpm->getBPMsByEditID($id);
	}
	
	protected function genBPM($id)
	{
		$buff = $this->lb + $this->rb;
		$draw = $this->cols * $this->aw / 2;
		$m = $this->aw * $this->bm * $this->speedmod;
		$g = $this->xml->createElement('g');
		$g->setAttribute('id', 'svgBPMs');
		$sm = $this->CI->svgmaker;
		
		foreach ($this->getBPMData($id) as $b)
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
	
	protected function getStopData($id)
	{
		return $this->CI->ppe_song_stop->getStopsByEditID($id);
	}
	
	protected function genStop($id)
	{
		$buff = $this->lb + $this->rb;
		$draw = $this->cols * $this->aw / 2;
		$m = $this->aw * $this->bm * $this->speedmod;
		$g = $this->xml->createElement('g');
		$g->setAttribute('id', 'svgStop');
		$sm = $this->CI->svgmaker;
		foreach ($this->getStopData($id) as $b)
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
	
	protected function getAllNotes()
	{
		$this->CI->load->model('ppe_edit_measure');
		return $this->CI->ppe_edit_measure->getNotes($this->eid)->result_array();
	}
	
	protected function prepArrows($counter = false)
	{
		$pre = ($counter === false ? '' : 'P' . $counter);
		$ret = array();
		$div = array('4th', '8th', '12th', '16th', 
			'24th', '32nd', '48th', '64th', '192nd');
		foreach ($div as $d)
		{
			switch($this->kind)
			{
			case "classic":
				{
					$dl = array('a' => "$pre%sDL", 'c' => 'note_004');
					$ul = array('a' => "$pre%sUL", 'c' => 'note_008');
					$cn = array('a' => "$pre%sCN", 'c' => 'note_016');
					$ur = array('a' => "$pre%sUR", 'c' => 'note_008');
					$dr = array('a' => "$pre%sDR", 'c' => 'note_004');
					break;
				}
			case "flat":
				{
					$g = 'note_00' . (array_key_exists('red4', $this) ? '8' : '4');
					$dl = array('a' => "$pre%sDL", 'c' => $g);
					$ul = array('a' => "$pre%sUL", 'c' => $g);
					$cn = array('a' => "$pre%sCN", 'c' => $g);
					$ur = array('a' => "$pre%sUR", 'c' => $g);
					$dr = array('a' => "$pre%sDR", 'c' => $g);
					break;
				}
			case "rhythm":
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
					break;
				}
			}
			
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
	
	protected function genArrows($style = "single")
	{
		$this->CI->load->model('ppe_edit_measure');
		for ($i = 0; $i < $this->cols; $i++)
		{
			$holds[] = array('on' => false, 'hold' => true, 
				'x' => 0, 'y' => 0, 'beat' => 0);
		}
		$w = $this->cw + $this->lb + $this->rb; # width + buffers.
		$m = $this->aw * $this->bm * $this->speedmod; # height of measure block
		
		$sm = $this->CI->svgmaker;
		$nt = $this->xml->createElement('g');
		$nt->setAttribute('id', 'svgNote');
		
		$arrows = $this->prepArrows();
		
		$allNotes = $this->getAllNotes();
		
		foreach ($allNotes as $note):
		
		$player = $note['player'];
		$measure = $note['measure'];
		$beat = $note['beat'];
		$column = $note['column'];
		$symbol = $note['symbol'];
		
		$nx = (intval($measure / $this->mpcol) * $w) + $column * $this->aw + $this->lb;
		$ny = $this->headheight + (($measure % $this->mpcol) * $m + $beat * $m / 192);
		
		$arow = $arrows[$this->getBeat($beat)];
		
		$arr = ($style === "routine" ? "P" . $player : '') . $arow[$column]['a'];
		
		switch ($symbol)
		{
			case "1": # Tap note. Just add to the chart.
			{
				$opt = array('href' => sprintf($arr, 'arrow'), 'class' => $arow[$column]['c']);
				$nt->appendChild($this->xml->importNode($sm->genUse($nx, $ny, $opt)));
				break;
			}
			case "2": case "4": # Start of hold/roll. Minor differences.
			{
				$holds[$column]['on'] = true;
				$holds[$column]['roll'] = $symbol == "2" ? false : true;
				$holds[$column]['x'] = $nx;
				$holds[$column]['y'] = $ny;
				$holds[$column]['beat'] = $arow;
				break;
			}
			case "3": # End of hold/roll. VERY complicated!
			{
				if ($holds[$column]['on'])
				{
					$id = $holds[$column]['roll'] ? "roll" : "hold";
					$bod = "{$id}Body";
					$end = "{$id}End";
					$a = $holds[$column]['beat'][$column];
					
					$ox = $holds[$column]['x'];
					$oy = $holds[$column]['y'];
					
					# First: check if tap note was on previous column.
					if ($holds[$column]['x'] < $nx)
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
						$opt = array('href' => sprintf($arr, $end), 'class' => $arow[$column]['c']);
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
						$opt = array('href' => sprintf($arr, $end), 'class' => $arow[$column]['c']);
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
				$holds[$column]['on'] = false;
				$opt = array('href' => sprintf($arr, 'mine'), 'class' => $arow[$column]['c']);
				$nt->appendChild($this->xml->importNode($sm->genUse($nx, $ny, $opt)));
				break;
			}
			case "L": # Lift note. Officially, pulsing tap note.
			{
				$holds[$column]['on'] = false;
				$opt = array('href' => sprintf($arr, 'lift'), 'class' => $arow[$column]['c']);
				$nt->appendChild($this->xml->importNode($sm->genUse($nx, $ny, $opt)));
				break;
			}
			case "F": # Fake note. Officially in Pro 2.
			{
				$holds[$column]['on'] = false;
				$opt = array('href' => sprintf($arr, 'fake'), 'class' => $arow[$column]['c']);
				$nt->appendChild($this->xml->importNode($sm->genUse($nx, $ny, $opt)));
				break;
			}
		}
		
		endforeach;
		$this->svg->appendChild($nt);
	}
	
	protected function getMeasureCount()
	{
		return $this->CI->ppe_edit_edit->getMeasureCount($this->eid);
	}
	
	protected function addScripts()
	{
		$body = $this->xml->getElementById("body");
		
		$jq = $this->xml->createElement('script');
		$jq->setAttribute('type', 'text/javascript');
		$jq->setAttribute('src', JQUERY_GOOGLE);
		$body->appendChild($jq);
		
		$js = $this->xml->createElement('script');
		$js->setAttribute('type', 'text/javascript');
		$js->setAttribute('src', '/js/jquery.svg.js');
		$body->appendChild($js);
		
		$jd = $this->xml->createElement('script');
		$jd->setAttribute('type', 'text/javascript');
		$jd->setAttribute('src', '/js/jquery.svgdom.js');
		$body->appendChild($jd);
		
		$ja = $this->xml->createElement('script');
		$ja->setAttribute('type', 'text/javascript');
		$ja->setAttribute('src', '/js/jquery.svganim.js');
		$body->appendChild($ja);
		
		$jt = $this->xml->createElement('script');
		$jt->setAttribute('type', 'text/javascript');
		$jt->setAttribute('src', '/js/jquery.ba-dotimeout.js');
		$body->appendChild($jt);
		
		$jn = $this->xml->createElement('script');
		$jn->setAttribute('type', 'text/javascript');
		$jn->setAttribute('src', sprintf('/js/svg/%s.js', $this->noteskin));
		$body->appendChild($jn);
	}
	
	public function genChart($notedata)
	{
		$measures = $this->getMeasureCount();
		$this->genXMLHeader($measures, $notedata);
		$this->genEditHeader($notedata);
		$this->genMeasures($measures);
		if ($this->showbpm) $this->genBPM($notedata['id']);
		if ($this->showstop) $this->genStop($notedata['id']);
		$this->genArrows($notedata['style']);
		$this->addScripts();
		return $this->xml;
	}
}
