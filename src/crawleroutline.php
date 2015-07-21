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
 * Represents the outline polygon found by the crawler.
 *
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * 
 */

class CrawlerOutline implements IteratorAggregate {

	/**
	 * The number of vertices in this polygon
	 * @var integer
	 */
	
	private $size = 0;

	/**
	 * The vertices in this polygon 
	 * @var array
	 */
	
	private $outline = array();

	/**
	 * Whether this outline is closed or not
	 * @var boolean
	 */
	
	private $closed = false;

	
	/**
	 * The boundary-object of this polygon.
	 * @var Boundary
	 */
	
	public $boundary = null;

	
	/**
	 * Constructor
	 *
	 * @param  Pixel $pixel  the starting pixel of this outline.
	 *
	 */

	public function __construct(Pixel $pixel)
	{
		$this->boundary = new Boundary();
		$this->push($pixel);
	}


	/**
	 * Getter for the polygon size.
	 *
	 * @return  integer  the polygon size.
	 * 
	 */

	public function size()
	{
		return $this->size;
	}


	/**
	 * Extend the outline by one pixel.
	 *
	 * @param  Pixel  $pixel  the next outline pixel
	 *
	 * @return self
	 * 
	 */
	
	public function push(Pixel $pixel)
	{
		$this->size++;
		$this->boundary->push($pixel);

		if (isset($this->outline[0]) && $this->outline[0]->position() == $pixel->position()) {
			$this->closed = true;
		}

		$this->outline[] = $pixel;

		return $this;
	}


	/**
	 * Getter for the closed state
	 *
	 * @return  boolean  whether this outline is closed or not.
	 * 
	 */
	
	public function closed()
	{
		return $this->closed;
	}


	/**
	 * Check whether the given point/pixel is in- or outside 
	 * of this outline.
	 *
	 * @param Point   $point  the point/pixel to check.
	 *
	 * @return boolean        whether the point/pixel is in- or oudsite.
	 * 
	 */

	public function contains(Point $point)
	{
		// The point is outsize the square-boundary
		if (!$this->boundary->contains($point)) {
			return false;
		}

        // The point lies on polygon vertex
        foreach ($this->outline as $pixel) {
        	if ($point->position() == $pixel->position())
        		return true;
        }

 
        // Check if the point is inside the polygon or on the boundary
        $intersections = 0; 
 
        for ($i = 1; $i < $this->size; $i++) {

            $v1 = $this->outline[$i-1]; 
            $v2 = $this->outline[$i];

            // Check if point is on an horizontal polygon boundary
            if ($v1->y() == $v2->y() && $v1->y() == $point->y() && $point->x() > min($v1->x(), $v2->x()) and $point->x() < max($v1->x(), $v2->x())) {
                return true;
            }


            if ($point->y() > min($v1->y(), $v2->y()) and $point->y() <= max($v1->y(), $v2->y()) and $point->x() <= max($v1->x(), $v2->x()) and $v1->y() != $v2->y()) { 
                $xinters = ($point->y() - $v1->y()) * ($v2->x() - $v1->x()) / ($v2->y() - $v1->y()) + $v1->x(); 
                // Check if point is on the polygon boundary (other than horizontal)
                if ($xinters == $point->x()) {
                    return true;
                }

                if ($v1->x() == $v2->x() || $point->x() <= $xinters) {
                    $intersections++; 
                }
            } 
        } 

        // If the number of edges we passed through is odd, then it's in the polygon. 
        return ($intersections % 2 != 0) ? true : false;
	}

	public function getIterator() {
        return new ArrayIterator($this->outline);
    }
}