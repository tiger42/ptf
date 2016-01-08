<?php

namespace PtfTest\App\Config;

class ViewPlain extends \Ptf\App\Config\ViewPlain
{
    public function __construct()
    {
        parent::__construct();
        $this->configData = array_merge($this->configData, [
            'template_dir' => 'src/View/Plain/templates/'
        ]);
    }

    public function getTemplateDir()
    {
        return $this->template_dir;
    }

}
