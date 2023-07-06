<?php

namespace MyStripe\Util;

use MyStripe\StripeObject;

abstract class Util
{
    private static $isMbstringAvailable = null;
    private static $isHashEqualsAvailable = null;

    /**
     * Whether the provided array (or other) is a list rather than a dictionary.
     * A list is defined as an array for which all the keys are consecutive
     * integers starting at 0. Empty arrays are considered to be lists.
     *
     * @param array|mixed $array
     *
     * @return bool true if the given object is a list
     */
    public static function isList($array)
    {
        if (!\is_array($array)) {
            return false;
        }
        if ($array === []) {
            return true;
        }
        if (\array_keys($array) !== \range(0, \count($array) - 1)) {
            return false;
        }

        return true;
    }

    /**
     * Converts a response from the Stripe API to the corresponding PHP object.
     *
     * @param array $resp the response from the Stripe API
     * @param array $opts
     *
     * @return array|StripeObject
     */
    public static function convertToStripeObject($resp, $opts)
    {
        $types = [
            // data structures
            \MyStripe\Collection::OBJECT_NAME => \MyStripe\Collection::class,

            // business objects
            \MyStripe\Account::OBJECT_NAME => \MyStripe\Account::class,
            \MyStripe\AccountLink::OBJECT_NAME => \MyStripe\AccountLink::class,
            \MyStripe\AlipayAccount::OBJECT_NAME => \MyStripe\AlipayAccount::class,
            \MyStripe\ApplePayDomain::OBJECT_NAME => \MyStripe\ApplePayDomain::class,
            \MyStripe\ApplicationFee::OBJECT_NAME => \MyStripe\ApplicationFee::class,
            \MyStripe\ApplicationFeeRefund::OBJECT_NAME => \MyStripe\ApplicationFeeRefund::class,
            \MyStripe\Balance::OBJECT_NAME => \MyStripe\Balance::class,
            \MyStripe\BalanceTransaction::OBJECT_NAME => \MyStripe\BalanceTransaction::class,
            \MyStripe\BankAccount::OBJECT_NAME => \MyStripe\BankAccount::class,
            \MyStripe\BitcoinReceiver::OBJECT_NAME => \MyStripe\BitcoinReceiver::class,
            \MyStripe\BitcoinTransaction::OBJECT_NAME => \MyStripe\BitcoinTransaction::class,
            \MyStripe\Capability::OBJECT_NAME => \MyStripe\Capability::class,
            \MyStripe\Card::OBJECT_NAME => \MyStripe\Card::class,
            \MyStripe\Charge::OBJECT_NAME => \MyStripe\Charge::class,
            \MyStripe\Checkout\Session::OBJECT_NAME => \MyStripe\Checkout\Session::class,
            \MyStripe\CountrySpec::OBJECT_NAME => \MyStripe\CountrySpec::class,
            \MyStripe\Coupon::OBJECT_NAME => \MyStripe\Coupon::class,
            \MyStripe\CreditNote::OBJECT_NAME => \MyStripe\CreditNote::class,
            \MyStripe\CreditNoteLineItem::OBJECT_NAME => \MyStripe\CreditNoteLineItem::class,
            \MyStripe\Customer::OBJECT_NAME => \MyStripe\Customer::class,
            \MyStripe\CustomerBalanceTransaction::OBJECT_NAME => \MyStripe\CustomerBalanceTransaction::class,
            \MyStripe\Discount::OBJECT_NAME => \MyStripe\Discount::class,
            \MyStripe\Dispute::OBJECT_NAME => \MyStripe\Dispute::class,
            \MyStripe\EphemeralKey::OBJECT_NAME => \MyStripe\EphemeralKey::class,
            \MyStripe\Event::OBJECT_NAME => \MyStripe\Event::class,
            \MyStripe\ExchangeRate::OBJECT_NAME => \MyStripe\ExchangeRate::class,
            \MyStripe\File::OBJECT_NAME => \MyStripe\File::class,
            \MyStripe\File::OBJECT_NAME_ALT => \MyStripe\File::class,
            \MyStripe\FileLink::OBJECT_NAME => \MyStripe\FileLink::class,
            \MyStripe\Invoice::OBJECT_NAME => \MyStripe\Invoice::class,
            \MyStripe\InvoiceItem::OBJECT_NAME => \MyStripe\InvoiceItem::class,
            \MyStripe\InvoiceLineItem::OBJECT_NAME => \MyStripe\InvoiceLineItem::class,
            \MyStripe\Issuing\Authorization::OBJECT_NAME => \MyStripe\Issuing\Authorization::class,
            \MyStripe\Issuing\Card::OBJECT_NAME => \MyStripe\Issuing\Card::class,
            \MyStripe\Issuing\CardDetails::OBJECT_NAME => \MyStripe\Issuing\CardDetails::class,
            \MyStripe\Issuing\Cardholder::OBJECT_NAME => \MyStripe\Issuing\Cardholder::class,
            \MyStripe\Issuing\Dispute::OBJECT_NAME => \MyStripe\Issuing\Dispute::class,
            \MyStripe\Issuing\Transaction::OBJECT_NAME => \MyStripe\Issuing\Transaction::class,
            \MyStripe\LoginLink::OBJECT_NAME => \MyStripe\LoginLink::class,
            \MyStripe\Mandate::OBJECT_NAME => \MyStripe\Mandate::class,
            \MyStripe\Order::OBJECT_NAME => \MyStripe\Order::class,
            \MyStripe\OrderItem::OBJECT_NAME => \MyStripe\OrderItem::class,
            \MyStripe\OrderReturn::OBJECT_NAME => \MyStripe\OrderReturn::class,
            \MyStripe\PaymentIntent::OBJECT_NAME => \MyStripe\PaymentIntent::class,
            \MyStripe\PaymentMethod::OBJECT_NAME => \MyStripe\PaymentMethod::class,
            \MyStripe\Payout::OBJECT_NAME => \MyStripe\Payout::class,
            \MyStripe\Person::OBJECT_NAME => \MyStripe\Person::class,
            \MyStripe\Plan::OBJECT_NAME => \MyStripe\Plan::class,
            \MyStripe\Product::OBJECT_NAME => \MyStripe\Product::class,
            \MyStripe\Radar\EarlyFraudWarning::OBJECT_NAME => \MyStripe\Radar\EarlyFraudWarning::class,
            \MyStripe\Radar\ValueList::OBJECT_NAME => \MyStripe\Radar\ValueList::class,
            \MyStripe\Radar\ValueListItem::OBJECT_NAME => \MyStripe\Radar\ValueListItem::class,
            \MyStripe\Recipient::OBJECT_NAME => \MyStripe\Recipient::class,
            \MyStripe\RecipientTransfer::OBJECT_NAME => \MyStripe\RecipientTransfer::class,
            \MyStripe\Refund::OBJECT_NAME => \MyStripe\Refund::class,
            \MyStripe\Reporting\ReportRun::OBJECT_NAME => \MyStripe\Reporting\ReportRun::class,
            \MyStripe\Reporting\ReportType::OBJECT_NAME => \MyStripe\Reporting\ReportType::class,
            \MyStripe\Review::OBJECT_NAME => \MyStripe\Review::class,
            \MyStripe\SetupIntent::OBJECT_NAME => \MyStripe\SetupIntent::class,
            \MyStripe\Sigma\ScheduledQueryRun::OBJECT_NAME => \MyStripe\Sigma\ScheduledQueryRun::class,
            \MyStripe\SKU::OBJECT_NAME => \MyStripe\SKU::class,
            \MyStripe\Source::OBJECT_NAME => \MyStripe\Source::class,
            \MyStripe\SourceTransaction::OBJECT_NAME => \MyStripe\SourceTransaction::class,
            \MyStripe\Subscription::OBJECT_NAME => \MyStripe\Subscription::class,
            \MyStripe\SubscriptionItem::OBJECT_NAME => \MyStripe\SubscriptionItem::class,
            \MyStripe\SubscriptionSchedule::OBJECT_NAME => \MyStripe\SubscriptionSchedule::class,
            \MyStripe\TaxId::OBJECT_NAME => \MyStripe\TaxId::class,
            \MyStripe\TaxRate::OBJECT_NAME => \MyStripe\TaxRate::class,
            \MyStripe\ThreeDSecure::OBJECT_NAME => \MyStripe\ThreeDSecure::class,
            \MyStripe\Terminal\ConnectionToken::OBJECT_NAME => \MyStripe\Terminal\ConnectionToken::class,
            \MyStripe\Terminal\Location::OBJECT_NAME => \MyStripe\Terminal\Location::class,
            \MyStripe\Terminal\Reader::OBJECT_NAME => \MyStripe\Terminal\Reader::class,
            \MyStripe\Token::OBJECT_NAME => \MyStripe\Token::class,
            \MyStripe\Topup::OBJECT_NAME => \MyStripe\Topup::class,
            \MyStripe\Transfer::OBJECT_NAME => \MyStripe\Transfer::class,
            \MyStripe\TransferReversal::OBJECT_NAME => \MyStripe\TransferReversal::class,
            \MyStripe\UsageRecord::OBJECT_NAME => \MyStripe\UsageRecord::class,
            \MyStripe\UsageRecordSummary::OBJECT_NAME => \MyStripe\UsageRecordSummary::class,
            \MyStripe\WebhookEndpoint::OBJECT_NAME => \MyStripe\WebhookEndpoint::class,
        ];
        if (self::isList($resp)) {
            $mapped = [];
            foreach ($resp as $i) {
                \array_push($mapped, self::convertToStripeObject($i, $opts));
            }

            return $mapped;
        }
        if (\is_array($resp)) {
            if (isset($resp['object']) && \is_string($resp['object']) && isset($types[$resp['object']])) {
                $class = $types[$resp['object']];
            } else {
                $class = \MyStripe\StripeObject::class;
            }

            return $class::constructFrom($resp, $opts);
        }

        return $resp;
    }

    /**
     * @param mixed|string $value a string to UTF8-encode
     *
     * @return mixed|string the UTF8-encoded string, or the object passed in if
     *    it wasn't a string
     */
    public static function utf8($value)
    {
        if (null === self::$isMbstringAvailable) {
            self::$isMbstringAvailable = \function_exists('mb_detect_encoding');

            if (!self::$isMbstringAvailable) {
                \trigger_error('It looks like the mbstring extension is not enabled. ' .
                    'UTF-8 strings will not properly be encoded. Ask your system ' .
                    'administrator to enable the mbstring extension, or write to ' .
                    'support@stripe.com if you have any questions.', \E_USER_WARNING);
            }
        }

        if (\is_string($value) && self::$isMbstringAvailable && 'UTF-8' !== \mb_detect_encoding($value, 'UTF-8', true)) {
            return \utf8_encode($value);
        }

        return $value;
    }

    /**
     * Compares two strings for equality. The time taken is independent of the
     * number of characters that match.
     *
     * @param string $a one of the strings to compare
     * @param string $b the other string to compare
     *
     * @return bool true if the strings are equal, false otherwise
     */
    public static function secureCompare($a, $b)
    {
        if (null === self::$isHashEqualsAvailable) {
            self::$isHashEqualsAvailable = \function_exists('hash_equals');
        }

        if (self::$isHashEqualsAvailable) {
            return \hash_equals($a, $b);
        }
        if (\strlen($a) !== \strlen($b)) {
            return false;
        }

        $result = 0;
        for ($i = 0; $i < \strlen($a); ++$i) {
            $result |= \ord($a[$i]) ^ \ord($b[$i]);
        }

        return 0 === $result;
    }

    /**
     * Recursively goes through an array of parameters. If a parameter is an instance of
     * ApiResource, then it is replaced by the resource's ID.
     * Also clears out null values.
     *
     * @param mixed $h
     *
     * @return mixed
     */
    public static function objectsToIds($h)
    {
        if ($h instanceof \MyStripe\ApiResource) {
            return $h->id;
        }
        if (static::isList($h)) {
            $results = [];
            foreach ($h as $v) {
                \array_push($results, static::objectsToIds($v));
            }

            return $results;
        }
        if (\is_array($h)) {
            $results = [];
            foreach ($h as $k => $v) {
                if (null === $v) {
                    continue;
                }
                $results[$k] = static::objectsToIds($v);
            }

            return $results;
        }

        return $h;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public static function encodeParameters($params)
    {
        $flattenedParams = self::flattenParams($params);
        $pieces = [];
        foreach ($flattenedParams as $param) {
            list($k, $v) = $param;
            \array_push($pieces, self::urlEncode($k) . '=' . self::urlEncode($v));
        }

        return \implode('&', $pieces);
    }

    /**
     * @param array $params
     * @param null|string $parentKey
     *
     * @return array
     */
    public static function flattenParams($params, $parentKey = null)
    {
        $result = [];

        foreach ($params as $key => $value) {
            $calculatedKey = $parentKey ? "{$parentKey}[{$key}]" : $key;

            if (self::isList($value)) {
                $result = \array_merge($result, self::flattenParamsList($value, $calculatedKey));
            } elseif (\is_array($value)) {
                $result = \array_merge($result, self::flattenParams($value, $calculatedKey));
            } else {
                \array_push($result, [$calculatedKey, $value]);
            }
        }

        return $result;
    }

    /**
     * @param array $value
     * @param string $calculatedKey
     *
     * @return array
     */
    public static function flattenParamsList($value, $calculatedKey)
    {
        $result = [];

        foreach ($value as $i => $elem) {
            if (self::isList($elem)) {
                $result = \array_merge($result, self::flattenParamsList($elem, $calculatedKey));
            } elseif (\is_array($elem)) {
                $result = \array_merge($result, self::flattenParams($elem, "{$calculatedKey}[{$i}]"));
            } else {
                \array_push($result, ["{$calculatedKey}[{$i}]", $elem]);
            }
        }

        return $result;
    }

    /**
     * @param string $key a string to URL-encode
     *
     * @return string the URL-encoded string
     */
    public static function urlEncode($key)
    {
        $s = \urlencode((string) $key);

        // Don't use strict form encoding by changing the square bracket control
        // characters back to their literals. This is fine by the server, and
        // makes these parameter strings easier to read.
        $s = \str_replace('%5B', '[', $s);

        return \str_replace('%5D', ']', $s);
    }

    public static function normalizeId($id)
    {
        if (\is_array($id)) {
            $params = $id;
            $id = $params['id'];
            unset($params['id']);
        } else {
            $params = [];
        }

        return [$id, $params];
    }

    /**
     * Returns UNIX timestamp in milliseconds.
     *
     * @return int current time in millis
     */
    public static function currentTimeMillis()
    {
        return (int) \round(\microtime(true) * 1000);
    }
}
