<?php

namespace Drupal\vendor_stream_wrapper\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\vendor_stream_wrapper\Service\VendorStreamWrapperManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mime\MimeTypeGuesserInterface;

/**
 * Vendor Stream Wrapper file controller.
 *
 * Sets up serving of files from the vendor directory, using the vendor://
 * stream wrapper.
 */
class VendorFileDownloadController extends ControllerBase implements VendorFileDownloadControllerInterface {

  /**
   * Service providing management functionality for vendor stream wrappers.
   *
   * @var \Drupal\vendor_stream_wrapper\Service\VendorStreamWrapperManagerInterface
   */
  protected $vendorStreamWrapperManager;

  /**
   * The MIME type guesser.
   *
   * @var \Symfony\Component\Mime\MimeTypeGuesserInterface
   */
  protected $mimeTypeGuesser;

  /**
   * The logger object, that writes to the specific channel of this module.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Creates a new VendorFileDownloadController instance.
   *
   * @param \Drupal\vendor_stream_wrapper\Service\VendorStreamWrapperManagerInterface $vendor_stream_wrapper_manager
   *   Service providing management functionality for vendor stream wrappers.
   * @param \Symfony\Component\Mime\MimeTypeGuesserInterface $mimeTypeGuesser
   *   The MIME type guesser.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger object, that writes to the specific channel of this module.
   */
  public function __construct(VendorStreamWrapperManagerInterface $vendor_stream_wrapper_manager, MimeTypeGuesserInterface $mimeTypeGuesser, LoggerInterface $logger) {
    $this->vendorStreamWrapperManager = $vendor_stream_wrapper_manager;
    $this->mimeTypeGuesser = $mimeTypeGuesser;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): VendorFileDownloadController {
    return new static(
      $container->get('vendor_stream_wrapper.manager'),
      $container->get('file.mime_type.guesser'),
      $container->get('logger.channel.vendor_stream_wrapper')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function download(Request $request): BinaryFileResponse {
    $filepath = str_replace(':', '/', $request->get('filepath'));

    // Check if the file path matches a whitelist pattern, this to ensure only
    // explicitly allowed vendor files can be accessed/downloaded.
    if (!$this->vendorStreamWrapperManager->isSafeListed($filepath)) {
      throw new AccessDeniedHttpException();
    }

    $scheme = 'vendor';
    $uri = $scheme . '://' . $filepath;

    $mime_type = '';
    try {
      $mime_type = $this->mimeTypeGuesser->guessMimeType($uri);
    }
    catch (\Exception $e) {
      $this->logger->error('Vendor file download error: %message', ['%message' => $e->getMessage()]);
    }

    if (!empty($mime_type)) {
      $headers = [
        'Content-Type' => $mime_type,
      ];

      try {
        return new BinaryFileResponse($uri, 200, $headers, TRUE);
      }
      catch (FileNotFoundException $e) {
        throw new NotFoundHttpException();
      }
    }

    throw new NotFoundHttpException();
  }

}
