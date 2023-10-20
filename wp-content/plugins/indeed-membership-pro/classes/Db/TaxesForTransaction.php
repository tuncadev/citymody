<?php
namespace Indeed\Ihc\Db;

class TaxesForTransaction
{
    /**
     * @var bool
     */
    private $isActive     = false;
    /**
     * @var string
     */
    private $country      = '';
    /**
     * @var string
     */
    private $state        = '';
    /**
     * @var float
     */
    private $amount       = 0.00;
    /**
     * @var string
     */
    private $currency     = '';
    /**
     * @var int
     */
    private $decimalsNumber = 2;


    /**
     * @param none
     * @return none
     */
    public function __construct()
    {
        $this->isActive = get_option('ihc_enable_taxes');
        $decimals = get_option( 'ihc_num_of_decimals' );
        if ( $decimals !== false && $decimals != '' && $decimals >= 0 ){
            $this->decimalsNumber = $decimals;
        }
    }

    /**
     * @param string
     * @return object
     */
    public function setCountry( $country='' )
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @param string
     * @return object
     */
    public function setState( $state='' )
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @param float
     * @return object
     */
    public function setAmount( $amount=0.00 )
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @param string
     * @return object
     */
    public function setCurrency( $currency='' )
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param none
     * @return array
     */
    public function getAll()
    {
        if ( !$this->isActive ){
            return false;
        }
        if ( empty( $this->currency ) ){
           $this->currency = get_option("ihc_currency");
        }
        if ( $this->country ){
            $taxes = $this->getForCountry();
        }
        if ( empty( $taxes ) ){
            $taxes = $this->getDefault();
        }
        $taxes = apply_filters( 'ihc_filter_return_taxes_for_transaction', $taxes, $this->amount, $this->currency, $this->country, $this->state );
        return $taxes;
    }

    /**
     * @param none
     * @return array
     */
    public function getForCountry()
    {
        $data = \Ihc_Db::get_taxes_by_country( $this->country, $this->state );
        if ( !$data ){
            return false;
        }
        $array['total'] = 0;

        foreach ($data as $tax){
            $temporary['label'] = $tax['label'];
      			$temporary['description'] = $tax['description'];
      			$temporary['percentage'] = $tax['amount_value'];
            $temporary['value'] = $tax['amount_value'] * $this->amount / 100;
            $temporary['value'] = round($temporary['value'], $this->decimalsNumber );
            $temporary['print_value'] = ihc_format_price_and_currency( $this->currency, $temporary['value'] );
            $array['items'][] = $temporary;
            $array['total'] += $temporary['value'];
        }
        $array['total'] = round( $array['total'], $this->decimalsNumber );
        $array['print_total'] = ihc_format_price_and_currency( $this->currency, $array['total'] );
        return $array;
    }

    /**
     * @param none
     * @return array
     */
    public function getDefault()
    {
        $taxesSettings = ihc_return_meta_arr('ihc_taxes_settings');
      	if ( empty($taxesSettings['ihc_default_tax_label']) || empty($taxesSettings['ihc_default_tax_value']) ){
            return false;
      	}
        $item['label'] = $taxesSettings['ihc_default_tax_label'];
        $item['percentage'] = $taxesSettings['ihc_default_tax_value'];
        $item['value'] = $taxesSettings['ihc_default_tax_value'] * $this->amount / 100;
        $item['value'] = round($item['value'], $this->decimalsNumber );
        $item['print_value'] = ihc_format_price_and_currency($this->currency, $item['value']);
        $array['items'][] = $item;
        $array['total'] = $item['value'];
        $array['print_total'] = ihc_format_price_and_currency($this->currency, $array['total']);
        return $array;
    }
}
