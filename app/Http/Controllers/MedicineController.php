<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Contracts\View\View;

class MedicineController extends Controller
{
    public function index(): View
    {
        return view('medicines.index', [
            'medicines' => Medicine::published()->latest('published_at')->paginate(12),
        ]);
    }

    public function show(Medicine $medicine): View
    {
        abort_unless($medicine->is_published, 404);

        return view('medicines.show', compact('medicine'));
    }
}
