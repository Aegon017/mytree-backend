<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    public function __construct()
    {
        $this->view = 'Admin.';
    }
    public function index()
    {
        $faqs = FAQ::all();

        return view(
            $this->view . 'faqs.index',
            compact('faqs')
        );
    }

    public function create()
    {
        return view(
            $this->view . 'faqs.create'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        FAQ::create($request->all());

        return redirect()->route('faqs.index')->with('success', 'FAQ added successfully.');
    }

    public function edit(FAQ $faq)
    {
        return view(
            $this->view . 'faqs.edit',
            compact('faq')
        );
    }

    public function update(Request $request, FAQ $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq->update($request->all());

        return redirect()->route('faqs.index')->with('success', 'FAQ updated successfully.');
    }

    public function destroy(FAQ $faq)
    {
        $faq->delete();

        return redirect()->route('faqs.index')->with('success', 'FAQ deleted successfully.');
    }
}
