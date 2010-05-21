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
  $.getJSON("/edits/userConquer/" + userID + "/" + pageclickednumber, function(data, status){
    if (status == "success")
    {
      var style = Array('single', 'double', 'halfdouble', 'routine');
      $("#base tbody").empty();
      for (var d in data.songs)
      {
        var batch = data.songs[d];
        batch = batch;
        var row = '<td>' + batch.name + '</td>';
        var link = '/base/download/';
        // all songs have single play.
        for (var s = 0; s < 4; s++)
        {
          if (s < 3 || batch.tmp)
          {
            row += '<td><a href="';
            row += link + batch.id + '/' + style[s] + '">' + batch.abbr + ' ' + style[s] + '</a></td>';
          }
          else
          {
            row += '<td></td>';
          }
        }
        $("#base tbody").append('<tr>' + row + '</tr>');
      }
    }
  });
};
