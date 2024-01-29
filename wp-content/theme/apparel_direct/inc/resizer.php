<?php 
class Resizer {

    public $image_to_resize;
    public $new_width;
    public $new_height;
    public $ratio;

    public function resize() {

        if(!file_exists($this->image_to_resize)) {
            exit("File ".$this->image_to_resize." does not exist.");
        }

        $info = getimagesize($this->image_to_resize);

        if(empty($info)) {
            exit("The file ".$this->image_to_resize." doesn't seem to be an image.");
        }

        $width = $info[0];
        $height = $info[1];
        $mime = $info['mime'];

        if($this->ratio) {
            if (isset($this->new_width)) {
                $factor = (float)$this->new_width / (float)$width;
                $this->new_height = $factor * $height;
            }
            else if (isset($this->new_height)) {
                $factor = (float)$this->new_height / (float)$height;
                $this->new_width = $factor * $width;
            }
            else    exit("neither new height or new width has been set");
        }

        $type = substr(strrchr($mime, '/'), 1);

        switch (strtolower($type)) {
            case 'jpeg':
                $image_create_func = 'imagecreatefromjpeg';
                $image_save_func = 'imagejpeg';
                $new_image_ext = 'jpg';
                break;

            case 'png':
                $image_create_func = 'imagecreatefrompng';
                $image_save_func = 'imagepng';
                $new_image_ext = 'png';
                break;

            case 'bmp':
                $image_create_func = 'imagecreatefrombmp';
                $image_save_func = 'imagebmp';
                $new_image_ext = 'bmp';
                break;

            case 'gif':
                $image_create_func = 'imagecreatefromgif';
                $image_save_func = 'imagegif';
                $new_image_ext = 'gif';
                break;

            case 'vnd.wap.wbmp':
                $image_create_func = 'imagecreatefromwbmp';
                $image_save_func = 'imagewbmp';
                $new_image_ext = 'bmp';
                break;

            case 'xbm':
                $image_create_func = 'imagecreatefromxbm';
                $image_save_func = 'imagexbm';
                $new_image_ext = 'xbm';
                break;

            default:
                $image_create_func = 'imagecreatefromjpeg';
                $image_save_func = 'imagejpeg';
                $new_image_ext = 'jpg';
        }

        // New Image
        $image_c = imagecreatetruecolor($this->new_width, $this->new_height);

        $new_image = $image_create_func($this->image_to_resize);

        imagecopyresampled($image_c, $new_image, 0, 0, 0, 0, $this->new_width, $this->new_height, $width, $height);

    header("Content-Type: ".$mime);
    $image_save_func($image_c);
        $process = $image_save_func($image_c);

    }
}

// Default Width and Height
$def_width = 100;
$def_height = 100;

// Get Variables from Get
$image_file_path = $_GET['image_file'];
$width = ( (isset($_GET['width']) && intval($_GET['width'])!=0) ? $_GET['width'] : $def_width );
$height = ( (isset($_GET['height']) && intval($_GET['height'])!=0) ? $_GET['height'] : $def_height );

// Use Resizer Class
$image = new Resizer();

// Set New Cropped Dimensions
$image->new_width = $width;
$image->new_height = $height;

// Path of Image to Resize
$image->image_to_resize = $image_file_path;

// Maintains Image Ratio
$image->ratio = true;

$image->resize();

?>