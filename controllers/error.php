<?php
class Errors extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->view->msg = 'Страницы не существует!';
        $this->view->render('error/index');
    }

}
