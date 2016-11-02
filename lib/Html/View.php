<?php

namespace Html;

class View
{
    public $content;

    /**
     * @param      $template_view
     * @param null $content_view
     */
    public function generate($template_view, $content_view = null)
    {
        $this->content=$content_view;
        include $_SERVER['DOCUMENT_ROOT'].'/view/'.$template_view;
    }
}
