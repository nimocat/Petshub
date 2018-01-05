<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_rich_snippets_snippet_microdata {
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

        $this->snippet = apply_filters('gdrts_rich_snippets_microdata_snippet', $this->snippet, $this->method, $this->series, $this->itemscope, $this->item);

        if (isset($this->snippet['root'])) {
            echo $this->_render_span($this->snippet['root']);
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
            $this->snippet['root'] = array(
                'tag' => 'span', 
                'itemscope' => true, 
                'itemtype' => 'http://schema.org/'.$this->itemscope,
                'items' => array(
                    'name' => array('tag' => 'meta', 'itemprop' => 'name', 'content' => $this->item->title()),
                    'url' => array('tag' => 'meta', 'itemprop' => 'url', 'content' => $this->item->url()),
                    'rating' => array('tag' => 'span', 'itemscope' => true, 'itemprop' => 'aggregateRating', 'itemtype' => 'http://schema.org/AggregateRating', 'items' => array(
                        'value' => array('tag' => 'meta', 'itemprop' => 'ratingValue', 'content' => floatval($rating['value'])),
                        'best' => array('tag' => 'meta', 'itemprop' => 'bestRating', 'content' => floatval($rating['best'])),
                        'count' => array('tag' => 'meta', 'itemprop' => 'ratingCount', 'content' => floatval($rating['count']))
                    ))
                )
            );
        }
    }

    private function _render_meta($data) {
        return '<meta itemprop="'.$data['itemprop'].'" content="'.$data['content'].'" />';
    }

    private function _render_span($data) {
        $out = '<span itemscope itemtype="'.$data['itemtype'].'"';

        if (isset($data['itemprop'])) {
            $out.= ' itemprop="'.$data['itemprop'].'"';
        }

        $out.= '>';

        foreach ($data['items'] as $item) {
            if ($item['tag'] == 'span') {
                $out.= $this->_render_span($item);
            } else {
                $out.= $this->_render_meta($item);
            }
        }

        $out.= '</span>';

        return $out;
    }
}
