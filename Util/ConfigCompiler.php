<?php

namespace Ptf\Util;

/**
 * Compiles INI files into PHP files.
 */
class ConfigCompiler
{
    /**
     * Compile the given config INI file into \Ptf\App\Config class files.
     *
     * @param string $configName  The filename of the config file
     * @param string $configDir   The target directory for the generated class files
     * @param string $namespace   The namespace of the application
     */
    public static function compile(string $configName, string $configDir, string $namespace): void
    {
        $inifile  = new IniFile($configName);
        $sections = $inifile->toArray();

        if (!is_dir($configDir)) {
            mkdir($configDir, 0775, true);
        }

        foreach ($sections as $section => $settings) {
            $now      = now();
            $confData = var_export($settings, true);
            $parent   = preg_replace('/_.*/', '', $section);

            $conf = "<?php\n"
                . "// Generated on: $now\n\n"
                . "namespace $namespace\\App\\Config;\n\n"
                . "class $section extends \\Ptf\\App\\Config\\$parent\n"
                . "{\n"
                . "    public function __construct()\n"
                . "    {\n"
                . "        parent::__construct();\n"
                . "        \$this->configData = array_merge(\$this->configData,\n$confData);\n"
                . "    }\n\n";
            foreach ($settings as $key => $value) {
                $conf .= "    public function get" . camelize($key) . "()\n"
                    . "    {\n"
                    . "        return \$this->$key;\n"
                    . "    }\n\n";
            }
            $conf .= "}\n";

            file_put_contents($configDir . '/' . $section . '.php', $conf);
        }
    }
}
