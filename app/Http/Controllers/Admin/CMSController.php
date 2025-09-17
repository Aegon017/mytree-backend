<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CMSPage;
use Illuminate\Http\Request;

class CMSController extends Controller
{
    public function __construct()
    {
        $this->view = 'Admin.';
    }

    public function index()
    {
        $cmsPages = CMSPage::all();
        return view(
            $this->view . 'cms_management',
            compact('cmsPages')
        );
    }

    public function create()
    {
        $page = new CMSPage();
        return view(
            $this->view . 'edit_cms',
            compact('page')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:cms_pages,slug',
            'content' => 'required',
        ]);

        CMSPage::create($request->all());
        return redirect()->route('cms.index')->with('success', 'CMS Page created successfully.');
    }

    public function edit($id)
    {
        $page = CMSPage::findOrFail($id);
        return view(
            $this->view . 'edit_cms',
            compact('page')
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:cms_pages,slug,' . $id,
            'content' => 'required',
        ]);

        $page = CMSPage::findOrFail($id);
        $page->update($request->all());
        return redirect()->route('cms.index')->with('success', 'CMS Page updated successfully.');
    }

    public function destroy($id)
    {
        $page = CMSPage::findOrFail($id);
        $page->delete();
        return redirect()->route('cms.index')->with('success', 'CMS Page deleted successfully.');
    }
}
