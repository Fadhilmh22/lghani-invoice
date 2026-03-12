<?php

namespace App\Http\Controllers;

use App\Models\Airlines;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;



class AirlinesController extends Controller
{
    public function index(Request $request)
    {
        $additionalData = Airlines::when($request->search, function($query) use ($request) {
            $query->where('airlines_code', 'like', "%{$request->search}%")
                  ->orWhere('airlines_name', 'like', "%{$request->search}%");
        })->orderBy('created_at', 'DESC')->paginate(10);

        return view('airlines.index', compact('additionalData'));
    }

    public function create()
    {
        return view('airlines.create');
    }

    public function save(Request $request)
    {
        // Normalize code to uppercase so database always stores uppercase codes
        $request->merge(['airlines_code' => strtoupper($request->airlines_code)]);

$this->validate($request, [
            'airlines_code' => 'required|string|max:3',
            'airlines_name' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $code = $request->airlines_code;
            $fileName = $code . '.jpg';
            $filePath = public_path('airlines-logo/' . $fileName);
            $sourceImage = @imagecreatefromstring(file_get_contents($request->file('logo')->getRealPath()));
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);
            $newWidth = 100;
            $newHeight = (int) ($height * $newWidth / $width);
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            $white = imagecolorallocate($resizedImage, 255, 255, 255);
            imagefill($resizedImage, 0, 0, $white);
            imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagejpeg($resizedImage, $filePath, 90);
            imagedestroy($sourceImage);
            imagedestroy($resizedImage);
            $logoPath = 'airlines-logo/' . $fileName;
        }
        try {
            $airlines = Airlines::create([
                'airlines_code' => $request->airlines_code,
                'airlines_name' => $request->airlines_name,
                'logo_path' => $logoPath
            ]);

            $redirect = $request->input('redirect_url') ?? '/airline';
            return redirect($redirect)->with(['success' => '<strong>' . $airlines->airlines_code . '</strong> Telah disimpan']);
        } catch(\Exception $e) {
            return redirect('/airline/new')->with(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $airlines = Airlines::find($id);
        return view('airlines.edit', compact('airlines'));
    }

    public function update(Request $request, $id)
    {
        $airlines = Airlines::find($id);
        // Ensure code is stored uppercase
        $request->merge(['airlines_code' => strtoupper($request->airlines_code)]);

        $this->validate($request, [
            'airlines_code' => 'required|string|max:3',
            'airlines_name' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);


        // Logo handling for update
        if ($request->hasFile('logo') && $airlines->logo_path) {
            File::delete(public_path($airlines->logo_path));
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $code = $request->airlines_code;
            $fileName = $code . '.jpg';
            $filePath = public_path('airlines-logo/' . $fileName);
            $sourceImage = @imagecreatefromstring(file_get_contents($request->file('logo')->getRealPath()));
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);
            $newWidth = 100;
            $newHeight = (int) ($height * $newWidth / $width);
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            $white = imagecolorallocate($resizedImage, 255, 255, 255);
            imagefill($resizedImage, 0, 0, $white);
            imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagejpeg($resizedImage, $filePath, 90);
            imagedestroy($sourceImage);
            imagedestroy($resizedImage);
            $logoPath = 'airlines-logo/' . $fileName;
        }

        $airlines->update([
            'airlines_code' => $request->airlines_code,
            'airlines_name' => $request->airlines_name,
            'logo_path' => $logoPath
        ]);


        $redirect = $request->input('redirect_url') ?? '/airline';
        return redirect($redirect)->with(['success' => '<strong>' . $airlines->airlines_code . '</strong> Diperbaharui']);
    }

    public function destroy($id)
    {
        $airlines = Airlines::find($id);
        $airlines->delete();
        return redirect('/airline')->with(['success' => '</strong>' . $airlines->airlines_code . '</strong> Dihapus']);
    }
}

