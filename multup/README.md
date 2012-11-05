# Multup

Multi image uploading and validation for Laravel using the Laravel validation class.

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

    <?php Input::file('picture') ?>

In your routes.php file or in any of your controller files, you can start the Multup bundle if you haven't set it to auto-load by calling:

    Bundle::start('multup');

Then to upload multiple images, in your controller function do something like...

	
    $upload = Multup::open('file', 'image|max:3000|mimes:jpg,gif,png', 'images/originals/', true)
				->upload();
	
	Params for ::open($input, $rules, $path, $random = true)
	$input string : is the name of the file upload element in your view (ex: <input id="fileupload" type="file" name="file" multiple> )
	$rules string: laravel style validation rules string
	$path string: relative to /public/ to move the images if valid
	$random bool: Whether or not to randomize the filename, the filename will be set to a 32 character string if true

	Multup->upload
		@return array of results
			Each result will be an array() with keys:
					errors array -> empty if saved properly, otherwise $validation->errors object
					path string -> full URL to the file if saved, empty if not saved
					filename string -> name of the saved file or file that could not be uploaded
	
## Example

    Coming soon :)