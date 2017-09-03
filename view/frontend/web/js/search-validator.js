/**
 * @author Miranda Johnson
 * @package MirandaJohnson\ProductsInRange
 */
require([
'jquery',
'mage/validation',
'mage/translate'
], function($) {
  "use strict";
  $.extend(true, $.mage, {
    /**
     * Checks if {value} is greater than {from}
     * and less than or equal to {to}
     * @param {string} value
     * @param {string} from
     * @param {string} to
     * @returns {boolean}
     */
    isBetweenExclusive: function (value, from, to) {
        return ($.mage.isEmpty(from) || value > $.mage.parseNumber(from)) &&
            ($.mage.isEmpty(to) || value <= $.mage.parseNumber(to));
    }
  });

  /**
   * Checks if {v} is greater than the value of #min_price
   * and less than or equal to 5 times that value
   * @param {string} v
   * @returns {boolean}
   */
  $.validator.addMethod('validate-price-range', function(v) {
      var result = false;
      var minPrice = $.mage.parseNumber($('#min_price').val());
      if (!minPrice) {
        this.priceRangeErrorMessage = $.mage.__('The "minimum price" is required.');
        return result;
      }
      var currentValue = $.mage.parseNumber(v),
          maxPrice = minPrice * 5;
      result = $.mage.isBetweenExclusive(currentValue, minPrice, maxPrice);
      if (result == false) {
        this.priceRangeErrorMessage = $.mage.__(
            'The value must be greater than %1 and less than or equal to %2.'
        ).replace('%1', minPrice).replace('%2', maxPrice);
      }
      return result;
  }, function() {
      return this.priceRangeErrorMessage;
  });

  /**
   * Checks if {v} matches one of "asc" or "desc"
   * @param {string} v
   * @returns {boolean}
   */
  $.validator.addMethod('validate-sort-by', function(v) {
      return v.match(/^(asc|desc)$/i);
  }, $.mage.__('Invalid "sort by" value.'));
});
