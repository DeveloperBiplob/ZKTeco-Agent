<?php

class Queue
{
    private $file = __DIR__ . '/../storage/queue.json';

    public function add($data)
    {
        $queue = $this->getAll();
        $queue[] = $data;

        file_put_contents($this->file, json_encode($queue));
    }

    public function getAll()
    {
        if (!file_exists($this->file)) return [];

        return json_decode(file_get_contents($this->file), true);
    }

    public function clear()
    {
        file_put_contents($this->file, json_encode([]));
    }
}
