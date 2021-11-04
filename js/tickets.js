$(document).ready(function(){
    var TicketApp = {
        url: 'process/tickets.php', 
        ticket_filter: '', 
        ticket_id: ''
    }

    var TicketFilterGrid = function(e) { 
        $('#ticket_grid').jsGrid('loadData'); 
    }

    var toolTip = function(value) { 
        return this._insertAuto = $("<div title='"+value+"'>"+value+"</div>"); 
    }
    var ticket_db = {
        loadData: function() {
            ticket_filter = {
            dba: $('.filter_ticket_dba').val(),
            legal_name: $('.filter_ticket_legal').val(),
            igb_license: $('.filter_ticket_license').val(),
            site_code: $('.filter_ticket_site_code').val(),
            name_tech: $('.filter_ticket_tech').val()
            }
            console.log(ticket_filter); 
            return $.ajax({ 
                type: "GET", 
                dataType: 'json', 
                url: TicketApp.url, 
                data: {
                    filter: ticket_filter,
                    process_filter: TicketApp.process_filter
                }
            }); 
        }, 
        insertItem: function(item) { 
            console.log(item);
            return $.ajax({ 
                type: "POST", 
                dataType: 'json', 
                url: TicketApp.url,
                data: item
            }).done(function(){
                TicketApp.ticket_id = item.id;
                $('#ticket_grid').jsGrid('loadData'); 
            });
        }, 
        updateItem: function(item) {
            return $.ajax({ 
                type: "PUT", 
                dataType: 'json', 
                url: TicketApp.url, 
                data: item
            }).done(function(){

                $('#ticket_grid').jsGrid('loadData'); 
            }); 
        }, 
        deleteItem: function(item) {
            return $.ajax({ 
                type: 'DELETE', 
                dataType: 'json', 
                url: TicketApp.url, 
                data: item
            }); 
        }
    }; 

    var ticket_grid = $('#ticket_grid').jsGrid({ 
        width: '1172', 
        height: 'auto', 
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
        controller: ticket_db, 
        fields: [ 
            { name: 'ticket_id', type: 'number', css: 'hide', width: 0}, 
            { name: 'customer_id', type: 'number', css: 'hide',width: 0}, 
            { 
                name: 'dba', 
                title: 'DBA', 
                type:'text', 
                editing: false, 
                width: '80',
                itemTemplate: toolTip, 
                insertTemplate: function(value) {
                    return this._insertAuto = $("<input>").autocomplete({ 
                        source: function(req,res) { 
                            $.ajax({ 
                                url: "./process/get_cust.php", 
                                dataType: 'json', 
                                data: { 
                                    term: req.term
                                }, 
                                success: function(data) { 
                                    var ticket_dba_result = []; 
                                    data.forEach(function(d){
                                        item = { 
                                            label: d.dba+' - '+d.legal_name+' - '+d.igb_license,
                                            value: d.customer_id
                                        }; 
                                        ticket_dba_result.push(item); 
                                    });
                                    res(ticket_dba_result); 
                                }
                            })
                        },
                        minLength: 2,
                        select: function(event,ui) { 
                            event.preventDefault(); 
                            $(this).val(ui.item.label); 
                            e = $(this).parent().parent().children().eq(1).children(':first-child');
                            e.val(ui.item.value);
                            /*$(this).prevAll('input').val(ui.item.value);*/
                            /*el = $(this).parent().parent().children().eq(1).children(':first-child'); */
                            
                        }
                    }); 
                },
                filterTemplate: function(value) { 
                    var $filter_ticket_dba = jsGrid.fields.text.prototype.filterTemplate.call(this,value);
                    $filter_ticket_dba.addClass('filter_ticket_dba'); 
                    $filter_ticket_dba.on("keyup",function(){
                        $('#ticket_grid').jsGrid('loadData'); 
                    }); 
                    return $filter_ticket_dba; 
                },
                insertValue: function() {
                    return this._insertAuto.val(); 
                }
            }, 
            { 
                name: 'legal_name', 
                title: 'Legal', 
                type: 'text', 
                width: 80, 
                inserting: false, 
                editing: false, 
                itemTemplate: toolTip, 
                filterTemplate: function(value) {
                    var $filter_ticket_legal = jsGrid.fields.text.prototype.filterTemplate.call(this,value); 
                    $filter_ticket_legal.addClass('filter_ticket_legal');
                    $filter_ticket_legal.on("keyup",function(){
                        $('#ticket_grid').jsGrid('loadData'); 
                    }); 
                    return $filter_ticket_legal; 
                }
            }, 
            { 
                name: 'igb_license', 
                title: 'IGB', 
                type: 'text',
                width: 41,
                inserting: false, 
                editing: false,
                itemTemplate: toolTip, 
                filterTemplate: function(value) {
                    var $filter_ticket_license = jsGrid.fields.text.prototype.filterTemplate.call(this,value); 
                    $filter_ticket_license.addClass('filter_ticket_license');
                    $filter_ticket_license.on("keyup",function(){
                        $('#ticket_grid').jsGrid('loadData'); 
                    }); 
                    return $filter_ticket_license; 
                }, 
                
            }, 
            { 
                name: 'site_code',
                title: 'Site Code', 
                type: 'text',
                width: 35,
                inserting: false, 
                editing: false, 
                itemTemplate: toolTip,
                filterTemplate: function(value) {
                    var $filter_ticket_site_code = jsGrid.fields.text.prototype.filterTemplate.call(this,value); 
                    $filter_ticket_site_code.addClass('filter_ticket_site_code');
                    $filter_ticket_site_code.on("keyup",function(){
                        $('#ticket_grid').jsGrid('loadData'); 
                    }); 
                    return $filter_ticket_site_code; 
                }
            },
            { name: 'vgt_no',title: 'VGT#',type:'number',width:20,editing: false },
            { name: 'tech_id', type: 'number', css: 'hide', width: 0 }, 
            { 
                name: 'name_tech', 
                title: 'Tech', 
                type: 'text',
                width:50, 
                editing: false, 
                itemTemplate: toolTip, 
                insertTemplate: function(value) {
                    return this._insertAuto = $("<input>").autocomplete({ 
                        source: function(req,res) { 
                            $.ajax({ 
                                url: "./process/accounts.php", 
                                dataType: 'json', 
                                data: { 
                                    term: req.term,
                                }, 
                                success: function(data) { 
                                    var result = []; 
                                    data.forEach(function(d){
                                        item = { 
                                            label: d.first_name+' '+d.last_name+' - '+d.region,
                                            value: d.account_id
                                        }; 
                                        result.push(item); 
                                    });
                                    res(result); 
                                }
                            })
                        },
                        minLength: 2,
                        select: function(event,ui) { 
                            event.preventDefault(); 
                            $(this).val(ui.item.label); 
                            e = $(this).parent().parent().children().eq(7).children(':first-child');
                            e.val(ui.item.value); 
                            /*el = $(this).parent().parent().children().eq(1).children(':first-child'); */ 
                        }
                    }); 
                },
                filterTemplate: function(value) {
                    var $filter_ticket_tech = jsGrid.fields.text.prototype.filterTemplate.call(this,value); 
                    $filter_ticket_tech.addClass('filter_ticket_tech');
                    $filter_ticket_tech.on("keyup",function(){
                        $('#ticket_grid').jsGrid('loadData'); 
                    }); 
                    return $filter_ticket_tech; 
                },
                insertValue: function(){
                    return this._insertAuto.val(); 
                }
            }, 
            { 
                name: 'ticket_notes', 
                title: 'Note', 
                type: 'text', 
                width: 120,
                itemTemplate: function(value) {
                    console.log(value); 
                    return this._insertAuto = $("<div title='"+value+"'>"+value.replaceAll('&#010;','<br>')+"</div>");
                }, 
                editTemplate: function(value, item) {
                    $el = jsGrid.fields.text.prototype.editTemplate.call(this,value); 
                    $el.val(''); 
                    return $el; 
                }
            }, 
            { 
                name: 'open', 
                title: 'Open', 
                type: 'select',
                width: 25,
                filtering: false,
                itemTemplate: function (value,item) { 
                    if(value == true) { 
                        return $("<span>").attr("class", "fas fa-check").css({"color":"#9CCC65"}); 
                    } else { 
                        return $("<span>").attr("class", "fas fa-times").css({"color":"red"});
                    }
                },
                items: [
                    { Name: "", Id: 0 }, 
                    { Name: "Open", Id: 1 }, 
                    { Name: "Closed", Id: 0 }
                ], 
                valueField: "Id", 
                textField: "Name"
            },
            { name: 'control',type: 'control' }
        ],

    }); 
}); 