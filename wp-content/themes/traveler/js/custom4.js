jQuery(document).ready(function ($) {

    $('#field-hotelroom-room, .extra-service-select, #field-hotelroom-checkin, #field-hotelroom-checkout').on('change', function (e) {
        changeServiceSelect();
    });
    var flag = false;
    if ($('#field-hotelroom-room').val() != '1' && $('#field-hotelroom-room').length > 0) {
        flag = true;
    }
    if ($('.extra-price').length > 0) {
        $('.extra-price .extra-service-select').each(function () {
            if ($(this).val() != '0') {
                flag = true;
            }
        });
    }
    if (flag) {
        changeServiceSelect();
    }

    function changeServiceSelect() {
        var basePrice = $('#st-base-price').data('base-price');
        var originPrice = 0;
        if ($('#st-origin-price').length > 0) {
            originPrice = $('#st-origin-price').data('origin-price');
        }
        var roomNumber = $('#field-hotelroom-room').val();
        var roomCheckIn = $('#field-hotelroom-checkin').val();
        var roomCheckOut = $('#field-hotelroom-checkout').val();

        var totalExtraPrice = 0;
        if ($('.extra-price').length > 0) {
            $('.extra-price .extra-service-select').each(function () {
                var totalItem;
                var numberItem = $(this).val();
                var priceItem = $(this).data('extra-price');
                totalItem = Number(getNumber(priceItem.toString())) * Number(numberItem);
                totalExtraPrice = totalExtraPrice + totalItem;
            });
        }
        renderHtml(basePrice, originPrice, roomNumber, totalExtraPrice, roomCheckIn, roomCheckOut);
    }

    function renderHtml(basePrice, originPrice, roomNumber, totalExtraPrice, checkIn, checkOut) {
        /* var total = Number(getNumber(basePrice.toString())) * Number(roomNumber) + (totalExtraPrice * Number(roomNumber));
        var totalOrigin = Number(getNumber(originPrice.toString())) * Number(roomNumber) + (totalExtraPrice * Number(roomNumber));
        if ($('#st-base-price').length > 0) {
            $('#st-base-price').html(format_money(total * nightNumber));
            $('#st-base-price').html(format_money(disCountTotalByPackage(total, nightNumber, renderDiscountPackage())));
            console.log('No D: ' + total * nightNumber);
        }
        if ($('#st-origin-price').length > 0) {
             $('#st-origin-price').html(format_money(totalOrigin * nightNumber));
            $('#st-origin-price').html(format_money(disCountTotalByPackage(totalOrigin, nightNumber, renderDiscountPackage())));
            console.log('No DO: ' + totalOrigin * nightNumber);
        }
        disCountTotalByPackage(total, nightNumber, renderDiscountPackage()); */

        $('.message_box').html('');

        $.ajax({
            method: "POST",
            url: st_params.ajax_url,
            data: {
                base_price: basePrice,
                origin_price: originPrice,
                post_id: $('#field-hotelroom-checkin').data('post-id'),
                number_room: roomNumber,
                check_in: checkIn,
                check_out: checkOut,
                total_extra: totalExtraPrice,
                action: 'st_format_real_price'
            },
            beforeSend: function () {
                $('#form-booking-room-over').fadeIn();
            },
            success: function (response) {
                if(response.status == true){
                    if($('#st-base-price').length > 0) {
                        $('#st-base-price').html(response.sale);
                    }
                    if($('#st-origin-price').length > 0) {
                        $('#st-origin-price').html(response.origin);
                    }
                    if($('#st-number-day').length > 0){
                        $('#st-number-day').html(response.numberday);
                    }
                    $('#form-booking-room-over').fadeOut();
                    $('.message_box').html('');
                }else{
                    if(response.message != ''){
                        $('.message_box').html('<div class="alert alert-danger">'+response.message+'</div>');
                        $('#form-booking-room-over').fadeOut();
                        return false;
                    }
                }
            }
        });
    }

    function getNumber(str) {
        return str.replace(/([^\d|^\.])*/g, '');
    }

    function format_money($money) {

        if (!$money) {
            return st_params.free_text;
        }
        //if (typeof st_params.booking_currency_precision && st_params.booking_currency_precision) {
        //    $money = Math.round($money).toFixed(st_params.booking_currency_precision);
        //}

        $money = st_number_format($money, st_params.booking_currency_precision, st_params.decimal_separator, st_params.thousand_separator);
        var $symbol = st_params.currency_symbol;
        var $money_string = '';

        switch (st_params.currency_position) {
            case "right":
                $money_string = $money + $symbol;
                break;
            case "left_space":
                $money_string = $symbol + " " + $money;
                break;

            case "right_space":
                $money_string = $money + " " + $symbol;
                break;
            case "left":
            default:
                $money_string = $symbol + $money;
                break;
        }

        return $money_string;
    }

    function st_number_format(number, decimals, dec_point, thousands_sep) {


        number = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                    .toFixed(prec);
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '')
                .length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1)
                .join('0');
        }
        return s.join(dec);
    }

    function getNightRoom(start, end){
        var dateNumber = daydiff(parseDate(formatD(start)), parseDate(formatD(end)));

        if(dateNumber <= 0){
            dateNumber = 1;
        }

        return dateNumber;
    }

    function parseDate(str) {
        var mdy = str.split('/');
        return new Date(mdy[2], mdy[0]-1, mdy[1]);
    }

    function daydiff(first, second) {
        return Math.round((second-first)/(1000*60*60*24));
    }

    function formatD(inputDate) {
        var d = new Date(inputDate.split("/").reverse().join("-"));
        var dd = d.getDate();
        var mm = d.getMonth()+1;
        var yy = d.getFullYear();
        var newdate = mm + "/" + dd + "/" + yy;
        return newdate;
    }

    function renderDiscountPackage(){
        var discountObj = [];
        if($('#discount-package').length > 0){
            $('#discount-package .discount-package-item').each(function(index, value){
                discountObj.push([$(this).val(), $(this).data('discount')])
            });
        }

        return discountObj;
    }

    function disCountTotalByPackage(total, nightNumber, discountObj){
        //console.log(discountObj.length);
        var result = total * nightNumber;
        for(var i = 0; i < discountObj.length; i++){
            var dayRange = discountObj[i][0].split('-');
            var discount = parseInt(discountObj[i][1] + ''.replace(/[^0-9\.]/g, ''), 10);

            dayRangeNumArr = [];

            for(var j = 0; j < dayRange.length; j++){
                var dayRangeNum = parseInt(dayRange[j] + ''.replace(/[^0-9\.]/g, ''), 10)
                dayRangeNumArr.push(dayRangeNum);
            }

            if(dayRangeNumArr.length == 1){
                if(nightNumber == dayRangeNumArr[0]){
                    result = total * nightNumber * (100 - discount) / 100;
                }
            }
            if(dayRangeNumArr.length == 2){
                if(nightNumber >= dayRangeNumArr[0] && nightNumber <= dayRangeNumArr[1]){
                    result = total * nightNumber * (100 - discount) / 100;
                }
            }
        }

        return result;
    }

});
