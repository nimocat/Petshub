<?php

$templates_raw = gdrts_settings()->current['templates'];

$table = array();
$templates = array();

foreach ($templates_raw as $method => $types) {
    $table[$method] = array('rows' => 0, 'types' => array(
        'single' => array('rows' => 0),
        'list' => array('rows' => 0)
    ));

    foreach ($types as $type => $tpls) {
        $table[$method]['rows']+= count($tpls);
        $table[$method]['types'][$type] = array('rows' => count($tpls));

        foreach ($tpls as $name => $label) {
            $templates[$method][$type][] = array('name' => $name, 'label' => $label);
        }
    }
}

?>
<table class="wp-list-table widefat fixed tables gdrts-grid-templates">
    <thead>
	<tr>
            <th class="manage-column column-method column-primary" id="name" scope="col">Method</th>
            <th class="manage-column column-type" id="engine" scope="col">Type</th>
            <th class="manage-column column-name" id="records" scope="col">Template</th>
            <th class="manage-column column-label" id="size" scope="col">Label</th>
            <th class="manage-column column-file" id="size" scope="col">File</th>
        </tr>
    </thead>
    <tbody data-wp-lists="list:table" id="the-list">
        <?php

        foreach ($table as $method => $data) {
            $_show_method = true;
            $_show_list = 0;
            $_show_single = 0;
            $_tpl_id = 0;

            $_key_method = $method;
            for ($i = 0; $i < $data['rows']; $i++) {
                echo '<tr>';

                if ($_show_method) {
                    echo '<td rowspan='.$data['rows'].'>'.ucwords(str_replace('-', ' ', $method)).'</td>';
                    $_show_method = false;
                }

                if ($_show_list < $data['types']['list']['rows']) {
                    if ($_show_list == 0) {
                        $_tpl_id = 0;
                        echo '<td rowspan='.$data['types']['list']['rows'].'>'.__("Ratings List", "gd-rating-system").'</td>';
                    }

                    $_show_list++;
                    $_key_type = 'list';
                } else if ($_show_single < $data['types']['single']['rows']) {
                    if ($_show_single == 0) {
                        $_tpl_id = 0;
                        echo '<td rowspan='.$data['types']['single']['rows'].'>'.__("Ratings Block", "gd-rating-system").'</td>';
                    }

                    $_show_single++;
                    $_key_type = 'single';
                }

                echo '<td class="column-name">'.$templates[$_key_method][$_key_type][$_tpl_id]['name'].'</td>';
                echo '<td>'.$templates[$_key_method][$_key_type][$_tpl_id]['label'].'</td>';
                echo '<td>gdrts--'.$method.'--'.$_key_type.'--'.$templates[$_key_method][$_key_type][$_tpl_id]['name'].'.php</td>';

                $_tpl_id++;

                echo '</tr>';
            }
        }
        
        ?>
    </tbody>
</table>