# ACF Focal Point Field

Adds a new field type to ACF allowing users to draw focal points on images. Utilises Responsify.js

-----------------------

### Description

Adds a new field type to ACF allowing users to draw focal points on images. Uses Wenting Zhang's [Responsify.js](https://github.com/wentin/ResponsifyJS/) to create focal points on images using jQuery.

Works similar to a traditional ACF image field, but once an image is selected and a preview is displayed, this plugin allows drawing a box around a focal point in the image using canvas. That image can then be displayed in a nice responsified fashion. View the [ResponsifyJs](http://responsifyjs.space/) homepage for a demo.

Image returned as either tag or object to use within themes. Includes responsify.js.

See Frequently Asked Questions for troubleshooting and detailed usage.

Last tested on ACF v4 (4.4.5) & ACF Pro (5.3.3.2).

-----------------------

### Installation

Since this a WordPress plugin just search for "ACF: Focal Point" in the plugins section in the admin and install it from there. If you would rather install it manually you can do the following:

1. Download and extract the plugin
2. Copy the `acf-focal_point` folder into your `wp-content/plugins` folder
3. Activate the Focal Point plugin via the plugins admin page
4. Create a new field via ACF and select the Focal Point type
5. Refer to the Frequently Asked Questions for more info regarding the field type settings

-----------------------

### Frequently Asked Questions


#### How do I use it?

Pretty much just like an [ACF Image Field](http://www.advancedcustomfields.com/resources/image/). There's a few differences, like not returning just an ID (there's no point without positions), but the basics are the same. 

Make sure the Preview Size and Return Size are of a similar ratio. And make sure the image parent has a height.

Image tag returns some additional markup on images:

`<img class="js-focal-point-image" src="img.png" alt="image alt" data-focus-left="0.12" data-focus-top="0.11" data-focus-right="0.40" data-focus-bottom="0.98">`


The Image object now has a few more values too:

```
'focal_point' 	=> array(
	'class'		=> 'js-focal-point-image',
	'top'  		=> $value['top'],
	'left' 		=> $value['left'],
	'right'		=> $value['right'],
	'bottom' 	=> $value['bottom']
)
```

`'class'` refers to the class that Responsify.js is called on. This needs to be on the image tag to work.

`'top'`, `'left'`, `'right'` and `'bottom'` refer to the `data-focus` attributes used to control focal point. 

Build image as per above tag. Responsify is called on load and resize automatically on `img.js-focal-point-image`.


#### Why are the images returned huge?

Make sure the image is wrapped inside an element that has a height and hasn't had it's `overflow: hidden` overridden (by `!important`). And that the image hasn't had it's `max-width: none` overridden.


#### Why are the images returned small?

Make sure the return image size (set when creating a Focal Point field) isn't set to a small image size. Larger images work better.


#### Why are the images not stretching to fill it's container?

Responsify.js tries it's best to fit as much of the photo in the container as it can, whilst keeping the focal point a priority, and also keeping ratios correct. This means that at some sizes/ratios your image will be displayed too small for it's container, try changing container size or image size. Or use `background-size: cover`.


#### Why are the images not scaling?

Because for this to work, the image needs to move around within a container. Which means we need to hide the containers overflow. Which means we need to set a height. This is not made for wysiwyg style images, it's for banners, sliders, fullpage, fixed height sections, etc.


#### Why is the focus area not lining up correctly when I view the page/post?

Make sure that the preview size and the return size are the same ratio. It's relatively accurate when they're close, but for best results they should be the same. And make sure any image sizes aren't differing their crops.

-----------------------

### Changelog
Please see `readme.txt` for changelog
