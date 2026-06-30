<?php

namespace Drupal\file_mime_validator\Service;

use Drupal\file\Entity\File;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\StringTranslation\TranslationInterface;

/**
 * Class File Mime Validator.
 *
 *  @package file_mime_validator
 */
class FileMimeValidator {
  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;
  /**
   * Logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerChannelFactory;
  /**
   * String translation service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected $translator;

  /**
   * Constructs the file upload secure validation service.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory service object.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory service object.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translator
   *   The string translation service object.
   */
  public function __construct(ConfigFactoryInterface $config_factory,
    LoggerChannelFactoryInterface $logger_factory,
    TranslationInterface $translator) {
    $this->configFactory = $config_factory;
    $this->loggerChannelFactory = $logger_factory;
    $this->translator = $translator;
  }

  /**
   * Check Real Mime function.
   *
   * @param \Drupal\file\Entity\File $file
   *   File.
   *
   * @return array
   *   Return Response Array.
   */
  public function checkRealMime(File $file) {
    // Get mime type from file.
    $mimeByFilename = self::getFileType($file->getMimeType());
    // Get mime type from fileinfo.
    $mimeByFileinfo = (substr($file->getFileUri(), 0, 5) == '/tmp/') ? (new FileinfoMimeTypeGuesser())->guessMimeType($file->getFileUri()) : 'NOTTMPDIR';

    // If fileinfo agrees with the file's extension, exit.
    if ($mimeByFilename === self::getFileType($mimeByFileinfo)) {
      return [];
    }
    // If no file type is found.
    elseif ($mimeByFilename == "no file type found") {
      // Logger.
      $this->loggerChannelFactory->get('file_mime_validator')->error("No file type found for '@extension'", [
        '@extension' => $file->getMimeType(),
      ]);
    }
    // If not falling in this case, it means its finally get uploaded to S3.
    elseif ($mimeByFileinfo != 'NOTTMPDIR') {
      // Logger.
      $this->loggerChannelFactory->get('file_mime_validator')
        ->error("Error while uploading file: Guessed type '%mime_by_fileinfo' but by filename it seems to be '%mime_by_filename'", [
          '%mime_by_fileinfo' => $mimeByFileinfo,
          '%mime_by_filename' => $file->getMimeType(),
        ]);
      // Error.
      return [
        new TranslatableMarkup('There was a problem with this file. The uploaded file must be of type @extension but the real seems to be @real_extension.', [
          '@extension' => $mimeByFilename,
          '@real_extension' => (self::getFileType($mimeByFileinfo) == "no file type found") ? $mimeByFileinfo : self::getFileType($mimeByFileinfo),
        ], [], $this->translator),
      ];
    }
    // No errors.
    else {
      return [];
    }
  }

  /**
   * Check  for Parent File Type.
   *
   * @param string $mime
   *   Mime Type.
   *
   * @return string
   *   Returns parent type, if found.
   */
  public function getFileType(string $mime) {
    $return = "no file type found";
    $config = $this->configFactory->get('file_mime_validator.settings');
    $fileTypes = [
      'text' => explode(",", $config->get('file_mime_validator_text')),
      'image' => explode(",", $config->get('file_mime_validator_image')),
      'compression' => explode(",", $config->get('file_mime_validator_compression')),
      'audio' => explode(",", $config->get('file_mime_validator_audio')),
      'video' => explode(",", $config->get('file_mime_validator_video')),
    ];
    foreach ($fileTypes as $key => $mimeTypes) {
      if (in_array($mime, $mimeTypes)) {
        $return = $key;
      }
    }
    return $return;
  }

}
