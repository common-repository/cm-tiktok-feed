<?php
/**
 * @var string $content
 * @var array $insta_widgets
 */
?>
<div class="wtik-widgets-container">

    <div class="wtik-demo-widgets">
		<?php echo $content; ?>
    </div>

    <style>
        .widget-inside
        {
            border-top: none;
            padding: 1px 15px 15px 15px;
            line-height: 1.2;
        }
    </style>
    <script>
        jQuery(document).ready(function ($) {
            $('.widget:not([id*="wtiktok_feed"])').remove();
            //$('[id*="jr_insta_slider"]').before($('#jr_insta_shortcode').val())
            $('.sidebar-name').find('button.handlediv').remove();
        });
    </script>
</div>
