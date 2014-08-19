<?php

namespace Ptf\Core;

require_once \Ptf\BASEDIR . '/Traits/Singleton.php';

/**
 * Loader for automagically loading PHP files on demand
 */
class Autoloader
{
    use \Ptf\Traits\Singleton;

    /**
     * Namespace to base directory mapping
     * @var array
     */
    private $nsMapping;
    /**
     * Class name to PHP filename mapping
     * @var array
     */
    private $fileMapping;
    /**
     * Name of the class mapping file
     * @var string
     */
    private $cacheFilename;
    /**
     * Directories to search in for overridden framework classes
     * @var string[]
     */
    private $overrideDirs;

    /**
     * Initialize the member variables
     */
    private function __construct()
    {
        $this->nsMapping    = [];
        $this->fileMapping  = [];
        $this->overrideDirs = [];
    }

    /**
     * Load the PHP file containing the given class, trait or interface
     *
     * @param   string $className           The name of the class to load
     */
    public function load($className)
    {
        if (isset($this->fileMapping[$className])
            && (@include $this->fileMapping[$className]) !== false
        ) {
            return;
        }

        foreach ($this->nsMapping as $ns => $dir) {
            if (strpos($className, $ns . '\\') !== 0) {
                continue;
            }
            $relname = preg_replace('/^' . $ns . '\\\/', '', $className);

            foreach ($this->overrideDirs as $overrideDir) {
                $filename = $overrideDir . '/' . str_replace('\\', '/', $relname) . '.php';
                if ($this->includeFile($filename, $className)) {
                    return;
                }
            }

            $filename = $dir . '/' . str_replace('\\', '/', $relname) . '.php';
            if ($this->includeFile($filename, $className)) {
                return;
            }
        }
    }

    /**
     * Include the file with the given filename if it contains the given class; cache the filename
     *
     * @param    string $filename           The name of the file to include
     * @param    string $className          The class name to check
     * @return   boolean                    Could the file be included?
     */
    private function includeFile($filename, $className)
    {
        if ((@include $filename) !== false
                && (class_exists($className, false)
                    || trait_exists($className, false)
                    || interface_exists($className, false)
                )
        ) {
            $this->fileMapping[$className] = $filename;
            $this->writeCacheFile();
            return true;
        }
        return false;
    }

    /**
     * Register a namespace with its base directory.<br />
     * The specific subdirectory of each class will be determined automatically
     *
     * @param   string $namespace           The namespace to register
     * @param   string $dir                 The base directory of the namespace
     */
    public function registerNamespace($namespace, $dir)
    {
        $this->nsMapping[$namespace] = $dir;
    }

    /**
     * Add a directory for overridden framework classes
     *
     * @param    string $overrideDir        The directory to add
     */
    public function addOverrideDir($overrideDir)
    {
        $this->overrideDirs[] = $overrideDir;
    }

    /**
     * Set the name of the class mapping file
     *
     * @param   string $filename            The filename to set
     */
    public function setCacheFilename($filename)
    {
        $this->cacheFilename = $filename;
        @include $filename;
        if (isset($mapping) && is_array($mapping)) {
            $this->fileMapping = $mapping;
        }
    }

    /**
     * Write the internal path mapping to the cache file
     */
    private function writeCacheFile()
    {
        if ($this->cacheFilename === null) {
            return;
        }

        $handle = @fopen($this->cacheFilename . '.tmp', 'w+b');
        if ($handle === false || !flock($handle, LOCK_EX | LOCK_NB)) {
            @fclose($handle);
            return;
        }

        $str = "\$mapping = " . var_export($this->fileMapping, true) . ";\n";
        fwrite($handle, "<?php\n// Generated on: " . \Ptf\Util\now() . "\n\n" . $str);
        flock($handle, LOCK_UN);
        fclose($handle);
        rename($this->cacheFilename . '.tmp', $this->cacheFilename);
    }

}

$autoloader = Autoloader::getInstance();
$autoloader->registerNamespace('Ptf', \Ptf\BASEDIR);   // Register Ptf's own namespace

spl_autoload_register([Autoloader::getInstance(), 'load']);
