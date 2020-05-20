<?php
if (! function_exists ('wcckplugin_has_parent_plugin') ) {
	function wcckplugin_has_parent_plugin() {
		if ( is_admin() && ( ! class_exists ('WooCommerce') && current_user_can( 'activate_plugins') ) ) {
			add_action ('admin_notices', create_function( null, 'echo \'<div class="error"><p>\' . sprintf( _(\'Activation failes : <strong> WooCommerce</strong> must be activated to use the <strong>WooCommerce ck </strong> plugin. %sVisit your plugins page to install and activate.\',\'ckPlugin\'),\'<a href="\' . admin_url(\'plugins.php#woocommerce\' ) .\'">\') . \'</a></p></div>\';') );
			deactivate_plugins (plugin_basename (_FILE_));
			if (isset ($_GET['activate'] ) ) {
				unset ($_GET['activate'] );
			}
		}
	}
}