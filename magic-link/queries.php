<?php

class Zume_Public_Heatmap_Queries {
    /**
     * This query returns the 50k saturation list of locations with population and country code.
     *
     * Returns
     * grid_id, population, country_code
     *
     * @return array
     */
    public static function query_saturation_list () : array {

        if ( false !== ( $value = get_transient( __METHOD__) ) ) {
//            return $value;
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
            # admin 1 for countries that have no level 2 (768)
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

//        set_transient( __METHOD__, $list, MONTH_IN_SECONDS );

        return $list;
    }

    public static function query_church_location_grid_totals( $status = null, $simple = false ) {

        global $wpdb;

        if ( $status ) {
            $results = $wpdb->get_results( $wpdb->prepare( "
            SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'groups'
                JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'group_status' AND pm3.meta_value = %s
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
                JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'group_status' AND pm3.meta_value = %s
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
                JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'group_status' AND pm3.meta_value = %s
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
                JOIN $wpdb->postmeta as pm2 ON pm2.post_id=pm.post_id AND pm2.meta_key = 'group_type' AND pm2.meta_value = 'church'
                JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'group_status' AND pm3.meta_value = %s
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t3
            GROUP BY t3.admin3_grid_id
            ", $status, $status, $status, $status
            ), ARRAY_A );

        } else {

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
        }

        if ( $simple ) {
            // simple gives a key/value pair of grid_id = count
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
        }
        else {
            // gives full array with key = grid_id, count
            $list = [];
            if ( is_array( $results ) ) {
                foreach ( $results as $result ) {
                    $list[$result['grid_id']] = $result;
                }
            }
        }

        return $list;
    }

    public static function query_training_location_grid_totals( $status = null, $simple = false ) {

        global $wpdb;

        if ( $status ) {
            $results = $wpdb->get_results( $wpdb->prepare( "
            SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'status' AND pm3.meta_value = %s
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t0
            GROUP BY t0.admin0_grid_id
            UNION
            SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'status' AND pm3.meta_value = %s
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t1
            GROUP BY t1.admin1_grid_id
            UNION
            SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'status' AND pm3.meta_value = %s
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t2
            GROUP BY t2.admin2_grid_id
            UNION
            SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                JOIN $wpdb->postmeta as pm3 ON pm3.post_id=pm.post_id AND pm3.meta_key = 'status' AND pm3.meta_value = %s
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t3
            GROUP BY t3.admin3_grid_id
            ", $status, $status, $status, $status
            ), ARRAY_A );

        } else {

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
            UNION
            SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t1
            GROUP BY t1.admin1_grid_id
            UNION
            SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t2
            GROUP BY t2.admin2_grid_id
            UNION
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
        }

        if ( $simple ) {
            // simple gives a key/value pair of grid_id = count
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
        }
        else {
            // gives full array with key = grid_id, count
            $list = [];
            if ( is_array( $results ) ) {
                foreach ( $results as $result ) {
                    $list[$result['grid_id']] = $result;
                }
            }
        }

        return $list;
    }

    public static function query_activity_location_grid_totals( ) {

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

    /**
     * This query returns the total amount of reported churches in the system.
     * @todo remove this was a performance fail
     * @return array
     */
    public static function query_totals() : array {

        if ( false !== ( $value = get_transient( 'query_totals' ) ) ) {
            return $value;
        }

        $saturation_list = self::query_saturation_list();
        $churches = self::query_church_location_grid_totals();
//        $trainings = self::query_training_location_grid_totals();
//        $activity = self::query_activity_location_grid_totals();
        $trainings = [];
        $activity = [];

        $data = [];

        foreach( $saturation_list as $location ) {
            $grid_id = $location['grid_id'];

            // set neededed by location
            if ( 'US' === $location['country_code'] ) {
                $neededed = round( (int) $location['population'] / US_POPULATION_BLOCKS );
                if ( $neededed < 1 ) {
                    $neededed = 1;
                }
            } else {
                $neededed = round( (int) $location['population'] / GLOBAL_POPULATION_BLOCKS );
                if ( $neededed < 1 ) {
                    $neededed = 1;
                }
            }
            $trainings_neededed = (int) $neededed;
            $churches_neededed = (int) $neededed * 2;

            $activity_report = ( isset( $activity[$grid_id]['count'] ) ) ? (int) $activity[$grid_id]['count'] : 0;
            $trainings_report = ( isset( $trainings[$grid_id]['count'] ) ) ? (int) $trainings[$grid_id]['count'] : 0;
            $churches_report = ( isset( $churches[$grid_id]['count'] ) ) ? (int) $churches[$grid_id]['count'] : 0;

            $data[$grid_id] = [
                'grid_id' => (int) $grid_id,
                'country_code' => $location['country_code'],
                'population' => (int) $location['population'],
                'population_formatted' => number_format_i18n( $location['population'] ),
                'activity' => [
                    'reported' => $activity_report,
                    'reported_formatted' => number_format_i18n( $activity_report ),
                ],
                'trainings' => [
                    'reported' => $trainings_report,
                    'reported_actual' => $trainings_report,
                    'reported_formatted' => number_format_i18n( $trainings_report ),
                    'neededed' => $trainings_neededed,
                    'neededed_formatted' => number_format_i18n( $trainings_neededed ),
                    'percent' => round($trainings_report / $trainings_neededed * 100 ),
                ],
                'churches' => [
                    'reported' => $churches_report,
                    'reported_actual' => $churches_report,
                    'reported_formatted' => number_format_i18n($churches_report ),
                    'neededed' => $churches_neededed,
                    'neededed_formatted' => number_format_i18n( $churches_neededed ),
                    'percent' => round($churches_report / $churches_neededed * 100 ),
                ],
            ];


            // evaluate if over
            if ( $data[$grid_id]['trainings']['reported'] > 0 && $data[$grid_id]['trainings']['reported'] > $data[$grid_id]['trainings']['neededed'] ) {
                $data[$grid_id]['trainings']['reported'] = $data[$grid_id]['trainings']['neededed'];
                $data[$grid_id]['trainings']['reported_formatted'] = number_format_i18n( $data[$grid_id]['trainings']['neededed'] );
                $data[$grid_id]['trainings']['percent'] = 100;
            }
            // evaluate if over
            if ( $data[$grid_id]['churches']['reported'] > 0 && $data[$grid_id]['churches']['reported'] > $data[$grid_id]['churches']['neededed'] ) {
                $data[$grid_id]['churches']['reported'] = $data[$grid_id]['churches']['neededed'];
                $data[$grid_id]['churches']['reported_formatted'] = number_format_i18n( $data[$grid_id]['churches']['neededed'] );
                $data[$grid_id]['churches']['percent'] = 100;
            }

        }

//        set_transient( 'query_totals', $data, DAY_IN_SECONDS );

        return $data;
    }

    public static function query_training_list(){
        global $wpdb;

        $results = $wpdb->get_results("
        SELECT t0.admin0_grid_id as grid_id, count(t0.admin0_grid_id) as count
            FROM (
             SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id, lg.country_code
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t0
            GROUP BY t0.admin0_grid_id
            UNION
            SELECT t1.admin1_grid_id as grid_id, count(t1.admin1_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id, lg.country_code
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t1
            GROUP BY t1.admin1_grid_id
            UNION
            SELECT t2.admin2_grid_id as grid_id, count(t2.admin2_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id, lg.country_code
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t2
            GROUP BY t2.admin2_grid_id
            UNION
            SELECT t3.admin3_grid_id as grid_id, count(t3.admin3_grid_id) as count
            FROM (
                SELECT lg.admin0_grid_id, lg.admin1_grid_id, lg.admin2_grid_id, lg.admin3_grid_id, lg.admin4_grid_id, lg.admin5_grid_id, lg.country_code
                FROM $wpdb->postmeta as pm
                JOIN $wpdb->posts as p ON p.ID=pm.post_id AND p.post_type = 'trainings'
                LEFT JOIN $wpdb->dt_location_grid as lg ON pm.meta_value=lg.grid_id
                WHERE pm.meta_key = 'location_grid'
            ) as t3
            GROUP BY t3.admin3_grid_id
        ", ARRAY_A );

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

    public static function query_grid_elements( $grid_id ) {
        global $wpdb;

        $result = $wpdb->get_row($wpdb->prepare( "
            SELECT admin3_grid_id, admin2_grid_id, admin1_grid_id, admin0_grid_id
            FROM $wpdb->dt_location_grid
            WHERE grid_id = %s
        ", $grid_id ), ARRAY_A );

        return $result;
    }

    public static function query_flat_grid() {
//        if ( false !== ( $value = get_transient( 'flat_grid' ) ) ) {
//            return $value;
//        }

        global $wpdb;
        $flat_grid_raw = $wpdb->get_results("
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
                         IF(ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000)) < 1, 1,
                         ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000)) < 1, 1,
                         ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000)) < 1, 1,
                         ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000)) < 1, 1,
                         ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000))) as needed
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
                         IF(ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000)) < 1, 1,
                            ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000))) as needed
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


//        set_transient( __METHOD__, $flat_grid_raw, HOUR_IN_SECONDS );

        return $flat_grid_raw;
    }


}
