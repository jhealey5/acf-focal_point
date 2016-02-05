<?php

class acf_field_focal_point extends acf_field {
	
	// vars
	var $settings, // will hold info such as dir / path
		$defaults; // will hold default field options
		
		
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct() {

		// vars
		$this->name 	= 'focal_point';
		$this->label 	= __('Focal Point');
		$this->category = __("jQuery",'acf'); // Basic, Content, Choice, etc
		$this->defaults = array(
			'save_format'	=>	'tag',
			'preview_size'	=>	'large',
			'image_size'	=>	'large'
		);
    	
    	
    	// settings
		$this->settings = array(
			'path' 		=> apply_filters('acf/helpers/get_path', __FILE__),
			'dir' 		=> apply_filters('acf/helpers/get_dir', __FILE__),
			'version' 	=> '1.0.0'
		);
		
		
		// do not delete!
    	parent::__construct();

	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like below) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function create_options( $field ) {

		// Merge defaults
		$field = array_merge($this->defaults, $field);
		
		// Key is needed in the field names to correctly save the data
		$key = $field['name'];
		
		
		// Create Field Options HTML
		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Return Value",'acf'); ?></label>
		<p><?php _e("Specify the returned value on front end",'acf') ?></p>
	</td>
	<td>
		<?php
		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][save_format]',
			'value'		=>	$field['save_format'],
			'layout'	=>	'horizontal',
			'choices'	=> 	array(
				'object'	=>	__("Image Object",'acf'),
				'tag'		=>	__("Image Tag",'acf')
			)
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Image Size",'acf'); ?></label>
		<p class="description"><?php _e("Size of image when returning an image tag",'acf'); ?></p>
	</td>
	<td>
		<?php
		
		do_action('acf/create_field', array(
			'type'		=>	'select',
			'name'		=>	'fields['.$key.'][image_size]',
			'value'		=>	$field['image_size'],
			'choices'	=>	apply_filters('acf/get_image_sizes', array())
		));
		
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Preview Size",'acf'); ?></label>
		<p class="description"><?php _e("Image used to create a Focal Point. Should be around the same image ratio as Image Size",'acf'); ?></p>
	</td>
	<td>
		<?php
		
		do_action('acf/create_field', array(
			'type'		=>	'select',
			'name'		=>	'fields['.$key.'][preview_size]',
			'value'		=>	$field['preview_size'],
			'choices'	=>	apply_filters('acf/get_image_sizes', array())
		));
		
		?>
	</td>
</tr>
	<?php
		
	}
	
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field ) {

		// Merge defaults
		$field = array_merge($this->defaults, $field);
		
		// Get set image id
		$id = (isset($field['value']['id'])) ? $field['value']['id'] : '';


		// data vars
		$data = array(
			'top'		=>	isset($field['value']['top']) ? $field['value']['top'] : '',
			'left'		=>	isset($field['value']['left']) ? $field['value']['left'] : '',
			'right'		=>	isset($field['value']['right']) ? $field['value']['right'] : '',
			'bottom'	=>	isset($field['value']['bottom']) ? $field['value']['bottom'] : '',
		);

		
		// If we already have an image set...
		if ($id) {
			
			// Get image by ID, in size set via options
			$img = wp_get_attachment_image_src($id, $field['preview_size']);
						
		}
			
		// If image found...
		// Set to hide add image button / show canvas
		$is_active 	= ($id) ? 'active' : '';

		// And set src
		$url = ($id) ? $img[0] : '';
		
		
		// create Field HTML
		?>
<div class="acf-focal_point <?php echo $is_active; ?>" data-preview_size="<?php echo $field['preview_size']; ?>">

	<input class="acf-focal_point-id" type="hidden" name="<?php echo $field['name']; ?>[id]" value="<?php echo $id; ?>" />

	<?php foreach ($data as $k => $d): ?>
		<input class="acf-focal_point-<?php echo $k ?>" type="hidden" name="<?php echo $field['name']; ?>[<?php echo $k ?>]" value="<?php echo $d ?>" />
	<?php endforeach ?>

	<div class="has-image">
		<span class="acf-button-delete ir"><?php _e("Remove",'acf'); ?></span>
		<img class="acf-focal_point-image" src="<?php echo $url; ?>" />
		<canvas class="acf-focal_point-canvas"></canvas>
	</div>

	<div class="clear"></div>

	<div class="no-image">
		<p><?php _e('No image selected','acf'); ?> <input type="button" class="button add-image" value="<?php _e('Add Image','acf'); ?>" />
	</div>

</div>
		<?php
	}
	
	
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts() {
		
		// register ACF scripts
		wp_register_script( 'acf-input-focal_point', $this->settings['dir'] . 'js/input.js', 	array('acf-input'), $this->settings['version'] );
		wp_register_style( 	'acf-input-focal_point', $this->settings['dir'] . 'css/input.css', 	array('acf-input'), $this->settings['version'] ); 
		
		// scripts
		wp_enqueue_script( array('acf-input-focal_point') );

		// styles
		wp_enqueue_style( array('acf-input-focal_point') );
		
		
	}
	
	
	/*
	*  format_value_for_api()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is passed back to the API functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value_for_api( $value, $post_id, $field ) {

		// Merge defaults
		$field = array_merge($this->defaults, $field);

		// validate
		if( !$value ) {
			return false;
		}


		// Get image ID
		$id = $value['id'];



		// If returning an image tag...
		if ($field['save_format'] == 'tag') {

			// Get image object at desired size
			$src = wp_get_attachment_image_src( $id, $field['image_size']);

			// Get image alt
			$alt = get_post_meta($id, '_wp_attachment_image_alt', true);

			// Create tag
			$tag  = '<img class="js-focal-point-image" src="' .$src[0]. '" ';
			$tag .= 'alt="' .$alt. '"  width="' .$src[1]. '" height="' .$src[2]. '" ';
			$tag .= 'data-focus-left="' .$value['left']. '" data-focus-top="' .$value['top']. '" ';
			$tag .= 'data-focus-right="' .$value['right']. '" data-focus-bottom="' .$value['bottom']. '" />';

			// Return tag
			$value = $tag;
		}

		// Otherwise if returning an object...
		elseif ($field['save_format'] == 'object') {
			
			
			// Get image object 
			$src = wp_get_attachment_image_src( $id, 'full' );
			
			// validate
			if( !$src ) {
				return false;	
			}

			// Get attachment values
			$attachment = get_post( $id );
			
			// Build return obj
			$value = array(
				'id' 			=> $attachment->ID,

				'focal_point' 	=> array(
					'class'		=> 'js-focal-point-image',
					'top'  		=> $value['top'],
					'left' 		=> $value['left'],
					'right'		=> $value['right'],
					'bottom' 	=> $value['bottom']
				),

				'alt' 			=> get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
				'title' 		=> $attachment->post_title,
				'caption' 		=> $attachment->post_excerpt,
				'description' 	=> $attachment->post_content,
				'mime_type'		=> $attachment->post_mime_type,

				'url' 			=> $src[0],
				'width' 		=> $src[1],
				'height' 		=> $src[2],
				'sizes' 		=> array(),
			);
			
			
			// Gind all image sizes
			$image_sizes = get_intermediate_image_sizes();
			
			// If we have some image sizes...
			if ($image_sizes) {

				// Loop through each one...
				foreach ($image_sizes as $image_size) {

					// Get image obj for each size
					$src = wp_get_attachment_image_src( $id, $image_size );
					
					// Add img obj values to return obj sizes 
					$value['sizes'][$image_size] 			= $src[0];
					$value['sizes'][$image_size.'-width']	= $src[1];
					$value['sizes'][$image_size.'-height']	= $src[2];
				}
				
			} // end if
			
		} // end elseif

		
		return $value;
	}

	
}


// create field
new acf_field_focal_point();

?>
