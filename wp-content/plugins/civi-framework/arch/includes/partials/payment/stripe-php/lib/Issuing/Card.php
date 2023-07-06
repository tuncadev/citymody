<?php

namespace MyStripe\Issuing;

/**
 * You can <a href="https://stripe.com/docs/issuing/cards">create physical or
 * virtual cards</a> that are issued to cardholders.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property \MyStripe\StripeObject $authorization_controls
 * @property string $brand The brand of the card.
 * @property null|\MyStripe\Issuing\Cardholder $cardholder The <a href="https://stripe.com/docs/api#issuing_cardholder_object">Cardholder</a> object to which the card belongs.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property string $currency Three-letter <a href="https://www.iso.org/iso-4217-currency-codes.html">ISO currency code</a>, in lowercase. Must be a <a href="https://stripe.com/docs/currencies">supported currency</a>.
 * @property int $exp_month The expiration month of the card.
 * @property int $exp_year The expiration year of the card.
 * @property string $last4 The last 4 digits of the card number.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property \MyStripe\StripeObject $metadata Set of key-value pairs that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property string $name The name of the cardholder, printed on the card.
 * @property null|\MyStripe\StripeObject $pin Metadata about the PIN on the card.
 * @property null|string|\MyStripe\Issuing\Card $replaced_by The latest card that replaces this card, if any.
 * @property null|string|\MyStripe\Issuing\Card $replacement_for The card this card replaces, if any.
 * @property null|string $replacement_reason The reason why the previous card needed to be replaced.
 * @property null|\MyStripe\StripeObject $shipping Where and how the card will be shipped.
 * @property string $status Whether authorizations can be approved on this card.
 * @property string $type The type of the card.
 */
class Card extends \MyStripe\ApiResource
{
    const OBJECT_NAME = 'issuing.card';

    use \MyStripe\ApiOperations\All;
    use \MyStripe\ApiOperations\Create;
    use \MyStripe\ApiOperations\Retrieve;
    use \MyStripe\ApiOperations\Update;

    /**
     * @param null|array $params
     * @param null|array|string $opts
     *
     * @throws \MyStripe\Exception\ApiErrorException if the request fails
     *
     * @return \MyStripe\Issuing\CardDetails the card details associated with that issuing card
     */
    public function details($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/details';
        list($response, $opts) = $this->_request('get', $url, $params, $opts);
        $obj = \MyStripe\Util\Util::convertToStripeObject($response, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }
}
