<?php

class DbManager
{

    protected $_connections = array();
    protected $_repositoryConnectionMap = array();
    protected $_repositories = array();

    /**
     * DBに接続する
     * 
     * @param string $name   接続を特定するための名前
     * @param array  $params 接続に必要な情報
     */
    public function connect($name, $params)
    {
        $params = array_merge(array(
            'dsn'      => null,
            'user'     => '',
            'password' => '',
            'options'  => array(),
                ), $params);

        $con = new PDO(
                        $params['dsn'],
                        $params['user'],
                        $params['password'],
                        $params['options']
        );

        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->_connections[$name] = $con;
    }

    /**
     * データベース名から、接続したコネクションを取得する
     *
     * @param  string データベース名
     * @return PDO    インスタンス
     */
    public function getConnection($name = null)
    {
        if (is_null($name) === true) {
            // 指定がなければ、最初に作成したものを返す
            return current($this->_connections);
        }

        return $this->_connections[$name];
    }

    /**
     * データベースとリポジトリをマッピングする
     *
     * @param string $repositoryName リポジトリ名
     * @param string $name           DB名
     */
    public function setRepositoryConnectionMap($repositoryName, $name)
    {
        $this->_repositoryConnectionMap[$repositoryName] = $name;
    }

    /**
     * リポジトリ名を指定し、データベース名を取得して、コネクション情報を取得する
     *
     * @param  string $repositoryName リポジトリ名
     * @return PDO    インスタンス
     */
    public function getConnectionForRepository($repositoryName)
    {
        if (isset($this->_repositoryConnectionMap[$repositoryName]) === true) {
            // 指定した名前のものが存在すれば、取得
            $name = $this->_repositoryConnectionMap[$repositoryName];
            $con = $this->getConnection($name);
        } else {
            $con = $this->getConnection();
        }

        return $con;
    }

    /**
     * リポジトル名を指定して、PDOのインスタンスを取得する
     *
     * @param  string $repositoryName リポジトリ名
     * @return PDO    インスタンス
     */
    public function get($repositoryName)
    {
        if (isset($this->_repositories[$repositoryName]) === false) {
            $repositoryClass = $repositoryName . 'Repository';          // クラス名指定
            $con = $this->getConnectionForRepository($repositoryName);  // コネクションを取得

            $repository = new $repositoryClass($con);

            $this->_repositories[$repositoryName] = $repository;
        }

        // すでにインスタンスを生成している場合、そのまま返す
        return $this->_repositories[$repositoryName];
    }

    /**
     * データベースとの接続を解放する 
     */
    public function __destruct()
    {
        foreach ($this->_repositories as $repository) {
            unset($repository);
        }

        foreach ($this->_connections as $con) {
            unset($con);
        }
    }

}
