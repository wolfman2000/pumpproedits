/*
 * This file deals strictly with setting up the javascript
 * actions that will be called (mostly) in create_event.js.
 */
/*
JS file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

$(document).ready(function()
{
  // Set up the portable dialog.
  $('#svg_nav').dialog({
    autoOpen: true,
    modal: false,
    resizable: false,
    width: 300,
    height: 450,
    minHeight: 400,
    position: Array(0, 50),
    open: function(){
      $(this).parents(".ui-dialog:first").find(".ui-dialog-titlebar-close").remove();
    }
  }).parent().css({position: "fixed"});
  
  $("#tabs").tabs().removeClass('ui-tabs').find(">:first-child").removeClass('ui-widget-header');

  $("button").hover(
	function(){ 
		$(this).addClass("ui-state-hover"); 
	},
	function(){ 
		$(this).removeClass("ui-state-hover"); 
	});

  init();
  $("#svg_nav_form").attr('style', 'display:block;');
  $("#songlist").val('');
  $("rect[id^=sel]").attr('x', BUFF_LFT);
  $("#notes").attr('transform', 'scale(' + SCALE + ')');
  
  // Don't show the rectangle when not in play.
  $("#svg").mouseout(function(){ hideRect(); });
  // Show the rectangle if the mouse is over a measure.
  $("#svg").mouseover(function(e){ if (songID) checkShadow(e); });
  $("#svg").mousemove(function(e){ if (songID) checkShadow(e); });
  // If the shadow rectangle is out, perform these.
  $("#svg").click(function(){
    if ($("#shadow").hasClass('hide')) return;
    if (selMode == 0) // insert mode
    {
      changeArrow();
      updateStats(convertStats(gatherStats()));
    }
    else if (clipboard) // paste mode
    {
      pasteArrows();
      $("#intro").text("Arrows pasted. Clipboard wiped.");
      updateStats(convertStats(gatherStats()));
    }
    else { selectRow(); } // select mode
  });
  
  // Work on a new file, but make sure it's saved/validated recently.
  $("#but_new").click(function(){
    $("#intro").text("Working... Working...");
    var checking = true;
    if (isDirty)
    {
      checking = confirm("You have work not validated/saved.\nAre you sure you want to start over?");
    }
    if (checking) { init(); }
  });
  
  // Load a chart from your hard drive or your Pump Pro Edits Account.
  $("#but_load").click(function(){
    $("#intro").text("Working... Working...");
    var checking = true;
    if (isDirty)
    {
      checking = confirm("You have work not validated/saved.\nAre you sure you want to load a new edit?");
    }
    if (checking)
    {
      _enable("#but_load");
      $.getJSON(baseURL + "/loadPermissions", function(data, status)
	  {
	  	  $("#web_sel").empty();
	  	  for (var i = 0; i < data.length; i++)
		{
			var out = data[i]['value'];
			var html = '<option value="' + data[i]['id'] + '">' + out + '</option>';
			$("#web_sel").append(html);
			
			if ($("#stylelist").val().length) { loadButtons(); }
	  	  else { $(".choose").slideUp(200, function(){ loadButtons(); }); }
		}
	  });
    }
  });
  
  // Provide help for those that need it.
  $("#but_help").click(function(){
    $("#intro").text("Loading help...");
    window.open(baseURL + "/help", "helpWindow",
      "status = 1, scrollbars = yes, dependent = 1, width = 400, height = 400, left = 100, top = 100");
    $("#intro").text("Help loaded!");
  });
  
  // Force all edits to be validated before saving/uploading.
  $("#but_val").click(function(){
    $("#intro").text("Validating the edit...");
    var data = gatherStats(1);
    if (!data.badds.length)
    {
      validationPassed(data);
    }
    else
    {
      $("#intro").text("Please fix your errors.");
      var ouch = "Errors were found here:\n\n";
      for (var i = 0; i < data.badds.length; i++)
      {
        ouch += "Player " + data.badds[i]['player'] + " Measure " + data.badds[i]['measure']
          + " Beat " + data.badds[i]['beat'] + " Column " + data.badds[i]['column'] + "\n";
      }
      alert(ouch);
    }
  });
  
  // The edit contents have to be placed in here due to AJAX requirements.
  $("#fCont").keyup(function(){
    if ($("#fCont").val().length)
    {
      _enable("#but_file");
    }
    else
    {
      _disable("#but_file");
    }
  });
  
  // Load the edit from the...text area, not a pure file.
  $("#but_file").click(function(){
    $("#intro").text("Loading edit...");
    $.post(baseURL + "/loadTextarea", { file: $("#fCont").val()}, function(data, status)
    {
      loadTextArea(data);
    }, "json");
  });
  
  // The author decides not to load an edit at all.
  $("button").filter(function(){ return $(this).text() == "Nevermind"; })
    .click(function(){ cancelLoad();});
  
  // save to your local hard drive
  $("#but_save").click(function(){
    $("#intro").text("Here it comes!");
  });
  
  // The author uploads the edit directly to the chosen account.
  $("#but_sub").click(function(){
    if (songData.dShort) { uploadOfficial(); }
    else                 { uploadEdit(); }
  });
  
  // The author wants to work on this song.
  $('#songlist').change(function(){
    songID = $("#songlist").val();
    if (!isNaN(songID))
    {
      $("#intro").text("Setting up styles...")
      $("#stylelist").empty();
      $.getJSON(baseURL + "/songDifficulties/" + songID, function(data, status)
      {
      	  for (var i = 0; i < data.length; i++)
      	  {
      	  	  if (i == 0)
      	  	  {
      	  	  	  $("#stylelist").append("<option value=\"\">" + data[i]["label"] + "</option>");
      	  	  }
      	  	  else
      	  	  {
      	  	  	  $("#stylelist").append("<option value=\"" + data[i]["value"]
      	  	  	  	  + "\">" + data[i]["label"] + "</option>");
      	  	  }
      	  }
      	  
        _enable("#stylelist");
        $("#intro").text("What style for today?");
      });
    }
    else { _disable("#stylelist"); }
  });

  // The author wants to work with this style.
  $("#stylelist").change(function(){
    editMode();
    $("#intro").text("Have fun editing!");
  });
  
  // The author wishes to change the edit title / name.
  $("#editName").keyup(function(){
    _disable("#but_save");
    _disable("but_sub");
    var t = $("#editName").val().length;
    if (t > 0 && t <= 12)
    {
      if (Math.floor($("#editDiff").val()) > 0)
      {
        _enable("#but_val");
        $("#intro").text("Validate your edit before saving.");
      }
    }
    else
    {
      _disable("#but_val");
      $("#intro").text("Provide an edit title and difficulty.");
    }
  });

  // The author wishes to rate the edit.
  $("#editDiff").keyup(function(){
    _disable("#but_save");
    _disable("#but_sub");
    var t = parseInt($("#editDiff").val());
    if (t > 0 && t < 100)
    {
      t = $("#editName").val().length;
      if (t > 0 && t <= 12)
      {
        _enable("#but_val");
        $("#intro").text("Validate your edit before saving.");
      }
    }
    else
    {
      _disable("#but_val");
      $("#intro").text("Provide an edit title and difficulty.");
    }
  });
  
  // The author wishes to change how zoomed in the chart is.
  $("#scalelist").change(function(){
    fixScale($("#scalelist").val());
  });

  // The author wishes to change the cursor mode to select rows of arrows.
  $("#modelist").change(function(){
    selMode = $("#modelist").val();
    swapCursor();
  });

  // The author wishes to jump to a particular section in the song.
  $("#sectionJump").click(function(){
    var let = $("#sectionList option:selected").text().substring(0, 1);
    var num = $("#sect_" + let).offset().top;
    if (navigator.userAgent.indexOf("WebKit") >= 0)
    {
      num = num + (parseFloat($("#sect_" + let).attr('y')) - SCALE) * SCALE;
    }
    $("html, body").animate({ scrollTop: num }, 2000);
  });
  
  // The author needs a reminder of what the song section sounds like.
  $("#sectionMusic").click(function(){
    var let = $("#sectionList option:selected").text().substring(0, 1);
    var path = "/create/playSound/" + songID + "/" + let;
    $("#audio").attr('src', path);
    document.getElementById('audio').load();
    document.getElementById('audio').play();
  });

  $("input").focusin(function(){ captured = true; });
  $("select").focusin(function(){ captured = true; });
  $('input').focusout(function(){ captured = false; });
  $('select').focusout(function(){ captured = false; });
  
  // The author wishes to switch to a different "tab".
  $("#tabNav a").click(function(){
    $("#allEditInfo ~ ul[id]").hide().filter(this.hash).show();
    $("#tabNav a").removeClass('selected');
    $(this).addClass('selected');
    return false;
  });
  
  // The author wishes to transform the arrows in various ways.
  $("#transformCut").click(function(){ commandCut(); });
  $("#transformCopy").click(function(){ commandCopy(); });
  $("#transformPaste").click(function(){ commandPaste(); });
  $("#transformMirrorSimple").click(function(){ commandMirror(0); });
  $("#transformMirrorDiag").click(function(){ commandMirror(1); });
  $("#transformRotateLeft").click(function(){ commandRotate(-1); });
  $("#transformRotateRight").click(function(){ commandRotate(1); });
  $("#transformMoveUp").click(function(){ commandMove(1); });
  $("#transformMoveDown").click(function() { commandMove(0); });
  
  // Keyboard shortcuts.
  $("html").keydown(function(e){
    if (captured) { return; }
    switch (e.which)
    {
      // 1
      case 49: { $("#quanlist").val(4); break; }
      // 2
      case 50: { $("#quanlist").val(8); break; }
      // 3
      case 51: { $("#quanlist").val(12); break; }
      // 4
      case 52: { $("#quanlist").val(16); break; }
      // 5
      case 53: { $("#quanlist").val(24); break; }
      // 6
      case 54: { $("#quanlist").val(32); break; }
      // 7
      case 55: { $("#quanlist").val(48); break; }
      // 8
      case 56: { $("#quanlist").val(64); break; }
      // 9
      case 57: { $("#quanlist").val(192); break; }
      // T
      case 84: { $("#typelist").val("1"); break; }
      // H
      case 72: { $("#typelist").val("2"); break; }
      // E
      case 69: { $("#typelist").val("3"); break; }
      // R
      case 82: { if (!e.ctrlKey) { $("#typelist").val("4"); } break; }
      // M
      case 77: { $("#typelist").val("M"); break; }
      // L
      case 76: { $("#typelist").val("L"); break; }
      // F
      case 70: { $("#typelist").val("F"); break; }
      
      // A
      case 65: { commandRotate(-1); break; }
      // D
      case 68: { commandRotate(1);  break; }
      // W
      case 87: { commandMove(1); break; }
      // S
      case 83: { commandMove(0); break; }
      // I
      case 73: { commandMirror(e.metaKey, e); break; }
      // X
      case 88: { commandCut(); break; }
      // C
      case 67: { commandCopy(); break; }
      // V
      case 86: { commandPaste(); break; }
      // + or =
      case 61: {
        var tmp = $("#scalelist > option:selected").next().val();
        if (!isEmpty(tmp))
        {
          fixScale(tmp);
          $("#scalelist").val(tmp);
        }
        break;
      }
      // - or _
      case 109: {
        var tmp = $("#scalelist > option:selected").prev().val();
        if (!isEmpty(tmp))
        {
          fixScale(tmp);
          $("#scalelist").val(tmp);
        }
        break;
      }
      
      // O
      case 79:
      {
        selMode = (selMode ? 0 : 1);
        $("#modelist").val(selMode);
        swapCursor();
        break;
      }
      
      // P
      case 80: {
        if ($("#stylelist").val() === "routine")
        {
          $("#playerlist").val(parseInt($("#playerlist").val()) ? 0 : 1);
        }
        break;
      }
      // /
      case 191: { commandMirror(1, e); break; }
    }
  });
});
