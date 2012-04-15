<?php

/**
 * 閲覧側のトップページのコントローラーです。
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
 * @category    Setuco
 * @package     Default
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author      suzuki_mar
 */



/**
 * @package     Default
 * @subpackage  Controller
 * @author      suzuki_mar
 */
class IndexController extends Setuco_Controller_Action_DefaultAbstract
{
    /**
     * 新着ページ表示用に標準で何件取得するか
     *
     * @var string
     */
    const LIMIT_GET_NEW_PAGE = 10;

    /**
     * ページのサービスクラス
     *
     * @var Default_Model_Page
     *
     */
    private $_pageService = null;

    /**
     * categoryサービスクラスのインスタンス
     *
     * @var Default_Model_Category
     */
    private $_categoryService = null;

    /**
     * アクションの共通設定
     *
     * @return void
     * @author suzuki_mar
     */
    public function init()
    {
        //モジュール間の共通の設定を実行
        parent::init();

        $this->_pageService = new Default_Model_Page();
        $this->_categoryService = new Default_Model_Category();

    }

    /**
     * トップページのアクションです
     *
     * @return void
     * @author suzuki-mar
     */
    public function indexAction()
    {
        //新着ページを取得する
        $entries = $this->_pageService->findLastUpdatedPages(Setuco_Data_Constant_Module_Default::LIMIT_GET_NEW_PAGE);
        
        $date = new Zend_Date();
        foreach ($entries as $cnt => $entry) {
            $entries[$cnt]['contents'] = mb_substr(strip_tags($entry['contents']), 0, 100, 'UTF-8');
            $date->set($entry['update_date'], Zend_Date::ISO_8601);
            $entries[$cnt]['update_date'] = $date->toString('Y/MM/dd HH:mm');

            $categoryId = $entries[$cnt]['category_id'];
            if (is_null($categoryId)) {
                $category['id'] = Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE;
                $category['name'] = Setuco_Data_Constant_Category::UNCATEGORIZED_STRING;
                $category['parent_id'] = Setuco_Data_Constant_Category::NO_PARENT_ID;
            } else {
                $category = $this->_categoryService->findCategory($categoryId);
            }
            $this->view->category = $category;
            // var_dump($category['name']);
        }
        $this->view->entries = $entries;
        // 
        // // ページにつけられたタグ情報の取得とセット
        // // $this->view->tags = $this->_tagService->findTagsByPageId($id);
        // 
        // if (is_null($id)) {
        //     $category = Setuco_Data_Constant_Category::UNCATEGORIZED_INFO();
        // } else {
        //     $category = $this->_categoryService->findCategory($id);
        // }
        // 
        // $this->view->category = $category;

        // ページネーター用の設定
        // $this->view->currentPage = $currentPage;
        // $this->setPagerForView($this->_pageService->countPagesByCategoryId($id, Setuco_Data_Constant_Page::STATUS_RELEASE), self::LIMIT_PAGE_CATEGORY);
    }
}


