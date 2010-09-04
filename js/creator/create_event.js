/*
 * This file deals with all of the functions directly
 * called by the different XHTML elements.
 */
// Hide the rectangle when not in use.
function hideRect()
{
  $("#shadow").attr('x', 0).attr('y', 0).addClass('hide');
  $("#yCheck").text("???");
  $("#mCheck").text("???");
}

// Determine if a shadow square can be shown.
function checkShadow(e)
{
  // No placing arrows while loading stuff.
  if (isEmpty('songData')) { return; }
  if ($(".buttons li[class^=load]:visible").length) { return; }
  shadow(e.pageX, e.pageY, $("#m1r0").offset());
}

// Add the arrow in the appropriate position.
function changeArrow()
{
  var r = $("#shadow");
  var rX = parseInt(r.attr('x'));
  var rY = parseFloat(r.attr('y'));
  isDirty = true;
  _disable("#but_val");

  var css = getNote($("#yCheck").text()); // get the class based on the beat.
  var cX = (rX - BUFF_LFT) / ARR_HEIGHT; // which column are we using?
  
  // see if a node exists in this area.
  var coll = $("#svgNote");
  
  // add if empty
  var sA = selectArrow(cX, rX, rY, css);
  var fin = false;
  
  coll.children().each(function(ind){
    if (fin) { return; }
    var nX = parseInt($(this).attr('x'));
    var nY = parseFloat($(this).attr('y'));
    
    if (nX == rX && nY.toFixed(4) == rY.toFixed(4)) // exact same: remove old
    {
      var nStyle = $(this).attr('class');
      $("#svgNote > svg:eq(" + ind + ")").remove();
       // No point in adding the same note type again.
      if (nStyle !== css)
      {
        if ($("#svgNote").children().length === 0) { coll.append(sA); }
        else { $("#svgNote > svg:eq(" + (ind - 1) + ")").after(sA); }
      }
      fin = true;
    }
    else if (nY > rY || nY.toFixed(4) == rY.toFixed(4) && nX > rX)
    {
      $(this).before(sA);
      fin = true;
    }
  });
  if (!fin) { coll.append(sA); }
}

// Place the selection row as required.
function selectRow()
{
  var rY = parseFloat($("#shadow").attr('y'));
  if ($("#selBot").attr('style').indexOf('none') == -1)
  {
    $("rect[id^=sel]").hide();
    $("#s_mCheck").text("???");
    $("#s_yCheck").text("???");
  }
  if ($("#selTop").attr('style').indexOf('none') != -1)
  {
    $("#selTop").attr('y', rY).show();
    rY -= BUFF_TOP;
    $("#f_mCheck").text(parseInt(rY / (ARR_HEIGHT * BEATS_PER_MEASURE)) + 1);
    $("#f_yCheck").text(Math.round(rY % (ARR_HEIGHT * BEATS_PER_MEASURE) * MEASURE_RATIO));
    $("#intro").text("Select the second row, or transform the data now.");
  }
  else
  {
    $("#selBot").attr('y', rY).show();
    rY -= BUFF_TOP;
    $("#s_mCheck").text(parseInt(rY / (ARR_HEIGHT * BEATS_PER_MEASURE)) + 1);
    $("#s_yCheck").text(Math.round(rY % (ARR_HEIGHT * BEATS_PER_MEASURE) * MEASURE_RATIO));
    rY += BUFF_TOP;
    $("#intro").text("Transform the rows with the keyboard, or start again.");
    
    var tY = parseFloat($("#selTop").attr('y'));
    if (rY < tY)
    {
      $("#selBot").attr('y', tY);
      $("#selTop").attr('y', rY);
      
      $("#f_mCheck").text(parseInt((rY - BUFF_TOP) / (ARR_HEIGHT * BEATS_PER_MEASURE)) + 1);
      $("#f_yCheck").text(Math.round((rY - BUFF_TOP) % (ARR_HEIGHT * BEATS_PER_MEASURE) * MEASURE_RATIO));
      $("#s_mCheck").text(parseInt((tY - BUFF_TOP) / (ARR_HEIGHT * BEATS_PER_MEASURE)) + 1);
      $("#s_yCheck").text(Math.round((tY - BUFF_TOP) % (ARR_HEIGHT * BEATS_PER_MEASURE) * MEASURE_RATIO));
    }
  }
}

// Display the updated stats. Primarily asynchronous.
function updateStats(data)
{
  var S = data.ysteps;
  var J = data.yjumps;
  var H = data.yholds;
  var M = data.ymines;
  var T = data.ytrips;
  var R = data.yrolls;
  var L = data.ylifts;
  var F = data.yfakes;
  if ($("#stylelist").val() === "routine")
  {
    S += "/" + data.msteps;
    J += "/" + data.mjumps;
    H += "/" + data.mholds;
    M += "/" + data.mmines;
    T += "/" + data.mtrips;
    R += "/" + data.mrolls;
    L += "/" + data.mlifts;
    F += "/" + data.mfakes;
  }
  $("#statS").text(S);
  $("#statJ").text(J);
  $("#statH").text(H);
  $("#statM").text(M);
  $("#statT").text(T);
  $("#statR").text(R);
  $("#statL").text(L);
  $("#statF").text(F);

  _disable("#but_save");
  _disable("#but_sub");
  var t = $("#editName").val().length;
  if (t > 0 && t <= 12 && parseInt($("#editDiff").val()) > 0)
  {
    if (data.ysteps || data.msteps || data.ymines || data.mmines ||
        data.ylifts || data.mlifts || data.yfakes || data.mfakes)
    {
      _enable("#but_val");
      $("#intro").text("Validate your edit before saving.");
    }
    else
    {
      isDirty = false;
      $("#intro").text("You can't save empty files.");
    }
  }
  else
  {
    $("#intro").text("Provide an edit title and difficulty.");
  }
}
// The author will load an edit from the hard drive.
function loadHardDrive()
{
  $("#fCont").val('');
  $(".loadFile").show();
  $("li[class^=load]:not(.loadFile)").hide();
  _enable("li.loadFile > *");
  _disable("#but_file");
  $("#intro").text("You can load your edit now.");
}
// Load the chosen edit...or at least, load the common stuff here.
function loadEdit(data, canPublic)
{
  $(".edit").hide();
  songID = data.id;
  columns = data.cols;
  var tmp = "<option value=\"" + data.style + "\">Tmp</option>";
  $("#stylelist").append(tmp);
  $("#stylelist").val(data.style);
  $("#editDiff").val(data.diff);
  $("#editName").val(data.title);
  updateStats(data);
  $("#fCont").val('');
  $("li[class^=load]").hide();
  $("li.edit").show();
  editMode(canPublic);
  $("#intro").text("Loading chart...");
  if (data.notes) { loadChart(data.notes); }
}

// Cancel the edit loading process, restoring the normal buttons.
function cancelLoad()
{
  $("nav li[class^=load]").hide();
  $("#fCont").val('');
  $("li.edit").show();
  _enable("#but_load");
  if (!$("#stylelist").val().length) { $(".choose").show(); }
}

// Enter this mode of an admin edits an official song.
// Similar to EditMode, but separation is needed.
function songMode()
{
  $("#intro").text("Loading chart...");
  $("li[class^=load]").hide();
  _disable("#authorlist");
  songID = $("#loadSong").val();
  var diff = $("#loadDifficulty").val();
  $("#notes > g").children().remove(); // remove the old chart.
  $(".edit").hide();
  $("#editName").val('');
  $("#editDiff").val('');
  $("#statS").text(0);
  $("#statJ").text(0);
  $("#statH").text(0);
  $("#statM").text(0);
  $("#statR").text(0);
  $("#statT").text(0);
  $("#statF").text(0);
  $("#statL").text(0);
  $("#fCont").val('');
  $("#editName").attr('maxlength', 32);
  $("#editSong").text("Edit Author:");
  $("#but_sub").attr('name', 'songSubmit');
  
  $.getJSON(baseURL + "/loadOfficial/" + songID + "/" + diff, function(data){
    $("#intro").text("Loading chart...");
    $(".author").hide();
    $("#stylelist").val(data.style);
    
    songData = data;
    measures = songData['measures'];
    $("#scalelist").val(2.5);
    captured = false;
    columns = getCols();
    $("rect[id^=sel]").attr('width', columns * ARR_HEIGHT).hide();
    fixScale(2.5, 600);
    
    $("#tabNav a").filter(':first').click();
    $("#navEditTransform span[id$=Check]").text("???");
    $("nav dt.edit").show();
    $("nav dd.edit").show();
    
    $("nav *.choose").hide();
    if ($("#stylelist").val() !== "routine") { $("nav .routine").hide(); }
    else { $("nav .routine").show(); }
    var phrase = songData.name + " " + data.title;
    $("h2").first().text(phrase);
    $("title").text("Editing " + phrase + " — Pump Pro Edits");
    _enable("#but_new");
    _enable("#editName");
    _enable("#but_load");
    $("#editName").val(data.author);
    $("#editDiff").val(data.diff);
    
    loadChart(data.notes);
    if (data.notes) { updateStats(data); }
    
    isDirty = false;
    clipboard = null;
    $("li.edit").show();
    $("#intro").text("All loaded up!");
  });
}

//Enter this mode upon choosing a song and difficulty.
function editMode(canPublic)
{
  $("#intro").text("Loading song data...");
  $.ajax({ async: false, dataType: 'json', url: baseURL + '/song/' + songID, success: function(data)
  {
    songData = data;
    measures = songData.measures;
    $("#scalelist").val(2.5);
    captured = false;
    columns = getCols();
    $("rect[id^=sel]").attr('width', columns * ARR_HEIGHT).hide();
    fixScale(2.5, 600);
    
    loadSVGMeasures();
    
    $("#tabNav a").filter(':first').click();
    $("#navEditTransform span[id$=Check]").text("???");
    $("nav dt.edit").show();
    $("nav dd.edit").show();
    $("nav *.choose").hide();
    if ($("#stylelist").val() !== "routine") { $("nav .routine").hide(); }
    else { $("nav .routine").show(); }
    var phrase = songData.name + " " + $("#stylelist").val().capitalize();
    $("h2").first().text(phrase);
    $("title").text("Editing " + phrase + " — Pump Pro Edits");
    _enable("#but_new");
    _enable("#editName");
    
    if (!authed)
    {
      $(".author").hide();
    }
    else
    {
      if (andamiro) { $(".author").show(); _enable("#authorlist"); }
      else          { $(".author").hide(); _disable("#authorlist"); }
      $("#authorlist").val(0);
      authID = authed;
      if (canPublic)
      {
        $("li.author:eq(0)").next().andSelf().hide();
        $("li.author:eq(2)").next().andSelf().show();
      }
    }
    
    clipboard = null;
    _enable("#but_load");
    $("#editName").attr('maxlength', 12);
    $("#editSong").text("Edit Name:");
    $("#but_sub").attr('name', 'editSubmit');
    return true;
  }});
  return false; // this is to ensure the asyncing is done right.
}


// Load up this data on new.
function init()
{
  captured = false;
  clipboard = null;
  songData = null;
  measures = 3; // temp variable.
  columns = 5; // reasonable default.
  $("article").css('height', '50em');
  fixScale(2, 1000,
    5 * ARR_HEIGHT * SCALE + BUFF_LFT + BUFF_RHT,
    ADJUST_SIZE * BEATS_PER_MEASURE * 3 + BUFF_TOP + BUFF_BOT);
  $("title").text("Edit Creator — Pump Pro Edits");
  $("h2").first().text("Edit Creator");
  
  $("nav dt.edit").hide();
  $("nav dd.edit").hide();
  $("nav li[class^=load]").hide();
  $(".loadOther").hide();
  $("#selTop").hide();
  $("#selBot").hide();
  $("#shadow").addClass('hide');
  $("nav *.choose").show();
  _disable("#stylelist");
  _disable("#but_sub");
  _disable("#but_save");
  _disable("#but_val");
  _disable("#but_new");
  _enable("#cho_file");
  if (authed > 0) { _enable("#cho_site"); }
  else            { _disable("#cho_site"); }
  
  // reset the drop downs (and corresponding variables) to default values.
  $("#songlist").val('');
  $("#stylelist").val('');
  $("#scalelist").val(2.5);
  $("#quanlist").val(4);
  $("#typelist").val(1);
  $("#playerlist").val(0);
  $("#modelist").val(0);
  $("#editName").val('');
  $("#editDiff").val('');
  editID = 0;
  selMode = 0;

  $("#notes g[id^=svg]").empty();
  
  $("#intro").text("Select your action.");
  
  isDirty = false;
  _enable("#but_load");
  _enable("#songlist");
  
  $("#loadDifficulty").val("");
  $("#loadSong").val("");
  _disable("#song_yes");
}

// Dynamically adjust the scale as needed.
function fixScale(num, len, w, h)
{
  // Round elements to the nearest 10 for easier calculations later.
  function round10(n)
  {
    n = Math.round(n);
    while (n % 10)
    {
      n = n + 1;
    }
    return n;
  }
  if (!len) { var len = 1000; }
  SCALE = num;
  ADJUST_SIZE = ARR_HEIGHT * SCALE;
  MEASURE_HEIGHT = ADJUST_SIZE * BEATS_PER_MEASURE;
  if (!h) { var h = SCALE * (ARR_HEIGHT * BEATS_PER_MEASURE * measures + BUFF_TOP + BUFF_BOT); }
  if (!w) { var w = SCALE * ((BUFF_LFT + BUFF_RHT) + columns * ARR_HEIGHT); }
  
  
  $("#svg").animate({
//    left: round10($("#svg_nav").width()) + 70,
//    top: round10($("header").first().height()) + 50,
    width: w,
    height: h,
  }, len).attr("width", w).attr("height", h).css('display', 'block');
  
  
  $("#notes").attr("transform", "scale(" + SCALE + ")");
  $("article").css("height", h + 150);
}

// Swap the cursor mode as required.
function swapCursor()
{
  if (selMode == 0)
  {
    $("#intro").text("Resume placing arrows.");
    $("#selTop").hide();
    $("#selBot").hide();
    clipboard = null;
    $("#navEditTransform span[id$=Check]").text("???");
  }
    else
  {
    $("#intro").text("Select the first row.");
  }
}


function commandMove(up)
{
  if ($("#selTop").attr('style').indexOf('none') == -1)
  {
    if (up) { shiftUp(); } else { shiftDown(); }
    updateStats(gatherStats());
  }
}
// Shift the selected arrows up based on the note sync.
function shiftUp()
{
  // remove all notes that are in the way of the shifting operation.
  function removeUp(top, bot)
  {
    if (top > bot)
    {
      var tmp = top;
      top = bot;
      bot = tmp;
    }
    $("#svgNote > svg").filter(function(index){
      var y = $(this).attr('y');
      return y >= top && y < bot;
    }).remove();
  }
  
  var val = Math.floor(-parseInt($("#quanlist").val()));
  var notes = getSelectedArrows();
  var oY = parseFloat($("#selTop").attr('y'));
  var gap = BEATS_MAX / val / MEASURE_RATIO;
  var nY = oY + gap;
  removeUp(oY, nY);
  var tY = parseFloat($("#selTop").attr('y'));
  if (tY > BUFF_TOP)
  {
    var gY = tY + gap;
    $("#selTop").attr('y', (gY < BUFF_TOP ? BUFF_TOP : gY));
  }
  tY = parseFloat($("#selBot").attr('y'));
  if (tY > BUFF_TOP)
  {
    var gY = tY + gap;
    $("#selBot").attr('y', (gY < BUFF_TOP ? BUFF_TOP : gY));
  }
  for (var i = 0; i < notes.length; i++)
  {
    var csses = notes[i].getAttribute('class').split(' ');
    var lOY = parseFloat(notes[i].getAttribute('y'));
    var nOY = lOY + gap;
    notes[i].setAttribute('y', nOY);
    nOY -= BUFF_TOP;
    
    var beatM = Math.round((nOY % (ARR_HEIGHT * SCALE * BEATS_PER_MEASURE)) * MEASURE_RATIO);
    
    notes[i].setAttribute('class', csses[0] + " " + getSync(beatM) + " " + csses[2]);
    
  }
  removeUp(0, BUFF_TOP);
}
// remove all notes that are in the way of the shifting operation.
function removeDown(top, bot)
{
  if (top > bot)
  {
    var tmp = top;
    top = bot;
    bot = tmp;
  }
  $("#svgNote > svg").filter(function(index){
    var y = $(this).attr('y');
    return y > top && y <= bot;
  }).remove();
}

// Shift the selected arrows down based on the note sync.
function shiftDown()
{
  var val = Math.floor(parseInt($("#quanlist").val()));
  var notes = getSelectedArrows();
  var oY = parseFloat($("#selBot").attr('y'));
  var gap = BEATS_MAX / val / MEASURE_RATIO;
  var nY = oY + gap;
  removeDown(oY, nY);
  var sH = Math.floor($("#svg").attr('height')) / SCALE;
  var mB = sH - BUFF_BOT;
  var tY = parseFloat($("#selTop").attr('y'));
  if (tY < mB)
  {
    var gY = tY + gap;
    $("#selTop").attr('y', (gY > mB ? mB : gY));
  }
  tY = parseFloat($("#selBot").attr('y'));
  if (tY < mB)
  {
    var gY = tY + gap;
    $("#selBot").attr('y', (gY > mB ? mB : gY));
  }
  for (var i = 0; i < notes.length; i++)
  {
    var csses = notes[i].getAttribute('class').split(' ');
    var lOY = parseFloat(notes[i].getAttribute('y'));
    var nOY = lOY + gap;
    notes[i].setAttribute('y', nOY);
    nOY += BUFF_BOT;
    
    var beatM = Math.round((nOY % (ARR_HEIGHT * SCALE * BEATS_PER_MEASURE)) * MEASURE_RATIO);
    
    notes[i].setAttribute('class', csses[0] + " " + getSync(beatM) + " " + csses[2]);
    
  }
  removeDown(mB, sH);
}


function commandCut()
{
  if ($("#selTop").attr('style').indexOf('none') == -1)
  {
    cutArrows();
    $("#intro").text("Click a row to paste the notes, or swap cursor mode to delete.");
    updateStats(gatherStats());
  }
}
// Cut the arrows, and place onto the clipboard.
function cutArrows()
{
  copyArrows();
  if (clipboard == null || !clipboard.length)
  {
    clipboard = null;
    $("#intro").text("You didn't cut or copy anything.");
    return;
  }
  getSelectedArrows().each(function(){
    $(this).remove();
  });
}

function commandCopy()
{
  if ($("#selTop").attr('style').indexOf('none') == -1)
  {
    copyArrows();
    $("#intro").text("Click a row to paste the notes, or swap cursor mode to cancel.");
  }
}
// Copy the arrows, and place onto the clipboard.
function copyArrows()
{
  clipboard = getSelectedArrows().clone();
  if (clipboard == null || !clipboard.length)
  {
    clipboard = null;
    $("#intro").text("You didn't cut or copy anything.");
  }
}

function commandPaste()
{
  if (clipboard && Math.floor($("#shadow").attr('x')) >= BUFF_LFT)
  {
    pasteArrows();
    updateStats(gatherStats());
    $("#intro").text("Arrows pasted. Clipboard wiped.");
  }
}

// Paste the arrows in the clipboard.
function pasteArrows()
{
  var tY = parseFloat($("#selTop").attr('y'));
  if ($("#selBot").attr('style').indexOf('none') != -1)
  {
    $("#selBot").attr('y', tY).attr('x', BUFF_LFT).show();
  }
  var bY = parseFloat($("#selBot").attr('y'));
  var range = bY - tY; // How big is the range for copy/pasting?
  var rY = parseFloat($("#shadow").attr('y'));
  var shift = rY - tY; // How much are we changing each note?
  
  // Move the selection rectangles to their new location.
  $("#selTop").attr('y', rY);
  $("#selBot").attr('y', rY + range);
  
  // Remove what's inside the pasting location.
  getSelectedArrows().each(function(){
    $(this).remove();
  });
  
  clipboard.each(function(){
    var csses = $(this).attr('class').split(' ');
    var oY = parseFloat($(this).attr('y'));
    var nY = oY + shift;
    $(this).attr('y', nY);
    nY += BUFF_BOT;
    
    var beatM = Math.round((nY % (ARR_HEIGHT * SCALE * BEATS_PER_MEASURE)) * MEASURE_RATIO);
    
    $(this).attr('class', csses[0] + " " + getSync(beatM) + " " + csses[2]);
  });
  $("#svgNote").append(clipboard);
  var sH = Math.floor($("#svg").attr('height')) / SCALE;
  removeDown(sH - BUFF_BOT - 0.1, sH * 2 * SCALE); // ensure nothing went BELOW the measures.
  sortArrows();
  clipboard = null;
}

function commandRotate(dir)
{
  if ($("#selTop").attr('style').indexOf('none') == -1) { rotateColumn(dir); }
}
// Cycle the arrows horizontally, changing arrow orientation as needed.
function rotateColumn(val)
{
  if (!val || val < 0) { val = -ARR_HEIGHT; } else { val = ARR_HEIGHT; }
  var notes = getSelectedArrows();
  
  notes.each(function(ind){
    var x = Math.floor($(this).attr('x')) + val;
    if (x < BUFF_LFT)                              { x += columns * ARR_HEIGHT; }
    else if (x >= BUFF_LFT + columns * ARR_HEIGHT) { x -= columns * ARR_HEIGHT; }
    
    var c = (x - BUFF_LFT) / ARR_HEIGHT;
    var y = Math.floor($(this).attr('y'));
    var a = selectArrow(c, x, y, $(this).attr('class'));
    $(this).attr('x', x).empty().append(a.firstChild);
  });
  
  sortArrows();
}

function commandMirror(diag, e)
{
  if ($("#selTop").attr('style').indexOf('none') == -1)
  {
    if (!isEmpty(e)) { e.preventDefault(); }
    mirrorRows(diag);
  }
}

// Mirror the arrows across the middle point of the chart.
function mirrorRows(diagMirror)
{
  var m = (BUFF_LFT + BUFF_RHT + columns * ARR_HEIGHT) / 2;
  getSelectedArrows().each(function(ind){
    var x = Math.floor($(this).attr('x'));
    var y = Math.floor($(this).attr('y'));
    
    // Note to code improvers: find a way to NOT hardcode this as bad.
    FIX_X:
    switch (columns)
    {
      case 5:
      {
        switch (x)
        {
          case 32: { x = (diagMirror ? 80 : 96); break FIX_X; }
          case 48: { x = (diagMirror ? 96 : 80); break FIX_X; }
          case 64: { x = 64; break FIX_X; }
          case 80: { x = (diagMirror ? 32 : 48); break FIX_X; }
          case 96: { x = (diagMirror ? 48 : 32); break FIX_X; }
        }
      }
      case 6:
      {
        switch (x)
        {
          case 32:  { x = 112; break FIX_X; }
          case 48:  { x = (diagMirror ? 80 : 96);  break FIX_X; }
          case 64:  { x = (diagMirror ? 96 : 80);  break FIX_X; }
          case 80:  { x = (diagMirror ? 48 : 64);  break FIX_X; }
          case 96:  { x = (diagMirror ? 64 : 48);  break FIX_X; }
          case 112: { x = 32;  break FIX_X; }
        }
      }
      case 10:
      {
        switch (x)
        {
          case 32:  { x = (diagMirror ? 160 : 176); break FIX_X; }
          case 48:  { x = (diagMirror ? 176 : 160); break FIX_X; }
          case 64:  { x = 144; break FIX_X; }
          case 80:  { x = (diagMirror ? 112 : 128); break FIX_X; }
          case 96:  { x = (diagMirror ? 128 : 112); break FIX_X; }
          case 112: { x = (diagMirror ? 80 : 96);  break FIX_X; }
          case 128: { x = (diagMirror ? 96 : 80);  break FIX_X; }
          case 144: { x = 64;  break FIX_X; }
          case 160: { x = (diagMirror ? 32 : 48);  break FIX_X; }
          case 176: { x = (diagMirror ? 48 : 32);  break FIX_X; }
        }
      }
    }
    
    var c = (x - BUFF_LFT) / ARR_HEIGHT;
    var a = selectArrow(c, x, y, $(this).attr('class'));
    $(this).attr('x', x).empty().append(a.firstChild);
  });
  sortArrows();
}

// Load up the chosen user's songs.
function loadWebEdits(user)
{
  authID = user;
  $(".loadSite").show();
  $("li[class^=load]:not(.loadSite)").hide();
  $("#intro").text("Loading " + (user == 2 ? "Andamiro's" : "your") + " edits...");
  $("#mem_edit").empty();
  $.getJSON(baseURL + '/loadEditList/' + user, function(data)
  {
    for (var i = 0; i < data.length; i++)
    {
      var out = data[i].title + " (" + data[i].name + ") " + data[i].style.charAt(0).capitalize() + data[i].diff;
      var html = '<option id="' + data[i].id + '">' + out + '</option>';
      $("#mem_edit").append(html);
    }
    _enable("#mem_nogo");
    $("#intro").text("Choose your edit!");
  });
}

// Upload the intended official chart.
function uploadOfficial()
{
  var data = {};
  data['b64'] = $("#b64").val();
  data['songID'] = songID;
  data['dShort'] = songData.dShort;
  data['difficulty'] = songData.difficulty;
  data['style'] = $("#stylelist").val();
   $("#intro").text("Uploading chart...");
  $.post(baseURL + "/uploadOfficial", data, function(data, status)
  {
    $("#intro").text("Chart Uploaded");
    _disable("#authorlist");
  }, "json");
}

// Upload the intended edit.
function uploadEdit()
{
  var data = {};
  data['b64'] = $("#b64").val();
  data['title'] = $("#editName").val();
  data['diff'] = $("#editDiff").val();
  data['style'] = $("#stylelist").val();
  data['editID'] = editID;
  data['songID'] = songID;
  data['userID'] = authID;
  data['notes'] = $("#noteJSON").val(); // JSON'ed.
  data['public'] = $("#editPublic").val();
  data['radar'] = $("#radar").val(); // underline separator
  data['steps1'] = $("#statS").text().split("/")[0];
  data['steps2'] = $("#statS").text().split("/")[1];
  data['jumps1'] = $("#statJ").text().split("/")[0];
  data['jumps2'] = $("#statJ").text().split("/")[1];
  data['holds1'] = $("#statH").text().split("/")[0];
  data['holds2'] = $("#statH").text().split("/")[1];
  data['mines1'] = $("#statM").text().split("/")[0];
  data['mines2'] = $("#statM").text().split("/")[1];
  data['rolls1'] = $("#statR").text().split("/")[0];
  data['rolls2'] = $("#statR").text().split("/")[1];
  data['trips1'] = $("#statT").text().split("/")[0];
  data['trips2'] = $("#statT").text().split("/")[1];
  data['fakes1'] = $("#statF").text().split("/")[0];
  data['fakes2'] = $("#statF").text().split("/")[1];
  data['lifts1'] = $("#statL").text().split("/")[0];
  data['lifts2'] = $("#statL").text().split("/")[1];
  
  $("#intro").text("Uploading edit...");
  $.post(baseURL + "/upload", data, function(res, status)
  {
    if (res.result === "duplicate")
    {
      $("#intro").text("Duplicate title!");
      alert("You already have an edit titled " + data['title']
        + "\nfor a " + data['style'] + " edit of " + songData.name
        + ". Please use a different title.");
    }
    else
    {
      $("#intro").text("Edit Uploaded");
      _disable("#authorlist");
      $("li.author:eq(0)").next().andSelf().hide();
      editID = res.editid;
    }
  }, "json");
}
