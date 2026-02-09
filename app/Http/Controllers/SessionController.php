<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session as FacadesSession;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
         $user = auth()->user();

        // Check if any required profile field is missing
        if (!$user->branch_name || !$user->branch_address || !$user->branch_email ||!$user->branch_phone) {
            // Redirect to profile edit page with a message
            return redirect()->route('profile.edit')->with('error', 'Please Fill Branch Information to access results.');
        }
        $wdays = Session::orderByDesc('id')->get();

        return view('sessions.index', ['wdays' => $wdays]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $wdays = Session::all();

        return view('sessions.create', compact('wdays'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'Session' => 'required|string',
            'term_one_working_days' => 'required',
            'term_two_working_days' => 'required',
        ]);
        // dd($request->all());
        $session = Session::where('title',$request->Session)->first();

        if ($session) {
    return redirect()
        ->route('sessions.index')
        ->with('error', 'Session with this name already exists.');
}


        $wdays = new Session;
        $wdays->title = $request->Session;
        $wdays->t1_working_days = $request->term_one_working_days;
        $wdays->t2_working_days = $request->term_two_working_days;
        $wdays->save();
        

        return redirect()->route('sessions.index')->with('success', 'Session Saved Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Session $session)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Session $session)
    
    {
        // dd($session);
        $wdays = Session::all();
        return view('sessions.edit', compact('session', 'wdays'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Session $session)
{
    // Validate the input
    $request->validate([
        'title' => 'required',
        't1_working_days' => 'required|numeric',
        't2_working_days' => 'required|numeric',
    ]);

    // Update the session
    $session->update([
        'title' => $request->title,
        't1_working_days' => $request->t1_working_days,
        't2_working_days' => $request->t2_working_days,
    ]);

    // Redirect back to index with success message
    return redirect()->route('sessions.index')->with('success', 'Session updated successfully!');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Session $session)
    {
        $session->delete();
        return redirect()->route('sessions.index')->with('succes','Session Deletedd Successfully');
        
    }
    public function session_working_days(Request $request)
{
    try {
        $sessionId = $request->session_id;

        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID is required',
            ], 400);
        }

        $session = Session::find($sessionId);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'session_id' => $session->id,
                'title' => $session->title,
                't1_working_days' => $session->t1_working_days ?? 0,
                't2_working_days' => $session->t2_working_days ?? 0,
            ],
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching session working days: ' . $e->getMessage(),
        ], 500);
    }
}
public function session_search(Request $request){
    $search = $request->search;
    $year = Session::where('created_by',auth()->user()->id())
    ->where(function($year) use($search){
        $year->where('title' ,'LIKe', "%{$search}%")
        ->where('t1_working_days' ,'LIKe', "%{$search}%")
        ->where('t2_working_days' ,'LIKe', "%{$search}%");
    })
    ->get();
    return response()->json($year);
}
}

