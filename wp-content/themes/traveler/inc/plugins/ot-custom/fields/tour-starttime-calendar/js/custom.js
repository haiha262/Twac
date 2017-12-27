jQuery(function($){
	$('.date-picker').datepicker({
        dateFormat: "mm/dd/yy",
        firstDay: 1
    });

	var TourStartTimeCalendar = function(container){
		var self = this;
		this.container = container;
		this.calendar= null;
		this.form_container = null;

		this.init = function(){
			self.container = container;
			self.calendar = $('.calendar-starttime-content', self.container);
			self.form_container = $('.calendar-starttime-form', self.container);
			setCheckInOut('', '', self.form_container);
			self.initCalendar();
		}

		this.initCalendar = function(){
            var hide_adult = self.calendar.data('hide_adult');
            var hide_children = self.calendar.data('hide_children');
            var hide_infant = self.calendar.data('hide_infant');

			self.calendar.fullCalendar({
				firstDay: 1,
                lang:st_params.locale,
                timezone: st_timezone.timezone_string,

				//Refresh button calendar
				customButtons: {
			        reloadButton: {
                        text: st_params.text_refresh,
			            click: function() {
			                self.calendar.fullCalendar( 'refetchEvents' );
			            }
			        }
			    },
				header : {
				    left:   'today,reloadButton',
                    center: 'title',
                    right:  'prev, next'
				},
				//Change date month by select box
				selectable: true,
				select : function(start, end, jsEvent, view){
					var start_date = new Date(start._d).toString("MM");
					var end_date = new Date(end._d).toString("MM");
					var start_year = new Date(start._d).toString("yyyy");
					var end_year = new Date(end._d).toString("yyyy");
					var today = new Date().toString("MM");
					var today_year = new Date().toString("yyyy");
					if((start_date < today && start_year <= today_year) || (end_date < today && end_year <= today_year)){
						self.calendar.fullCalendar('unselect');
						setCheckInOut('', '', self.form_container);
					}else{
						var zone = moment(start._d).format('Z');
							zone = zone.split(':');
							zone = "" + parseInt(zone[0]) + ":00";
						var check_in = moment(start._d).utcOffset(zone).format("MM/DD/YYYY");
						var	check_out = moment(end._d).utcOffset(zone).subtract(1, 'day').format("MM/DD/YYYY");
						setCheckInOut(check_in, check_out, self.form_container);
					}

					//self.calendar.trigger('st.click.eventcalendar', [moment(start._d).utcOffset(zone), moment(end._d).utcOffset(zone), jsEvent, view]);
				},

				//Get data from db into calendar table
				events:function(start, end, timezone, callback) {
                    $.ajax({
                        url: ajaxurl,
                        dataType: 'json',
                        type:'post',
                        data: {
                            action: 'st_get_availability_starttime_tour',
                            tour_id: self.container.data('post-id'),
                            start: start.unix(),
                            end: end.unix()
                        },
                        success: function(doc){
                        	if(typeof doc == 'object'){
                            	callback(doc);
                        	}
                        },
                        error:function(e)
                        {
                            alert('Can not get the availability slot. Lost connect with your sever');
                        }
                    });
                },
				eventClick: function(event, element, view){
                    setCheckInOut(event.start.format('MM/DD/YYYY'),event.start.format('MM/DD/YYYY'),self.form_container);

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

                    $('#calendar_adult_price', self.form_container).val(event.adult_price);
                    $('#calendar_child_price', self.form_container).val(event.child_price);
                    $('#calendar_infant_price', self.form_container).val(event.infant_price);
                    $('#calendar_status option[value='+event.status+']', self.form_container).prop('selected');
                    var zone = moment(event.start).format('Z');
						zone = zone.split(':');
						zone = "" + parseInt(zone[0]) + ":00";
                    self.calendar.trigger('st.click.eventcalendar', [moment(event.start).utcOffset(zone), moment(event.start).utcOffset(zone), element, view]);
				},

                //Render starttime to table calendar
				eventRender: function(event, element, view){
                    var html = '';
                    if (event.status == 'available') {
                        if (event.starttime != '') {
                            html += '<div class="starttime-tag price">Start time: ' + event.starttime + '</div>';
                        }
                    }
                    $('.fc-content', element).html(html);
                    self.calendar.trigger('st.render.eventcalendar', [event, element, view]);
				},
                loading: function(isLoading, view){
                    if(isLoading){
                    	$('.overlay', self.container).addClass('open');
                    }else{
                    	$('.overlay', self.container).removeClass('open');
                    }
                },

			});
		}
	};

	function setCheckInOut(check_in, check_out, form_container){
		$('#calendar_starttime_check_in', form_container).val(check_in);
		$('#calendar_starttime_check_out', form_container).val(check_out);
	}
	function resetForm(form_container){
        //$('#calendar_starttime_check_in', form_container).val('');
        //$('#calendar_starttime_check_out', form_container).val('');
        //$('.calendar-starttime-wraper').not('.starttime-origin').remove();
        //$('.calendar-starttime-wraper .calendar_starttime_hour').eq(0).val('00');
        //$('.calendar-starttime-wraper .calendar_starttime_minute').eq(0).val('00');
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
		$('.calendar-starttime-wrapper').each(function(index, el) {
			var t = $(this);
			var tour = new TourStartTimeCalendar(t);
			tour.init();

			var flag_submit = false;
			$('#calendar_starttime_submit', t).click(function(event) {

				var data = $('input, select', '.calendar-starttime-form').serializeArray();
					data.push({
						name: 'action',
						value: 'st_add_custom_starttime_tour'
					});
				$('.form-message', t).attr('class', 'form-message').find('p').fadeIn().html('');
				$('.overlay', self.container).addClass('open');
				if(flag_submit) return false; flag_submit = true;
				$.post(ajaxurl, data, function(respon, textStatus, xhr) {
					if(typeof respon == 'object'){
						if(respon.status == 1){
							resetForm(t);
							$('.calendar-starttime-content', t).fullCalendar('refetchEvents');
                            $('.form-message', t).addClass(respon.type).find('p').html(respon.message);
                            setTimeout(function(){
                                $('.form-message', t).attr('class', 'form-message').find('p').fadeOut().html('');
                            }, 10000);
						}else{
							$('.form-message', t).addClass(respon.type).find('p').html(respon.message);
                            setTimeout(function(){
                                $('.form-message', t).attr('class', 'form-message').find('p').fadeOut().html('');
                            }, 10000);
							$('.overlay', self.container).removeClass('open');
						}
					}else{
						$('.overlay', self.container).removeClass('open');
					}

					flag_submit = false;
				}, 'json');
				return false;
			});
            $(document).on('click','.ui-tabs-anchor[href="#setting_starttime_tab"]',function(){
            	resetForm(t);
                $('.calendar-starttime-wraper').not('.starttime-origin').remove();
                tour.calendar.fullCalendar( 'refetchEvents' );
            });

            $('body').on('calendar.change_month', function(event, value){
            	var date = tour.calendar.fullCalendar('getDate');
            	var month = date.format('M');
            	date = date.add(value-month, 'M');
            	tour.calendar.fullCalendar( 'gotoDate', date.format('YYYY-MM-DD') );
            });

		});


		
		if($('select#type_tour').length && $('select#type_tour').val() == 'daily_tour'){
			$('input#calendar_groupday').prop('checked', false).parent().hide();
		}else{
			$('input#calendar_groupday').parent().show();
		}
		$('select#type_tour').change(function(event) {
			tour_type = $(this).val();
			if(tour_type == 'daily_tour'){
				$('input#calendar_groupday').prop('checked', false).parent().hide();
			}else{
				$('input#calendar_groupday').parent().show();
			}
		});
	});
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
});
