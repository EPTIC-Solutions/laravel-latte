<?php

namespace Eptic\Latte;

use Illuminate\Contracts\Foundation\Application;
use Latte\RuntimeException;

class ViewFactory implements \Illuminate\Contracts\View\Factory
{
    protected array $sharedData = [];
    /**
     * @var array<string, \Closure|string>
     */
    protected array $composers = [];

    public function __construct(
        protected readonly Application $app,
        protected readonly \Latte\Engine $engine
    ) {
        //
    }

    public function exists($view)
    {
        try {
            $this->engine->getLoader()->getContent($view);
            return true;
        } catch (RuntimeException $e) {
            return false;
        }
    }

    public function file($path, $data = [], $mergeData = [])
    {
        $data = array_merge($mergeData);

        $engine = clone $this->engine;
        $engine->setLoader(new \Latte\Loaders\FileLoader(dirname($path)));

        $fileName = basename($path, '.' . View::EXTENSION);

        $view = new \Eptic\Latte\View($this->app, $engine, $fileName, $data);

        return $this->compose($view);
    }

    public function make($view, $data = [], $mergeData = [])
    {
        $data = array_merge($mergeData);

        $view =  new \Eptic\Latte\View($this->app, $this->engine, $view, $data);

        return $this->compose($view);
    }

    protected function compose(View $view): View
    {
        if (isset($this->composers[$view->name()])) {
            $composer = $this->composers[$view->name()];
            if (is_callable($composer)) {
                $composer($view);
            } else {
                $this->app[$composer]->compose($view);
            }
        }

        return $view;
    }

    public function share($key, $value = null)
    {
        $this->sharedData[$key] = $value;

        return $this;
    }

    public function composer($views, $callback)
    {
        if (!is_array($views)) {
            $views = [$views];
        }

        foreach ($views as $view) {
            $this->composers[$view] = $callback;
        }
    }

    public function creator($views, $callback)
    {
        $this->composer($views, $callback);
    }

    public function addNamespace($namespace, $hints)
    {
        //
    }

    public function replaceNamespace($namespace, $hints)
    {
        //
    }
}
