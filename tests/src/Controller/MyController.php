<?php

namespace PtfTest\Controller;

class MyController extends \Ptf\Controller\Base
{
    public function getDefaultActionName(): string
    {
        return 'MyDefaultAction';
    }
}
