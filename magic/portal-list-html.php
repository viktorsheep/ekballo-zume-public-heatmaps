<?php DT_Mapbox_API::geocoder_scripts(); ?>

<!-- title -->
<div class="grid-x">
    <div class="cell padding-1" >
        <button type="button" style="margin:1em .5em 1em;" id="menu-icon" data-open="offCanvasLeft"><i class="fi-list" style="font-size:2em;"></i></button>
        <a style="margin:1em 1em 1em 0; color:black;" href="<?php echo esc_url( site_url() . '/' . $this->parts['root'] . '/' . $this->parts['type'] . '/' . $this->parts['public_key'] . '/' ) ?>"><i class="fi-home" style="font-size:2em;"></i></a>
        <span style="font-size:1.5rem;font-weight: bold;">Edit Church List</span>
        <?php if ( ! wp_is_mobile() ) : ?>
            <span class="loading-spinner active" style="float:right;margin:10px;"></span><!-- javascript container -->
        <?php endif; ?>
    </div>
</div>

<!-- nav -->
<?php $this->nav(); ?>

<div id="wrapper">
    <div class="dd" id="domenu-0">
        <button class="dd-new-item">+</button>
        <div id="initial-loading-spinner"><span class="loading-spinner active"></span></div>
        <li class="dd-item-blueprint" id="" data-prev_parent="domenu-0">
            <button class="collapse" data-action="collapse" type="button" style="display: none;">–</button>
            <button class="expand" data-action="expand" type="button" style="display: none;">+</button>
            <div class="dd-handle dd3-handle">&nbsp;</div>
            <div class="dd3-content">
                <div class="item-name">[item_name]</div>
                <div class="dd-button-container">
                    <button class="item-edit">✎</button>
                    <button class="item-add">+</button>
                    <button class="item-remove" style="display:none;">&times;</button>
                </div>
                <div class="dd-edit-box" style="display: none;">
                    <input type="text" name="title" autocomplete="off" placeholder="Item"
                           data-placeholder="Any nice idea for the title?"
                           data-default-value="Saving New Church {?numeric.increment}">
                </div>
            </div>
        </li>
        <ol class="dd-list"></ol>
    </div>
</div>

<div class="reveal large" id="edit-modal" data-v-offset="0" data-close-on-click="false" data-reveal>
    <div id="modal-title"></div>
    <div id="modal-content"></div>
    <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">Save</span>
    </button>
</div>

<!--<div class="float dd-new-item">-->
<!--    <i class="fi fi-plus floating small"></i>-->
<!--</div>-->
