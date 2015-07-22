<?php

/*
 * This file is part of ImageCompare.
 *
 * (c) 2015 Philipp Steingrebe <philipp@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 */


/**
 * ValueObject as representation of an Image.
 *
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * 
 */

Class Image {

    /**
     * Compare methods
     */
    const COMP_COLOR    = 0b00001;
    const COMP_FRAGMENT = 0b00010;
    const COMP_SHAPE    = 0b00100; 
    const COMP_HASH     = 0b01000;
    

    /**
     * The size of the square that is used to estimate the average color of this image
     */
    
    const AVG_SIZE = 10;

    /**
     * The underlying image-resource
     * @var resource
     */
    
    private $img = null;

    /**
     * The average color of this image
     * @var Color
     */
    
    private $avg = null;

    /**
     * Constructor
     *
     * @param resource the image-resource this object builds ib
     * 
     */

    public function __construct($resource)
    {
        $this->img = $resource;
    }

    /**
     * Destructor
     * Frees the memory used by the image-resource
     * 
     */

    public function __destruct()
    {
        imagedestroy($this->img);
    }

    public function difference(Image $comp, $options = self::COMP_COLOR | self::COMP_FRAGMENT | self::COMP_HASH)
    {
        // Hash compare
        if ($options & self::COMP_HASH && $this->hash() == $comp->hash()) {
            return 1;
        }

        // Equalize image size
        if ($comp->size() != $this->size()) {
            $comp = $comp->resize($this->size());
        }

        list($icComp, $ocComp) = ImagePixelMatrix::fromImage($comp)->getHotSpots();
        list($icOrg, $ocOrg) = ImagePixelMatrix::fromImage($this)->getHotSpots();

        // Compare color average
        $deviation = 1;
        if ($options & self::COMP_COLOR) {

            $avgOrg = $avgComp = array();

            foreach($icComp as $image) {
                $avgComp[] = $image->avg();
            }

            foreach($icOrg as $image) {
                $avgOrg[] = $image->avg();
            }

            $deviation = 1 - Color::avg($avgComp)->deviation(Color::avg($avgOrg));
        }

        // Compare fragment count
        $factor = 1;
        if ($options & self::COMP_FRAGMENT) {
            $factor = min($icComp->size(), $icOrg->size()) / max($icComp->size(), $icOrg->size());
        }

        return $deviation * $factor;
    }

    public function compare(Image $comp, $tolerance = 20, $options = self::COMP_COLOR | self::COMP_FRAGMENT | self::COMP_HASH) 
    {
        $tolerance /= 100;
        return $this->difference($comp, $options) > (1 - $tolerance);
    }

    /**
     * Substracts a given mask from this image.
     * The mask is resized to the curent image size and the color
     * of each pixel is compared within a given tolerance. 
     * Similar colored pixels are turned into white. 
     *
     * @param  Image   $mask      the image mask to substract
     * @param  integer $tolerance the color tolerance in percent
     *
     * @return Image              the substracted image
     * 
     */
    
    public function subtract(Image $mask, $tolerance = 0)
    {
        $size = $this->size();

        if ($mask->size() != $size) {
            $mask = $mask->resize($size);
        }

        $target = imagecreatetruecolor($size[0], $size[1]);

        for ($x = 0; $x < $size[0]; $x++) {
            for ($y = 0; $y < $size[1]; $y++) {
                if ($this->colorat($x, $y)->compare($mask->colorat($x, $y), $tolerance)) {
                    imagesetpixel($target, $x, $y, 0xFFFFFF);
                }
                else {
                    imagesetpixel($target, $x, $y, $this->colorat($x, $y)->toInt());
                }
            }
        }

        return self::fromResource($target);
    }

    public function avg()
    {
        if ($this->avg === null) {
            $image = $this->resize([self::AVG_SIZE, self::AVG_SIZE]);
            $colors = array();

            for ($x = 0; $x < self::AVG_SIZE; $x++) {
                for ($y = 0; $y < self::AVG_SIZE; $y++) {
                    $color = $image->colorat($x, $y);

                    if (!$color->compare(Color::white())) {
                        $colors[] = $color;
                    }
                }
            }

            $this->avg = Color::avg($colors);
        }

        return $this->avg;
    }

    public function colorat($x, $y)
    {
        return Color::fromInt(imagecolorat($this->img, $x, $y));
    }


    public function slice(array $rect)
    {
        // This does not work in all PHP versions due to a bug
        //return Image::fromResource(imagecrop($this->img, $rect));
        
        $target = imagecreatetruecolor($rect['width'], $rect['height']);
        imagecopy($target, $this->img, 0, 0, $rect['x'], $rect['y'], $rect['width'], $rect['height']);

        return self::fromResource($target);
    }

    public function sliceByOutline(CrawlerOutline $outline)
    {
        $target = imagecreatetruecolor($outline->boundary->width(), $outline->boundary->height());
        imagefill($target, 0, 0, 0xFFFFFF);

        $topLeft = $outline->boundary->topLeft();
        $bottomRight = $outline->boundary->bottomRight();

        for ($x = $topLeft->x(); $x <= $bottomRight->x(); $x++) {
            for ($y = $topLeft->y(); $y <= $bottomRight->y(); $y++) {
                if ($outline->contains(new Point($x, $y))) {
                    imagesetpixel($target, $x - $topLeft->x(), $y - $topLeft->y(), imagecolorat($this->img, $x, $y));
                }
            }
        }

        return self::fromResource($target);
    }

    public function size()
    {
        return [imagesx($this->img), imagesy($this->img)];
    }

    public function resize(array $size = array(100, 100))
    {
        $target = imagecreatetruecolor($size[0], $size[1]);
        imagecopyresized($target, $this->img, 0, 0, 0, 0, $size[0], $size[1], imagesx($this->img), imagesy($this->img));

        return self::fromResource($target);
    }

    public function show()
    {
        header("Content-type: image/png");
        imagepng($this->img);
        die();
    }


    /**
     * Saves this image to png file.
     * If no name is provied the hash value of this image will be used.
     *
     * @param  string $name (optional) the name of the image
     * @param  string $path (optional) the path where to save the image
     *
     * @return self         this object
     */
    
    public function save($name = null, $path = '') {
        if ($name === null) {
            $name = $this->hash();
        }

        imagepng($this->img, $path . $name . '.png');

        return $this;
    }


    /**
     * Generates the MD5 hash of this image.
     * Optionaly a salt can be provided to generate more enthropy.
     *
     * @param String $salt  the salt
     *
     * @return String       the MD5 hash
     * 
     */
    
    public function hash($salt = '')
    {
        $hash = null;

        ob_start(function($buffer) use (&$hash, $salt){
            $hash = md5($buffer . $salt);
        });

        imagepng($this->img);
        ob_end_clean();

        return $hash;
    }


    /**
     * Factory method: From Resource
     *
     * @param resource $resource the image-resource
     *
     * @return Image             the new instance of Image
     * 
     */
    
    public static function fromResource($resource)
    {
        return new self($resource);
    }


    /**
     * Factory method: From binary
     *
     * @param resource $resource the image-binary content
     *
     * @return Image             the new instance of Image
     * 
     */
    
    public static function fromBin($binf)
    {
        return new self(imagecreatefromstring($bin));
    }


    /**
     * Factory method: From Resource
     *
     * @param resource $resource the path or url to an image
     *
     * @return Image             the new instance of Image
     * 
     */

    public static function fromFile($path)
    {
        return new self(imagecreatefromstring(file_get_contents($path)));
    }
}
