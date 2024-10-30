<?php
$current_url = admin_url( 'admin.php?page=settings-' . WTIK_Plugin::app()->getPluginName() );
$current_tab = 'tiktok';
$TABS        = array(
	'tiktok' => array(
		'current' => false,
		'caption' => 'Feeds',
		'icon'    => 'tiktok',
		'url'     => $current_url . "&tab=tiktok",
	),
);
if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
	$current_tab                     = htmlspecialchars( $_GET['tab'] );
	$current_url                     .= "&tab={$current_tab}";
	$TABS[ $current_tab ]['current'] = true;
} else {
	$current_tab                     = 'tiktok';
	$current_url                     .= "&tab={$current_tab}";
	$TABS[ $current_tab ]['current'] = true;
}
?>
<div class="wtik-container">
    <div class="wtik-page-title">
        <h1><?php echo WTIK_Plugin::app()->getPluginTitle() . " " . WTIK_Plugin::app()->getPluginVersion(); ?></h1>
    </div>
    <div id="tabs" class="tabs">
        <nav>
            <ul>
				<?php
				foreach ( $TABS as $key => $tab ) {
					if ( $tab['current'] ) {
						echo "<li class='tab-current'>";
					} else {
						echo "<li>";
					}
					echo "<a href='{$tab['url']}' class='icon-{$tab['icon']}'><span>{$tab['caption']}</span></a>";
					echo "</li>";
				}
				?>
            </ul>
        </nav>
        <div class="content">
            <section id="<?php echo $current_tab; ?>">
				<?php include_once WTIK_PLUGIN_DIR . "/admin/views/{$current_tab}.php"; ?>
            </section>
        </div><!-- /content -->
    </div><!-- /tabs -->
</div>
