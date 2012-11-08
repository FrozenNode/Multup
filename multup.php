<?php

/*
* @package Multup
* @version 0.1.0
* @author Nick Kelly @ Frozen Node
* @link github.com/
* 
* Requires Validator, URL, and Str class from Laravel if used
*
* @example
*	
*
*/
class Multup {

	/* 
		image array 
	*/
	private $image; 
	
	/* 
		string of laravel validation rules 
	*/
	private $rules; 
	
	/* 
		randomize uploaded filename 
	*/
	private $random; 
	
	/* 
		path relative to /public/ that the image should be saved in 
	*/
	private $path;
	
	/* 
		id/name of the file input to find
	*/
	private $input; 
	
	/*	
		How long the random filename should be
	*/
	private $random_length = 32;
	
	/*
	*	Callback function for setting your own random filename
	*/
	private $random_cb;
	
	/**
	 * Instantiates the Multup
	 * @param mixed $file The file array provided by Laravel's Input::file('field_name') or a path to a file
	 */
	public function __construct($input, $rules, $path, $random)
	{
		$this->input  = $input;
		$this->rules  = $rules;
		$this->path = $path;
		$this->random = $random;
	}
	
	/**
	 * Static call, Laravel style.
	 * Returns a new Multup object, allowing for chainable calls
	 * @param  string $input name of the file to upload
	 * @param  string $rules laravel style validation rules string
	 * @param  string $path relative to /public/ to move the images if valid
	 * @param  bool $random Whether or not to randomize the filename, the filename will be set to a 32 character string if true
	 * @return Multup
	 */
	public static function open($input, $rules, $path, $random = true)
	{
		return new Multup( $input, $rules, $path, $random );
	}
	
	/*
	*	Set the length of the randomized filename
	*   @param int $len
	*/
	public function set_length($len){
		if(!is_int($len)){
			return false;
		} else{
			$this->random_length = $len;
		}
		return $this;
	}
	
	/*
	*	Upload the image
	*	@return array of results
	*			each result will be an array() with keys:
				errors array -> empty if saved properly, otherwise $validation->errors object
				path string -> full URL to the file if saved, empty if not saved
				filename string -> name of the saved file or file that could not be uploaded
	*		
	*/
	public function upload(){
		
		$images = Input::file($this->input);
		$result = array();
		
		if(!is_array($images['name'])){ 
		
			$this->image = array($this->input => $images);
			
			$result[] = $this->upload_image();
			
		} else {
			$size = $count($images['name']);
			
			for($i = 0; $i < $size; $i++){
				
				$this->image = array(
					$this->input => array(
						'name'      => $images['name'][$i],
						'type'      => $images['type'][$i],
						'tmp_name'  => $images['tmp_name'][$i],
						'error'     => $images['error'][$i],
						'size'      => $images['size'][$i]
					)
				);
				
				$result[] = $this->upload_image();
			}
		}
		
		return $result;
	
	}

	/*
	*	Upload the image
	*/
	private function upload_image(){
		
		/* validate the image */
		$validation = Validator::make($this->image, array($this->input => $this->rules));
		$errors = array();	
		$original_name = $this->image[$this->input]['name'];
		$path = '';
		$filename = '';
		
		if($validation->fails()){
			/* use the messages object for the erros */
			$errors = $validation->errors;
		} else {

			if($this->random){
				if(is_callable($this->random_cb)){
					$filename =  call_user_func( $this->random_cb, $original_name );
				} else {
					$ext = File::extension($original_name);
					$filename = $this->generate_random_filename().$ext;
				}
			}
			
			/* upload the file */
			$save = Input::upload($this->input, $this->path, $filename);

			if($save){
				$path = $this->path.$filename;
			} else {
				$errors = 'Could not save image';
			}
		}
		
		return compact('errors', 'path', 'filename', 'original_name');
	}
	
	/*
	* Default random filename generation 
	*/
	private function generate_random_filename(){
		 return Str::random($this->random_length);
	}
	
	/*
	* Default random filename generation 
	*/
	public function filename_callback( $func ){
		if(is_callable($func)){
			$this->random_cb = $func;
		}
		
		return $this;
	}
	
}