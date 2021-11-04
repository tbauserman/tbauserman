$(document).ready(function(){
    $('#user_grid').dialog({
        position: { my: 'center bottom', at: 'center center', of: window },
        width: 'auto', 
        autoOpen: false, 
        resizable: false,
    }); 
    $("#mp_grid").dialog({
        position: { my: "center bottom", at: "center center", of: window },
        width: 'auto',
        autoOpen: false, 
    });

    $('#ticket_grid').dialog({ 
        position: { my: 'center bottom', at: 'center center', of: window }, 
        width: 1200, 
        autoOpen: false, 
        resizable: false
    })
    $('#open_users').on('click',function(){ 
        $('#user_grid').dialog('open');
    });
    $('#open_mp').on("click",function(){ 
        $("#mp_grid").dialog("open"); 
    });
    $('#open_tickets').on('click', function(){ 
        $('#ticket_grid').dialog('open'); 
    });
    $(document).on({ 
        ajaxStart: function() {
            $('body').addClass('loading'); 
        },
        ajaxStop: function() {
            $('body').removeClass('loading'); 
        }
    }); 
            
    $(".tabs-list li a").click(function(e){
        e.preventDefault();
    });

    $(".tabs-list li").click(function(){
        var tabid = $(this).find("a").attr("href");
        $(".tabs-list li,.tabs div.tab").removeClass("active");   // removing active class from tab
        $(".tab").hide();   // hiding open tab
        $(tabid).show();    // show tab
        $(this).addClass("active"); //  adding active class to clicked tab
    });
});