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
 * Represents the pixel of an image.
 *
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * 
 */

class Pixel extends Point {

	/**
	 * Color of this pixel
	 * @var Color
	 */
	
	private $color = null;


	/**
	 * Constructor
	 *
	 * @param  integer $x      the x-coordinate.
	 * @param  integer $y      the y-coordinate.
	 * @param  Color   $color  the color of this pixel.
	 *
	 */

	public function __construct($x, $y, Color $color)
	{
		$this->color = $color;
		parent::__construct($x, $y);
	}


	/**
	 * Getter for the pixel color.
	 *
	 * @return  Color  the color.
	 * 
	 */

	public function color()
	{
		return $this->color;
	}
}