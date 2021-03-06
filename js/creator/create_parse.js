/*
 * This file deals with parsing the SVG file to gather stats,
 * load charts, and save/upload charts.
 */
/*
JS file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

// Load the data from database JSON to SVG.
function loadDatabaseChart(nd)
{
	if (!nd) { return; }
	$("#notes > g").children().remove(); // clear the old chart.
	loadSVGMeasures();
	
	for (var i = 0; i < nd.length; i++)
	{
		var mul = parseInt(nd[i]['beat']);
		var pl = "S";
		if ($("#stylelist").val() === "routine") pl = nd[i]['player'];
		var note = "p" + pl + " " + getSync(mul) + " " + nd[i]['note'];
		var c = parseInt(nd[i]['column']);
		var x = c * ARR_HEIGHT + BUFF_LFT;
		var m = parseInt(nd[i]['measure']);
		var y = ((m * BEATS_MAX + mul) / MEASURE_RATIO) + BUFF_TOP;
		$("#svgNote").append(selectArrow(c, x, y, note));
	}
}

// Load the data from textarea JSON to JS/SVG.
function loadChart(nd, beats)
{
  if (!nd) { return; }
  $("#notes > g").children().remove(); // remove the old chart.
  $("#svg").attr('width', SCALE * (BUFF_LFT + BUFF_RHT + columns * ARR_HEIGHT));
  
  loadSVGMeasures();
  
  
  for (var i = 0; i < nd.length; i++)
  {
		var m = parseInt(nd[i]['measure']);
		var p = nd[i]['player'];
		var mul = parseInt(nd[i]['row']) * 192 / beats[p][m];
		var pl = "S";
		if ($("#stylelist").val() === "routine") pl = p;
		var note = "p" + pl + " " + getSync(mul) + " " + nd[i]['kind'];
		var c = parseInt(nd[i]['column']);
		var x = c * ARR_HEIGHT + BUFF_LFT;
		var y = ((m * BEATS_MAX + mul) / MEASURE_RATIO) + BUFF_TOP;
		$("#svgNote").append(selectArrow(c, x, y, note));
  }
}

// Load the measures and other related data for the SVG chart.
function loadSVGMeasures()
{
  $("#notes > g").hide();
  // append the measures.
  for (var i = 0; i < songData.measures; i++)
  {
    $("#svgMeas").append(genMeasure(BUFF_LFT, BUFF_TOP + ARR_HEIGHT * BEATS_PER_MEASURE * i, i + 1));
  }
  var tmp;
  // place the BPM data.
  var bpms = songData.bpms;
  var x = parseInt($("#svg").attr('width')) / 2 / SCALE;
  var y;
  for (var i = 0; i < bpms.length; i++)
  {
    y = BUFF_TOP + bpms[i].beat * ARR_HEIGHT;
    if (isNaN(bpms[i].bpm))
    {
    	tmp = '???';
    }
    else
    {
    	tmp = Number(bpms[i].bpm);
    }
    $("#svgSync").append(genText(BUFF_LFT + columns * ARR_HEIGHT + 2 * SCALE,
        y + SCALE, tmp, 'bpm'));
    $("#svgSync").append(genLine(x, y, x + columns * ARR_HEIGHT / 2, y, 'bpm'));
  }
  // place the Stop data.
  var stps = songData.stps;
  for (var i = 0; i < stps.length; i++)
  {
    y = BUFF_TOP + stps[i].beat * ARR_HEIGHT;
    if (isNaN(stps[i].time))
    {
    	tmp = '???';
    }
    else
    {
    	tmp = Number(stps[i].time);
    }
    $("#svgSync").append(genText(BUFF_LFT / 2, y + SCALE, tmp, 'stop'));
    $("#svgSync").append(genLine(BUFF_LFT, y, BUFF_LFT + columns * ARR_HEIGHT / 2, y, 'stop'));
  }
  
  // place the Section data (if it's around).
  var secs = songData.secs;
  
  if (isEmpty(secs)) { $("nav .sections").hide(); _disable("nav .sections button"); }
    else
    {
      $("nav .sections").show(); _enable("nav .sections button");
      $("#sectionList").empty();
      var letter = 65;
      for (var i = 0; i < secs.length; i++)
      {
        var let = String.fromCharCode(letter + i);
        y = BUFF_TOP + secs[i].beat * ARR_HEIGHT;
        $("#svgSect").append(genText(SCALE * 2, y + SCALE, let + ")", 'sect', 'sect_' + let));
        var phrase = let + ") " + secs[i].section + " (Measure " + secs[i].measure + ")";
        var opt = "<option value=\"" + (y + SCALE) + "\">" + phrase + "</option>";
        $("#sectionList").append(opt);
      }
      if (songData.has_music)
      {
      	  _enable("#sectionMusic");
      	  $("#sectionMusic").show();
      }
      else
      {
      	  _disable("#sectionMusic");
      	  $("#sectionMusic").hide();
      }
    }
  
  $("#notes > g").show();
}

// Determine how much to increment a loop in saveChart.
function getMultiplier(row)
{
  var mul = Array(1, 2, 4, 6, 8, 12, 16, 24, 32, 48, 64, 192);
  
  MULTIPLIER:
  for (var m = 0; m < mul.length; m++)
  {
    NOTE:
    for (var i = 0; i < BEATS_MAX; i++)
    {
      if (isEmpty(row[i])) { continue NOTE; }
      if (i % (BEATS_MAX / mul[m]) > 0) { continue MULTIPLIER; }
    }
    return BEATS_MAX / mul[m];
  }
  return BEATS_MAX; // just in case it doesn't catch it up there.
}

// Turn the SVG data structure into the 4 layered arrays.
function SVGtoNOTES()
{
  var notes = Array();
  notes[0] = Array();
  if ($("#stylelist").val() === "routine") { notes[1] = Array() };
  
  $("#svgNote").children().each(function(ind){
    var p = getPlayerByClass($(this).attr('class'));
    var y = parseFloat($(this).attr('y')) - BUFF_TOP;
    var m = Math.floor(y * MEASURE_RATIO / BEATS_MAX);
    var b = Math.round(y * MEASURE_RATIO % BEATS_MAX);
    var x = parseFloat($(this).attr('x'));
    var c = (x - BUFF_LFT) / ARR_HEIGHT;
    var t = getTypeByClass($(this).attr('class'));
    
    if (isEmpty(notes[p][m]))     { notes[p][m]    = Array(); }
    if (isEmpty(notes[p][m][b]))  { notes[p][m][b] = Array(); }
    
    notes[p][m][b][c] = t;
  });
  return notes;
}

/*
 * Call this function for when the user wants to save the chart.
 */
function saveChart(data)
{
  var style = $("#stylelist").val();
  var title = $("#editName").val();
  var diff = $("#editDiff").val();
  var start = (songData.difficulty == "Edit" ? "#SONG:" : "#TITLE:");
  var file = start + songData.name + ";" + EOL;
  file += "#NOTES:" + EOL;
  file += "   pump-" + style + ":" + EOL;
  file += "   " + title + ":" + EOL;
  file += "   " + songData.difficulty + ":" + EOL;
  file += "   " + diff + ":" + EOL;
  var s1 = data.stream[0].toFixed(3);
  var s2 = data.stream[1].toFixed(3);
  var v1 = data.voltage[0].toFixed(3);
  var v2 = data.voltage[1].toFixed(3);
  var a1 = data.air[0].toFixed(3);
  var a2 = data.air[1].toFixed(3);
  var f1 = data.freeze[0].toFixed(3);
  var f2 = data.freeze[1].toFixed(3);
  var c1 = data.chaos[0].toFixed(3);
  var c2 = data.chaos[1].toFixed(3);
  // pretty sure this is the style of the new radar line.
  file += "   " + s1 + "," + v1 + "," + a1 + "," + f1 + "," + c1
    + "," + data.steps[0] + ',' + data.jumps[0] + ',' + data.holds[0] 
    + ',' + data.mines[0] + ',' + data.trips[0] + ',' + data.rolls[0] + ',';
  if (style !== "routine")
  {
  	  file += s1 + "," + v1 + "," + a1 + "," + f1 + "," + c1
  	  	+ "," + data.steps[0] + ',' + data.jumps[0] + ',' + data.holds[0] 
  	  	+ ',' + data.mines[0] + ',' + data.trips[0] + ',' + data.rolls[0];
  }
  else
  {
  	  file += s2 + "," + v2 + "," + a2 + "," + f2 + "," + c2
  	  	+ "," + data.steps[1] + ',' + data.jumps[1] + ',' + data.holds[1] 
  	  	+ ',' + data.mines[1] + ',' + data.trips[1] + ',' + data.rolls[1];
  }
  file += ':' + EOL + EOL;
  
  notes = SVGtoNOTES();
  
  // And now, we're at measure data.
  LOOP_PLAYER:
  for (var iP = 0; iP < 2; iP++) // for each player
  {
    if (iP)
    {
      if (style !== "routine") { break LOOP_PLAYER; }
      file += "&" + EOL;
    }
    
    LOOP_MEASURE:
    for (var iM = 0; iM < songData.measures; iM++)
    {
      file += (iM ? "," : " ") + "  // measure " + (iM + 1) + EOL;
      
      if (isEmpty(notes[iP][iM]))
      {
        file += stringMul("0", columns) + EOL;
        continue LOOP_MEASURE;
      }
      
      var mul = getMultiplier(notes[iP][iM]);
      LOOP_BEAT:
      for (var iB = 0; iB < BEATS_MAX; iB = iB + mul)
      {
        if (isEmpty(notes[iP][iM][iB]))
        {
          file += stringMul("0", columns) + EOL;
          continue LOOP_BEAT;
        }
        
        LOOP_ROW:
        for (var iR = 0; iR < columns; iR++)
        {
          var tmp = notes[iP][iM][iB][iR];
          file += (isEmpty(tmp) ? "0" : tmp);
        }
        
        file += EOL;
      }
    }
  }
  file += ";" + EOL + EOL;
  
  var allRadar = s1 + "_" + v1 + "_" + a1 + "_" + f1 + "_" + c1;
  if (style === "routine")
  {
  	  allRadar += "_" + s2 + "_" + v2 + "_" + a2 + "_" + f2 + "_" + c2;
  }
  
  $("#b64").val(file);
  $("#abbr").val(songData.abbr);
  $("#style").val(style);
  $("#radar").val(allRadar);
  $("#diff").val(diff);
  $("#title").val(title);
  $("#noteJSON").val(JSON.stringify(data.notes));
}

function genObject(p, m, b, c, n)
{
  var t = {};
  t['player'] = p + 1;
  t['measure'] = m;
  t['beat'] = b;
  t['column'] = c + 1;
  t['note'] = n;
  return t;  
}

// Convert the old style data to new data.
function convertStats(data)
{
	var ret = {};
	ret.ysteps = data.steps[0];
	ret.msteps = data.steps[1];
	ret.yjumps = data.jumps[0];
	ret.mjumps = data.jumps[1];
	ret.yholds = data.holds[0];
	ret.mholds = data.holds[1];
	ret.ymines = data.mines[0];
	ret.mmines = data.mines[1];
	ret.ytrips = data.trips[0];
	ret.mtrips = data.trips[1];
	ret.yrolls = data.rolls[0];
	ret.mrolls = data.rolls[1];
	ret.ylifts = data.lifts[0];
	ret.mlifts = data.lifts[1];
	ret.yfakes = data.fakes[0];
	ret.mfakes = data.fakes[1];
	return ret;
}

/*
 * Update the chart details to show what's going on.
 * Return the data gathered, including the points
 * that are considered invalid for the chart.
 */
function gatherStats(useRadar)
{
  var data = {};
  data.steps = Array(0, 0);
  data.jumps = Array(0, 0);
  data.holds = Array(0, 0);
  data.mines = Array(0, 0);
  data.trips = Array(0, 0);
  data.rolls = Array(0, 0);
  data.lifts = Array(0, 0);
  data.fakes = Array(0, 0);
  // radar values aren't used in Pro 2, but they may eventually.
  data.stream = Array(0, 0);
  data.voltage = Array(0, 0);
  data.air = Array(0, 0);
  data.freeze = Array(0, 0);
  data.chaos = Array(0, 0);
  // These are used to help calculate the radar values.
  data.allT = Array(0, 0);
  data.allC = Array(0, 0);
  var notes = $("#svgNote").children();
  
  // The following four are meant to be constants.
  var len = songData.duration;
  var range = ARR_HEIGHT * BEATS_PER_MEASURE * 2;
  var lastBeat = notes.last().attr('y');
  var avgBPS = lastBeat / len;

  var maxDensity = 0; // peak density of steps
  
  data.badds = Array(); // make a note of where the bad points are.
  data.notes = Array(); // keep up with the notes to eventually return.
  
  
  var holdCheck = Array();
  var stepCheck = Array();
  var numMeasures = songData.measures;
  
  var oX = -1;
  var oY = -1;
  var numSteps = Array(0, 0);
  var trueC = Array(0, 0);
  
  function checkBasics(sC, hC)
  {
    for (var k = 0; k < columns; k++)
    {
      if      (stepCheck[k]) { trueC[stepCheck[k]['player'] - 1]++; }
      else if (holdCheck[k]) { trueC[holdCheck[k]['player'] - 1]++; }
    }
    for (var playa = 0; playa < 2; playa++)
    {
      if (numSteps[playa] > 0 && trueC[playa] >= 3) { data.trips[playa]++; }
      if (numSteps[playa] >= 2)                     { data.jumps[playa]++; }
      if (numSteps[playa] > 0)                      { data.steps[playa]++; }
    }
  }
  
  notes.each(function(ind){
    var cur = $(this); // store the current node for later use.
    var css = cur.attr('class');
    var p = getPlayerByClass(css);
    var y = parseFloat(cur.attr('y')) - BUFF_TOP;
    var m = Math.floor(y * MEASURE_RATIO / BEATS_MAX);
    var b = Math.round(y * MEASURE_RATIO % BEATS_MAX);
    var x = parseFloat(cur.attr('x'));
    var c = (x - BUFF_LFT) / ARR_HEIGHT;
    var t = getTypeByClass(css);
    
    if (useRadar)
    {
      // chaotic note: doesn't matter the type, include it.
      if (css.indexOf('004') == -1 && css.indexOf('008') == -1)
      {
        data.allC[p]++;
      }
      var curDensity = 0;
      var present = cur; // Ensure a separate copy.
      var pY = parseFloat(present.attr('y'));
      while (present.length && pY < parseFloat(cur.attr('y')) + range)
      {
        pC = present.attr('class');
        pP = getPlayerByClass(pC);
        if (pP == p)
        {
          pT = getTypeByClass(pC);
          if (pT === "1" || pT === "2") { curDensity++; }
        }
        present = present.next();
        pY = parseFloat(present.attr('y'));
      }
      
      maxDensity = Math.max(maxDensity, curDensity / 8);
    }
    
    if (oY !== y) // new row
    {
      if (oY >= 0) // calculate all of the old stats first.
      {
        checkBasics(stepCheck, holdCheck);
        
        stepCheck = Array(); // reset.
        for (var i = 0; i < columns; i++) { stepCheck[i] = false; }
        numSteps = Array(0, 0);
        trueC = Array(0, 0);
      }
      oY = y;
    }
    
    if (t != "0")
    {
    	var noteObj = genObject(p, m, b, c, t);
    	data.notes.push(noteObj);
    	
    
    if (t === "1") // tap
    {
      // if tap follows hold/roll head
      if (holdCheck[c]) { data.badds.push(holdCheck[c], noteObj); }
      holdCheck[c] = false;
      stepCheck[c] = noteObj;
      numSteps[p]++;
      data.allT[p]++;
    }
    else if (t === "2") // hold
    {
      // if hold head follows hold/roll head
      if (holdCheck[c]) { data.badds.push(holdCheck[c]); }
      holdCheck[c] = noteObj;
      stepCheck[c] = noteObj;
      numSteps[p]++;
      data.holds[p]++;
      data.allT[p]++;
    }
    else if (t === "3") // hold/roll end
    {
      // if hold/roll end doesn't follow head
      if (!holdCheck[c]) { data.badds.push(noteObj); }
      holdCheck[c] = false;
      stepCheck[c] = noteObj;
    }
    else if (t === "4") // roll
    {
      // if roll head follows hold/roll head
      if (holdCheck[c]) { data.badds.push(holdCheck[c]); }
      holdCheck[c] = noteObj;
      stepCheck[c] = noteObj;
      numSteps[p]++;
      data.rolls[p]++;
    }
    else if (t === 'M') // mine
    {
      // if mine follows hold/roll head
      if (holdCheck[c]) { data.badds.push(holdCheck[c], noteObj); }
      holdCheck[c] = false;
      data.mines[p]++;
    }
    else if (t === 'L') // lift
    {
      // if lift follows hold/roll head
      if (holdCheck[c]) { badds.push(holdCheck[c], noteObj); }
      holdCheck[c] = false;
      data.lifts[p]++;
    }
    else if (t === 'F') // fake
    {
       // if fake follows hold/roll head
      if (holdCheck[c]) { data.badds.push(holdCheck[c], noteObj); }
      holdCheck[c] = false;
      data.fakes[p]++;
    }
    
    }
  });
  checkBasics(stepCheck, holdCheck);
  for (var i = 0; i < columns; i++) // if hold heads are still active
  {
    if (holdCheck[i]) { data.badds.push(holdCheck[i]) }
  }
  
  // Wrap up all of the radar data here before returning it.
  if (useRadar)
  {
    for (var i = 0; i < 2; i++)
    {
      data.stream[i] = Math.min(data.allT[i] / len / 7, 1);
      data.voltage[i] = Math.min(maxDensity * avgBPS / 10, 1);
      data.air[i] = Math.min(data.jumps[i] / len, 1);
      data.freeze[i] = Math.min(data.holds[i] / len, 1);
      data.chaos[i] = Math.min(data.allC[i] / len * .5, 1);
    }
  }
  
  return data;
}
