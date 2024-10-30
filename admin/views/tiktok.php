<?php
$accounts       = WTIK_Plugin::app()->getPopulateOption( WTIK_ACCOUNT_OPTION_NAME, array() );
$count_accounts = count( $accounts );
?>
<div class="factory-bootstrap-431 factory-fontawesome-000">
    <div class="row">
        <div class="col-md-9">
            <div class="row">
                <form action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
                    <div class="col-md-12">
                        <div class="wtik-add-form">
                            <input type="radio" name="wtik_feed_type" id="wtik_feed_type" class="wtik_feed_type"
                                   value="account" checked>
                            <label for="wtik_feed_type"><?php _e( 'Account', 'tiktok-feed' ) ?></label>
                            <input type="radio" name="wtik_feed_type" id="wtik_feed_type" class="wtik_feed_type"
                                   value="hashtag">
                            <label for="wtik_feed_type"><?php _e( 'Hashtag', 'tiktok-feed' ) ?></label>
                        </div>
                        <div class="wtik-add-form">
                            <input type="text" name="wtik_feed_account" id="wtik_feed_account" class=""
                                   placeholder="<?php _e( 'Username', 'tiktok-feed' ) ?>">
                            <input type="text" name="wtik_feed_hashtag" id="wtik_feed_hashtag" class=""
                                   placeholder="<?php _e( 'Hashtag', 'tiktok-feed' ) ?>" style="display: none;">
                        </div>
                        <div class="wtik-add-form">
                            <input type="submit" class="wtik-btn-tiktok-account"
                                   value="<?php _e( 'Add Feed', 'tiktok-feed' ) ?>">
                        </div>
                    </div>
                </form>

                <div class="col-md-12">
					<?php
					if ( count( $accounts ) ) :
						?>
                        <h3><?php _e( 'Feeds', 'tiktok-feed' ) ?></h3>
                        <table class="widefat wtik-table wtik-personal-status">
                            <thead>
                            <tr>
                                <th><?php echo __( 'Image', 'tiktok-feed' ); ?></th>
                                <th><?php echo __( 'User', 'tiktok-feed' ); ?></th>
                                <th><?php echo __( 'Name', 'tiktok-feed' ); ?></th>
                                <th><?php echo __( 'Video', 'tiktok-feed' ); ?></th>
                                <th style="width: 256px"><?php echo __( 'Action', 'tiktok-feed' ); ?></th>
                            </tr>
                            </thead>
                            <tbody>
							<?php
							foreach ( $accounts as $profile_info ) {
								?>
                                <tr>
                                    <td class="profile-picture">
										<?php if ( $profile_info['type'] == 'account' ):
											$icon = esc_url( $profile_info['profile_pic_url'] );
										else:
											$icon = WTIK_PLUGIN_URL."/admin/assets/img/hashtag.png";
										endif; ?>
                                        <img src="<?= $icon; ?>"
                                             width="50" alt=""/>
                                    </td>
                                    <td>
                                        <a href="<?= $profile_info['link']; ?>"><?= $profile_info['username']; ?></a>
                                    </td>
                                    <td>
										<?= $profile_info['full_name']; ?>
                                    </td>
                                    <td>
										<?= $profile_info['video_count']; ?>
                                    </td>
                                    <td>
                                        <a href="#"
                                           data-item_id="<?php echo ! empty( $profile_info['id'] ) ? $profile_info['id'] : 0; ?>"
                                           data-is_business="0"
                                           class="btn btn-danger wtik-delete-account">
                                            <span class="dashicons dashicons-trash"></span><?php echo __( 'Delete', 'tiktok-feed' ); ?>
                                        </a>
                                        <span class="spinner"
                                              id="wtik-delete-spinner-<?php echo ! empty( $profile_info['id'] ) ? $profile_info['id'] : 0; ?>"></span>
                                    </td>
                                </tr>
								<?php
							}
							?>
                            </tbody>
                        </table>
						<?php wp_nonce_field( $this->plugin->getPrefix() . 'settings_form', $this->plugin->getPrefix() . 'nonce' ); ?>
					<?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div id="wtik-dashboard-widget" class="wtik-right-widget">
				<?php
				if ( ! WTIK_Plugin::app()->is_premium() ) {
					WTIK_Plugin::app()->get_adverts_manager()->render_placement( 'right_sidebar' );
				}
				?>
            </div>
        </div>
    </div>
</div>