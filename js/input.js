(function($){
	
	// initialized on ACF events
	function initialize_field( $el ) {

		// Cache jquery selectors
		// Values to get/set
		var $id 	= $el.find('.acf-focal_point-id'),
			$top 	= $el.find('.acf-focal_point-top'),
			$left 	= $el.find('.acf-focal_point-left'),
			$right 	= $el.find('.acf-focal_point-right'),
			$bottom = $el.find('.acf-focal_point-bottom'),

			// Elements to get/set 
			$fp 	= $el.find('.acf-focal_point'),
			$img 	= $el.find('.acf-focal_point-image'),
			$canvas = $el.find('.acf-focal_point-canvas'),

			// Buttons to trigger events
			$add 	= $el.find('.add-image'),
			$del 	= $el.find('.acf-button-delete');


		// Hold/get our values
		var values = {
			id: 	$id.val(),
			top: 	$top.val(),
			left: 	$left.val(),
			width: 	$right.val(),
			height: $bottom.val(),
			size: 	$fp.data('preview_size')
		};
		

		// DOM elements
		var img  	 = $img.get(0),
			canvas 	 = $canvas.get(0);


		// To hold WP media frame
		var file_frame;


		// Vars for Canvas work
		var ctx 		= canvas.getContext("2d"),
        	rect 		= {},
	        mouseDown 	= false,

	        canvasWidth,
	        canvasHeight;




	    // When we've loaded an image, draw the canvas.
	    // (either on dom load or adding new image from WP media manager)
		$img.on("load", drawCanvas).each(function() {
	    	
	    	// Make sure to trigger load event by triggering load
	    	// after jquery has done it's iteration
			if (this.complete) {
				$(this).load();
			}
		});


	    // When resizing the page, redraw the canvas.
	    $(window).on('resize', drawCanvas);


	    // When we click the add image button...
		$add.on('click', function(){

			// If the media frame already exists, reopen it.
			if ( file_frame ) {
				file_frame.open();
				return;
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: 'Select Image',
				button: { text: 'Select' }
			});

			// When an image is selected..
			file_frame.on('select', function() {

				// Get selected image objects
				var attachment 	= file_frame.state().get('selection').first().toJSON(),
					src 		= attachment.sizes[values.size];

				// Make UI active (hide add image button, show canvas)
	        	$fp.addClass('active');

	        	if (src === undefined) {
	        		src = attachment;
	        	}

	        	// Set image to new src, triggering on load
				$img.attr('src', src.url);

				// Update our post values and values obj
				$id.val(attachment.id);
				values.id = attachment.id;

			});

			// Finally, open the modal
			file_frame.open();
		});


		// When we click the delete image button...
	    $del.on('click', function(){

	    	// Reset DOM image attributes
	    	$img.removeAttr('src width height');

	    	// Hide canvas and show add image button
	    	$fp.removeClass('active');

	    	// Reset our post values
			$id.val('');
			$top.val('');
			$left.val('');
			$right.val('');
			$bottom.val('');

			// And our values obj, but just one value (to check later) will do.
	    	values.top = null;
	    });

		// When we click on canvas...
	    canvas.addEventListener("mousedown", function(e) {

	    	// Track our position
	        rect.startX = e.layerX;
	        rect.startY = e.layerY;

	        // And allow drawing
	        mouseDown 	= true;
	    }, false);


	    // When we stopped holding down mouse button, prevent further drawing.
	    canvas.addEventListener("mouseup", function() { mouseDown = false; }, false);


	    // When mouse button is down and we're moving the mouse
	    canvas.addEventListener("mousemove", function(e) {

	        if (mouseDown) {

	        	// Keep drawing image as bottom layer 
	        	// (otherwise we get multiple layers of the focus, making it opaque)
	            drawImg();
	            
	            // Get distance from when we first clicked on canvas
	            rect.w 			= (e.layerX) - rect.startX;
	            rect.h 			= (e.layerY) - rect.startY;

	            // Put positions in our values object
	            values.top 		= rect.startY / canvasHeight;
	            values.left 	= rect.startX / canvasWidth;
	            values.width 	= (rect.w + rect.startX) / canvasWidth;
	            values.height 	= (rect.h + rect.startY) / canvasHeight;

	            // Set post values
            	$top.val(values.top.toFixed(2));
            	$left.val(values.left.toFixed(2));
            	$right.val(values.width.toFixed(2));
            	$bottom.val(values.height.toFixed(2));

            	// draw focal point
	            drawFocus(rect.startX, rect.startY, rect.w, rect.h);
	        }
	    }, false);
	    
		

		// Used to draw the image onto the canvas
		function drawImg() {

			// Ratios previously worked out (resizeCanvas), so it should fill canvas
	        ctx.drawImage(img, 0, 0, canvasWidth, canvasHeight);
	    }

	    // Used to draw focal point on canvas
		function drawFocus(x, y, w, h) {
			ctx.strokeStyle = "rgba(255, 0, 0, 0.6)"
	        ctx.fillStyle = "rgba(255, 0, 0, 0.3)";
	        ctx.strokeRect(x, y, w, h);
	        ctx.fillRect(x, y, w, h);
	    }

	    // Used to draw focal point on load
        function redrawFocus() {

        	// if existing values set...
        	if (values.top !== null) {

        		// Get our positions
	        	var x = values.left * canvasWidth, 
	        		y = values.top * canvasHeight, 
	        		w = (values.width * canvasWidth) - x, 
	        		h = (values.height  * canvasHeight) - y;

	        	// draw focual point
	        	drawFocus(x, y, w, h);
        	}
        }

        // Shortcut to calling canvas draw functions
        function drawCanvas() {

        	// resize, redraw, refocus
	    	resizeCanvas();
	        drawImg();
			redrawFocus();
        }

        // Used to clear canvas
	    function clearCanvas() {

	    	// Faster than clearRect
	        ctx.fillStyle = "#ffffff";
	        ctx.fillRect(0, 0, canvasWidth, canvasHeight);
        }

        // Used to set up canvas sizing
        function resizeCanvas() {

        	// Get natural imge sizes
	        var natural_width 	= img.naturalWidth,
	        	natural_height 	= img.naturalHeight,

	        	// Get image width/height ratio
	        	ratio 			= natural_width / natural_height,

	        	// Get parent width (annoyingly, we have to account for delete button)
	        	parent_width 		= $el.parent().width() - ($del.width()/2),

	        	// To hold new canvas widths
	        	new_width, new_height;


	        // If image is naturally bigger than parent...
	        if (natural_width > parent_width) {

	        	// Set to full width (same as parent)
	        	new_width 	= parent_width;

	        	// And use ratio to work out new proportional height
	        	new_height 	= parent_width / ratio;

	        // Otherwise...
	        } else {

	        	// Set to same width/height as image
		        new_width 	= natural_width;
		        new_height 	= natural_height;
	        }


	        // Set canvas DOM width
        	$canvas.width(new_width);
        	$canvas.height(new_height);

        	// And canvas attribute widths 
        	// (otherwise it gets a weird coord system)
        	canvas.width = new_width;
	    	canvas.height = new_height;


	    	// Remember our new sizes
	    	canvasWidth = new_width;
	        canvasHeight = new_height;
        }
		
	}
	
	
	if( typeof acf.add_action !== 'undefined' ) {
	
		/*
		*  ready append (ACF5)
		*
		*  These are 2 events which are fired during the page load
		*  ready = on page load similar to $(document).ready()
		*  append = on new DOM elements appended via repeater field
		*
		*  @type	event
		*  @date	20/07/13
		*
		*  @param	$el (jQuery selection) the jQuery element which contains the ACF fields
		*  @return	n/a
		*/
		
		acf.add_action('ready append', function( $el ){
			
			// search $el for fields of type 'focal_point'
			acf.get_fields({ type : 'focal_point'}, $el).each(function(){
				
				initialize_field( $(this) );
				
			});
			
		});
		
		
	} else {
		
		
		/*
		*  acf/setup_fields (ACF4)
		*
		*  This event is triggered when ACF adds any new elements to the DOM. 
		*
		*  @type	function
		*  @since	1.0.0
		*  @date	01/01/12
		*
		*  @param	event		e: an event object. This can be ignored
		*  @param	Element		postbox: An element which contains the new HTML
		*
		*  @return	n/a
		*/
		
		$(document).on('acf/setup_fields', function(e, postbox){
			
			$(postbox).find('.field[data-field_type="focal_point"]').each(function(){
				
				initialize_field( $(this) );
				
			});
		
		});
	
	
	}


})(jQuery);
