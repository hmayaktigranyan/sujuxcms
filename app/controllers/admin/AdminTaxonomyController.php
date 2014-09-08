<?php

class AdminTaxonomyController extends AdminController {

    protected $path = "taxonomy";

    public function __construct() {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $objects = Taxonomy::paginate();

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
            'name' => 'required|unique:taxonomies',
        );
        $languages = Language::all();
        foreach ($languages as $language) {
            $rules['title_' . $language->code] = 'required';
        }
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            Notification::error('Please check the form below for errors');
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            // store
            $object = new Taxonomy;
            $object->name = Input::get('name');
            foreach ($languages as $language) {
                $fieldname = 'title_' . $language->code;
                $object->$fieldname = Input::get($fieldname);
            }
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

        $object = Taxonomy::findOrFail($id);
        return View::make('admin.' . $this->path . '.show')->with('object', $object);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $object = Taxonomy::findOrFail($id);
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
            'name' => 'required|unique:taxonomies,name,' . $id . ',_id',
        );
        $languages = Language::all();
        foreach ($languages as $language) {
            $rules['title_' . $language->code] = 'required';
        }
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            Notification::error('Please check the form below for errors');

            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            $object = Taxonomy::findOrFail($id);
            $object->name = Input::get('name');
            foreach ($languages as $language) {
                $fieldname = 'title_' . $language->code;
                $object->$fieldname = Input::get($fieldname);
            }
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

        Taxonomy::destroy($id);
        Notification::success('Successfully deleted');
        return Redirect::to('admin/' . $this->path . '');
    }

    public function confirmDestroy($id) {

        $object = Taxonomy::findOrFail($id);
        return View::make('admin.' . $this->path . '.confirm-destroy')->with('object', $object);
    }

    public function terms($id) {
        $object = Taxonomy::findOrFail($id);
       

        $fields = array_sort($object->fields, function($value) {
            return $value['order'];
        });
        $taxonomyTerms = array();
        foreach ($fields as $field) {
            if (!$field['enabled']) {
                continue;
            }
            if (!$field['filter_browse'] && !$field['visible_browse']) {
                continue;
            }
            if ($field['filter_browse']) {
                if (in_array($field['type'], array('text', 'textarea', 'richtext', 'date', 'file', 'image', 'checkbox', 'tree', 'multitree', 'select', 'multiselect'))) {
                    $filterFields[$field['name']] = $field;
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
            } elseif ($field['type'] == 'tree' || $field['type'] == 'multitree') {

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
 $terms = DB::collection('terms')->where('taxonomy_id', $object->id)->get();
        return View::make('admin.' . $this->path . '.terms')->with('object', $object)->with('terms', $terms)->with('fields', $fields)
                        ->with('taxonomyTerms', $taxonomyTerms)->with('taxonomyTermsFull', $taxonomyTermsFull);
    }

    public function termsUpdate($id) {
        $object = Taxonomy::findOrFail($id);
        $languages = Language::all();
        $inputs = Input::all();

        if ($inputs['bulkaction'] && !$inputs['term_list']) {
            Notification::error(trans('Please select items to perform action'));
        }
        if (is_array($inputs['new_term_title'])) {
            for ($index = 0; $index < count($inputs['new_term_title']['en']); $index++) {
                $term = new Term;
                $term->taxonomy_id = $object->id;
                foreach ($languages as $language) {
                    $fieldname = 'title_' . $language->code;
                    $value = trim($inputs['new_term_title'][$language->code][$index]); //Input::get($fieldname);
                    if ($value) {
                        $term->$fieldname = $value;
                    }
                }
                $term->visible = 'y';
                $term->parent_id = 0;
                $term->order = 0;
                $term->level = 0;

                $term->save();
            }
            Notification::success('Successfully added !');
        }
        if ($inputs['term_list'] && isset($inputs['bulkaction']) && $inputs['bulkaction'] == "deleteselected") {
            if (!is_array($inputs['term_list'])) {
                $inputs['term_list'] = array($inputs['term_list']);
            } else {
                $inputs['term_list'] = array_keys($inputs['term_list']);
            }
            DB::table('terms')->whereIn('_id', $inputs['term_list'])->delete();
            Notification::success('Successfully deleted !');
        } else if ($inputs['term_list'] && isset($inputs['delete_yes'])) {
            if (!is_array($inputs['term_list'])) {
                $inputs['term_list'] = array($inputs['term_list']);
            } else {
                $inputs['term_list'] = array_keys($inputs['term_list']);
            }
            DB::table('terms')->whereIn('_id', $inputs['term_list'])->delete();
            Notification::success('Successfully deleted !');
        } elseif (($inputs['term_list'] && $inputs['bulkaction'] == "updateselected") || isset($inputs['update'])) {


            if (isset($inputs['title']) && is_array($inputs['title'])) {
                foreach ($inputs['title'] as $termId => $titles) {
                    if ($inputs["bulkaction"] == "updateselected" && !isset($inputs['term_list'][$termId])) {
                        continue;
                    }

                    if (is_array($titles)) {
                        $term = Term::findOrFail($termId);
                        foreach ($languages as $language) {
                            $fieldname = 'title_' . $language->code;
                            $value = trim($titles[$language->code]);
                            if ($value) {
                                $term->$fieldname = $value;
                            }
                        };

                        $term->save();
                    }
                }
            }
            Notification::success('Successfully updated !');
        } elseif ($inputs['term_list'] && $inputs['bulkaction'] == "visible") {
            if (!is_array($inputs['term_list'])) {
                $inputs['term_list'] = array($inputs['term_list']);
            } else {
                $inputs['term_list'] = array_keys($inputs['term_list']);
            }
            DB::table('terms')->whereIn('_id', $inputs['term_list'])->update(array('visible' => 'y'));
            Notification::success('Successfully updated !');
        } elseif ($inputs['term_list'] && $inputs['bulkaction'] == "disable") {
            if (!is_array($inputs['term_list'])) {
                $inputs['term_list'] = array($inputs['term_list']);
            } else {
                $inputs['term_list'] = array_keys($inputs['term_list']);
            }
            DB::table('terms')->whereIn('_id', $inputs['term_list'])->update(array('visible' => 'n'));
            Notification::success('Successfully updated !');
        }
        if ($inputs) {
            Event::fire('terms.update', array($object->id));
        }
        return Redirect::back();
    }

    public function termsOrder($id) {
        $object = Taxonomy::findOrFail($id);
        $terms = DB::collection('terms')->where('taxonomy_id', $object->id)->orderBy('order')->get();

        return View::make('admin.' . $this->path . '.terms-order')->with('object', $object)->with('terms', $terms);
    }

    public function termsOrderUpdate($id) {
        $object = Taxonomy::findOrFail($id);
        $inputs = Input::all();
        if (isset($inputs['itemsorder'])) {
            $itemorders = @json_decode(stripslashes($inputs['itemsorder']), true);

            if (is_array($itemorders)) {
                $this->updateTermsOrder($itemorders);
            }
        }
        Event::fire('terms.update', array($object->id));
        return Redirect::back();
    }

    public function fields($id) {
        $object = Taxonomy::findOrFail($id);

        $taxonomies = Taxonomy::lists('title_en', '_id');
        $fieldsOrdered = array_sort($object->fields, function($value) {
            return $value['order'];
        });
        return View::make('admin.' . $this->path . '.fields')
                        ->with('object', $object)->with('taxonomies', $taxonomies)->with('fieldsOrdered', $fieldsOrdered);
    }

    public function fieldsUpdate($id) {
        $object = Taxonomy::findOrFail($id);
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
        $object = Taxonomy::findOrFail($id);
        $languages = Language::all();
        $inputs = Input::all();
        $fields = $object->fields;

        $fieldsOrdered = array_sort($fields, function($value) {
            return $value['order'];
        });

        return View::make('admin.' . $this->path . '.fields-order')->with('object', $object)->with('fieldsOrdered', $fieldsOrdered);
    }

    public function fieldsOrderUpdate($id) {
        $object = Taxonomy::findOrFail($id);
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
        $object = Taxonomy::findOrFail($id);

        $taxonomies = Taxonomy::lists('title_en', '_id');
        $fieldsOrdered = array_sort($object->fields, function($value) {
            return $value['order'];
        });
        return View::make('admin.' . $this->path . '.fields-details')
                        ->with('object', $object)->with('taxonomies', $taxonomies)->with('fieldsOrdered', $fieldsOrdered);
    }

    public function fieldsDetailsUpdate($id) {
        $object = Taxonomy::findOrFail($id);
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
            $fields[$fieldName]['visible_form'] = $visible_form;
            $fields[$fieldName]['visible_browse'] = $visible_browse;
            $fieldsChanged = true;
        }

        if ($fieldsChanged) {
            $object->fields = $fields;
            $object->save();
            Notification::success('Successfully updated !');
        }
        return Redirect::back();
    }

    public function termShow($id) {
        $object = Term::findOrFail($id);

        $taxonomy = Taxonomy::findOrFail($object->taxonomy_id);
        foreach ($taxonomy->fields as $field) {
            if ($field['type'] == "date") {
                $object->addDateField($field['name']);
            }
        }
        $taxonomyIds = array();

        $fields = array_sort($taxonomy->fields, function($value) {
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

        return View::make('admin.' . $this->path . '.term-show')->with('object', $object)->with('taxonomy', $taxonomy)
                        ->with('fields', $fields)->with('taxonomyTerms', $taxonomyTerms);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function termEdit($id) {
        $object = Term::findOrFail($id);

        $taxonomy = Taxonomy::findOrFail($object->taxonomy_id);
        foreach ($taxonomy->fields as $field) {
            if (!$field['enabled'] || !$field['visible_form']) {
                continue;
            }
            if ($field['type'] == "date") {
                $object->addDateField($field['name']);
            }
        }
        $taxonomyIds = array();

        $fields = array_sort($taxonomy->fields, function($value) {
            return $value['order'];
        });
        $taxonomyTerms = array();
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
            } elseif ($field['type'] == 'tree' || $field['type'] == 'multitree') {

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

        return View::make('admin.' . $this->path . '.term-edit')->with('object', $object)->with('taxonomy', $taxonomy)
                        ->with('fields', $fields)->with('taxonomyTerms', $taxonomyTerms)->with('taxonomyTermsFull', $taxonomyTermsFull);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function termUpdate($id) {
        $object = Term::findOrFail($id);
        $taxonomy = Taxonomy::findOrFail($object->taxonomy_id);
        $locale = App::getLocale();
        $fields = array_sort($taxonomy->fields, function($value) {
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
                    $val = Input::get($fieldName);
                    $val = str_replace(URL::to('/') . "/", "", $val);
                    $object->$fieldName = $val;
                } else {
                    $object->$fieldName = Input::get($fieldName);
                }
            }
            $object->save();
            Notification::success('Successfully updated !');
            return Redirect::to('admin/' . $this->path . '/termshow/' . $object->id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function termDestroy($id) {
        $object = Term::findOrFail($id);
        $taxonomy_id = $object->taxonomy_id;
        Term::destroy($id);
        Notification::success('Successfully deleted');
        return Redirect::to('admin/' . $this->path . '/terms/' . $taxonomy_id);
    }

    public function termConfirmDestroy($id) {

        $object = Term::findOrFail($id);
        return View::make('admin.' . $this->path . '.term-confirm-destroy')->with('object', $object);
    }

    private function updateTermsOrder($itemorders, $parent_id = 0, $level = 0, $order = 0) {

        if (!is_array($itemorders)) {
            return;
        }
        /* $itemorders = @json_decode(stripslashes($_POST['itemsorder']),true);
         */

        foreach ($itemorders as $itemorder) {
            if (!$itemorder['id']) {
                continue;
            }
            $order++;
            DB::table('terms')
                    ->where('_id', $itemorder['id'])
                    ->update(array('order' => (int) $order, 'level' => (int) $level, 'parent_id' => $parent_id));
            if (is_array($itemorder["children"])) {
                $order = $this->updateTermsOrder($itemorder["children"], $itemorder['id'], $level + 1, $order);
            }
        }
        return $order;
    }

}
