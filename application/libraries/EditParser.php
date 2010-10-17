<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class EditParser
{
  private function gen_measure($cols, $step = false, $routine = false)
  {
    $line = str_repeat("0", $cols) . "\r\n";
    $measure = str_repeat($line, 4);
    if ($step)
    {
      $measure = substr_replace($measure, "1", ($routine ? 7 : 2), 1);
    }
    return $measure;
  }

  private function gen_edit_file($kind, $name, $abbr, $measures, $duration = 90)
  {
    $fname = sprintf("base_%06d_%s.edit.gz", $abbr, ucfirst($kind));
    $eol = "\r\n";
    $loc = APPPATH . 'data/base_edits';
    $file = "";

    $file .= sprintf("#SONG:%s;%s#NOTES:%s", $name, $eol, $eol);
    $file .= sprintf("     pump-%s:%s", $kind, $eol);
    $file .= sprintf("     NameEditHere:%s", $eol);
    $file .= sprintf("     Edit:%s     10:%s     ", $eol, $eol);
    $file .= sprintf("0, 0, 0, 0, 0, %d, 0, 0, 0, 0, 0, ", $measures - 1);
    $file .= sprintf("0, 0, 0, 0, 0, %d, 0, 0, 0, 0, 0:%s%s", $measures - 1, $eol, $eol);

    $cols = $this->getCols("pump-" . $kind);
    
    $allM = $this->gen_measure($cols);
    for ($i = 2; $i <= $measures; $i++)
    {
      $allM .= sprintf(",  // measure %s%s", $i, $eol);
      $allM .= $this->gen_measure($cols, true);
    }
    
    if ($kind === "routine")
    {
      $allM .= sprintf("&  // measures 1%s", $eol, $allM);
      $allM .= $this->gen_measure($cols);
      for ($i = 2; $i <= $measures; $i++)
      {
        $allM .= sprintf(",  // measure %s%s", $i, $eol);
        $allM .= $this->gen_measure($cols, true, true);
      }
    }
    
    $allM .= sprintf(";%s", $eol);
    $file .= $allM;
    $fh = gzopen(sprintf("%s/%s", $loc, $fname), "w");
    gzwrite($fh, $file);
    gzclose($fh);
    return true;
  }

  public function generate_base($songid)
  {
    $CI =& get_instance();
    $CI->load->model('ppe_song_song');
    $base = $CI->ppe_song_song->getSongRow($songid);
    foreach (array("single", "double", "halfdouble", "routine") as $kind)
    {
      $this->gen_edit_file($kind, $base->name, $base->id, $base->measures);
    }

  }
  
  protected function getCols($style)
  {
    switch ($style)
    {
      case "pump-single": return 5;
      case "pump-double": return 10;
      case "pump-couple": return 10;
      case "pump-routine": return 10;
      case "pump-halfdouble": return 6;
      default: return 5; // Lazy right now.
    }
  }
  
  // Get the SM style difficulty based on the pump abbr.
  public function getSMDiff($pump)
  {
    switch ($pump)
    {
      case "ez": { return "Beginner"; }
      case "nr": { return "Easy"; }
      case "hr": case "fs": case "hd": case "rt": { return "Medium"; }
      case "cz": case "nm": { return "Hard"; }
      default: { return "Challenge"; } // don't know why
    }
  }
  
  public function getOfficialStyle($style, $title)
  {
    switch ($style)
    {
      case "pump-single":
      {
        switch ($title)
        {
          case "Beginner": return "Easy";
          case "Easy": return "Normal";
          case "Medium": return "Hard";
          case "Hard": return "Crazy";
        }
      }
      case "pump-double":
      {
        return $title == "Hard" ? "Nightmare" : "Freestyle";
      }
      case "pump-halfdouble": return "Halfdouble";
      case "pump-routine": case "pump-couple": return "Routine";
      default: return "Undefined"; // Lazy right now.
    }
  }
  
  /**
   * Check if this line is really blank or is a line comment.
   */
  private function checkCommentLine($line)
  {
    $line = ltrim($line);
    if (strlen($line) === 0 or strpos($line, "//") === 0)
    {
      return true;
    }
    return false;
  }
  
  protected function getOfficialAbbr($diff)
  {
    switch ($diff)
    {
      case "Easy": return "ez";
      case "Normal": return "nr";
      case "Hard": return "hr";
      case "Crazy": return "cz";
      case "Halfdouble": return "hd";
      case "Freestyle": return "fs";
      case "Nightmare": return "nm";
      case "Routine": return "rt";
      default: return "xx";
    }
  }

 /**
  * Pass a file handle, get the note data.
  * Return the notes themselves ONLY when asked.
  *
  * This code uses alternative control syntax a lot to keep indentation low.
  */
  public function get_stats($fh, $params = array())
  {
    $res = array(); # Return variables go in here.
    # Make all of these an array to allow for routine steps.
    $steps = array(0 => 0, 1 => 0);
    $jumps = array(0 => 0, 1 => 0);
    $holds = array(0 => 0, 1 => 0);
    $mines = array(0 => 0, 1 => 0);
    $trips = array(0 => 0, 1 => 0);
    $rolls = array(0 => 0, 1 => 0);
    $lifts = array(0 => 0, 1 => 0);
    $fakes = array(0 => 0, 1 => 0);

    $steps_on = array();
    $holds_on = array();
    $actve_on = array();
    $notes = array();
    $state = $diff = $cols = $measure = $songid = 0;
    $title = $song = $style = "";
    
    $allTypes = array('1' => 'tap', '2' => 'hold', '3' => 'end', '4' => 'roll',
    	'M' => 'mine', 'L' => 'lift', 'F' => 'fake');
    
    $couple = false; # Turn couple into routine.
    $CI =& get_instance();
    $CI->load->model('ppe_song_song');
    $base = $CI->ppe_song_song;
                
    if (!array_key_exists('strict_song', $params)) { $params['strict_song'] = true; }
    if (!array_key_exists('strict_edit', $params)) { $params['strict_edit'] = true; }
    if (!array_key_exists('arcade', $params)) { $params['arcade'] = false; }

    $numl = 0;
    while(!gzeof($fh)):

    $numl++;
    $line = rtrim(gzgets($fh, 1000)); // force newline.

    switch ($state):

    case 0: /* Initial state: verify first line and song title.*/
    {
      if ($this->checkCommentLine($line)) { break; }
      $key = $params['arcade'] ? "#TITLE:" : "#SONG:";
      $pos = strpos($line, $key, 0);
      if ($pos !== 0)
      {
        $s = "The first line must contain \"$key\" in it.";
        throw new Exception($s);
      }
      $pos = strpos($line, ";");
      if ($pos === false)
      {
        $s = "This line needs a semicolon at the end: %s";
        throw new Exception(sprintf($s, $line));
      }
      $line = rtrim($line, ";");
      
      $song = substr($line, strlen($key));
      
      $slash = strpos($song, "//");
      if ($slash !== false)
      {
        $song = substr($song, 0, $slash);
      }
      
      $slash = strpos($song, "/");
      while ($slash !== false)
      {
      
        $song = substr($song, $slash + 1);
        $slash = strpos($song, "/");
      }
      
      if (strpos($song, ";") !== false)
      {
        $song = substr($song, 0, strpos($song, ";"));
      }
      
      $songid = $base->getIDBySong($song);
      
      if ($params['strict_song'])
      {
        
        if (!$songid)
        {
          $s = "This song is not found in the database: %s. ";
          $s .= "Make sure you spelt it right.";
          throw new Exception(sprintf($s, $song));
        }
      }
      else
      {
        if (!$songid) $songid = -1;
      }
      $state = $params['arcade'] ? 10 : 1; # The song exists. We can move on.
      break;
    }
    case 10: /* Idle until we find a notes tag. */
    {
      if (trim($line) === "#NOTES:")
      {
        $state = 2;
      }
      break;
    }
    case 1: /* Verify NOTES tag is present next. */
    {
      if ($this->checkCommentLine($line)) { continue; }
      if (strpos($line, "#NOTES:", 0) !== 0)
      {
        $s = "The #NOTES: tag must be on line 2.";
        throw new Exception($s);
      }
      $state = 2;
      break;
    }
    case 2: /* Confirm this is a valid difficulty. */
    {
      if ($this->checkCommentLine($line)) { continue; }
      $line = ltrim($line);
      $pos = strpos($line, ":", 0);
      if ($pos === false)
      {
        $s = "This line needs a colon at the end: %s";
        throw new Exception(sprintf($s, $line));
      }
      $style = substr($line, 0, $pos - strlen($line));
      
      if ($style === "pump-couple") { $couple = true; }
      if (!in_array($style, array("pump-single", "pump-double", "pump-halfdouble", "pump-couple", "pump-routine")))
      {
        if ($params['arcade'])
        {
          $state = 10; // don't deal with extraneous modes.
          break;
        }
        else
        {
          $s = "The style %s is invalid. Use pump-single, double, halfdouble, or routine.";
          throw new Exception(sprintf($s, $style));
        }
      }
      $state = 3;
      break;
    }
    case 3: /* Get the title / author of the edit. No blank names Dread. ☻ */
    {
      if ($this->checkCommentLine($line)) { continue; }
      $line = ltrim($line);
      $pos = strpos($line, ":", 0);
      if ($pos === false)
      {
        $s = "This line needs a colon at the end: %s";
        throw new Exception(sprintf($s, $line));
      }
      
      if ($params['arcade']) // Different rules:
      {
        if ($pos)
        {
          $author = substr($line, 0, $pos - strlen($line));
        }
        else
        {
          $author = 'Someone';
        }
      }
      else
      {
        if ($pos === 0)
        {
          if ($params['strict_edit'])
          {
            $s = "Blank edit names are no longer allowed.";
            throw new Exception($s);
          }
          else
          {
            $title = "JDread Law ☻";
          }
        }
        else
        {
          $title = substr($line, 0, $pos - strlen($line));
        }
        $maxlen = APP_MAX_EDIT_NAME_LENGTH;
        $titlen = mb_strlen($title);
        if ($titlen > $maxlen and $params['strict_edit'])
        {
          $s = 'The edit titled "%s" is %d characters too long.';
          throw new Exception(sprintf($s, $title, $titlen - $maxlen));
        }
        $author = false;
      }
      $state = 4;
      break;
    }
    case 4: /* Arcade mode: get title here. Otherwise, ensure the "Edit:" line is in place. */
    {
      if ($this->checkCommentLine($line)) { continue; }
      $line = ltrim($line);
      $pos = strpos($line, ":", 0);
      if ($pos === false)
      {
        $s = "This line needs a colon at the end: %s";
        throw new Exception(sprintf($s, $line));
      }
      $line = substr($line, 0, $pos - strlen($line));
      if ($params['arcade'])
      {
        $title = $this->getOfficialStyle($style, $line); // set title now.
        $res['dShort'] = $this->getOfficialAbbr($title);
        if ($params['arcade'] !== $res['dShort'])
        {
          $state = 10;
          break;
        }
      }
      elseif ($line !== "Edit" and !$params['arcade']) // temp measure.
      {
      	$line = "Edit";
        /*
      	$s = 'The edit must have "Edit:" on a new line after the title.';
        throw new Exception($s);
        */
      }
      
      $state = 5;
      $res['difficulty'] = substr($line, 0, $pos);
      break;
    }
    case 5: /* Get the difficulty level of the edit. */
    {
      if ($this->checkCommentLine($line)) { continue; }
      $line = ltrim($line);
      $pos = strpos($line, ":", 0);
      if ($pos === false)
      {
        $s = "This line needs a colon at the end: %s";
        throw new Exception(sprintf($s, $line));
      }

      $diff = (int) $line;
      if ($diff != $line) /* Unsure of !== here. */
      {
        $s = "The difficulty must be a positive integer. You gave: %d ";
        throw new Exception(sprintf($s, $line));
      }
      $mindiff = APP_MIN_DIFFICULTY_RATING;
      $maxdiff = APP_MAX_DIFFICULTY_RATING;
      if (!($mindiff <= $diff and $diff <= $maxdiff))
      {
        $s = "The difficulty rating %d must be between %d and %d.";
        throw new Exception(sprintf($s, $diff, $mindiff, $maxdiff));
      }
      $state = 6;
      break;
    }
    case 6: /* Radar line: use this time to prep other variables. */
    {
      if ($this->checkCommentLine($line)) { continue; }
      $cols = $this->getCols($style);

      for ($dummy = 0; $dummy < $cols; $dummy++)
      {
        $holds_on[] = 0;
        $actve_on[] = 0;
        $steps_on[] = 0;
      }
      //$notes[] = array();
      //if ($couple) { $notes[] = array(); }
      $beats = array();
      $beats[] = array();
      $beats[] = array();
      //if ($style == "pump-routine") { $beats[1] == array(); }
      $state = 7;
      $side = 0; // routine compatible switch.
      $beat = 0;
      break;
    }
    case 7: /* Finally at step content. Read until ; is first. */
    {
      $line = ltrim($line);
      if (substr($line, 0, 1) === ",") /* New measure upcoming. */
      {
        $measure++;
        $beats[$side][] = $beat;
        $beat = 0;
        //$notes[$side][] = array();
        //if ($couple) { $notes[1][] = array(); }
      }
      elseif (substr($line, 0, 1) === "&") /* New routine step partner. */
      {
        $beats[$side][] = $beat;
        $beat = 0;
        $side = 1;
        $measure = 0;
      }
      elseif (substr($line, 0, 1) === ";") /* Should be EOF */
      {
        $beats[$side][] = $beat;
        if ($params['arcade']) { break 2; }
        $state = 8;
        
      }
      elseif (!$this->checkCommentLine($line)) // Parse.
      {
        $steps_per_row = array();
        $steps_per_row[] = 0;
        $steps_per_row[] = 0; // for both sides.
        $row = substr($line, 0, $cols);
        if ($couple)
        {
          //$notes[0][$measure][] = substr($row, 0, 5) . '00000';
          //$notes[1][$measure][] = '00000' . substr($row, 5, 5);
        }
        else
        {
          //$notes[$side][$measure][] = $row;
        }

        for ($i = 0; $i < $cols; $i++)
        {
          $steps_on[$i] = 0; // Reset previous info.
          if ($couple) { $steps_on[1] = 0; }
          $char = substr($row, $i, 1);
          
          if ($char != "0")
          {
          	  $notes[] = array('player' => ($couple ? (int)($i >= 5) : $side),
          	  	  'measure' => $measure,
          	  	  'column' => $i,
          	  	  'note' => $char,
          	  	  'kind' => $allTypes[$char],
          	  	  'row' => $beat);
          }
          
          switch ($char):

          case "0": // Empty space
          {
            break;
          }
          case "1": // Tap note
          {
            $holds_on[$i] = 0;
            $steps_on[$i] = 1;
            if ($couple and $i >= 5)
            {
            	$steps_per_row[1]++;
            }
            else
            {
            	$steps_per_row[$side]++;
            }
            break;
          }
          case "2": // Start of hold note
          {
            $holds_on[$i] = 1;
            $steps_on[$i] = 1;
            $steps_per_row++;
            if ($couple and $i >= 5)
            {
              $holds[1]++;
            }
            else
            {
              $holds[$side]++;
            }
            break;
          }
          case "3": // End of hold/roll note
          {
            $holds_on[$i] = 0;
            $steps_on[$i] = 1; // Triple check. (Why did I have this?)
            break;
          }
          case "4": // Start of roll note
          {
            $holds_on[$i] = 1;
            $steps_on[$i] = 1;
            $steps_per_row++;
            if ($couple and $i >= 5)
            {
              $rolls[1]++;
            }
            else
            {
              $rolls[$side]++;
            }
            break;
          }
          case "M": // Mine
          {
            $holds_on[$i] = 0;
            if ($couple and $i >= 5)
            {
              $mines[1]++;
            }
            else
            {
              $mines[$side]++;
            }
            break;
          }
          case "L": // Lift note (not fully implemented)
          {
            $holds_on[$i] = 0;
            if ($couple and $i >= 5)
            {
              $lifts[1]++;
            }
            else
            {
              $lifts[$side]++;
            }
            break;
          }
          case "F": // Fake note
          {
            $holds_on[$i] = 0;
            if ($couple and $i >= 5)
            {
              $fakes[1]++;
            }
            else
            {
              $fakes[$side]++;
            }
            break;
          }
          default: // Invalid data found.
          {
            $n = "0, 1, 2, 3, 4, M, L, F";
            $s = "Line %d has an invalid note %s. Stick with %s.";
            throw new Exception(sprintf($s, $numl, $char, $n));
          }
          endswitch;

        }
        for ($i = 0; $i < $cols; $i++)
        {
          $actve_on[$i] = ($holds_on[$i] === 1 or $steps_on[$i] === 1 ? 1 : 0);
        }

        if ($couple)
        {
          if ($steps_per_row[0] > 0 and array_sum(array_slice($actve_on, 0, 5)) >= 3)
          {
          	  $trips[0]++;
          }
          if ($steps_per_row[0] >= 2) { $jumps[0]++; }
          if ($steps_per_row[0]) { $steps[0]++; }
          if ($steps_per_row[1] > 0 and array_sum(array_slice($actve_on, 5, 5)) >= 3)
          {
          	  $trips[1]++;
          }
          if ($steps_per_row[1] >= 2) { $jumps[1]++; }
          if ($steps_per_row[1]) { $steps[1]++; }
        }
        else
        {
          if ($steps_per_row[$side] > 0 and array_sum($actve_on) >= 3) { $trips[$side]++; }
          if ($steps_per_row[$side] >= 2) { $jumps[$side]++; }
          if ($steps_per_row[$side]) { $steps[$side]++; }
        }
        $beat++;
      }
      
      break;
    }

    case 8: /* Ensure no non-comment data is after the end. */
    {
      $line = ltrim($line);
      if (!($line === "" or strpos($line, "//", 0) === 0))
      {
        $s = "Remove all data on and after line %d of your file.";
        throw new Exception(sprintf($s, $numl));
      }
      break;
    }
    
    default: /* Don't throw an error at this point. */
    {
      break;
    }
    endswitch;
    endwhile;
    
    if ($params['arcade'] and !count($notes))
    {
      throw new Exception("The chosen song / difficulty combination doesn't exist! Please choose another.");
    }
    
    $res['id'] = $songid;
    $res['sname'] = $song;
    $res['diff'] = $diff;
    $res['cols'] = $cols;
    $res['style'] = ($couple ? "routine" : substr($style, 5));
    $res['title'] = $title;
    $res['ysteps'] = $steps[0];
    $res['msteps'] = $steps[1];
    $res['yjumps'] = $jumps[0];
    $res['mjumps'] = $jumps[1];
    $res['yholds'] = $holds[0];
    $res['mholds'] = $holds[1];
    $res['ymines'] = $mines[0];
    $res['mmines'] = $mines[1];
    $res['ytrips'] = $trips[0];
    $res['mtrips'] = $trips[1];
    $res['yrolls'] = $rolls[0];
    $res['mrolls'] = $rolls[1];
    $res['ylifts'] = $lifts[0];
    $res['mlifts'] = $lifts[1];
    $res['yfakes'] = $fakes[0];
    $res['mfakes'] = $fakes[1];
    $res['author'] = $author;
    if (isset($params['notes']) and $params['notes'])
    {
      $res['beats'] = $beats;
      $res['notes'] = $notes;
    }
    return $res;
  }
}
