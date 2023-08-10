<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Response;

class AdminRolesController extends Controller
{

    private $rules = ['name' => 'required|unique:roles,name'];

    public function index(Request $request)
    {
        $search = $request->input('search');
        $roles = Role::select(['id', 'name', 'created_at'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($qr) use ($search) {
                    $qr->where('name', 'LIKE', "%$search%");
                });
            })
            ->orderBy('id', 'ASC')->paginate(20);
        return view('admin_dashboard.roles.index', compact('roles', 'search'));
        // return view('admin_dashboard.roles.index', [
        //     'roles' => Role::paginate(20),
        // ]);
    }

    public function create()
    {
        return view('admin_dashboard.roles.create', [
            'permissions' => Permission::all(),
        ]);
    }


    public function store(Request $request)
    {


        $validated = $request->validate($this->rules);
        $permissions = $request->input('permissions');


        $role = Role::create($validated);
        $role->permissions()->sync($permissions);

        return redirect()->route('admin.roles.create')->with('success', 'Thêm quyền mới thành công.');
    }


    public function edit(Role $role)
    {
        return view('admin_dashboard.roles.edit', [
            'role' => $role,
            'permissions' => Permission::all(),
        ]);
    }


    public function update(Request $request, Role $role)
    {

        $this->rules['name'] = ['required', Rule::unique('roles')->ignore($role)];
        $validated = $request->validate($this->rules);
        $permissions = $request->input('permissions');


        $role->update($validated);
        $role->permissions()->sync($permissions);

        return redirect()->route('admin.roles.edit', $role)->with('success', 'Cập nhật quyền mới thành công.');
    }

    public function destroy(Request $request)
    {
        try {
            $roleId = $request->input('id');
            if (empty($roleId)) {
                return response()->json([1], Response::HTTP_BAD_REQUEST);
            }
            $role = Role::where('id', $roleId)->first();
            if (empty($role)) {
                return response()->json([2], Response::HTTP_BAD_REQUEST);
            }
            $role->delete();
            return response()->json([], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([3], Response::HTTP_BAD_REQUEST);
        }
        // return redirect()->route('admin.roles.index')->with('success', 'Xóa quyền thành công.');
    }

     //delete all
     public function deleteAll(Request $request){
        $ids = $request->ids;
        Role::whereIn('id',$ids)->delete();
        return response()->json(["success" => "Đã xoá các quyền chọn thành công"]);
    }
}
