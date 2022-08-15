<?php

namespace App\View;

class View
{
    private string $pathToTemplate;
    private array $data;

    public function __construct(string $pathToTemplate, array $data = [])
    {
        $this->pathToTemplate = $pathToTemplate;
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getPathToTemplate(): string
    {
        return $this->pathToTemplate;
    }
}