<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TypeConge;
use App\Models\Direction;

class SettingsController extends Controller
{
    public function index()
    {
        $users = User::with('direction')->get();
        $roles = User::select('role')->distinct()->pluck('role');
        $types = TypeConge::all();
        $directions = Direction::all();

        return view('settings.index', compact('users', 'roles', 'types', 'directions'));
    }
}
