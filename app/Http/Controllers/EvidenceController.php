<?php

namespace App\Http\Controllers;

use App\Models\ViolationEvidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvidenceController extends Controller
{
    /**
     * Display the specified evidence file.
     */
    public function show(ViolationEvidence $evidence)
    {
        // Verify tenant isolation
        $violation = $evidence->violation;
        $user = auth()->user();

        // Check if violation exists
        if (!$violation) {
            abort(404);
        }

        // Check if user has access to this school's data
        if (!$user->isSuperAdmin() && $violation->school_id !== $user->school_id) {
            abort(403);
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($evidence->file_path)) {
            abort(404);
        }

        return response()->file(
            Storage::disk('public')->path($evidence->file_path)
        );
    }
}
