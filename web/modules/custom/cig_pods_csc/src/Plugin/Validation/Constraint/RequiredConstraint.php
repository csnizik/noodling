<?php

namespace Drupal\cig_pods_csc\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides a Required constraint.
 *
 * @Constraint(
 *   id = "Required",
 *   label = @Translation("Required Field", context = "Validation"),
 * )
 *
 * @DCG
 * To apply this constraint on third party entity types implement either
 * hook_entity_base_field_info_alter() or hook_entity_bundle_field_info_alter().
 */
class RequiredConstraint extends Constraint {
  public $errorMessage = '%field_name must not be blank';

}
