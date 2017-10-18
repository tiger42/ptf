<?php

namespace PtfTest\View;

class Dummy extends \Ptf\View\Base
{
    public function render(string $cacheId = null): void
    {
    }

    public function fetch(string $cacheId = null): string
    {
    }

    public function registerFunctionPlugin(string $name, callable $function): void
    {
    }
}
