// initialise plugins
		$(function(){
			 $("ul.sf-menu").supersubs({ 
            minWidth:    12,   // minimum width of sub-menus in em units 
            maxWidth:    27,   // maximum width of sub-menus in em units 
            extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
                               // due to slight rounding differences and font-family 
        }).superfish({ 
            delay:       200,                            // one second delay on mouseout 
            animation:   {height:'show'},  // fade-in and slide-down animation 
            speed:       'fast',                          // faster animation speed 
            autoArrows:  true,                           // disable generation of arrow mark-up 
            dropShadows: false                     // disable drop shadows 
        }); 
		});
		
		  $(document).ready(function() {  
                // Tell jQuery that our div is to be a dialog  
                $('#loginbox').dialog({
					autoOpen: false,
					modal: true,
					resizable: false,
					width: 400,
					height: 200,
					buttons: {
						'Log in': function(){
							// Login button
						},
						'Forgot password?': function(){
							document.location.href = 'http://www.pumpproedits.com/help';
						},
						'Register': function(){
							// Register button
						}
					}
				});  
  
                $('#loginlink').click(function() {  
                    $('#loginbox').dialog('open');  
                });  
            });   	

