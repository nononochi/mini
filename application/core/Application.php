<?php

abstract class Application
{
    protected $_debug = false;
    protected $_request;
    protected $_response;
    protected $_session;
    protected $_dbManager;
    protected $_loginAction = array();
    private $_router;

    public function __construct($debug = false)
    {
        $this->_setDebugMode($debug);
        $this->_initialize();
        $this->_configure();
    }

    /**
     * デバックモードを設定する
     *
     * @param boolean $debug デバックモードにする場合true
     */
    protected function _setDebugMode($debug)
    {
        if ($debug === true) {
            $this->_debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->_debug = false;
            ini_set('display_errors', 0);
        }
    }

    /**
     * コンストラクターから呼ばれ、初期化する
     */
    protected function _initialize()
    {
        $this->_request   = new Request();
        $this->_response  = new Response();
        $this->_session   = new Session();
        $this->_dbManager = new DbManager();
        $this->_router     = new Router($this->_registerRoutes());
    }

    /**
     * コンストラクターから呼ばれ、何らかを設定する
     */
    protected function _configure()
    {
    }

    // ルートパスを返す
    abstract public function getRootDir();
    
    // ルーティング定義配列を設定する
    abstract protected function _registerRoutes();

    /**
     * デバックモードかどうか
     *
     * @return boolean デバックモードの場合true
     */
    public function isDebugMode()
    {
        return $this->_debug;
    }

    /**
     * リクエストオブジェクトを取得する
     *
     * @return Request 
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * レスポンスオブジェクトを取得する
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * セッションオブジェクトを取得する
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * DBマネージャーオブジェクトを取得する
     *
     * @return DbManager
     */
    public function getDbManager()
    {
        return $this->_dbManager;
    }

    /**
     * コントローラーのパスを取得する
     *
     * @return string
     */
    public function getControllerDir()
    {
        return $this->getRootDir() . '/controllers';
    }

    /**
     * ビューのパスを取得する
     *
     * @return string
     */
    public function getViewDir()
    {
        return $this->getRootDir() . '/views';
    }

    /**
     * モデルのパスを取得する
     *
     * @return string
     */
    public function getModelDir()
    {
        return $this->getRootDir() . '/models';
    }

    /**
     * webのパスを取得する
     *
     * @return string
     */
    public function getWebDir()
    {
        return $this->getRootDir() . '/web';
    }

    /**
     * アプリケーションを実行する
     */
    public function run()
    {
        try {
            // URLからコントローラー名とアクション名の配列を得る
            $params = $this->_router->resolve($this->_request->getPathInfo());
            if ($params === false) {
                throw new HttpNotFoundException('No route found for' . $this->_request->getPathInfo());
            }

            $controller = $params['controller'];
            $action     = $params['action'];
            
            // アクションを実行する
            $this->runAction($controller, $action, $params);
        } catch (HttpNotFoundException $e){
            $this->render404Page($e);
        } catch(UnauthorizedActionException $e){
            list($controller, $action) = $this->_loginAction;
            $this->runAction($controller, $action);
        }

        $this->_response->send();
    }

    /**
     * アクションを実行する
     *
     * @param string $controllerName コントローラー名
     * @param string $action         アクション名
     * @param array  $params         パラメータ
     */
    public function runAction($controllerName, $action, $params = array())
    {
        $controllerClass = ucfirst($controllerName) . 'Controller';     // 文字列の最初の文字を大文字にする
        // コントローラーのインスタンスを取得
        $controller = $this->_findController($controllerClass);

        if ($controller === false) {
           // コントローラーがない場合
           throw new HttpNotFoundException($controllerClass . ' controller is not found.');
        }

        // アクションを実行する
        $content = $controller->run($action, $params);

        // 実行結果をレスポンスへセットする
        $this->_response->setContent($content);
    }

    /**
     * コントローラーのインスタンスを取得する
     *
     * @param  string $controllerClass コントローラーのクラス名
     * @return AccountController       
     */
    protected function _findController($controllerClass)
    {
        if (class_exists($controllerClass) === false) {
            // クラスが定義済みでない場合
            $controllerFile = $this->getControllerDir() . '/' . $controllerClass . '.php';
            if (is_readable($controllerFile) === false) {
                // クラスファイルが読み取れない場合
                return false;
            } else {
                require_once $controllerFile;

                if (class_exists($controllerClass) === false){
                    // ファイル内にクラスが定義されていなかった場合
                    return false;
                }
            }
        }

        return new $controllerClass($this);
    }

    /**
     * エラーページを表示する
     *
     * @param HttpNotFoundException $e
     */
    protected function _render404Page($e)
    {
        $this->_response->setStatusCode('404', 'Not Found');
        $message = htmlspecialchars($this->isDebugMode() ? $e->getmessage() : 'Page not found.', ENT_QUOTES, 'UTF-8');

        $this->_response->setContent(<<<EOF
<!DOCTYPE html>  
<html lang="ja">  
<head>
    <meta charset="utf-8">
    <title>404</title>
</head>
<body>
    {$message}
</body>
</html>
EOF
        );
        
    }
}
