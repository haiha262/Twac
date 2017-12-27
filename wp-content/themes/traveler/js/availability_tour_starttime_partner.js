jQuery(function($) {
    if ($(".st_partner_avaiablity.edit-tours").length < 1) return;
    $('.date-picker').datepicker({
        dateFormat: "mm/dd/yy",
        weekStart: 1
    });
    var TourStarttimeCalendar = function(container) {
        var self = this;
        this.container = container;
        this.calendar = null;
        this.form_container = null;
        this.init = function() {
            self.container = container;
            self.calendar = $('.calendar-starttime-content', self.container);
            self.form_container = $('.calendar-starttime-form', self.container);
            setCheckInOut('', '', self.form_container);
            self.initCalendar()
        }
        this.initCalendar = function() {
            var hide_adult = self.calendar.data('hide_adult');
            var hide_children = self.calendar.data('hide_children');
            var hide_infant = self.calendar.data('hide_infant');
            self.calendar.fullCalendar({
                firstDay: 1,
                lang: st_params.locale,
                timezone: st_timezone.timezone_string,
                customButtons: {
                    reloadButton: {
                        text: st_params.text_refresh,
                        click: function() {
                            self.calendar.fullCalendar('refetchEvents')
                        }
                    }
                },
                header: {
                    left: 'today,reloadButton',
                    center: 'title',
                    right: 'prev, next'
                },
                contentHeight: 560,
                selectable: !0,
                select: function(start, end, jsEvent, view) {
                    var start_date = new Date(start._d).toString("MM");
                    var end_date = new Date(end._d).toString("MM");
                    var start_year = new Date(start._d).toString("yyyy");
                    var end_year = new Date(end._d).toString("yyyy");
                    var today = new Date().toString("MM");
                    var today_year = new Date().toString("yyyy");
                    if ((start_date < today && start_year <= today_year) || (end_date < today && end_year <= today_year)) {
                        self.calendar.fullCalendar('unselect');
                        setCheckInOut('', '', self.form_container)
                    } else {
                        var zone = moment(start._d).format('Z');
                        zone = zone.split(':');
                        zone = "" + parseInt(zone[0]) + ":00";
                        var check_in = moment(start._d).utcOffset(zone).format("MM/DD/YYYY");
                        var check_out = moment(end._d).utcOffset(zone).subtract(1, 'day').format("MM/DD/YYYY");
                        setCheckInOut(check_in, check_out, self.form_container)
                    }
                    var zone = moment(start._d).format('Z');
                    zone = zone.split(':');
                    zone = "" + parseInt(zone[0]) + ":00"
                },
                events: function(start, end, timezone, callback) {
                    $.ajax({
                        url: ajaxurl,
                        dataType: 'json',
                        type: 'post',
                        data: {
                            action: 'st_get_availability_starttime_tour',
                            tour_id: self.container.data('post-id'),
                            start: start.unix(),
                            end: end.unix()
                        },
                        success: function(doc) {
                            if (typeof doc == 'object') {
                                callback(doc)
                            }
                        },
                        error: function(e) {
                            alert('Can not get the availability slot. Lost connect with your sever')
                        }
                    })
                },
                eventClick: function(event, element, view) {
                    var zone = moment(event.start).format('Z');
                    zone = zone.split(':');
                    zone = "" + parseInt(zone[0]) + ":00";
                    self.calendar.trigger('st.click.eventcalendar.front', [moment(event.start).utcOffset(zone), moment(event.start).utcOffset(zone), element, view])
                },
                eventRender: function(event, element, view) {

                    var html = '';
                    if (event.status == 'available') {
                        if (event.starttime != '') {
                            html += '<div class="starttime-tag price">Start time: ' + event.starttime + '</div>';
                        }
                    }
                    $('.fc-content', element).html("<div class='bgr-main'>" + html + "</div>");


                    element.bind('click', function(calEvent, jsEvent, view) {
                        $('.fc-day-grid-event').removeClass('st-active');
                        $(this).addClass('st-active');
                        date = $.fullCalendar.moment(event.start._i).format("MM/DD/YYYY");
                        $('input#calendar_starttime_check_in').val(date).parent().show();
                        if (typeof event.end != 'undefined' && event.end && typeof event.end._i != 'undefined') {
                            date = new Date(event.end._i);
                            date.setDate(date.getDate() - 1);
                            date = $.fullCalendar.moment(date).format("MM/DD/YYYY");
                            $('input#calendar_starttime_check_out').val(date)
                        } else {
                            date = $.fullCalendar.moment(event.start._i).format("MM/DD/YYYY");
                            $('input#calendar_starttime_check_out').val(date)
                        }

                        var starttime_arr = event.starttime.split(', ');

                        starttime_arr = cleanArray(starttime_arr);

                        $('.calendar-starttime-wraper').not('.starttime-origin').remove();
                        $('.calendar-starttime-wraper.starttime-origin').find('select.calendar_starttime_hour').attr('name', 'calendar_starttime_hour[]');
                        $('.calendar-starttime-wraper.starttime-origin').find('select.calendar_starttime_minute').attr('name', 'calendar_starttime_minute[]');

                        if(starttime_arr.length > 0) {
                            for (var i = 0; i < (starttime_arr.length - 1); i++) {
                                $('.calendar-starttime-wraper.starttime-origin').clone(true).show().removeClass('starttime-origin').insertBefore('#calendar-add-starttime');
                            }
                        }

                        $('.calendar-starttime-wraper').show();
                        $('.calendar-starttime-wraper').each(function(index, value){
                            if(starttime_arr.length > 0){
                                var starttime_string = starttime_arr[index];
                                var starttime_sub_arr = starttime_string.split(':');
                                $('.calendar-starttime-wraper .calendar_starttime_hour').eq(index).val(starttime_sub_arr[0]);
                                $('.calendar-starttime-wraper .calendar_starttime_minute').eq(index).val(starttime_sub_arr[1]);
                            }else{
                                $('.calendar-starttime-wraper .calendar_starttime_hour').eq(index).val('00');
                                $('.calendar-starttime-wraper .calendar_starttime_minute').eq(index).val('00');
                            }
                        });

                        $('#calendar_status option[value='+event.status+']', self.form_container).prop('selected');
                    });
                    self.calendar.trigger('st.render.eventcalendar.frontend', [event, element, view])
                },
                loading: function(isLoading, view) {
                    if (isLoading) {
                        $('.calendar-starttime-wrapper-inner .overlay-form').fadeIn()
                    } else {
                        $('.calendar-starttime-wrapper-inner .overlay-form').fadeOut()
                    }
                },
            })
        }
    };

    function setCheckInOut(check_in, check_out, form_container) {
        $('#calendar_starttime_check_in', form_container).val(check_in);
        $('#calendar_starttime_check_out', form_container).val(check_out)
    }

    function resetForm(form_container) {
        $('#calendar_check_in', form_container).val('');
        $('#calendar_check_out', form_container).val('');
        $('#calendar_adult_price', form_container).val('');
        $('#calendar_child_price', form_container).val('');
        $('#calendar_infant_price', form_container).val('');
        $('#calendar_number', form_container).val('')
    }

    function cleanArray(actual) {
        var newArray = new Array();
        for (var i = 0; i < actual.length; i++) {
            if (actual[i]) {
                newArray.push(actual[i]);
            }
        }
        return newArray;
    }
    jQuery(document).ready(function($) {
        if ($('a[href="#starttime_tab"]').length) {
            $('a[href="#starttime_tab"]').click(function(event) {
                setTimeout(function() {
                    $('.calendar-starttime-content', '.calendar-starttime-wrapper').fullCalendar('today')
                }, 1000)
            })
        }
        $('.calendar-starttime-wrapper').each(function(index, el) {
            var t = $(this);
            var tour = new TourStarttimeCalendar(t);
            tour.init();
            var flag_submit = !1;
            $('#calendar_starttime_submit', t).click(function(event) {
                $('.calendar-starttime-wrapper-inner .overlay-form').fadeIn();
                var data = $('input, select', '.calendar-starttime-form').serializeArray();
                data.push({
                    name: 'action',
                    value: 'st_add_custom_starttime_tour'
                });
                $('.form-message', t).attr('class', 'form-message').find('p').html('');
                $('.overlay', self.container).addClass('open');
                if (flag_submit) return !1;
                flag_submit = !0;
                $.post(ajaxurl, data, function(respon, textStatus, xhr) {
                    if (typeof respon == 'object') {
                        if (respon.status == 1) {
                            resetForm(t);
                            $('.calendar-starttime-content', t).fullCalendar('refetchEvents');
                        } else {
                            $('.calendar-starttime-content', t).fullCalendar('refetchEvents');
                            $('.form-message', t).addClass(respon.type).find('p').html(respon.message);
                            $('.overlay', self.container).removeClass('open')
                        }
                    } else {
                        $('.overlay', self.container).removeClass('open')
                    }
                    flag_submit = !1
                }, 'json');
                return !1
            });

            $('body').on('calendar.change_month', function(event, value){
            	var date = tour.calendar.fullCalendar('getDate');
            	var month = date.format('M');
            	date = date.add(value-month, 'M');
            	tour.calendar.fullCalendar( 'gotoDate', date.format('YYYY-MM-DD') );
            });
        });
        if ($('select#type_tour').length && $('select#type_tour').val() == 'daily_tour') {
            $('input#calendar_groupday').prop('checked', !1).parents('.form-group').hide()
        } else {
            $('input#calendar_groupday').parents('.form-group').show()
        }
        $('select#type_tour').change(function(event) {
            tour_type = $(this).val();
            if (tour_type == 'daily_tour') {
                $('input#calendar_groupday').prop('checked', !1).parents('.form-group').hide()
            } else {
                $('input#calendar_groupday').parents('.form-group').show()
            }
        })
    });

    //XKEI - Add script for sarttime form
    $('#calendar-add-starttime').click(function(){
        if(!$('.calendar-starttime-wraper.starttime-origin').is(":visible")) {
            $('.calendar-starttime-wraper.starttime-origin').find('select.calendar_starttime_hour').attr('name', 'calendar_starttime_hour[]');
            $('.calendar-starttime-wraper.starttime-origin').find('select.calendar_starttime_minute').attr('name', 'calendar_starttime_minute[]');
        }
        $('.calendar-starttime-wraper.starttime-origin').clone(true).show().removeClass('starttime-origin').insertBefore('#calendar-add-starttime');
        if(!$('.calendar-starttime-wraper.starttime-origin').is(":visible")) {
            $('.calendar-starttime-wraper.starttime-origin').find('select.calendar_starttime_hour').attr('name', '');
            $('.calendar-starttime-wraper.starttime-origin').find('select.calendar_starttime_minute').attr('name', '');
        }
    });
    $(document).on('click', '.calendar-remove-starttime', function () {
        if($(this).parent().hasClass('starttime-origin')){
            $(this).parent().hide();
            $(this).parent().find('select.calendar_starttime_hour').attr('name', '');
            $(this).parent().find('select.calendar_starttime_minute').attr('name', '');
        }else{
            $(this).parent().remove();
        }
    });
})