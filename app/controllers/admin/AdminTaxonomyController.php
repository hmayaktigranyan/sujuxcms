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
        $terms = DB::collection('terms')->where('taxonomy_id', $object->id)->get();

        return View::make('admin.' . $this->path . '.terms')->with('object', $object)->with('terms', $terms);
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
        } elseif (($inputs['term_list']  && $inputs['bulkaction'] == "updateselected") || isset($inputs['update'])  ) {


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
        if($inputs){
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
