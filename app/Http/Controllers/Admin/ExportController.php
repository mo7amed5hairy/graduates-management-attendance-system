<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\Qualification;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportController extends Controller
{
    public function graduates(Request $request)
    {
        $query = User::where('role', 'user')
            ->select([
                'id', 'first_name', 'father_name', 'grandfather_name', 'family_name',
                'mother_name', 'mother_father_name', 'mother_grandfather_name',
                'email', 'phone', 'national_id', 'governorate', 'address',
                'gender', 'social_status', 'age', 'date_of_birth',
                'qualification_id', 'graduation_year',
                'job_status', 'status', 'approval_status', 'points', 'created_at',
            ])
            ->with('qualification:id,name')
            ->orderBy('id');

        if ($request->filled('governorate')) {
            $gov = $request->governorate;
            $query->where(function ($q) use ($gov) {
                $q->where('governorate', $gov);
                $govModel = Governorate::find((int) $gov);
                if ($govModel) {
                    $q->orWhere('governorate', $govModel->name);
                }
            });
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('graduation_year')) {
            $query->where('graduation_year', $request->graduation_year);
        }
        if ($request->filled('qualification_id')) {
            $query->where('qualification_id', $request->qualification_id);
        }
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('national_id', 'like', "%{$s}%");
            });
        }

        $users = $query->get();
        return $this->buildExcel($users, 'graduates');
    }

    public function users(Request $request)
    {
        $query = User::select([
            'id', 'first_name', 'father_name', 'grandfather_name', 'family_name',
            'mother_name', 'mother_father_name', 'mother_grandfather_name',
            'email', 'phone', 'national_id', 'governorate', 'address',
            'gender', 'social_status', 'age', 'date_of_birth',
            'qualification_id', 'graduation_year',
            'job_status', 'status', 'approval_status', 'role', 'points', 'created_at',
        ])
            ->with('qualification:id,name')
            ->orderBy('id');

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('governorate')) {
            $gov = $request->governorate;
            $query->where(function ($q) use ($gov) {
                $q->where('governorate', $gov);
                $govModel = Governorate::find((int) $gov);
                if ($govModel) {
                    $q->orWhere('governorate', $govModel->name);
                }
            });
        }
        if ($request->filled('graduation_year')) {
            $query->where('graduation_year', $request->graduation_year);
        }
        if ($request->filled('qualification_id')) {
            $query->where('qualification_id', $request->qualification_id);
        }
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $users = $query->get();
        return $this->buildExcel($users, 'users');
    }

    private function buildExcel($users, string $type)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);
        $sheet->getPageSetup()->setOrientation('landscape');

        $headers = [
            'ت', 'الحالة', 'فحص المكرر', 'الاسم الكامل مع اللقب',
            'العمر', 'الجنس', 'عنوان السكن الحالي',
            'سنة التخرج', 'التحصيل الدراسي', 'رقم الهاتف',
            'اسم الأم الرباعي', 'الحالة الاجتماعية',
        ];

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        foreach ($headers as $col => $label) {
            $cell = $sheet->getCellByColumnAndRow($col + 1, 1);
            $cell->setValue($label);
        }
        $sheet->getStyle('A1:' . chr(64 + count($headers)) . '1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $rowStyle = [
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $altStyle = [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ];

        $qualificationsMap = Qualification::pluck('name', 'id')->toArray();

        foreach ($users as $i => $u) {
            $rowNum = $i + 2;
            $fullName = trim(
                ($u->first_name ?? '') . ' ' .
                ($u->father_name ?? '') . ' ' .
                ($u->grandfather_name ?? '') . ' ' .
                ($u->family_name ?? '')
            ) ?: ($u->name ?? '');

            $motherFull = trim(
                ($u->mother_name ?? '') . ' ' .
                ($u->mother_father_name ?? '') . ' ' .
                ($u->mother_grandfather_name ?? '')
            );

            $qualName = $qualificationsMap[$u->qualification_id] ?? ($u->qualification->name ?? '—');

            $gender = $u->gender ?? '—';
            if ($gender === 'ذكر') $gender = 'ذكر';
            elseif (in_array($gender, ['أنثى', 'انثى'], true)) $gender = 'أنثى';

            $social = $u->social_status ?? '—';
            $gradYear = $u->graduation_year ?? '—';

            $phone = $u->phone ?? '—';
            $phone = preg_replace('/[^0-9]/', '', $phone);

            $address = $u->address ?? '—';
            $age = $u->age ?? '—';

            $rowData = [
                $i + 1,
                'مقبول',
                '',
                $fullName,
                $age,
                $gender,
                $address,
                $gradYear,
                $qualName,
                $phone,
                $motherFull,
                $social,
            ];

            foreach ($rowData as $col => $value) {
                $cell = $sheet->getCellByColumnAndRow($col + 1, $rowNum);
                $cell->setValue($value);
            }

            $range = 'A' . $rowNum . ':' . chr(64 + count($headers)) . $rowNum;
            $sheet->getStyle($range)->applyFromArray($rowStyle);

            if ($i % 2 === 1) {
                $sheet->getStyle($range)->applyFromArray($altStyle);
            }
        }

        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $sheet->setAutoFilter('A1:' . chr(64 + count($headers)) . '1');

        $filename = 'الخريجين_' . date('Y_m_d') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        $tempFile = tempnam(sys_get_temp_dir(), 'export_');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
