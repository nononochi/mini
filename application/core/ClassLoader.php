<?php

class ClassLoader
{
    protected $_dirs;

    /**
     * PHPにオートローダクラスを登録する
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * クラスファイルを読み込むディレクトリを登録する
     * 
     * @param $dir ディレクトリのフルパス
     */
    public function registerDir($dir)
    {
        $this->_dirs[] = $dir;
    }

    /**
     * オートローダが実行された際に、クラスファイルを読み込む
     * 
     * @params string $class クラス名
     */
    public function loadClass($class)
    {
        foreach ($this->_dirs as $dir) {
            $file = $dir . '/' . $class . '.php';
            if (is_readable($file)) {
                require $file;

                return;
            }
        }
    }
}
