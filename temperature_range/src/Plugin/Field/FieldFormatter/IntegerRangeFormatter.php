<?php

/**
 * @file
 * Contains \Drupal\temperarture_range\Plugin\Field\FieldFormatter\IntegerRangeFormatter.
 */

namespace Drupal\temperature_range\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface; 

/**
 * Plugin implementation of the 'integer_range' formatter.
 *
 * @FieldFormatter(
 *   id = "integer_range",
 *   label = @Translation("Integer Range"),
 *   field_types = {
 *     "integer_range"
 *   }
 * )
 */

class IntegerRangeFormatter extends RangeFormatterBase {

  /**
   * {@inheritdoc}
   */
  protected function numberFormat($number) {
    return number_format($number, 0, '', $this->getSetting('thousand_separator'));
  }

}
