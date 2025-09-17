<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\RestfulTrait;
use Illuminate\Http\Request;
use App\Models\Admin\Contact;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

/**
 * @category	Controller
 * @package		Contact
 * @author		Harish Mogilipuri
 * @license
 * @link
 *
 */

class ContactController extends Controller
{
    use RestfulTrait;
    public function __construct()
    {
        $this->view = 'Admin.';
    }
    public function index(Request $request)
    {
        $contact = Contact::select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));

        $contact = $contact->orderBy('id', 'DESC')->get();
        return DataTables::of($contact)
            ->addColumn('srno', function ($contact) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $contact->id .  '"/> ' . $contact->rownum;
            })
            ->rawColumns(['srno'])
            ->make(true);
    }


    public function manageContact()
    {
        $All = Contact::count();
        return view(
            $this->view . 'contact',
            compact(['All'])
        );
    }
}
