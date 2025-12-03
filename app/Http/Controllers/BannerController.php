<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();

        if ($request->has('placement')) {
            $query->where('placement', $request->get('placement'));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $banners = $query->orderBy('display_order')->paginate(20);

        return response()->json($banners);
    }

    public function show(Banner $banner)
    {
        return response()->json($banner);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:1024',
            'image' => 'nullable|image|max:5120',
            'alt_text' => 'nullable|string|max:255',
            'placement' => 'required|string|max:50',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'is_active' => 'sometimes|boolean',
            'display_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('banners', 'public');
            $data['image_url'] = Storage::url($path);
        }

        $banner = Banner::create($data);

        return response()->json($banner, 201);
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:1024',
            'image' => 'nullable|image|max:5120',
            'alt_text' => 'nullable|string|max:255',
            'placement' => 'nullable|string|max:50',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'is_active' => 'sometimes|boolean',
            'display_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            // delete old file if exists
            if ($banner->image_url) {
                $parsed = parse_url($banner->image_url);
                $path = ltrim($parsed['path'] ?? '', '/storage/');
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            $path = $request->file('image')->store('banners', 'public');
            $data['image_url'] = Storage::url($path);
        }

        $banner->update($data);

        return response()->json($banner);
    }

    public function destroy(Banner $banner)
    {
        // Optionally delete image file from storage
        if ($banner->image_url) {
            $parsed = parse_url($banner->image_url);
            $path = ltrim($parsed['path'] ?? '', '/storage/');
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $banner->delete();

        return response()->json(['message' => 'Deleted'], 200);
    }
}
