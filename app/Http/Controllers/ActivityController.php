<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Display a paginated, searchable, filterable listing of activities.
     */
    public function index(Request $request): View
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->search($request->string('search')->toString())
            ->ofEvent($request->string('event')->toString())
            ->ofType($request->string('type')->toString())
            ->sort($request->string('sort')->toString(), $request->string('direction')->toString())
            ->paginate(10)
            ->withQueryString();

        return view('activities.index', [
            'activities' => $activities,
            'events' => Activity::EVENTS,
            'types' => Activity::TYPES,
        ]);
    }
}
