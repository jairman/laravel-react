<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contact\StoreRequest;
use App\Http\Requests\Contact\UpdateRequest;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = Contact::where('user_id', auth()->id())->get();

        return Inertia::render('Contact/Index', compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return  Inertia::render('Contact/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->only('name', 'phone', 'visibility');
        $data['user_id'] = auth()->id();
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $routeImage = $file->store('avatars', ['disk' => 'public']);
            $data['avatar'] = $routeImage;
        }
        Contact::create($data);
        return to_route('contacts.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        return Inertia::render('Contact/Edit', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Contact $contact)
    {
        $data = $request->only('name', 'phone', 'visibility');
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $routeImage = $file->store('avatars', ['disk' => 'public']);
            $data['avatar'] = $routeImage;

            //eliminar el avatar anterior
            if ($contact->avatar) {
                //unlink(storage_path('app/public/' . $contact->avatar));
                Storage::disk('public')->delete($contact->avatar);
            }
        }
        $contact->update($data);
        return to_route('contacts.edit', $contact);
       // return to_route('contacts.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        if ($contact->avatar) {
            //unlink(storage_path('app/public/' . $contact->avatar));
            Storage::disk('public')->delete($contact->avatar);
        }
        $contact->delete();
        return to_route('contacts.index');
    }
}
