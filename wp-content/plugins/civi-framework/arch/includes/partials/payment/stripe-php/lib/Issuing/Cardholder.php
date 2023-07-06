<?php

namespace MyStripe\Issuing;

/**
 * An Issuing <code>Cardholder</code> object represents an individual or business
 * entity who is <a href="https://stripe.com/docs/issuing">issued</a> cards.
 *
 * Related guide: <a
 * href="https://stripe.com/docs/issuing/cards#create-cardholder">How to create a
 * Cardholder</a>
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property null|\MyStripe\StripeObject $authorization_controls Spending rules that give you some control over how this cardholder's cards can be used. Refer to our <a href="https://stripe.com/docs/issuing/purchases/authorizations">authorizations</a> documentation for more details.
 * @property \MyStripe\StripeObject $billing
 * @property null|\MyStripe\StripeObject $company Additional information about a <code>company</code> cardholder.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property null|string $email The cardholder's email address.
 * @property null|\MyStripe\StripeObject $individual Additional information about an <code>individual</code> cardholder.
 * @property bool $is_default [DEPRECATED] Whether or not this cardholder is the default cardholder.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property \MyStripe\StripeObject $metadata Set of key-value pairs that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property string $name The cardholder's name. This will be printed on cards issued to them.
 * @property null|string $phone_number The cardholder's phone number.
 * @property \MyStripe\StripeObject $requirements
 * @property string $status Specifies whether to permit authorizations on this cardholder's cards.
 * @property string $type One of <code>individual</code> or <code>company</code>.
 */
class Cardholder extends \MyStripe\ApiResource
{
    const OBJECT_NAME = 'issuing.cardholder';

    use \MyStripe\ApiOperations\All;
    use \MyStripe\ApiOperations\Create;
    use \MyStripe\ApiOperations\Retrieve;
    use \MyStripe\ApiOperations\Update;
}
