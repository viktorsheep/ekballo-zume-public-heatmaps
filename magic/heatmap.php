<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Zume_App_Heatmap {


    /**
     * This query returns the 50k saturation list of locations with population and country code.
     *
     * Returns
     * grid_id, population, country_code
     *
     * @return array
     */
    public static function query_saturation_list() : array {

        if ( false !== ( $value = get_transient( __METHOD__ ) ) ) { // phpcs:ignore
            return $value;
        }

        // 44141 records

        global $wpdb;
        $results = $wpdb->get_results("

            SELECT
            lg1.grid_id, lg1.population, lg1.country_code
            FROM $wpdb->dt_location_grid lg1
            WHERE lg1.level = 0
			AND lg1.grid_id NOT IN ( SELECT lg11.admin0_grid_id FROM $wpdb->dt_location_grid lg11 WHERE lg11.level = 1 AND lg11.admin0_grid_id = lg1.grid_id )
 			#'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg1.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg1.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)
			# above admin 0 (22)

			UNION ALL
            --
            # admin 1 locations that have no level 2 (768)
            --
            SELECT
            lg2.grid_id, lg2.population, lg2.country_code
            FROM $wpdb->dt_location_grid lg2
            WHERE lg2.level = 1
			AND lg2.grid_id NOT IN ( SELECT lg22.admin1_grid_id FROM $wpdb->dt_location_grid lg22 WHERE lg22.level = 2 AND lg22.admin1_grid_id = lg2.grid_id )
             #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg2.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg2.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

			UNION ALL
			--
            # admin 2 all countries (37100)
            --
			SELECT
            lg3.grid_id, lg3.population,  lg3.country_code
            FROM $wpdb->dt_location_grid lg3
            WHERE lg3.level = 2
            #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg3.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg3.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

			UNION ALL
            --
            # admin 1 for little highly divided countries (352)
            --
            SELECT
            lg4.grid_id, lg4.population,  lg4.country_code
            FROM $wpdb->dt_location_grid lg4
            WHERE lg4.level = 1
            #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg4.admin0_grid_id NOT IN (100050711,100219347, 100089589,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg4.admin0_grid_id IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

			UNION ALL

 			--
            # admin 3 for big countries (6153)
            --
            SELECT
            lg5.grid_id, lg5.population, lg5.country_code
            FROM $wpdb->dt_location_grid as lg5
            WHERE
            lg5.level = 3
            #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg5.admin0_grid_id IN (100050711,100219347, 100089589,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg5.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

			# Total Records (44395)

       ", ARRAY_A );

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                $list[$result['grid_id']] = $result;
            }
        }

        set_transient( __METHOD__, $list, MONTH_IN_SECONDS );

        return $list;
    }

    public static function query_flat_grid_by_level( $administrative_level, $us_div = 5000, $global_div = 50000 ) {

        if ( false !== ( $value = get_transient( __METHOD__ . $administrative_level . $us_div . $global_div ) ) ) { // phpcs:ignore
            return $value;
        }

        global $wpdb;
        $wpdb->us_div = $us_div;
        $wpdb->global_div = $global_div;
        switch ( $administrative_level ) {
            case 'a0':
                $results = $wpdb->get_results("
                    # 'Needs' GROUPED BY country
                    SELECT tb0.admin0_grid_id as grid_id, loc.name,loc.country_code, SUM(tb0.population) as population, SUM(tb0.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             # 44395 Records
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tb0
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb0.admin0_grid_id=loc.grid_id
                    GROUP BY tb0.admin0_grid_id
                ", ARRAY_A );
                break;
            case 'a1':
                $results = $wpdb->get_results("
                    # 'Needs' GROUPED BY state level
                    SELECT tb1.admin1_grid_id as grid_id, loc.name, loc.country_code, SUM(tb1.population) as population, SUM(tb1.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tb1
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb1.admin1_grid_id=loc.grid_id
                    GROUP BY tb1.admin1_grid_id
                ", ARRAY_A );
                break;
            case 'a2':
                $results = $wpdb->get_results("
                    # 'Needs' GROUPED BY county level
                    SELECT tb2.admin2_grid_id as grid_id, loc.name, loc.country_code, SUM(tb2.population) as population, SUM(tb2.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                 FROM $wpdb->dt_location_grid lg11
                                 WHERE lg11.level = 1
                               AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                 FROM $wpdb->dt_location_grid lg22
                                 WHERE lg22.level = 2
                               AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                    ) as tb2
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb2.admin2_grid_id=loc.grid_id
                    GROUP BY tb2.admin2_grid_id
                ", ARRAY_A );
                break;
            case 'a3':
                $results = $wpdb->get_results("
                    # 'Needs' GROUPED BY sub-county level
                    SELECT tb3.admin3_grid_id as grid_id, loc.name, loc.country_code, SUM(tb3.population) as population, SUM(tb3.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             # 44395 Records
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tb3
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb3.admin3_grid_id=loc.grid_id
                    WHERE tb3.admin3_grid_id IS NOT NULL
                    GROUP BY tb3.admin3_grid_id
                ", ARRAY_A );
                break;
            case 'world':
                $results = $wpdb->get_results("
                    # World
                    SELECT 1 as grid_id, 'World' as name,'' as country_code, SUM(tbw.population) as population, SUM(tbw.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             # 44395 Records
                             SELECT
                                 'World',
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tbw
                    LEFT JOIN $wpdb->dt_location_grid loc ON 1=loc.grid_id
                    GROUP BY 'World';
                ", ARRAY_A );
                break;
            default:
                $results = $wpdb->get_results("
                    # 48367 Records
                    # 'Needs' GROUPED BY sub-county level
                    SELECT tb3.admin3_grid_id as grid_id, loc.name, loc.country_code, SUM(tb3.population) as population, SUM(tb3.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             # 44395 Records
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tb3
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb3.admin3_grid_id=loc.grid_id
                    WHERE tb3.admin3_grid_id IS NOT NULL
                    GROUP BY tb3.admin3_grid_id

                    UNION ALL

                    # 'Needs' GROUPED BY county level
                    SELECT tb2.admin2_grid_id as grid_id, loc.name, loc.country_code, SUM(tb2.population) as population, SUM(tb2.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                 FROM $wpdb->dt_location_grid lg11
                                 WHERE lg11.level = 1
                               AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                 FROM $wpdb->dt_location_grid lg22
                                 WHERE lg22.level = 2
                               AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                 ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                 (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                 100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                 100054605, 100253456, 100342975, 100074571)
                    ) as tb2
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb2.admin2_grid_id=loc.grid_id
                    GROUP BY tb2.admin2_grid_id

                    UNION ALL

                    # 'Needs' GROUPED BY state level
                    SELECT tb1.admin1_grid_id as grid_id, loc.name, loc.country_code, SUM(tb1.population) as population, SUM(tb1.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tb1
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb1.admin1_grid_id=loc.grid_id
                    GROUP BY tb1.admin1_grid_id

                    UNION ALL

                    # 'Needs' GROUPED BY country
                    SELECT tb0.admin0_grid_id as grid_id, loc.name,loc.country_code, SUM(tb0.population) as population, SUM(tb0.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             # 44395 Records
                             SELECT
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tb0
                    LEFT JOIN $wpdb->dt_location_grid loc ON tb0.admin0_grid_id=loc.grid_id
                    GROUP BY tb0.admin0_grid_id

                    UNION ALL

                    # World
                    SELECT 1 as grid_id, 'World','' as country_code, SUM(tbw.population) as population, SUM(tbw.needed) as needed, (0) as reported, (0) as percent
                    FROM (
                             # 44395 Records
                             SELECT
                                 'World',
                                 lg1.admin0_grid_id,
                                 lg1.admin1_grid_id,
                                 lg1.admin2_grid_id,
                                 lg1.admin3_grid_id,
                                 lg1.population,
                                 IF(ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg1.population / IF(lg1.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg1
                             WHERE lg1.level = 0
                               AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                                       FROM $wpdb->dt_location_grid lg11
                                                       WHERE lg11.level = 1
                                                         AND lg11.admin0_grid_id = lg1.grid_id)
                               AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg1.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg2.admin0_grid_id,
                                 lg2.admin1_grid_id,
                                 lg2.admin2_grid_id,
                                 lg2.admin3_grid_id,
                                 lg2.population,
                                 IF(ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg2.population / IF(lg2.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg2
                             WHERE lg2.level = 1
                               AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                                       FROM $wpdb->dt_location_grid lg22
                                                       WHERE lg22.level = 2
                                                         AND lg22.admin1_grid_id = lg2.grid_id)
                               AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg2.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg3.admin0_grid_id,
                                 lg3.admin1_grid_id,
                                 lg3.admin2_grid_id,
                                 lg3.admin3_grid_id,
                                 lg3.population,
                                 IF(ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg3.population / IF(lg3.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg3
                             WHERE lg3.level = 2
                               AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg3.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg4.admin0_grid_id,
                                 lg4.admin1_grid_id,
                                 lg4.admin2_grid_id,
                                 lg4.admin3_grid_id,
                                 lg4.population,
                                 IF(ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg4.population / IF(lg4.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid lg4
                             WHERE lg4.level = 1
                               AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg4.admin0_grid_id IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                             UNION ALL
                             SELECT
                                 'World',
                                 lg5.admin0_grid_id,
                                 lg5.admin1_grid_id,
                                 lg5.admin2_grid_id,
                                 lg5.admin3_grid_id,
                                 lg5.population,
                                 IF(ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div )) < 1, 1,
                                    ROUND(lg5.population / IF(lg5.country_code = 'US', $wpdb->us_div, $wpdb->global_div ))) as needed
                             FROM $wpdb->dt_location_grid as lg5
                             WHERE lg5.level = 3
                               AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
                               AND lg5.admin0_grid_id NOT IN
                                   (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                                    100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                                    100054605, 100253456, 100342975, 100074571)
                    ) as tbw
                    LEFT JOIN $wpdb->dt_location_grid loc ON 1=loc.grid_id
                    GROUP BY 'World';
                ", ARRAY_A );
        }

        if ( empty( $results ) ) {
            return [];
        }

        set_transient( __METHOD__ . $administrative_level . $us_div . $global_div, $results, MONTH_IN_SECONDS );

        return $results;
    }


    public static function clear_church_grid_totals() {
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totals' );
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totalsa0' );
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totalsa1' );
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totalsa2' );
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totalsa3' );
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totalsa4' );
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totalsworld' );
        delete_transient( 'Zume_App_Heatmap::query_church_grid_totalsfull' );
    }

    public static function query_church_grid_totals( $administrative_level = null ) {

        if ( false !== ( $value = get_transient( __METHOD__ . $administrative_level ) ) ) { // phpcs:ignore
            return $value;
        }

        global $wpdb;

        switch ( $administrative_level ) {
            case 'a0':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    ", ARRAY_A );
                break;
            case 'a1':
                $results = $wpdb->get_results( "
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    ", ARRAY_A );
                break;
            case 'a2':
                $results = $wpdb->get_results( "
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    ", ARRAY_A );
                break;
            case 'a3':
                $results = $wpdb->get_results( "
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id

                    ", ARRAY_A );
                break;
            case 'world':
                $results = $wpdb->get_results( "
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                             SELECT 'World'
                             FROM $wpdb->postmeta as pm
                                      JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                                      JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                             WHERE pm.meta_key = 'location_grid'
                         ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            case 'full': // full query including world
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                        JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    UNION ALL
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                             SELECT 'World'
                             FROM $wpdb->postmeta as pm
                                      JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                                      JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                             WHERE pm.meta_key = 'location_grid'
                         ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            default:
                $results = $wpdb->get_results( "
                        SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                        FROM (
                         SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                            FROM $wpdb->postmeta as pm
                            JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                            JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                            LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                            WHERE pm.meta_key = 'location_grid'
                        ) as t0
                        GROUP BY t0.admin0_grid_id
                        UNION ALL
                        SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                        FROM (
                            SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                            FROM $wpdb->postmeta as pm
                            JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                            JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                            LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                            WHERE pm.meta_key = 'location_grid'
                        ) as t1
                        GROUP BY t1.admin1_grid_id
                        UNION ALL
                        SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                        FROM (
                            SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                            FROM $wpdb->postmeta as pm
                            JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                            JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                            LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                            WHERE pm.meta_key = 'location_grid'
                        ) as t2
                        GROUP BY t2.admin2_grid_id
                        UNION ALL
                        SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                        FROM (
                            SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                            FROM $wpdb->postmeta as pm
                            JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                            JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                            LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                            WHERE pm.meta_key = 'location_grid'
                        ) as t3
                        GROUP BY t3.admin3_grid_id
                        ", ARRAY_A );
                    break;
        }

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                if ( empty( $result['grid_id'] ) ) {
                    continue;
                }
                if ( empty( $result['count'] ) ) {
                    continue;
                }
                $list[$result['grid_id']] = $result['count'];
            }
        }

        set_transient( __METHOD__ . $administrative_level, $list, HOUR_IN_SECONDS . 6 );

        return $list;
    }

    public static function clear_multiplier_grid_totals() {
        delete_transient( 'Zume_App_Heatmap::query_multiplier_grid_totals' );
        delete_transient( 'Zume_App_Heatmap::query_multiplier_grid_totalsa0' );
        delete_transient( 'Zume_App_Heatmap::query_multiplier_grid_totalsa1' );
        delete_transient( 'Zume_App_Heatmap::query_multiplier_grid_totalsa2' );
        delete_transient( 'Zume_App_Heatmap::query_multiplier_grid_totalsa3' );
        delete_transient( 'Zume_App_Heatmap::query_multiplier_grid_totalsa4' );
        delete_transient( 'Zume_App_Heatmap::query_multiplier_grid_totalsworld' );
        delete_transient( 'Zume_App_Heatmap::query_multiplier_grid_totalsfull' );
    }

    public static function query_multiplier_grid_totals( $administrative_level = null ) {

        if ( false !== ( $value = get_transient( __METHOD__ . $administrative_level ) ) ) { // phpcs:ignore
            return $value;
        }

        global $wpdb;

        switch ( $administrative_level ) {
            case 'a0':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                                ) as t0
                    GROUP BY t0.admin0_grid_id
                    ", ARRAY_A );
                break;
            case 'a1':
                $results = $wpdb->get_results( "
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                                ) as t1
                    GROUP BY t1.admin1_grid_id
                    ", ARRAY_A );
                break;
            case 'a2':
                $results = $wpdb->get_results( "
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                       SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                                ) as t2
                    GROUP BY t2.admin2_grid_id
                    ", ARRAY_A );
                break;
            case 'a3':
                $results = $wpdb->get_results( "
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                                ) as t3
                    GROUP BY t3.admin3_grid_id
                    ", ARRAY_A );
                break;
            case 'world':
                $results = $wpdb->get_results( "
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            case 'full':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                                ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                                ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                                ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                       SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                                ) as t3
                    GROUP BY t3.admin3_grid_id
                    UNION ALL
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                                ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            default:
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                       SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    UNION ALL
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'contacts'
                        JOIN $wpdb->postmeta as pmm ON pmm.post_id=pm.post_id AND pmm.meta_key = 'tags' AND pmm.meta_value = 'multiplier'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                        AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE ( p.meta_key = 'corresponds_to_user' AND p.meta_value != '') OR ( p.meta_key = 'overall_status' AND p.meta_value = 'closed'))
                    ) as tw
                    GROUP BY 'World'

                    ", ARRAY_A );
                break;
        }

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                if ( empty( $result['grid_id'] ) ) {
                    continue;
                }
                if ( empty( $result['count'] ) ) {
                    continue;
                }
                $list[$result['grid_id']] = $result['count'];
            }
        }

        return $list;
    }

    /**
     * Performance query for groups location_grid without dependency on location_grid_meta
     * @param null $status
     * @return array
     */
    public static function query_groups_location_grid_totals( $status = null ) {

        global $wpdb;

        if ( $status ) {
            $results = $wpdb->get_results( $wpdb->prepare( "
            SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
                AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE p.meta_key = 'group_status' AND p.meta_value = %s )
            ) as t0
            GROUP BY t0.admin0_grid_id
            UNION
            SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
                AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE p.meta_key = 'group_status' AND p.meta_value = %s )
            ) as t1
            GROUP BY t1.admin1_grid_id
            UNION
            SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
                AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE p.meta_key = 'group_status' AND p.meta_value = %s )
            ) as t2
            GROUP BY t2.admin2_grid_id
            UNION
            SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
                AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE p.meta_key = 'group_status' AND p.meta_value = %s )
            ) as t3
            GROUP BY t3.admin3_grid_id
            UNION
            SELECT t4.admin4_grid_id as grid_id, count(t4.admin4_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
                AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE p.meta_key = 'group_status' AND p.meta_value = %s )
            ) as t4
            GROUP BY t4.admin4_grid_id
            UNION
            SELECT t5.admin5_grid_id as grid_id, count(t5.admin5_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
                AND pm.post_id NOT IN (SELECT DISTINCT(p.post_id) FROM $wpdb->postmeta as p WHERE p.meta_key = 'group_status' AND p.meta_value = %s )
            ) as t5
            GROUP BY t5.admin5_grid_id;
            ", $status, $status, $status, $status, $status, $status
            ), ARRAY_A );

        } else {

            $results = $wpdb->get_results( "
            SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t0
            GROUP BY t0.admin0_grid_id
            UNION
            SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
            FROM (
            SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t1
            GROUP BY t1.admin1_grid_id
            UNION
            SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t2
            GROUP BY t2.admin2_grid_id
            UNION
            SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t3
            GROUP BY t3.admin3_grid_id
            UNION
            SELECT t4.admin4_grid_id as grid_id, count(t4.admin4_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t4
            GROUP BY t4.admin4_grid_id
            UNION
            SELECT t5.admin5_grid_id as grid_id, count(t5.admin5_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t5
            GROUP BY t5.admin5_grid_id;
            ", ARRAY_A );
        }

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                if ( empty( $result['grid_id'] ) ) {
                    continue;
                }
                if ( empty( $result['count'] ) ) {
                    continue;
                }
                $list[$result['grid_id']] = $result['count'];
            }
        }

        set_transient( __METHOD__ . $administrative_level, $list, HOUR_IN_SECONDS . 6 );

        return $list;
    }

    public static function query_training_grid_totals( $administrative_level = null ) {
        if ( false !== ( $value = get_transient( __METHOD__ . $administrative_level ) ) ) { // phpcs:ignore
            return $value;
        }
        global $wpdb;

        switch ( $administrative_level ) {
            case 'a0':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    ", ARRAY_A );
                break;
            case 'a1':
                $results = $wpdb->get_results( "
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    ", ARRAY_A );
                break;
            case 'a2':
                $results = $wpdb->get_results( "
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    ", ARRAY_A );
                break;
            case 'a3':
                $results = $wpdb->get_results( "
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    ", ARRAY_A );
                break;
            case 'world':
                $results = $wpdb->get_results( "
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT 'World'
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            case 'full':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    UNION ALL
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT 'World'
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            default:
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->postmeta as pm
                        JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                        LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                        WHERE pm.meta_key = 'location_grid'
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    ", ARRAY_A );
                break;
        }

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                if ( empty( $result['grid_id'] ) ) {
                    continue;
                }
                if ( empty( $result['count'] ) ) {
                    continue;
                }
                $list[$result['grid_id']] = $result['count'];
            }
        }

        set_transient( __METHOD__ . $administrative_level, $list, HOUR_IN_SECONDS . 6 );

        return $list;
    }


    public static function query_registration_grid_totals( $administrative_level = null ) {

        if ( false !== ( $value = get_transient( __METHOD__ . $administrative_level ) ) ) { // phpcs:ignore
            return $value;
        }

        global $wpdb;

        switch ( $administrative_level ) {
            case 'a0':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                             JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t0
                    GROUP BY t0.admin0_grid_id
                    ", ARRAY_A );
                break;
            case 'a1':
                $results = $wpdb->get_results( "
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t1
                    GROUP BY t1.admin1_grid_id
                    ", ARRAY_A );
                break;
            case 'a2':
                $results = $wpdb->get_results( "
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t2
                    GROUP BY t2.admin2_grid_id
                    ", ARRAY_A );
                break;
            case 'a3':
                $results = $wpdb->get_results( "
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t3
                    GROUP BY t3.admin3_grid_id
                    ", ARRAY_A );
                break;
            case 'world':
                $results = $wpdb->get_results( "
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT 'World'
                        FROM $wpdb->usermeta as um
                             LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                        WHERE um.meta_key = 'zume_location_grid_from_ip'
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            case 'full':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t3
                    GROUP BY t3.admin3_grid_id
                    UNION ALL
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT 'World'
                        FROM $wpdb->usermeta as um
                             LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                        WHERE um.meta_key = 'zume_location_grid_from_ip'
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            default:
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t3
                    GROUP BY t3.admin3_grid_id
                    ", ARRAY_A );
                break;
        }

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                if ( empty( $result['grid_id'] ) ) {
                    continue;
                }
                if ( empty( $result['count'] ) ) {
                    continue;
                }
                $list[$result['grid_id']] = $result['count'];
            }
        }

        set_transient( __METHOD__ . $administrative_level, $list, HOUR_IN_SECONDS . 6 );

        return $list;
    }

    public static function query_trained_people_grid_totals( $administrative_level = null ) {

        if ( false !== ( $value = get_transient( __METHOD__ . $administrative_level ) ) ) { // phpcs:ignore
            return $value;
        }

        global $wpdb;

        switch ( $administrative_level ) {
            case 'a0':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                             JOIN $wpdb->usermeta uc ON uc.user_id=um.user_id AND uc.meta_key = 'zume_training_complete'
                             LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t0
                    GROUP BY t0.admin0_grid_id
                    ", ARRAY_A );
                break;
            case 'a1':
                $results = $wpdb->get_results( "
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                             JOIN $wpdb->usermeta uc ON uc.user_id=um.user_id AND uc.meta_key = 'zume_training_complete'
                             LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t1
                    GROUP BY t1.admin1_grid_id
                    ", ARRAY_A );
                break;
            case 'a2':
                $results = $wpdb->get_results( "
                     SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                             JOIN $wpdb->usermeta uc ON uc.user_id=um.user_id AND uc.meta_key = 'zume_training_complete'
                             LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t2
                    GROUP BY t2.admin2_grid_id
                    ", ARRAY_A );
                break;
            case 'a3':
                $results = $wpdb->get_results( "
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                             JOIN $wpdb->usermeta uc ON uc.user_id=um.user_id AND uc.meta_key = 'zume_training_complete'
                             LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t3
                    GROUP BY t3.admin3_grid_id
                    ", ARRAY_A );
                break;
            case 'world':
                $results = $wpdb->get_results( "
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT 'World'
                        FROM $wpdb->usermeta as um
                             JOIN $wpdb->usermeta uc ON uc.user_id=um.user_id AND uc.meta_key = 'zume_training_complete'
                             LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                        WHERE um.meta_key = 'zume_location_grid_from_ip'
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            case 'full':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                                      LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t3
                    GROUP BY t3.admin3_grid_id
                    UNION ALL
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                            SELECT 'World'
                            FROM $wpdb->usermeta as um
                            JOIN $wpdb->usermeta uc ON uc.user_id=um.user_id AND uc.meta_key = 'zume_training_complete'
                            LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                            WHERE um.meta_key = 'zume_location_grid_from_ip'
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            default:
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                             JOIN $wpdb->usermeta uc ON uc.user_id=um.user_id AND uc.meta_key = 'zume_training_complete'
                             LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION ALL
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                             JOIN $wpdb->usermeta uc ON uc.user_id=um.user_id AND uc.meta_key = 'zume_training_complete'
                             LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION ALL
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                             JOIN $wpdb->usermeta uc ON uc.user_id=um.user_id AND uc.meta_key = 'zume_training_complete'
                             LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION ALL
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                             FROM $wpdb->usermeta as um
                             JOIN $wpdb->usermeta uc ON uc.user_id=um.user_id AND uc.meta_key = 'zume_training_complete'
                             LEFT JOIN $wpdb->dt_location_grid as lg ON um.meta_value=lg.grid_id
                             WHERE um.meta_key = 'zume_location_grid_from_ip'
                         ) as t3
                    GROUP BY t3.admin3_grid_id
                    ", ARRAY_A );
                break;
        }

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                if ( empty( $result['grid_id'] ) ) {
                    continue;
                }
                if ( empty( $result['count'] ) ) {
                    continue;
                }
                $list[$result['grid_id']] = $result['count'];
            }
        }

        set_transient( __METHOD__ . $administrative_level, $list, HOUR_IN_SECONDS . 6 );

        return $list;
    }

    public static function query_activity_grid_totals( $administrative_level = null ) {

        if ( false !== ( $value = get_transient( __METHOD__ . $administrative_level ) ) ) { // phpcs:ignore
            return $value;
        }

        global $wpdb;

        switch ( $administrative_level ) {
            case 'a0':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log ml
                        JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                        WHERE ml.grid_id > 0
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    ", ARRAY_A );
                break;
            case 'a1':
                $results = $wpdb->get_results( "
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log ml
                        JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                        WHERE ml.grid_id > 0
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    ", ARRAY_A );
                break;
            case 'a2':
                $results = $wpdb->get_results( "
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log ml
                        JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                        WHERE ml.grid_id > 0
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    ", ARRAY_A );
                break;
            case 'a3':
                $results = $wpdb->get_results( "
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log ml
                        JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                        WHERE ml.grid_id > 0
                    ) as t3
                    GROUP BY t3.admin2_grid_id
                    ", ARRAY_A );
                break;
            case 'world':
                $results = $wpdb->get_results( "
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT 'World'
                        FROM $wpdb->dt_movement_log ml
                        LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                        WHERE ml.grid_id != 0
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            case 'full':
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    UNION
                    SELECT t4.admin4_grid_id as grid_id, count(t4.admin4_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t4
                    GROUP BY t4.admin4_grid_id
                    UNION
                    SELECT t5.admin5_grid_id as grid_id, count(t5.admin5_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t5
                    GROUP BY t5.admin5_grid_id
                    UNION ALL
                    SELECT 1 as grid_id, count('World') as count
                    FROM (
                        SELECT 'World'
                        FROM $wpdb->dt_movement_log ml
                        LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                        WHERE ml.grid_id != 0
                    ) as tw
                    GROUP BY 'World'
                    ", ARRAY_A );
                break;
            default:
                $results = $wpdb->get_results( "
                    SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t0
                    GROUP BY t0.admin0_grid_id
                    UNION
                    SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
                    FROM (
                     SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t1
                    GROUP BY t1.admin1_grid_id
                    UNION
                    SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t2
                    GROUP BY t2.admin2_grid_id
                    UNION
                    SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t3
                    GROUP BY t3.admin3_grid_id
                    UNION
                    SELECT t4.admin4_grid_id as grid_id, count(t4.admin4_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t4
                    GROUP BY t4.admin4_grid_id
                    UNION
                    SELECT t5.admin5_grid_id as grid_id, count(t5.admin5_grid_id) as count
                    FROM (
                        SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                        FROM $wpdb->dt_movement_log as ml
                        JOIN $wpdb->dt_location_grid as lg ON ml.grid_id=lg.grid_id
                        WHERE ml.grid_id > 0
                    ) as t5
                    GROUP BY t5.admin5_grid_id

                    ", ARRAY_A );
                break;
        }

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                if ( empty( $result['grid_id'] ) ) {
                    continue;
                }
                if ( empty( $result['count'] ) ) {
                    continue;
                }
                $list[$result['grid_id']] = $result['count'];
            }
        }

        set_transient( __METHOD__ . $administrative_level, $list, HOUR_IN_SECONDS . 6 );

        return $list;
    }

    public static function query_activity_location_grid_totals() {

        global $wpdb;

        $results = $wpdb->get_results( "
        SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
            FROM (
             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->dt_movement_log ml
                LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                WHERE ml.grid_id != 0
            ) as t0
            GROUP BY t0.admin0_grid_id
            UNION
            SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->dt_movement_log ml
                LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                WHERE ml.grid_id != 0
            ) as t1
            GROUP BY t1.admin1_grid_id
            UNION
            SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->dt_movement_log ml
                LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                WHERE ml.grid_id != 0
            ) as t2
            GROUP BY t2.admin2_grid_id
            UNION
            SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->dt_movement_log ml
                LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                WHERE ml.grid_id != 0
            ) as t3
            GROUP BY t3.admin3_grid_id;
        ", ARRAY_A );

        $list = [];
        if ( is_array( $results ) ) {
            foreach ( $results as $result ) {
                $list[$result['grid_id']] = $result;
            }
        }

        return $list;
    }

    public static function query_grid_elements( $grid_id ) {
        global $wpdb;

        $result = $wpdb->get_row($wpdb->prepare( "
            SELECT
                   lg.admin3_grid_id as a3,
                   lg.admin2_grid_id as a2,
                   lg.admin1_grid_id as a1,
                   lg.admin0_grid_id as a0,
                   1 as world,
                   lg3.population as a3_population,
                   lg2.population as a2_population,
                   lg1.population as a1_population,
                   lg0.population as a0_population,
                   lgw.population as world_population,
                   lg.country_code
            FROM $wpdb->dt_location_grid lg
            LEFT JOIN $wpdb->dt_location_grid lg0 ON lg.admin0_grid_id=lg0.grid_id
            LEFT JOIN $wpdb->dt_location_grid lg1 ON lg.admin1_grid_id=lg1.grid_id
            LEFT JOIN $wpdb->dt_location_grid lg2 ON lg.admin2_grid_id=lg2.grid_id
            LEFT JOIN $wpdb->dt_location_grid lg3 ON lg.admin3_grid_id=lg3.grid_id
            LEFT JOIN $wpdb->dt_location_grid lgw ON 1=lgw.grid_id
            WHERE lg.grid_id = %s
        ", $grid_id ), ARRAY_A );

        return $result;
    }

    /**
     * Shared heatmap functions
     */
    public static function _header(){
        ?>
        <link rel="dns-prefetch" href="https://storage.googleapis.com/" >
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/1.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/2.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/3.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/4.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/5.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/6.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/7.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/8.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/9.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/10.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/11.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/12.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/13.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/14.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/15.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/16.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/17.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/18.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/19.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/20.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/21.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/22.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/23.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/24.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/25.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/26.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/27.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/28.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/29.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/30.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/31.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/32.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/33.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/34.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/35.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/36.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/37.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/38.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/39.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/40.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/41.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/42.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/43.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/44.geojson">
        <link rel="prefetch" href="https://storage.googleapis.com/location-grid-mirror-v2/tiles/world/saturation/45.geojson">
        <style>
            #initialize-screen {
                background-image: url("<?php echo esc_url( plugin_dir_url( __FILE__ ) ) ?>initialize-background.jpg");
                background-size:cover;
            }
        </style>
        <?php
        wp_head();
    }

    public static function wp_enqueue_scripts(){
        wp_enqueue_script( 'lodash' );
        wp_enqueue_script( 'jquery-ui' );
        wp_enqueue_script( 'jquery-touch-punch' );

        wp_enqueue_script( 'heatmap-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'heatmap.js', [
            'jquery',
            'mapbox-cookie',
            'jquery-cookie'
        ], filemtime( plugin_dir_path( __FILE__ ) .'heatmap.js' ), true );

        wp_enqueue_style( 'heatmap-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'heatmap.css', [], filemtime( plugin_dir_path( __FILE__ ) .'heatmap.css' ) );

        wp_enqueue_script( 'jquery-cookie', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js.cookie.min.js', [ 'jquery' ],
        filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'js.cookie.min.js' ), true );

        wp_enqueue_script( 'mapbox-cookie', trailingslashit( get_stylesheet_directory_uri() ) . 'dt-mapping/geocode-api/mapbox-cookie.js', [ 'jquery', 'jquery-cookie' ], '3.0.0' );
    }

    /**
     * Grid list build initial map list of elements and drives sidebar
     * @return array
     */
    public static function _initial_polygon_value_list( $grid_totals, $global_div, $us_div ){
        $flat_grid = self::query_saturation_list();

        $data = [];
        $highest_value = 1;
        foreach ( $flat_grid as $i => $v ){
            $data[$i] = [
                'grid_id' => $i,
                'population' => number_format_i18n( $v['population'] ),
                'needed' => 1,
                'reported' => 0,
                'percent' => 0,
            ];

            $population_division = self::get_population_division( $v['country_code'], $global_div, $us_div );

            $needed = round( $v['population'] / $population_division );
            if ( $needed < 1 ){
                $needed = 1;
            }

            if ( isset( $grid_totals[$v['grid_id']] ) && ! empty( $grid_totals[$v['grid_id']] ) ){
                $reported = $grid_totals[$v['grid_id']];

                if ( ! empty( $reported ) && ! empty( $needed ) ){
                    $data[$v['grid_id']]['needed'] = $needed;

                    $data[$v['grid_id']]['reported'] = $reported;
                    $percent = ceil( $reported / $needed * 100 );
                    if ( 100 < $percent ) {
                        $percent = 100;
                    } else {
                        $percent = number_format_i18n( $percent, 2 );
                    }

                    $data[$v['grid_id']]['percent'] = $percent;
                }
            }
            else {
                $data[$v['grid_id']]['percent'] = 0;
                $data[$v['grid_id']]['reported'] = 0;
                $data[$v['grid_id']]['needed'] = $needed;
            }

            if ( $highest_value < $data[$v['grid_id']]['reported'] ){
                $highest_value = $data[$v['grid_id']]['reported'];
            }
        }

        return [
            'highest_value' => (int) $highest_value,
            'data' => $data
        ];
    }

    public static function get_self( $grid_id, $global_div, $us_div ) {
        global $wpdb;

        // get grid elements for design
        $grid = $wpdb->get_row( $wpdb->prepare( "
            SELECT
              g.grid_id,
              g.level,
              g.alt_name as name,
              gn.alt_name as parent_name,
              g.country_code,
              g.population,
              IF(ROUND(g.population / IF(g.country_code = 'US', %d, %d)) < 1, 1,
                 ROUND(g.population / IF(g.country_code = 'US', %d, %d))) as needed,
              (SELECT COUNT(prs.grid_id) FROM $wpdb->dt_location_grid as prs WHERE prs.parent_id = g.parent_id ) as peers
            FROM $wpdb->dt_location_grid as g
            LEFT JOIN $wpdb->dt_location_grid as gn ON g.parent_id=gn.grid_id
            WHERE g.grid_id = %s
        ", $us_div, $global_div, $us_div, $global_div, $grid_id ), ARRAY_A );

        // set array
        $population_division = self::get_population_division( $grid['country_code'], $global_div, $us_div );
        $data = [
            'level' => $grid['level'],
            'parent_level' => $grid['level'] - 1, // one level higher than current
            'population_division' => number_format_i18n( $population_division ), // label for content not calculation
            'population_division_int' => $population_division, // label for content not calculation
            'name' => $grid['name'],
            'parent_name' => $grid['parent_name'],
            'peers' => number_format_i18n( $grid['peers'] ),
            'population' => number_format_i18n( $grid['population'] ),
            'needed' => number_format_i18n( $grid['needed'] ),
        ];

        return $data;
    }

    public static function endpoint_get_level( $grid_id, $administrative_level, $list, $global_div, $us_div ) {
        // add levels
        $flat_grid = self::query_flat_grid_by_level( $administrative_level, $us_div, $global_div );
        $flat_grid_limited = self::_limit_counts( $flat_grid, $list ); // limit counts to no larger than needed per location.

        $grid = self::query_grid_elements( $grid_id ); // get level ids for grid_id

        if ( isset( $flat_grid_limited[$grid[$administrative_level]] ) && ! empty( $flat_grid_limited[$grid[$administrative_level]] ) ) {
            $level = $flat_grid_limited[$grid[$administrative_level]];
        }
        else {
            return false;
        }

        $percent = $level['reported'] / $level['needed'] * 100 ;
        if ( 100 < $percent ) {
            $percent = 100;
        } else {
            $percent = number_format_i18n( $percent, 2 );
        }

        if ( isset( $flat_grid[$grid[$administrative_level]] ) && ! empty( $flat_grid[$grid[$administrative_level]] ) ) {
            $raw_level = $flat_grid[$grid[$administrative_level]];
            $raw_reported = $raw_level['reported'];
        } else {
            $raw_reported = $level['reported'];
        }

        /**
         * @todo temp cover for populations
         */
        if ( isset( $grid[$administrative_level . '_population'] )
            && ! empty( $grid[$administrative_level . '_population'] )
            && in_array( $administrative_level, [ 'a0', 'world' ] ) ) {
            $level['population'] = $grid[$administrative_level . '_population'];

            $population_division = self::get_population_division( $grid['country_code'], $global_div, $us_div );
            $needed = round( $level['population'] / $population_division );
            if ( $needed < 1 ){
                $needed = 1;
            }
            $level['needed'] = $needed;
            if ( $administrative_level === 'world' ) {
                $world_population = 7860000000;
                $us_population = 331000000;
                $global_pop_block = $global_div;
                $us_pop_block = $us_div;
                $world_population_without_us = $world_population - $us_population;
                $needed_without_us = $world_population_without_us / $global_pop_block;
                $needed_in_the_us = $us_population / $us_pop_block;
                $level['needed'] = $needed_without_us + $needed_in_the_us;
                $percent = $level['reported'] / $level['needed'] * 100 ;
            }
        }
        // @todo end temp cover for populations

        if ( empty( $level['name'] ) ) {
            return false;
        }

        $data = [
            'name' => $level['name'],
            'grid_id' => (int) $level['grid_id'],
            'population' => number_format_i18n( $level['population'] ),
            'needed' => number_format_i18n( $level['needed'] ),
            'reported' => number_format_i18n( $raw_reported ),
            'percent' => number_format_i18n( $percent, 2 ),
        ];

        return $data;
    }

    public static function endpoint_get_activity_level( $grid_id, $administrative_level, $global_div, $us_div ) {

        // add levels
        $list = self::query_activity_grid_totals( $administrative_level ); // get list of training counts
        $flat_grid = self::query_flat_grid_by_level( $administrative_level, $us_div, $global_div );

        $flat_grid_limited = self::_limit_counts( $flat_grid, $list ); // limit counts to no larger than needed per location.

        $grid = self::query_grid_elements( $grid_id ); // get level ids for grid_id

        if ( isset( $flat_grid_limited[$grid[$administrative_level]] ) && ! empty( $flat_grid_limited[$grid[$administrative_level]] ) ) {
            $level = $flat_grid_limited[$grid[$administrative_level]];
        }
        else {
            return false;
        }

        $percent = ceil( $level['reported'] / $level['needed'] * 100 );
        if ( 100 < $percent ) {
            $percent = 100;
        } else {
            $percent = number_format_i18n( $percent, 2 );
        }

        if ( isset( $flat_grid[$grid[$administrative_level]] ) && ! empty( $flat_grid[$grid[$administrative_level]] ) ) {
            $raw_level = $flat_grid[$grid[$administrative_level]];
            $raw_reported = $raw_level['reported'];
        } else {
            $raw_reported = $level['reported'];
        }


        /**
         * @todo temp cover for populations
         */
        if ( isset( $grid[$administrative_level . '_population'] )
            && ! empty( $grid[$administrative_level . '_population'] )
            && in_array( $administrative_level, [ 'a0', 'world' ] ) ) {
            $level['population'] = $grid[$administrative_level . '_population'];

            $population_division = self::get_population_division( $grid['country_code'], $global_div, $us_div );
            $needed = round( $level['population'] / $population_division );
            if ( $needed < 1 ){
                $needed = 1;
            }
            $level['needed'] = $needed;
            if ( $administrative_level === 'world' ) {
                $world_population = 7860000000;
                $us_population = 331000000;
                $global_pop_block = $global_div;
                $us_pop_block = $us_div;
                $world_population_without_us = $world_population - $us_population;
                $needed_without_us = $world_population_without_us / $global_pop_block;
                $needed_in_the_us = $us_population / $us_pop_block;
                $level['needed'] = $needed_without_us + $needed_in_the_us;
            }
        }
        // @todo end temp cover for populations

        if ( empty( $level['name'] ) ) {
            return false;
        }

        $data = [
            'name' => $level['name'],
            'grid_id' => (int) $level['grid_id'],
            'population' => number_format_i18n( $level['population'] ),
            'needed' => number_format_i18n( $level['needed'] ),
            'reported' => number_format_i18n( $raw_reported ),
            'percent' => $percent,
        ];

        return $data;
    }

    /**
     * Function limits counts to no higher than the location need. This keeps from inflating the counts up the levels.
     * @param $flat_grid
     * @param $list
     * @return array
     */
    public static function _limit_counts( $flat_grid, $list ) {
        $flat_grid_limited = [];
        foreach ( $flat_grid as $value ) {
            $flat_grid_limited[$value['grid_id']] = $value;

            if ( isset( $list[$value['grid_id']] ) && ! empty( $list[$value['grid_id']] ) ) {
                if ( $list[$value['grid_id']] <= $value['needed'] ) {
                    $flat_grid_limited[$value['grid_id']]['reported'] = $list[$value['grid_id']];
                } else {
                    $flat_grid_limited[$value['grid_id']]['reported'] = $value['needed'];
                }
            }
        }
        return $flat_grid_limited;
    }

    public static function get_population_division( $country_code, $global_div, $us_div ){
        $population_division = $global_div;
        if ( $country_code === 'US' ){
            $population_division = $us_div;
        }
        return $population_division;
    }


    public static function query_activity_data( $grid_id, $offset ) {
        global $wpdb;
        $ids = [];
        $ids[] = $grid_id;
        $children = Disciple_Tools_Mapping_Queries::get_children_by_grid_id( $grid_id );
        if ( ! empty( $children ) ) {
            foreach ( $children as $child ){
                $ids[] = $child['grid_id'];
            }
        }
        $prepared_list = dt_array_to_sql( $ids );
        // phpcs:disable
        $list = $wpdb->get_results("
                SELECT
                       id,
                       action,
                       category,
                       lng,
                       lat,
                       label,
                       grid_id,
                       payload,
                       timestamp,
                       'A Zúme partner' as site_name
                FROM $wpdb->dt_movement_log
                WHERE grid_id IN ($prepared_list)
                ORDER BY timestamp DESC", ARRAY_A);
        // phpcs:enable
        if ( empty( $list ) ){
            return [];
        }

        foreach ( $list as $index => $item ){
            $list[$index]['payload'] = maybe_unserialize( $item['payload'] );
            $list[$index]['formatted_time'] = gmdate( 'M, d Y, g:i a', $item['timestamp'] + $offset );
        }

        if ( function_exists( 'zume_log_actions' ) ) {
            $list = zume_log_actions( $list );
        }
        if ( function_exists( 'dt_network_dashboard_translate_log_generations' ) ) {
            $list = dt_network_dashboard_translate_log_generations( $list );
        }
        if ( function_exists( 'dt_network_dashboard_translate_log_new_posts' ) ) {
            $list = dt_network_dashboard_translate_log_new_posts( $list );
        }

        foreach ( $list as $index => $item ){
            if ( ! isset( $item['message'] ) ) {
                $list[$index]['message'] = 'Non-public movement event reported.';
            }
        }

        return $list;
    }

    public static function query_multiplier_list_data( $grid_id ) {
        global $wpdb;

        $ids = [];
        $ids[] = $grid_id;
        $children = Disciple_Tools_Mapping_Queries::get_children_by_grid_id( $grid_id );
        if ( ! empty( $children ) ) {
            foreach ( $children as $child ){
                $ids[] = $child['grid_id'];
            }
        }
        $prepared_list = dt_array_to_sql( $ids );
        // phpcs:disable
        $list = $wpdb->get_results("
                SELECT p.post_title, lgm.*
                FROM $wpdb->dt_location_grid_meta as lgm
				JOIN $wpdb->postmeta as pm ON pm.post_id=lgm.post_id AND pm.meta_key = 'tags' AND pm.meta_value = 'multiplier'
                LEFT JOIN $wpdb->posts as p ON p.ID=lgm.post_id
                WHERE grid_id IN ($prepared_list)
                AND lgm.post_type = 'contacts'
                ORDER BY p.post_title DESC;
        ", ARRAY_A);
        // phpcs:enable
        if ( empty( $list ) ){
            return [];
        }

        return $list;
    }


}

class Zume_Public_Heatmap_100hours_Utilities {

    public static function create_initials( $longitude, $latitude, $payload ) : string {
        $letters = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'N',
            'S',
            'M',
            'N',
            'O',
            'P',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'E',
            'A',
            'R',
            'I',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'D',
            'E',
            'M',
            'N',
            'O',
            'P',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'E',
            'A',
            'R',
            'I',
            'E',
            'A',
            'R',
            'I',
            'T',
            'N',
            'S',
            'L',
            'E',
            'A',
            'R',
            'I',
            'N',
            'S',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'C',
            'D',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'A',
            'B',
            'M',
            'N',
            'O',
            'P',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'E',
            'A',
            'R',
            'I',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'A',
            'B',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'E',
            'A',
            'R',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'N',
            'S',
            'M',
            'N',
            'O',
            'P',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'E',
            'A',
            'R',
            'I',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'D',
            'E',
            'M',
            'N',
            'O',
            'P',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'E',
            'A',
            'R',
            'I',
            'E',
            'A',
            'R',
            'I',
            'T',
            'N',
            'S',
            'L',
            'E',
            'A',
            'R',
            'I',
            'N',
            'S',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'C',
            'D',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'A',
            'B',
            'E',
            'A',
            'R',
            'I',
            'T',
            'N',
            'S',
            'L',
            'E',
            'A',
            'R',
            'I',
            'N',
            'S',
            'M',
            'N',
            'O',
            'P',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'E',
            'A',
            'R',
            'I',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'N',
            'S',
        ];
        $fnum = abs( round( $longitude ) );
        $lnum = abs( round( $latitude ) );
        $list = str_split( hash( 'sha256', maybe_serialize( $payload ) ) );
        foreach ( $list as $character ){
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
        if ( $timestamp > strtotime( '-1 hour' ) ) {
            $time_string = self::_time_elapsed_string( '@'.$timestamp );
        }
        else if ( $timestamp > strtotime( 'today+00:00' ) + $timezone_offset ) {
            $time_string = date( 'g:i a', $adjusted_time );
        }
        else {
            $time_string = date( 'D g:i a', $adjusted_time );
        }
        return $time_string;
    }

    public static function _time_elapsed_string( $datetime, $full = false ) {
        $now = new DateTime();
        $ago = new DateTime( $datetime );
        $diff = $now->diff( $ago );

        $diff->w = floor( $diff->d / 7 );
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
                $v = $diff->$k . ' ' . $v . ( $diff->$k > 1 ? 's' : '' );
            } else {
                unset( $string[$k] );
            }
        }

        if ( !$full) { $string = array_slice( $string, 0, 1 );
        }
        return $string ? implode( ', ', $string ) . ' ago' : 'just now';
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

        // @todo remove the precision reduction

//        $restricted = self::_persecuted_countries();
//
//        if ( ! isset( $payload['country'] ) ) { // if country is not set, reduce precision to 111km
//            $location['lng'] = round( $location['lng'], 0 );
//            $location['lat'] = round( $location['lat'], 0 );
//            $location['label'] = '';
//        }
//        else if ( in_array( $payload['country'], $restricted ) ) { // if persecuted country, reduce precision to 111km
//            $location['label'] = ' (' . $payload['country'] . ')';
//            $location['lng'] = round( $location['lng'], 0 );
//            $location['lat'] = round( $location['lat'], 0 );
//        } else { // if non-persecuted country, reduce precision to 11km
//            $location['label'] = ' (' . $location['label'] . ')';
//            $location['lng'] = round( $location['lng'], 3 );
//            $location['lat'] = round( $location['lat'], 3 );
//        }

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

    public static function _get_type( $action ) {

        switch ( $action ) {
            case 'updated_3_month':
            case 'coaching':
            case 'starting_group':
            case 'building_group':
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
                return 'producing';
            case 'zume_training':
            case 'zume_vision':
                return 'joining';
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
                return 'learning';
            default:
                return false;
        }

    }

    public static function create_note_data( $action, $string_elements, $payload ) : array {
        // learning
        // joining
        // producing

        $initials = $string_elements['initials'] ?? '';
        $in_language = $string_elements['in_language'] ?? '';
        $location_label = $string_elements['location_label'] ?? ' (' . $payload['country'] . ') ' ?? '';

        $data = [
            'note' => '',
            'type' => 'learning',
        ];

        switch ( $action ) {
            case 'starting_group':
                $data['note'] = $initials . ' is starting a training group' . $in_language . '! ' . $location_label;
                $data['type'] = 'producing';
                break;
            case 'building_group':
                $data['note'] = $initials . ' is growing a training group' . $in_language . '! ' . $location_label;
                $data['type'] = 'producing';
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
                if ( isset( $payload['group_size'] ) && $payload['group_size'] > 1 ) {
                    $data['note'] = $initials . ' is leading a group of '. $payload['group_size'] .' through session ' . str_replace( '_', '', substr( $action, -2, 2 ) ) . $in_language . '! ' . $location_label;
                } else {
                    $data['note'] = $initials . ' is leading a group through session ' . str_replace( '_', '', substr( $action, -2, 2 ) ) . $in_language . '! ' . $location_label;
                }
                $data['type'] = 'producing';
                break;
            case 'zume_training':
                $data['note'] = $initials . ' is registering for Zúme training' . $in_language . '! ' . $location_label;
                $data['type'] = 'joining';
                break;
            case 'zume_vision':
                $data['note'] = $initials . ' is joining the Zúme community to engage in Disciple Making Movements' . $in_language . '! ' . $location_label;
                $data['type'] = 'joining';
                break;
            case 'coaching':
                $data['note'] = $initials . ' is requesting coaching from Zúme coaches' . $in_language . '! ' . $location_label;
                $data['type'] = 'producing';
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
                $data['note'] = $initials . ' is studying' . $title . $in_language . '! ' . $location_label;
                $data['type'] = 'learning';
                break;
            case 'updated_3_month':
                $data['note'] = $initials . '  made a three month plan to multiply disciples' . $in_language . '! ' . $location_label;
                $data['type'] = 'producing';
                break;
            default:
                break;
        }

        return $data;
    }


    public static function get_activity_list( $filters, $limit = false ) {
        global $wpdb;

        $utc_time = new DateTime( 'now', new DateTimeZone( $filters['timezone'] ) );
        $timezoneOffset = $utc_time->format( 'Z' );

        $additional_where = '';
        if ( ! empty( $filters['bounds'] ) && is_array( $filters['bounds'] ) && $filters['zoom'] > 1.5  ) {
            if ( isset( $filters['bounds']['n_lat'] )
                && isset( $filters['bounds']['s_lat'] )
                && isset( $filters['bounds']['e_lng'] )
                && isset( $filters['bounds']['w_lng'] )
            ) {
                $additional_where .= "
                AND lng < ".$filters['bounds']['e_lng']."
                AND lng > ".$filters['bounds']['w_lng']."
                AND lat > ".$filters['bounds']['s_lat']."
                AND lat < ".$filters['bounds']['n_lat']."
                ";
            }
        }

        if ( 'none' !== $filters['country'] ) {
            $additional_where .= " AND lga0.country_code = '" .$filters['country']. "'";
        }

        $timestamp = strtotime( '-100 hours' );
        $results = $wpdb->get_results( "
                SELECT ml.action, ml.category, ml.lng, ml.lat, ml.label, ml.payload, ml.timestamp, lga0.name as country_name, lga0.country_code
                FROM $wpdb->dt_movement_log as ml
                LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                LEFT JOIN $wpdb->dt_location_grid lga0 ON lga0.grid_id=lg.admin0_grid_id
                WHERE ml.timestamp > $timestamp
                $additional_where
                ORDER BY ml.timestamp DESC
                ", ARRAY_A );

        foreach ( $results as $i => $v ) {
            $t = self::_get_type( $v['action'] );
            if ( empty( $t ) ) {
                unset( $results[$i] );
            } else {
                $results[$i]['type'] = $t;
            }
        }

        foreach ( $results as $result ) {

            $payload = maybe_unserialize( $result['payload'] );

            // BUILD NOTE
            $string_elements = [];

            // time string
            $time_string = self::create_time_string( $result['timestamp'], $timezoneOffset );

            // language
            $string_elements['in_language'] = self::create_in_language_string( $payload );

            // initials string
            $string_elements['initials'] = self::create_initials( $result['lng'], $result['lat'], $payload );

            // location string
            $location = self::create_location_precision( $result['lng'], $result['lat'], $result['label'], $payload );
            $string_elements['label'] = $location['label'];

            // note and type data
            $data = self::create_note_data( $result['action'], $string_elements, $payload );

            $prepared_array = array(
                "note" => esc_html( $data['note'] ),
                "time" => esc_attr( $time_string ),
                "type" => esc_attr( $result['type'] ),
                "language" => esc_attr( $payload['language_code'] ?? '' ),
                "country" => esc_attr( $result['country_code'] )
            );

            // filter out non selected country
            // no filter set
            if ( 'none' === $filters['country'] && 'none' === $filters['language'] && 'none' === $filters['type'] ) {
                $list[] = $prepared_array;
            }
            // country set
            else if ( $prepared_array['country'] === $filters['country'] && 'none' === $filters['language'] && 'none' === $filters['type'] ) {
                $list[] = $prepared_array;
            }
            // language set
            else if ( 'none' === $filters['country'] && $prepared_array['language'] === $filters['language'] && 'none' === $filters['type'] ) {
                $list[] = $prepared_array;
            }
            // type set
            else if ( 'none' === $filters['country'] && 'none' === $filters['language'] && $prepared_array['type'] === $filters['type'] ) {
                $list[] = $prepared_array;
            }
            // language & type set
            else if ( 'none' === $filters['country'] && $prepared_array['language'] === $filters['language'] && $prepared_array['type'] === $filters['type'] ) {
                $list[] = $prepared_array;
            }
            // country & type set
            else if ( $prepared_array['country'] === $filters['country'] && 'none' === $filters['language'] && $prepared_array['type'] === $filters['type'] ) {
                $list[] = $prepared_array;
            }
            // country & language set
            else if ( $prepared_array['country'] === $filters['country'] && $prepared_array['language'] === $filters['language'] && 'none' === $filters['type'] ) {
                $list[] = $prepared_array;
            }
            // country & language & type set
            else if ( $prepared_array['country'] === $filters['country'] && $prepared_array['language'] === $filters['language'] && $prepared_array['type'] === $filters['type'] ) {
                $list[] = $prepared_array;
            }

        } // end foreach loop

        if ( empty( $list ) ) {
            return [
                'list' => [],
                'count' => 0
            ];
        }

        $c = array_chunk( $list, 250 );
        return [
            'list' => $c[0] ?? $list,
            'count' => count( $list )
        ];
    }

    public static function get_activity_geojson() {
        $results = self::query_activity_list();

        $countries = [];
        $languages = [];
        $types = [];

        $features = [];
        foreach ( $results as $result ) {

            $payload = maybe_unserialize( $result['payload'] );

            // count country
            if ( isset( $result['country_code'] ) && !empty( $result['country_code'] ) && ! isset( $countries[$result['country_name']] ) ) {
                $countries[$result['country_name']] = [
                    'code' => $result['country_code'],
                    'name' => $result['country_name'],
                    'count' => 0
                ];
            }
            if ( isset( $result['country_code'] ) ) {
                $countries[$result['country_name']]['count']++;
            }

            // count language
            if ( isset( $payload['language_name'] )
                && isset( $payload['language_code'] )
                && ! isset( $languages[$payload['language_name']] )
            ) {
                $languages[$payload['language_name']] = [
                    'code' => $payload['language_code'],
                    'name' => $payload['language_name'],
                    'count' => 0
                ];
            }
            if ( isset( $payload['language_name'] ) ) {
                $languages[$payload['language_name']]['count']++;
            }

            // count types
            if ( isset( $result['type'] ) && ! empty( $result['type'] ) )
            {
                if ( ! isset( $types[$result['type']] ) ) {
                    $types[$result['type']] = [
                        'code' => $result['type'],
                        'name' => ucwords( $result['type'] ),
                        'count' => 0
                    ];
                }
                $types[$result['type']]['count']++;
            }

            // reduce lng to 1.1 km
            $lng = round( $result['lng'], 2 );
            $lat = round( $result['lat'], 2 );

            $features[] = array(
                'type' => 'Feature',
                'properties' => [
                    'type' => $result['type'] ?? '',
                    'language' => $payload['language_code'] ?? '',
                    'country' => $result['country_code']
                ],
                'geometry' => array(
                    'type' => 'Point',
                    'coordinates' => array(
                        $lng,
                        $lat,
                        1
                    ),
                ),
            );

        } // end foreach loop

        ksort( $countries );
        ksort( $languages );

        $new_data = array(
            'type' => 'FeatureCollection',
            'countries' => $countries,
            'languages' => $languages,
            'types' => $types,
            'features' => $features,
        );

        return $new_data;
    }

    public static function query_activity_list() {
        global $wpdb;
        $timestamp = strtotime( '-100 hours' );
        $results = $wpdb->get_results( "
                SELECT ml.action, ml.category, ml.lng, ml.lat, ml.label, ml.payload, ml.timestamp, lga0.name as country_name, lga0.country_code
                FROM $wpdb->dt_movement_log ml
                LEFT JOIN $wpdb->dt_location_grid lg ON lg.grid_id=ml.grid_id
                LEFT JOIN $wpdb->dt_location_grid lga0 ON lga0.grid_id=lg.admin0_grid_id
                WHERE ml.timestamp > $timestamp
                ORDER BY ml.timestamp DESC
                ", ARRAY_A );

        foreach ( $results as $i => $v ) {
            $t = self::_get_type( $v['action'] );
            if ( empty( $t ) ) {
                unset( $results[$i] );
            } else {
                $results[$i]['type'] = $t;
            }
        }

        return $results;
    }
}
