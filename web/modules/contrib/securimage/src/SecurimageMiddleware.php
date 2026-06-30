<?php

namespace Drupal\securimage;

class SecurimageMiddleware {

  /**
   * Holds the Securimage instance.
   *
   * @var \Securimage
   */
  private static $instance;

  /**
   * Holds a cached copy of the settings.
   *
   * @var array
   */
  private static $settings;

  /**
   * @param array $vars
   * @return \Securimage
   * @throws \Exception
   */
  public static function getInstance($vars = array()) {
    if (!static::$instance) {
      $db_options = \Drupal::database()->getConnectionOptions();
      $host = !empty($db_options['port']) ? $db_options['host'] . ':' . $db_options['port'] : $db_options['host'];
      $si_options = static::getStoredSettings() + $vars + [
          // Tell Securimage to use a MySQL database to store data internally.
          'no_session' => TRUE,
          'use_database' => TRUE,
          'database_driver' => \Securimage::SI_DRIVER_MYSQL,
          'database_host' => $host,
          'database_user' => $db_options['username'],
          'database_pass' => $db_options['password'],
          'database_name' => $db_options['database'],
          'database_table' => \Drupal::database()->prefixTables('securimage_cache'),
          // The database is created once during install, so there's no need to check
          // during every page load.
          'skip_table_check' => TRUE,
        ];
      static::$instance = new \Securimage($si_options);
      if (!empty($si_options['lame_binary_path'])) {
        // lame_binary_path is a static, and isn't set in the constructor like the
        // others.
        \Securimage::$lame_binary_path = $si_options['lame_binary_path'];
      }
    }

    return static::$instance;
  }

  /**
   * Get all stored settings, with defaults.
   *
   * @return array
   */
  public static function getStoredSettings() {
    // Override some Securimage defaults to improve efficiency. Most of these
    // can be reconfigured by the user in the settings page.
    return \Drupal::config('securimage.settings')->get() + [
      // This is the same as the Securimage default, without 'l'.
      'charset' => 'ABCDEFGHKLMNPRSTUVWYZabcdefghkmnprstuvwyz23456789',
      'code_length' => 4,
      // These are internal to this module, not used directly by Securimage.
      '_module' => [
        'use_audio' => TRUE,
        'textfield_prompt' => t('Enter the characters shown in the image or use the speaker icon to get an audio version.'),
      ],
    ];
  }

  /**
   * @param null $which
   * @return mixed
   */
  public static function getSetting($which = NULL) {
    if (is_null(static::$settings)) {
      $securimage = static::getInstance();
      $curr_vars = static::getStoredSettings();
      static::$settings = array('_module' => $curr_vars['_module']);
      foreach (array_keys((array) $securimage) as $key) {
        if (preg_match('/^\w+$/', $key)) {
          if (isset($curr_vars[$key])) {
            static::$settings[$key] = $curr_vars[$key];
          }
          else if ($securimage->$key instanceof \Securimage_Color) {
            static::$settings[$key] = sprintf('#%x%x%x', $securimage->$key->r, $securimage->$key->g, $securimage->$key->b);
          }
          else {
            static::$settings[$key] = $securimage->$key;
          }
        }
      }
      // lame_binary_path is a static, so it can't be accessed like the others.
      if (isset($curr_vars['lame_binary_path'])) {
        static::$settings['lame_binary_path'] = $curr_vars['lame_binary_path'];
      }
      else {
        static::$settings['lame_binary_path'] = \Securimage::$lame_binary_path;
      }
    }

    if (empty($which)) {
      return static::$settings;
    }
    return static::$settings[$which] ?? NULL;
  }

  public static function getSolution() {
    if (empty($_GET['sid'])) {
      return FALSE;
    }

    // If there is an entry for this sid in the database, return it.
    return \Drupal::database()->query('SELECT csid, solution FROM {captcha_sessions} WHERE csid = :csid', array(':csid' => $_GET['sid']))->fetchObject();
  }

}
