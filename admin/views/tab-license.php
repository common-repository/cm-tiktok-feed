<div class="wrap">
    <div class="factory-bootstrap-431 factory-fontawesome-000">
		<?php wp_nonce_field( 'license' ); ?>
        <div id="wtik-license-wrapper"
             data-loader="<?php echo WTIK_PLUGIN_URL . '/admin/assets/img/loader.gif'; ?>"
             data-plugin="<?php echo get_class( $this->plugin ) ?>">

            <div class="factory-bootstrap-431 onp-page-wrap <?php echo $this->get_license_type() ?>-license-manager-content"
                 id="license-manager">
                <div>
                    <h3><?php printf( __( 'Activate %s', 'tiktok-feed' ), $this->plan_name ) ?></h3>
					<?php echo $this->get_plan_description() ?>
                </div>
                <br>

                <div class="onp-container">
                    <div class="license-details">
						<?php if ( $this->get_license_type() == 'free' ): ?>
                            <a href="<?php echo $this->plugin->get_support()->get_pricing_url( true, 'license_page' ); ?>"
                               class="purchase-premium" target="_blank" rel="noopener">
                            <span class="btn btn-gold btn-inner-wrap">
                            <?php printf( __( 'Upgrade to Premium', 'tiktok-feed' ), $this->premium->get_price() ) ?>
                            </span>
                            </a>
                            <p><?php printf( __( 'Your current license for %1$s:', 'tiktok-feed' ), $this->plugin->getPluginTitle() ) ?></p>
						<?php endif; ?>
                        <div class="license-details-block <?php echo $this->get_license_type() ?>-details-block">
							<?php if ( $this->is_premium ): ?>
                                <a data-action="deactivate" href="#"
                                   class="btn btn-default btn-small license-delete-button wtik-control-btn">
									<?php _e( 'Delete Key', 'tiktok-feed' ) ?>
                                </a>
                                <a data-action="sync" href="#"
                                   class="btn btn-default btn-small license-synchronization-button wtik-control-btn">
									<?php _e( 'Synchronization', 'tiktok-feed' ) ?>
                                </a>
							<?php endif; ?>
                            <h3>
								<?php echo ucfirst( $this->get_plan() ); ?>

								<?php if ( $this->is_premium && $this->premium_has_subscription ): ?>
                                    <span style="font-size: 15px;">
                                    (<?php printf( __( 'Automatic renewal, every %s', '' ), esc_attr( $this->get_billing_cycle_readable() ) ); ?>
                                                )
                                </span>
								<?php endif; ?>
                            </h3>
							<?php if ( $this->is_premium ): ?>
                                <div class="license-key-identity">
                                    <code><?php echo esc_attr( $this->get_hidden_license_key() ) ?></code>
                                </div>
							<?php endif; ?>
                            <div class="license-key-description">
                                <p><?php _e( 'Public License is a GPLv2 compatible license allowing you to change and use this version of the plugin for free. Please keep in mind this license covers only free edition of the plugin. Premium versions are distributed with other type of a license.', 'tiktok-feed' ) ?>
                                </p>
								<?php if ( $this->is_premium && $this->premium_has_subscription ): ?>
                                    <p class="activate-trial-hint">
										<?php _e( 'You use a paid subscription for the plugin updates. In case you don’t want to receive paid updates, please, click <a data-action="unsubscribe" class="wtik-control-btn" href="#">cancel subscription</a>', 'tiktok-feed' ) ?>
                                    </p>
								<?php endif; ?>

								<?php if ( $this->get_license_type() == 'trial' ): ?>
                                    <p class="activate-error-hint">
										<?php printf( __( 'Your license has expired, please extend the license to get updates and support.', 'tiktok-feed' ), '' ) ?>
                                    </p>
								<?php endif; ?>
                            </div>
                            <table class="license-params" colspacing="0" colpadding="0">
                                <tr>
                                    <!--<td class="license-param license-param-domain">
										<span class="license-value"><?php echo esc_attr( $_SERVER['SERVER_NAME'] ); ?></span>
										<span class="license-value-name"><?php _e( 'domain', 'tiktok-feed' ) ?></span>
									</td>-->
                                    <td class="license-param license-param-days">
                                        <span class="license-value"><?php echo $this->get_plan() ?></span>
                                        <span class="license-value-name"><?php _e( 'plan', 'tiktok-feed' ) ?></span>
                                    </td>
									<?php if ( $this->is_premium ) : ?>
                                        <td class="license-param license-param-sites">
                                        <span class="license-value">
                                            <?php echo esc_attr( $this->premium_license->get_count_active_sites() ); ?>
                                            <?php _e( 'of', 'tiktok-feed' ) ?>
                                            <?php echo esc_attr( $this->premium_license->get_sites_quota() ); ?></span>
                                            <span class="license-value-name"><?php _e( 'active sites', 'tiktok-feed' ) ?></span>
                                        </td>
									<?php endif; ?>
                                    <td class="license-param license-param-version">
                                        <span class="license-value"><?php echo $this->plugin->getPluginVersion() ?></span>
                                        <span class="license-value-name"><span><?php _e( 'version', 'tiktok-feed' ) ?></span></span>
                                    </td>
									<?php if ( $this->is_premium ): ?>
                                        <td class="license-param license-param-days">
											<?php if ( $this->get_license_type() == 'trial' ): ?>
                                                <span class="license-value"><?php _e( 'EXPIRED!', 'tiktok-feed' ) ?></span>
                                                <span class="license-value-name"><?php _e( 'please update the key', 'tiktok-feed' ) ?></span>
											<?php else: ?>
                                                <span class="license-value">
													<?php
													if ( $this->premium_license->is_lifetime() ) {
														echo 'infiniate';
													} else {
														echo $this->get_expiration_days();
													}
													?>
                                                            <small> <?php _e( 'day(s)', 'tiktok-feed' ) ?></small>
                                             </span>
                                                <span class="license-value-name"><?php _e( 'remained', 'tiktok-feed' ) ?></span>
											<?php endif; ?>
                                        </td>
									<?php endif; ?>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="license-input">
                        <form action="" method="post">
							<?php if ( $this->is_premium ): ?>
                        <p><?php _e( 'Have a key to activate the premium version? Paste it here:', 'tiktok-feed' ) ?><p>
						<?php else: ?>
                            <p><?php _e( 'Have a key to activate the plugin? Paste it here:', 'tiktok-feed' ) ?>
                            <p>
								<?php endif; ?>
                                <button data-action="activate" class="btn btn-default wtik-control-btn"
                                        type="button"
                                        id="license-submit">
									<?php _e( 'Submit Key', 'tiktok-feed' ) ?>
                                </button>
                            <div class="license-key-wrap">
                                <input type="text" id="license-key" name="licensekey" value=""
                                       class="form-control"/>
                            </div>
							<?php if ( $this->is_premium ): ?>
                                <p style="margin-top: 10px;">
									<?php printf( __( '<a href="%s" target="_blank" rel="noopener">Lean more</a> about the premium version and get the license key to activate it now!', 'tiktok-feed' ), $this->plugin->get_support()->get_pricing_url( true, 'license_page' ) ); ?>
                                </p>
							<?php else: ?>
                                <p style="margin-top: 10px;">
									<?php printf( __( 'Can’t find your key? Go to <a href="%s" target="_blank" rel="noopener">this page</a> and login using the e-mail address associated with your purchase.', 'tiktok-feed' ), "https://users.freemius.com/" ) ?>
                                </p>
							<?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>