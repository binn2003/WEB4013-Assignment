<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Contact;
use Illuminate\Http\Response;

class AdminContactsController extends Controller
{
    public function index()
    {
        return view('admin_dashboard.contacts.index', [
            'contacts' => Contact::all(),
        ]);
    }

    public function destroy(Request $request)
    {
        try {
            $contactId = $request->input('id');
            if (empty($contactId)) {
                return response()->json([1], Response::HTTP_BAD_REQUEST);
            }
            $contact = Contact::where('id', $contactId)->first();
            if (empty($contact)) {
                return response()->json([2], Response::HTTP_BAD_REQUEST);
            }
            $contact->delete();
            return response()->json([], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([3], Response::HTTP_BAD_REQUEST);
        }
        // return redirect()->route('admin.contacts')->with('success', 'Xóa liên hệ thành công');
    }
}
