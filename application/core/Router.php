<?php

class Router
{

    protected $_routes;

    public function __construct($definitions)
    {
        $this->_routes = $this->compileRoutes($definitions);
    }

    /**
     * ルーティング内の動的パラメータを正規表現に置換する
     *
     * @param  array $definitions ルーティング定義配列
     * @return array              置換済のルーティング配列
     */
    public function compileRoutes($definitions)
    {
        $routes = array();

        foreach ($definitions as $url => $params) {
            $tokens = explode('/', ltrim($url, '/'));   // スラッシュで分割する

            foreach ($tokens as $i => $token) {
                if (strpos($token, ':') === 0) {
                    // コロンではじまる場合、正規表現形式に変換
                    $name = substr($token, 1);
                    $token = '(?P<' . $name . '>[^/]+)';
                }
                $tokens[$i] = $token;
            }

            $pattern = '/' . implode('/', $tokens);     //分割したURLをつなげる
            $routes[$pattern] = $params;
        }

        return $routes;
    }

    /**
     * 変換済のルーティング定義配列からパラメータの特定する
     * 
     * @param  array         $pathInfo変換済のルーティング定義配列
     * @return array|boolean ルーティングの配列、正しくない場合false
     */
    public function resolve($pathInfo)
    {
        if (substr($pathInfo, 0, 1) !== '/') {
            $pathInfo = '/' . $pathInfo;
        }
        foreach ($this->_routes as $pattern => $params) {

            if (preg_match('#^' . $pattern . '$#', $pathInfo, $matches)) {
                $params = array_merge($params, $matches);

                return $params;
            }
        }

        return false;
    }

}
