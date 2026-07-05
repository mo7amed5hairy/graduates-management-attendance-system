import openpyxl, sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

wb = openpyxl.load_workbook('ملف الخريجين القدامى -البصرة 2022.xlsx')
ws = wb.active
rows = list(ws.iter_rows(values_only=True))

print('=== All unique qualifications ===')
quals = set()
for r in range(4, len(rows)):
    v = rows[r][8]
    if v: quals.add(str(v).strip())
for q in sorted(quals):
    print(f'  {q}')

print('\n=== All unique social statuses ===')
statuses = set()
for r in range(4, len(rows)):
    v = rows[r][11]
    if v: statuses.add(str(v).strip())
for s in sorted(statuses):
    print(f'  {s}')

print('\n=== All unique statuses (col 1) ===')
st = set()
for r in range(4, len(rows)):
    v = rows[r][1]
    if v: st.add(str(v).strip())
for s in sorted(st):
    print(f'  {s}')

print('\n=== Row 100 as sample ===')
vals = []
for i, v in enumerate(rows[100]):
    if v is not None:
        vals.append(f'[{i}]={repr(v)}')
print(f'Row 101: {", ".join(vals)}')

print('\n=== Row 200 as sample ===')
vals = []
for i, v in enumerate(rows[200]):
    if v is not None:
        vals.append(f'[{i}]={repr(v)}')
print(f'Row 201: {", ".join(vals)}')

print('\n=== Checking name patterns (first 20 data rows) ===')
for r in range(4, 24):
    name = rows[r][3]
    if name:
        parts = str(name).strip().split()
        print(f'Row {r+1}: {len(parts)} parts -> {parts}')
