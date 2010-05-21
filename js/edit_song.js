var parts;
var last;
$(document).ready(function()
{
  parts = baseURL.split("/");
  last = parts[parts.length-1];
  if (last != parseInt(last))
  {
    last = 1;
  }
  $(".pager").pager({ pagenumber: last, pagecount: maxPages, buttonClickCallback: PageClick });

});

var PageClick = function(pageclickednumber) {
  $(".pager").pager({ pagenumber: pageclickednumber, pagecount: maxPages, buttonClickCallback: PageClick });
  $.getJSON("/edits/songConquer/" + songID + "/" + pageclickednumber, function(data, status){
    if (status == "success")
    {
      var types = Array("Steps", "Jumps", "Holds", "Mines", "Trips", "Rolls", "Lifts", "Fakes");
      $("#edits tbody").empty();
      for (var d in data.edits)
      {
        var batch = data.edits[d];
        batch = batch;
        if (batch.user_id != 2)
        {
          var row = '<td><a href="/user/' + batch.user_id + '">' + batch.uname + '</a></td>';
        }
        else
        {
          var row = '<td><a href="/official">Official</a></td>';
        }
        row += '<td>' + batch.title + '</td>';


        // stats are shown here.
        row += '<td><dl>';
        row += '<dt>Style</dt><dd>' + batch.style.substring(0,1).toUpperCase() + batch.diff + '</dd>';
        
        for (var i = 0; i < 8; i++)
        {
          if (eval("batch.y" + types[i].toLowerCase()) > 0 || eval("batch.m" + types[i].toLowerCase()) > 0)
          {
            row += '<dt>' + types[i] + '</dt><dd>' + eval("batch.y" + types[i].toLowerCase());
            if (batch.style == "routine")
            {
              row += "/" + eval("batch.m" + types[i].toLowerCase());
            }
            row += '</dd>';
          }
        }

        row += '</dl></td>';

        var link = '/edits/download/';

        row += '<td><ul>';
        row += '<li><a href="/edits/download/' + batch.id + '">Download</a></li>';
        row += '<li><a href="/chart/quick/' + batch.id + '/classic">Classic Chart</a></li>';
        row += '<li><a href="/chart/quick/' + batch.id + '/rhythm">Rhythm Chart</a></li>';
        row += '</ul></td>';

        $("#edits tbody").append('<tr>' + row + '</tr>');
      }
    }
  });
};
