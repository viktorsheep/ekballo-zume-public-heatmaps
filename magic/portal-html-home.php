<?php
$parts_post_id = $this->parts["post_id"];
$parts_post = DT_Posts::get_post( $this->post_type, $parts_post_id, true, false );
if ( is_wp_error( $parts_post ) ){
    return;
}

DT_Mapbox_API::geocoder_scripts();

?>
<!-- title -->
<div class="grid-x">
    <div class="cell padding-1" >
        <button type="button" style="margin:1em .5em 1em;" id="menu-icon" data-open="offCanvasLeft"><i class="fi-list" style="font-size:2em;"></i></button>
        <span style="font-size:1.5rem;font-weight: bold;">Home</span>
        <?php if ( ! wp_is_mobile() ) : ?>
            <span class="loading-spinner active" style="float:right;margin:10px;"></span><!-- javascript container -->
        <?php endif; ?>
    </div>
</div>

<!-- nav -->
<?php $this->nav(); ?>

<div id="wrapper">
    <div class="grid-x">
        <div class="cell">
            <p>Thank you <?php echo esc_html( $parts_post['title'] ) ?> for sharing your faithful work with the community.</p>
        </div>
        <div class="cell">
            <a class="button large expanded" href="<?php echo esc_url( site_url() . '/' . $this->parts['root'] . '/' . $this->parts['type'] . '/' . $this->parts['public_key'] . '/profile' ) ?>"><i class="fi-torso"></i> COMMUNITY PROFILE</a>
        </div>
        <div class="cell">
            <a class="button large expanded" onclick="window.open_create_modal()"><i class="fi-plus"></i> ADD NEW CHURCH</a>
        </div>
        <div class="cell">
            <a class="button large expanded" href="<?php echo esc_url( site_url() . '/' . $this->parts['root'] . '/' . $this->parts['type'] . '/' . $this->parts['public_key'] . '/list' ) ?>"><i class="fi-list-thumbnails"></i> EDIT CHURCH LIST</a>
        </div>
        <div class="cell">
            <a class="button large expanded" href="<?php echo esc_url( site_url() . '/' . $this->parts['root'] . '/' . $this->parts['type'] . '/' . $this->parts['public_key'] . '/map' ) ?>"><i class="fi-map"></i> MAP</a>
        </div>
    </div>
</div>

<div class="reveal large" id="edit-modal" data-v-offset="0" data-close-on-click="false" data-reveal>
    <div id="modal-title"></div>
    <div id="modal-content"></div>
    <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">Save</span>
    </button>
</div>

<div class="float">
    <i class="fi fi-plus floating small"></i>
</div>
