<?php

/**
 * 認証サービスです。
 *
 * Copyright (c) 2010-2011 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * All Rights Reserved.
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category   Setuco
 * @package    Admin
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     Yuu Yamanaka, charlesvineyard　suzuki-mar
 */

/**
 * @category   Setuco
 * @package    Admin
 * @subpackage Model
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author     Yuu Yamanaka, charlesvineyard suzuki_mar
 */
class Admin_Model_Auth
{

    /**
     * ユーザー認証インスタンス
     *
     * @var Zend_Auth
     */
    private $_authInstance;

    /**
     * ユーザー認証アダプタークラス
     * DBにアクセスするクラス
     *
     * @var Zend_Auth_Adapter_DbTable
     */
    private $_authAdapter;

    /**
     * 変数の初期設定をする
     *
     * @author suzuki-mar
     */
    public function  __construct()
    {
        $this->_authInstance = Zend_Auth::getInstance();
    }
    
    /**
     * アカウント情報のうち、セッションに保存する項目
     *
     * @return array
     */
    public static function RETURN_COLUMNS()
    {
        return array ('id', 'login_id', 'nickname');
    }

    /**
     * ログイン処理を行う
     *
     * @param  string $loginId  ログインID
     * @param  string $password ログインパスワード
     * @return bool ログインできたら true
     * @author suzuki-mar
     */
    public function login($loginId, $password)
    {
        $this->_authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $this->_authAdapter->setTableName('account')
                ->setIdentityColumn('login_id')
                ->setCredentialColumn('password')
                ->setCredentialTreatment('sha1(?)');
        $this->_authAdapter->setIdentity($loginId)
                ->setCredential($password);

        // DBと照合
        $result = $this->_authInstance->authenticate($this->_authAdapter);

        if (!$result->isValid()) {
            return false;
        }

        $loginAccountData = $this->_authAdapter->getResultRowObject(self::RETURN_COLUMNS());
        $this->saveLoginAccount($loginAccountData);

        return true;
    }

    /**
     * セッションにログインユーザー情報を書き込む
     *
     * @param mixed $loginAccountData ログインするアカウントデータ
     * @return void
     * @author suzuki-mar
     */
    public function saveLoginAccount($loginAccountData)
    {
        $storage = $this->_authInstance->getStorage();
        $storage->write($loginAccountData);
    }

    /**
     * ユーザー情報を取得する
     *
     * @return array ユーザー情報
     * @author suzuki-mar
     */
    public function getAccountInfos()
    {
        $storage = $this->_authInstance->getStorage();
        $result = $storage->read();

        if (is_object($result)) {
           $result = get_object_vars($result);
        }

        return $result;
    }

    /**
     * ログインしているか
     *
     * @return boolean ログインしているか
     * @author suzuki-mar
     */
    public function isLoggedIn()
    {
        return $this->_authInstance->hasIdentity();
    }

    /**
     * ログアウト処理を行う
     *
     * @return void
     */
    public function logout()
    {
        Zend_Auth::getInstance()->clearIdentity();
    }

}