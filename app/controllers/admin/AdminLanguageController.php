<?php

class AdminLanguageController extends AdminController {

    protected $path = "language";

    public function __construct() {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $objects = Language::paginate();
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
            'code' => 'required|unique:languages'
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            Notification::error('Please check the form below for errors');
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            // store
            $object = new Language;
            $object->title = Input::get('title');
            $object->code = Input::get('code');
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

        $object = Language::findOrFail($id);
        return View::make('admin.' . $this->path . '.show')->with('object', $object);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $object = Language::findOrFail($id);
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
            'code' => 'required|unique:languages,code,' . $id.',_id'
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            Notification::error('Please check the form below for errors');

            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            $object = Language::findOrFail($id);
            $object->title = Input::get('title');
            $object->code = Input::get('code');
            $object->site_default = Input::get('site_default');
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

        Language::destroy($id);
        Notification::success('Successfully deleted');
        return Redirect::to('admin/' . $this->path . '');
    }

    public function confirmDestroy($id) {

        $object = Language::findOrFail($id);
        return View::make('admin.' . $this->path . '.confirm-destroy')->with('object', $object);
    }

}
