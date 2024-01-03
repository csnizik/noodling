<?php

namespace Drupal\cig_pods_csc\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the MinMax constraint.
 */
class MinMaxConstraintValidator extends ConstraintValidator {
  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint) {

    foreach ($items as $delta => $item) {
      // @DCG Validate the item here.
      $val = $item->getValue()['numerator'];
      if ($val < $constraint->min or $val > $constraint->max) {
        $this->context->buildViolation($constraint->errorMessage)
          ->setParameter('%field_name', $item->getFieldDefinition()->getLabel())
          ->setParameter('%min', $constraint->min)
          ->setParameter('%max', $constraint->max)
          ->atPath($delta)
          ->addViolation();
      }
    }

  }

}
