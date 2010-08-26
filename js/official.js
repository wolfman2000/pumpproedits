$(document).ready(function()
{
  $("#diff > option:first-child").before("<option value=''>Choose!</option>");
  $("#diff").val('');
  $("#diff > option:not(:first-child)").hide();
  $("#songs").val(0);
  $("#submit").attr('disabled', 'disabled');
  
  $("#songs").change(function()
  {
    $("#submit").attr('disabled', 'disabled');
    $("#diff > option:not(:first-child)").hide();
    var sid = Math.floor($("#songs").val());
    if (sid > 0)
    {
      var diff = $("#diff").val();
      $.getJSON("/chart/diff/" + sid, function(data){
        for (var d in data)
        {
          if (d === "id") { continue; }
          if (data[d]) { $("#diff > option[value=" + d + "]").show(); }
        }
        if ($("#diff > optgroup > option:selected"))
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