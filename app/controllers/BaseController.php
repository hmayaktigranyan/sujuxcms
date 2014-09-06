<?php

use Illuminate\Routing\Controller;

class BaseController extends Controller {

    protected $path = "";

    protected $fieldTypes = array();
    
    protected $pageSize = 10;
    
    public function __construct() {
        $languages = Language::all();
        View::share('languages', $languages);
        $languagesByKey = array();
        foreach ($languages as $language) {
            $languagesByKey[$language->code] = $language->title;
        }
        View::share('languagesByKey', $languagesByKey);
        if ($this->path) {
            View::share('path', $this->path);
        }
        $entities = Entity::all();
        View::share('entities', $entities);
        
        $this->fieldTypes = array(
            'text' => trans('Text'),
            'textarea' => trans('Textarea'),
            'richtext' => trans('Rich text'),
            'date' => trans('Date'),
            //'radio' => trans('Radio field'),
            'checkbox' => trans('Checkbox'),
            //'location' => trans('Geolocation'),
            //'user_select' => trans('User Select'),
            //'user_select_multi' => trans('Multivalue User Select'),
            'tree' => trans('Tree'),
            'multitree' => trans('MultivalutTree'),
            'select' => trans('Select'),
            'multiselect' => trans('Multivalue select'),
            'file' => trans('File'),
            'image' => trans('Image'),
        );
        View::share('fieldTypes', $this->fieldTypes);

    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout() {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

}
