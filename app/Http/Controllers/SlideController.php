<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;

class SlideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $slide = Slide::orderBy('slide_order', 'asc')->where('action', 1)->get();
        if (!$slide) {
            return response()->json([
                'success' => false,
                'message' => 'Not found slide'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $slide
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slide_id = null)
    {
        $get_slide = Slide::where('id', $slide_id)->first();
        if ($slide_id) {
            if ($get_slide) {
                return response()->json([
                    'success' => true,
                    'data' => $get_slide
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'slide not found'
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'The slide ID must not be null '
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
