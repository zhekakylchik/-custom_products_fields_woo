
function art_woo_add_custom_fields() {

	global $product, $post;

	echo '<div class="options_group">'; // Grouping fields.

	// text field.
	woocommerce_wp_text_input(
		[
			'id'                => '_text_field',
			'label'             => __( 'text field', 'woocommerce' ),
			'placeholder'       => 'text field',
			'desc_tip'          => 'true',
			'custom_attributes' => [ 'required' => 'required' ],
			'description'       => __( 'Enter field value here', 'woocommerce' ),
		]
	);

	// number_field.
	woocommerce_wp_text_input(
		[
			'id'                => '_number_field',
			'label'             => __( 'number_field', 'woocommerce' ),
			'placeholder'       => 'Entering numbers',
			'description'       => __( 'Enter numbers only', 'woocommerce' ),
			'type'              => 'number',
			'custom_attributes' => [
				'step' => 'any',
				'min'  => '0',
			],
		]
	);

	// textarea.
	woocommerce_wp_textarea_input(
		[
			'id'            => '_textarea', // Field identifier.
			'label'         => 'textarea', // Field title.
			'placeholder'   => 'Text entry', // Text inside the field.
			'class'         => 'textarea-field', // Custom field class.
			'style'         => 'width: 70%; background:red', // Custom styles for the field.
			'wrapper_class' => 'wrap-textarea', // Field wrapper class.
			'desc_tip'      => 'true', // Enable tooltip.
			'description'   => 'Here you can enter anything', // Field Description.
			'name'          => 'textarea-field', // Field name.
			'rows'          => '5', // Field height in lines of text.
			'col'           => '10', // Field width in symbols.
		]
	);

	// Selecting a value.
	woocommerce_wp_select(
		[
			'id'      => '_select',
			'label'   => 'dropdown list',
			'options' => [
				'one'   => __( 'Option 1', 'woocommerce' ),
				'two'   => __( 'Option 2', 'woocommerce' ),
				'three' => __( 'Option 3', 'woocommerce' ),
			],
		]
	);

	// Checkbox.
	woocommerce_wp_checkbox(
		[
			'id'            => '_checkbox',
			'wrapper_class' => 'show_if_simple',
			'label'         => 'Checkbox',
			'description'   => 'Choose me!',
		]
	);

	// Radio buttons.
	woocommerce_wp_radio(
		[
			'id'            => '_radiobutton',
			'label'         => 'Radio buttons',
			'class'         => 'radio-field', // Custom field class.
			'style'         => '', // Custom styles for the field.
			'wrapper_class' => 'wrap-radio', // Field wrapper class.
			'desc_tip'      => 'true', // Enable tooltip.
			'description'   => 'Select value', // Field Description.
			'name'          => 'radio-field', // Field name.
			'options'       => [
				'one'   => 'one',
				'two'   => 'two',
				'three' => 'three',
			],
		]
	);

	// hidden field.
	woocommerce_wp_hidden_input(
		[
			'id'    => '_hidden_field',
			'value' => 'hidden_value',
		]
	);
	echo '</div>'; // End tag Field groupings
  
  
	// Product selection.
	?>
	<p class="form-field product_field_type">
		<label for="product_field_type">Product selection</label>
		<select
			id="product_field_type"
			name="product_field_type[]"
			class="wc-product-search"
			multiple="multiple"
			style="width: 50%;"
			data-placeholder="<?php esc_attr_e( 'Search for a product…', 'woocommerce' ); ?>"
			data-action="woocommerce_json_search_products_and_variations"
			data-exclude="<?php echo intval( $post->ID ); ?>">
			<?php
			$product_ids            = [];
			$product_field_type_ids = get_post_meta( $post->ID, '_product_field_type_ids', true );

			if ( ! empty( $product_field_type_ids ) ) {
				$product_ids = array_map( 'absint', $product_field_type_ids );
			}

			if ( $product_ids ) {
				foreach ( $product_ids as $product_id ) {
					$product = wc_get_product( $product_id );

					echo sprintf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $product_id ),
						selected( true, true, false ),
						esc_html( $product->get_formatted_name() )
					);
				}
			}
			?>
		</select>
		<span class="woocommerce-help-tip" data-tip="Here you can enter some description"></span>
	</p>

	<div class="options_group">
		<h2><strong>Arbitrary group of fields</strong></h2>
		<p class="form-field custom_field_type">
			<label for="custom_field_type">
				<?php echo 'Packed size (mm)'; ?>
			</label>
			<span class="wrap">
			<input
				placeholder="Length"
				class="input-text wc_input_decimal"
				size="6"
				type="text"
				name="_pack_length"
				value="<?php echo esc_attr( get_post_meta( $post->ID, '_pack_length', true ) ); ?>"
				style="width: 15.75%;margin-right: 2%;"/>
			<input
				placeholder="Width"
				class="input-text wc_input_decimal"
				size="6"
				type="text"
				name="_pack_width"
				value="<?php echo esc_attr( get_post_meta( $post->ID, '_pack_width', true ) ); ?>"
				style="width: 15.75%;margin-right: 2%;"/>
			<input
				placeholder="height"
				class="input-text wc_input_decimal"
				size="6"
				type="text"
				name="_pack_height"
				value="<?php echo esc_attr( get_post_meta( $post->ID, '_pack_height', true ) ); ?>"
				style="width: 15.75%;margin-right: 0;"/>
		</span>
			<span
				class="description">
			<?php echo esc_html( wc_help_tip( 'Enter the size of the product in the package in the format LхWхH' ) ); ?>
		</span>
		</p>
	</div>
	<?php
}

add_action( 'woocommerce_product_options_general_product_data', 'art_woo_add_custom_fields' );


/**
 * Saving data of custom fields by WooCommerce methods 
 */
add_action( 'woocommerce_process_product_meta', 'art_woo_custom_fields_save', 10 );
function art_woo_custom_fields_save( $post_id ) {

	// Calling a class object
	$product = wc_get_product( $post_id );

	// Saving a text field
	$text_field = isset( $_POST['_text_field'] ) ? sanitize_text_field( $_POST['_text_field'] ) : '';
	$product->update_meta_data( 'special_price', $text_field );

	// Saving a number field
	$number_field = isset( $_POST['_number_field'] ) ? sanitize_text_field( $_POST['_number_field'] ) : '';
	$product->update_meta_data( '_number_field', $number_field );

	// Saving textarea
	$textarea_field = isset( $_POST['textarea-field'] ) ? sanitize_text_field( $_POST['textarea-field'] ) : '';
	$product->update_meta_data( '_textarea', $textarea_field );

	// Saving select field
	$select_field = isset( $_POST['_select'] ) ? sanitize_text_field( $_POST['_select'] ) : '';
	$product->update_meta_data( '_textarea', $select_field );

	// Saving radiobutton
	$radio_field = isset( $_POST['radio-field'] ) ? sanitize_text_field( $_POST['radio-field'] ) : '';
	$product->update_meta_data( '_radiobutton', $radio_field );

	// Saving checkbox
	$checkbox_field = isset( $_POST['_checkbox'] ) ? 'yes' : 'no';
	$product->update_meta_data( '_checkbox', $checkbox_field );

	// Saving custom field groups
	$pack_length = isset( $_POST['_pack_length'] ) ? sanitize_text_field( $_POST['_pack_length'] ) : '';
	$pack_width  = isset( $_POST['_pack_width'] ) ? sanitize_text_field( $_POST['_pack_width'] ) : '';
	$pack_height = isset( $_POST['_pack_height'] ) ? sanitize_text_field( $_POST['_pack_height'] ) : '';

	$product->update_meta_data( 'pack_length', $pack_length );
	$product->update_meta_data( 'pack_width', $pack_width );
	$product->update_meta_data( 'pack_height', $pack_height );

	// Saving hidden field
	$hidden_field = isset( $_POST['_hidden_field'] ) ? sanitize_text_field( $_POST['_hidden_field'] ) : '';
	$product->update_meta_data( '_hidden_field', $hidden_field );

	//Save all values
	$product->save();

}



//Display fields with formatting

add_action( 'woocommerce_before_add_to_cart_form', 'art_get_text_field_before_add_card' );
function art_get_text_field_before_add_card() {

	// Calling the product object
	$product = wc_get_product();

	// Writing field values to variables
	$text_field     = $product->get_meta( '_text_field', true );
	$num_field      = $product->get_meta( '_number_field', true );
	$textarea_field = $product->get_meta( '_textarea', true );

	// Displaying field values
	if ( $text_field ) :
		?>
		<div class="text-field">
			<strong>Text field: </strong>
			<?php echo $text_field; ?>
		</div>
	<?php endif;
	if ( $num_field ) : ?>
		<div class="number-field">
			<strong>Number field: </strong>
			<?php echo $num_field; ?>
		</div>
	<?php endif;
	if ( $textarea_field ) : ?>
		<div class="textarea-field">
			<strong>Textarea: </strong>
			<?php echo $textarea_field; ?>
		</div>
	<?php
	endif;
}


// Displaying a group of fields in the Additional Information tab

add_action( 'woocommerce_product_additional_information', 'art_get_fields_tab_additional_information' );
function art_get_fields_tab_additional_information() {
	global $post;
	$length_field = get_post_meta( $post->ID, '_pack_length', true );
	$width_field  = get_post_meta( $post->ID, '_pack_width', true );
	$height_field = get_post_meta( $post->ID, '_pack_height', true );
	?>
	<table class="shop_attributes_addon">
		<tbody>
		<tr>
			<th>Package size, <?php echo get_option( 'woocommerce_dimension_unit' );?></th>
			<td class="product_length"><?php echo $length_field; ?></td>
			<td class="product_width"><?php echo $width_field; ?></td>
			<td class="product_height"><?php echo $height_field; ?></td>
		</tr>
		</tbody>
	</table>
	<?php
}
