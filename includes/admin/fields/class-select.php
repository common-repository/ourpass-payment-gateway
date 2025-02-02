<?php

/**
 * Select field class.
 */
class OurPass_Form_Select extends OurPass_Form_Field {

	/**
	 * Validate the args before rendering.
	 *
	 * @return bool
	 */
	protected function should_do_render() {
		if ( empty( $this->args['options'] ) ) {
			return false;
		}

		return parent::should_do_render();
	}

	/**
	 * Get the default args for the field type.
	 *
	 * @return array
	 */
	protected function get_default_args() {
		return array(
			'name'        => '',
			'id'          => '',
			'class'       => 'ourpass-select',
			'description' => '',
			'options'     => array(),
			'value'       => ''
		);
	}

	/**
	 * Render the field.
	 */
	protected function render() {
		?>
		<select
			class="<?php echo \esc_attr( $this->args['class'] ); ?>"
			name="<?php echo \esc_attr( $this->args['name'] ); ?>"
			id="<?php echo \esc_attr( $this->args['id'] ); ?>"
		>
		<?php
		foreach ( $this->args['options'] as $value => $label ) :
			?>
		<option value="<?php echo \esc_attr( $value ); ?>" <?php \selected( $this->args['value'], $value ); ?>><?php echo \esc_html( $label ); ?></option>
			<?php
		endforeach;
		?>
		</select>
		<?php
	}
}
