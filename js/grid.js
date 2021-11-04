$(document).ready(function(){
    
    /* Initialize grid variables */
    var MPApp = {
        loadUrl: 'process/get_mp.php', 
        editUrl: 'process/update_mp.php',
        deleteUrl: 'process/delete_mp.php',
        filter: '', 
        process_filter: 0,
        mp_search: '',
        customer_id: '',
    }

    var MPfilterGrid = function(e) {
        MPApp.process_filter = 1; 
        MPApp.filter.dba = $('.filter_mp_dba').val(); 
        MPApp.filter.legal_name = $('.filter_mp_legal').val(); 
        MPApp.filter.igb_license = $('.filter_mp_license').val(); 
        MPApp.filter.site_code = $('.filter_mp_site_code').val(); 
        MPApp.filter.video = $('.filter_mp_video').val(); 
        MPApp.filter.online = $('.filter_mp_online').val(); 
        $('#mp_grid').jsGrid('loadData');
    }
    /* might be able to get rid of this */
    var insertRowMoved = false; 
    var toolTip = function(value) { 
        return this._insertAuto = $("<div title='"+value+"'>"+value+"</div>"); 
    };
    /* Inititalize Grid Controllers */
    var db = {
        loadData: function(filter){
            return $.ajax({
                type: "GET",
                dataType: 'json', 
                url: MPApp.loadUrl, 
                data: { 
                    mp_search: MPApp.mp_search, 
                    customer_id: MPApp.customer_id, 
                    filter: filter, 
                    process_filter: MPApp.process_filter }
            });
        },
        insertItem: function(item) {
            return $.ajax({ 
                type: "POST", 
                dataType: 'json',
                url: "./process/insert_mp.php", 
                data: item
            }).done(function(){
                MPApp.customer_id = item.customer_id; 
                $('#mp_grid').jsGrid('loadData'); 
            });
        }, 
        updateItem: function(item){
            return $.ajax({
                type: "PUT", 
                dataType: 'json',
                url: MPApp.editUrl, 
                data: item,
                filter: filter,
            }).done(function(response){
                $('#mp_grid').jsGrid('loadData');
            });
        },
        deleteItem: function(item){
            return $.ajax({
                type: "DELETE", 
                url: MPApp.deleteUrl, 
                data: item
            }); 
        }
    }; 

    var mp_grid = $('#mp_grid').jsGrid({
        width: "1386px", 
        height: "auto", 
        autoload: true, 
        inserting: true, 
        editing: true, 
        sorting: true, 
        paging: true, 
        filtering: true,
        pageSize: 10, 
        pageButtonCount: 5, 
        pageIndex: 1, 
        deleteConfirm: "Are you sure you know what you're doing?", 
        controller: db, 
        fields: [
            {name:'id', type:'number', css: 'hide',width:0},
            {name:'customer_id',type: 'number', css: 'hide', width:0},
            {
                name: "dba",
                type: "text",
                width: 45, 
                title: "DBA", 
                editing: false, 
                itemTemplate: function(value) { 
                    return this._insertAuto = $("<div title='"+value+"'>"+value+"</div>"); 
                },
                insertTemplate: function(value) { 
                    return this._insertAuto = $("<input>").autocomplete({
                        source: function(req,res) {
                            $.ajax({
                                url: "./process/get_cust.php",
                                dataType: 'json',
                                data: {
                                    term: req.term
                                },
                                success: function(data){
                                    var result = []; 
                                    data.forEach(function(d){
                                        item = {
                                            label: d.dba+' - '+d.legal_name+' - '+d.igb_license,
                                            value: d.customer_id
                                        };

                                        result.push(item);
                                    }); 
                                    res(result);
                                }
                            });
                        },
                        minLength: 2, 
                        select: function(event,ui) {
                            event.preventDefault(); 
                            $(this).val(ui.item.label); 
                            e = $(this).parent().parent().children().eq(1).children(':first-child');
                            e.val(ui.item.value); 
                        }
                    });
                }, 
                filterTemplate: function(value) { 
                  var $filter_dba = jsGrid.fields.text.prototype.filterTemplate.call(this,value);  
                  $filter_dba.addClass('filter_mp_dba'); 
                  $filter_dba.on("keyup",MPfilterGrid);
                  return $filter_dba;
                },
                insertValue: function() {
                    return this._insertAuto.val(); 
                }
            }, 
            {
                name: "legal_name",
                type:"text",
                width:45,
                title:"Legal",
                editing:false,
                inserting:false, 
                itemTemplate: function(value) { 
                    return this._insertAuto = $("<div title='"+value+"'>"+value+"</div>"); 
                },
                filterTemplate: function(value) { 
                    var $filter_legal = jsGrid.fields.text.prototype.filterTemplate.call(this,value);  
                    $filter_legal.addClass('filter_mp_legal'); 
                    $filter_legal.on("keyup",MPfilterGrid);
                    return $filter_legal;
                },
            }, 
            {
                name: "igb_license",
                type:"number",
                width:25,
                title:"IGB",
                editing:false,
                inserting:false,
                filterTemplate: function(value) { 
                    var $filter_license = jsGrid.fields.text.prototype.filterTemplate.call(this,value);  
                    $filter_license.addClass('filter_mp_license'); 
                    $filter_license.on("keyup",MPfilterGrid);
                    return $filter_license;
                },
            },
            {
                name: "site_code",
                type:"number",
                width:2,
                title:"BMC",
                editing:false,
                inserting:false,
                filterTemplate: function(value) { 
                    var $filter_site_code = jsGrid.fields.text.prototype.filterTemplate.call(this,value);  
                    $filter_site_code.addClass('filter_mp_site_code'); 
                    $filter_site_code.on("keyup",MPfilterGrid);
                    return $filter_site_code;
                },
            },
            {name: "number",type:"number",width:10,title:"#",filtering:false}, 
            {
                name: "ip",
                type:"text",
                width:35,
                title:"IP",
                filterTemplate: function(value) { 
                    var $filter_ip = jsGrid.fields.text.prototype.filterTemplate.call(this,value);  
                    $filter_ip.addClass('filter_mp_ip'); 
                    $filter_ip.on("keyup",MPfilterGrid);
                    return $filter_ip;
                },
            },
            {
                name: "video",
                type:"text",
                width:40,
                title:"Video",
                editing:false,
                inserting:false,
                filterTemplate: function(value) { 
                    var $filter_video = jsGrid.fields.text.prototype.filterTemplate.call(this,value);  
                    $filter_video.addClass('filter_mp_video'); 
                    $filter_video.on("keyup",MPfilterGrid);
                    return $filter_video;
                },
            },
            {
                name: "online",
                type:"text",
                width:15,
                title:"OnLine",
                editing:false,
                inserting:false,
                filtering:false
            },
            {
                width: 35, 
                title: "Upload",
                editing: false,
                itemTemplate: function(value,item){
                    $form = $("<form method='post' action='process/upload_mp.php' name='uploadForm_"+item.id+"' id='uploadForm_"+item.id+"'></form>");
				    $form.append("<input class='auto-submit' type='file' id='uploadVideo_"+item.id+"' name='uploadVideo_"+item.id+"' style='display:none;'/>"); 
					$form.append("<label class='btn' for='uploadVideo_"+item.id+"'>Upload</label>"); 
					$form.append("<input type='hidden' name='mp_id_"+item.id+"' id='mp_id_"+item.id+"' value='"+item.id+"'/>");

                    $(document).on('change','#uploadVideo_'+item.id,function(e){
                        e.preventDefault(); 
                        var form = $(this).closest("form");
                        $(form).submit(); 
                    });

                    $(document).on('submit','#uploadForm_'+item.id,function(e){
                        e.preventDefault(); 
                        var formData = new FormData(this); 
                        console.log(formData); 
                        $.ajax({
                            type: "POST", 
                            url: $(this).prop("action"),
                            data: formData, 
                            contentType: false, 
                            processData: false, 
                            success: function(data) {
                                $('#mp_grid').jsGrid('loadData'); 
                            }
                        })
                    })
                    return $form; 
                }
            },
            { type: "control", width: 20 },
        ],
    });

    var myReload = function(obj) {  
        MPApp.mp_search = $(obj).val(); 
        $('#mp_grid').jsGrid('loadData'); 
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

    $('#mp_search').change(myReload).keyup(function(e){ 
        keyupHandler(e,myReload,this); 
    });

    $('#customer_s').on(
        "input",
        function(){
            if($(this).val().length >= 3) {
                url = "process/get_cust.php"; 
                data = { customer_s: $(this).val() }
                $.ajax({
                    url: url,
                    data: data,
                    dataType: 'json',
                    type: 'post', 
                    success: function(output) {
                        var cust_html='<select id="customer" name="customer">'; 
                        cust_html+='<option value="">Select Customer</option>'; 
                        $.each(output, function(i,item) {
                            cust_html+='<option value="'+item.customer_id+'">'+item.legal_name+' - '+item.dba+' - '+item.site_code+'</option>';
                        });
                        cust_html+='</select>'; 
                        $('div[id="customer_div"]').html(cust_html); 
                    }
                }); 
                return false; 
            }
        }
    );
});