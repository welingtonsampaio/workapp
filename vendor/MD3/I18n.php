<?php

namespace MD3;

use Engine\Registry;

/**
 * Class I18n, using to translations
 *
 * @package MD3
 * @author Welington Sampaio <welington.sampaio@zaez.net>
 */
class I18n
{

    /**
     * @var I18n
     */
    public static $_instance = null;
    /**
     * @var \Engine\Registry
     */
    protected $registry;
    /**
     * @var array
     */
    protected $translations = array();
    /**
     * @var array
     */
    protected $currentNamespace = null;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $dirname = $this->registry['path']['root'] . '/..' . $this->registry['path']['languages'] . $this->registry['lang'];
        if ($dh = opendir($dirname)) {
            while (false !== ($filename = readdir($dh))) {
                if (($filename != ".") and ($filename != "..")) {
                    include_once $dirname . '/' . $filename;
                }
            }
        }
    }

    /**
     * Add new translate namespace to collection.
     * Configure current namespace with method setNamespace
     *
     * +example+<br>
     *   $i18n->addNamespace('ns1', array('label'=>'value 1'));<br>
     *   $i18n->addNamespace('ns2', array('label'=>'value 2'));<br>
     *   $i18n->setNamespace('ns1');<br>
     *   echo $i18n->label; // value 1<br>
     *   $i18n->setNamespace('ns2');<br>
     *   echo $i18n->label; // value 2
     *
     * @see setNamespace
     * @param string $namespace
     * @param array $content
     */
    public function addNamespace($namespace, array $content)
    {
        if (array_key_exists($namespace, $this->translations))
            $this->translations[$namespace] = array_merge_recursive($this->translations[$namespace], $content);
        else
            $this->translations[$namespace] = $content;
    }

    /**
     * Setting the current namespace for translations.
     *
     * @param $namespace
     */
    public function setNamespace($namespace)
    {
        $this->currentNamespace = $namespace;
    }

    /**
     * Magic method to return a label in defined language
     *
     * @param string $label
     * @return string
     * @throws \Exception if setting namespace or namespace not found in collection
     */
    public function __get($label)
    {
        if ($this->currentNamespace === null)
            throw new \Exception('Namespace not configured!');
        if (!array_key_exists($this->currentNamespace, $this->translations))
            throw new \Exception('Namespace not found in collection!');

        return $this->translations[$this->currentNamespace][$label];
    }

    /**
     * @return I18n
     */
    public static function getInstance()
    {
        if (self::$_instance === null)
            self::$_instance = new self;
        return self::$_instance;
    }
}