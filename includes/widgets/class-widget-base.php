<?php
/**
 * GoDaddy Reseller Store widget base class.
 *
 * Base class for Reseller Store widgets.
 *
 * @class    Reseller_Store/Widgets/Widget_Base
 * @package  WP_Widget
 * @category Class
 * @author   GoDaddy
 * @since    1.6.0
 */

namespace Reseller_Store\Widgets;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

class Widget_Base extends \WP_Widget {

	/**
	 * Display form input field
	 *
	 * @since 1.6.0
	 *
	 * @param  string $field Feield name.
	 * @param  array  $value Value of the field.
	 * @param  array  $label Form label text.
	 * @param  string $type (optional) Input type label text.
	 * @param  string $description (optional) Description text.
	 */
	protected function display_form_input( $field, $value, $label, $type = 'text', $description = '' ) {
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>"><?php echo $label; ?></label>
			<input type="<?php echo $type; ?>" id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" value="<?php echo esc_attr( $value ); ?>" class="widefat">
			<span class="description" ><?php echo $description; ?></span>
		</p>
		<?php
	}

	/**
	 * Display form checkbox field
	 *
	 * @since 1.6.0
	 *
	 * @param  string $field Feield name.
	 * @param  array  $value Value of the field.
	 * @param  array  $label Form label text.
	 */
	protected function display_form_checkbox( $field, $value, $label ) {
		?>
		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" value="1" class="checkbox" <?php checked( $value, true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>">
				<?php echo $label; ?>
			</label>
		</p>
		<?php
	}
}
