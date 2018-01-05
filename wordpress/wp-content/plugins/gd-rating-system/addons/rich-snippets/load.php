<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_addon_rich_snippets extends gdrts_addon {
    public $prefix = 'rich-snippets';
    public $inserted = false;

    public function _load_admin() {
        require_once(GDRTS_PATH.'addons/rich-snippets/admin.php');
    }

    public function core() {
        if (is_admin()) {
            return;
        }

        add_action('gdrts-template-rating-rich-snippet', array($this, 'snippet'));
    }

    public function rule() {
        global $post;

        $item = gdrts_rating_item::get_instance(null, 'posts', $post->post_type, $post->ID);

        return array(
            'item' => $item,
            'display' => $item->get('rich-snippets_display', $this->get($post->post_type.'_snippet_display')),
            'method' => $item->get('rich-snippets_method', $this->get($post->post_type.'_snippet_method')),
            'itemscope' => $item->get('rich-snippets_itemscope', $this->get($post->post_type.'_snippet_itemscope'))
        );
    }

    public function is_allowed() {
        $allowed = false;

        if ($this->get('snippets_on_singular_pages_only')) {
            $allowed = is_main_query() && is_singular();
        } else {
            $allowed = true;
        }

        if ($allowed && $this->get('single_snippet_per_page')) {
            $allowed = !$this->inserted;
        }

        return $allowed;
    }

    public function snippet() {
        if ($this->is_allowed()) {
            $rule = $this->rule();

            if ($rule['display'] != 'hide' && $rule['display'] != 'default') {
                $_split = explode('::', $rule['method']);
                $_method = $_split[0];
                $_series = gdrts_method_has_series($_method) ? $_split[1] : '';

                if (gdrts_is_method_loaded($_method) && gdrts_single()->args('method') == $_method) {
                    $_loop_series = is_null(gdrts_single()->args('series')) || gdrts_single()->args('series') == '' ? '' : gdrts_single()->args('series');

                    if ($_loop_series == $_series) {
                        switch ($rule['display']) {
                            default:
                            case 'microdata':
                                require_once(GDRTS_PATH.'addons/rich-snippets/microdata.php');

                                $snippet = new gdrts_rich_snippets_snippet_microdata($_method, $_series, $rule['itemscope'], $rule['item']);
                                $snippet->snippet();

                                break;
                            case 'jsonld':
                                require_once(GDRTS_PATH.'addons/rich-snippets/jsonld.php');

                                $snippet = new gdrts_rich_snippets_snippet_jsonld($_method, $_series, $rule['itemscope'], $rule['item']);
                                $snippet->snippet();

                                break;
                        }

                        $this->inserted = true;
                    }
                }
            }
        }
    }
}

global $_gdrts_addon_rich_snippets;
$_gdrts_addon_rich_snippets = new gdrts_addon_rich_snippets();

function gdrtsa_rich_snippets() {
    global $_gdrts_addon_rich_snippets;
    return $_gdrts_addon_rich_snippets;
}
