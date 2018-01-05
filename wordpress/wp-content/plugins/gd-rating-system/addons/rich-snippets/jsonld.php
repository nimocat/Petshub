<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_rich_snippets_snippet_jsonld {
    public $method = '';
    public $series = '';
    public $itemscope = '';

    public $item;

    public $snippet = array();

    function __construct($method, $series, $itemscope, $item) {
        $this->method = $method;
        $this->series = $series;
        $this->itemscope = $itemscope;
        $this->item = $item;
    }

    public function snippet() {
        $this->_rating();

        $this->snippet = apply_filters('gdrts_rich_snippets_jsonld_snippet', $this->snippet, $this->method, $this->series, $this->itemscope, $this->item);

        if (!empty($this->snippet)) {
            echo $this->_render($this->snippet);
        }
    }

    private function _rating() {
        $rating = array();

        switch ($this->method) {
            case 'stars-rating':
                $rating = gdrtsm_stars_rating()->rating($this->item);
                break;
        }

        if (!empty($rating)) {
            $this->snippet = array(
                '@context' => 'http://schema.org/',
                '@type' => $this->itemscope,
                'name' => $this->item->title(),
                'url' => $this->item->url(),
                'aggregateRating' => array(
                    '@type' => 'aggregateRating',
                    'ratingValue' => floatval($rating['value']),
                    'bestRating' => floatval($rating['best']),
                    'ratingCount' => floatval($rating['count'])
                )
            );
        }
    }

    private function _render($snippet) {
        $out = '<script type="application/ld+json">';
        $out.= json_encode($snippet);
        $out.= '</script>';

        return $out;
    }
}
