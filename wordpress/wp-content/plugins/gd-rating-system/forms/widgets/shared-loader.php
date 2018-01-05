<?php

$_tab = $instance['_tab'];

?>

<div class="d4plib-widget gdrts-widget">
    <div class="d4plib-widget-tabs" role="tablist">
        <input class="d4plib-widget-active-tab" value="<?php echo $_tab; ?>" id="<?php echo $this->get_field_id('_tab'); ?>" name="<?php echo $this->get_field_name('_tab'); ?>" type="hidden" />
        <?php

        foreach ($_tabs as $tab => $obj) {
            $tabkey = $this->get_tabkey($tab);

            $class = 'd4plib-widget-tab d4plib-tabname-'.$tabkey;
            $selected = 'false';

            if (isset($obj['class'])) {
                $class.= ' '.$obj['class'];
            }

            if ($tab == $_tab) {
                $class.= ' d4plib-tab-active';
                $selected = 'true';
            }

            echo '<a tabindex="0" id="'.$tabkey.'-tab" aria-controls="'.$tabkey.'" aria-selected="'.$selected.'" role="tab" data-tabname="'.$tab.'" href="#'.$tabkey.'" class="'.$class.'">'.$obj['name'].'</a>';
        }

        ?>
    </div>
    <div class="d4plib-widget-tabs-content">
        <?php

        $first = true;
        foreach ($_tabs as $tab => $obj) {
            $tabkey = $this->get_tabkey($tab);

            $class = 'd4plib-tab-content d4plib-tabname-'.$tabkey;
            $selected = 'true';

            if (isset($obj['class'])) {
                $class.= ' '.$obj['class'];
            }

            if ($tab == $_tab) {
                $class.= ' d4plib-content-active';
                $selected = 'false';
            }

            echo '<div id="'.$tabkey.'" aria-hidden="'.$selected.'" role="tabpanel" class="'.$class.'" aria-labelledby="'.$tabkey.'-tab">';

            foreach ($obj['include'] as $inc) {
                include(GDRTS_PATH.'forms/widgets/'.$inc.'.php');
            }

            echo '</div>';
        }

        ?>
    </div>
</div>