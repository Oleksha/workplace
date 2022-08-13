<?php

namespace workplace\libs;

class Pagination {

    public $currentPage;    // текущая страница
    public $perpage;        // сколько записей на страницу
    public $total;          // общее количество записей в БД
    public $countPages;     // общее количество страниц для вывода всез щаписей из БД
    public $uri;            // передеваемые параметры отличные от page

    public function __construct($page, $perpage, $total) {
        $this->perpage = $perpage;
        $this->total = $total;
        $this->countPages = $this->getCountPages();
        $this->currentPage = $this->getCurrentPage($page);
        $this->uri = $this->getParams();
    }

    public function getHtml(){
        $back = null;   // ссылка НАЗАД
        $forward = null;    // ссылка ВПЕРЕД
        //$startpage = null;  // ссылка В НАЧАЛО
        //$endpage = null;    // ссылка В КОНЕЦ
        $page2left = null;  // вторая страница слева
        $page1left = null;  // первая страница слева
        $page2right = null; // вторая страница справа
        $page1right = null; // первая страница справа

        if( $this->currentPage > 1 ){
            $back = "<li class='page-item'><a class='page-link' href='{$this->uri}page=" . ($this->currentPage - 1) . "'  aria-label='Previous'><span aria-hidden='true'>&laquo;</span><span class='sr-only'>Previous</span></a></li>";
        }
        if( $this->currentPage < $this->countPages ){
            $forward = "<li class='page-item'><a class='page-link' href='{$this->uri}page=" . ($this->currentPage + 1) . "' aria-label='Next'><span aria-hidden='true'>&raquo;</span><span class='sr-only'>Next</span></a></li>";
        }
        /*if( $this->currentPage > 3 ){
            $startpage = "<li><a class='nav-link' href='{$this->uri}page=1'>&lt;&lt;</a></li>";
        }
        if( $this->currentPage < ($this->countPages - 2) ){
            $endpage = "<li><a class='nav-link' href='{$this->uri}page={$this->countPages}'>&gt;&gt;</a></li>";
        }*/
        if( $this->currentPage - 2 > 0 ){
            $page2left = "<li class='page-item'><a class='page-link' href='{$this->uri}page=" .($this->currentPage-2). "'>" .($this->currentPage - 2). "</a></li>";
        }
        if( $this->currentPage - 1 > 0 ){
            $page1left = "<li class='page-item'><a class='page-link' href='{$this->uri}page=" .($this->currentPage-1). "'>" .($this->currentPage-1). "</a></li>";
        }
        if( $this->currentPage + 1 <= $this->countPages ){
            $page1right = "<li class='page-item'><a class='page-link' href='{$this->uri}page=" .($this->currentPage + 1). "'>" .($this->currentPage+1). "</a></li>";
        }
        if( $this->currentPage + 2 <= $this->countPages ){
            $page2right = "<li class='page-item'><a class='page-link' href='{$this->uri}page=" .($this->currentPage + 2). "'>" .($this->currentPage + 2). "</a></li>";
        }

        return '<ul class="pagination justify-content-center">' . $back . $page2left . $page1left . '<li class="page-item active"><a class="page-link">' . $this->currentPage . '</a></li>' . $page1right . $page2right . $forward . '</ul>';
    }

    public function __toString(){
        return $this->getHtml();
    }

    public function getCountPages(){
        return ceil($this->total / $this->perpage) ?: 1;
    }

    public function getCurrentPage($page){
        if(!$page || $page < 1) $page = 1;
        if($page > $this->countPages) $page = $this->countPages;
        return $page;
    }

    public function getStart(){
        return ($this->currentPage - 1) * $this->perpage;
    }

    public function getParams(){
        $url = $_SERVER['REQUEST_URI'];     // содержит строку параметров
        $url = explode('?', $url);  // разбиваем строку на массив параметров
        $uri = $url[0] . '?';
        if(isset($url[1]) && $url[1] != ''){
            $params = explode('&', $url[1]);
            foreach($params as $param){
                if(!preg_match("#page=#", $param)) $uri .= "{$param}&amp;";
            }
        }
        return urldecode($uri);
    }

}