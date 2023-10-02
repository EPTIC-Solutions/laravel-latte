<?php

namespace Eptic\Latte;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Htmlable;
use Stringable;

class View implements \Illuminate\Contracts\View\View, Stringable, Htmlable
{
    public const EXTENSION = 'latte';

    public function __construct(
        protected readonly Application $app,
        protected readonly \Latte\Engine $engine,
        protected readonly string $name,
        protected array $data = []
    ) {
        //
    }

    public function name()
    {
        return $this->name;
    }

    public function with($key, $value = null)
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function render()
    {
        return $this->engine->renderToString($this->name . '.' . $this::EXTENSION, $this->data);
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function toHtml()
    {
        return $this->render();
    }
}
