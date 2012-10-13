<?php

class StatusController extends Controller
{

    const TABLE_NAME = 'Status';

    protected $_authActions = array('index', 'post');
    private $_db;

    protected function _init()
    {
        $this->_db = $this->_dbManager->get(self::TABLE_NAME);
    }

    public function indexAction()
    {
        $user = $this->_session->get('user');
        $statuses = $this->_db->fetchAllPersonalArchives($user['id']);

        return $this->_render(array(
                    'statuses' => $statuses,
                    'body'     => '',
                    '_token'   => $this->_generateCsrfToken('status/post'),
                ));
    }

    public function postAction()
    {
        if ($this->_request->isPost() === false) {
            $this->_forward404();
        }

        $token = $this->_request->getpost('_token');

        if ($this->_checkCsrfToken('status/post', $token) === false) {
            // トークンが不正の場合
            return $this->_redirect('/');
        }

        $body = $this->_request->getPost('body');
        // バリデート
        $errors = $this->_db->validateBody($body);

        // セッション情報取得
        $user = $this->_session->get('user');

        if (empty($errors) === true) {
            // 投稿する
            $this->_db->insert($user['id'], $body);

            return $this->_redirect('/');
        }

        $statuses = $this->_db->fetchAllPersonalArchives($user['id']);

        // エラーを表示する
        return $this->_render(array(
                    'errors'   => $errors,
                    'body'     => $body,
                    'statuses' => $statuses,
                    '_token'   => $this->_generateCsrfToken('status/post'),
                        ), 'index');
    }

    public function userAction($params)
    {
        $user = $this->_dbManager->get('User')->fetchByUserName($params['user_name']);

        if ($user === false) {
            // ユーザが存在しない場合
            $this->forward404();
        }

        $statuses = $this->_db->fetchAllByUserId($user['id']);

        return $this->_render(array(
                    'user'     => $user,
                    'statuses' => $statuses,
                ));
    }

    public function showAction($params)
    {
        $status = $this->_db->fetchByIdAndUserName($params['id'], $params['user_name']);

        if ($status === false) {
            $this->forward404();
        }

        return $this->_render(array('status' => $status));
    }

}
