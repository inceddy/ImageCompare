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
 * Represents the square boundary spaned arroud a polygon.
 *
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * 
 */

class Boundary {

	/**
	 * The boundary values
	 * @var array
	 */
	
	private $boundary = array();


	public function construct(Point $pointTopLeft = null, Point $pointBottomRight = null)
	{
		if ($pointTopLeft !== null) {
			$boundary[0] = $pointTopLeft->x();
			$boundary[1] = $pointTopLeft->y();
		}

		if ($pointBottomRight !== null) {
			$boundary[2] = $pointBottomRight->x();
			$boundary[3] = $pointBottomRight->y();
		}
	}

	public function contains(Point $point)
	{
		return ($this->boundary[0] <= $point->x() && $this->boundary[2] >= $point->x()) &&
			   ($this->boundary[1] <= $point->y() && $this->boundary[3] >= $point->y());
	}

	public function push(Point $point)
	{
		if (!isset($this->boundary[0]) || $this->boundary[0] > $point->x())
			$this->boundary[0] = $point->x();

		if (!isset($this->boundary[1]) || $this->boundary[1] > $point->y())
			$this->boundary[1] = $point->y();

		if (!isset($this->boundary[2]) || $this->boundary[2] < $point->x())
			$this->boundary[2] = $point->x();

		if (!isset($this->boundary[3]) || $this->boundary[3] < $point->y())
			$this->boundary[3] = $point->y();

		return $this;
	}

	public function width()
	{
		return $this->boundary[2] - $this->boundary[0] + 1;
	}

	public function height()
	{
		return $this->boundary[3] - $this->boundary[1] + 1;
	}

	public function topLeft()
	{
		return new Point($this->boundary[0], $this->boundary[1]);
	}

	public function bottomRight()
	{
		return new Point($this->boundary[2], $this->boundary[3]);
	}

	public function toArray() 
	{
		return array(
			'x'      => $this->boundary[0], 
			'y'      => $this->boundary[1], 
			'width'  => $this->width(), 
			'height' => $this->height()
		);
	}
}