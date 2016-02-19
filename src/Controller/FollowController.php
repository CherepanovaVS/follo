<?php

/**
 * @file
 * Contains \Drupal\follow\Controller\FollowController.
 */

namespace Drupal\follow\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Default controller for the follow module.
 */
class FollowController extends ControllerBase {

  public function follow_css() {
    $directory_path = \Drupal::service('stream_wrapper_manager')->getViaScheme('public')->getDirectoryPath();
    $destination = $directory_path . '/css/follow.css';
    if ($destination == follow_save_css()) {
      new BinaryFileResponse($destination, 200, array('Content-Type' => 'text/css', 'Content-Length' => filesize($destination)));
    }
    else {
      \Drupal::logger('follow')->notice('Unable to generate the Follow CSS located at %path.', array('%path' => $destination));
      throw new ServiceUnavailableHttpException(0, t('Error generating CSS.'), 'Status', '500 Internal Server Error');
    }
  }

  /**
   * Access callback for user follow links editing.
   */
  function follow_links_user_access($user) {
    return AccessResult::allowedIf(((($this->currentUser()->uid == $user) && $this->currentUser()->hasPermission('edit own follow //links')) || $this->currentUser()->hasPermission('edit any user follow links')) && $user > 0);
  }
}
