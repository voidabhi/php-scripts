class CircularBuffer extends Buffer
{
    public function add($element)
    {
        $this->buffer[$this->position] = $element;
        $this->position = ($this->position + 1) % $this->maxSize;
        $this->size = min($this->size + 1, $this->maxSize);
        return true;
    }
}
