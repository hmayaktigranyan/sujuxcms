<?php

class AdminDashboardController extends AdminController {

    protected $path = "dashboard";

    /**
     * Admin dashboard
     *
     */
    public function getIndex() {
        return View::make('admin/dashboard');
    }

}
