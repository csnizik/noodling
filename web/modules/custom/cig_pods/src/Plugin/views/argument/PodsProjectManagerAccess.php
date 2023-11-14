<?php

namespace Drupal\cig_pods\Plugin\views\argument;

use Drupal\cig_pods\ProjectAccessControlHandler;
use Drupal\views\Plugin\views\argument\ArgumentPluginBase;

/**
 * Filter out assets that are not part of a project the user is assigned to.
 *
 * @ViewsArgument("pods_project_manager_access")
 */
class PodsProjectManagerAccess extends ArgumentPluginBase {

  /**
   * {@inheritdoc}
   */
  public function query($group_by = FALSE) {

    // Delegate to the helper method in ProjectAccessControlHandler.
    ProjectAccessControlHandler::viewsArgumentQueryAlter($this, TRUE, TRUE);
  }

}
