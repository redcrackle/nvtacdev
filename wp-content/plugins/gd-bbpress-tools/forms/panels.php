<?php

$current = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'tools';

$tabs = array(
    'tools' => '<span class="dashicons dashicons-admin-settings" title="'.__("Settings", "gd-bbpress-tools").'"></span><span class="tab-title"> '.__("Settings", "gd-bbpress-tools").'</span>', 
    'tweaks' => '<span class="dashicons dashicons-lightbulb" title="'.__("Tweaks", "gd-bbpress-tools").'"></span><span class="tab-title"> '.__("Tweaks", "gd-bbpress-tools").'</span>', 
    'bbcode' => '<span class="dashicons dashicons-editor-code" title="'.__("BBCodes", "gd-bbpress-tools").'"></span><span class="tab-title"> '.__("BBCodes", "gd-bbpress-tools").'</span>', 
    'views' => '<span class="dashicons dashicons-visibility" title="'.__("Views", "gd-bbpress-tools").'"></span><span class="tab-title"> '.__("Views", "gd-bbpress-tools").'</span>', 
    'd4p' => '<span class="dashicons dashicons-flag" title="'.__("Dev4Press", "gd-bbpress-tools").'"></span><span class="tab-title"> '.__("Dev4Press", "gd-bbpress-tools").'</span>',
    'about' => '<span class="dashicons dashicons-info" title="'.__("About", "gd-bbpress-tools").'"></span><span class="tab-title"> '.__("About", "gd-bbpress-tools").'</span>'
);

if (!isset($tabs[$current])) {
    $current = 'tools';
}

$to_load = $current;

?>
<div class="wrap">
    <h2>GD bbPress Tools</h2>
    <div id="icon-tools" class="icon32"><br></div>
    <h2 class="nav-tab-wrapper d4p-tabber-ctrl">
    <?php

    foreach($tabs as $tab => $name){
        $class = ($tab == $current) ? ' nav-tab-active' : '';

        if ($tab == 'toolbox') {
            $class.= ' d4p-tab-toolbox';
        }

        echo '<a class="nav-tab'.$class.'" href="edit.php?post_type=forum&page=gdbbpress_tools&tab='.$tab.'">'.$name.'</a>';
    }

    ?>
    </h2>
    <div id="d4p-panel" class="d4p-panel-<?php echo $current; ?>">
        <?php include(GDBBPRESSTOOLS_PATH.'forms/tabs/'.$to_load.'.php'); ?>
    </div>
</div>