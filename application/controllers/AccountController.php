<?php

class AccountController extends Controller
{

    const TABLE_NAME = 'User';

    protected $_authActions = array('index', 'signout');
    private $_db;

    protected function _init()
    {
        $this->_db = $this->_dbManager->get(self::TABLE_NAME);
    }

    public function indexAction()
    {
        $user = $this->_session->get('user');

        return $this->_render(array('user' => $user));
    }

    public function signupAction()
    {
        return $this->_render(array(
                    'userName' => '',
                    'password' => '',
                    '_token'   => $this->_generateCsrfToken('account/signup'),
                ));
    }

    public function signinAction()
    {
        if ($this->_session->isAuthenticated() === true) {
            // すでにログインしていた場合
            return $this->_redirect('/account');
        }

        return $this->_render(array(
                    'userName' => '',
                    'password' => '',
                    '_token'   => $this->_generateCsrfToken('account/signin'),
                ));
    }

    public function registerAction()
    {
        if ($this->_request->isPost() === false) {
            $this->_forward404;
        }

        $token = $this->_request->getPost('_token');

        if ($this->_checkCsrfToken('account/signup', $token) === false) {
            return $this->_redirect('/account/signup');
        }

        $userName = $this->_request->getPost('user_name');
        $password = $this->_request->getPost('password');

        $errors = array();
        $errors['user'] = $this->_db->validateUserName($userName);
        $errors['pass'] = $this->_db->validatePassword($password);

        if ($errors['user'] === '' && $errors['pass'] === '') {
            // 入力が問題ない場合登録する
            $this->_db->insert($userName, $password);

            // ログイン状態に設定
            $this->_session->setAuthenticated(true);

            $user = $this->_db->fetchByUserName($userName);
            // ユーザー情報をセッションに保存する
            $this->_session->set('user', $user);
            return $this->_redirect('/');
        }

        return $this->_render(array(
                    'userName' => $userName,
                    'password' => $password,
                    'errors'   => $errors,
                    '_token'   => $this->_generateCsrfToken('account/signup'),
                        ), 'signup');
    }

    public function authenticateAction()
    {
        if ($this->_session->isAuthenticated() === true) {
            // ログイン済みの場合ｓ
            return $this->_redirect('/account');
        }

        if ($this->_request->isPost() === false) {
            // ポスト以外でアクセス
            $this->_forward404();
        }

        $token = $this->_request->getPost('_token');

        if ($this->_checkCsrfToken('account/signin', $token) === false) {
            // トークが不正の場合、ログイン画面へ
            return $this->_redirect('/account/signin');
        }

        $userName = $this->_request->getPost('user_name');
        $password = $this->_request->getPost('password');

        $errors = array();
        if (strlen($userName) === 0) {
            $errors[] = 'ユーザーIDを入力してください';
        }
        if (strlen($password) === 0) {
            $errors[] = 'パスワードを入力してください';
        }

        if (count($errors) === 0) {
            $user = $this->_db->fetchByUserName($userName);
            if ($user === false || $user['password'] !== $this->_db->hashPassword($password)) {
                $errors[] = 'ユーザIDかパスワードが不正です';
            } else {
                $this->_session->setAuthenticated(true);
                $this->_session->set('user', $user);

                return $this->_redirect('/');
            }
        }

        return $this->_render(array(
                    'userName' => $userName,
                    'password' => $password,
                    'errors'   => $errors,
                    '_token'   => $this->_generateCsrfToken('account/signin'),
                        ), 'signin');
    }

    public function signoutAction()
    {
        $this->_session->clear();
        $this->_session->setAuthenticated(false);

        return $this->_redirect('/account/signin');
    }

}
