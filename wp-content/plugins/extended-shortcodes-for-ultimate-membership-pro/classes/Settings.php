<?php
namespace UmpESh;

class Settings
{
    /**
     * Modify this array with your custom values
     * @var array
     */
    private $data = [

    										'lang_domain'				=> 'extended-shortcodes-for-ultimate-membership-pro',
    										'slug'							=> 'extended-shortcodes-for-ultimate-membership-pro',
    										'name'						  => 'Extended Shortcodes',
    										'description'				=> 'Extra functionality provided based on additional dedicated Shortcodes',
                        'ump_min_version'		=> '9.5',
    ];
    /**
     * Initialized automaticly. don't edit this array
     * @var array
     */
    private $paths = [
                      'dir_path'					=> '',
                      'dir_url'						=> '',
                      'plugin_base_name'	=> '',
    ];

    /**
     * @param none
     * @return none
     */
    public function __construct()
    {
        $this->setPaths();
        add_filter( 'ihc_default_options_group_filter', [ $this, 'options' ], 1, 2 );
    }



  /**
    * @param array
    * @param string
    * @return array
    */
    public function options( $options=[], $type='' )
    {
        if ( $this->data['slug']== $type ){
                return [
                    $this->data['slug'] . '-enabled'         		=> 0,
                ];
        }
        return $options;
    }

    /**
     * @param none
     * @return none
     */
    public function setPaths()
    {
        $this->paths['dir_path'] = plugin_dir_path( __FILE__ );
        $this->paths['dir_path'] = str_replace( 'classes/', '', $this->paths['dir_path'] );

        $this->paths['dir_url'] = plugin_dir_url( __FILE__ );
        $this->paths['dir_url'] = str_replace( 'classes/', '', $this->paths['dir_url'] );

        $this->paths['plugin_base_name'] = dirname(plugin_basename( __FILE__ ));
        $this->paths['plugin_base_name'] = str_replace( 'classes', '', $this->paths['plugin_base_name'] );
    }

    /**
     * @param string
     * @return object
     */
    public function get()
    {
        return $this->data + $this->paths;
    }

    /**
     * @param none
     * @return string
     */
    public function getPluginSlug()
    {
        return $this->data['slug'];
    }
}
