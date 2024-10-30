<?php
/** @var array $args account data */
/** @var string $username account username */
/** @var string $profile_pic_url URL of account profile picture */
/** @var int $posts_count count of account posts */
/** @var int $followers count of account followers */
/** @var string $profile_url ULR of account */

$username        = isset( $args['username'] ) ? $args['username'] : '';
$profile_pic_url = ! empty( $args['profile_pic_url'] ) ? $args['profile_pic_url'] : WTIK_PLUGIN_URL . "/admin/assets/img/hashtag.png";
$posts_count     = wtik_shortenNumber( $args['video_count'] );
$followers       = isset( $args['fans_count'] ) ? wtik_shortenNumber( $args['fans_count'] ) : '';
$profile_url     = $args['link'];
?>

<div class="wtik-feed-header">
    <a href="<?php echo esc_url( $profile_url ) ?>" target="_blank" style="text-decoration: none;border: 0 !important;">
        <div class="wtik-box">
            <div class="wtik-header-img">
                <div class="wtik-round wtik-header-neg">
                    <i class="wtik-header-neg-icon"></i>
                </div>
                <img class="wtik-round" style="position: relative" src="<?php echo esc_url( $profile_pic_url ) ?>"
                     alt=""
                     width="50" height="50">
            </div>
            <div class="wtik-header-info">
                <p class="wtik-header-info-username"><?php echo esc_html( $username ) ?></p>
                <p style="margin-top: 0; font-size: 11px">
                    <span class="fa fa-image">&nbsp;<?php echo esc_html( $posts_count ) ?></span>&nbsp;&nbsp;
					<?php if ( ! empty( $followers ) ) : ?>
                        <span class="fa fa-user">&nbsp;<?php echo esc_html( $followers ) ?></span>
					<?php endif; ?>
                </p>
            </div>
        </div>
    </a>
</div>
<br>
