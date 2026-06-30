<?php

namespace Drupal\vendor_stream_wrapper\StreamWrapper;

/**
 * Interface for VendorStreamWrapper (vendor://).
 */
interface VendorStreamWrapperInterface {

  /**
   * Returns the path to the site vendor directory.
   *
   * This is first searched for one level above the webroot, then the webroot,
   * and if not found in either of those locations, a custom path can be set in
   * settings.php.
   *
   * @return string
   *   The path to the vendor directory.
   */
  public function getDirectoryPath(): string;

  /**
   * Returns the base path for vendor://.
   *
   * @return string
   *   The base path for vendor://.
   */
  public static function basePath(): string;

}
