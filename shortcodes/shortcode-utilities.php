<?php

class Movement_Shortcode_Utilities {

    public static function create_initials( $longitude, $latitude, $payload ) : string {
        $letters = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'N', 'S',
            'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'E', 'A', 'R', 'I',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'D', 'E',
            'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'E', 'A', 'R', 'I',
            'E', 'A', 'R', 'I', 'T', 'N', 'S', 'L', 'E', 'A', 'R', 'I', 'N', 'S',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'C', 'D',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'A', 'B',
            'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'E', 'A', 'R', 'I',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'A', 'B',
            'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'E', 'A', 'R',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'N', 'S',
            'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'E', 'A', 'R', 'I',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'D', 'E',
            'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'E', 'A', 'R', 'I',
            'E', 'A', 'R', 'I', 'T', 'N', 'S', 'L', 'E', 'A', 'R', 'I', 'N', 'S',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'C', 'D',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'A', 'B',
            'E', 'A', 'R', 'I', 'T', 'N', 'S', 'L', 'E', 'A', 'R', 'I', 'N', 'S',
            'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'E', 'A', 'R', 'I',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'N', 'S',
        ];
        $fnum = abs( round( $longitude ) );
        $lnum = abs( round( $latitude ) );
        $list = str_split( hash( 'sha256', maybe_serialize( $payload ) ) );
        foreach( $list as $character ){
            if ( is_numeric( $character ) ) {
                $fnum = $fnum + $character;
                $lnum = $lnum + $character;
                break;
            }
        }
        return strtoupper( $letters[$fnum] . $letters[$lnum] );
    }

    public static function create_time_string( $timestamp, $timezone_offset ) : string {
        $adjusted_time = $timestamp + $timezone_offset;
        if ( $timestamp > strtotime('-1 hour') ) {
            $time_string = self::_time_elapsed_string('@'.$timestamp);
        }
        else if ( $timestamp > strtotime('today+00:00') + $timezone_offset ) {
            $time_string = date( 'g:i a', $adjusted_time );
        }
        else {
            $time_string = date( 'D g:i a', $adjusted_time );
        }
        return $time_string;
    }

    public static function _time_elapsed_string( $datetime, $full = false ) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public static function create_in_language_string( $payload ) : string {
        $in_language = '';
        if ( isset( $payload['language_name'] ) && ! empty( $payload['language_name'] ) && 'English' !== $payload['language_name'] ) {
            $in_language = ' in ' . $payload['language_name'];
        }
        return $in_language;
    }

    public static function create_location_precision( $lng, $lat, $label, $payload ) : array {
        $location = [
           'lng' => $lng,
           'lat' => $lat,
           'label' => $label
        ];

        $restricted = self::_persecuted_countries();

        if ( ! isset( $payload['country'] ) ) { // if country is not set, reduce precision to 111km
            $location['lng'] = round($location['lng'], 0 );
            $location['lat'] = round($location['lat'], 0 );
            $location['label'] = '';
        }
        else if ( in_array( $payload['country'], $restricted ) ) { // if persecuted country, reduce precision to 111km
            $location['label'] = ' (' . $payload['country'] . ')';
            $location['lng'] = round( $location['lng'], 0 );
            $location['lat'] = round( $location['lat'], 0 );
        } else { // if non-persecuted country, reduce precision to 11km
            $location['label'] = ' (' . $location['label'] . ')';
            $location['lng'] = round( $location['lng'], 1 );
            $location['lat'] = round( $location['lat'], 1 );
        }

        return $location;
    }

    public static function _persecuted_countries() : array {

        // Top 50 most persecuted countries
        // @link https://www.opendoorsusa.org/christian-persecution/world-watch-list/

        return [
            'North Korea',
            'Afghanistan',
            'Somolia',
            'Libya',
            'Pakistan',
            'Eritrea',
            'Sudan',
            'Yemen',
            'Iran',
            'India',
            'Syria',
            'Nigeria',
            'Saudi Arabia',
            'Maldives',
            'Iraq',
            'Egypt',
            'Algeria',
            'Uzbekistan',
            'Myanmar',
            'Laos',
            'Vietnam',
            'Turkmenistan',
            'China',
            'Mauritania',
            'Central African Republic',
            'Morocco',
            'Qatar',
            'Burkina Faso',
            'Mali',
            'Sri Lanka',
            'Tajikistan',
            'Nepal',
            'Jordan',
            'Tunisia',
            'Kazakhstan',
            'Turkey',
            'Brunei',
            'Bangladesh',
            'Ethiopia',
            'Malaysia',
            'Colombia',
            'Oman',
            'Kuwait',
            'Kenya',
            'Bhutan',
            'Russian Federation',
            'United Arab Emirates',
            'Cameroon',
            'Indonesia',
            'Niger'
        ];
    }

    public static function create_note_data( $category, $action, $initials, $in_language, $location_label, $payload ) : array {
        $data = [
            'note' => '',
            'type' => 'blessing',
        ];

        switch( $action ) {
            case 'starting_group':
                $data['note'] =  $initials . ' is starting a training group' . $in_language . '! ' . $location_label;
                $data['type'] = 'greater_blessing';
                break;
            case 'building_group':
                $data['note'] =  $initials . ' is growing a training group' . $in_language . '! ' . $location_label;
                $data['type'] = 'greater_blessing';
                break;
            case 'leading_1':
            case 'leading_2':
            case 'leading_3':
            case 'leading_4':
            case 'leading_5':
            case 'leading_6':
            case 'leading_7':
            case 'leading_8':
            case 'leading_9':
            case 'leading_10':
                if ( isset($payload['group_size']) && $payload['group_size'] > 1 ) {
                    $data['note'] =  $initials . ' is leading a group of '. $payload['group_size'] .' through session ' . str_replace( '_', '', substr( $action, -2, 2 ) ) . $in_language . '! ' . $location_label;
                } else {
                    $data['note'] =  $initials . ' is leading a group through session ' . str_replace( '_', '', substr( $action, -2, 2 ) ) . $in_language . '! ' . $location_label;
                }
                $data['type'] = 'greatest_blessing';
                break;
            case 'zume_training':
                $data['note'] =  $initials . ' is registering for Zúme training' . $in_language . '! ' . $location_label;
                $data['type'] = 'great_blessing';
                break;
            case 'zume_vision':
                $data['note'] =  $initials . ' is joining the Zúme community to engage in Disciple Making Movements' . $in_language . '! ' . $location_label;
                $data['type'] = 'greatest_blessing';
                break;
            case 'coaching':
                $data['note'] =  $initials . ' is requesting coaching from Zúme coaches' . $in_language . '! ' . $location_label;
                $data['type'] = 'great_blessing';
                break;
            case 'studying_1':
            case 'studying_2':
            case 'studying_3':
            case 'studying_4':
            case 'studying_5':
            case 'studying_6':
            case 'studying_7':
            case 'studying_8':
            case 'studying_9':
            case 'studying_10':
            case 'studying_11':
            case 'studying_12':
            case 'studying_13':
            case 'studying_14':
            case 'studying_15':
            case 'studying_16':
            case 'studying_17':
            case 'studying_18':
            case 'studying_19':
            case 'studying_20':
            case 'studying_21':
            case 'studying_22':
            case 'studying_23':
            case 'studying_24':
            case 'studying_25':
            case 'studying_26':
            case 'studying_27':
            case 'studying_28':
            case 'studying_29':
            case 'studying_30':
            case 'studying_31':
            case 'studying_32':
            case 'studying_offline_1':
            case 'studying_offline_2':
            case 'studying_offline_3':
            case 'studying_offline_4':
            case 'studying_offline_5':
            case 'studying_offline_6':
            case 'studying_offline_7':
            case 'studying_offline_8':
            case 'studying_offline_9':
            case 'studying_offline_10':
            case 'studying_offline_11':
            case 'studying_offline_12':
            case 'studying_offline_13':
            case 'studying_offline_14':
            case 'studying_offline_15':
            case 'studying_offline_16':
            case 'studying_offline_17':
            case 'studying_offline_18':
            case 'studying_offline_19':
            case 'studying_offline_20':
            case 'studying_offline_21':
            case 'studying_offline_22':
            case 'studying_offline_23':
            case 'studying_offline_24':
            case 'studying_offline_25':
            case 'studying_offline_26':
            case 'studying_offline_27':
            case 'studying_offline_28':
            case 'studying_offline_29':
            case 'studying_offline_30':
            case 'studying_offline_31':
            case 'studying_offline_32':
                $title = ' disciple making movement principles';
                if ( isset( $payload['title'] ) && ! empty( $payload['title'] ) ) {
                    $title = ' "' . $payload['title'] . '"';
                }
                $data['note'] =  $initials . ' is studying' . $title . $in_language . '! ' . $location_label;
                $data['type'] = 'blessing';
                break;
            case 'updated_3_month':
                $data['note'] =  $initials . '  made a three month plan to multiply disciples' . $in_language . '! ' . $location_label;
                $data['type'] = 'great_blessing';
                break;
            default:
                break;
        }

        return $data;
    }

    public static function query_contacts_points_geojson( $tz_name, $country = 'none', $language = 'none' ) {
        global $wpdb;

        $utc_time = new DateTime('now', new DateTimeZone($tz_name));
        $timezoneOffset = $utc_time->format('Z');

        $timestamp = strtotime('-100 hours' );
        $results = $wpdb->get_results( $wpdb->prepare( "
                SELECT action, category, lng, lat, label, payload, timestamp FROM $wpdb->dt_movement_log WHERE timestamp > %s ORDER BY timestamp DESC
                ", $timestamp ), ARRAY_A );

        /**
         * (none) - #0E172F
         * Blessing - blessing- #21336A
         * Great Blessing - great_blessing - #2CACE2
         * Greater Blessing - greater_blessing - #90C741
         * Greatest Blessing - greatest_blessing - #FAEA38
         */
        $counts = [
            'blessing' => 0,
            'great_blessing' => 0,
            'greater_blessing' => 0,
            'greatest_blessing' => 0,
        ];
        $countries = [];
        $languages = [];
        $hash = [];

        $features = [];
        foreach ( $results as $result ) {

            $payload = maybe_unserialize( $result['payload'] );

            // make country list from results
            if ( isset( $payload['country'] ) && ! empty( $payload['country'] ) ) {
                $countries[$payload['country']] = $payload['country'];
            }

            // make language list
            if ( isset( $payload['language_name'] )
                && ! empty( $payload['language_name'] )
                && isset( $payload['language_code'] )
                && ! empty( $payload['language_code'] )
            ) {
                $languages[$payload['language_code']] = $payload['language_name'];
            }

            // BUILD NOTE

            // time string
            $time_string = Movement_Shortcode_Utilities::create_time_string( $result['timestamp'], $timezoneOffset );

            // language
            $in_language = Movement_Shortcode_Utilities::create_in_language_string( $payload );

            // initials string
            $initials = Movement_Shortcode_Utilities::create_initials( $result['lng'], $result['lat'], $payload );

            // location string
            $location = Movement_Shortcode_Utilities::create_location_precision( $result['lng'], $result['lat'], $result['label'], $payload );

            // note and type data
            $data = Movement_Shortcode_Utilities::create_note_data( $result['category'], $result['action'], $initials, $in_language, $location['label'], $payload );

            // filter out non selected country
            if ( 'none' !== $country && $country !== $payload['country'] ?? '' ) {
                continue;
            }

            // filter out non selected language
            if ( 'none' !== $language && $language !== $payload['language_code'] ?? '' ) {
                continue;
            }

            $hash[] = $data;

            $counts[$data['type']]++;

            $features[] = array(
                'type' => 'Feature',
                'properties' => array(
                    "note" => esc_html( $data['note'] ),
                    "type" => esc_attr( $data['type'] ),
                    "time" => esc_attr( $time_string ),
                    "language" => esc_attr( $payload['language_code'] ?? '' ),
                    "country" => esc_attr( $payload['country'] ?? '' )
                ),
                'geometry' => array(
                    'type' => 'Point',
                    'coordinates' => array(
                        $location['lng'],
                        $location['lat'],
                        1
                    ),
                ),
            );

        } // end foreach loop

        ksort( $countries );
        ksort($languages);

        $new_data = array(
            'type' => 'FeatureCollection',
            'counts' => $counts,
            'countries' => $countries,
            'languages' => $languages,
            'hash' => hash('sha256', serialize( $hash ) ),
            'features' => $features,
        );

        return $new_data;
    }



}
