<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TagController extends Controller
{
    public function index(): View
    {
        return view('admin.tags.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = Tag::withCount('posts');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        return DataTables::of($query)
            ->addColumn('actions', fn (Tag $tag) => '
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-primary" onclick="editTag('.$tag->id.', \''.e($tag->name).'\')"><i class="bi bi-pencil"></i></button>
                    <form action="'.route('admin.tags.destroy', $tag).'" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this tag?\')">
                        '.csrf_field().method_field('DELETE').'
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            ')
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:100|unique:tags,name']);

        Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return back()->with('success', 'Tag created!');
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:100|unique:tags,name,' . $tag->id]);

        $tag->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return back()->with('success', 'Tag updated!');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();
        return back()->with('success', 'Tag deleted!');
    }
}
