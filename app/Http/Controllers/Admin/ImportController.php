<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ImportController extends Controller
{
    public function index()
    {
        return view('admin.import.index');
    }

    public function import(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        $file = $request->file('file');
        $imported = 0;
        $errors = [];

        if ($file->getClientOriginalExtension() === 'csv' || $file->getClientOriginalExtension() === 'txt') {
            $handle = fopen($file->getPathname(), 'r');
            $header = fgetcsv($handle);

            // Map expected columns
            $headerMap = array_flip(array_map(function ($h) {
                return trim(mb_strtolower($h));
            }, $header ?? []));

            $requiredColumns = ['name', 'email'];
            foreach ($requiredColumns as $col) {
                if (!isset($headerMap[$col])) {
                    fclose($handle);
                    return back()->withErrors(['file' => "العمود '$col' مطلوب في ملف CSV"]);
                }
            }

            $rowNumber = 1;
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                $data = [];
                foreach ($header as $i => $h) {
                    $key = trim(mb_strtolower($h));
                    $data[$key] = $row[$i] ?? '';
                }

                try {
                    if (empty($data['email']) || User::where('email', $data['email'])->exists()) {
                        continue;
                    }

                    $userData = [
                        'name' => $data['name'] ?? '',
                        'email' => $data['email'] ?? '',
                        'password' => Hash::make($data['password'] ?? 'password'),
                        'phone' => $data['phone'] ?? null,
                        'national_id' => $data['national_id'] ?? null,
                        'governorate' => $data['governorate'] ?? null,
                        'university' => $data['university'] ?? null,
                        'faculty' => $data['faculty'] ?? null,
                        'graduation_year' => $data['graduation_year'] ?? null,
                        'job_status' => $data['job_status'] ?? null,
                        'address' => $data['address'] ?? null,
                        'role' => 'user',
                        'approval_status' => 'pending',
                    ];

                    // Filter out nulls
                    $userData = array_filter($userData, function ($v) { return $v !== null; });

                    User::create($userData);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "صف $rowNumber: " . $e->getMessage();
                }
            }

            fclose($handle);
        }

        $message = "تم استيراد $imported خريج بنجاح";
        if (count($errors) > 0) {
            $message .= ' مع ' . count($errors) . ' خطأ';
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'errors' => $errors,
            ]);
        }

        return back()->with('success', $message)->with('import_errors', $errors);
    }
}
