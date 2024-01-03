<?php

namespace Drupal\cig_pods_csc\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides the MinMax constraint.
 *
 * @Constraint(
 *   id = "MinMax",
 *   label = @Translation("Min Max bounds", context = "Validation"),
 * )
 *
 * @DCG
 * To apply this constraint on third party entity types implement either
 * hook_entity_base_field_info_alter() or hook_entity_bundle_field_info_alter().
 */
class MinMaxConstraint extends Constraint {
  public $min;
  public $max;

  public $errorMessage = '%field_name must be between %min and %max';

}
