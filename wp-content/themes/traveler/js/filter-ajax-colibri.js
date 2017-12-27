(function ($) {
    $(document).ready(function () {
        $('.bookingdc-num-children').change(function () {
            var t = $(this);
            var af = $('.bookingdc-age-children');

            var number_child = t.val();

            if (number_child > 0) {
                var te = '';
                for (var i = 0; i < number_child; i++) {
                    te += '<select name="age">';
                    for (var j = 0; j < 18; j++) {
                        te += '<option value="' + j + '">' + j + '</option>';
                    }
                    te += '</select>';
                }
                af.show().children('#bookingdc-age-select').html(te);
            } else {
                af.hide().children('#bookingdc-age-select').html('');
            }
        });

        $('.bookingdc-start').change(function () {
            $('input[name="checkin_monthday"]').remove();
            $('input[name="checkin_month"]').remove();
            $('input[name="checkin_year"]').remove();

            var start = $(this).datepicker("getDate");
            var ci_dd = start.getDate();
            var ci_mm = start.getMonth() + 1;
            var ci_yy = start.getFullYear();

            var ci_te = '';

            if ($('input[name="checkin_monthday"]').length == 0 && $('input[name="checkin_month"]').length == 0 && $('input[name="checkin_year"]').length == 0) {
                ci_te += '<input type="hidden" name="checkin_monthday" value="' + ci_dd + '"/>';
                ci_te += '<input type="hidden" name="checkin_month" value="' + ci_mm + '"/>';
                ci_te += '<input type="hidden" name="checkin_year" value="' + ci_yy + '"/>';
                $('.main-bookingdc-search').append(ci_te);
            }
        });

        $('.bookingdc-end').change(function () {
            $('input[name="checkout_monthday"]').remove();
            $('input[name="checkout_month"]').remove();
            $('input[name="checkout_year"]').remove();

            var end = $(this).datepicker("getDate");
            var co_dd = end.getDate();
            var co_mm = end.getMonth() + 1;
            var co_yy = end.getFullYear();

            var co_te = '';

            if ($('input[name="checkout_monthday"]').length == 0 && $('input[name="checkout_month"]').length == 0 && $('input[name="checkout_year"]').length == 0) {
                co_te += '<input type="hidden" name="checkout_monthday" value="' + co_dd + '"/>';
                co_te += '<input type="hidden" name="checkout_month" value="' + co_mm + '"/>';
                co_te += '<input type="hidden" name="checkout_year" value="' + co_yy + '"/>';

                $('.main-bookingdc-search').append(co_te);
            }
        });

        //////////////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Remove city_code from url when change this
         */
        $('#cl-country').change(function (e) {
            if ($(this).value != 0) {
                $('input[name="city_code"]').not('.i-check').remove();
                $('#cba-city-form').submit();
            }
        });

        /**
         * Search room ajax button
         */
        $('#cba-search-room').click(function (e) {
            e.preventDefault();
            var hotel_code = $('#cba-search-room').data('hotel-code');
            var start = $('#field-cba-hotel-checkin').val();
            var end = $('#field-cba-hotel-checkout').val();
            //var rate_plan = $('#field-hotel-rate').val();
            var rooms = 0;

            if (!$('.cba-select-rooms .btn-group-select-num').hasClass('hidden')) {
                $('.cba-select-rooms .btn-group-select-num .btn-primary').each(function () {
                    if ($(this).hasClass('active')) {
                        rooms = $(this).find('input').val();
                    }
                });
            } else {
                rooms = $('#field-hotel-room').val();
            }

            /* Select people type */
            var peoples = [];
            $('#cba-age-select .cba-select-people').each(function () {
                var people = 0;
                if (!$(this).find('.btn-group-select-num').hasClass('hidden')) {
                    $(this).find('.btn-group-select-num .btn-primary').each(function () {
                        if ($(this).hasClass('active')) {
                            people = $(this).find('input').val();
                        }
                    });
                } else {
                    people = $(this).find('#field-hotel-people-' + $(this).data('age-code')).val();
                }
                peoples.push($(this).data('age-code') + '|' + people);
            });

            $('.cba-hotel-detail .overlay-form').fadeIn();
            $('.cba-list-room-of-hotel .overlay-form').fadeIn();

            $.ajax({
                url: st_params.ajax_url,
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'cl_get_list_room_ajax',
                    hotel_code: hotel_code,
                    start: start,
                    end: end,
                    room: rooms,
                    people: peoples
                },
                success: function (doc) {
                    $('.booking-list.loop-room').html(doc['data']);
                    $('.cba-hotel-detail .overlay-form').fadeOut();
                    $('.cba-list-room-of-hotel .overlay-form').fadeOut();
                    $('.booking-item-features li').tooltip({
                        placement: 'top'
                    });
                },
                complete: function () {
                }
            });
        });

        $(document).on('change', '.cba-select-rooms', function () {
            var num_room = $(this).val();
            var base_price = $(this).data('base-price');
            $(this).parent().parent().parent().parent().find('.booking-item-price').text(num_room * base_price);
        });

        /**
         * Page checkout
         */
        $(document).on('click', '.cba-btn-checkout-page', function (e) {
            var dataRoomRates = [];
            $(this).closest('.booking-item').find('select').each(function () {
                if ($(this).val() != 0) {
                    var sub_arr = [];
                    sub_arr.push($(this).val());
                    sub_arr.push($(this).data('rate-code'));
                    sub_arr.push($(this).data('price'));
                    sub_arr.push($(this).data('rate-name'));
                    dataRoomRates.push(sub_arr);
                }
            });
            //Age code
            var peoples = '';
            $('#cba-age-select .cba-select-people').each(function () {
                var people = 0;
                if (!$(this).find('.btn-group-select-num').hasClass('hidden')) {
                    $(this).find('.btn-group-select-num .btn-primary').each(function () {
                        if ($(this).hasClass('active')) {
                            people = $(this).find('input').val();
                        }
                    });
                } else {
                    people = $(this).find('#field-hotel-people-' + $(this).data('age-code')).val();
                }
                peoples += $(this).data('age-code') + '|' + people + ',';
            });

            var hotel_name = $('.featured_single').text();
            $(this).closest('#cba-form-checkout-page').find('input.cba-form-room-rates').val(JSON.stringify(dataRoomRates));
            $(this).closest('#cba-form-checkout-page').find('input.cba-form-age-code').val(JSON.stringify(peoples));
            $(this).closest('#cba-form-checkout-page').find('input.cba-form-hotel-name').val(hotel_name);
            $(this).closest('#cba-form-checkout-page').find('input.cba-form-hotel-thumb').val($('#cba-co-thumb-hotel').val());
        });

        /**
         * Modal checkout
         */
        $(document).on('click', '.btn_cba_hotel_booking', function (e) {
            e.preventDefault();
            var rate_plan_code = [];
            var dataRoomRate = [];
            $(this).closest('.booking-item').find('select').each(function () {
                if ($(this).val() != 0) {
                    rate_plan_code.push($(this).data('rate-code'));
                    var sub_arr = [];
                    sub_arr.push($(this).val());
                    sub_arr.push($(this).data('rate-code'));
                    sub_arr.push($(this).data('price'));
                    sub_arr.push($(this).data('rate-name'));
                    dataRoomRate.push(sub_arr);
                }
            });

            var roomTypeCode = $(this).data('room-type-code');
            var ratePlanCode = JSON.stringify(rate_plan_code);
            var roomRates = JSON.stringify(dataRoomRate);
            var timeSpanStart = $('#field-cba-hotel-checkin').val();
            var timeSpanEnd = $('#field-cba-hotel-checkout').val();
            var hotelCode = $(this).data('hotel-code');
            var room_name = $(this).data('room-name');
            var hotel_name = $('.featured_single').text();
            var hotel_thumb = $('#cba-co-thumb-hotel').val();
            var room_thumb = '';
            if ($(this).closest('.booking-item').find('.cba-room-item-thumb').length > 0) {
                var room_thumb = $(this).closest('.booking-item').find('.cba-room-item-thumb').attr('src');
            }
            /* Select people type */
            var peoples = '';
            $('#cba-age-select .cba-select-people').each(function () {
                var people = 0;
                if (!$(this).find('.btn-group-select-num').hasClass('hidden')) {
                    $(this).find('.btn-group-select-num .btn-primary').each(function () {
                        if ($(this).hasClass('active')) {
                            people = $(this).find('input').val();
                        }
                    });
                } else {
                    people = $(this).find('#field-hotel-people-' + $(this).data('age-code')).val();
                }
                peoples += $(this).data('age-code') + '|' + people + ',';
            });

            $('#cba-checkout-form #cba_co_room_type_code').val(roomTypeCode);
            $('#cba-checkout-form #cba_co_rate_plan_code').val(ratePlanCode);
            $('#cba-checkout-form #cba_co_room_rates').val(roomRates);
            $('#cba-checkout-form #cba_co_time_span_start').val(timeSpanStart);
            $('#cba-checkout-form #cba_co_time_span_end').val(timeSpanEnd);
            $('#cba-checkout-form #cba_co_hotel_code').val(hotelCode);
            $('#cba-checkout-form #cba_co_hotel_name').val(hotel_name);
            $('#cba-checkout-form #cba_co_room_name').val(room_name);
            $('#cba-checkout-form #cba_co_code_age').val(peoples);

            $('#cba-checkout-form #cba_co_room_thumb').val(room_thumb);
            $('#cba-checkout-form #cba_co_hotel_thumb').val(hotel_thumb);
            $('#cba-checkout-form #cba_currency').val($(this).data('currency'));

        });

        /**
         * Room detail condition toggle
         */
        $(document).on('click', '.cba-condition-push', function () {
            var t = $(this);
            var data = $(this).data();
            data['action'] = 'cl_get_rates_condition_detail';
            t.parent().next('tr').slideToggle();
            var epthtml = t.parent().next('tr').find('#cba-condition-detail-content').is(':empty');
            if(epthtml) {

                t.parent().next('tr').find('#cba-condition-detail-content').show().html('');
                t.parent().next('tr').find('.overlay-form').show();
                $.ajax({
                    url: st_params.ajax_url,
                    dataType: 'json',
                    type: 'post',
                    data: data,
                    success: function (doc) {
                        t.parent().next('tr').find('#cba-condition-detail-content').html(doc.data);
                        t.parent().next('tr').find('.overlay-form').hide();
                    },
                    complete: function () {
                    }
                });
            }
        });

        /**
         * Check select number room for per rate plan
         */
        $(document).on('change', '.cba-select-numbr-room', function () {
            var total = $(this).data('total-unit');
            var sum = 0;
            $(this).parent().parent().parent().find('select').each(function () {
                sum = sum + parseInt($(this).val());
            });

            if (sum == 0) {
                $(this).closest('.booking-item').find('.btn_cba_hotel_booking, .cba-btn-checkout-page').addClass('disabled');
            } else {
                $(this).closest('.booking-item').find('.btn_cba_hotel_booking, .cba-btn-checkout-page').removeClass('disabled');
            }

            if (sum > total) {
                alert('Max ' + total + ' room(s) available in  selected category.');

                var sum_not_this = 0;
                $(this).parent().parent().parent().find('select').not($(this)).each(function () {
                    sum_not_this = sum_not_this + parseInt($(this).val());
                });

                var pos = 0;
                if (sum_not_this < total) {
                    pos = total - sum_not_this;
                }

                $(this).val(pos);
            }
        });

        $(document).on('change', '.cba-select-numbr-room-modify', function () {
            var sum = 0;
            $(this).parent().parent().parent().find('select').each(function () {
                sum = sum + parseInt($(this).val());
            });
            if (sum == 0) {
                $('.cba-btn-st-checkout-submit-modify').addClass('disabled');
            } else {
                $('.cba-btn-st-checkout-submit-modify').removeClass('disabled');
            }
        });

        /**
         * Country home select
         */
        $('#cba-home-country').change(function () {
            var country_code = $(this).val();
            $('.cba-search-advance.expanded .overlay-form').show();
            $('#cba-home-city-alert').hide();
            $('#cba-home-city').show();
            $.ajax({
                url: st_params.ajax_url,
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'cl_get_city_code',
                    country_code: country_code,
                },
                success: function (doc) {
                    $('#cba-home-city').empty();
                    if (doc.posts_found > 0) {
                        var te = '';
                        for (var i = 0; i < doc.posts.length; i++) {
                            te += '<option value="' + doc.posts[i]['code'] + '">' + doc.posts[i]['name'] + '</option>';
                        }
                        $('#cba-home-city').append(te);
                    } else {
                        $('#cba-home-city').hide();
                        $('#cba-home-city-alert').show();
                    }
                    //$('#cover-list-city').fadeOut();
                    $('.cba-search-advance.expanded .overlay-form').hide();
                },
                complete: function () {
                }
            });
        });

        //Fix zzz
        /*$('.st_check_term_conditions .i-check').iCheck({
            checkboxClass: 'i-check-wrapper',
            radioClass: 'i-radio'
        });*/

        /**
         * Check active credit card
         */
        $(document).on('click', '.cba-credit-card-type ul li', function () {
            $('.cba-credit-card-type ul li').not(this).removeClass('active');
            $(this).addClass('active');
            $('input[name="cba_card_type"]').val($(this).data('card'));
        });

        //Fix zzz
        //$('[data-toggle="tooltip"]').tooltip();

        $('.cba-btn-st-checkout-submit').click(function (e) {
            e.preventDefault();
            var message = '';
            var validate = 0;
            $('#cba-checkout-form input.required:visible').each(function () {
                if ($(this).val() == '') {
                    $(this).addClass('error');
                    validate = 1;
                } else {
                    $(this).removeClass('error');
                }
            });

            if (validate == 1) {
                validate = 0;
                $('#cba-checkout-form .form_alert').removeClass('hidden alert-success').addClass('alert-danger').html(st_checkout_text.validate_form).show();
                return;
            } else {
                var checkEmail = ValidateEmail($('#cba-checkout-form #field-cba_email').val());
                if (!checkEmail) {
                    $('#cba-checkout-form #field-cba_email').addClass('error');
                    $('#cba-checkout-form .form_alert').removeClass('hidden alert-success').addClass('alert-danger').html('Email validation').show();
                    return;
                } else {
                    $('#cba-checkout-form #field-cba_email').removeClass('error');
                }

                if (!$('.cba_st_check_term_conditions .i-check').hasClass('checked')) {
                    $('#cba-checkout-form .form_alert').removeClass('hidden alert-success').addClass('alert-danger').html(st_checkout_text.error_accept_term).show();
                    return;
                }
                $('#cba-checkout-form input.required').removeClass('error');
            }

            var data = $('#cba-checkout-form:visible').serializeArray();
            $('.cba-btn-st-checkout-submit i').css({'display': 'inline-block'});
            $('#cba-checkout-form .overlay-form').show();
            $.ajax({
                url: st_params.ajax_url,
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'cl_check_out_submit',
                    data: data,
                },
                success: function (doc) {
                    if (doc.status == false) {
                        $('#cba-checkout-form .form_alert').removeClass('hidden alert-success').addClass('alert-danger').html(doc.message).show();
                    } else {
                        $('#cba-checkout-form .form_alert').removeClass('hidden alert-danger').addClass('alert-success').html(doc.message).show();
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    }
                    $('.cba-btn-st-checkout-submit i').css({'display': 'none'});
                    $('#cba-checkout-form .overlay-form').hide();
                },
                complete: function () {
                }
            });
        });

        $(document).on('click', '.cba-btn-st-checkout-submit-modify', function (e) {
            e.preventDefault();
            var message = '';
            var validate = 0;
            $('#cba-checkout-form-modify input.required:visible').each(function () {
                if ($(this).val() == '') {
                    $(this).addClass('error');
                    validate = 1;
                } else {
                    $(this).removeClass('error');
                }
            });

            if (validate == 1) {
                validate = 0;
                $('#cba-checkout-form-modify .form_alert').removeClass('hidden alert-success').addClass('alert-danger').html(st_checkout_text.validate_form).show();
                return;
            } else {
                var checkEmail = ValidateEmail($('#cba-checkout-form-modify #field-cba_email').val());
                if (!checkEmail) {
                    $('#cba-checkout-form-modify #field-cba_email').addClass('error');
                    $('#cba-checkout-form-modify .form_alert').removeClass('hidden alert-success').addClass('alert-danger').html('Email validation').show();
                    return;
                } else {
                    $('#cba-checkout-form-modify #field-cba_email').removeClass('error');
                }

                if (!$('.cba_st_check_term_conditions .i-check').hasClass('checked')) {
                    $('#cba-checkout-form-modify .form_alert').removeClass('hidden alert-success').addClass('alert-danger').html(st_checkout_text.error_accept_term).show();
                    return;
                }
                $('#cba-checkout-form-modify input.required').removeClass('error');
            }

            //Age code calc
            var peoples = [];
            $('#cba-age-select-modify .cba-select-people').each(function () {
                var people = 0;
                people = $(this).find('#field-hotel-people-' + $(this).data('age-code')).val();
                peoples.push($(this).data('age-code') + '|' + people);
            });

            $('#cba-checkout-form-modify #cba_co_code_age').val(peoples.toString());
            $('#cba-checkout-form-modify #cba_co_time_span_start').val($('.cba-modify-booking-date #field-cba-hotel-checkin').val());
            $('#cba-checkout-form-modify #cba_co_time_span_end').val($('.cba-modify-booking-date #field-cba-hotel-checkout').val());

            //Room rates
            var dataRoomRate = [];
            $('.cba-room-conditions-modify').find('select.cba-select-numbr-room-modify').each(function () {
                if ($(this).val() != 0) {
                    var sub_arr = [];
                    sub_arr.push($(this).val());
                    sub_arr.push($(this).data('rate-code'));
                    sub_arr.push($(this).data('price'));
                    sub_arr.push($(this).data('rate-name'));
                    dataRoomRate.push(sub_arr);
                }
            });
            var roomRates = JSON.stringify(dataRoomRate);
            $('#cba-checkout-form-modify #cba_co_room_rates').val(roomRates);

            var data = $('#cba-checkout-form-modify:visible').serializeArray();
            $('.cba-btn-st-checkout-submit-modify i').css({'display': 'inline-block'});
            $('#cba-checkout-form-modify .overlay-form').show();
            $.ajax({
                url: st_params.ajax_url,
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'cl_check_out_submit_modify',
                    data: data,
                },
                success: function (doc) {
                    console.log(doc);
                    if (doc.status == false) {
                        $('#cba-checkout-form-modify .form_alert').removeClass('hidden alert-success').addClass('alert-danger').html(doc.message).show();
                    } else {
                        $('#cba-checkout-form-modify .form_alert').removeClass('hidden alert-danger').addClass('alert-success').html(doc.message).show();
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    }
                    $('.cba-btn-st-checkout-submit-modify i').css({'display': 'none'});
                    $('#cba-checkout-form-modify .overlay-form').hide();
                },
                complete: function () {
                }
            });
        })

        function ValidateEmail(mail) {
            if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
                return (true)
            }
            return (false)
        }

        $('.cba-home-amenity .i-check-amenity').on('ifClicked', function (event) {
            var $this = $(this);
            var $value = '';
            $this.parent().parent().parent().parent().parent().find('.i-check-amenity').each(function () {
                var $this2 = $(this);
                setTimeout(function () {
                    if ($this2.prop('checked')) {
                        $value += $this2.val() + ",";
                    }
                }, 100);
            });

            setTimeout(function () {
                $('#cba-home-amenity-total').val($value.substr(0, $value.length - 1));
            }, 200)
        });

        /**
         * Load more bookng history page
         */
        var paged = 0;
        $(document).on('click', '.btn_cba_load_more_history_book', function () {
            paged++;
            var post_per_page = 5;
            $('.btn_cba_load_more_history_book i').css({'display': 'inline-block'});

            $.ajax({
                url: st_params.ajax_url,
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'cl_load_more_history',
                    paged: paged,
                    post_per_page: post_per_page,
                    stt: $('#cba_data_history_book .cba-stt').last().text(),
                },
                success: function (doc) {
                    $('table #cba_data_history_book').append(doc.data);
                    $('.btn_cba_load_more_history_book i').hide();
                },
                complete: function () {
                }
            });

        });

        /**
         * View popup detail
         */
        $(document).on('click', '.cba-show-room-detail', function (e) {
            e.preventDefault();

            var div = $('#modalCBARoomDetail');
            var room_code = $(this).data('room-code');
            var hotel_code = $(this).data('hotel-code');
            var rate_plan = $(this).data('rate-plan-code');
            var start = $(this).data('start');
            var end = $(this).data('end');
            var number_room = $(this).data('number-room');

            div.find('#cba-room-detail').html('');

            div.find('.overlay-form').show();
            $.ajax({
                url: st_params.ajax_url,
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'cl_get_detail_room',
                    room_code: room_code,
                    hotel_code: hotel_code,
                    rate_plan: rate_plan,
                    start: start,
                    end: end,
                    number_room: number_room,
                },
                success: function (doc) {
                    if (doc.status == true) {
                        div.find('#cba-room-detail').html(doc.message);
                        $('.booking-item-features li').tooltip({
                            placement: 'top'
                        });
                        $('[data-toggle="tooltip"]').tooltip();

                        $('.popup-gallery').each(function () {
                            $(this).magnificPopup({
                                delegate: 'a.popup-gallery-image',
                                type: 'image',
                                gallery: {
                                    enabled: true
                                }
                            });
                        });

                        $('.fotorama').fotorama();

                    } else {
                        alert(doc.message);
                    }
                    div.find('.overlay-form').hide();
                },
                complete: function () {
                }
            });
        });

        //Fix zzz
        if($('#cba-filter-city .cba-list-city, #cba-list-amnity, .cba-home-amenity').length > 0) {
            if (jQuery().niceScroll) {
                $('#cba-filter-city .cba-list-city, #cba-list-amnity, .cba-home-amenity').niceScroll({autohidemode: false});
            }
        }

        $('#cl-country').change(function (e) {
            return;
            e.preventDefault();
            var country_code = $(this).val();
            //$('#cba-filter-city .cba-list-city').empty();
            $('#cover-list-city').fadeIn();
            $.ajax({
                url: st_params.ajax_url,
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'cl_get_city_code',
                    country_code: country_code,
                },
                success: function (doc) {
                    $('#cba-filter-city .cba-list-city').empty();
                    if (doc.posts_found > 0) {
                        for (var i = 0; i < doc.posts.length; i++) {
                            var te = '';
                            te += '<div class="checkbox">';
                            te += '<label>';
                            te += '<input  value="' + doc.posts[i]['code'] + '" name="" data-url="#" class="i-check i-check-tax" type="checkbox"/>';
                            te += doc.posts[i]['name'];
                            te += '</label>';
                            te += '</div>';
                            $('#cba-filter-city .cba-list-city').append(te);
                        }
                        $('#cba-filter-city .cba-list-city .i-check').iCheck({
                            checkboxClass: 'i-check',
                            radioClass: 'i-radio'
                        });
                    } else {
                        $('#cba-filter-city .cba-list-city').append($('#cba-no-city').val());
                    }
                    $('#cover-list-city').fadeOut();
                },
                complete: function () {
                }
            });
        });

        $('.cba-load-more').click(function (e) {
            $('#cba-list-amnity').css({'height': '600px'});
        });

        /**
         * Cancel booking by id and type res
         */
        $(document).on('click', '.cba-cancel-booking', function (e) {
            e.preventDefault();
            var r = confirm("Are you sure want to cancel it?");
            if (r == false) {
                return;
            }
            $(this).find('i').css({'display': 'inline-block'});
            $(".cba-alert-cancel-booking").hide();
            $(".cba-alert-cancel-booking").removeClass(function (index, className) {
                return (className.match(/(^|\s)alert-\S+/g) || []).join(' ');
            });
            $.ajax({
                url: st_params.ajax_url,
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'cl_cancel_reservation',
                    res_type: $(this).data('type'),
                    res_id: $(this).data('id'),
                    res_source: $(this).data('source'),
                    stas_id: $(this).data('stas-id'),
                },
                success: function (doc) {
                    $('.cba-alert-cancel-booking').addClass('alert-' + doc.status).html(doc.message).show();
                    $('.cba-cancel-booking').find('i').hide();
                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                },
                complete: function () {
                }
            });
        });

        /**
         * Delete row statis cba booking history
         */
        $(document).on('click', '.cba-remove-booking', function (e) {
            e.preventDefault();
            var r = confirm("Are you sure want to delete it?");
            if (r == false) {
                return;
            }
            $(this).find('i').css({'display': 'inline-block'});
            $.ajax({
                url: st_params.ajax_url,
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'cl_remove_reservation',
                    stas_id: $(this).data('stas-id'),
                },
                success: function (doc) {
                    $('.cba-alert-cancel-booking').addClass('alert-' + doc.status).html(doc.message).show();
                    $('.cba-remove-booking').find('i').hide();
                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                },
                complete: function () {
                }
            });
        });

        /**
         * Modify booking
         */
        $(document).on('click', '.cba-modify-booking', function (e) {
            e.preventDefault();
            $(this).find('i').css({'display': 'inline-block'});
            $('#modalCBAModifyBooking .overlay-form').fadeIn();
            $.ajax({
                url: st_params.ajax_url,
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'cl_modify_reservation',
                    stas_id: $(this).data('stas-id'),
                },
                success: function (doc) {
                    if (doc.status == 'success') {
                        $('#cba-modify-booking-detail').html(doc.data);
                        $('#modalCBAModifyBooking .overlay-form').fadeOut();
                        $('#modalCBAModifyBooking .i-check').iCheck({
                            checkboxClass: 'i-check',
                            radioClass: 'i-radio'
                        });

                        //$('.cba-modify-booking-date input[name="start"], .cba-modify-booking-date input[name="end"]').datepicker({
                        //    format: 'dd/mm/yyyy',
                        //    startDate: get_current_date(),
                        //});
                    } else {
                        $('.cba-alert-cancel-booking').addClass('alert-' + doc.status).html(doc.data).show();
                    }
                    $('.cba-modify-booking').find('i').hide();
                },
                complete: function () {
                }
            });
        });

        function get_current_date() {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth() + 1; //January is 0!

            var yyyy = today.getFullYear();
            if (dd < 10) {
                dd = '0' + dd;
            }
            if (mm < 10) {
                mm = '0' + mm;
            }
            var today = dd + '/' + mm + '/' + yyyy;
            return today;
        }

        $('body').on('focus', '.cba-modify-booking-date input', function () {
            $(this).datepicker({
                format: 'dd/mm/yyyy',
                startDate: get_current_date(),
                autoclose: true
            }).on('change', function () {
                $('.cba-btn-st-checkout-submit-modify').addClass('disabled');
                $('#load_rates_data').show();
            });
        });

        $(document).on('click', '#load_rates_data', function () {

            var start = $('.cba-modify-booking-date').find('#field-cba-hotel-checkin').val();
            var end = $('.cba-modify-booking-date').find('#field-cba-hotel-checkout').val();
            var hotel_code = $('#load_hotel_code').val();
            var room_code = $('#load_room_code').val();
            var room_rates = $('#load_room_rates').val();
            $('#modalCBAModifyBooking .overlay-form').fadeIn();

            $.ajax({
                url: st_params.ajax_url,
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'cl_modify_reservation_load_cond',
                    start: start,
                    end: end,
                    hotel_code: hotel_code,
                    room_code: room_code,
                    room_rates: room_rates,
                },
                success: function (doc) {
                    if (doc.status == true) {
                        $('#ccond').html('');
                        $('#ccond').html(doc.message);
                        $('.cba-btn-st-checkout-submit-modify').removeClass('disabled');
                    } else {
                        alert(doc.message);
                    }
                    $('#modalCBAModifyBooking .overlay-form').fadeOut();
                    $('#load_rates_data').hide();
                },
                complete: function () {
                }
            });

        });

        function formatDate(date) {
            var d = new Date(date.split("/").reverse().join("-"));
            var dd = d.getDate();
            var mm = d.getMonth() + 1;
            var yy = d.getFullYear();
            var newdate = mm + "/" + dd + "/" + yy;
            return newdate;
        }
    });

})(jQuery)