<?php

namespace Drupal\cig_pods_csc\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the Required constraint.
 */
class RequiredConstraintValidator extends ConstraintValidator {
  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint) {

    foreach ($items as $delta => $item) {
      // @DCG Validate the item here.
      $val = $item->getValue();
      if ($val == NULL or $val == "" or $val == []) {
        $this->context->buildViolation($constraint->errorMessage)
          ->setParameter('%field_name', $item->getFieldDefinition()->getLabel())
          ->atPath($delta)
          ->addViolation();
      }
    }

  }

}
