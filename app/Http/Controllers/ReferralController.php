<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Referral;

class ReferralController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the referrals page — global list (not session-filtered).
     */
    public function index()
    {
        $currentSession = session('current_session');
        $referrals = Referral::orderBy('name')->get();

        return view('referrals.index', compact('referrals', 'currentSession'));
    }

    /**
     * Store a new referral (global — no session filter on uniqueness).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Check for duplicate name globally
        $exists = Referral::whereRaw('LOWER(name) = ?', [strtolower(trim($request->name))])->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A referral with this name already exists.',
            ], 422);
        }

        $referral = Referral::create([
            'name'    => trim($request->name),
            'session' => session('current_session') ?? 'global',
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Referral added successfully.',
            'referral' => $referral,
        ]);
    }

    /**
     * Return a single referral as JSON.
     */
    public function show($id)
    {
        $referral = Referral::findOrFail($id);

        return response()->json([
            'success'  => true,
            'referral' => $referral,
        ]);
    }

    /**
     * Update an existing referral.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $referral = Referral::findOrFail($id);

        // Check for duplicate name globally (excluding current record)
        $exists = Referral::where('id', '!=', $id)
            ->whereRaw('LOWER(name) = ?', [strtolower(trim($request->name))])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A referral with this name already exists.',
            ], 422);
        }

        $referral->update([
            'name' => trim($request->name),
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Referral updated successfully.',
            'referral' => $referral->fresh(),
        ]);
    }

    /**
     * Delete a referral.
     */
    public function destroy($id)
    {
        $referral = Referral::findOrFail($id);
        $referral->delete();

        return response()->json([
            'success' => true,
            'message' => 'Referral deleted successfully.',
        ]);
    }

    /**
     * Referral report — all referrals listed; selecting one shows
     * students from the current session who have that referral.
     */
    public function report(Request $request)
    {
        $currentSession = session('current_session');
        $referrals      = Referral::orderBy('name')->get();

        $selectedReferral = $request->query('referral');
        $students         = collect();
        $totalCount       = 0;

        if ($selectedReferral) {
            $students = \App\Models\StudentDetail::where('exam_session', $currentSession)
                ->where('referral', $selectedReferral)
                ->select('student_id', 'first_name', 'surname', 'uci_number', 'email', 'contact_number', 'referral')
                ->get()
                ->unique('student_id')
                ->values();

            $totalCount = $students->count();
        }

        return view('referrals.report', compact('referrals', 'selectedReferral', 'students', 'totalCount', 'currentSession'));
    }
}
