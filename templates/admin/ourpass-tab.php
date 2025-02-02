<?php
/**
 * Template for rendering individual tabs.
 *
 * @package OurPass
 */

$tab_name = ! empty( $args['tab'] ) ? $args['tab'] : '';

?>
<form method="post" action="options.php">
	<?php
	ourpasswc_maybe_render_cta( 'tab-' . $tab_name );
	settings_fields( $tab_name );
	do_settings_sections( $tab_name );
	submit_button();
	?>
</form>
