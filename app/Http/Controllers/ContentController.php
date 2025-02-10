<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class ContentController extends Controller
{
    public function create($id)
    {
    
        return view('content_create',compact('id'));
        
    }
    public function store(Request $request, $id)
    {
        // Validate the request data
        $cre_id = Crypt::decrypt($id);

        $validator = Content::validate($request->all());
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle file upload if Media type is selected
        $value = null;
        if ($request->type === 'Media' && $request->hasFile('value')) {
            $file = $request->file('value');
            $value = $file->store('media', 'public'); // Save the file in the "public/media" directory
        }
        
        // Create the Content and save to the database
        Content::create([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $value, // Store the file path
            'cre_id'=> $cre_id,
        ]);

        return redirect()->route('content.create',['id' => $id])->with('success', 'Content created successfully!');
    }
}
