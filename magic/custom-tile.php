<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Public_Portal_Fields {

    public $page_title = 'Reporting Portal';
    public $page_description = 'This is a portal for reporting church multiplication and community practitioner profile.';
    public $root = "zume_app";
    public $type = 'portal';
    public $post_type = 'contacts';
    private $meta_key = 'zume_app_portal_magic_key';

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct() {
        add_filter( 'dt_custom_fields_settings', [ $this, 'dt_custom_fields_settings' ], 50, 2 );
        add_filter( "dt_user_list_filters", [ $this, "dt_user_list_filters" ], 10, 2 );
    }

    public function dt_custom_fields_settings( array $fields, string $post_type = "" ) {
        //check if we are dealing with a contact
        if ( $post_type === "contacts" ) {
            if ( isset( $fields["overall_status"] ) && !isset( $fields["overall_status"]["default"]["reporting_only"] ) ) {
                $fields["overall_status"]["default"]["reporting_only"] = [
                    'label' => 'Reporting Only',
                    'description' => 'Contact is a practitioner reporting on church progress.',
                    'color' => '#F43636'
                    ];
            }
            if ( isset( $fields["sources"] ) && !isset( $fields["sources"]["default"]["self_registered_reporter"] ) ) {
                $fields["sources"]["default"]["self_registered_reporter"] = [
                    'label' => 'Self-Registered Reporter',
                    'key' => 'self_registered_reporter',
                    'type' => 'other',
                    'description' => 'Contact came from self-registration portal as a church reporter.',
                    'enabled' => 1
                ];
            }

            $fields["leader_milestones"] = [
                'name' => __( 'Leader Milestones', 'zume-public-heatmaps' ),
                'description' => __( "This is a leader/practitioner working towards movement.", 'zume-public-heatmaps' ),
                'type' => 'multi_select',
                'default' => [
                    'trained' => [
                        'label' => __( 'Trained', 'zume-public-heatmaps' ),
                        'description' => _x( 'Trained in disciple multiplication.', 'field description', 'zume-public-heatmaps' ),
                        "icon" => get_template_directory_uri() . '/dt-assets/images/coach.svg?v=2'
                    ],
                    'practicing' => [
                        'label' => __( 'Practicing', 'zume-public-heatmaps' ),
                        'description' => _x( 'Actively practicing disciple multiplication.', 'field description', 'zume-public-heatmaps' ),
                        "icon" => get_template_directory_uri() . '/dt-assets/images/group-peer.svg?v=2'
                    ],
                    'baptizing' => [
                        'label' => __( 'Baptizing', 'zume-public-heatmaps' ),
                        'description' => _x( 'Actively practicing disciple multiplication.', 'field description', 'zume-public-heatmaps' ),
                        "icon" => get_template_directory_uri() . '/dt-assets/images/group-peer.svg?v=2'
                    ],
                    'planting_churches' => [
                        'label' => __( 'Planting Churches', 'zume-public-heatmaps' ),
                        'description' => _x( 'Has started a simple church and is working to start others.', 'field description', 'zume-public-heatmaps' ),
                        "icon" => get_template_directory_uri() . '/dt-assets/images/stream.svg?v=2'
                    ],
                    'coaching_leaders' => [
                        'label' => __( 'Coaching Leaders', 'zume-public-heatmaps' ),
                        'description' => _x( 'Has experience and can coach other practicing leaders.', 'field description', 'zume-public-heatmaps' ),
                        "icon" => get_template_directory_uri() . '/dt-assets/images/connection-people.svg?v=2'
                    ],
                ],
                "tile" => "faith",
                "in_create_form" => true,
                'icon' => get_template_directory_uri() . "/dt-assets/images/sign-post.svg?v=2",
            ];

            $fields["leader_community_restrictions"] = [
                'name' => __( 'Community Restrictions', 'zume-public-heatmaps' ),
                'description' => __( "Restrictions for communication and publicity of information in the community/coalition/network.", 'zume-public-heatmaps' ),
                'type' => 'multi_select',
                'default' => [
                    'no_inquiries' => [
                        'label' => __( 'No Inquiries', 'zume-public-heatmaps' ),
                        'description' => _x( 'Do not connect me with other community members or forward inquiries to me.', 'field description', 'zume-public-heatmaps' ),
                        "icon" => get_template_directory_uri() . '/dt-assets/images/alert-circle-exc.svg?v=2'
                    ],
                    'no_public_map' => [
                        'label' => __( 'No Public Map', 'zume-public-heatmaps' ),
                        'description' => _x( 'Do not add my location to the public map. Internal maps can have my location.', 'field description', 'zume-public-heatmaps' ),
                        "icon" => get_template_directory_uri() . '/dt-assets/images/alert-circle-exc.svg?v=2'
                    ]
                ],
                "tile" => "details",
                "in_create_form" => true,
                'icon' => get_template_directory_uri() . "/dt-assets/images/sign-post.svg?v=2",
            ];
            $fields["church_reporter"] = [
                "name" => __( 'Reporter for Churches', 'disciple-tools' ),
                'description' => _x( 'The person who is reporting on this church.', 'Optional Documentation', 'disciple-tools-streams' ),
                "type" => "connection",
                "post_type" => "groups",
                "p2p_direction" => "from",
                "p2p_key" => "reporter_to_groups",
                'tile' => 'other',
                'icon' => get_template_directory_uri() . '/dt-assets/images/coach.svg',
                'create-icon' => get_template_directory_uri() . '/dt-assets/images/add-group.svg',
            ];
            $fields["zume_app_portal_magic_key"] = [
                'name' => 'zume_app_portal_magic_key',
                'type' => 'hash',
                'default' => dt_create_unique_key(),
                "hidden" => true,
            ];

        }
        if ( $post_type === "groups" ) {
            $fields["church_reporter"] = [
                "name" => __( 'Church Reporter', 'disciple-tools' ),
                'description' => _x( 'The person who is reporting on this church.', 'Optional Documentation', 'disciple-tools-streams' ),
                "type" => "connection",
                "post_type" => "contacts",
                "p2p_direction" => "to",
                "p2p_key" => "reporter_to_groups",
                'tile' => 'status',
                'icon' => get_template_directory_uri() . '/dt-assets/images/coach.svg',
                'create-icon' => get_template_directory_uri() . '/dt-assets/images/add-contact.svg',
            ];
        }
        return $fields;
    }

    public function dt_user_list_filters( $filters, $post_type ){
        if ( $post_type === $this->post_type ){

            $fields = DT_Posts::get_post_field_settings( $post_type );
            if ( current_user_can( 'dt_all_admin_' . $this->post_type  ) ){
                $counts = self::get_all_status_types();
                $status_counts = [];
                $total_all = 0;
                foreach ( $counts as $count ){
                    $total_all += $count["count"];
                    dt_increment( $status_counts[$count["status"]], $count["count"] );
                }
                $filters["tabs"][] = [
                    "key" => "all",
                    "label" => __( "Leadership Milestones", 'disciple-tools' ),
                    "order" => 20
                ];
                foreach ( $fields["leader_milestones"]["default"] as $status_key => $status_value ) {
                    if ( isset( $status_counts[$status_key] ) ){
                        $filters["filters"][] = [
                            "ID" => 'all_' . $status_key,
                            "tab" => 'all',
                            "name" => $status_value["label"],
                            "query" => [
                                'leader_milestones' => [ $status_key ],
                                'sort' => '-post_date'
                            ],
                            "count" => $status_counts[$status_key]
                        ];
                    }
                }
            }
            else {
                $counts = self::get_my_status();
                /**
                 * Setup my filters
                 */
                $active_counts = [];
                $status_counts = [];
                $total_my = 0;
                foreach ( $counts as $count ){
                    $total_my += $count["count"];
                    dt_increment( $status_counts[$count["status"]], $count["count"] );
                    if ( $count["status"] === "active" ){
                        dt_increment( $active_counts[$count["status"]], $count["count"] );
                    }
                }

                $filters["tabs"][] = [
                    "key" => "assigned_to_me",
                    "label" => __( "My Leader Milestones", 'disciple-tools' ),
                    "order" => 20
                ];
                foreach ( $fields["leader_milestones"]["default"] as $status_key => $status_value ) {
                    if ( isset( $status_counts[$status_key] ) ){
                        $filters["filters"][] = [
                            "ID" => 'my_' . $status_key,
                            "tab" => 'assigned_to_me',
                            "name" => $status_value["label"],
                            "query" => [
                                'assigned_to' => [ 'me' ],
                                'leader_milestones' => [ $status_key ],
                                'sort' => '-post_date'
                            ],
                            "count" => $status_counts[$status_key]
                        ];
                    }
                }
            }
        }
        return $filters;
    }

    public function get_my_status(){
        /**
         * @todo adjust query to return count for update needed
         */
        global $wpdb;
        $post_type = $this->post_type ;
        $current_user = get_current_user_id();

        $results = $wpdb->get_results( $wpdb->prepare( "
            SELECT pm.meta_value as status, count(pm.post_id) as count
             FROM $wpdb->postmeta pm
            INNER JOIN $wpdb->posts a ON ( a.ID = pm.post_id AND a.post_type = %s and a.post_status = 'publish' )
            INNER JOIN $wpdb->postmeta as assigned_to ON pm.post_id=assigned_to.post_id
                          AND assigned_to.meta_key = 'assigned_to'
                          AND assigned_to.meta_value = CONCAT( 'user-', %s )
            WHERE pm.meta_key = 'leader_milestones'
            GROUP BY pm.meta_value
        ", $post_type, $current_user ), ARRAY_A);

//        dt_write_log(__METHOD__);
//        dt_write_log($results);

        return $results;
    }

    //list page filters function
    public function get_all_status_types(){
        /**
         * @todo adjust query to return count for update needed
         */
        global $wpdb;
        if ( current_user_can( 'dt_all_admin_'.$this->post_type ) ){
            $results = $wpdb->get_results($wpdb->prepare( "
                SELECT pm.meta_value as status, count(pm.post_id) as count
                 FROM $wpdb->postmeta pm
                INNER JOIN $wpdb->posts a ON ( a.ID = pm.post_id AND a.post_type = %s and a.post_status = 'publish' )
                WHERE pm.meta_key = 'leader_milestones'
                GROUP BY pm.meta_value
            ", $this->post_type  ), ARRAY_A );
        } else {
            $results = $wpdb->get_results($wpdb->prepare("
                SELECT status.meta_value as status, count(pm.post_id) as count
                FROM $wpdb->postmeta pm
                INNER JOIN $wpdb->postmeta status ON( status.post_id = pm.post_id AND status.meta_key = 'leader_milestones' )
                INNER JOIN $wpdb->posts a ON( a.ID = pm.post_id AND a.post_type = %s and a.post_status = 'publish' )
                LEFT JOIN $wpdb->dt_share AS shares ON ( shares.post_id = a.ID AND shares.user_id = %s )
                LEFT JOIN $wpdb->postmeta assigned_to ON ( assigned_to.post_id = pm.post_id AND assigned_to.meta_key = 'assigned_to' && assigned_to.meta_value = %s )
                WHERE ( shares.user_id IS NOT NULL OR assigned_to.meta_value IS NOT NULL )
                GROUP BY status.meta_value, pm.meta_value
            ", $this->post_type , get_current_user_id(), 'user-' . get_current_user_id() ), ARRAY_A);
        }
//        dt_write_log(__METHOD__);
//        dt_write_log($results);

        return $results;
    }
}
Zume_Public_Portal_Fields::instance();
