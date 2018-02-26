<?php

namespace Ptf\Util;

/**
 * Provides functions for INI file access.
 */
class IniFile
{
    /**
     * The name of the INI file
     * @var string
     */
    protected $filename;

    /**
     * Array that holds the INI file's data
     * @var array
     */
    protected $data;

    /**
     * Load the INI file, initialize the internal data array.<br />
     * If no file with the given name exists, saveToDisk() will create a new file.
     *
     * @param string $filename  The filename of the INI file
     *
     * @throws \RuntimeException  If the file is not readable
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;

        if (file_exists($filename)) {
            if (!is_readable($filename)) {
                throw new \RuntimeException(get_class($this) . '::' . __FUNCTION__ . ': Could not read INI file');
            }
            $this->data = parse_ini_file($filename, true);

            return;
        }

        $this->data = [];
    }

    /**
     * Write the INI file to disk.
     *
     * @return int|false  Amount of bytes that were written to the file, FALSE on failure
     */
    public function saveToDisk()
    {
        return file_put_contents($this->filename, $this->__toString(), LOCK_EX);
    }

    /**
     * Get the value of the given key.
     *
     * @param string $section  The section the key is located in
     * @param string $key      The key to read the value of
     *
     * @return string|null  The requested value or NULL if the key does not exist
     */
    public function getValue(string $section, string $key): ?string
    {
        if (isset($this->data[$section]) && isset($this->data[$section][$key])) {
            return $this->data[$section][$key];
        }

        return null;
    }

    /**
     * Set a value to the given key.
     *
     * @param string $section  The section the key is located in
     * @param string $key      The key to write to
     * @param mixed  $value    The value to write
     *
     * @throws \InvalidArgumentException  If the value has an invalid type
     */
    public function setValue(string $section, string $key, $value): void
    {
        if (!strlen($section)) {
            $error = 'Invalid section name given';
        } elseif (!strlen($key)) {
            $error = 'Invalid key given';
        } elseif (is_array($value) || is_object($value) || is_resource($value)) {
            $error = 'Value parameter has invalid type';
        }

        if ($error) {
            throw new \InvalidArgumentException(get_class($this) . '::' . __FUNCTION__ . ': ' . $error);
        }

        if (!isset($this->data[$section])) {
            $this->data[$section] = [];
        }

        if ($value === true) {
            $this->data[$section][$key] = 'on';
        } elseif ($value === false) {
            $this->data[$section][$key] = 'off';
        } else {
            $this->data[$section][$key] = (string)$value;
        }
    }

    /**
     * Get a complete section as array.
     *
     * @param string $section  The name of the section to retrieve
     *
     * @return array|null  The section as array or NULL if the section does not exist
     */
    public function getSection(string $section): ?array
    {
        return $this->data[$section] ?? null;
    }

    /**
     * Delete a section.
     *
     * @param string $section  The section to delete
     */
    public function deleteSection(string $section): void
    {
        unset($this->data[$section]);
    }

    /**
     * Return all available section names as array.
     *
     * @return string[]  The list of all section names
     */
    public function getSectionNames(): array
    {
        return array_keys($this->data);
    }

    /**
     * Return the INI file's content as array.
     *
     * @return array  The INI file's data content
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Convert the internal data array to string.
     *
     * @return string  String representation of the object
     */
    public function __toString(): string
    {
        $ini = '; Generated on: ' . \Ptf\Util\now() . "\n\n";

        foreach ($this->data as $section => $sectionData) {
            $ini .= '[' . $section . "]\n";
            foreach ($sectionData as $key => $value) {
                $ini .= $key . ' = ' . var_export($value, true) . "\n";
            }
            $ini .= "\n";
        }

        return $ini;
    }
}
