<?php
function wk_st_toc_settings()
{
    if ($_GET['do'] == 'save') {

        if (wp_verify_nonce($_POST['_wpnonce'], 'wk_st_toc_settings')) {
            global $wpdb;
            $title = wk_st_toc_validate(sanitize_text_field($_POST["title"]), "title");
            $list_type = wk_st_toc_validate(sanitize_text_field($_POST["list-type"]), "list_type");
            $toc_first = wk_st_toc_validate(sanitize_text_field($_POST["open-tag"]), "toc_first");
            $load_css = wk_st_toc_validate(sanitize_text_field($_POST["load-css"]), "load_css");

            if ($title && get_option('wk_st_toc_table_title') != $title) {
                update_option('wk_st_toc_table_title', $title);
                echo '<div class="updated"><p><b>Title updated.</b></p></div>';
            }
            if ($list_type && get_option('wk_st_toc_table_list_type') != $list_type) {
                update_option('wk_st_toc_table_list_type', $list_type);
                echo '<div class="updated"><p><b>List Style type changed.</b></p></div>';
            }
            if ($toc_first && get_option('wk_st_toc_first') != $toc_first) {
                update_option('wk_st_toc_first', $toc_first);
                echo '<div class="updated"><p><b>Content Title heading tag changed.</b></p></div>';
            }
            if (get_option('wk_st_toc_load_style') != $load_css) {
                update_option('wk_st_toc_load_style', $load_css);
                echo '<div class="updated"><p><b>Loading of plugin CSS reverted.</b></p></div>';
            }
            echo '<div class="wrap">';
            toc_settings_form();
            echo '</div>';
        } else {
            echo "Nonce failed";
        }
    } else {
        echo '<div class="wrap">';
        wk_st_toc_settings_form();
        echo '</div>';
    }
}

function wk_st_toc_validate($data, $type)
{
    switch ($type) {
        case "title":
            strip_tags(html_entity_decode($data));
            break;
        case "list_type":
            if ($data != "ol" && $data != "ul")
                $data = false;
            break;
        case "toc_first":
            if (intval($data))
                if (($data >= 1) && ($data <= 5))
                    return $data;
                else $data = false;
            else $data = false;
            break;
        case "load_css":
            if ($data != "y")
                $data = "";
            break;
        default :
            $data = false;
    }
    return $data;
}

function wk_st_toc_settings_form()
{
    $table_title = get_option('wk_st_toc_table_title');
    $table_list_type = get_option('wk_st_toc_table_list_type');
    $toc_first = get_option('wk_st_toc_first');
    $toc_style = get_option('wk_st_toc_load_style');

    if ($toc_style == "y") {
        $css = ' checked="checked"';
        $css_info = 'Plugin stylesheet is loading';
    } else {
        $css = '';
        $css_info = 'Plugin stylesheet is <b>not</b> loading';
    }
    if ($table_list_type == "ol") {
        $ol = 'selected';
        $ul = '';
        $info = ' <b>[Ordered Listing]</b> is selected';
    } elseif ($table_list_type == "ul") {
        $ol = '';
        $ul = 'selected';
        $info = ' <b>[Unordered listing]</b> is selected';
    } else {
        $ol = '';
        $ul = '';
        $info = ' Unknown list type. Result may break.';
    }
    if ($toc_first == "1") {
        $h1 = 'selected';
        $h2 = '';
        $h3 = '';
        $h4 = '';
        $h5 = '';
        $tag_info = '<b>[h1]</b> tag is selected';
    } elseif ($toc_first == "2") {
        $h1 = '';
        $h2 = 'selected';
        $h3 = '';
        $h4 = '';
        $h5 = '';
        $tag_info = '<b>[h2]</b> tag is selected';
    } elseif ($toc_first == "3") {
        $h1 = '';
        $h2 = '';
        $h3 = 'selected';
        $h4 = '';
        $h5 = '';
        $tag_info = '<b>[h3]</b> tag is selected';
    } elseif ($toc_first == "4") {
        $h1 = '';
        $h2 = '';
        $h3 = '';
        $h4 = 'selected';
        $h5 = '';
        $tag_info = '<b>[h4]</b> tag is selected';
    } elseif ($toc_first == "5") {
        $h1 = '';
        $h2 = '';
        $h3 = '';
        $h4 = '';
        $h5 = 'selected';
        $tag_info = ' <b>[h5]</b> tag is selected';
    } else {
        $h1 = '';
        $h2 = '';
        $h3 = '';
        $h4 = '';
        $h5 = '';
        $tag_info = ' Invalid data, results may break.';
    }

    ?>

    <div class="metabox-holder">
        <div class="postbox ">
            <h3 class="hndle"><span>Table of Content settings</span></h3>
            <div class="inside">
                <form id="save-settings" action="<?php echo($_SERVER['PHP_SELF']) ?>?page=toc&action=settings&do=save"
                      method="post">
                    <?php wp_nonce_field("toc_settings", "_wpnonce"); ?>
                    <h4>These settings allows to modify the Output for both Table of Content widget and content sections
                        on Single Post or Page screen.</h4>

                    <table class="wp-list-table widefat fixed posts" cellspacing="0">
                        <thead>
                        <tr>
                            <th scope="col" class="manage-column" style="width:15%;">Item</th>
                            <th scope="col" class="manage-column" style="width:20%;">Input</th>
                            <th scope="col" class="manage-column">Current Value</th>
                            <th scope="col" class="manage-column" style="width:45%;">Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr style="vertical-align:middle;">
                            <td><b>Widget Title</b></td>
                            <td><input type="text" name="title" value="<?php echo $table_title; ?>"/></td>
                            <td><b><?php echo $table_title; ?></b></td>
                            <td style="width:;"><span class="description">Table widget title that shows on posts after using the [toc] shortcode.</span>
                            </td>
                        </tr>

                        <tr style="vertical-align:middle;">
                            <td><b>List Type</b></td>
                            <td>
                                <select name="list-type">
                                    <option value="ol" <?php echo $ol ?>>OL</option>
                                    <option value="ul" <?php echo $ul ?>>UL</option>
                                </select>
                            </td>
                            <td><?php echo $info; ?></td>
                            <td style="width:;"><span class="description">Listing type for Table of Content widget items.</span>
                            </td>
                        </tr>

                        <tr style="vertical-align:middle;">
                            <td><b>Content Title tag</b></td>
                            <td>
                                <select name="open-tag">
                                    <option value="1" <?php echo $h1 ?>>H1</option>
                                    <option value="2" <?php echo $h2 ?>>H2</option>
                                    <option value="3" <?php echo $h3 ?>>H3</option>
                                    <option value="4" <?php echo $h4 ?>>H4</option>
                                    <option value="5" <?php echo $h5 ?>>H5</option>
                                </select>
                            </td>
                            <td><?php echo $tag_info; ?></td>
                            <td style="width:;"><span class="description">Table of content title and First level contents will get this TAG, following levels will have 1 step lower than parent tag.</span>
                            </td>
                        </tr>

                        <tr style="vertical-align:middle;">
                            <td><b>Load default CSS</b></td>
                            <td>
                                <input type="checkbox" name="load-css" value="y" <?php echo $css; ?>/>
                            </td>
                            <td><?php echo $css_info; ?></td>
                            <td style="width:;"><span class="description">This will load the pluging's style sheet and override the themes styles.</span>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                    <br/><br/>
                    <input type="submit" value="Save Settings" id="save" class="button-primary"/>

                    <div style="clear:both;"></div>
                </form>
            </div>
        </div>
    </div>

    <?php
}