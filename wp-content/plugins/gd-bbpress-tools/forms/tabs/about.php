<div class="d4p-information">
    <fieldset>
        <h3>GD bbPress Tools <?php echo $options["version"]; ?></h3>
        <?php

        $status = ucfirst($options["status"]);
        if ($options["revision"] > 0) {
            $status.= " #".$options["revision"];
        }

        _e("Release Date: ", "gd-bbpress-tools");
        echo '<strong>'.$options["date"].'</strong><br/>';
        _e("Status: ", "gd-bbpress-tools");
        echo '<strong>'.$status.'</strong><br/>';
        _e("Build: ", "gd-bbpress-tools");
        echo '<strong>'.$options["build"].'</strong>';

        ?>
    </fieldset>

    <fieldset>
        <h3><?php _e("System Requirements", "gd-bbpress-tools"); ?></h3>
        <?php

            _e("PHP: ", "gd-bbpress-tools");
            echo '<strong>5.6 or newer</strong><br/>';
            _e("WordPress: ", "gd-bbpress-tools");
            echo '<strong>4.7 or newer</strong><br/>';
            _e("bbPress: ", "gd-bbpress-tools");
            echo '<strong>2.5 or newer</strong>';

        ?>
    </fieldset>

    <fieldset>
        <h3><?php _e("Important Plugin Links", "gd-bbpress-tools"); ?></h3>
        <a target="_blank" href="https://plugins.dev4press.com/gd-bbpress-tools/">GD bbPress Tools <?php _e("Home Page", "gd-bbpress-tools"); ?></a><br/>
        <a target="_blank" href="https://wordpress.org/extend/plugins/gd-bbpress-tools/">GD bbPress Tools <?php _e("on", "gd-bbpress-tools"); ?> WordPress.org</a>
        <h3><?php _e("Plugin Support", "gd-bbpress-tools"); ?></h3>
        <a target="_blank" href="https://support.dev4press.com/forums/forum/plugins-free/gd-bbpress-tools/"><?php _e("Plugin Support Forum on Dev4Press", "gd-bbpress-tools"); ?></a><br/>
        <h3><?php _e("Dev4Press Important Links", "gd-bbpress-tools"); ?></h3>
        <a target="_blank" href="https://twitter.com/milangd">Dev4Press <?php _e("on", "gd-bbpress-tools"); ?> Twitter</a><br/>
        <a target="_blank" href="https://www.facebook.com/dev4press">Dev4Press Facebook <?php _e("Page", "gd-bbpress-tools"); ?></a>
    </fieldset>
</div>
<div class="d4p-information-second">
    <?php include(GDBBPRESSTOOLS_PATH.'forms/more/toolbox.php'); ?>
</div>
<div class="d4p-clear"></div>
<div class="d4p-copyright">
    Dev4Press &copy; 2008 - 2020 <a target="_blank" href="https://www.dev4press.com/">www.dev4press.com</a> | Golden Dragon WebStudio <a target="_blank" href="https://www.gdragon.info">www.gdragon.info</a>
</div>
