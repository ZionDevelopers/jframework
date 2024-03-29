<?php

/**
 * jFramework
 *
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2023, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

namespace jFramework\Image;

use jFramework\Core\Registry;

/**
 * Image Core
 * 
 * Created: 2010-07-30 08:50 AM
 * Updated: 2014-06-13 11:44 PM 
 * @version 2.0.0 
 * @package jFramework
 * @subpackage Core
 * @copyright Copyright (c) 2010-2018, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class Core
{
    /**
     * Private vars
     * 
     * @access private
     * @var string
     * @var resource
     * @var boolean
     * @var resource
     * @var array
     * @var array
     * @var array
     * @var string
     * @var integer
     * @var integer
     * @var boolean
     */
    private $ext;
    private $fontRcImg;
    private $trans = false;
    private $newImgRc;
    private $originalSize;
    private $finalSize;
    private $wishedSize;
    private $imgName;
    private $photoX;
    private $photoY;
    private $forceScale = false;

    /**
     * Function to define image type
     * 
     * @param string $file
     * @param boolean $trans
     * @throws Exception
     */
    public function __construct($file, $trans = false)
    {
        if (!is_readable($file)) {
            throw new \Exception('jFramework Image Core: Image "'.$file."' does not exists!");
        }    
        
        $this->imgName = $file;        

        $this->trans = $trans;
        $this->setType();
    }

    /**
     * internal function to make a New Image Size
     *
     * @return resource
     * @access private
     */
    protected function resize()
    {
        if ($this->trans && $this->ext == 'png') {
            imagealphablending($this->newImgRc, false);
            imagesavealpha($this->newImgRc, true);
        }

        $result = imagecopyresampled($this->newImgRc, $this->fontRcImg, 0, 0, 0, 0, $this->finalSize ['width'], $this->finalSize ['height'], $this->originalSize ['width'], $this->originalSize ['height']);

        return $result;
    }

    /**
     * Function to reMake image size
     *
     * @param integer $w
     * @param integer $h
     * @param boolean $forceScale
     */
    public function newSize($w, $h, $forceScale = false)
    {
        $this->forceScale = $forceScale;
        $this->wishedSize = array('width' => $w, 'height' => $h);

        $this->size();

        //If will force Scale
        if (!$forceScale) {
            //Check if NEED Scale
            if ($this->needScale()) {
                $this->setRadio();
            } else {
                $this->setNoScale();
            }
        } else {
            $this->forceScale();
        }

        $this->newImage();
        $this->import($this->imgName);
        $this->resize();
    }

    protected function needScale()
    {
        return ($this->originalSize ['width'] > $this->wishedSize ['width'] || $this->originalSize ['height'] > $this->wishedSize ['height']);
    }

    /**
     * internal function to check the Photo Radio
     *
     * @access private
     */
    protected function setRadio()
    {
        if ($this->originalSize ['width'] > $this->originalSize ['height']) { // If a height is major then width (Original)
            $this->finalSize ['width'] = $this->wishedSize ['width'];
            $this->finalSize ['height'] = floor($this->wishedSize ['width'] * $this->originalSize ['height'] / $this->originalSize ['width']);
            $this->photoX = 0;
            $this->photoY = round(($this->wishedSize ['height'] / 2) - ($this->finalSize ['height'] / 2));
        } else {
            $this->finalSize ['width'] = floor($this->wishedSize ['height'] * $this->originalSize ['width'] / $this->originalSize ['height']);
            $this->finalSize ['height'] = $this->wishedSize ['height'];
            $this->photoX = round(($this->wishedSize ['width'] / 2) - ($this->finalSize ['width'] / 2));
            $this->photoY = 0;
        }
    }

    /**
     * internal function to set the OriginalSize the Photo
     *
     * @access private
     */
    protected function setNoScale()
    {
        $this->finalSize = $this->originalSize;
        $this->photoX = 0;
        $this->photoY = 0;
    }

    /**
     * internal function to set the OriginalSize the Photo
     *
     * @access private
     */
    protected function forceScale()
    {
        $this->finalSize = $this->wishedSize;
        $this->photoX = 0;
        $this->photoY = 0;
    }

    /**
     * Function to generate Image Water Mark
     *
     * @param string $file
     * @throws Exception
     */
    public function waterMark($file)
    {
        if (!is_readable($file)) {
            throw new \Exception('jFramework Image Core: Watermark Image "'.$file."' does not exists!");
        }    
        
        $ext = pathinfo($file);
        $ext = strtolower($ext ['extension']);

        if ($this->isJPG($ext)) {
            $ext = 'jpeg';
        }

        $waterMark = getimagesize($file);
        $width = $waterMark [0];
        $height = $waterMark [1];

        if ($ext == 'png') {
            $waterMark = imagecreatefrompng($file);
        } elseif ($ext == 'jpg') {
            $waterMark = imagecreatefromjpeg($file);
        }

        $x = ($this->finalSize ['width'] - $width);
        $y = ($this->finalSize ['height'] - $height);
        $result = imagecopyresampled($this->newImgRc, $waterMark, $x, $y, 0, 0, $width, $height, $width, $height);

        return $result;
    }

    /**
     * Function to add a image to this Image
     *
     * @param string $image
     */
    public function addImage($image, $x, $y, $type = 'png')
    {
        $waterMark = getimagesize($image);
        $width = $waterMark [0];
        $height = $waterMark [1];

        if ($type == 'png') {
            $waterMark = imagecreatefrompng($image);
        } elseif ($type == 'jpg') {
            $waterMark = imagecreatefromjpeg($image);
        }

        $result = imagecopyresampled($this->newImgRc, $waterMark, $x, $y, 0, 0, $width, $height, $width, $height);

        return $result;
    }

    /**
     * Internal function to import image
     *
     * @access private
     * @param string $imgName
     * @return resource
     */
    protected function import($imgName)
    {
        $func = 'imagecreatefrom' . $this->ext;
        $this->fontRcImg = $func($imgName);
        $result = $this->fontRcImg;

        return $result;
    }

    /**
     * internal function to create a new Image Resource
     *
     * @access private
     * @return resource
     */
    protected function newImage()
    {
        $this->newImgRc = imagecreatetruecolor($this->finalSize ['width'], $this->finalSize ['height']);

        return $this->newImgRc;
    }

    /**
     * internal function to set image type
     *
     * @access private
     * @return string
     */
    protected function setType()
    {
        $ext = pathinfo($this->imgName);

        $ext = strtolower($ext ['extension']);
        if ($this->isJPG($ext)) {
            $this->ext = 'jpeg';
        } else {
            $this->ext = $ext;
        }

        return $ext;
    }

    /**
     * internal function to get the image size
     *
     * @access private
     * @return array
     */
    protected function size()
    {
        list ( $width, $height, $type, $attr ) = getimagesize($this->imgName);
        $this->originalSize = array('width' => $width, 'height' => $height, 'type' => $type, 'attr' => $attr);
        $result = $this->originalSize;

        return $result;
    }

    /**
     * Internal function to set the page Header
     * @access private
     *
     */
    protected function setHeader()
    {
        if (isset($this->ext)) {
            header('Content-Type: image/' . $this->ext, true);
        }
    }

    /**
     * Function to save image into File
     *
     * @param string $imgName
     * @param integer $quality
     * @return resource
     */
    public function save($imgName, $quality = 80)
    {

        if (isset($this->ext)) {
            if ($this->isJPG()) {
                $result = imagejpeg($this->newImgRc, $imgName, $quality);
            } else {
                $func = 'image' . $this->ext;
                $result = $func($this->newImgRc, $imgName);
            }
        }

        return $result;
    }

    /**
     * Function to check if the extension is JPG (With Variants)
     *
     * @param string $ext
     * @return boolean
     */
    public function isJPG($ext = null)
    {
        if (is_null($ext)) {
            $ext = $this->ext;
        }

        return ($ext == 'jpeg' || $ext == 'pjpeg' || $ext == 'jpg' || $ext == 'jif');
    }

    /**
     * Function to show the image on Screen
     *
     * @param integer $quality
     * @return resource
     * */
    public function show($quality = 80)
    {
        if (isset($this->ext)) {
            $this->setHeader();
            if ($this->isJPG()) {
                $result = imagejpeg($this->newImgRc, null, $quality);
            } else {
                $func = 'image' . $this->ext;
                $result = $func($this->newImgRc);
            }
        }
        return $result;
    }

    /**
     * Function to Draw a Text
     *
     * @param string $text
     * @param integer $size
     * @param integer $x
     * @param integer $y
     * @param array $color
     * @param string $font
     */
    public function text($text, $size = 12, $x = 0, $y = 0, array $color = array(0, 0, 0), $font = 'arial')
    {
        if (!is_resource($this->fontRcImg)) {
            $this->import($this->imgName);
            $this->newImgRc = $this->fontRcImg;
        }

        $color = imagecolorallocate($this->newImgRc, $color [0], $color [1], $color [2]);
        imagettftext($this->newImgRc, $size, 0, $x, $y, $color, Registry::get('FOLDER.temp') . '/fonts/' . $font . '.ttf', $text);
    }

    /**
     * Function to destroy the image resources to get free Memory
     *
     */
    public function destroy()
    {
        if (is_resource($this->fontRcImg) && is_resource($this->newImgRc)) {
            //Destroing image Memory Resources
            imagedestroy($this->fontRcImg);
            imagedestroy($this->newImgRc);

            $this->ext = null;
            $this->fontRcImg = null;
            $this->trans = false;
            $this->newImgRc = null;
            $this->originalSize = null;
            $this->finalSize = null;
            $this->wishedSize = null;
            $this->imgName = null;
            $this->photoX = 0;
            $this->photoY = 0;
            $this->forceScale = false;
        }
    }

    public function clearBuff()
    {
        ob_end_clean();
    }

    /**
     * Function to Auto Destruct image Resources to get Free Memory
     *
     */
    public function __destruct()
    {
        $this->destroy();
    }

}
