<?php
class Application_Model_Image
{
    var $prefix = ''; // prefix for file name
    var $ext = ''; // convert into this type of image at resize
    var $new_width = 100; // max width
    var $new_height = 100; // max height
    var $scale = 1; // scale proportionally
    var $cutoff = 1; // cut off parts to match thumbnail size
    var $dir = './'; // images dir
    function __construct($dir = '')
    {
        if ($dir != '')  $this->dir = $dir;
    }

    function resizeByWidth($fname, $ext, $width = 0,  $overlay = '')
    {
        $new_width  = $width  ? $width  : $this->new_width;
        $new_height = $height ? $height : $this->new_height;
        if (@is_file($this->dir . $fname . '.' . $ext)) {
            if (@imagetypes() & !IMG_GIF && $ext == 'gif')  return 0;
            elseif (@imagetypes() & !IMG_PNG && $ext == 'png') return 0;
            elseif (@imagetypes() & !IMG_JPEG && ($ext == 'jpg'  ||  $ext == 'jpeg'))  return 0;
            list($width_orig, $height_orig) = @getimagesize($this->dir . $fname . '.' . $ext);
            $height=$height_orig;
            if ($new_width < $width_orig){
                if ($this->cutoff) {
                    if ($this->scale){
                        $new_height = ($new_width / $width_orig) * $height_orig;
                        $new_x = 0;
                        $new_y = ($new_height - $height) / 2;
                    }
                } else {
		    $new_x = 0;
		    $new_y = 0;
                    if ($this->scale  &&  ($width_orig > ($height_orig * (2 - ($new_height / $new_width))))) {
                        $new_height = ($new_width / $width_orig) * $height_orig;
                        $height = $new_height;
                    } 
                }

                $resize = 1;
                if (!$image_p = @imagecreatetruecolor($width, $height))
                    return 0;
				
				$white = @imagecolorallocate($image_p, 255, 255, 255);
				@imagefill ( $image_p , 0 , 0 , $white );

            } else  {
                $resize = 0;
                if (!$image_p = @imagecreatetruecolor($width_orig, $height_orig))
                    return 0;
		    $white = @imagecolorallocate($image_p, 255, 255, 255);
		    @imagefill ( $image_p , 0 , 0 , $white );
            }

            switch ($ext)
            {
                case 'gif': if (!$image = @imagecreatefromgif($this->dir . $fname . '.' . $ext))
                                return 0;
                            break;

                case 'png': if (!$image = @imagecreatefrompng($this->dir . $fname . '.' . $ext))
                                return 0;
                            break;

                case 'jpg':
                case 'jpeg': if (!$image = @imagecreatefromjpeg($this->dir . $fname . '.' . $ext))
                                return 0;
            }

            if ($resize) {
                if (!@imagecopyresampled($image_p, $image, -$new_x, -$new_y, 0, 0, $new_width, $new_height, $width_orig, $height_orig))
                    return 0;
            }
            else {
                $width = $width_orig;
                $height = $height_orig;
                if (!@imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width_orig, $height_orig, $width_orig, $height_orig))
                    return 0;
            }

            if ($overlay && @is_file(SYS_PATH . 'media/' . $overlay))
            {
                $insert = @imagecreatefrompng(SYS_PATH . 'media/' . $overlay);

                $insert_x = @imagesx($insert);
                $insert_y = @imagesy($insert);

                @imagealphablending($image_p, true);
                @imagecopy($image_p, $insert, $width-$insert_x, $height-$insert_y, 0, 0, $insert_x, $insert_y);
            }

            $ext = $this->ext != '' ? $this->ext : $ext;

            switch ($ext)
            {
                case 'gif': if (!@imagegif($image_p, $this->dir . $this->prefix . $fname . '.' . $ext)) return 0;
                            break;

                case 'png': if (!@imagepng($image_p, $this->dir . $this->prefix . $fname . '.' . $ext, 90)) return 0;
                            break;

                case 'jpg':
                case 'jpeg': if (!@imagejpeg($image_p, $this->dir . $this->prefix . $fname . '.' . $ext, 90)) return 0;
                            break;
            }

            return 1;
        }
        return 0;
    }
    
    function resizeByHeight($fname, $ext, $height = 0, $overlay = '')
    {
        $new_width  = $width  ? $width  : $this->new_width;
        $new_height = $height ? $height : $this->new_height;
        if (@is_file($this->dir . $fname . '.' . $ext)) {
            if (@imagetypes() & !IMG_GIF && $ext == 'gif')  return 0;
            elseif (@imagetypes() & !IMG_PNG && $ext == 'png') return 0;
            elseif (@imagetypes() & !IMG_JPEG && ($ext == 'jpg'  ||  $ext == 'jpeg'))  return 0;
            list($width_orig, $height_orig) = @getimagesize($this->dir . $fname . '.' . $ext);
            if ( $new_height < $height_orig){
                if ($this->cutoff) {
                    if ($this->scale  &&  ($width_orig > ($height_orig * (2 - ($new_height / $new_width))))) {
                        $new_width = ($new_height / $height_orig) * $width_orig;
                        $new_x = ($new_width - $width) / 2;
                        $new_y = 0;
                    } 
                } else {
		    $new_x = 0;
		    $new_y = 0;
                    if ($this->scale  &&  !($width_orig > ($height_orig * (2 - ($new_height / $new_width))))) {                        
                        $new_width = ($new_height / $height_orig) * $width_orig;
                        $width = $new_width;
                    }
                }
                $resize = 1;
                if (!$image_p = @imagecreatetruecolor($width, $height))
                    return 0;
				
				$white = @imagecolorallocate($image_p, 255, 255, 255);
				@imagefill ( $image_p , 0 , 0 , $white );

                } else  {
                    $resize = 0;
                    if (!$image_p = @imagecreatetruecolor($width_orig, $height_orig))
                        return 0;
                        $white = @imagecolorallocate($image_p, 255, 255, 255);
                        @imagefill ( $image_p , 0 , 0 , $white );
                }

            switch ($ext)
            {
                case 'gif': if (!$image = @imagecreatefromgif($this->dir . $fname . '.' . $ext))
                                return 0;
                            break;

                case 'png': if (!$image = @imagecreatefrompng($this->dir . $fname . '.' . $ext))
                                return 0;
                            break;

                case 'jpg':
                case 'jpeg': if (!$image = @imagecreatefromjpeg($this->dir . $fname . '.' . $ext))
                                return 0;
            }

            if ($resize) {
                if (!@imagecopyresampled($image_p, $image, -$new_x, -$new_y, 0, 0, $new_width, $new_height, $width_orig, $height_orig))
                    return 0;
            }
            else {
                $width = $width_orig;
                $height = $height_orig;
                if (!@imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width_orig, $height_orig, $width_orig, $height_orig))
                    return 0;
            }

            if ($overlay && @is_file(SYS_PATH . 'media/' . $overlay))
            {
                $insert = @imagecreatefrompng(SYS_PATH . 'media/' . $overlay);

                $insert_x = @imagesx($insert);
                $insert_y = @imagesy($insert);

                @imagealphablending($image_p, true);
                @imagecopy($image_p, $insert, $width-$insert_x, $height-$insert_y, 0, 0, $insert_x, $insert_y);
            }

            $ext = $this->ext != '' ? $this->ext : $ext;

            switch ($ext)
            {
                case 'gif': if (!@imagegif($image_p, $this->dir . $this->prefix . $fname . '.' . $ext)) return 0;
                            break;

                case 'png': if (!@imagepng($image_p, $this->dir . $this->prefix . $fname . '.' . $ext, 90)) return 0;
                            break;

                case 'jpg':
                case 'jpeg': if (!@imagejpeg($image_p, $this->dir . $this->prefix . $fname . '.' . $ext, 90)) return 0;
                            break;
            }

            return 1;
        }
        return 0;
    }
    
    function resize($fname, $ext, $width = 0, $height = 0, $overlay = '')
    {
        $new_width  = $width  ? $width  : $this->new_width;
        $new_height = $height ? $height : $this->new_height;
        if (@is_file($this->dir . $fname . '.' . $ext)) {
            
            if (@imagetypes() & !IMG_GIF && $ext == 'gif')  return 0;
            elseif (@imagetypes() & !IMG_PNG && $ext == 'png') return 0;
            elseif (@imagetypes() & !IMG_JPEG && ($ext == 'jpg'  ||  $ext == 'jpeg'))  return 0;
            list($width_orig, $height_orig) = @getimagesize($this->dir . $fname . '.' . $ext);
            if ($new_width < $width_orig  ||  $new_height < $height_orig){
                if ($this->cutoff) {
                    if ($this->scale  &&  ($width_orig > ($height_orig * (2 - ($new_height / $new_width))))) {
                        $new_width = ($new_height / $height_orig) * $width_orig;
                        $new_x = ($new_width - $width) / 2;
                        $new_y = 0;
                    } elseif ($this->scale){
                        $new_height = ($new_width / $width_orig) * $height_orig;
                        $new_x = 0;
                        $new_y = ($new_height - $height) / 2;
                    }
                } else {
		    $new_x = 0;
		    $new_y = 0;
                    if ($this->scale  &&  ($width_orig > ($height_orig * (2 - ($new_height / $new_width))))) {
                        $new_height = ($new_width / $width_orig) * $height_orig;
                        $height = $new_height;
                    } elseif ($this->scale) {
                        $new_width = ($new_height / $height_orig) * $width_orig;
                        $width = $new_width;
                    }
                }

                $resize = 1;
                if (!$image_p = @imagecreatetruecolor($width, $height))
                    return 0;
				
				$white = @imagecolorallocate($image_p, 255, 255, 255);
				@imagefill ( $image_p , 0 , 0 , $white );

            } else  {
                $resize = 0;
                if (!$image_p = @imagecreatetruecolor($width_orig, $height_orig))
                    return 0;
		    $white = @imagecolorallocate($image_p, 255, 255, 255);
		    @imagefill ( $image_p , 0 , 0 , $white );
            }

            switch ($ext)
            {
                case 'gif': if (!$image = @imagecreatefromgif($this->dir . $fname . '.' . $ext))
                                return 0;
                            break;

                case 'png': if (!$image = @imagecreatefrompng($this->dir . $fname . '.' . $ext))
                                return 0;
                            break;

                case 'jpg':
                case 'jpeg': if (!$image = @imagecreatefromjpeg($this->dir . $fname . '.' . $ext))
                                return 0;
            }

            if ($resize) {
                if (!@imagecopyresampled($image_p, $image, -$new_x, -$new_y, 0, 0, $new_width, $new_height, $width_orig, $height_orig))
                    return 0;
            }
            else {
                $width = $width_orig;
                $height = $height_orig;
                if (!@imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width_orig, $height_orig, $width_orig, $height_orig))
                    return 0;
            }

            if ($overlay && @is_file(SYS_PATH . 'media/' . $overlay))
            {
                $insert = @imagecreatefrompng(SYS_PATH . 'media/' . $overlay);

                $insert_x = @imagesx($insert);
                $insert_y = @imagesy($insert);

                @imagealphablending($image_p, true);
                @imagecopy($image_p, $insert, $width-$insert_x, $height-$insert_y, 0, 0, $insert_x, $insert_y);
            }

            $ext = $this->ext != '' ? $this->ext : $ext;

            switch ($ext)
            {
                case 'gif': if (!@imagegif($image_p, $this->dir . $this->prefix . $fname . '.' . $ext)) return 0;
                            break;

                case 'png': if (!@imagepng($image_p, $this->dir . $this->prefix . $fname . '.' . $ext, 90)) return 0;
                            break;

                case 'jpg':
                case 'jpeg': if (!@imagejpeg($image_p, $this->dir . $this->prefix . $fname . '.' . $ext, 90)) return 0;
                            break;
            }

            return 1;
        }
        return 0;
    }

    function thumbnail($fname, $ext, $new_width, $new_height, $x, $y, $w, $h, $overlay = '')
    {
       if (@is_file($this->dir . $fname . '.' . $ext))
        {
            if (@imagetypes() & !IMG_GIF && $ext == 'gif')
                return 0;
            elseif (@imagetypes() & !IMG_PNG && $ext == 'png')
                return 0;
            elseif (@imagetypes() & !IMG_JPEG && ($ext == 'jpg'  ||  $ext == 'jpeg'))
                return 0;


            switch ($ext)
            {
                case 'gif': if (!$image = @imagecreatefromgif($this->dir . $fname . '.' . $ext))
                                return 0;
                            break;

                case 'png': if (!$image = @imagecreatefrompng($this->dir . $fname . '.' . $ext))
                                return 0;
                            break;

                case 'jpg':
                case 'jpeg': if (!$image = @imagecreatefromjpeg($this->dir . $fname . '.' . $ext))
                                return 0;
            }


            if (!$image_p = @imagecreatetruecolor($new_width, $new_height))
                return 0;


            @imagecopyresampled($image_p, $image, 0, 0, $x, $y, $new_width, $new_height, $w, $h);


            if ($overlay && @is_file(SYS_PATH . 'media/' . $overlay))
            {
                $insert = @imagecreatefrompng(SYS_PATH . 'media/' . $overlay);

                $insert_x = @imagesx($insert);
                $insert_y = @imagesy($insert);

                @imagealphablending($image_p, true);
                @imagecopy($image_p, $insert, $new_width-$insert_x, $new_height-$insert_y, 0, 0, $insert_x, $insert_y);
            }


            $ext = $this->ext != '' ? $this->ext : $ext;

            switch ($ext)
            {
                case 'gif': if (!@imagegif($image_p, $this->dir . $this->prefix . $fname . '.' . $ext)) return 0;
                            break;

                case 'png': if (!@imagepng($image_p, $this->dir . $this->prefix . $fname . '.' . $ext, 90)) return 0;
                            break;

                case 'jpg':
                case 'jpeg': if (!@imagejpeg($image_p, $this->dir . $this->prefix . $fname . '.' . $ext, 90)) return 0;
                            break;
            }

            return 1;
        }
        return 0;
    }
};