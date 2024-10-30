<?php
/** @var array $args data */
$ul_class = 'thumbnails wtik_col_' . $args['columns'];
?>
<div class='wtiktok-thumb'>
    <ul class='no-bullet <?=$ul_class;?>' id='wtik-slides'>
		<?php
		foreach ( $args['posts'] as $post ) {
			$link_to         = $post['link_to'];
			$image_url       = $post['image'];
			$clean_image_url = WTIK_PLUGIN_URL . "/assets/image.png";
			$image_src       = "<img src='{$clean_image_url}' alt='' style='opacity: 0;'>";
			$image_output    = "<a href='{$link_to}' target='_blank'  rel='nofollow noopener noreferer'>{$image_src}</a>";

			?>
            <li>
                <div style="background: url('<?= $image_url; ?>') no-repeat center center; background-size: cover;">
					<?= $link_to ? $image_output : $image_src; ?>
                </div>
            </li>
			<?php
		}
		?>
    </ul>
</div>
