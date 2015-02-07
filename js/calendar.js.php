<?php

    $isAdmin = $this->isAdmin();
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    
    if ($isAdmin) {
?>
        <script type="text/javascript" src="./js/jquery.ui.timepicker.js"></script>
        <link href="js/jquery.ui.timepicker.css" rel="stylesheet" type="text/css" />
<?php
    }
?>

        <script type="text/javascript">
            var serviceUrl = location.pathname + "?action=data&day=[day]&month=[month]&year=[year]";
            var itemName = '<?php echo ucfirst($this->getItemName()); ?>';
            var itemsName = '<?php echo ucfirst($this->getItemsName()); ?>';
            var selectedDay = '';
            var today = '<?php echo $this->arrToday["mday"] ; ?>';
            

            //Update our live clock
            function updateLiveClock(){
                var $clock = $("#todaysdate");
                var prevTime = new Date($clock.text());
                var date = new Date();
                
                //Current day change
                if (prevTime.getDate() !== date.getDate()) {
                    var day = $(".td_actday .day").text();
                    $(".td_actday").addClass("td").removeClass("td_actday").next().addClass("td_actday");
                    if (day != today) {
                        //setSelectedDay($(".td_actday .day").text());
                    }
                }
                //var format = "Sunday 20th of November 2011 05:33:31 PM";
                //var newformat = "Sunday, November 20, 2011 5:35:38 PM";
                $clock.text(date.toLocaleString());
            }
            
            //================View <?php echo ($this->getItemsName()); ?> in a day
            //Call data service with date information, and get a list of <?php echo ($this->getItemsName()); ?> back
            function setSelectedDay(day, elem) {
                
                //Check that we don't go off the edge of the calendar ;)
                var validate= Application.validateMonth($('#jump_year').val(), $('#jump_month').val(), day);
                if (validate == -1) {
                    JumpToPrevMonth();
                } else if (validate == 1) {
                    JumpToNextMonth();
                }
                validate = Application.validateYear($('#jump_year').val(), $('#jump_month').val(), day);
                if (validate == -1) {
                    JumpToPrevMonth();
                } else if (validate == 1) {
                    JumpToNextMonth();
                }
                
                selectedDay = day;
                $(".td_selday").removeClass("td_selday").addClass('td');
                if (elem) {
                    $(elem).addClass("td_selday");
                } else {
                    $("table.month td .day, table.month_small td .day").each(function(){
                        if ($(this).text() == day+'') {
                            $(this).parent().addClass("td_selday");
                        }
                    });
                }
            }
            function loadList(day) {
                setSelectedDay(day);
                var url = serviceUrl.replace('[day]', parseInt(day))
                    .replace('[month]', $('#jump_month').val())
                    .replace('[year]', $('#jump_year').val());

                $.ajax({
                    url : url,
                    data : {},
                    success : loadListSuccessCallback,
                    dataType : 'json'
                });
            }
            //Success callback for loadList().  Construct view here with returned data.
            function loadListSuccessCallback(data, textstatus, jqXHR) {
                
                var html = "";

                if (data && data.events && data.date) {
                    data.date = formatDateForClient(data.date);
                    for (var i = 0; i < data.events.length; ++i) {
                        var event = data.events[i];
                        event.StartDate = formatDateForClient(event.StartDate);
                        event.EndDate = formatDateForClient(event.EndDate);
                        event.StartTime = formatTimeForClient(event.StartTime);
                        event.EndTime = formatTimeForClient(event.EndTime);
                        if (event.StartDate == event.EndDate) {
                            event.EndDate = "";
                        } else {
                            var endDate = new Date(event.EndDate);
                            event.EndDate = "<a href='#' onclick='loadList("+endDate.getDate()+");return false;'>"+event.EndDate+"</a>";
                        }
                        
                        var template = $("#eventListItem").html();
                        html += Application.parseTemplate(event, template);
                    }
                    
                    if (!data.events.length) {
                        html = "No "+itemsName; 
                    }
                    
                    var dataDate = data.date;
                    dataDate = "<a href='' onclick='loadList("+(parseInt(selectedDay)-1)+");return false;'>&laquo;&laquo;</a> "+dataDate+" <a href='' onclick='loadList("+(parseInt(selectedDay)+1)+");return false;'>&raquo;&raquo;</a>";
                    var $container = $("<div />");
                    var $box = $("<div style='width:600px'></div>").appendTo($container);
                    var $header = $("<h3>"+itemsName+" for "+dataDate+"</h3>").appendTo($box);
                    var $list = $("<dl>"+html+"</dl>").appendTo($box);
<?php 
    if ($isAdmin) {
?>
                    var $tools2 = $("<div class='tools'><a class='newLink' style='float:right' rel='"+data.date+"'>New "+itemName+"</a></div>")
                        .prependTo($box);
                    if (data.events.length) { 
                        var $tools = $("<div class='tools'><a class='newLink' rel='"+data.date+"'>New "+itemName+"</a></div>").appendTo($box);
                    }
                    
                    $container.find(".notes_container").each(function(){
                        var $this = $(this);
                        if ($.trim($this.find(".notes").text()) == "") {
                            $this.addClass("hide");
                        }
                    });
<?php } ?>
                    
                    $.facebox($container.html());
                }
            }
            
            
            $(function(){
            
                $.facebox.overlay = false;
                
                //Set our live clock
                var clockTmr = setInterval('updateLiveClock()', 1000);
                
                //
                //
                //Day <?php echo ($this->getItemsName()); ?> list show
                $("table.month td, table.month_small td").click(function(ev){
                    var $this = $(this),
                        isInMonth = !$this.hasClass("td_empty"),
                        $count = $this.find(".count"),
                        hasEvents = $count.length 
                            && $.trim($count.text()) != "" 
                            && $.trim($count.text()) != "0";

                    if ( isInMonth ) {
                        $('#jump_day').val(Application.PadZeroes($this.text(), 2));
                        //JumpToDate(); //non-ajax
                        var day = $this.find(".day").text();
                        setSelectedDay(day, $this);
                        
                        if ( hasEvents <?php if ($isAdmin) echo "|| true "; ?>) {
                            loadList(day);
                        }
                    }
                });
                
<?php if ($isAdmin) { ?>

                var formHasChanges = false;
    
                //====================Edit
                //Edit show handler.  markup generation here.
                function loadEditForm(ev){
                    
                    ev.preventDefault();
                    var template = $("#editForm").html();
                    template = "<form style=\"width:300px; padding:20px\" class='flash'>" + template + "</form>";
                    var $template = $(template);
                    $template.find("*[rel='name-to-id']").each(function(){
                        var $this = $(this);
                        $this.attr('id', $this.attr('name'));
                    });
                    
                    var editObject = {};
                    
                    //if we are updating (not new), 
                    //load the <?php echo ($this->getItemName()); ?> data and prepare it.
                    if ($(this).hasClass("editLink")) {
                        var id = $(this).attr('rel') || 0;
                        var $form = $("#facebox form");
                        if (id && parseInt(id) != 0) {
                            $.ajax({
                                url : location.pathname,
                                data : {
                                    action:"data",
                                    id:id
                                },
                                success : function(data){
                                    if (data && data.event && data.message && data.message == "Success") {
                                        //alert("event got");
                                        var evt = data.event;
                                        evt.StartDate = formatDateForClient(evt.StartDate);
                                        evt.EndDate = formatDateForClient(evt.EndDate);
                                        evt.StartTime = formatTimeForClient(evt.StartTime);
                                        evt.EndTime = formatTimeForClient(evt.EndTime);
                                        
                                        editObject = data.event;
                                    } else if (data && data.message) {
                                        alert(data.message);
                                    } else {
                                        alert("There was a problem loading the "+itemName+".");
                                    }
                                },
                                dataType : 'json',
                                async : false
                            });
                        }
                    } else if ($(this).hasClass("newLink")) {
                        var date = $(this).attr('rel') || null;
                        if (date) {
                            editObject = {StartDate:date,EndDate:date,StartTime:"06:00 AM",EndTime:"07:00 AM"}; 
                        }
                    }
                    
                    //Insert edit form html into facebox modal window and show
                    $("#facebox .content .close_image").click().delay(1000);
                    var $container = $("<div style='width:370px'></div>");
                    $template.appendTo($container);
                    var $wrapper = $("<div />");
                    $container.appendTo($wrapper);
                    $.facebox($wrapper.html());
                    
                    //Set header to 'new' if new
                    if ($(this).hasClass("newLink")) {
                        $("#facebox .content form h3").text("New "+itemName);
                    }
                    
                    //Back button click handler
                    //reload selected day's list of <?php echo ($this->getItemsName()); ?>   
                    $("#facebox .back").click(function(ev){
                        ev.preventDefault();
                        loadList(selectedDay);
                    });
                    
                    //Save button click handler
                    $("#facebox #submitEdit").click(function(ev){
                        ev.preventDefault();
                        var alreadyFired = false;
                        //alert("(not) submitted"); return false;
                        var key = ev.charCode ? ev.charCode : ev.keyCode ? ev.keyCode : 0;
                        var $form = $("#facebox form");
                        //if enter key (13) on fields, or submitted by click (no .which).
                        if ( key == 13 || key == 0 ) {
                            if (key == 13) ev.preventDefault();
                            
                            if ($.trim($("#facebox").find("input#Name").val()) == "") {
                                alert("You need a name for this " + itemName.toLowerCase()+"!");
                            }
                            $.ajax({
                                url : location.pathname + "?format=json&" + $form.serialize(),
                                data : {
                                    Id : $("#facebox").find("input#Id").val(),
                                    Name : $("#facebox").find("input#Name").val(),
                                    Description : $("#facebox").find("input#Description").val(), 
                                    Notes : $("#facebox").find("textarea#Notes").val(),
                                    StartDate : formatDateForServer($("#facebox").find("input#StartDate").val()),
                                    StartTime : formatTimeForServer($("#facebox").find("input#StartTime").val()),
                                    EndDate : formatDateForServer($("#facebox").find("input#EndDate").val()),
                                    EndTime : formatTimeForServer($("#facebox").find("input#EndTime").val()),
                                    Active : $("#facebox").find("input#Active:checked").length > 0 
                                },
                                success : function(data){
                                    if (data && data.message && data.message == "Success") {
                                        //alert(data.message);
                                        new Application.FlashMessage(data.message);
                                        //location.reload();
                                        formHasChanges = true;
                                        //$("#facebox .content form .back").click();
                                    } else if (data && data.message) {
                                        alert(data.message);
                                    } else {
                                        alert("There was a problem saving the "+itemName+".");
                                    }
                                },
                                dataType : 'json',
                                type : 'POST',
                                async : false
                            });
                        }
                    });
                    
                    //populate form if we have data prepared
                    for (var prop in editObject) {
                        if (editObject.hasOwnProperty(prop)) {
                            var val = $.trim(editObject[prop]);
                            $("#facebox .content input#"+prop).val(val);
                            $("#facebox .content textarea#"+prop).val(val);
                            if (prop == "Active" && parseInt(val) == 1) {
                                $("#facebox .content input[type='checkbox']#"+prop).attr("checked","checked");
                            } else if (prop == "Active" && parseInt(val) == 0) {
                                $("#facebox .content input[type='checkbox']#"+prop).removeAttr("checked");
                            }
                        }
                    }
                    
                    
                    //Set special form UI components
                    var timepickerOptions = {
                        showPeriod: true,
                        minutes: {
                            starts: 0,                // First displayed minute
                            ends: 45,                 // Last displayed minute
                            interval: 15               // Interval of displayed minutes
                        }
                    };
                    $("#facebox").find("input#StartTime").timepicker(timepickerOptions);
                    $("#facebox").find("input#EndTime").timepicker(timepickerOptions);
                    
                    var datepickerOptions = { };
                    $("#facebox").find("input#StartDate").datepicker(datepickerOptions);
                    $("#facebox").find("input#EndDate").datepicker(datepickerOptions);
                }
                
                $(".editLink").live('click', loadEditForm);
                $(".newLink").live('click', loadEditForm);
                
                //If we changed anything, reload calendar page when modal closes
                $(document).bind('afterClose.facebox', function() { if (formHasChanges) location.reload(); });
    
    
                //==================Logout
                //Logout click handler
                $("#logoutLink").click(function(ev){
                    ev.preventDefault();
                    $.ajax({
                        url : location.pathname + "?action=logout&format=json",
                        success : function(data){
                            if (data && data.message && data.message == "Success") {
                                location.reload();
                            } else {
                                alert("Error.  User still logged in!");
                            }
                        },
                        dataType : 'json'
                    });
                });
<?php } else { ?>

                //==============Login
                //Login auth
                function authLoginForm(ev) {
                    var key = ev.charCode ? ev.charCode : ev.keyCode ? ev.keyCode : 0;
                    var $form = $("#facebox form");
                    //if enter key (13) on fields, or submitted by click (no .which).
                    if ( key == 13 || key == 0 ) {
                        if (key == 13) ev.preventDefault();
                        $.ajax({
                            url : location.pathname + "?format=json&" + $form.serialize(),
                            data : {
                                user : $("#facebox").find("input#user").val(), 
                                pass : $("#facebox").find("input#pass").val()
                            },
                            success : function(data){
                                if (data && data.message && data.message == "Success") {
                                    location.reload();
                                } else if (data && data.message) {
                                    alert(data.message);
                                } else {
                                    alert("Username and password not accepted");
                                }
                            },
                            dataType : 'json'
                        });
                    }
                }
                
                //Login show   
                function showLoginForm(ev) {
                    ev.preventDefault();
                    var $form = $("#loginForm");    //Get template
                    var template = $form.html();
                    template = Application.parseTemplate(null, template);
                    //template = Application.parseTemplate(event, template);
                    //wrap in form tag
                    $.facebox("<form style=\"width:300px; padding:20px\" class='flash'>" + template + "</form>");
                    //bind 'submit'
                    $("#facebox").find("input#submitLogin").bind('keydown', authLoginForm);
                    $("#facebox").find("input#submitLogin").bind('click', authLoginForm);
                    $("#facebox").find("input#pass, input#user").bind('keypress', authLoginForm);
                }
                $("#loginLink").click(showLoginForm);
<?php } ?>

            });
        </script>
        <script type='text/javascript'>
            
            //var Calendar = {

            function JumpToDate(day, today){
                if (today) {
                    JumpToDate(day);
                }
                var jump_day   = day || ($('#jump_day')).val() || 1;
                var jump_month = ($('#jump_month')).val();
                var jump_year  = ($('#jump_year')).val();
                var view  = ($('#view')).val();

                __doPostBack('display', view, jump_year, jump_month, jump_day);
            }
            
            function JumpToPrevMonth(){
                $("#facebox .content .close_image").click();
                var func = $("#jumpPrevMonth").attr('href').replace('javascript:', '');
                eval(func);
            }
            
            function JumpToNextMonth() {
                $("#facebox .content .close_image").click();
                var func = $("#jumpNextMonth").attr('href').replace('javascript:', '');
                eval(func); 
            }
            
            function __doPostBack(action, view, year, month, day)
            {			
                var action    = (action) ? action : '<?php echo $this->defaultAction; ?>';
                var view = (view) ? view : '<?php echo $this->defaultView; ?>';
                var year      = (year) ? year : '<?php echo $this->arrToday["year"] ; ?>';
                var month     = (month) ? month : '<?php echo $this->ConvertToDecimal($this->arrToday["mon"]) ; ?>';
                var day       = (day) ? day : today;

                document.location.href = location.pathname + '?view='+view+'&year='+year+'&month='+month/*+'&day='+day*/;		
            }

            
            
            function formatDateForServer(date){
                //alert(date);
                date = date.split("/");
                var ret = "";
                ret += $.trim(date[2]) + "-";
                ret += date[0] + "-" + date[1];
                ret += " ";
                return ret;
            }
            function formatDateForClient(date){
                date = date.split("-");
                var ret = "";
                ret += date[1] + "/" + date[2] + "/";
                ret += "20" + date[0].substr(2);
                ret += " ";
                return ret;
            }
            function formatTimeForClient(time) {
                if (time) {
                    //alert(time);
                    time = time.split(":");
                    var hour = $.trim(time[0]+'');
                    var minutes = time[1];
                    var half = minutes.split(" ");
                    minutes = half[0];
                    half = "AM";
                    if (hour == "00") {
                        hour = "12";
                    } else if (hour == '12') {
                        half = "PM";
                    } else if ((hour) > '12') {
                        hour = parseInt(hour) - 12;
                        half = "PM"; 
                    } else if (hour.substr(0,1) == "0" && hour.length == 2) {
                        hour = hour.substr(1,1)
                    } 
                    var ret = "";
                    ret += hour + ":" + minutes + " " + half;
                    return ret;
                } return null;
            }
            function formatTimeForServer(time){
                time = time.split(":");
                var hour = (time[0]+'');
                var minutes = time[1];
                var half = minutes.split(" ");
                minutes = half[0];
                half = half[1];
                if (hour == "08") {
                    if (half == "PM") {
                        hour = 20;
                    }
                } else if (hour == "09") {
                    if (half == "PM") {
                        hour = 21;
                    }
                } else if (half == "PM" && parseInt(hour) < 12) {
                    hour = parseInt(hour) + 12; 
                } else if (hour.length == 1 && parseInt(hour) < 12) {
                    hour = '0'+hour;
                } else if (half == "AM" && hour == '12') {
                    hour = '00';
                }
                var ret = "";
                ret += hour + ":" + minutes;
                return ret;
            }
            

            //}
        </script>