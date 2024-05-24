<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;


class TemporaryController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        $folder = Auth::user()->creatorId() . '-' . 'case-doc';
        $filename = $request->getContent();
        
        $filePath = public_path('storage/uploads/case_docs/tmp/' . $folder . '/' . $filename);

        if (File::exists($filePath)) {
            File::delete($filePath);
        } else {
        }

    }
}
