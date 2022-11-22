<?php

/**
 * @todo 
 * +make an install.php script that will require the wp-config.php and connect to it's db
 * +curl the zip with plugins and install
 * +insert all predefined values from plugins so there is no need to configure them nanually throug the WordPress dashboard
 * 
 * install custom user with details and private counter role
 * make instructions to run the install 
 * reload script with js during installation
 */

/**
 * @todo
 * modify table options
 *  $db_prefix_ options
 * 
 * selector option_name column:
 *  ure_role_additional_options_values
 *  uninstall_plugins
 *  active_plugins
 *  $db_prefix_ user_roles
 *  $db_prefix_ backup_user_roles
 *  ws_menu_editor
 * 
 * for start copy existing option values from the option_value column
 */

if (!file_exists('wp-config.php')) {
  die("no wp-config.php found!");
}

require_once('wp-config.php');

echo "$table_prefix\r\n<br>";
echo "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "\r\n<br>";

set_time_limit(0);

try {
  $url = 'https://wp-wb-installer.muster-webseiten.de/wb.zip';

  $file_name = basename($url);
  $result = file_put_contents('wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $file_name, file_get_contents($url));
  if (!empty($result)) {

    $zip = new ZipArchive;
    $res = $zip->open('wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $file_name);
    if ($res === TRUE) {
      $zip->extractTo('wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR);
      $zip->close();
      unlink('wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $file_name);
    } else {
      echo 'doh!\r\n<br>';
    }
  }
} catch (Exception $e) {
  echo $e->getMessage() . '\r\n<br>';
  die();
}


try {
  $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // begin the transaction
  $conn->beginTransaction();

  $options_table = $table_prefix . 'options';

  $ws_menu_editor = file_get_contents("ws_menu_editor.txt");
  $ure_role_additional_options_values = 'a:1:{s:23:"wp_private_counter_role";a:0:{}}';
  $uninstall_plugins = 'a:1:{s:37:"user-role-editor/user-role-editor.php";a:2:{i:0;s:16:"User_Role_Editor";i:1;s:9:"uninstall";}}';

  /**
   * @todo 
   * combine with existing active plugins
   */
  $active_plugins = 'a:8:{i:0;s:33:"admin-menu-editor/menu-editor.php";i:1;s:19:"akismet/akismet.php";i:2;s:9:"hello.php";i:3;s:43:"host-webfonts-local/host-webfonts-local.php";i:4;s:13:"pods/init.php";i:5;s:37:"user-role-editor/user-role-editor.php";i:6;s:29:"wp-mail-smtp/wp_mail_smtp.php";i:7;s:41:"wp-private-counter/wp-private-counter.php";}';
  $prefixed_backup_user_roles = 'a:5:{s:13:"administrator";a:2:{s:4:"name";s:13:"Administrator";s:12:"capabilities";a:61:{s:13:"switch_themes";b:1;s:11:"edit_themes";b:1;s:16:"activate_plugins";b:1;s:12:"edit_plugins";b:1;s:10:"edit_users";b:1;s:10:"edit_files";b:1;s:14:"manage_options";b:1;s:17:"moderate_comments";b:1;s:17:"manage_categories";b:1;s:12:"manage_links";b:1;s:12:"upload_files";b:1;s:6:"import";b:1;s:15:"unfiltered_html";b:1;s:10:"edit_posts";b:1;s:17:"edit_others_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:10:"edit_pages";b:1;s:4:"read";b:1;s:8:"level_10";b:1;s:7:"level_9";b:1;s:7:"level_8";b:1;s:7:"level_7";b:1;s:7:"level_6";b:1;s:7:"level_5";b:1;s:7:"level_4";b:1;s:7:"level_3";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:17:"edit_others_pages";b:1;s:20:"edit_published_pages";b:1;s:13:"publish_pages";b:1;s:12:"delete_pages";b:1;s:19:"delete_others_pages";b:1;s:22:"delete_published_pages";b:1;s:12:"delete_posts";b:1;s:19:"delete_others_posts";b:1;s:22:"delete_published_posts";b:1;s:20:"delete_private_posts";b:1;s:18:"edit_private_posts";b:1;s:18:"read_private_posts";b:1;s:20:"delete_private_pages";b:1;s:18:"edit_private_pages";b:1;s:18:"read_private_pages";b:1;s:12:"delete_users";b:1;s:12:"create_users";b:1;s:17:"unfiltered_upload";b:1;s:14:"edit_dashboard";b:1;s:14:"update_plugins";b:1;s:14:"delete_plugins";b:1;s:15:"install_plugins";b:1;s:13:"update_themes";b:1;s:14:"install_themes";b:1;s:11:"update_core";b:1;s:10:"list_users";b:1;s:12:"remove_users";b:1;s:13:"promote_users";b:1;s:18:"edit_theme_options";b:1;s:13:"delete_themes";b:1;s:6:"export";b:1;}}s:6:"editor";a:2:{s:4:"name";s:6:"Editor";s:12:"capabilities";a:34:{s:17:"moderate_comments";b:1;s:17:"manage_categories";b:1;s:12:"manage_links";b:1;s:12:"upload_files";b:1;s:15:"unfiltered_html";b:1;s:10:"edit_posts";b:1;s:17:"edit_others_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:10:"edit_pages";b:1;s:4:"read";b:1;s:7:"level_7";b:1;s:7:"level_6";b:1;s:7:"level_5";b:1;s:7:"level_4";b:1;s:7:"level_3";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:17:"edit_others_pages";b:1;s:20:"edit_published_pages";b:1;s:13:"publish_pages";b:1;s:12:"delete_pages";b:1;s:19:"delete_others_pages";b:1;s:22:"delete_published_pages";b:1;s:12:"delete_posts";b:1;s:19:"delete_others_posts";b:1;s:22:"delete_published_posts";b:1;s:20:"delete_private_posts";b:1;s:18:"edit_private_posts";b:1;s:18:"read_private_posts";b:1;s:20:"delete_private_pages";b:1;s:18:"edit_private_pages";b:1;s:18:"read_private_pages";b:1;}}s:6:"author";a:2:{s:4:"name";s:6:"Author";s:12:"capabilities";a:10:{s:12:"upload_files";b:1;s:10:"edit_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:4:"read";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:12:"delete_posts";b:1;s:22:"delete_published_posts";b:1;}}s:11:"contributor";a:2:{s:4:"name";s:11:"Contributor";s:12:"capabilities";a:5:{s:10:"edit_posts";b:1;s:4:"read";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:12:"delete_posts";b:1;}}s:10:"subscriber";a:2:{s:4:"name";s:10:"Subscriber";s:12:"capabilities";a:2:{s:4:"read";b:1;s:7:"level_0";b:1;}}}';
  $prefixed_user_roles = 'a:6:{s:13:"administrator";a:2:{s:4:"name";s:13:"Administrator";s:12:"capabilities";a:92:{s:13:"switch_themes";b:1;s:11:"edit_themes";b:1;s:16:"activate_plugins";b:1;s:12:"edit_plugins";b:1;s:10:"edit_users";b:1;s:10:"edit_files";b:1;s:14:"manage_options";b:1;s:17:"moderate_comments";b:1;s:17:"manage_categories";b:1;s:12:"manage_links";b:1;s:12:"upload_files";b:1;s:6:"import";b:1;s:15:"unfiltered_html";b:1;s:10:"edit_posts";b:1;s:17:"edit_others_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:10:"edit_pages";b:1;s:4:"read";b:1;s:8:"level_10";b:1;s:7:"level_9";b:1;s:7:"level_8";b:1;s:7:"level_7";b:1;s:7:"level_6";b:1;s:7:"level_5";b:1;s:7:"level_4";b:1;s:7:"level_3";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:17:"edit_others_pages";b:1;s:20:"edit_published_pages";b:1;s:13:"publish_pages";b:1;s:12:"delete_pages";b:1;s:19:"delete_others_pages";b:1;s:22:"delete_published_pages";b:1;s:12:"delete_posts";b:1;s:19:"delete_others_posts";b:1;s:22:"delete_published_posts";b:1;s:20:"delete_private_posts";b:1;s:18:"edit_private_posts";b:1;s:18:"read_private_posts";b:1;s:20:"delete_private_pages";b:1;s:18:"edit_private_pages";b:1;s:18:"read_private_pages";b:1;s:12:"delete_users";b:1;s:12:"create_users";b:1;s:17:"unfiltered_upload";b:1;s:14:"edit_dashboard";b:1;s:14:"update_plugins";b:1;s:14:"delete_plugins";b:1;s:15:"install_plugins";b:1;s:13:"update_themes";b:1;s:14:"install_themes";b:1;s:11:"update_core";b:1;s:10:"list_users";b:1;s:12:"remove_users";b:1;s:13:"promote_users";b:1;s:18:"edit_theme_options";b:1;s:13:"delete_themes";b:1;s:6:"export";b:1;s:14:"ure_edit_roles";b:1;s:16:"ure_create_roles";b:1;s:16:"ure_delete_roles";b:1;s:23:"ure_create_capabilities";b:1;s:23:"ure_delete_capabilities";b:1;s:18:"ure_manage_options";b:1;s:15:"ure_reset_roles";b:1;s:14:"edit_pods_pods";b:1;s:21:"edit_others_pods_pods";b:1;s:17:"publish_pods_pods";b:1;s:22:"read_private_pods_pods";b:1;s:16:"delete_pods_pods";b:1;s:12:"create_posts";b:1;s:20:"delete_pods_template";b:1;s:26:"edit_others_pods_templates";b:1;s:18:"edit_pods_template";b:1;s:19:"edit_pods_templates";b:1;s:17:"install_languages";b:1;s:4:"pods";b:1;s:37:"pods_component_import_export_packages";b:1;s:20:"pods_component_pages";b:1;s:24:"pods_component_templates";b:1;s:15:"pods_components";b:1;s:12:"pods_content";b:1;s:13:"pods_settings";b:1;s:22:"publish_pods_templates";b:1;s:18:"read_pods_template";b:1;s:27:"read_private_pods_templates";b:1;s:14:"resume_plugins";b:1;s:13:"resume_themes";b:1;s:23:"view_site_health_checks";b:1;}}s:6:"editor";a:2:{s:4:"name";s:6:"Editor";s:12:"capabilities";a:34:{s:17:"moderate_comments";b:1;s:17:"manage_categories";b:1;s:12:"manage_links";b:1;s:12:"upload_files";b:1;s:15:"unfiltered_html";b:1;s:10:"edit_posts";b:1;s:17:"edit_others_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:10:"edit_pages";b:1;s:4:"read";b:1;s:7:"level_7";b:1;s:7:"level_6";b:1;s:7:"level_5";b:1;s:7:"level_4";b:1;s:7:"level_3";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:17:"edit_others_pages";b:1;s:20:"edit_published_pages";b:1;s:13:"publish_pages";b:1;s:12:"delete_pages";b:1;s:19:"delete_others_pages";b:1;s:22:"delete_published_pages";b:1;s:12:"delete_posts";b:1;s:19:"delete_others_posts";b:1;s:22:"delete_published_posts";b:1;s:20:"delete_private_posts";b:1;s:18:"edit_private_posts";b:1;s:18:"read_private_posts";b:1;s:20:"delete_private_pages";b:1;s:18:"edit_private_pages";b:1;s:18:"read_private_pages";b:1;}}s:6:"author";a:2:{s:4:"name";s:6:"Author";s:12:"capabilities";a:10:{s:12:"upload_files";b:1;s:10:"edit_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:4:"read";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:12:"delete_posts";b:1;s:22:"delete_published_posts";b:1;}}s:11:"contributor";a:2:{s:4:"name";s:11:"Contributor";s:12:"capabilities";a:5:{s:10:"edit_posts";b:1;s:4:"read";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:12:"delete_posts";b:1;}}s:10:"subscriber";a:2:{s:4:"name";s:10:"Subscriber";s:12:"capabilities";a:2:{s:4:"read";b:1;s:7:"level_0";b:1;}}s:23:"wp_private_counter_role";a:2:{s:4:"name";s:20:"Private counter role";s:12:"capabilities";a:5:{s:12:"create_posts";b:1;s:10:"edit_posts";b:1;s:18:"edit_private_posts";b:1;s:13:"publish_posts";b:1;s:4:"read";b:1;}}}';

  $user_meta = $table_prefix . 'usermeta';

  /**
   * @todo check before inserting user meta
   */
  $capabilities = 'a:1:{s:23:"wp_private_counter_role";b:1;}';
  $conn->exec("INSERT IGNORE INTO $user_meta (meta_key, meta_value) VALUES ('" . $table_prefix . "user_level', '0')");
  $conn->exec("INSERT IGNORE INTO $user_meta (meta_key, meta_value) VALUES ('" . $table_prefix . "capabilities', '" . $capabilities . "')");

  $conn->exec("INSERT IGNORE INTO $options_table (option_name, option_value) VALUES ('ws_menu_editor', '" . $ws_menu_editor . "')");
  $conn->exec("INSERT IGNORE INTO $options_table (option_name, option_value) VALUES ('ure_role_additional_options_values', '" . $ure_role_additional_options_values . "')");
  $conn->exec("INSERT IGNORE INTO $options_table (option_name, option_value) VALUES ('uninstall_plugins', '" . $uninstall_plugins . "')");
  $conn->exec("INSERT IGNORE INTO $options_table (option_name, option_value) VALUES ('active_plugins', '" . $active_plugins . "')");
  $conn->exec("INSERT IGNORE INTO $options_table (option_name, option_value) VALUES ('" . $table_prefix . "backup_user_roles', '" . $prefixed_backup_user_roles . "')");
  $conn->exec("INSERT IGNORE INTO $options_table (option_name, option_value) VALUES ('" . $table_prefix . "user_roles', '" . $prefixed_user_roles . "')");

  // commit the transaction
  $conn->commit();

  echo "New records created successfully";
} catch (PDOException $e) {

  // roll back the transaction if something failed
  $conn->rollback();
  echo "Error: " . $e->getMessage() . '\r\n<br>';
}


// if (!file_exists('wp-load.php')) {
//   die("no wp-config.php found!");
// }

// try {
//   require_once __DIR__ . '/wp-load.php';

//   /**
//    * @todo try to import file with pods
//    */

// } catch (Exception $e) {
//   echo $e->getMessage() . '\r\n<br>';
// }
