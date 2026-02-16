<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\About as ResourcesAbout;
use App\Models\About as ModelsAbout;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class About extends Controller
{
    public function index()
    {
        $about = ModelsAbout::orderBy('created_at','desc')->get();
        if (empty($about)) {
            return response()->json([
                'success' => false,
                'message' => 'Data About Belum ada'
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => ResourcesAbout::collection($about)
            ], 200);
        }
    }
    public function store(Request $request)
    {
        if (ModelsAbout::count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'About sudah ada, tidak bisa menambah lagi'
            ], 400);
        }
        $validator = Validator::make($request->all(), [
            'headline'    => 'required|string|max:255',
            'title'       => 'required|string|max:255',
            'subtitle'    => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:wep,png,jpg|max:2048',
            'paragraf'    => 'required|string|max:255',
            'image_visi'  => 'nullable|image|mimes:wep,png,jpg|max:2048',
            'icon'      => 'nullable|array',
            'icon.*'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'power' => 'nullable|array',
            'power.*' => 'required|string|max:255',
            'visi_misi'   => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }
      
        $data = $validator->validate();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('about', 'public');
        }
        if ($request->hasFile('image_visi')) {
            $data['image_visi'] = $request->file('image_visi')->store('about', 'public');
        }
        if ($request->hasFile('icon')) {
            $paths = [];
            foreach ($request->file('icon') as $img) {
                $paths[] = $img->store('about', 'public');
            }
            $data['icon'] = $paths;
        }

        $about = ModelsAbout::create($data);

        return response()->json([
            'success' => true,
            'message' => 'About berhasil dibuat',
            'data' => new ResourcesAbout($about)
        ], 201);
    }

    public function show($slug)
    {
        $about = ModelsAbout::where('slug', $slug)
            ->firstOrFail();

        if (empty($about)) {
            return response()->json([
                'success' => false,
                'message' => 'Data About Belum ada'
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'data'    => new ResourcesAbout($about)
            ], 200);
        }
    }

    public function update(Request $request, ModelsAbout $about)
    {
        $validator = Validator::make($request->all(), [
            'headline'    => 'sometimes|string|max:255',
            'title'       => 'sometimes|string|max:255',
            'subtitle'    => 'sometimes|string|max:255',
            'image'       => 'sometimes|image|mimes:wep,png,jpg|max:2048',
            'paragraf'    => 'sometimes|string|max:255',
            'image_visi'  => 'sometimes|image|mimes:wep,png,jpg|max:2048',
            'icon'      => 'sometimes|array',
            'icon.*'      => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'power' => 'sometimes|array',
            'power.*' => 'sometimes|string|max:255',
            'visi_misi'   => 'sometimes|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }
        $data = $validator->validate();
        if ($request->hasFile('image')) {
            if ($about->image && Storage::disk('public')->exists($about->image)) {
                Storage::disk('public')->delete($about->image);
            }
            $data['image'] = $request->file('image')->store('about', 'public');
        }
        if ($request->hasFile('image_visi')) {
            if ($about->image_visi && Storage::disk('public')->exists($about->image_visi)) {
                Storage::disk('public')->delete($about->image_visi);
            }
            $data['image_visi'] = $request->file('image_visi')->store('about', 'public');
        }

        if ($request->hasFile('icon')) {
            // delete old
            if ($about->icon) {
                foreach ($about->icon as $old) {
                    Storage::disk('public')->delete($old);
                }
            }

            $newImages = [];
            foreach ($request->file('icon') as $img) {
                $newImages[] = $img->store('about', 'public');
            }
            $data['icon'] = $newImages;
        }

        $about->update($data);
        return response()->json([
            'success' => true,
            'message' => 'About berhasil diupadte',
            'data' => new ResourcesAbout($about)
        ], 201);
    }

    public function destroy(ModelsAbout $about)
    {
        if (!empty($about->image) && Storage::disk('public')->exists($about->image)) {
            Storage::disk('public')->delete($about->image);
        }
          if (!empty($about->image_visi) && Storage::disk('public')->exists($about->image_visi)) {
            Storage::disk('public')->delete($about->image_visi);
        }
        if (!empty($about->icon) && is_array($about->icon)) {
            foreach ($about->icon as $img) {
                if (Storage::disk('public')->exists($img)) {
                    Storage::disk('public')->delete($img);
                }
            }
        }

        $about->delete();
        return response()->json([
            'success' => true,
            'message' => 'About berhasil dihapus'
        ], 200);
    }
}
