<?php

class DT_Zume_Public_Heatmap {
    public static function query_saturation_list (){
        global $wpdb;

        return $wpdb->get_results("

        SELECT
        lg0.grid_id, lg0.population, lg0.country_code
        FROM $wpdb->dt_location_grid lg0
        WHERE lg0.level <= 2
        #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
	    AND lg0.admin0_grid_id NOT IN (100050711,100219347,100074576,100259978,100018514)
	    #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
	    AND lg0.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

        UNION ALL
        --
        # admin 3 for big countries
        --
        SELECT
        lg1.grid_id, lg1.population, lg1.country_code
        FROM $wpdb->dt_location_grid as lg1
        WHERE
        lg1.level <= 3
        #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
	    AND lg1.admin0_grid_id IN (100050711,100219347,100074576,100259978,100018514)
	    #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
	    AND lg1.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)


        UNION ALL
        --
        # admin 1 for little highly divided countries
        --
        SELECT
        lg2.grid_id, lg2.population, lg2.country_code
        FROM $wpdb->dt_location_grid lg2
        WHERE lg2.level <= 1
        #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
	    AND lg2.admin0_grid_id NOT IN (100050711,100219347,100074576,100259978,100018514)
	    #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
	    AND lg2.admin0_grid_id IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

       ", ARRAY_A );
    }
}
