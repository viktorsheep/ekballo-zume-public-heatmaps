<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Public_Map_Fields {

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
        add_action( 'dt_details_additional_section', [ $this, 'dt_details_additional_section' ], 30, 2 );
        add_filter( 'dt_details_additional_tiles', [ $this, 'dt_details_additional_tiles' ], 10, 2 );
        add_filter( 'dt_custom_fields_settings', [ $this, 'add_active_reporter_status' ], 50, 2 );
    }

    public function dt_details_additional_tiles( $tiles, $post_type = "" ) {
        if ( $post_type === 'contacts' ){
            $tiles["apps"] = [
                "label" => __( "Apps", 'disciple-tools-contact-portal' ),
                "description" => "This tile contains magic link apps and survey tools."
            ];
        }
        return $tiles;
    }

    public function add_active_reporter_status( array $fields, string $post_type = "" ) {
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
                'name' => __( 'Portal Magic Key', 'zume-public-heatmaps' ),
                'type' => 'text',
                'default' => dt_create_unique_key(),
                "hidden" => true,
            ];
//            dt_write_log($fields);

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

    public function dt_details_additional_section( $section, $post_type ) {
        // test if campaigns post type and campaigns_app_module enabled
        if ( $post_type === $this->post_type ) {
            if ( 'apps' === $section ) {
                $record = DT_Posts::get_post( $post_type, get_the_ID() );
                if ( isset( $record[$this->meta_key] )) {
                    $key = $record[$this->meta_key];
                } else {
                    $key = dt_create_unique_key();
                    update_post_meta( get_the_ID(), $this->meta_key, $key );
                }
                $link = DT_Magic_URL::get_link_url( $this->root, $this->type, $key )
                ?>
                <div class="section-subheader">Practitioner Portal</div>
                <div id="practitioner_portal">
                    <a class="button small hollow" href="<?php echo esc_html( $link ); ?>" target="_blank">Open Portal</a>
                    <a class="button small hollow copy_to_clipboard" data-value="<?php echo esc_html( $link ); ?>" target="_blank">Copy Link</a>
                    <!--                <a class="button" id="open-portal-activity" style="cursor:pointer;">Open Activity</a>-->
                </div>

                <script>
                    jQuery(document).ready(function(){
                        jQuery('#open-portal-activity').on('click', function(e){
                            jQuery('#modal-full-title').empty().html(`Portal Activity`)
                            jQuery('#modal-full-content').empty().html(`content`) // @todo add content logic
                            jQuery('#modal-full').foundation('open')
                        })
                    })
                </script>
                <?php
            }
        }
    }
}
Zume_Public_Map_Fields::instance();