<?php
namespace Indeed\Ihc;

class LoadTemplates
{
    public function __construct(){

      add_filter('ihc_filter_on_load_template', array($this, 'loadTemplate'), 1, 2 );
    }

    public function loadTemplate($currentLocation='', $searchFile='')
    {
        /// search into indeed-membership-pro theme folder
        if ($location=$this->searchTemplateIntoCurrentTheme('indeed-membership-pro/' . $searchFile)){
            return $location;
        }
        /// search into theme root
        if ($location=$this->searchTemplateIntoCurrentTheme($searchFile)){
            return $location;
        }
        /// default (plugin template file)
        return $currentLocation;
    }


    private function searchTemplateIntoCurrentTheme($search=''){
        if ($location=locate_template($search)){
            return $location;
        }
        return '';
    }


}
