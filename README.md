# Multup

Multiple image uploading, validation, and resizing for Laravel using the Laravel validation class and an edited version of the Resizer Bundle.

- **Author:** Nick Kelly w/portions edited from the Resizer bundle by Jarrod Oberto &  Maikel D
- **Website:** [http://frozennode.com](http://frozennode.com)
- **Version:** 1.2.0

## Installation

Install Multup using artisan:

    php artisan bundle:install multup

Then in your *application/bundles.php* file, add the following line to load Multup automatically:

    return array(
        'multup' => array( 'auto' => true )
    );

Or without the `'auto' => true` to load it on demand:

    return array(
        'multup'
    );

## Usage

In your view files, you'd add a file input element.

    <input id="fileupload" type="file" name="file[]" multiple>

In your routes.php file or in any of your controller files, you can start the Multup bundle if you haven't set it to auto-load by calling:

    Bundle::start('multup');

Then to upload multiple images, in your controller function do something like...

	
    $upload = Multup::open('file', 'image|max:3000|mimes:jpg,gif,png', 'public/images/originals/', true)
				->upload();
	
	Params for ::open($input, $rules, $path, $random = true)
	$input string : is the name of the file upload element in your view 
	$rules string: laravel style validation rules string
	$path string: relative to root to move the images if valid
	$random bool: Whether or not to randomize the filename, the filename will be set to a 32 character string if true

	Multup->upload
		@return array of results
			Each result will be an array() with keys:
					errors array -> empty if saved properly, otherwise $validation->errors object
					path string -> full URL to the file if saved, empty if not saved
					filename string -> name of the saved file or file that could not be uploaded
					original_filename -> name of the file uploaded by the user
					
## Methods

### Resizing

To resize the originals after they are uploaded pass in an array with your sizing information to the sizes method.

	$sizes = array( 
		array(200 , 200 , 'crop', 'public/images/thumbs/200/', 90 ), 
		array(300 , 300 , 'crop', 'public/images/thumbs/300/', 90 ), 
	);

	Multup::open('file', 'image|max:3000|mimes:jpg,gif,png', 'public/images/originals/')
		->sizes( $sizes )
		->upload()
	
### After Upload

The after_upload method. This should be called before the upload method. It can be any callable function. 
The function will get passed an array containing the following variables
	$path: string containing path relative to your app root to access the image
	$filename: string containing the full filename (including extension) of the uploaded image
	$original_filename string, the original filename
	$resizes: array containing booleans indicating the success of each resize
The variables will be merged with the array you provide in the second parameter
	
	Multup::open('file', 'image|max:3000|mimes:jpg,gif,png', 'public/images/originals/')
		->after_upload(function($args){ return $args['my_var'].'lolcats'; }, array( 'my_var'=> 'ieat_') )
		->upload();

### Filename Randomization

You set the length of the randomized filename by calling, set_length, after your open Multup.
	
	Mutlup::open( 'file', 'image|max:3000|mimes:jpg,gif,png', 'public/images/originals/' )
	->set_length( 20 )
	->upload();

Or, if that doesn't satisfy you, set your own randomization function. Provide a callback function with a parameter for the original filename

	Multup::open('file', 'image|max:3000|mimes:jpg,gif,png', 'public/images/originals/')
			->set_length( 20 )
			->filename_callback(
				function( $original_name ){ 
					$ext = File::extension($original_name);
					$filename = basename($original_name);
					return 'lolcat_'.$filename; 
				}
			)
			->upload();
			
## Example

This example uses jquery-file-upload. Check the [github example](https://github.com/blueimp/jQuery-File-Upload/wiki/Basic-plugin) for details

Your view file should contain a form like this

	<form action="/home/upload" method="post">
		<input id="fileupload" type="file" name="file" multiple>
	</form>
	/* paths to your jquery upload files */
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<script src="js/vendor/jquery.ui.widget.js"></script>
	<script src="js/jquery.iframe-transport.js"></script>
	<script src="js/jquery.fileupload.js"></script>
	<script src="js/main.js"></script>

Add your main.js file ...
	
	$(function(){
		$('#fileupload').fileupload({
			url: '/home/upload',
			dataType: 'json',
			done: function (e, data) {
				//path, success, errors
				if(data && data.result){
					$.each(data.result, function(indx, val){
						/* this will log the return from the controller in your console zomg */
						console.log( val );
					});
				}
			},
		});
	});

And your controller (named home for this example) function 

	public function action_upload(){
		
		/* this assumes the bundle is auto started */
		$success = Multup::open('file', 'image|max:3000|mimes:jpg,gif,png', 'public/images/originals/')
			->filename_callback(function( $filename ){ 
					/* prepend lolcat to our image */
					return 'lolcat_'.basename($filename); 
				}
			)
			->upload();
		
		
		die(json_encode( $success ));
	}

You should see a return in your console of something like...

	[{"errors":[],"path":"public\/images\/originals\/lolcat_imagename.png","filename":"lolcat_imagename.png","original_name":"imagename.png", "resizes": []}]
	
## Copyright and License
Multup was written by Nick Kelly for the Laravel framework.
Multup is released under the MIT License. See the LICENSE file for details.

## Changelog

### Multup 1.2.0
- Added some resizing stuff release.
