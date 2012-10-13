<?php

class View
{
    protected $_baseDir;
    protected $_defaults;
    protected $_layoutVariables = array();

    public function __construct($baseDir, $defaults = array())
    {
        $this->_baseDir = $baseDir;
        $this->_defaults = $defaults;
    }

    /**
     * レイアウトファイルで変数を設定する
     *
     * @param string 変数名
     * @param mixed  値
     */
    public function setLayoutVar($name, $value)
    {
        $this->_layoutVariables[$name] = $value;
    }

    /**
     * レイアウトファイルを読み込む
     * 
     * @param  string   $_path      ビューファイルへのパス
     * @param  array    $_variables テンプレートにアサインする変数の配列
     * @param  string   $_layout    レイアウトファイル名
     * @return 表示内容
     */
    public function render($_path, $_variables = array(), $_layout = false)
    {
        $_file = $this->_baseDir . '/' . $_path . '.php';

        // ビューファイルからアクセス可能にするため
        extract(array_merge($this->_defaults, $_variables));

        ob_start();                 // アウトプットバッファリング開始(echoしたものがバッファへ)
        ob_implicit_flush(0);       // 自動フラッシュをしないように設定(容量を超えた場合に)      

        require $_file;

        $content = ob_get_clean();  // 出力内容を取得


        if ($_layout !== false) {
            $content = $this->render($_layout, array_merge($this->_layoutVariables, array('_content' => $content,)));
        }

        return $content;
    }

    /**
     * エスケープする
     *
     * @param string $string エスケープしたい対象
     */
    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
