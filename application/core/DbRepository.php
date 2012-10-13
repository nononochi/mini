<?php

abstract class DbRepository
{
    protected $con;

    public function __construct($con)
    {
        $this->setConnection($con);
    }

    /**
     * コネクション情報をセットする
     *
     * @param PDO $con コネクション情報
     */
    public function setConnection($con)
    {
        $this->con = $con;
    }

    /**
     * 実行する
     *
     * @param  string $sql    SQL文
     * @param  array  $params プレースホルダの値(':name' => $name)
     * @return PDOStatement
     */
    public function execute($sql, $params = array())
    {
        // PDOStatementクラスのインスタンス
        $stmt = $this->con->prepare($sql);
        // SQLを実行する
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * 1行取得する
     *
     * @param  string $sql    SQL文
     * @param  array  $params プレースホルダの値(':name' => $name)
     * @return array          連想配列 
     */
    public function fetch($sql, $params = array())
    {
        // FETCH_ASSOCは連想配列で受け取る指定
        return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * すべての行取得する
     *
     * @param  string $sql    SQL文
     * @param  array  $params プレースホルダの値(':name' => $name)
     * @return array  連想配列 
     */
    public function fetchAll($sql, $params = array())
    {
        return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

}

