<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;

use App\Models\Role;
use App\Models\User;


class AdminUsersController extends Controller
{
    private $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|max:20',
        'image' => 'nullable|file|mimes:jpg,png,webp,svg,jpeg|dimensions:max-width:300,max-height:300',
        'role_id' => 'required|numeric'
    ];

    public function index(Request $request)
    {
        // dd(User::
        // select([
        //     'users.id as user_id',
        //     'images.path as user_image',
        //     'users.name as user_name',
        //     'users.email as user_email',
        //     'users.role_id',
        //     'users.created_at as user_created_at'
        // ])->
        // with('role')->leftJoin('images', 'users.id', 'images.imageable_id')
        // ->orderBy('images.created_at', 'desc')
        // ->orderBy('users.id', 'desc')
        // ->paginate(20)->toArray());

        //Search
        $search = $request->input('search');
        $users = User::select([
            'users.id as user_id',
            'images.path as user_image',
            'users.name as user_name',
            'users.email as user_email',
            'users.role_id',
            'users.created_at as user_created_at'
        ])->with('role')->leftJoin('images', 'users.id', 'images.imageable_id')->leftJoin('roles', 'users.role_id', 'roles.id')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($qr) use ($search) {
                    $qr->where('users.name', 'LIKE', "%$search%")
                        ->orWhere('users.email', 'LIKE', "%$search%")
                        ->orWhere('roles.name', 'LIKE', "%$search%");
                });
            })
            ->orderBy('users.id', 'asc')
            ->groupBy('user_id')->paginate(20);


        return view('admin_dashboard.users.index', compact('users', 'search'));
        // return view('admin_dashboard.users.index', [
        //     'users' => User::select([
        //         'users.id as user_id',
        //         'images.path as user_image',
        //         'users.name as user_name',
        //         'users.email as user_email',
        //         'users.role_id',
        //         'users.created_at as user_created_at'
        //     ])->with('role')->leftJoin('images', 'users.id', 'images.imageable_id')
        //         ->when($search, function ($query) use ($search) {
        //             $query->where(function ($qr) use ($search) {
        //                 $qr->where('users.name', 'LIKE', "%$search%")
        //                     ->orWhere('users.email', 'LIKE', "%$search%");
        //             });
        //         })
        //         ->orderBy('users.id', 'asc')
        //         ->groupBy('user_id')
        //         ->paginate(20),
        //     'search' => $search,
        // ]);
    }


    public function create()
    {
        return view('admin_dashboard.users.create', [
            'roles' => Role::pluck('name', 'id'),
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate($this->rules);
        $validated['password'] = Hash::make($request->input('password'));
        $user = User::create($validated);

        if ($request->has('image')) {
            $image = $request->file('image');
            $filename = $image->getClientOriginalName();
            $file_extension = $image->getClientOriginalExtension();
            $path   = $image->store('images', 'public');

            $user->image()->create([
                'name' => $filename,
                'extension' => $file_extension,
                'path' => $path
            ]);
        }

        return redirect()->route('admin.users.create')->with('success', 'Thêm tài khoản thành công.');
    }


    public function edit(User $user)
    {
        $image = Image::select('path')->where('imageable_id', $user->id)->orderBy('created_at', 'desc')->first();
        return view('admin_dashboard.users.edit', [
            'user' => $user,
            'image_path' => $image,
            'roles' => Role::pluck('name', 'id'),
        ]);
    }

    public function show(User $user)
    {
        return view('admin_dashboard.users.show', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->rules['password'] = 'nullable|min:3|max:20';
        $this->rules['email'] = ['required', 'email', Rule::unique('users')->ignore($user)];

        $validated = $request->validate($this->rules);

        if ($validated['password'] === null)
            unset($validated['password']);
        else
            $validated['password'] = Hash::make($request->input('password'));

        $user->update($validated);
        if (!empty($request->image)) {
            $image = $request->file('image');
            $filename = $image->getClientOriginalName();
            $file_extension = $image->getClientOriginalExtension();
            $path   = $image->store('images', 'public');
            Image::where('imageable_id', $user->id)->delete();
            $hh = $user->image()->create([
                'name' => $filename,
                'extension' => $file_extension,
                'path' => $path
            ]);
        }

        return redirect()->route('admin.users.edit', $user)->with('success', 'Sửa tài khoản thành công.');
    }


    public function destroy(Request $request)
    {
        try {
            $userId = $request->input('id');
            if (empty($userId)) {
                return response()->json([1], Response::HTTP_BAD_REQUEST);
            }
            $user = User::where('id', $userId)->first();
            if (empty($user)) {
                return response()->json([2], Response::HTTP_BAD_REQUEST);
            }

            if ($user->id === auth()->id())
                return redirect()->back()->with('error', 'Bạn không thể xóa tài khoản bạn ( quản trị viên) ');

            User::whereHas('role', function ($query) {
                $query->where('name', 'admin');
            })->first()->posts()->saveMany($user->posts);

            $user->delete();
            return response()->json([], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([3], Response::HTTP_BAD_REQUEST);
        }
    }

    //delete all
    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        User::whereIn('id', $ids)->delete();
        return response()->json(["success" => "Đã xoá các tài khoản thành công"]);
    }
}
