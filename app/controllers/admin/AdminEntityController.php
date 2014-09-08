<?php

class AdminEntityController extends AdminController {

    protected $path = "entity";

    public function __construct() {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {

        $objects = Entity::all();
        return View::make('admin.' . $this->path . '.index')->with('objects', $objects);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return View::make('admin.' . $this->path . '.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        $rules = array(
            'title' => 'required',
            'name' => 'required|unique:entities',
            'languages' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            Notification::error('Please check the form below for errors');
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            // store
            $object = new Entity;
            $object->title = Input::get('title');
            $object->name = Input::get('name');
            $object->languages = Input::get('languages');

            $object->save();

            // redirect
            Notification::success('Successfully created !');
            return Redirect::to('admin/' . $this->path);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {

        $object = Entity::findOrFail($id);
        return View::make('admin.' . $this->path . '.show')->with('object', $object);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $object = Entity::findOrFail($id);
        return View::make('admin.' . $this->path . '.edit')->with('object', $object);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        $rules = array(
            'title' => 'required',
            'name' => 'required|unique:entities,name,' . $id . ',_id',
            'languages' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            Notification::error('Please check the form below for errors');

            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            $object = Entity::findOrFail($id);
            $object->title = Input::get('title');
            $object->name = Input::get('name');
            $object->languages = Input::get('languages');
            $object->save();

            Notification::success('Successfully updated !');
            return Redirect::to('admin/' . $this->path . '/' . $id . '/edit');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {

        Entity::destroy($id);
        Notification::success('Successfully deleted');
        return Redirect::to('admin/' . $this->path . '');
    }

    public function confirmDestroy($id) {

        $object = Entity::findOrFail($id);
        return View::make('admin.' . $this->path . '.confirm-destroy')->with('object', $object);
    }

    public function fields($id) {
        $object = Entity::findOrFail($id);

        $taxonomies = Taxonomy::lists('title_en', '_id');
        $fieldsOrdered = array_sort($object->fields, function($value) {
            return $value['order'];
        });
        return View::make('admin.' . $this->path . '.fields')
                        ->with('object', $object)->with('taxonomies', $taxonomies)->with('fieldsOrdered', $fieldsOrdered);
    }

    public function fieldsUpdate($id) {
        $object = Entity::findOrFail($id);
        $languages = Language::all();
        $inputs = Input::all();
        $fieldsOriginal = $object->fields;
        $fields = array();
        $fieldsChanged = false;
        $maxOrder = 0;
        if ($fieldsOriginal) {
            foreach ($fieldsOriginal as $field) {
                if ($field['name']) {
                    $fields[$field['name']] = $field;
                    $maxOrder = max($maxOrder, $field['order']);
                }
            }
        }
        if ($inputs['bulkaction'] && !$inputs['fields_list']) {
            Notification::error(trans('Please select items to perform action'));
        }
        if (is_array($inputs['new_field_name'])) {

            for ($index = 0; $index < count($inputs['new_field_name']); $index++) {
                if (!$inputs['new_field_type'][$index] || !$inputs['new_field_name'][$index]) {
                    continue;
                }
                $field = array();
                $field['type'] = $inputs['new_field_type'][$index];
                $field['name'] = $inputs['new_field_name'][$index];
                if ($fields[$field['name']]) {
                    continue;
                }
                if ($field['type'] == 'tree' || $field['type'] == 'multitree' || $field['type'] == 'select' || $field['type'] == 'multiselect') {
                    if (!$inputs['new_field_taxonomy'][$index]) {
                        continue;
                    }
                    $field['taxonomy_id'] = $inputs['new_field_taxonomy'][$index];
                }

                foreach ($languages as $language) {
                    $fieldname = 'title_' . $language->code;
                    $value = trim($inputs['new_field_title'][$language->code][$index]); //Input::get($fieldname);
                    if ($value) {
                        $field[$fieldname] = $value;
                    }
                }
                $maxOrder++;
                $field['enabled'] = true;
                $field['order'] = $maxOrder;
                $field['visible_form'] = true;
                $fields[$field['name']] = $field;
                $fieldsChanged = true;
                // DB::collection('entities')->where('_id', $object->id)->push('fields', $field);
            }
            Notification::success('Successfully added !');
        }
        if ($inputs['fields_list'] && isset($inputs['bulkaction']) && $inputs['bulkaction'] == "deleteselected") {
            if (!is_array($inputs['fields_list'])) {
                $inputs['fields_list'] = array($inputs['fields_list']);
            } else {
                $inputs['fields_list'] = array_keys($inputs['fields_list']);
            }
            foreach ($inputs['fields_list'] as $fieldName) {
                unset($fields[$fieldName]);
            }
            $fieldsChanged = true;
            Notification::success('Successfully deleted !');
        } else if ($inputs['fields_list'] && isset($inputs['delete_yes'])) {
            if (!is_array($inputs['fields_list'])) {
                $inputs['fields_list'] = array($inputs['fields_list']);
            } else {
                $inputs['fields_list'] = array_keys($inputs['fields_list']);
            }
            foreach ($inputs['fields_list'] as $fieldName) {
                unset($fields[$fieldName]);
            }
            $fieldsChanged = true;
            Notification::success('Successfully deleted !');
        } elseif (($inputs['fields_list'] && $inputs['bulkaction'] == "updateselected") || isset($inputs['update'])) {

            if (isset($inputs['title']) && is_array($inputs['title'])) {
                foreach ($inputs['title'] as $fieldName => $titles) {
                    if ($inputs["bulkaction"] == "updateselected" && !isset($inputs['fields_list'][$fieldName])) {
                        continue;
                    }

                    if (isset($fields[$fieldName]) && is_array($titles)) {
                        $field = $fields[$fieldName];
                        foreach ($languages as $language) {
                            $fieldname = 'title_' . $language->code;
                            $value = trim($titles[$language->code]);
                            if ($value) {
                                $field[$fieldname] = $value;
                            }
                        }
                        $fields[$fieldName] = $field;
                        $fieldsChanged = true;
                    }
                }
            }
            Notification::success('Successfully updated !');
        } elseif ($inputs['fields_list'] && $inputs['bulkaction'] == "enabled") {
            if (!is_array($inputs['fields_list'])) {
                $inputs['fields_list'] = array($inputs['fields_list']);
            } else {
                $inputs['fields_list'] = array_keys($inputs['fields_list']);
            }
            foreach ($inputs['fields_list'] as $fieldName) {
                $fields[$fieldName]['enabled'] = true;
            }
            $fieldsChanged = true;
            Notification::success('Successfully updated !');
        } elseif ($inputs['fields_list'] && $inputs['bulkaction'] == "disable") {
            if (!is_array($inputs['fields_list'])) {
                $inputs['fields_list'] = array($inputs['fields_list']);
            } else {
                $inputs['fields_list'] = array_keys($inputs['fields_list']);
            }
            foreach ($inputs['fields_list'] as $fieldName) {
                $fields[$fieldName]['enabled'] = false;
            }
            $fieldsChanged = true;
            Notification::success('Successfully updated !');
        }
        if ($fieldsChanged) {
            $object->fields = $fields;
            $object->save();
        }
        return Redirect::back();
    }

    public function fieldsOrder($id) {
        $object = Entity::findOrFail($id);
        $languages = Language::all();
        $inputs = Input::all();
        $fields = $object->fields;

        $fieldsOrdered = array_sort($fields, function($value) {
            return $value['order'];
        });

        return View::make('admin.' . $this->path . '.fields-order')->with('object', $object)->with('fieldsOrdered', $fieldsOrdered);
    }

    public function fieldsOrderUpdate($id) {
        $object = Entity::findOrFail($id);
        $inputs = Input::all();
        $fieldsOriginal = $object->fields;
        $fields = array();
        if ($fieldsOriginal) {
            foreach ($fieldsOriginal as $field) {
                if ($field['name']) {
                    $fields[$field['name']] = $field;
                }
            }
        }
        if (isset($inputs['itemsorder'])) {
            $itemorders = @json_decode(stripslashes($inputs['itemsorder']), true);

            if (is_array($itemorders)) {
                $i = 1;
                foreach ($itemorders as $itemorder) {
                    $fieldName = $itemorder['id'];
                    if (!$fieldName || !$fields[$fieldName]) {
                        continue;
                    }
                    $fields[$fieldName]['order'] = $i;
                    $i++;
                }
                $object->fields = $fields;
                $object->save();
            }
        }
        Notification::success('Successfully updated !');
        return Redirect::back();
    }

    public function fieldsDetails($id) {
        $object = Entity::findOrFail($id);

        $taxonomies = Taxonomy::lists('title_en', '_id');
        $fieldsOrdered = array_sort($object->fields, function($value) {
            return $value['order'];
        });
        return View::make('admin.' . $this->path . '.fields-details')
                        ->with('object', $object)->with('taxonomies', $taxonomies)->with('fieldsOrdered', $fieldsOrdered);
    }

    public function fieldsDetailsUpdate($id) {
        $object = Entity::findOrFail($id);
        $inputs = Input::all();
        $fieldsOriginal = $object->fields;
        $fields = array();
        $fieldsChanged = false;
        $maxOrder = 0;
        if ($fieldsOriginal) {
            foreach ($fieldsOriginal as $field) {
                if ($field['name']) {
                    $fields[$field['name']] = $field;
                    $maxOrder = max($maxOrder, $field['order']);
                }
            }
        }
        foreach ($fields as $fieldName => $field) {
            $postfix = "_" . $fieldName;
            $visible_form = $inputs['visible_form' . $postfix];
            $visible_browse = $inputs['visible_browse' . $postfix];
            $filter_browse = $inputs['filter_browse' . $postfix];
            if ($visible_form) {
                $visible_form = true;
            } else {
                $visible_form = false;
            }
            if ($visible_browse) {
                $visible_browse = true;
            } else {
                $visible_browse = false;
            }
            if ($filter_browse) {
                $filter_browse = true;
            } else {
                $filter_browse = false;
            }
            $fields[$fieldName]['visible_form'] = $visible_form;
            $fields[$fieldName]['visible_browse'] = $visible_browse;
            $fields[$fieldName]['filter_browse'] = $filter_browse;
            $fields[$fieldName]['validation'] = $inputs['validation' . $postfix];
            $fieldsChanged = true;
        }

        if ($fieldsChanged) {
            $object->fields = $fields;
            $object->save();
            Notification::success('Successfully updated !');
        }
        return Redirect::back();
    }

}
