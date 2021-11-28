<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;

class EventController extends Controller
{
	public function index (Request $r) {
		$records = Event::select('id', 'title', 'date', 'day')->get();
		$result = [];

		foreach ($records as $key => $record) {
			$date = Carbon::parse($record->date)->format('Y-n');
			if ($date == $r->date) {
				array_push($result, $record);
			}
		}

		return response([
			'records' => $result
		]);
	}
	public function store (Request $r) {
		$validator = \Validator::make($r->all(), [
			'title' => 'required',
			'from' => 'required',
			'to' => 'required',
			'days' => 'required'
		]);

		if ($validator->fails()) {
			return response([
				'errors' => $validator->errors()->all()
			], 403);
		}

		if ($r->override == 1) {
			$events = Event::select('id')->get();

			foreach ($events as $key => $event) {
				$event->delete();
			}
		}

		$from = Carbon::parse($r->from);
		$to = Carbon::parse($r->to);

		$diff = $to->diffInDays($from);

		$temp_days = [];

		for ($i = 0; $i <= $diff; $i++) {
			$temp_day;
			if ($i != 0) {
				$from = $from->addDay();
				$temp_day = $from->format('l');
			} else {
				$temp_day = $from->format('l');
			}
			array_push($temp_days, (object) [
				'date' => ($i != 0) ? Carbon::parse($r->from)->addDay($i) : Carbon::parse($r->from),
				'day' => $temp_day
			]);
		}

		foreach ($temp_days as $key => $temp_day) {
			if (in_array($temp_day->day, $r->days)) {
				Event::create([
					'title' => $r->title,
					'slug' => str_slug($r->title),
					'date' => $temp_day->date,
					'day' => $temp_day->day
				]);
			}
		}

		return response([
			'record' => 'success'
		]);
	}
}
