<?php

class ImageCollection implements IteratorAggregate
{
	private $images = array();

	public function __construct(array $images = array())
	{
		$this->images = $images;
	}

	public function push(Image $image) {
		$this->images[] = $image;
		return $this;
	}

	public function pop()
	{
		return array_pop($this->images);
	}

	public function save()
	{
		foreach($this->images as $index => $image) {
			$image->save('Chunk_' . $index);
		}

		return $this;
	}

    public function getIterator() {
        return new ArrayIterator($this->images);
    }
}