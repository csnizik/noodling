<?php

namespace Drupal\cig_pods\Plugin\views\argument;

use Drupal\cig_pods\ProjectAccessControlHandler;
use Drupal\views\Plugin\views\argument\ArgumentPluginBase;

/**
 * Filter out awards that a user is not assigned as a manager to.
 *
 * @ViewsArgument("pods_project_manager_award_access")
 */
class PodsProjectManagerAwardAccess extends ArgumentPluginBase {

  /**
   * {@inheritdoc}
   */
  public function query($group_by = FALSE) {

    // Delegate to the helper method in ProjectAccessControlHandler.
    ProjectAccessControlHandler::viewsArgumentQueryAlter($this, TRUE, FALSE);
  }

}
