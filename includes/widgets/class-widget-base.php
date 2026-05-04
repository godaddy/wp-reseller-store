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

declare(strict_types=1);

namespace Reseller_Store\Widgets;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

class Widget_Base extends \WP_Widget {

	/**
	 * List of allowed tags and attributes for widgets..
	 *
	 * @since 2.0.3
	 *
	 * @var array
	 */
	protected static array $widget_allowed_html;

	/**
	 * Display form input field
	 *
	 * @since 1.6.0
	 *
	 * @param  string $field       Field name.
	 * @param  mixed  $value       Value of the field.
	 * @param  string $label       Form label text.
	 * @param  string $type        (optional) Input type.
	 * @param  string $description (optional) Description text.
	 */
	protected function display_form_input( string $field, mixed $value, string $label, string $type = 'text', string $description = '' ): void {
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>"><?php echo esc_html( $label ); ?></label>
			<input type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" value="<?php echo esc_attr( $value ); ?>" class="widefat">
			<span class="description" ><?php echo esc_html( $description ); ?></span>
		</p>
		<?php
	}

	/**
	 * Display form checkbox field
	 *
	 * @since 1.6.0
	 *
	 * @param  string $field Field name.
	 * @param  mixed  $value Value of the field.
	 * @param  string $label Form label text.
	 */
	protected function display_form_checkbox( string $field, mixed $value, string $label ): void {
		?>
		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" value="1" class="checkbox" <?php checked( $value, true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		</p>
		<?php
	}
}
