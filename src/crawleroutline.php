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
		$this->boundary->push($pixel);

		if (isset($this->outline[0]) && $this->outline[0]->position() == $pixel->position()) {
			$this->closed = true;
			return $this->clean();
		}

		$this->size++;
		$this->outline[] = $pixel;

		return $this;
	}

	public function clean()
	{
		$pp = $this->outline[0];

		for ($i = 1; $i < $this->size - 1; $i++) {

			$p0 = $this->outline[$i];
			$pn = $this->outline[$i + 1];

			$s1 = ($pp->x() - $p0->x()) == 0 ? INF : ($pp->y() - $p0->y()) / ($pp->x() - $p0->x());
			$s2 = ($p0->x() - $pn->x()) == 0 ? INF : ($p0->y() - $pn->y()) / ($p0->x() - $pn->x());

			if ($s1 == $s2) {
				unset($this->outline[$i]);
				continue;
			}

			$pp = $p0;
		}

		$this->size = sizeof($this->outline);
		$this->outline = array_values($this->outline);

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
	 * Checks whether the given point/pixel is in- or outside 
	 * of this outline based on the PointPolygonTest of OpenCV.
	 *
	 * @see docs.opencv.org/modules/imgproc/doc/structural_analysis_and_shape_descriptors.html?highlight=pointpolygontest#pointpolygontest
	 *
	 * @param Point   $point  the point/pixel to check.
	 *
	 * @return integer        whether the point/pixel is in- (1) or oudsite (2) or on the outline (0).
	 * 
	 */

	public function contains(Point $point)
	{
        $intersections = 0;
        $v2 = $this->outline[$this->size - 1];

        for ($i = 0; $i < $this->size; $i++) {
            
            $v1 = $v2;
            $v2 = $this->outline[$i];

            if (($v1->y() <= $point->y() && $v2->y() <= $point->y()) ||
                ($v1->y() >  $point->y() && $v2->y() >  $point->y()) ||
                ($v1->x() <  $point->x() && $v2->x() <  $point->x())) 
            {

                if ($point->y() == $v2->y() && 
                	($point->x() == $v2->x() || ($point->y() == $v1->y() &&
                    (($v1->x() <= $point->x() && $point->x() <= $v2->x()) || 
                    ($v2->x() <= $point->x() && $point->x() <= $v1->x()))))) 
                {
                	return 0;
                }
                    

                continue;
            }

            $dist = ($point->y() - $v1->y()) * ($v2->x() - $v1->x()) - ($point->x() - $v1->x()) * ($v2->y() - $v1->y());
            
            if ($dist == 0) {
                return 0;
            }

            if ($v2->y() < $v1->y()) {
                $dist = - $dist;
            }

            $intersections += $dist > 0;
        }

        // If the number of edges we passed through is odd, then it's in the polygon. 
        return $intersections % 2 == 0 ? -1 : 1;
	}

	public function getIterator() {
        return new ArrayIterator($this->outline);
    }
}