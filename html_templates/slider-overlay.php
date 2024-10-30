<?php
/** @var array $args data */
$direction_nav = ( $args['controls'] == 'prev_next' ) ? 'true' : 'false';
$control_nav   = ( $args['controls'] == 'numberless' ) ? 'true' : 'false';


?>
<script type='text/javascript'>
    jQuery(document).ready(function ($) {
        $('.instaslider-nr-<?=$args['widget_id'];?>').pllexislider({
            animation: '<?=$args['animation'];?>',
            slideshowSpeed: <?=$args['slidespeed'];?>,
            directionNav: <?=$direction_nav;?>,
            controlNav: <?=$control_nav;?>,
            prevText: '',
            nextText: '',
            start: function (slider) {
                slider.hover(
                    function () {
                        slider.find('.pllex-control-nav, .pllex-direction-nav').stop(true, true).fadeIn();
                        slider.find('.wtiktok-datacontainer').fadeIn();
                    },
                    function () {
                        slider.find('.pllex-control-nav, .pllex-direction-nav').stop(true, true).fadeOut();
                        slider.find('.wtiktok-datacontainer').fadeOut();
                    }
                );
            }
        });
    });

</script>
<div class='pllexislider pllexislider-overlay instaslider-nr-<?= $args['widget_id']; ?>'>
    <ul class='no-bullet slides' id='wtik-slides'>
		<?php
		foreach ( $args['posts'] as $post ) {
			$description     = $args['description'];
			$link_to         = $post['link_to'];
			$image_url       = $post['image'];
			$caption         = $post['caption'];
			$time            = $post['timestamp'];
			$username        = $post['username'];
			$caption         = wp_trim_words( $caption, $args['caption_words'], '' );
			$clean_image_url = WTIK_PLUGIN_URL . "/assets/image.png";
			$image_src       = "<img src='{$clean_image_url}' alt='' style='opacity: 0;'>";
			$image_output    = "<a href='{$link_to}' target='_blank'  rel='nofollow noopener noreferer'>{$image_src}</a>";

			$full_description = "";
			if ( $time && in_array( 'time', $description ) ) {
				$time             = human_time_diff( $time );
				$full_description .= "<span class='wtiktok-time'>{$time} ago</span>";
			}

			if ( in_array( 'username', $description ) && $username ) {
				$full_description .= "<span class='wtiktok-username'>by <a rel='nofollow' href='https://www.instagram.com/{$username}/' target='_blank'>{$username}</a></span>";
			}

			if ( $caption != '' && in_array( 'caption', $description ) ) {
				$tiktok_url       = WTIK_Api::app()->get_tiktok_url();
				$caption          = preg_replace( '/\@([a-z0-9А-Яа-я_-]+)/u', "&nbsp;<a href='{$tiktok_url}/@$1/' rel='nofollow' target='_blank'>@$1</a>&nbsp;", $caption );
				$caption          = preg_replace( '/\#([a-zA-Z0-9А-Яа-я_-]+)/u', "&nbsp;<a href='{$tiktok_url}/tag/$1/' rel='nofollow' target='_blank'>$0</a>&nbsp;", $caption );
				$full_description .= "<span class='wtiktok-caption' style='text-align: left !important;'>{$caption}</span>";
			}

			?>
            <li class=''>
                <div id='wtik-image-overlay'
                     style="background: url('<?= $image_url; ?>') no-repeat center center; background-size: cover;">
					<?= $link_to ? $image_output : $image_src; ?>
                </div>
				<?php

				if ( is_array( $args['description'] ) && count( $args['description'] ) >= 1 ) {
					?>
                    <div class='wtiktok-wrap'>
                        <div class='wtiktok-datacontainer'>
							<?= $full_description; ?>
                        </div>
                    </div>
					<?php
				}
				?>
            </li>
			<?php
		}
		?>
    </ul>
</div>
