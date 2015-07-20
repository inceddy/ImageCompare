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
 * ValueObject as representation of an RGB-Color.
 *
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * 
 */

class Color {
	/**
	 * Maximum difference between two colors (3*255^2)^1/2.
	 */
	
	const MAX_DEVIATION = 441.67295593006;

	/**
	 * Maximum color value as integer
	 */
	
	const MAX_INT  = 0xFFFFFF;

	/**
	 * Red
	 * @var integer
	 */
	
	private $r = 0;

	/**
	 * Green
	 * @var integer
	 */
	
	private $g = 0;

	/**
	 * Blue
	 * @var integer
	 */
	
	private $b = 0;

	/**
	 * Default white color
	 * @var Color
	 */
	
	private static $white = null;

	/**
	 * Default black color
	 * @var Color
	 */
	
	private static $black = null;

	/**
	 * Constructor
	 *
	 * @param integer $r the red value
	 * @param integer $g the green value
	 * @param integer $b the blue value
	 * 
	 */
	
	public function __construct($r = 0, $g = 0, $b = 0)
	{
		$this->r = $r;
		$this->g = $g;
		$this->b = $b;
	}


	/**
	 * Getter red value
	 *
	 * @return integer the red value
	 * 
	 */
	
	public function r()
	{
		return $this->r;
	}


	/**
	 * Getter green value
	 *
	 * @return integer the green value
	 * 
	 */

	public function g()
	{
		return $this->g;
	}


	/**
	 * Getter blue value
	 *
	 * @return integer the blue value
	 * 
	 */

	public function b()
	{
		return $this->b;
	}

	public function get($key)
	{
		return isset($this->$key) ? $this->$key : null;
	}


	/**
	 * Transforms the rgb-values to an integer
	 * 
	 * @return  integer the color value
	 * 
	 */

	public function toInt()
	{
		return ($this->r << 16) + ($this->g << 8) + ($this->b);
	}



	/**
	 * Retuns an array with the rgb values
	 *
	 * @return array the color value array
	 * 
	 */

	public function toRgb()
	{
		return [$this->r, $this->g, $this->b];	
	}


	/**
	 * Mixes this color with a given one and returns the new mixed color
	 *
	 * @param  Color  $color the color to mix with
	 *
	 * @return Color         the new mixed color
	 * 
	 */
	
	public function mix(Color $color)
	{
		return new self(
			round(($this->r + $color->r()) / 2),
			round(($this->g + $color->g()) / 2),
			round(($this->b + $color->b()) / 2)
		);
	}


	/**
	 * Calculates the difference between this and a given color
	 *
	 * @param  Color  $color    the color to compare with
	 * @param  string $channel  the channel which will be compared (default null -> compare all 3 channels)
	 *
	 * @return float            the difference between this and the given color as float between 0 and 1
	 * 
	 */
	
	public function difference(Color $color, $channel = null)
	{
		if ($channel === null) {
			list($r1, $g1, $b1) = $this->toRgb();
			list($r2, $g2, $b2) = $color->toRgb();
			return (($r1 - $r2) / 255 + ($g1 - $g2) / 255 + ($b1 - $b2) / 255) / 3;
		}

		return ($this->get($channel) - $color->get($channel)) / 255; 

		
	}

	public function deviation(Color $color, $channel = null)
	{
		if ($channel === null) {
			list($r1, $g1, $b1) = $this->toRgb();
			list($r2, $g2, $b2) = $color->toRgb();
			return sqrt(pow($r1 - $r2, 2) + pow($g1 - $g2, 2) + pow($b1 - $b2, 2)) / self::MAX_DEVIATION;
		}

		return abs($this->get($channel) - $color->get($channel)) / 255;
	}


	/**
	 * Compares this color to a given one within a tolerance range
	 *
	 * @param  Color   $color     the color to compare with
	 * @param  integer $tolerance the tolerance in percent in which the colors are assumed equal
	 *
	 * @return bool               the eqaulity as boolean
	 * 
	 */
	
	public function compare(Color $color, $tolerance = 0)
	{
		$tolerance /= 100;
		return  $this->deviation($color) <= $tolerance;
	}


	/**
	 * Factory method
	 *
	 * @param integer $int the integer value of a color
	 *
	 * @return Color       the new instance of Color
	 * 
	 */

	public static function fromInt($int) 
	{
		if (!is_int($int) || $int > self::MAX_INT || $int < 0) {
			throw new Exception("Invalid argument for Color::fromInt. Must be an integer between 0 and 16777215");
		}
			
		return new self($int >> 16, $int >> 8 & 255, $int & 255);
	}

	public static function fromRgb($r = 0, $g = 0, $b = 0)
	{
		return new self($r, $g, $b);
	}

	public static function white()
	{
		if (self::$white === null) {
			self::$white = self::fromInt(0xFFFFFF);
		}

		return self::$white;
	}

	public static function black()
	{
		if (self::$black === null) {
			self::$black = self::fromInt(0x000000);
		}

		return self::$black;
	}

	public static function avg(array $colors)
	{
		if (empty($colors)) {
			return self::white();
		}

		$r = $g = $b = 0;
		$size = sizeof($colors);

		foreach ($colors as $color) {
			$rgb = $color->toRgb();
			$r += $rgb[0];
			$g += $rgb[1];
			$b += $rgb[2];
		}

		return new self(
			round($r / $size),
			round($g / $size),
			round($b / $size)
		);
	}

	public function __toString()
	{
		list($r, $g, $b) = $this->toRgb();
		return sprintf('Color RGB(%s,%s,%s)', $r, $g, $b);
	}
}