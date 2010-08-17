// initialise plugins
$(function(){
  $("ul.sf-menu").supersubs({ 
    minWidth:    12,   // minimum width of sub-menus in em units 
    maxWidth:    27,   // maximum width of sub-menus in em units 
    extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
                       // due to slight rounding differences and font-family 
  })
  .superfish({
    delay:       400,                            // one second delay on mouseout 
    animation:   {height:'show'},  // fade-in and slide-down animation 
    speed:       100,                          // faster animation speed 
    autoArrows:  true,                           // disable generation of arrow mark-up 
    dropShadows: false                     // disable drop shadows 
  }); 
});
		
$(document).ready(function() {
  $("#userbar li").removeClass("hide");
  // Tell jQuery that our div is to be a dialog  
  $('#loginbox').dialog({
    autoOpen: false,
    modal: true,
    resizable: false,
    width: 400,
    height: 270,
    buttons: {
      'Log in': function(){
        var good = 1;
        if ($("#username").val().length)
        {
          $("#loginForm label:eq(0)").removeClass("error_list");
        }
        else
        {
          $("#loginForm label:eq(0)").addClass("error_list");
          good = 0;
        }
        if ($("#password").val().length)
        {
          $("#loginForm label:eq(1)").removeClass("error_list");
        }
        else
        {
          $("#loginForm label:eq(1)").addClass("error_list");
          good = 0;
        }
        if (good) $("#loginForm").submit();
      },
      'Trouble logging in?': function(){
        document.location.href = location.protocol + "//" + location.host + '/help';
      },
      'Register': function(){
        document.location.href = location.protocol + "//" + location.host + "/register";
      }
    }
  });  

  $('#loginlink').click(function() {
    $('#loginbox').removeClass("hide").dialog('open');
    });  
});
