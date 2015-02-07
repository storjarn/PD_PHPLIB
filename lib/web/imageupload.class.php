<?php
/**
 * Validates and creates uploaded images.
 */
class ImageUpload
{
    private $thumb_x;
    private $thumb_y;
    private $extension; # (to lower)
    private $upload_dir;
    private $upload_name;
    private $original_filename;
    private $temporary_name;
    private $uploadfile;
    private $image_resource;
    private $output_message = "<div id='update_text'>Only .jpg, .jpeg, .gif, and .png files are supported.</div>\n";
    
    /**
     * Initializes our object values.
     * 
     * The fields used for $params are:
     * <br>upload_dir - Upload directory
     * <br>upload_filename - Name you want to save the uploaded file as
     * <br>temp_filename - The temp name of the uploaded file
     * <br>original_filename - The original name of the uploaded file.
     * <p>Set these values in your array and pass it to your NEW call. (See Example)</p>
     * 
     * @example <code>$params_array = array(
     * "upload_dir" => $_SERVER['DOCUMENT_ROOT'] . "/files/logos/",
     * "upload_filename => md5($_FILES['logo']['name']) . ".png",
     * "temp_filename" => $_FILES['logo']['tmp_name'],
     * "original_filename" => $_FILES['logo']['name']
     * );
     * 
     * $obj = new Image_upload($params_array);</code>
     * 
     * @param Array $params Associative array of the parameters we set on object creation.
     */
    function __construct($params)
    {
        $this->upload_dir = $params["upload_dir"]; //$_SERVER['DOCUMENT_ROOT'] . "/files/logos/";
        $this->original_filename = $params["original_filename"];
        #$this->logo = strip_tags($_POST['logo']);
        #$this->revert = strip_tags($_POST['revert']); 
        #$this->previous_logo = strip_tags($_POST['previous_logo']);
        $this->extension = strtolower(end(explode('.', $this->original_filename))); //$_FILES['logo']['name']));
        $this->upload_name = $params["upload_filename"]; //md5($_FILES['logo']['name']) . ".png";
        $this->temporary_name = $params["temp_filename"]; // $_FILES['logo']['tmp_name'];
        $this->uploadfile = $this->upload_dir . $this->upload_name;
    }   // ends __construct()
    
    function __get($name)
    {
        if (property_exists($this, $name))
        {
            return $this->$name;
        }
        
        return NULL;
    }
    
    /**
     * Validates the image that has been uploaded and creates the image resource.
     * 
     * @return int 0 = fail 1 = pass
     */
    function validate_image()
    {
        $result = 1;
        
        if ($this->extension == 'jpg' || $this->extension == 'jpeg')
        {
            $this->image_resource = imagecreatefromjpeg($this->temporary_name);
        }
        elseif ($this->extension == 'gif')
        {
            $this->image_resource = imagecreatefromgif($this->temporary_name);
        }
        elseif ($this->extension == 'png')
        {
            $this->image_resource = imagecreatefrompng($this->temporary_name);
        }
        else
        {
            $result = 0;
        }
        
        return $result;
    }   // ends validate_image()
    
    /**
     * Creates new resized image of the current image resource in the image_resource attribute.
     * 
     * The new image that is created is a PNG.  If we want to make this more flexible later
     * we'll need to do some more work here.
     * 
     * @param int $thumb_x
     * @param int $thumb_y
     * @return int 0 = fail and 1 = pass 
     */
    function resize_image($thumb_x, $thumb_y)
    {
        $thumb = imagecreatetruecolor($thumb_x,$thumb_y);  
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        imagecopyresampled($thumb, $this->image_resource ,0, 0, 0, 0, $thumb_x, $thumb_y, imagesx($this->image_resource), imagesy($this->image_resource));    
        imagepng($thumb, $this->uploadfile, 9);
        
        if (is_file($this->uploadfile))
        {
            return 1;
        }
        
        return 0;
    }   // ends resize_image()
    
    /**
     * Creates new resized image of the current image resource in the image_resource attribute using the max X and Y you pass in.
     * 
     * This method calls the set_max_dimensions() method with the max X and Y
     * values that you pass in.  Then this method calls the resize_image() method
     * with the thumb_x and thumb_y attributes that were set by set_max_dimensions().
     * 
     * So basically, this is a helper to reduce your two calls into just one!
     * 
     * 
     * @see set_max_dimensions
     * @see resize_image
     * 
     * @param int $max_x
     * @param int $max_y 
     * @return int This is the result from resize_image method.
     */
    function resize_image_with_max($max_x, $max_y)
    {
        $this->set_max_dimensions($max_x, $max_y);
        return($this->resize_image($this->thumb_x, $this->thumb_y));
    }   // ends resize_image_with_max()
    
    /**
     * Sets the thumb_x and thumb_y attributes to be no larger than the requested value or the current image dimensions if requested max values are lareger than the image.
     * 
     * @param int $max_x Max X (width)
     * @param int $max_y Max Y (height)
     */
    function set_max_dimensions($max_x, $max_y)
    {
        $width = imagesx($this->image_resource);
        $height = imagesy($this->image_resource);
        
        if ($width > $max_x or $height > $max_y)
        {
            if ($width >= $height)
            {
                $this->thumb_x = $max_x; 
                $this->thumb_y = $height*($max_x/$width); 
            }
            else
            {
                $this->thumb_x = $width*($max_y/$height); 
                $this->thumb_y = $max_y; 
            } 
        }
        else
        {
            $this->thumb_x = $width; 
            $this->thumb_y = $height; 
        }
    }   // ends set_max_dimensions()
}   // ends class Image_upload
?>