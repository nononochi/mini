<?php

class Request
{

    /**
     * POSTかどうか判定する
     *
     * @return boolean
     */
    public function isPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }

        return false;
    }

    /**
     * $_GETから指定した値を取得する
     *
     * @param  string $name    取得したい値 
     * @param  mixed  $default 存在しなかった場合のデフォルトの値
     * @return mixed           取得した値もしくは、デフォルトを返す
     */
    public function getGet($name, $default = null)
    {
        if (isset($_GET[$name]) === true) {
            return $_GET[$name];
        }

        return $default;
    }

    /**
     * $_POSTから指定した値を取得する
     *
     * @param  string $name    取得したい値 
     * @param  mixed  $default 存在しなかった場合のデフォルトの値
     * @return mixed           取得した値もしくは、デフォルトを返す
     */
    public function getPost($name, $default = null)
    {
        if (isset($_POST[$name]) === true) {
            return $_POST[$name];
        }

        return $default;
    }

    /**
     * サーバのホスト名を取得する
     * 
     * @return string ホスト名
     */
    public function getHost()
    {
        if (empty($_SERVER['HTTP_HOST']) === false) {
            return $_SERVER['HTTP_HOST'];       // HTTPリクエストのヘッダに含まれるホスト名
        }

        return $_SERVER['SERVER_NAME'];         // Apache側に設定されたホスト名
    }

    /**
     * HTTPSアクセスかどうか判定する
     *
     * @return boolean
     */
    public function isSsl()
    {
        if (isset($_SERVER['HTTPS']) === true && $_SERVER['HTTPS'] === 'on') {
            return true;
        }

        return false;
    }

    /**
     * リクストされたURLの情報(ホスト名以降)を取得する
     *
     * @return string
     */
    public function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * ベースURLを取得
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $requestUri = $this->getRequestUri();

        if (strpos($requestUri, $scriptName) === 0) {
            // フロントコントローラーがURLに含まれる
            return $scriptName;
        } elseif (strpos($requestUri, dirname($scriptName)) === 0) {
            // フロントコントローラーが省略されている場合
            return rtrim($scriptName, '/');
        }

        return '';
    }

    /**
     * URLを取得する
     *
     * @return string 
     */
    public function getPathInfo()
    {
        $baseUrl = $this->getBaseUrl();
        $requestUri = $this->getRequestUri();

        if (($pos = strpos($requestUri, '?')) !== false) {
            // パラメータが含まれる場合、取り除く
            $requestUri = substr($requestUri, 0, $pos);
        }
        $pathInfo = (string) substr($requestUri, strlen($baseUrl));

        return $pathInfo;
    }

}
