<?php namespace eTrack\Controllers;

abstract class RestController extends BaseController {

    abstract public function index();

    abstract public function create();

    abstract public function store();

    abstract public function edit($id);

    abstract public function update($id);

    abstract public function destroy($id);

} 