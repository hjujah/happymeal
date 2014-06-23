<?php namespace Vizioart\Cookbook\Services\Plupload;

use Input;
use Closure;
use Illuminate\Http\Request;
//use Illuminate\View\Compilers\CompilerInterface;

class Manager {

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function receive($name, Closure $handler)
    {
        $receiver = new Receiver($this->request);

        return $receiver->receive($name, $handler);
    }
}