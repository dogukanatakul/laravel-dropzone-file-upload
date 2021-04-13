<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class FileManagerController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            // Get file extension
            $extension = $request->file('file')->getClientOriginalExtension();
            if (!$extension) {
                return response()->json(['error' => 'This file structure is not supported!'], 500);
            }

            // Valid extensions
            $validextensions = array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'mdb', 'mdbx', 'avi', 'mp4', 'mkv', 'wmv', 'flv', '3gp', 'dat', 'mov', 'ogg', 'mp3', 'wav', 'mid', 'jpeg', 'jpg', 'png', 'bmp', 'psd', 'gif', 'pdf', 'rar', 'zip', 'tar', '7z', 'txt', 'odt', 'ott', 'rtf', 'uot', 'dic');
            // Check extension
            if (in_array(strtolower($extension), $validextensions)) {

                $uid = Uuid::uuid4();
                // Rename file
                $fileName = $uid . '.' . $extension;
                // Uploading file to given path

                $request->file('file')->storeAs('/', $fileName, 'evraklar');

                $url = Storage::disk('evraklar')->url($fileName);
                $size = Storage::disk('evraklar')->size($fileName);
                // SHELL CONTROL START
                $get = Storage::disk('evraklar')->get($fileName);
                if (strstr($get, "<?")) {
                    return response()->json(['error' => 'Shell file detected!'], 500);
                }
                // SHELL CONTROL FINISH


            } else {
                return response()->json(['error' => 'This file structure is not supported!'], 500);
            }
            return response()->json([
                'success' => $request->file('file')->getClientOriginalName(),
                'url' => $url,
                'size' => $size,
                'uuid' => $uid,
                'ext' => $extension,
            ]);
        }
        return response()->json(['error' => 'This file structure is not supported!'], 500);
    }
}
