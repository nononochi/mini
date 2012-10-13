<?php
require_once 'conf/system.conf.php';

class MiniBlogApplication extends Application
{
    protected $_loginAction = array('account', 'signin');

    /**
     * ルートディレクトリを返す
     * @return string
     */
    public function getRootDir()
    {
        return dirname(__FILE__);
    }

    /**
     * ルーティング定義配列を設定
     * @return array
     */
    protected function _registerRoutes()
    {
        return array(
            '/' => array('controller' => 'status', 'action' => 'index'),
            '/status/post' => array('controller' => 'status', 'action' => 'post'),
            '/account' => array('controller' => 'account', 'action' => 'index'),
            '/account/:action' => array('controller' => 'account'),
            '/user/:user_name' => array('controller' => 'status', 'action' => 'user'),
            '/user/:user_name/status/:id' => array('controller' => 'status', 'action' => 'show'),
        );
    }

    /**
     * DBに接続する
     */
    protected function _configure()
    {
        $this->_dbManager->connect(
            'master', array(
                'dsn'      => 'mysql:dbname=mini;host=localhost',
                'user'     => USER,
                'password' => PASSWORD
            )        
        );
    }
}
