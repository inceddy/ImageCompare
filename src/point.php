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
 * Represents the point on an image.
 *
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * 
 */

class Point {

	/**
	 * The x-coordinate
	 * @var integer
	 */
	
	private $x = 0;

	/**
	 * The y-coordinate
	 * @var integer
	 */

	private $y = 0;

	
	/**
	 * Constructor
	 *
	 * @param integer $x  the x-coordinate.
	 * @param integer $y  the y-coordinate.
	 *
	 */

	public function __construct($x, $y)
	{
		$this->x = $x;
		$this->y = $y;
	}


	/**
	 * Getter for x-coordinate.
	 *
	 * @return integer  the x-coordinate.
	 * 
	 */
	
	public function x()
	{
		return $this->x;
	}


	/**
	 * Getter for y-coordinate.
	 *
	 * @return integer  the y-coordinate.
	 * 
	 */

	public function y()
	{
		return $this->y;
	}


	/**
	 * Getter for a position array of both coordinates.
	 *
	 * @return array  the position array.
	 * 
	 */
	
	public function position()
	{
		return array('x' => $this->x, 'y' => $this->y);
	}
}