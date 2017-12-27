(function ($) {


    $(document).ready(function () {

        $('.input-daterange').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'mm/dd/yyyy',
        });

        $('#start').datepicker({
            autoclose: true,
        }).on('changeDate', function (ev) {
            $("#end").focus();
        });
        $('#end').datepicker();

        if($('.aclass').length > 0) {
            var arr = [];
            $('[class*="booking-"]').each(function () {
                for (var i = 0; i < this.classList.length; i++) {
                    if (jQuery.inArray(this.classList[i], arr) === -1) {
                        arr.push(this.classList[i]);
                    }
                }
            });

            for (var i = arr.length - 1; i--;) {
                if (arr[i] === "aclass") arr.splice(i, 1);
            }

            arr.forEach(function (element) {
                $('.' + element).first().append('<div class="arrow-right"></div>');
                $('.' + element).last().append('<div class="arrow-left"></div>');

                var arr_time = [];
                $('.' + element).each(function () {
                    arr_time.push($(this).data('time'));
                })
                $('.' + element).attr('data-min-max', Math.min.apply(null, arr_time) + ',' + Math.max.apply(null, arr_time));
            });


            var mouseX;
            var mouseY;
            $(document).mousemove(function (e) {
                mouseX = e.pageX;
                mouseY = e.pageY;
            });

            $('.aclass').mouseenter(function (e) {
                var class_string = $(this).attr('class');

                var class_arr = class_string.split(" ");

                for (var i = class_arr.length - 1; i--;) {
                    if (class_arr[i] === "aclass") class_arr.splice(i, 1);
                }

                var arr_id = [];
                class_arr.forEach(function (element) {
                    var thenum = element.replace(/^\D+/g, '');
                    if (thenum != '') {
                        arr_id.push(thenum);
                    }
                });
                //$('#cba-stas-hover').css({'top':mouseY,'left':mouseX}).fadeIn('slow');
                $("#cba-stas-hover #cba-stas-number-booking").text(arr_id.length);
                var arr_date = [];
                arr_date = $(this).data('min-max').split(",");
                var from = new Date(arr_date[0] * 1000);
                var dfrom = from.getDate() + '/' + (from.getMonth() + 1) + '/' + from.getFullYear();
                var to = new Date(arr_date[1] * 1000);
                var dto = to.getDate() + '/' + (to.getMonth() + 1) + '/' + to.getFullYear();


                $("#cba-stas-hover #cba-stas-date-booking").text(dfrom + ' - ' + dto);
                $("#cba-stas-hover").stop(true, true).fadeIn().offset({left: e.pageX, top: e.pageY});
            }).mouseleave(function () {
                $("#cba-stas-hover").stop(true, true).fadeOut();
            });

            $('.aclass').click(function () {
                var min_max = $(this).data('min-max');
                var class_string = $(this).attr('class');

                var class_arr = class_string.split(" ");

                for (var i = class_arr.length - 1; i--;) {
                    if (class_arr[i] === "aclass") class_arr.splice(i, 1);
                }

                var arr_id = [];
                class_arr.forEach(function (element) {
                    var thenum = element.replace(/^\D+/g, '');
                    if (thenum != '') {
                        arr_id.push(thenum);
                    }
                });

                $('#jbs-booking-calendar-wrap .overlay-form').fadeIn();

                $.ajax({
                    url: ajaxurl,
                    dataType: 'json',
                    type: 'post',
                    data: {
                        action: 'cl_ad_get_detail_booking',
                        min_max: min_max,
                        ids: arr_id.toString()
                    },
                    success: function (doc) {
                        $('#cba-stas-detail').html(doc.data);
                        $('#jbs-booking-calendar-wrap .overlay-form').fadeOut();
                    }
                });
            });


        }



        /////////////////////////////////////

        $('#jbs-calendar-prev').click(function (e) {
            e.preventDefault();

            var currentMonth = $(this).data('month');
            var currentYear = $(this).data('year');

            if (currentMonth <= 12 && currentMonth > 1) {
                currentMonth = currentMonth - 1;
            } else {
                if (currentMonth == 1) {
                    currentMonth = 12;
                    currentYear = currentYear - 1;
                }
            }
            var queryParameters = {}, queryString = location.search.substring(1),
                re = /([^&=]+)=([^&]*)/g, m;

            while (m = re.exec(queryString)) {
                queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
            }

            queryParameters['month'] = currentMonth;
            queryParameters['year'] = currentYear;

            location.search = $.param(queryParameters); // Causes page to reload
        });
        $('#jbs-calendar-next').click(function (e) {
            e.preventDefault();

            var currentMonth = $(this).data('month');
            var currentYear = $(this).data('year');

            if (currentMonth < 12 && currentMonth >= 1) {
                currentMonth = currentMonth + 1;
            } else {
                if (currentMonth == 12) {
                    currentMonth = 1;
                    currentYear = currentYear + 1;
                }
            }
            var queryParameters = {}, queryString = location.search.substring(1),
                re = /([^&=]+)=([^&]*)/g, m;

            while (m = re.exec(queryString)) {
                queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
            }

            queryParameters['month'] = currentMonth;
            queryParameters['year'] = currentYear;

            location.search = $.param(queryParameters); // Causes page to reload
        });
    });
})(jQuery);