<?php

abstract class Controller
{
    protected $_controllerName;
    protected $_actionName;
    protected $_application;
    protected $_request;
    protected $_response;
    protected $_session;
    protected $_dbManager;
    protected $_authActions = array();

    public function __construct($application)
    {
        // クラス名からコントローラー名取得
        $this->_controllerName = strtolower(substr(get_class($this), 0, -10));

        $this->_application = $application;
        $this->_request     = $application->getRequest();
        $this->_response    = $application->getResponse();
        $this->_session     = $application->getSession();
        $this->_dbManager   = $application->getDbManager();
        $this->_init();
    }
    
    protected function _init()
    {
        
    }

    /**
     * アクションの実行を行う
     *
     * @param string $action アクション名
     * @param array  $params パラメータ
     * @param string         実行結果
     */
    public function run($action, $params = array())
    {
        $this->_actionName = $action;

        $actionMethod = $action . 'Action';
        if (method_exists($this, $actionMethod) === false) {
            $this->_forward404();
        }

        if ($this->_needAuthentication($action) && $this->_session->isAuthenticated() === false){
            throw new UnauthorizedActionException();
        }

        $content = $this->$actionMethod($params);

        return $content;
    }

    /**
     * 認証が必要なアクションか
     *
     * @param  string  $action アクション名
     * @return boolean 必要な場合true
     */
    protected function _needAuthentication($action)
    {
        if ($this->_authActions === true || (is_array($this->_authActions) === true && in_array($action, $this->_authActions) === true)) {
            return true;
        }

        return false;
    }

    /**
     * レンダリングを行う
     * 
     * @param  array  $variables テンプレートにアサインする連想配列
     * @param  string $template  テンプレート名
     * @param  string $layout    レイアウトファイル名
     * @return array             レンダリング結果
     */
    protected function _render($variables = array(), $template = null, $layout = 'layout')
    {
        $defaults = array(
            'request' => $this->_request,
            'baseUrl' => $this->_request->getBaseUrl(),
            'session' => $this->_session,
        );

        $view = new View($this->_application->getViewDir(), $defaults);

        if (is_null($template) === true) {
            $template = $this->_actionName;
        }

        $path = $this->_controllerName . '/' . $template;

        return $view->render($path, $variables, $layout);
    }

    /**
     * 404の例外を投げる
     */
    protected function _forward404()
    {
        throw new HttpNotFoundException('Forwarded 404 page from '
            . $this->_controllerName . '/' . $this->_actionName);
    }

    /**
     * リダイレクトする
     * 
     * @param string $url リダイレクト先
     */
    protected function _redirect($url)
    {
        if (!preg_match('#https?://#', $url)) {
            $protocol = $this->_request->isSsl() ? 'https://' : 'http://';
            $host = $this->_request->getHost();
            $baseUrl = $this->_request->getBaseUrl();

            $url = $protocol . $host . $baseUrl;
        }
        $this->_response->setStatusCode(302, 'Found');
        $this->_response->setHttpHeader('Location', $url);

    }

    /**
     * トークンを作成し、保存する
     *
     * @param  string $formName フォーム名
     * @return string トークン 
     */
    protected function _generateCsrfToken($formName)
    {
        $key = 'csrf_tokens/' . $formName;

        $tokens = $this->_session->get($key, array());

        if (count($tokens) >= 10) {
            array_shift($tokens);
        }

        $token = sha1($formName . session_id() . microtime());
        $tokens[] = $token;

        $this->_session->set($key, $tokens);

        return $token;
    }

    /**
     * トークンをチェックする
     *
     * @param  string  $formName フォーム名
     * @param  string  $token    トークン
     * @return boolean 正しい場合true
     */
    protected function _checkCsrfToken($formName, $token)
    {
        $key = 'csrf_tokens/' . $formName;
        $tokens = $this->_session->get($key, array());

        if (($pos = array_search($token, $tokens, true) !== false)) {
            unset($tokens[$pos]);
            $this->_session->set($key, $tokens);
            
            return true;
        }
        return false;
    }

    
}
