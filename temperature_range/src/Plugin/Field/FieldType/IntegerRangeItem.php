<?php

/**
 * @file
 * Contains \Drupal\temperature_range\Plugin\Field\FieldType\IntegerRangeItem.
 */

namespace Drupal\temperature_range\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;


/**
 * Plugin implementation of the 'integer_range' field type.
 * 
 * @FieldType(
 *   id = "integer_range",
 *   label = @Translation("Range (integer)"),
 *   description = @Translation("This field stores an integer range in the database."),
 *   category = @Translation("Numeric range"),
 *   default_widget = "temperature_range",
 *   default_formatter = "integer_range"
 * )
 */

class IntegerRangeItem extends ItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    return static::propertyDefinitionsByType('string');
  }

  /**
   * {@inheritdoc}
   */
  protected static function getColumnSpecification(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'type' => 'varchar',
      'length' => 12
    );
  }

}