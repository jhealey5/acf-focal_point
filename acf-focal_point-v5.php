<?php

class acf_field_focal_point extends acf_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/
		
		$this->name = 'focal_point';
		
		
		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/
		
		$this->label = __('Focal Point', 'acf-focal_point');
		
		
		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/
		
		$this->category = 'jquery';
		
		
		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/
		
		$this->defaults = array(
			'save_format'	=>	'tag',
			'preview_size'	=>	'large',
			'image_size'	=>	'large'
		);
		
		
		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('focal_point', 'error');
		*/
		
		$this->l10n = array();
		
				
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field_settings( $field ) {
		
		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/
		
		// Render return value radio
		acf_render_field_setting( $field, array(
			'label'			=> __('Return Value','acf-focal_point'),
			'instructions'	=> __('Specify the returned value on front end','acf-focal_point'),
			'type'			=> 'radio',
			'name'			=> 'save_format',
			'layout'		=>	'horizontal',
			'choices'		=> 	array(
				'object'		=>	__("Image Object",'acf'),
				'tag'			=>	__("Image Tag",'acf')
			)
		));
		
		// Render return size select
		acf_render_field_setting( $field, array(
			'label'			=> __('Image Size','acf-focal_point'),
			'instructions'	=> __('Size of image when returning an image tag','acf-focal_point'),
			'type'			=> 'select',
			'name'			=> 'image_size',
			'choices'		=>	acf_get_image_sizes()
		));
		
		// Render preview size select
		acf_render_field_setting( $field, array(
			'label'			=> __('Preview Size','acf-focal_point'),
			'instructions'	=> __('Image used to create a Focal Point. Should be around the same image ratio as Image Size','acf-focal_point'),
			'type'			=> 'select',
			'name'			=> 'preview_size',
			'choices'		=>	acf_get_image_sizes()
		));
		

	}
	
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field( $field ) {

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
		<span class="acf-button-delete acf-icon -cancel acf-icon-cancel dark" data-name="remove"></span>
		<img class="acf-focal_point-image" src="<?php echo $url; ?>" />
		<canvas class="acf-focal_point-canvas"></canvas>
	</div>

	<div class="clear"></div>

	<div class="no-image">
		<p><?php _e('No image selected','acf'); ?> <input type="button" class="button add-image" value="<?php _e('Add Image','acf'); ?>" />
	</div>

</div><?php
	}
	
		
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function input_admin_enqueue_scripts() {
		
		$dir = plugin_dir_url( __FILE__ );
		
		
		// register & include JS
		wp_register_script( 'acf-input-focal_point', "{$dir}js/input.js", array('acf-input') );
		wp_enqueue_script('acf-input-focal_point');
		
		
		// register & include CSS
		wp_register_style( 'acf-input-focal_point', "{$dir}css/input.css", array('acf-input') ); 
		wp_enqueue_style('acf-input-focal_point');
		
		
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
	
	function format_value( $value, $post_id, $field ) {
		
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
