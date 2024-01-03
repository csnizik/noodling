<?php

namespace Drupal\cig_pods_csc\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the Sum constraint.
 */
class SumConstraintValidator extends ConstraintValidator {
  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint) {
    $entity = $items->getEntity();

    $sum = 0;
    foreach ($constraint->fields as $field) {
      $sum += $entity->get($field)->getValue()[0]["numerator"];
    }
    
    $bound = $constraint->bound;
    if (is_string($bound)) {
      $bound = $entity->get($bound)->getValue()[0]["numerator"];
    }

    switch ($constraint->comp){
      case "==":
        $valid = ($sum == $bound);
        break;
      case "<=":
        $valid = ($sum <= $bound);
        break;
      case "<":
        $valid = ($sum < $bound);
        break;
      case ">":
        $valid = ($sum > $bound);
        break;
      case ">=":
        $valid = ($sum >= $bound);
        break;
    }

    foreach ($items as $delta => $item) {
      if (!$valid) {
        $this->context->buildViolation($constraint->errorMessage)
          ->atPath($delta)
          ->addViolation();
      }
    }
  }
}
