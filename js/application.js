/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


//Global store/namespace for this application
var Application = {};

//Document ready event
$(function(){

    /*=============================
     * jQuery UI Dialog
     ==============================*/
    //[modal] dialog ready to go
    $("body").append("<div id='modal_dialog'></div>");

    //Dialog function ready to be used.
    Application.Dialog = function(options){
        $("#modal_dialog").dialog(options);
        return $("#modal_dialog");
    };

    function isType(obj, typeName){
        return Object.prototype.toString.call(obj).indexOf(typeName) > -1;
    }

    Application.isString = function(obj) {
        return isType(obj, "String");
    };

    Application.isArray = function(obj) {
        return isType(obj, "Array");
    };

    Application.isNull = function(obj) {
        return isType(obj, "Null");
    };

    Application.isNumber = function(obj) {
        return isType(obj, "Number");
    };

    Application.checkAll = function(isChecked, elems){
        $(elems).each( function() {
            $(this).attr("checked", isChecked);
        });
    };

    Application.tableSorterWithIgnoreHeaders = function(){
        var $this = $(this);    //'this' should be a table element.
        var myHeaders = {};
        $this.find('th.no').each(function (i, e) {
            myHeaders[$(this).index()] = {sorter: false};
        });
        $this.tablesorter({
            // enable debug mode
           //debug: true,
           headers: myHeaders
        });
    };

    //Remove values from array that match the 'deleteValue' parameter.  Default is "", empty string.
    Array.prototype.clean = function(deleteValue) {
        deleteValue = deleteValue || "";
        for (var i = 0; i < this.length; ++i) {
            if (this[i] == deleteValue) {
                this.splice(i, 1);
                i--;
            }
        }
        return this;
    };

    Application.Querystring = function(url) {
        url = url || window.location.toString();
        var qs = url.split("?");
        if (qs.length > 1) {
            qs = qs[1];
        }
        var ret = {};
        qs = qs.split("&");
        for (var i = 0; i < qs.length; ++i) {
            var pair = qs[i].split("=");
            if (pair.length > 1 && !ret[pair[0]]) {
                ret[pair[0]] = pair[1];
            } else {
                if (Application.isArray(ret[pair[0]])) {
                    ret[pair[0]].push(pair[1]);
                } else {
                    ret[pair[0]] = [ pair[1] ];
                }
            }
        }
        return ret;
    };




    /*=============================
     * Flash messages
     *
     *  :namespace:
     *
     * Application = (object) {
     *      FlashMessage : (class) {
     *          initialize : function(){...},   //This effectively executes the main logic.
     *          Options : (object) {
     *              FlashMessageClass : 'flashmsg',
                    CloseButtonClass : 'closeBtn',
                    InfoImage : '/images/info.png',
                    AlertImage : '/images/warning.png',
                    ErrorImage : '/images/cancel.png',
                    CloseButtonText : 'X',
                    DefaultType : 'info',
                    Type : 'info',
                    Msg : '',
                    DelayTime : 10000,  //Before the message disappears.
                    InsertBeforeQuery : 'form.flash'
                    UseCloseButton : false,
                    UseIcon : false,
                    Dispose : true      //Removes the flashmsg element after it disappears.
     *          },
     *          Info : (class) {
     *          },
     *          Alert : (class) {
     *          },
     *          Error : (class) {
     *          },
     *          PulseColor : (function) {elems, [color = 'yellow']}
     *      }
     * }
     *
     * You can call FlashMessage many ways:
     *  1. <code> new Application.FlashMessage("msg") </code>
     *  2. <code> var flash = new Application.FlashMessage({Msg:"msg",Dispose:false}) </code>
     *  3. <code> new Application.FlashMessage.Alert("alertmsg") </code>
     *  4. <code> new Application.FlashMessage({Msg:"alertmsg",Type:'alert'}) </code>
     *  5. <code> new Application.FlashMessage.Error("errormsg") </code>
     *  6. <code> new Application.FlashMessage({Msg:"errormsg",Type:'error'}) </code>
     *
     * You can also call the static utility method PulseColor to change or pulse an element's or elements' color quickly and then back just as quickly.
     * <code> Application.FlashMessage.PulseColor(elems, [color = 'yellow']) </code>
     ==============================*/
    var FlashMessage = Application.FlashMessage = function(options){

        var passedOptions = (!!options);
        if (options && Application.isString(options))
            options = {Msg:options};
        options = options || {};
        var settings = {
            FlashMessageClass : 'flashmsg',
            CloseButtonClass : 'closeBtn',
            InfoImage : '/images/info.png',
            AlertImage : '/images/warning.png',
            ErrorImage : '/images/cancel.png',
            CloseButtonText : 'X',
            DefaultType : 'info',
            Type : 'info',
            Msg : '',
            DelayTime : 10000,
            InsertBeforeQuery : 'form.flash',
            UseCloseButton : false,
            UseIcon : false,
            Dispose : true
        };
        options = jQuery.extend(true, {}, settings, options);
        //alert(options.Type);
        settings = this.Options = options;

        var thisFlash = this;

        var initialize = this.initialize = function(options) {
            options = options || thisFlash.Options || {};

            //Get container
            var $flashMsg = $("."+options.FlashMessageClass);

            $flashMsg.each(function(){
                var $this = $(this);

                options.Type = $($this.children("div")[0]).attr('class');

                //Close button
                if (options.UseCloseButton) {
                    var $closeBtn = $("<div />");
                    $closeBtn.addClass(options.CloseButtonClass).text(options.CloseButtonText).click(function(ev) {
                        $(this).parent().remove();
                    });
                    $this.append($closeBtn);
                }

                //Type icon
                if (options.UseIcon) {
                    var $icon = $("<div class='icon'></div>");
                    //alert(options.Type);
                    switch(options.Type) {
                        case 'error' :
                            $icon.html("<img src='"+options.ErrorImage+"' />");
                            break;
                        case 'alert' :
                            $icon.html("<img src='"+options.AlertImage+"' />");
                            break;
                        case 'info' :
                            $icon.html("<img src='"+options.InfoImage+"' />");
                            break;
                    }
                    $this.append($icon);
                }

                if (!options.UseCloseButton && !options.UseIcon) {
                    $this.find("."+options.Type).css('padding', '8px 15px 8px 15px');
                }

                //Show flash messages, wait any delay, then hide
                $this.fadeIn('normal');

                if (options.DelayTime) {
                    $this.delay(options.DelayTime);
                }
                if (options.Dispose) {
                    $this.fadeOut('normal', function(){
                        $flashMsg.remove();
                    });
                }
            });
        };

        /*
         * options: A way to call the FlashMessage constructor long after page load, complete with a type and message(s).
         *      type - info, alert, error
         *      msg - a string of messages separated by html breaks, or an array of messages
         */
        if (
                passedOptions
                && $.trim(options.Msg) !== ''
                && options.InsertBeforeQuery
            ) {
            var $form = $(options.InsertBeforeQuery);
            if ($form.length) {
                var type = options.Type || 'info';
                var msg = options.Msg;
                if ( msg instanceof Array ) {
                    msg = msg.join('<br />');
                }
                var $flash = $("<div class='"+options.FlashMessageClass+"'><div class='"+type+"'>"+msg+"</div></div>");
                $flash.insertBefore($form);
                initialize();
            }
        } else {
            initialize();
        }

        return this;
    };
    FlashMessage.Info = function(msg){
        return new FlashMessage({Msg:msg, Type:'info'});
    };
    FlashMessage.Alert = function(msg){
        return new FlashMessage({Msg:msg, Type:'alert'});
    };
    FlashMessage.Error = function(msg){
        return new FlashMessage({Msg:msg, Type:'error'});
    };
    /*
     *static utility method to flash or pulse an element's color quickly
     *  be forewarned, if the element's bkgd color is transparent (whether explicit or computed),
     *  it will return to white.
     *  The problem with "transparency" is that "invisible green" looks the same as "invisible red".
     */
    FlashMessage.PulseColor = function(elems, color) {
        var $elems = $(elems);
        color = color || "yellow";
        if($elems.length) {
            $elems.each(function(){
                var $this = $(this);
                var originalColor = $this.css('backgroundColor');
                $this.animate({
                    backgroundColor: color
                },'normal','linear',function(){
                    $(this).animate({
                        backgroundColor:originalColor
                    });
                });
            });
        }
    };
    new FlashMessage();

    Application.PadZeroes = function(number, maxlength) {
        var str = '' + number;
        while (str.length < maxlength) {
            str = '0' + str;
        }
        return str;
    };

    Application.validateMonth = function(year, month, day) {
        year = year +'';
        if (year.length == 2) year = '20'+year;
        var int_m = new Date('2011', parseInt(month)-1, 1);
        var int_d = new Date(year, parseInt(month)-1, day);
        if (int_d.getMonth() < int_m.getMonth()) return -1;
        if (int_d.getMonth() > int_m.getMonth()) return 1;
        return 0;
    };

    Application.validateYear = function(year, month, day) {
        year = year +'';
        if (year.length == 2) year = '20'+year;
        var int_y = new Date(year, 1, 1);
        var int_d = new Date(year, month-1, day);
        if (int_d.getFullYear() < int_y.getFullYear()) return -1;
        if (int_d.getFullYear() > int_y.getFullYear()) return 1;
        return 0;
    };

    Application.dateDiffDays = function(start, end) {
        var one_day=1000*60*60*24;
        return (start.getTime()-end.getTime())/(one_day);
    };

    Application.dateDiffHours = function(start, end) {
        var one_hour=1000*60*60;
        return (start.getTime()-end.getTime())/(one_hour);
    };

    Application.dateDiffMinutes = function(start, end) {
        var one_minute=1000*60;
        return (start.getTime()-end.getTime())/(one_minute);
    };

    Application.parseTemplate = function(object, templateString){

        var template = templateString;
        template = "<div>" + template + "</div>";
        var $template = $(template);
        $template.find("*[rel='name-to-id']").each(function(){
            var $this = $(this);
            $this.attr('id', $this.attr('name'));
        });

        templateString = $template.html();
        if (object) {
            for (var prop in object) {
                if (object.hasOwnProperty(prop)) {
                    templateString = templateString.replace("{"+prop+"}", object[prop]);
                }
            }
        }
        return templateString;
    };

});

