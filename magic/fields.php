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
                $fields["overall_status"]["default"]["reporting_only"] = __( "Reporting Only", 'zume-public-heatmaps' );
            }

            if ( isset( $fields["milestones"] ) && !isset( $fields["milestones"]["default"]["milestones_trained"] ) ) {
                $fields["milestones"]["default"]["milestones_trained"] = [
                    "label" => __( "DMM Trained", 'zume-public-heatmaps' ),
                    "description" => 'This is a DMM trained person. This milestone contributes to the Zúme public mapping system.',
                    "icon" => get_template_directory_uri() . "/dt-assets/images/coach.svg?v=2",
                ];
            }
            if ( isset( $fields["milestones"] ) && !isset( $fields["milestones"]["default"]["milestones_practicing"] ) ) {
                $fields["milestones"]["default"]["milestones_practicing"] = [
                    "label" => __( "DMM Practicing", 'zume-public-heatmaps' ),
                    "description" => 'This is a DMM practitioner. This milestone contributes to the Zúme public mapping system.',
                    "icon" => get_template_directory_uri() . "/dt-assets/images/group-peer.svg?v=2",
                ];
            }
//            if ( isset( $fields["milestones"] ) && !isset( $fields["milestones"]["default"]["milestones_reporting"] ) ) {
//                $fields["milestones"]["default"]["milestones_reporting"] = [
//                    "label" => __( "Reporting", 'zume-public-heatmaps' ),
//                    "description" => 'This is a DMM reporting person. This milestone contributes to the Zúme public mapping system.',
//                    "icon" => get_template_directory_uri() . "/dt-assets/images/socialmedia.svg?v=2",
//                ];
//            }
            $fields["practitioner_community_name"] = [
                'name' => __( 'Community Name', 'zume-public-heatmaps' ),
                'description' => __( "Name for sharing in the community.", 'zume-public-heatmaps' ),
                'type' => 'text',
                'default' => '',
                "tile" => "details",
                "in_create_form" => true,
                'icon' => get_template_directory_uri() . "/dt-assets/images/sign-post.svg?v=2",
            ];

            $fields["practitioner_community_restrictions"] = [
                'name' => __( 'Community Restrictions', 'zume-public-heatmaps' ),
                'description' => __( "Restrictions for communication and publicity of information in the community/coalition/network.", 'zume-public-heatmaps' ),
                'type' => 'multi_select',
                'default' => [
                    'no_inquiries' => [
                        'label' => __( 'No Inquiries', 'zume-public-heatmaps' ),
                        'description' => _x( 'Do not connect me with other community members or forward inquiries to me.', 'field description', 'zume-public-heatmaps' ),
                    ],
                    'no_public_map' => [
                        'label' => __( 'No Public Map', 'zume-public-heatmaps' ),
                        'description' => _x( 'Do not add my location to the public map. Internal maps can have my location.', 'field description', 'zume-public-heatmaps' ),
                    ]
                ],
                "tile" => "details",
                "in_create_form" => true,
                'icon' => get_template_directory_uri() . "/dt-assets/images/sign-post.svg?v=2",
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
