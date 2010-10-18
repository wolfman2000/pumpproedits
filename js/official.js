/*
JS file for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$(document).ready(function()
{
  $("#diff > option:first-child").before("<option value=''>Choose!</option>");
  $("#diff").val('');
  $("#diff > option:not(:first-child)").remove();
  $("#songs").val(0);
  $("#submit").attr('disabled', 'disabled');
  
  $("#songs").change(function()
  {
    $("#submit").attr('disabled', 'disabled');
    var diff = $("#diff").val();
    $("#diff > option:not(:first-child)").remove();
    var sid = Math.floor($("#songs").val());
    if (sid > 0)
    {
      $.getJSON("/chart/diff/" + sid, function(data){
        var options = '';
        for (var d in data)
        {
          if (d === "id") { continue; }
          if (data[d])
          {
          	  options = options + '<option value="' + d + '">' + data[d] + '</option>';
          }
        }
        $("#diff").append(options);
        var old = $("#diff option[value=" + diff + "]");
        if (old.val() !== undefined)
        {
          $("#submit").removeAttr('disabled');
        }
        else { diff = ''; }
        $("#diff").val(diff);
      });
    }
  });
  
  $("#diff").change(function()
  {
    if ($("#diff").val().length) { $("#submit").removeAttr('disabled'); }
    else                         { $("#submit").attr('disabled', true); }
  });
});
