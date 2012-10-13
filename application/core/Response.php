<?php

class Response
{

    protected $_content;
    protected $_statusCode = 200;
    protected $_statusText = 'OK';
    protected $_httpHeaders = array();

    /**
     * レスンポンスを送信する
     */
    public function send()
    {
        header('HTTP/1.1 ' . $this->_statusCode . ' ' . $this->_statusText);

        foreach ($this->_httpHeaders as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->_content;
    }

    /**
     * クライアントに返す内容をセットする
     *
     * @param string $content コンテンツの内容
     */
    public function setContent($content)
    {
        $this->_content = $content;
    }

    /**
     * ステータスコードとテキストをセットする
     *
     * @param int    $statusCode
     * @param string $statusText
     */
    public function setStatusCode($statusCode, $statusText = '')
    {
        $this->_statusCode = $statusCode;
        $this->_statusText = $statusText;
    }

    /**
     * HTTPヘッダを配列で格納する
     *
     * @param string $name  ヘッダのキー 
     * @param string $value ヘッダの内容
     */
    public function setHttpHeader($name, $value)
    {
        $this->_httpHeaders[$name] = $value;
    }

}
