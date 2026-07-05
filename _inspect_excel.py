import openpyxl, sys, io

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

wb = openpyxl.load_workbook('ملف الخريجين القدامى -البصرة 2022.xlsx')
ws = wb.active

rows = list(ws.iter_rows(values_only=True))
print(f'Total rows: {len(rows)}')
print('=== Header Row ===')
for i, h in enumerate(rows[0]):
    print(f'  [{i}] {h}')

print('\n=== First 5 data rows ===')
for r in range(1, min(6, len(rows))):
    vals = []
    for i, v in enumerate(rows[r]):
        if v is not None:
            vals.append(f'[{i}]={repr(v)}')
    print(f'Row {r+1}: {", ".join(vals)}')
