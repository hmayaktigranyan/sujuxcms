<?php

class AdminObjectController extends AdminController {

    protected $path = "object";

    public function __construct() {

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($id) {
        $entity = Entity::findOrFail($id);
        $inputs = Input::all();
        $fields = array_sort($entity->fields, function($value) {
            return $value['order'];
        });

        $taxonomyTerms = array();
        $filterFields = array();
        if(count($entity->languages)>1){
        $filterFields['language'] = array('name' => 'language', 'type' => 'language');
        }
        $queryFilterExists = false;
        foreach ($fields as $field) {
            if (!$field['enabled']) {
                continue;
            }
            if (!$field['filter_browse'] && !$field['visible_browse']) {
                continue;
            }
            if ($field['filter_browse']) {
                if (in_array($field['type'], array('text', 'textarea','richtext', 'date', 'file', 'image', 'checkbox', 'tree', 'multitree', 'select', 'multiselect'))) {
                    $filterFields[$field['name']] = $field;
                }
                if ($field['type'] == 'text' || $field['type'] == 'textarea' || $field['type'] == 'richtext') {
                    $queryFilterExists = true;
                }
            }
            if ($field['type'] == 'select' || $field['type'] == 'multiselect') {
                $taxonomyId = $field['taxonomy_id'];
                $cacheKey = 'terms_ordered_select_' . $taxonomyId;
                if (!Cache::has($cacheKey)) {
                    $terms = DB::collection('terms')->where('taxonomy_id', $taxonomyId)->orderBy('order')->lists('title_en', '_id');
                    Cache::forever($cacheKey, $terms);
                }
                $terms = Cache::get($cacheKey);
                $taxonomyTerms[$taxonomyId] = $terms;
            }elseif ($field['type'] == 'tree' || $field['type'] == 'multitree' ) {

                $taxonomyId = $field['taxonomy_id'];
                $cacheKey = 'terms_ordered_full_' . $taxonomyId;
                if (!Cache::has($cacheKey)) {
                    $terms = DB::collection('terms')->where('taxonomy_id', $taxonomyId)->orderBy('order')->get();
                    Cache::forever($cacheKey, $terms);
                }
                $terms = Cache::get($cacheKey);
                $taxonomyTermsFull[$taxonomyId] = $terms;
                
                $cacheKey = 'terms_ordered_select_' . $taxonomyId;
                if (!Cache::has($cacheKey)) {
                    $terms = DB::collection('terms')->where('taxonomy_id', $taxonomyId)->orderBy('order')->lists('title_en', '_id');
                    Cache::forever($cacheKey, $terms);
                }
                $terms = Cache::get($cacheKey);
                $taxonomyTerms[$taxonomyId] = $terms;
            }
        }
        $query = DB::collection('objects')->where('entity_id', $id);
        $queryParams = array();
        $queryStrings = array();
        foreach ($filterFields as $filterName => $field) {
            $fieldType = $field['type'];

            $vals = $inputs[$filterName];
            if ($fieldType == "text" || $fieldType == "textarea" || $field['type'] == 'richtext') {
                $vals = $inputs['query'];
            }
            if ($vals) {
                $queryParams[$filterName] = $vals;
                if ($fieldType == "language" || $fieldType == "select" || $fieldType == 'multiselect' || $fieldType== 'tree' || $fieldType == 'multitree') {
                    $query->whereIn($filterName, (array) $vals);
                } elseif ($fieldType == 'checkbox') {
                    if (count($vals) == 1) {
                        if ($vals[0] == 1) {
                            $query->where($filterName, "1");
                        } else {
                            $query->whereNull($filterName);
                            /* $query->where(function($query2) {
                              $query2->whereNull($filterName)->orWhere($filterName, 0);
                              }); */
                        }
                    }
                } elseif ($fieldType == 'file' || $fieldType == 'image') {
                    if (count($vals) == 1) {
                        if ($vals[0] == 1) {
                                $query->whereNotNull($filterName)->where($filterName,"<>", '');
                            
                        } else {
                            $query->where(function($query)  use ($filterName) {
                                $query->whereNull($filterName)->orWhere($filterName, '');
                            });
                        }
                    }
                } elseif ($fieldType == "text" || $fieldType == "textarea" || $field['type'] == 'richtext') {
                    $queryStrings[$filterName] = '%' . (string) $vals . '%';
                } elseif ($fieldType == "date") {
                    if ($vals['start']) {
                        $query->where($filterName, '>=', new DateTime($vals['start']));
                    }
                    if ($vals['end']) {
                        $query->where($filterName, '<=', new DateTime($vals['end']));
                    }
                }
            }
        }
        if ($queryStrings) {
            $query->where(function ($query) use ($queryStrings) {
                foreach ($queryStrings as $filterName => $vals) {
                    $query->orWhere($filterName, 'LIKE', $vals);
                }
            });
        }
        $objects = $query->paginate($this->pageSize);//get();


        return View::make('admin.' . $this->path . '.index')->with('entity', $entity)->with('objects', $objects)
                        ->with('fields', $fields)->with('taxonomyTerms', $taxonomyTerms)->with('filterFields', $filterFields)
                        ->with('queryParams', $queryParams)->with('queryFilterExists', $queryFilterExists)->with('taxonomyTermsFull',$taxonomyTermsFull);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($id) {
        $entity = Entity::findOrFail($id);
        $taxonomyIds = array();

        $fields = array_sort($entity->fields, function($value) {
            return $value['order'];
        });
        $taxonomyTerms = array();
        $taxonomyTermsFull = array();
        foreach ($fields as $field) {
            if (!$field['enabled'] || !$field['visible_form']) {
                continue;
            }
            if ($field['type'] == 'select' || $field['type'] == 'multiselect') {

                $taxonomyId = $field['taxonomy_id'];
                $cacheKey = 'terms_ordered_select_' . $taxonomyId;
                if (!Cache::has($cacheKey)) {
                    $terms = DB::collection('terms')->where('taxonomy_id', $taxonomyId)->orderBy('order')->lists('title_en', '_id');
                    Cache::forever($cacheKey, $terms);
                }
                $terms = Cache::get($cacheKey);
                $taxonomyTerms[$taxonomyId] = $terms;
            }elseif ($field['type'] == 'tree' || $field['type'] == 'multitree' ) {

                $taxonomyId = $field['taxonomy_id'];
                $cacheKey = 'terms_ordered_full_' . $taxonomyId;
                if (!Cache::has($cacheKey)) {
                    $terms = DB::collection('terms')->where('taxonomy_id', $taxonomyId)->orderBy('order')->get();
                    Cache::forever($cacheKey, $terms);
                }
                $terms = Cache::get($cacheKey);
                $taxonomyTermsFull[$taxonomyId] = $terms;
            }
        }

        return View::make('admin.' . $this->path . '.create')->with('entity', $entity)->with('fields', $fields)->with('taxonomyTerms', $taxonomyTerms)
                ->with('taxonomyTermsFull', $taxonomyTermsFull);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($id) {
        $entity = Entity::findOrFail($id);
        $locale = App::getLocale();
        $fields = array_sort($entity->fields, function($value) {
            return $value['order'];
        });

        $rules = array();
        foreach ($fields as $field) {
            if (!$field['enabled'] || !$field['visible_form'] || !$field['validation']) {
                continue;
            }
            $fieldName = $field['name'];
            $validation = $field['validation'];
            if (!is_array($validation)) {
                $validation = array($validation);
            }
            foreach ($validation as $rule) {
                if ($rule == "required") {
                    $rules[$fieldName] = $rule;
                }
            }
        }
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            Notification::error('Please check the form below for errors');
            return Redirect::back()->withInput()->withErrors($validator);
        } else {

            $object = new Object;
            foreach ($fields as $field) {
                if (!$field['enabled'] || !$field['visible_form']) {
                    continue;
                }
                if ($field['type'] == "date") {
                    $object->addDateField($field['name']);
                }
            }
            $object->entity_id = $entity->_id;
            $object->language = Input::get("language");
            if (Input::get('originalObjectId')) {
                $originalObjectId = Input::get('originalObjectId');
                $originalObject = Object::findOrFail($originalObjectId);
                $origianlLang = $originalObject->language;
                $tr = $origianlLang . "_id";
                $object->$tr = $originalObjectId;
                foreach ($entity->languages as $lang) {
                    $tr = $lang . "_id";
                    if ($originalObject->$tr) {
                        $object->$tr = $originalObject->$tr; //set ar_id= original ar_id and so
                    }
                }
            }
            foreach ($fields as $field) {
                if (!$field['enabled'] || !$field['visible_form']) {
                    continue;
                }
                $fieldName = $field['name'];
                $object->$fieldName = Input::get($fieldName);
            }
            $object->save();
            if ($originalObjectId) {
                DB::collection('objects')->where('_id', $originalObjectId)
                        ->update(array($object->language . "_id" => $object->id));
                DB::collection('objects')->where($origianlLang . '_id', $originalObjectId)->where('_id', '!=', $object->id)
                        ->update(array($object->language . "_id" => $object->id));
            }
            Notification::success('Successfully created !');
            return Redirect::to('admin/' . $this->path . '/' . $id);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function translate($id, $language) {
        $object = Object::findOrFail($id);

        $entity = Entity::findOrFail($object->entity_id);
        $taxonomyIds = array();

        $fields = array_sort($entity->fields, function($value) {
            return $value['order'];
        });
        $taxonomyTerms = array();
        foreach ($fields as $field) {
            if ($field['type'] == 'tree' || $field['type'] == 'multitree' || $field['type'] == 'select' || $field['type'] == 'multiselect') {
                $taxonomyId = $field['taxonomy_id'];
                $cacheKey = 'terms_ordered_select_' . $taxonomyId;
                if (!Cache::has($cacheKey)) {
                    $terms = DB::collection('terms')->where('taxonomy_id', $taxonomyId)->orderBy('order')->lists('title_en', '_id');
                    Cache::forever($cacheKey, $terms);
                }
                $terms = Cache::get($cacheKey);
                $taxonomyTerms[$taxonomyId] = $terms;
            }
        }

        return View::make('admin.' . $this->path . '.translate')->with('originalObject', $object)
                        ->with('toLanguage', $language)
                        ->with('entity', $entity)
                        ->with('fields', $fields)
                        ->with('taxonomyTerms', $taxonomyTerms);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $object = Object::findOrFail($id);

        $entity = Entity::findOrFail($object->entity_id);
        foreach ($entity->fields as $field) {
            if ($field['type'] == "date") {
                $object->addDateField($field['name']);
            }
        }
        $taxonomyIds = array();

        $fields = array_sort($entity->fields, function($value) {
            return $value['order'];
        });
        $taxonomyTerms = array();
        foreach ($fields as $field) {
            if (!$field['enabled'] || !$field['visible_form']) {
                continue;
            }
            if ($field['type'] == 'tree' || $field['type'] == 'multitree' || $field['type'] == 'select' || $field['type'] == 'multiselect') {
                $taxonomyId = $field['taxonomy_id'];
                $cacheKey = 'terms_ordered_select_' . $taxonomyId;
                if (!Cache::has($cacheKey)) {
                    $terms = DB::collection('terms')->where('taxonomy_id', $taxonomyId)->orderBy('order')->lists('title_en', '_id');
                    Cache::forever($cacheKey, $terms);
                }
                $terms = Cache::get($cacheKey);
                $taxonomyTerms[$taxonomyId] = $terms;
            }
        }

        return View::make('admin.' . $this->path . '.show')->with('object', $object)->with('entity', $entity)
                        ->with('fields', $fields)->with('taxonomyTerms', $taxonomyTerms);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $object = Object::findOrFail($id);

        $entity = Entity::findOrFail($object->entity_id);
        foreach ($entity->fields as $field) {
            if (!$field['enabled'] || !$field['visible_form']) {
                continue;
            }
            if ($field['type'] == "date") {
                $object->addDateField($field['name']);
            }
        }
        $taxonomyIds = array();

        $fields = array_sort($entity->fields, function($value) {
            return $value['order'];
        });
        $taxonomyTerms = array();
        foreach ($fields as $field) {
            if (!$field['enabled'] || !$field['visible_form']) {
                continue;
            }
            if ( $field['type'] == 'select' || $field['type'] == 'multiselect') {
                $taxonomyId = $field['taxonomy_id'];
                $cacheKey = 'terms_ordered_select_' . $taxonomyId;
                if (!Cache::has($cacheKey)) {
                    $terms = DB::collection('terms')->where('taxonomy_id', $taxonomyId)->orderBy('order')->lists('title_en', '_id');
                    Cache::forever($cacheKey, $terms);
                }
                $terms = Cache::get($cacheKey);
                $taxonomyTerms[$taxonomyId] = $terms;
            }elseif ($field['type'] == 'tree' || $field['type'] == 'multitree' ) {

                $taxonomyId = $field['taxonomy_id'];
                $cacheKey = 'terms_ordered_full_' . $taxonomyId;
                if (!Cache::has($cacheKey)) {
                    $terms = DB::collection('terms')->where('taxonomy_id', $taxonomyId)->orderBy('order')->get();
                    Cache::forever($cacheKey, $terms);
                }
                $terms = Cache::get($cacheKey);
                $taxonomyTermsFull[$taxonomyId] = $terms;
            }
        }

        return View::make('admin.' . $this->path . '.edit')->with('object', $object)->with('entity', $entity)
                        ->with('fields', $fields)->with('taxonomyTerms', $taxonomyTerms)->with('taxonomyTermsFull', $taxonomyTermsFull);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        $object = Object::findOrFail($id);
        $entity = Entity::findOrFail($object->entity_id);
        $locale = App::getLocale();
        $fields = array_sort($entity->fields, function($value) {
            return $value['order'];
        });

        $rules = array();
        foreach ($fields as $field) {
            if (!$field['enabled'] || !$field['visible_form'] || !$field['validation']) {
                continue;
            }
            $fieldName = $field['name'];
            $validation = $field['validation'];
            if (!is_array($validation)) {
                $validation = array($validation);
            }
            foreach ($validation as $rule) {
                if ($rule == "required") {
                    $rules[$fieldName] = $rule;
                }
            }
        }
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            Notification::error('Please check the form below for errors');
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            foreach ($fields as $field) {
                if ($field['type'] == "date") {
                    $object->addDateField($field['name']);
                }
            }
            
            foreach ($fields as $field) {
                if (!$field['enabled'] || !$field['visible_form']) {
                    continue;
                }
                $fieldName = $field['name'];
                if ($field['type'] == "file" || $field['type'] == "image") {
                    $val =  Input::get($fieldName);
                    $val = str_replace(URL::to('/')."/","",$val);
                    $object->$fieldName = $val;
                }else{
                    $object->$fieldName = Input::get($fieldName);
                }
            }
            $oldLanguage = $object->language;
            if ($object->language != Input::get("language")) {
                $newLanguage = Input::get("language");
                $tr = $newLanguage . "_id";
                if ($object->$tr) {//if exist translation unset old tranlsations of this
                    DB::collection('objects')->where($oldLanguage . '_id', $id)
                            ->update(array($oldLanguage . "_id" => null));
                    foreach ($entity->languages as $lang) {
                        $tr = $lang . "_id";
                        $object->$tr = null;
                    }
                } else {
                    DB::collection('objects')->where($oldLanguage . '_id', $id)
                            ->update(array($newLanguage . "_id" => $id));
                    DB::collection('objects')->where($oldLanguage . '_id', $id)
                            ->update(array($oldLanguage . "_id" => null));
                }
            }
            $object->language = Input::get("language");
            $object->save();
            Notification::success('Successfully updated !');
            return Redirect::to('admin/' . $this->path . '/' . $object->entity_id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        $object = Object::findOrFail($id);
        $language = $object->language;
        $entity_id = $object->entity_id;
        Object::destroy($id);
        DB::collection('objects')->where($language . '_id', $id)
                ->update(array($language . "_id" => null));
        Notification::success('Successfully deleted');
        return Redirect::to('admin/' . $this->path . '/' . $entity_id);
    }

    public function confirmDestroy($id) {

        $object = Object::findOrFail($id);
        return View::make('admin.' . $this->path . '.confirm-destroy')->with('object', $object);
    }

}
