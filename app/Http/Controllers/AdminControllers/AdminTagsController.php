<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\Http\Response;

class AdminTagsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tags = Tag::with('posts')->select(['id', 'name', 'created_at'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($qr) use ($search) {
                    $qr->where('name', 'LIKE', "%$search%");
                });
            })
            ->orderBy('id', 'ASC')->paginate(50);
        return view('admin_dashboard.tags.index', compact('tags', 'search'));
        // return view('admin_dashboard.tags.index', [
        //     'tags' => Tag::with('posts')->paginate(50),
        //     'search' => $search,
        // ]);
    }

    public function show(Tag $tag)
    {
        return view('admin_dashboard.tags.show', [
            'tag' => $tag
        ]);
    }

    public function destroy(Request $request)
    {
        try {
            $tagId = $request->input('id');
            if (empty($tagId)) {
                return response()->json([1], Response::HTTP_BAD_REQUEST);
            }
            $tag = Tag::where('id', $tagId)->first();
            if (empty($tag)) {
                return response()->json([2], Response::HTTP_BAD_REQUEST);
            }
            $tag->posts()->detach();
            $tag->delete();
            return response()->json([], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([3], Response::HTTP_BAD_REQUEST);
        }
    }

    //delete all
    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        Tag::whereIn('id', $ids)->delete();
        return response()->json(["success" => "Đã xoá các từ khoá chọn thành công"]);
    }
}
