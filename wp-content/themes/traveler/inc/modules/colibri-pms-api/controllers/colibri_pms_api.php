<?php

class Colibri_PMS {
	static $username;
	static $password;
	static $url;
	static $string_xml;
	static $request_code;
	static $res;

	public static function cl_get_city_code() {
		$xml_request = '<EXT_CitiesRQ PrimaryLangID="eng" AltLangID="" Version="1.003">
						        <POS />
						        <Criteria>
						            <Criterion>
						                <Location CountryCode="CA" />
						            </Criterion>
						            <Criterion>
						                <Location CountryCode="FR" />
						            </Criterion>
						        </Criteria>
						</EXT_CitiesRQ>';
		self::init();
		self::cl_set_request_xml( $xml_request );
		self::cl_display_xml_result();
	}

	public function __construct() {

	}

	public static function init() {
		self::$username = st()->get_option( 'cba_id', '' );
		self::$password = st()->get_option( 'cba_pw', '' );
		self::$url      = 'https://api.colibripms.com/external/xml.php';
	}

	public static function cl_parse_xml_result_from_string( $res ) {
		$xml_parse = simplexml_load_string( $res );
		return $xml_parse;
	}

	public static function cl_send_request_xml( $xml ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_URL, self::$url );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		curl_setopt( $ch, CURLOPT_USERPWD, self::$username . ":" . self::$password );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, "gds=&xml=" . $xml );
		$res = curl_exec( $ch );
		curl_close( $ch );
		return $res;
	}

	public static function cl_send_request() {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_URL, self::$url );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		curl_setopt( $ch, CURLOPT_USERPWD, self::$username . ":" . self::$password );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, "gds=&xml=" . self::$string_xml );
		self::$res          = curl_exec( $ch );
		self::$request_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );
	}

	public static function cl_check_authorization() {
		switch ( self::$request_code ) {
			case '200':
				return true;
				break;
			case '401':
				return false;
				break;
			default:
				return false;
				break;
		}
	}

	public static function cl_set_request_xml( $xml ) {
		self::$string_xml = $xml;
		self::cl_send_request();
	}

	public static function cl_display_xml_result() {
		header( 'Content-Type: application/xml' );
		$output = self::$res;
		print ( $output );
		die;
	}

	public static function cl_parse_xml_result() {
		$xml_parse = simplexml_load_string( self::$res );
		return $xml_parse;
	}

	public static function cl_get_list_hotels() {
		$hotel_data = self::cl_parse_xml_result()->Properties->Property;
		$hotels     = array();
		$i          = 0;
		if ( ! empty( $hotel_data ) ) {
			foreach ( $hotel_data as $key => $value ) {
				$desc    = '';
				$amen    = '';
				$service = '';
				$thumb   = '';
				$photos  = array();
				foreach ( $value->VendorMessages->VendorMessage as $key_vend => $value_vend ) {
					if ( $value_vend->attributes()->Title == 'Images' ) {
						foreach ( $value_vend->SubSection as $key_gal => $value_gal ) {
							if ( $value_gal->Paragraph->attributes()->Name == 'Thumbnail' ) {
								$thumb = (string) $value_gal->Paragraph->Image;
							} else {
								array_push( $photos, (string) $value_gal->Paragraph->Image );
							}
						}
					}

					if ( $value_vend->attributes()->Title == 'Descriptions' ) {
						foreach ( $value_vend->SubSection as $key_desc => $value_desc ) {
							if ( $value_desc->attributes()->SubTitle == 'Description' ) {
								$desc = (string) $value_desc->Paragraph->Text;
							}
							if ( $value_desc->attributes()->SubTitle == 'Amenities' ) {
								$amen = (string) $value_desc->Paragraph->Text;
							}
							if ( $value_desc->attributes()->SubTitle == 'Services' ) {
								$service = (string) $value_desc->Paragraph->Text;
							}
						}
					}
				}

				$hotels[ $i ]['hotel_code']     = (int) $value->attributes()->HotelCode[0];
				$hotels[ $i ]['check_in_time']  = (string) $value->Policy->attributes()->CheckInTime;
				$hotels[ $i ]['check_out_time'] = (string) $value->Policy->attributes()->CheckOutTime;
				$hotels[ $i ]['hotel_name']     = (string) $value->attributes()->HotelName[0];
				$hotels[ $i ]['lat']            = (string) $value->Position->attributes()->Latitude;
				$hotels[ $i ]['lng']            = (string) $value->Position->attributes()->Longitude;
				$hotels[ $i ]['country']        = (string) $value->Address->CountryName;
				$hotels[ $i ]['city_name']      = (string) $value->Address->CityName;
				$hotels[ $i ]['postal_code']    = (string) $value->Address->PostalCode;
				$hotels[ $i ]['address_line']   = (string) $value->Address->AddressLine;
				$hotels[ $i ]['rating']         = (int) $value->Award->attributes()->Rating;
				$hotels[ $i ]['hotel_content']  = '';
				$hotels[ $i ]['hotel_desc']     = $desc;
				$hotels[ $i ]['hotel_amen']     = $amen;
				$hotels[ $i ]['hotel_services'] = $service;
				$hotels[ $i ]['hotel_photos']   = $photos;
				$hotels[ $i ]['hotel_thumb']    = $thumb;
				$hotels[ $i ]['min_price']      = (int) $value->RateRange->attributes()->MinRate;
				$hotels[ $i ]['max_price']      = (int) $value->RateRange->attributes()->MaxRate;
				$hotels[ $i ]['currency_code']  = (string) $value->RateRange->attributes()->CurrencyCode;
				$i ++;
			}
		}

		$cldt_obj                = new stdClass();
		$cldt_obj->min_max_price = self::cl_get_min_max_price( $hotels );
		$order_by                = STInput::get( 'orderby', 'ID' );
		$price_range             = STInput::get( 'price_range', '' );

		if ( $order_by != 'ID' ) {
			usort( $hotels, array(
				new OrderCmp( Colibri_Helper::render_order_by()['field'], Colibri_Helper::render_order_by()['sort'] ),
				"call"
			) );
		}

		if ( isset( $price_range ) && $price_range != '' ) {
			$arr_mm = explode( ';', $price_range );
			$mm_min = $arr_mm[0];
			$mm_max = $arr_mm[1];

			$i = 0;
			foreach ( $hotels as $key => $val ) {
				if ( $val['min_price'] < $mm_min || $val['min_price'] > $mm_max ) {
					unset( $hotels[ $i ] );
				}
				$i ++;
			}
		}

		$cldt_obj->found_posts = count( $hotels );
		$cldt_obj->posts       = $hotels;

		global $cldt;
		$cldt = $cldt_obj;
	}

	public static function cl_get_min_max_price( $obj ) {
		if ( ! empty( $obj ) ) {
			$arr_min = array();
			$arr_max = array();
			foreach ( $obj as $key => $value ) {
				array_push( $arr_min, $value['min_price'] );
				array_push( $arr_max, $value['max_price'] );
			}

			return array(
				'min' => min( array_merge( $arr_min, $arr_max ) ),
				'max' => max( array_merge( $arr_min, $arr_max ) )
			);
		}

		return array(
			'min' => 0,
			'max' => 0
		);
	}

	public static function cl_get_detail_hotel( $hotel_id ) {
		if ( ! isset( $hotel_id ) || $hotel_id == '' ) {
			return;
		}
		$hotel_data = self::cl_parse_xml_result()->Properties->Property;

		$hotels = array();

		foreach ( $hotel_data as $key => $value ) {
			if ( (int) $value->attributes()->HotelCode[0] == $hotel_id ) {

				$name    = (string) $value->attributes()->HotelName[0];
				$address = $value->Address->AddressLine . ', ' . $value->Address->CityName . ', ' . $value->Address->CountryName;

				$gallery_array = array();
				$desc          = '';
				$amen          = '';
				$service       = '';
				foreach ( $value->VendorMessages->VendorMessage as $key_vend => $value_vend ) {
					if ( $value_vend->attributes()->Title == 'Images' ) {
						foreach ( $value_vend->SubSection as $key_gal => $value_gal ) {
							array_push( $gallery_array, (string) $value_gal->Paragraph->Image );
						}
					}

					if ( $value_vend->attributes()->Title == 'Descriptions' ) {
						foreach ( $value_vend->SubSection as $key_desc => $value_desc ) {
							if ( $value_desc->attributes()->SubTitle == 'Description' ) {
								$desc = (string) $value_desc->Paragraph->Text;
							}
							if ( $value_desc->attributes()->SubTitle == 'Amenities' ) {
								$amen = (string) $value_desc->Paragraph->Text;
							}
							if ( $value_desc->attributes()->SubTitle == 'Services' ) {
								$service = (string) $value_desc->Paragraph->Text;
							}
						}
					}

				}

				$rating    = (int) $value->Award->attributes()->Rating;
				$lat       = (string) $value->Position->attributes()->Latitude;
				$lng       = (string) $value->Position->attributes()->Longitude;
				$min_price = (string) $value->RateRange->attributes()->MinRate;

				$hotels['hotel_code']    = (int) $value->attributes()->HotelCode[0];
				$hotels['name']          = $name;
				$hotels['address_line']  = (string) $value->Address->AddressLine;
				$hotels['city_name']     = (string) $value->Address->CityName;
				$hotels['country_name']  = (string) $value->Address->CountryName;
				$hotels['photo']         = $gallery_array;
				$hotels['map_lat']       = $lat;
				$hotels['map_lng']       = $lng;
				$hotels['rating']        = $rating;
				$hotels['desc']          = $desc;
				$hotels['amen']          = $amen;
				$hotels['service']       = $service;
				$hotels['min_price']     = $min_price;
				$hotels['currency_code'] = (string) $value->RateRange->attributes()['CurrencyCode'];
				global $cldt_dtht;
				$cldt_dtht = $hotels;
			}
		}
	}

	/**
	 * @param $hotel_code
	 * @param $start
	 * @param $end
	 *
	 * @return string
	 */
	public static function cl_get_list_rooms_of_hotel( $hotel_code, $start, $end ) {
		$xml_request = '<OTA_HotelAvailRQ PrimaryLangID="eng" AltLangID="deu" Version="1.003" xmlns="http://www.opentravel.org/OTA/2003/05">
            <POS />
            <AvailRequestSegments>
                <AvailRequestSegment>
                    <StayDateRange Start="' . $start . '" End="' . $end . '" />
                    <HotelSearchCriteria>
                        <Criterion>
                            <HotelRef HotelCode="' . $hotel_code . '"/>
                        </Criterion>
                    </HotelSearchCriteria>
                </AvailRequestSegment>
            </AvailRequestSegments>
        </OTA_HotelAvailRQ>
        ';
		self::init();
		self::cl_set_request_xml( $xml_request );

		$rooms_data = self::cl_parse_xml_result()->RoomStays->RoomStay;

		$rooms_detail = array();
		$i            = 0;
		foreach ( $rooms_data as $key => $value ) {
			$name   = (string) $value->RoomTypes->RoomType->TPA_Extensions->RoomTypeName;
			$photos = $value->RoomTypes->RoomType->RoomDescription->Image;
			$thumb  = '';
			if ( ! empty( $photos ) ) {
				$thumb = (string) $photos[0];
			}
			//Guest count
			$guest_count = $value->GuestCounts->GuestCount;
			$guest_arr   = array();
			$g           = 0;
			foreach ( $guest_count as $key_g => $val_g ) {
				$guest_arr[ $g ]['age']   = Colibri_Helper::cl_parse_aqc_code( (string) $val_g->attributes()['AgeQualifyingCode'] );
				$guest_arr[ $g ]['count'] = (string) $val_g->attributes()['Count'];
				$guest_arr[ $g ]['code']  = (string) $val_g->attributes()['AgeQualifyingCode'];
				$g ++;
			}

			$room_rates     = [];
			$room_rate_plan = $value->RoomRates->RoomRate;
			$rr             = 0;
			foreach ( $room_rate_plan as $key_rate => $val_rate ) {
				$room_rates[ $rr ]['start'] = (string) $val_rate->attributes()['EffectiveDate'];
				$room_rates[ $rr ]['end']   = (string) $val_rate->attributes()['ExpireDate'];
				$arr_rate                   = [];
				$ar                         = 0;
				foreach ( $val_rate->Rates->Rate as $k => $v ) {
					$arr_rate[ $ar ]['min']           = (string) $v->attributes()['MinGuestApplicable'];
					$arr_rate[ $ar ]['max']           = (string) $v->attributes()['MaxGuestApplicable'];
					$arr_rate[ $ar ]['price']         = (string) $v->Base->attributes()['AmountAfterTax'];
					$arr_rate[ $ar ]['currency_code'] = (string) $v->Base->attributes()['CurrencyCode'];
					$ar ++;
				}
				$room_rates[ $rr ]['rate'] = $arr_rate;
				$rr ++;
			}

			$cancel_policy = $value->CancelPenalties->CancelPenalty;
			$cancel_arr    = [];

			//Cancel policy
			$c = 0;
			foreach ( $cancel_policy as $key_c => $val_c ) {
				$cancel_arr[ $c ]['deadline'] = [
					'time_unit' => (string) $val_c->Deadline->attributes()['OffsetTimeUnit'],
					'unit'      => (string) $val_c->Deadline->attributes()['OffsetUnitMultiplier'],
					'drop_time' => (string) $val_c->Deadline->attributes()['OffsetDropTime'],
				];

				$cancel_arr[ $c ]['amount_percent'] = [
					'number_of_nights' => (string) $val_c->AmountPercent->attributes()['NmbrOfNights']
				];
				$c ++;
			}

			$bed                                      = (string) $value->RoomTypes->RoomType->TPA_Extensions->NumberOfBedrooms;
			$rooms_detail[ $i ]['name']               = $name;
			$rooms_detail[ $i ]['desc']               = (string) $value->RoomTypes->RoomType->RoomDescription->Text;
			$rooms_detail[ $i ]['thumb']              = $thumb;
			$rooms_detail[ $i ]['photos']             = $photos;
			$rooms_detail[ $i ]['id']                 = (string) $value->RoomTypes->RoomType->attributes()['RoomTypeCode'];
			$rooms_detail[ $i ]['bed']                = $bed;
			$rooms_detail[ $i ]['amenity']            = $value->RoomTypes->RoomType->Amenities->Amenity;
			$rooms_detail[ $i ]['rate_plan_code']     = (string) $value->RatePlans->RatePlan->attributes()['RatePlanCode'];
			$rooms_detail[ $i ]['rate_plan_name']     = (string) $value->RatePlans->RatePlan->RatePlanDescription->Text;
			$rooms_detail[ $i ]['num_of_unit']        = (string) $value->RoomTypes->RoomType->attributes()['NumberOfUnits'];
			$rooms_detail[ $i ]['guest_count']        = $guest_arr;
			$rooms_detail[ $i ]['avg_price']          = '123';
			$rooms_detail[ $i ]['room_rates']         = $room_rates;
			$rooms_detail[ $i ]['hotel_code']         = (string) $value->BasicPropertyInfo->attributes()['HotelCode'];
			$rooms_detail[ $i ]['number_select_room'] = 1;
			$rooms_detail[ $i ]['cancel_policy']      = $cancel_arr;
			$i ++;
		}

		global $cldt_dtr;

		$cldt_dtr = $rooms_detail;
	}

	public static function cl_get_list_city( $country_code, $from, $to ) {
		$xml_request = '<EXT_CitiesRQ PrimaryLangID="eng" AltLangID="" Version="1.003">
						        <POS />
						        <Criteria>
						            <Criterion>
						                <Location CountryCode="' . $country_code . '" />
						            </Criterion>
						        </Criteria>
						</EXT_CitiesRQ>';

		self::init();
		$cba_status = Colibri_Helper::cl_check_api( self::cl_send_request_xml( $xml_request ) );
		$arr        = new stdClass();
		if ( $cba_status ) {
			$res = self::cl_parse_xml_result_from_string( self::cl_send_request_xml( $xml_request ) );

			$arr->posts_found = count( $res->Result );
			$arr->posts       = [];

			$i = 0;
			foreach ( $res->Result as $key => $val ) {
				if ( $i >= $from && $i < $to ) {
					array_push( $arr->posts, array(
						'code' => (string) $val->CityID,
						'name' => (string) $val->Name
					) );
				}
				$i ++;
			}
			return $arr;
		}
	}

	//Declare
	public static function cl_get_response_xml() {
		$data                  = simplexml_load_string( self::$res );
		$cldt_obj              = new stdClass();
		$cldt_obj->found_posts = count( $data->Properties->Property );

		$i = 0;
		foreach ( $data->Properties->Property as $key => $value ) {
			$cldt_obj_posts[ $i ]            = new stdClass();
			$cldt_obj_posts[ $i ]->name      = (string) $value->attributes()['HotelName'];
			$cldt_obj_posts[ $i ]->min_price = (string) $value->RateRange->attributes()['MinRate'];
			$i ++;
		}
		$cldt_obj->posts = $cldt_obj_posts;

		global $cldt;
		$cldt = $cldt_obj;

		//TravelHelper::d( $cldt );
	}

	/**
	 * CUSTOM FUNCTION FOR AJAX GET DATA NOT SET GLOBAL VARIABLE
	 */
	public static function cl_rt_get_list_rooms_of_hotel( $hotel_code, $start, $end ) {
		$start       = TravelHelper::convertDateFormatColibri( $start );
		$end         = TravelHelper::convertDateFormatColibri( $end );
		$xml_request = '<OTA_HotelAvailRQ PrimaryLangID="eng" AltLangID="deu" Version="1.003" xmlns="http://www.opentravel.org/OTA/2003/05">
            <POS />
            <AvailRequestSegments>
                <AvailRequestSegment>
                    <StayDateRange Start="' . $start . '" End="' . $end . '" />
                    <HotelSearchCriteria>
                        <Criterion>
                            <HotelRef HotelCode="' . $hotel_code . '"/>
                        </Criterion>
                    </HotelSearchCriteria>
                </AvailRequestSegment>
            </AvailRequestSegments>
        </OTA_HotelAvailRQ>
        ';
		self::init();
		$res        = self::cl_send_request_xml( $xml_request );
		$rooms_data = simplexml_load_string( $res )->RoomStays->RoomStay;

		$rooms_detail = array();
		$i            = 0;
		if ( ! empty( $rooms_data ) ) {
			foreach ( $rooms_data as $key => $value ) {
				$name   = (string) $value->RoomTypes->RoomType->TPA_Extensions->RoomTypeName;
				$photos = $value->RoomTypes->RoomType->RoomDescription->Image;
				$thumb  = '';
				if ( ! empty( $photos ) ) {
					$thumb = (string) $photos[0];
				}
				//Guest count
				$guest_count = $value->GuestCounts->GuestCount;
				$guest_arr   = array();
				$g           = 0;
				foreach ( $guest_count as $key_g => $val_g ) {
					$guest_arr[ $g ]['age']   = Colibri_Helper::cl_parse_aqc_code( (string) $val_g->attributes()['AgeQualifyingCode'] );
					$guest_arr[ $g ]['count'] = (string) $val_g->attributes()['Count'];
					$guest_arr[ $g ]['code']  = (string) $val_g->attributes()['AgeQualifyingCode'];
					$g ++;
				}

				$room_rates     = [];
				$room_rate_plan = $value->RoomRates->RoomRate;
				$rr             = 0;
				foreach ( $room_rate_plan as $key_rate => $val_rate ) {
					$room_rates[ $rr ]['start'] = (string) $val_rate->attributes()['EffectiveDate'];
					$room_rates[ $rr ]['end']   = (string) $val_rate->attributes()['ExpireDate'];
					$arr_rate                   = [];
					$ar                         = 0;
					foreach ( $val_rate->Rates->Rate as $k => $v ) {
						$arr_rate[ $ar ]['min']           = (string) $v->attributes()['MinGuestApplicable'];
						$arr_rate[ $ar ]['max']           = (string) $v->attributes()['MaxGuestApplicable'];
						$arr_rate[ $ar ]['price']         = (string) $v->Base->attributes()['AmountAfterTax'];
						$arr_rate[ $ar ]['currency_code'] = (string) $v->Base->attributes()['CurrencyCode'];
						$ar ++;
					}
					$room_rates[ $rr ]['rate'] = $arr_rate;
					$rr ++;
				}

				//Cancel policy
				$cancel_policy = $value->CancelPenalties->CancelPenalty;
				$cancel_arr    = [];

				$c = 0;
				foreach ( $cancel_policy as $key_c => $val_c ) {
					$cancel_arr[ $c ]['deadline'] = [
						'time_unit' => (string) $val_c->Deadline->attributes()['OffsetTimeUnit'],
						'unit'      => (string) $val_c->Deadline->attributes()['OffsetUnitMultiplier'],
						'drop_time' => (string) $val_c->Deadline->attributes()['OffsetDropTime'],
					];

					$cancel_arr[ $c ]['amount_percent'] = [
						'number_of_nights' => (string) $val_c->AmountPercent->attributes()['NmbrOfNights']
					];
					$c ++;
				}

				$bed                                  = (string) $value->RoomTypes->RoomType->TPA_Extensions->NumberOfBedrooms;
				$rooms_detail[ $i ]['name']           = $name;
				$rooms_detail[ $i ]['desc']           = (string) $value->RoomTypes->RoomType->RoomDescription->Text;
				$rooms_detail[ $i ]['thumb']          = $thumb;
				$rooms_detail[ $i ]['photos']         = $photos;
				$rooms_detail[ $i ]['id']             = (string) $value->RoomTypes->RoomType->attributes()['RoomTypeCode'];
				$rooms_detail[ $i ]['bed']            = $bed;
				$rooms_detail[ $i ]['amenity']        = $value->RoomTypes->RoomType->Amenities->Amenity;
				$rooms_detail[ $i ]['rate_plan_code'] = (string) $value->RatePlans->RatePlan->attributes()['RatePlanCode'];
				$rooms_detail[ $i ]['rate_plan_name'] = (string) $value->RatePlans->RatePlan->RatePlanDescription->Text;
				$rooms_detail[ $i ]['num_of_unit']    = (string) $value->RoomTypes->RoomType->attributes()['NumberOfUnits'];
				$rooms_detail[ $i ]['guest_count']    = $guest_arr;
				$rooms_detail[ $i ]['avg_price']      = '123';
				$rooms_detail[ $i ]['room_rates']     = $room_rates;
				$rooms_detail[ $i ]['hotel_code']     = (string) $value->BasicPropertyInfo->attributes()['HotelCode'];
				$rooms_detail[ $i ]['cancel_policy']  = $cancel_arr;
				$i ++;
			}
		}

		return $rooms_detail;
	}

	public static function cl_get_rates( $hotel_code, $start, $end, $rate_plan = '' ) {
		$start       = TravelHelper::convertDateFormatColibri( $start );
		$end         = TravelHelper::convertDateFormatColibri( $end );
		$xml_request = '<OTA_HotelRatePlanRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.003">
                            <POS />
                            <RatePlans>
                                <RatePlan>
                                    <HotelRef HotelCode="' . $hotel_code . '" />
                                    <DateRange Start="' . $start . '" End="' . $end . '" />
                                </RatePlan>
                            </RatePlans>
                        </OTA_HotelRatePlanRQ>';
		self::init();
		$res        = self::cl_send_request_xml( $xml_request );
		$rates_data = simplexml_load_string( $res )->RatePlans->RatePlan;
		$rates = [];
		$i     = 0;

		if ( ! empty( $rates_data ) ) {
			foreach ( $rates_data as $k => $v ) {

				$cancels    = $v->CancelPenalties->CancelPenalty;
				$cancel_arr = [];
				$i_c        = 0;
				foreach ( $cancels as $k_c => $v_c ) {
					$cancel_arr[ $i_c ]['deadline']       = [
						'time_unit' => (string) $v_c->Deadline->attributes()['OffsetTimeUnit'],
						'unit'      => (string) $v_c->Deadline->attributes()['OffsetUnitMultiplier'],
					];
					$cancel_arr[ $i_c ]['amount_percent'] = [
						'number_of_night' => [
							'unit' => (string) $v_c->AmountPercent->attributes()['NmbrOfNights'],
							'text' => __( 'Night(s)', ST_TEXTDOMAIN )
						],
						'amount'          => [
							'unit' => (string) $v_c->AmountPercent->attributes()['Amount'],
							'text' => __( 'EUR', ST_TEXTDOMAIN )
						],
						'percent'         => [
							'unit' => (string) $v_c->AmountPercent->attributes()['Percent'],
							'text' => __( '%', ST_TEXTDOMAIN )
						],
					];
					$i_c ++;
				}

				$rates[ $i ]['rate_plan_code']          = (string) $v->attributes()['RatePlanCode'];
				$rates[ $i ]['destination_system_code'] = '';
				$rates[ $i ]['cancel']                  = $cancel_arr;
				$i ++;
			}

			if ( $rate_plan != '' ) {
				$r = [];
				foreach ( $rates as $kk => $vv ) {
					if ( $vv['rate_plan_code'] == $rate_plan ) {
						$r = $vv;
					}
				}

				return $r;
			} else {
				return $rates;
			}
		}
		return;

	}

	/**
	 * @param $start
	 * @param $end
	 * @param $hotel_code
	 *
	 * @return mixed
	 */
	public static function cl_get_rates_restric_of_hotel( $start, $end, $hotel_code ) {
		$start = TravelHelper::convertDateFormatColibri( $start );
		$end   = TravelHelper::convertDateFormatColibri( $end );

		$xml_request = '<OTA_HotelAvailGetRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.003">
                            <POS />
                            <HotelAvailRequests>
                                <HotelAvailRequest>
                                    <DateRange Start="' . $start . '" End="' . $end . '" />
                                    <HotelRef HotelCode="' . $hotel_code . '" />
                                </HotelAvailRequest>
                            </HotelAvailRequests>
                        </OTA_HotelAvailGetRQ>';
		self::init();
		$res        = self::cl_send_request_xml( $xml_request );
		$rates_data = simplexml_load_string( $res )->AvailStatusMessages->AvailStatusMessage;
		if ( ! empty( $rates_data ) ) {
			$arr_res = [];
			$i       = 0;
			foreach ( $rates_data as $k => $v ) {
				$los      = [];
				$los_data = $v->LengthsOfStay->LengthOfStay;
				if ( ! empty( $los_data ) ) {
					$ii = 0;
					foreach ( $los_data as $kk => $vv ) {
						$los[ $ii ]['time']         = (string) $vv->attributes()['Time'];
						$los[ $ii ]['time_unit']    = (string) $vv->attributes()['TimeUnit'];
						$los[ $ii ]['los_message']  = (string) $vv->attributes()['MinMaxMessageType'];
						$los[ $ii ]['min_los']      = (string) $vv->attributes()['MinLOS'];
						$los[ $ii ]['max_los']      = (string) $vv->attributes()['MaxLOS'];
						$los[ $ii ]['no_check_in']  = (string) $vv->attributes()['NoCheckin'];
						$los[ $ii ]['no_check_out'] = (string) $vv->attributes()['NoCheckout'];
						$ii ++;
					}
				}

				$arr_res[ $i ]['room_code']      = (string) $v->StatusApplicationControl->attributes()['InvTypeCode'];
				$arr_res[ $i ]['length_of_stay'] = $los;
				$i ++;
			}

			$datas = [];
			foreach ( $arr_res as $kkk => $vvv ) {
				$datas[ $vvv['room_code'] ] = $vvv;
			}

			return $datas;
		} else {
			return;
		}
	}

	public static function cl_get_rates_plan_of_hotel( $hotel_code, $start, $end ) {
		$start = TravelHelper::convertDateFormatColibri( $start );
		$end   = TravelHelper::convertDateFormatColibri( $end );

		$xml_request = '<OTA_HotelAvailRQ PrimaryLangID="eng" AltLangID="deu" Version="1.003" xmlns="http://www.opentravel.org/OTA/2003/05">
                            <POS />
                            <AvailRequestSegments>
                                <AvailRequestSegment>
                                    <StayDateRange Start="' . $start . '" End="' . $end . '"/>
                                    <HotelSearchCriteria>
                                        <Criterion>
                                            <HotelRef HotelCode="' . $hotel_code . '"/>
                                        </Criterion>
                                    </HotelSearchCriteria>
                                </AvailRequestSegment>
                            </AvailRequestSegments>
                        </OTA_HotelAvailRQ>';
		self::init();
		$res        = self::cl_send_request_xml( $xml_request );
		$rates_data = simplexml_load_string( $res )->RoomStays->RoomStay;
		$rate_arr   = [];

		$i = 0;
		foreach ( $rates_data as $k => $v ) {
			$rate_arr[ $i ]['code'] = (string) $v->RatePlans->RatePlan->attributes()['RatePlanCode'];
			$rate_arr[ $i ]['name'] = (string) $v->RatePlans->RatePlan->RatePlanDescription->Text;
			$i ++;
		}
		$rt_arr = [];
		foreach ( $rate_arr as $kk => $vv ) {
			$rt_arr[ $vv['code'] ] = $vv['name'];
		}

		return $rt_arr;
	}

	public static function cl_get_rate_restrictions( $hotel_code, $start, $end ) {
		$start = TravelHelper::convertDateFormatColibri( $start );
		$end   = TravelHelper::convertDateFormatColibri( $end );

		$xml_request = '<OTA_HotelAvailGetRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.003">
                            <POS />
                            <HotelAvailRequests>
                                <HotelAvailRequest>
                                    <DateRange Start="' . $start . '" End="' . $end . '" />
                                    <HotelRef HotelCode="' . $hotel_code . '" />
                                </HotelAvailRequest>
                            </HotelAvailRequests>
                        </OTA_HotelAvailGetRQ>';
		self::init();
		$res        = self::cl_send_request_xml( $xml_request );
		$rates_data = simplexml_load_string( $res );
		//TravelHelper::d( $rates_data );
	}

	/**
	 * @param $xml
	 *
	 * @return mixed|array
	 */
	public static function cl_get_response_reservation( $xml ) {
		$res = self::cl_send_request_xml( $xml );
		return $res;
	}

}

Colibri_PMS::init();