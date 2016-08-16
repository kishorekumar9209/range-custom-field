<?php

/**
 * @file
 * Contains \Drupal\temperature_range\Plugin\field\formatter\RangeFormatterBase.
 */

namespace Drupal\temperature_range\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldFilteredMarkup;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Parent plugin for decimal and integer range formatters.
 */
abstract class RangeFormatterBase extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'range_separator' => '-',
      'thousand_separator' => '',
      'range_combine' => TRUE,
        ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['range_separator'] = array(
      '#type' => 'textfield',
      '#title' => t('Range separator.'),
      '#default_value' => $this->getSetting('range_separator'),
      '#weight' => 0,
    );
    $options = array(
      '' => t('- None -'),
      '.' => t('Decimal point'),
      ',' => t('Comma'),
      ' ' => t('Space'),
      chr(8201) => t('Thin space'),
      "'" => t('Apostrophe'),
    );
    $elements['thousand_separator'] = array(
      '#type' => 'select',
      '#title' => t('Thousand marker'),
      '#options' => $options,
      '#default_value' => $this->getSetting('thousand_separator'),
      '#weight' => 1,
    );
    $elements['range_combine'] = array(
      '#type' => 'checkbox',
      '#title' => t('Combine equivalent values'),
      '#description' => t('If the FROM and TO values are equal, combine the display into a single value.'),
      '#default_value' => $this->getSetting('range_combine'),
      '#weight' => 10,
    );
    /*    $elements['from_prefix_suffix'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display <em>FROM value</em> prefix and suffix'),
      '#default_value' => $this->getSetting('from_prefix_suffix'),
      '#weight' => 11,
      );
      $elements['to_prefix_suffix'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display <em>TO value</em> prefix and suffix'),
      '#default_value' => $this->getSetting('to_prefix_suffix'),
      '#weight' => 12,
      );
     */
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $from_value = $this->numberFormat(1234.1234567890);
    $to_value = $this->numberFormat(4321.0987654321);

    $summary[] = $from_value . $this->getSetting('range_separator') . $to_value;
    if ($this->getSetting('range_combine')) {
      $summary[] = t('Equivalent values will be combined into a single value.');
    }
    /* if ($this->getSetting('from_prefix_suffix')) {
      $summary[] = t('Display with <em>FROM value</em> prefix and suffix.');
      }
      if ($this->getSetting('to_prefix_suffix')) {
      $summary[] = t('Display with <em>TO value</em> prefix and suffix.');
      }
     */
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    foreach ($items as $delta => $item) {
      $from_value = $this->numberFormat($item->from);
      $to_value = $this->numberFormat($item->to);
      $unit_value = $item->unit;
      // Combine values if they are equal.
      if ($this->getSetting('range_combine') && $from_value === $to_value) {
        $output = $this->viewElementCombined($from_value,$unit_value);
      }
      else {
        $output = $this->viewElementSeparate($from_value, $to_value, $item->unit);
      }

      $elements[$delta] = array('#markup' => $output);
    }

    return $elements;
  }

  /**
   * Helper method. Creates the combined value and returns field markup.
   *
   * FROM and TO might have different prefixes/suffixes.
   * Code below decides which one to use, based on the following:
   *   1. If both are disabled - show naked value
   *   2. If either FROM or TO are enabled - show prefix/suffix of the
   *      enabled one
   *   3. If both are enabled, show prefix from FROM and suffix from TO
   *
   * @param string $value
   *   Field value.
   *
   * @return string
   *   Field markup.
   */
  protected function viewElementCombined($value,$unit_value) {
    /* $from_prefix_suffix = !empty($this->getSetting('from_prefix_suffix'));
      $to_prefix_suffix = !empty($this->getSetting('to_prefix_suffix'));

      // Option #1: both are disabled.
      if (empty($from_prefix_suffix) && empty($to_prefix_suffix)) {
      return $value;
      }
      // Option #2a: FROM is enabled.
      elseif (!empty($from_prefix_suffix) && empty($to_prefix_suffix)) {
      return $this->viewElementPrefixSuffix($value, TRUE, $this->getFieldSetting('from'));
      }
      // Option #2b: TO is enabled.
      elseif (empty($from_prefix_suffix) && !empty($to_prefix_suffix)) {
      return $this->viewElementPrefixSuffix($value, TRUE, $this->getFieldSetting('unit'));
      }
      // Option #3: both are enabled.
      else {
      return $this->viewElementPrefixSuffix($value, TRUE, array('prefix' => $this->getFieldSetting('from')['prefix'], 'suffix' => $this->getFieldSetting('to')['prefix']));
      } */
    return $value . $unit_value;
  }

  /**
   * Helper method. Creates and returns field markup for separate values.
   *
   * @param string $from_value
   *   Field FROM value.
   * @param string $to_value
   *   Field TO value.
   *
   * @return string
   *  Field markup.
   */
  protected function viewElementSeparate($from_value, $to_value, $unit_value) {
    /*  $from = $this->viewElementPrefixSuffix($from_value, $this->getSetting('from_prefix_suffix'), $this->getFieldSetting('from'));
      $to = $this->viewElementPrefixSuffix($to_value, $this->getSetting('to_prefix_suffix'), $this->getFieldSetting('to'));
      $unit = $this->viewElementPrefixSuffix($unit_value, $this->getSetting('to_prefix_suffix'), $this->getFieldSetting('unit'));
      dsm($unit); */
    return $from_value . $this->getSetting('range_separator') . $to_value . $unit_value;
  }

  /**
   * Helper method. Adds prefix and suffix to the given range field value.
   *
   * @param string $value
   *   FROM/TO range field value.
   * @param boolean $display_prefix_suffix
   *   Whether to add suffix/prefix or not.
   * @param array $settings
   *   Field instance FROM/TO value settings.
   *
   * @return string
   *   Range field value with added prefix/suffix.
   *//*
    protected function viewElementPrefixSuffix($value, $display_prefix_suffix, array $settings) {
    if ($display_prefix_suffix) {
    $prefix = !empty($settings['prefix']) ? FieldFilteredMarkup::create($settings['prefix']) : '';
    $suffix = !empty($settings['suffix']) ? FieldFilteredMarkup::create($settings['suffix']) : '';
    return $prefix . $value . $suffix;
    }

    return $value;
    } */

  /**
   * Formats a number.
   *
   * @param mixed $number
   *   The numeric value.
   *
   * @return string
   *   The formatted number.
   */
  abstract protected function numberFormat($number);
}
