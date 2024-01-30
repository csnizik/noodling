<?php

namespace Drupal\cig_pods_csc\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides the Sum constraint.
 *
 * @Constraint(
 *   id = "Sum",
 *   label = @Translation("Sum", context = "Validation"),
 * )
 *
 * @DCG
 * To apply this constraint on third party entity types implement either
 * hook_entity_base_field_info_alter() or hook_entity_bundle_field_info_alter().
 */
class SumConstraint extends Constraint {
  public $fields;
  public $comp;
  public $bound;

  public $errorMessage = 'Sum of fields %fields does not match %bound';

}
