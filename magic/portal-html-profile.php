<?php
$parts_post_id = $this->parts["post_id"];
$parts_post = DT_Posts::get_post( $this->post_type, $parts_post_id, true, false );
if (is_wp_error( $parts_post )) {
    return;
}

DT_Mapbox_API::geocoder_scripts();

?>
<!-- title -->
<div class="grid-x">
    <div class="cell padding-1" >
        <button type="button" style="margin:1em .5em 1em;" id="menu-icon" data-open="offCanvasLeft"><i class="fi-list" style="font-size:2em;"></i></button>
        <a style="margin:1em 1em 1em 0; color:black;" href="<?php echo esc_url( site_url() . '/' . $this->parts['root'] . '/' . $this->parts['type'] . '/' . $this->parts['public_key'] . '/' ) ?>"><i class="fi-home" style="font-size:2em;"></i></a>
        <span style="font-size:1.5rem;font-weight: bold;">Community Profile</span>
        <?php if ( ! wp_is_mobile() ) : ?>
            <span class="loading-spinner active" style="float:right;margin:10px;"></span><!-- javascript container -->
        <?php endif; ?>
    </div>
</div>

<!-- nav -->
<?php $this->nav(); ?>

<div id="wrapper">
    <span class="loading-spinner active"></span>
</div>
