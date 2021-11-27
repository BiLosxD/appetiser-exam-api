<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index () {
		return response([
			'events' => ['asdsad']
		]);
	}
}
