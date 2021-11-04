$(document).ready(function(){ 
    var App = {
        url: 'process/accounts.php',
        term: '', 
        account_id: '', 
    }

    var db = {
        loadData: function() { 
            return $.ajax({ 
                type: "GET", 
                dataType: 'json', 
                url: App.url, 
                data: {
                    term: App.term, 
                    account_id: App.account_id
                }
            });
        }, 
        insertItem: function(item) { 
            return $.ajax({ 
                type: "POST", 
                dataType: 'json',
                url: App.url, 

            }).done(function(){
                App.account_id = item.account_id; 
                $('#user_grid').jsGrid('loadData'); 
            });
        },
        updateItem: function(item) {
            return $.ajax({ 
                type: "PUT", 
                dataType: 'json', 
                url: App.url, 
                data: item
            });
        }, 
        deleteItem: function(item) { 
            return $.ajax({ 
                type: "DELETE", 
                dataType: 'json', 
                url: App.url, 
                data: item
            }); 
        }
    }; 

    var user_grid = $('#user_grid').jsGrid({ 
        width: "900px", 
        height: 'auto', 
        autoload: true, 
        inserting: true, 
        editing: true, 
        sorting: true,
        paging: true, 
        pageSize: 10, 
        pageButtonCount: 5, 
        pageIndex: 1, 
        deleteConfirm: "Are you sure about that?", 
        controller: db, 
        fields: [
            { name: 'account_id',visible: false }, 
            { name: 'region', title: 'Region', type: 'text',width:30 },
            { name: 'first_name',title: 'First Name',type:'text',width:45 },
            { name: 'last_name',title: 'Last Name',type:'text',width:45 }, 
            { name: 'account_email',title: 'E-Mail',type:'text',width:80 }, 
            { name: 'phone',title: 'Ph #',type: 'text',width:45 }, 
            { name: 'account_type', title: 'Type', type: 'text',width:30 },
            { name: 'enabled',title: 'Active',type:'text',width:27 }, 
            { name: 'admin',title: 'Admin', type:'text',width:27 },
            { type: 'control'}
        ],

    });

    var myReload = function(obj) {  
        App.term = $(obj).val(); 
        $('#user_grid').jsGrid('loadData'); 
    }
    var keyupHandler = function(e,refreshFunction,obj) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 33 /*page up*/|| keyCode === 34 /*page down*/||
        keyCode === 35 /*end*/|| keyCode === 36 /*home*/||
        keyCode === 38 /*up arrow*/|| keyCode === 40 /*down arrow*/) {}
            if (typeof refreshFunction === "function") {
                refreshFunction(obj);
            }
    }

    $(document).on('change','.user-search',function(e){ 
        keyupHandler(e,myReload,this);
    });
});