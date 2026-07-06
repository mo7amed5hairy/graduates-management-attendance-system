<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\Qualification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    public function index()
    {
        return view('admin.import.index');
    }

    public function import(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:20480',
        ]);

        $file = $request->file('file');
        $imported = 0;
        $errors = [];
        $ext = $file->getClientOriginalExtension();

        if (in_array($ext, ['xlsx', 'xls'])) {
            $result = $this->importFromExcel($file, $errors, $imported);
        } else {
            $result = $this->importFromCsv($file, $errors, $imported);
        }

        if (!$result) {
            return back()->withErrors(['file' => 'تعذر قراءة الملف. تأكد من أن الملف بصيغة صحيحة.']);
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

    private function importFromExcel($file, array &$errors, int &$imported): bool
    {
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
        } catch (\Exception $e) {
            $errors[] = 'تعذر قراءة ملف Excel: ' . $e->getMessage();
            return false;
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);
        $totalRows = count($rows);

        if ($totalRows < 2) {
            $errors[] = 'الملف لا يحتوي على بيانات كافية';
            return false;
        }

        $qualifications = Qualification::pluck('id', 'name')->toArray();
        $governorates = Governorate::pluck('id', 'name')->toArray();

        // Expected columns (0-indexed):
        // 0: مسلسل | 1: الحالة | 2: الاسم الكامل مع اللقب | 3: العمر | 4: الجنس
        // 5: عنوان السكن الحالي | 6: سنة تخرج | 7: التحصيل الدراسي | 8: رقم الهاتف
        // 9: اسم الام الرباعي | 10: الحالة الاجتماعية

        for ($i = 1; $i < $totalRows; $i++) {
            $row = $rows[$i];
            $rowNum = $i + 1;
            $serial = trim((string) ($row[0] ?? ''));

            if (empty($serial) || !is_numeric($serial)) {
                continue;
            }

            try {
                $fullName = trim((string) ($row[2] ?? ''));
                if (empty($fullName)) {
                    continue;
                }

                $phoneRaw = preg_replace('/[^0-9]/', '', (string) ($row[8] ?? ''));
                if (!empty($phoneRaw) && User::where('phone', $phoneRaw)->exists()) {
                    continue;
                }

                $nameParts = $this->parseFullName($fullName);
                $motherParts = $this->parseMotherName(trim((string) ($row[9] ?? '')));

                $qualText = trim((string) ($row[7] ?? ''));
                $qualId = $this->findQualificationId($qualText, $qualifications);

                $gender = $this->normalizeGender(trim((string) ($row[4] ?? '')));
                $socialStatus = $this->normalizeSocialStatus(trim((string) ($row[10] ?? '')));
                $gradYear = trim((string) ($row[6] ?? ''));
                $gradYear = is_numeric($gradYear) ? (int) $gradYear : null;

                if ($gradYear && ($gradYear < 1950 || $gradYear > (int) date('Y') + 5)) {
                    $gradYear = null;
                }

                $age = trim((string) ($row[3] ?? ''));
                $age = is_numeric($age) ? (int) $age : null;
                $birthYear = $age ? (int) date('Y') - $age : null;

                $address = trim((string) ($row[5] ?? ''));
                $govName = $this->extractGovernorates($address, $governorates);

                // Column 1 = الحالة (status: مقبول / مرفوض / قيد المراجعة)
                $statusText = trim((string) ($row[1] ?? ''));
                $approvalStatus = 'approved';
                if (mb_strpos($statusText, 'مرفوض') !== false) {
                    $approvalStatus = 'rejected';
                } elseif (mb_strpos($statusText, 'قيد') !== false || mb_strpos($statusText, 'مراجعة') !== false) {
                    $approvalStatus = 'pending';
                }

                $email = 'user_' . ($phoneRaw ?: uniqid()) . '@system.local';
                $nationalId = null;

                $userData = array_merge($nameParts, $motherParts, [
                    'name' => $fullName,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'phone' => $phoneRaw ?: null,
                    'national_id' => $nationalId,
                    'governorate' => $govName,
                    'address' => $address ?: null,
                    'gender' => $gender,
                    'social_status' => $socialStatus,
                    'age' => $age,
                    'date_of_birth' => $birthYear,
                    'graduation_year' => $gradYear,
                    'qualification_id' => $qualId,
                    'job_status' => 'غير موظف',
                    'role' => 'user',
                    'approval_status' => $approvalStatus,
                    'approved_at' => ($approvalStatus === 'approved') ? now() : null,
                    'approved_by' => ($approvalStatus === 'approved') ? auth()->id() : null,
                    'access_token' => bin2hex(random_bytes(32)),
                    'status' => 'active',
                    'points' => 0,
                ]);

                User::create($userData);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "صف $rowNum: " . $e->getMessage();
            }
        }

        return true;
    }

    private function importFromCsv($file, array &$errors, int &$imported): bool
    {
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            $errors[] = 'تعذر فتح ملف CSV';
            return false;
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            $errors[] = 'الملف فارغ';
            return false;
        }

        $headerMap = array_flip(array_map(function ($h) {
            return trim(mb_strtolower($h));
        }, $header));

        $requiredColumns = ['name', 'email'];
        foreach ($requiredColumns as $col) {
            if (!isset($headerMap[$col])) {
                fclose($handle);
                $errors[] = "العمود '$col' مطلوب في ملف CSV";
                return false;
            }
        }

        $qualifications = Qualification::pluck('id', 'name')->toArray();
        $governorates = Governorate::pluck('id', 'name')->toArray();

        $rowNumber = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $data = [];
            foreach ($header as $i => $h) {
                $data[trim(mb_strtolower($h))] = $row[$i] ?? '';
            }

            try {
                $email = trim($data['email'] ?? '');
                if (empty($email) || User::where('email', $email)->exists()) {
                    continue;
                }

                $fullName = trim($data['name'] ?? '');
                if (empty($fullName)) continue;

                $nameParts = $this->parseFullName($fullName);
                $motherRaw = trim($data['mother_name'] ?? '');
                $motherParts = $this->parseMotherName($motherRaw);

                $qualText = trim($data['qualification'] ?? $data['qualification_name'] ?? '');
                $qualId = $this->findQualificationId($qualText, $qualifications);

                $address = trim($data['address'] ?? '');
                $govName = $this->extractGovernorates($address, $governorates);
                if (empty($govName) && !empty($data['governorate'])) {
                    $govName = $data['governorate'];
                }

                $gender = $this->normalizeGender(trim($data['gender'] ?? ''));
                $socialStatus = $this->normalizeSocialStatus(trim($data['social_status'] ?? ''));

                $phoneRaw = preg_replace('/[^0-9]/', '', (string) ($data['phone'] ?? ''));
                $gradYear = !empty($data['graduation_year']) ? (int) $data['graduation_year'] : null;
                $age = !empty($data['age']) ? (int) $data['age'] : null;
                $birthYear = $age ? date('Y') - $age : null;

                $userData = array_merge($nameParts, $motherParts, [
                    'name' => $fullName,
                    'email' => $email,
                    'password' => Hash::make($data['password'] ?? 'password'),
                    'phone' => $phoneRaw ?: null,
                    'national_id' => trim($data['national_id'] ?? '') ?: null,
                    'governorate' => $govName ?? null,
                    'address' => $address ?: null,
                    'gender' => $gender,
                    'social_status' => $socialStatus,
                    'age' => $age,
                    'date_of_birth' => $birthYear,
                    'graduation_year' => $gradYear,
                    'qualification_id' => $qualId,
                    'job_status' => trim($data['job_status'] ?? '') ?: 'غير موظف',
                    'university' => trim($data['university'] ?? '') ?: null,
                    'faculty' => trim($data['faculty'] ?? '') ?: null,
                    'role' => 'user',
                    'approval_status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'access_token' => bin2hex(random_bytes(32)),
                    'status' => 'active',
                    'points' => 0,
                ]);

                User::create($userData);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "صف $rowNumber: " . $e->getMessage();
            }
        }

        fclose($handle);
        return true;
    }

    private function parseFullName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName));
        $parts = array_values(array_filter($parts));

        return [
            'first_name' => $parts[0] ?? $fullName,
            'father_name' => $parts[1] ?? '',
            'grandfather_name' => $parts[2] ?? '',
            'family_name' => $parts[3] ?? '',
        ];
    }

    private function parseMotherName(string $motherName): array
    {
        $parts = preg_split('/\s+/', trim($motherName));
        $parts = array_values(array_filter($parts));

        return [
            'mother_name' => $parts[0] ?? $motherName,
            'mother_father_name' => $parts[1] ?? '',
            'mother_grandfather_name' => $parts[2] ?? '',
        ];
    }

    private function normalizeGender(string $gender): ?string
    {
        $gender = trim($gender);
        if ($gender === 'ذكر') return 'ذكر';
        if (in_array($gender, ['أنثى', 'انثى'], true)) return 'أنثى';
        return null;
    }

    private function normalizeSocialStatus(string $status): ?string
    {
        $status = trim($status);
        if (in_array($status, ['أعزب', 'اعزب', 'عازب'], true)) return 'أعزب';
        if (in_array($status, ['متزوج', 'متزوجة'], true)) return 'متزوج';
        if (in_array($status, ['مطلق', 'مطلقة'], true)) return 'مطلق';
        if (in_array($status, ['أرمل', 'أرملة', 'ارمل', 'ارمله'], true)) return 'أرمل';
        return null;
    }

    private function findQualificationId(string $text, array $qualifications): ?int
    {
        if (empty($text)) return null;

        $normalized = trim(mb_strtolower($text));

        foreach ($qualifications as $name => $id) {
            if (mb_strtolower(trim($name)) === $normalized) {
                return $id;
            }
        }

        foreach ($qualifications as $name => $id) {
            $similar = similar_text($normalized, mb_strtolower(trim($name)), $percent);
            if ($percent >= 70) {
                return $id;
            }
        }

        return null;
    }

    private function extractGovernorates(string $address, array $governorates): null|int|string
    {
        if (empty($address)) return null;

        foreach ($governorates as $name => $id) {
            $normalizedName = str_replace(['ه', 'ة'], ['ة', 'ة'], $name);
            $normalizedAddress = str_replace(['ه', 'ة'], ['ة', 'ة'], $address);
            if (mb_strpos($normalizedAddress, $normalizedName) !== false) {
                return $id;
            }
        }

        return null;
    }
}
