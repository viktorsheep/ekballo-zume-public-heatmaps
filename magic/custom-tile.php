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
        add_filter( 'dt_details_additional_tiles', [ $this, 'dt_details_additional_tiles' ], 10, 2 );
        add_action( 'dt_details_additional_section', [ $this, 'dt_details_additional_section' ], 30, 2 );
        add_filter( 'dt_settings_apps_list', [ $this, 'dt_settings_apps_list' ], 10, 1 );
        add_filter( 'dt_custom_fields_settings', [ $this, 'dt_custom_fields_settings' ], 50, 2 );
    }

    public function dt_details_additional_tiles( $tiles, $post_type = "" ) {
        if ( $post_type === 'contacts' && ! isset( $tiles["apps"] ) ){
            $tiles["apps"] = [
                "label" => __( "Apps", 'disciple_tools' ),
                "description" => __( "Apps available on this record.", 'disciple_tools' )
            ];
        }
        return $tiles;
    }

    public function dt_details_additional_section( $section, $post_type ) {
        if ( $section === "apps" && $post_type === "contacts" ) {
            $record = DT_Posts::get_post( $post_type, get_the_ID() );
            if ( isset( $record[$this->meta_key] )) {
                $key = $record[$this->meta_key];
            } else {
                $key = dt_create_unique_key();
                update_post_meta( get_the_ID(), $this->meta_key, $key );
            }
            $link = DT_Magic_URL::get_link_url( $this->root, $this->type, $key )
            ?>
            <div class="section-subheader"><?php echo esc_html( $this->page_title ) ?></div>
            <div class="section-app-links <?php echo esc_attr( $this->meta_key ); ?>">
                <a type="button" class="empty-select-button select-button small button view"><img class="dt-icon" src="<?php echo get_template_directory_uri() . '/dt-assets/images/visibility.svg?v=2' ?>" /></a>
                <a type="button" class="empty-select-button select-button small button copy_to_clipboard" data-value="<?php echo esc_html( $link ); ?>"><img class="dt-icon" src="<?php echo get_template_directory_uri() . '/dt-assets/images/duplicate.svg?v=2' ?>" /></a>
                <a type="button" class="empty-select-button select-button small button send"><img class="dt-icon" src="<?php echo get_template_directory_uri() . '/dt-assets/images/send.svg?v=2' ?>" /></a>
                <a type="button" class="empty-select-button select-button small button qr"><img class="dt-icon" src="<?php echo get_template_directory_uri() . '/dt-assets/images/qrcode-solid.svg?v=2' ?>" /></a>
                <a type="button" class="empty-select-button select-button small button reset"><img class="dt-icon" src="<?php echo get_template_directory_uri() . '/dt-assets/images/undo.svg?v=2' ?>" /></a>
            </div>
            <script>
                jQuery(document).ready(function(){
                    if ( typeof window.app_key === 'undefined' ){
                        window.app_key = []
                    }
                    if ( typeof window.app_url === 'undefined' ){
                        window.app_url = []
                    }
                    window.app_key['<?php echo esc_attr( $this->meta_key ) ?>'] = '<?php echo esc_attr( $key ) ?>'
                    window.app_url['<?php echo esc_attr( $this->meta_key ) ?>'] = '<?php echo esc_url( site_url() . '/' . $this->root . '/' .$this->type . '/' ) ?>'

                    jQuery('.<?php echo esc_attr( $this->meta_key ); ?>.select-button.button.copy_to_clipboard').data('value', `${window.app_url['<?php echo esc_attr( $this->meta_key ) ?>']}${window.app_key['<?php echo esc_attr( $this->meta_key ) ?>']}`)
                    jQuery('.section-app-links.<?php echo esc_attr( $this->meta_key ); ?> .view').on('click', function(e){
                        jQuery('#modal-large-title').empty().html(`<h3 class="section-header"><?php echo esc_html( $this->page_title )  ?></h3><span class="small-text"><?php echo esc_html( $this->page_description ) ?></span><hr>`)
                        jQuery('#modal-large-content').empty().html(`<iframe src="${window.app_url['<?php echo esc_attr( $this->meta_key ) ?>']}${window.app_key['<?php echo esc_attr( $this->meta_key ) ?>']}" style="width:100%;height: ${window.innerHeight - 170}px;border:1px solid lightgrey;"></iframe>`)
                        jQuery('#modal-large').foundation('open')
                    })
                    jQuery('.section-app-links.<?php echo esc_attr( $this->meta_key ); ?> .send').on('click', function(e){
                        jQuery('#modal-small-title').empty().html(`<h3 class="section-header"><?php echo esc_html( $this->page_title )  ?></h3><span class="small-text">Send a link via email through the system.</span><hr>`)
                        jQuery('#modal-small-content').empty().html(`<div class="grid-x"><div class="cell"><input type="text" class="note <?php echo esc_attr( $this->meta_key ); ?>" placeholder="Add a note" /><br><button type="button" class="button <?php echo esc_attr( $this->meta_key ); ?>">Send Email with Link</button></div></div>`)
                        jQuery('#modal-small').foundation('open')
                        jQuery('.button.<?php echo esc_attr( $this->meta_key ); ?>').on('click', function(e){
                            let note = jQuery('.note.<?php echo esc_attr( $this->meta_key ); ?>').val()
                            makeRequest('POST', window.detailsSettings.post_type + '/email_magic', { root: '<?php echo esc_attr( $this->root ); ?>', type: '<?php echo esc_attr( $this->type ); ?>', magic_key: '<?php echo esc_attr( $this->meta_key ); ?>', note: note, post_ids: [ window.detailsSettings.post_id ] } )
                                .done( data => {
                                    jQuery('#modal-small').foundation('close')
                                })
                        })
                    })
                    jQuery('.section-app-links.<?php echo esc_attr( $this->meta_key ); ?> .qr').on('click', function(e){
                        jQuery('#modal-small-title').empty().html(`<h3 class="section-header"><?php echo esc_html( $this->page_title )  ?></h3><span class="small-text">QR codes are useful for passing the coaching links to mobile devices.</span><hr>`)
                        jQuery('#modal-small-content').empty().html(`<div class="grid-x"><div class="cell center"><img src="https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=${window.app_url['<?php echo esc_attr( $this->meta_key ) ?>']}${window.app_key['<?php echo esc_attr( $this->meta_key ) ?>']}" style="max-width:400px;" /></div></div>`)
                        jQuery('#modal-small').foundation('open')
                    })
                    jQuery('.section-app-links.<?php echo esc_attr( $this->meta_key ); ?> .reset').on('click', function(e){
                        jQuery('#modal-small-title').empty().html(`<h3 class="section-header"><?php echo esc_html( $this->page_title )  ?></h3><span class="small-text">Reset the security code. No data is removed. Only access. The previous link will be disabled and another one created.</span><hr>`)
                        jQuery('#modal-small-content').empty().html(`<button type="button" class="button <?php echo esc_attr( $this->meta_key ); ?> delete-and-reset">Delete and replace the app link</button> <span class="loading-spinner"></span>`)
                        jQuery('#modal-small').foundation('open')
                        jQuery('.button.<?php echo esc_attr( $this->meta_key ); ?>.delete-and-reset').on('click', function(e){
                            jQuery('.button.<?php echo esc_attr($this->meta_key); ?>.delete-and-reset').prop('disable', true)
                            window.API.update_post('<?php echo esc_attr( $post_type ); ?>', <?php echo esc_attr( get_the_ID()); ?>, { ['<?php echo esc_attr( $this->meta_key ); ?>']: window.sha256(Date.now()) })
                                .done( newPost => {
                                    console.log( newPost )
                                    jQuery('#modal-small').foundation('close')
                                    window.app_key['<?php echo esc_attr( $this->meta_key ) ?>'] = newPost['<?php echo esc_attr( $this->meta_key ) ?>']
                                    jQuery('.section-app-links.<?php echo esc_attr( $this->meta_key ); ?> .select-button.button.copy_to_clipboard').data('value', `${window.app_url['<?php echo esc_attr( $this->meta_key ) ?>']}${window.app_key['<?php echo esc_attr( $this->meta_key ) ?>']}`)
                                })
                        })
                    })
                })
            </script>
            <?php
        }
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

    public function dt_settings_apps_list( $apps_list ) {
        $apps_list[$this->meta_key] = [
            'key' => $this->meta_key,
            'url_base' => $this->root. '/'. $this->type,
            'label' => $this->page_title,
            'description' => $this->page_description,
        ];
        return $apps_list;
    }
}
Zume_Public_Portal_Fields::instance();
