<?php

namespace Drupal\vendor_stream_wrapper\Service;

// cspell:ignore creat

/**
 * Service providing management functionality for vendor stream wrappers.
 */
interface VendorStreamWrapperManagerInterface {

  /**
   * Gets the patterns for vendor files/directories that are safe to serve.
   *
   * @return string[]
   *   An array of safe-list regex patterns.
   */
  public function getSafeListRegexPatterns(): array;

  /**
   * Adds safe-list patterns.
   *
   * @param string[] $patterns
   *   The safe-list patterns to add.
   */
  public function addSafeListRegexPatterns(array $patterns): void;

  /**
   * Checks if the provided vendor file (path) is allowed to be downloaded.
   *
   * @param string $file_path
   *   The path of the vendor file.
   *
   * @return bool
   *   TRUE when the file (path) matches a pattern of the whitelist, FALSE
   *   otherwise.
   */
  public function isSafeListed(string $file_path): bool;

  /**
   * Creates a public facing URL from URIs with the vendor:// schema.
   *
   * @param string $uri
   *   The vendor:// prefixed URI to be converted to a public facing URL.
   * @param bool $include_base_url
   *   (Optional) Whether the URL should include the base URL. Defaults to TRUE.
   *
   * @return string|null
   *   - If the $uri is prefixed with vendor://, and the path is valid, a public
   *     facing URL will be returned.
   *   - If the $uri is prefixed with vendor://, and the path is invalid, NULL
   *     is returned.
   *   - If $uri is not prefixed with vendor://, the passed $uri is returned
   *     unaltered.
   *
   * @deprecated in vendor_stream_wrapper:2.0.5 and is removed from
   *   vendor_stream_wrapper:3.0.0. Use createUrlFromUri() instead.
   *
   * @see https://www.drupal.org/project/vendor_stream_wrapper/issues/3452824
   */
  public function creatUrlFromUri(string $uri, bool $include_base_url = TRUE): ?string;

  /**
   * Creates a public facing URL from URIs with the vendor:// schema.
   *
   * @param string $uri
   *   The vendor:// prefixed URI to be converted to a public facing URL.
   * @param bool $include_base_url
   *   (Optional) Whether the URL should include the base URL. Defaults to TRUE.
   *
   * @return string|null
   *   - If the $uri is prefixed with vendor://, and the path is valid, a public
   *     facing URL will be returned.
   *   - If the $uri is prefixed with vendor://, and the path is invalid, NULL
   *     is returned.
   *   - If $uri is not prefixed with vendor://, the passed $uri is returned
   *     unaltered.
   */
  public function createUrlFromUri(string $uri, bool $include_base_url = TRUE): ?string;

}
