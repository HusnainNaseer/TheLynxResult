<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>The Lynx School - Term Report Card</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #efefef;
            color: #000;
            font-family: Calibri, Arial, sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .sheet {
            width: 90vw;
            max-width: 920px;
            min-height: 90vh;
            margin: 12px auto;
            background: #fff;
            padding: 54px 54px 36px;
        }

        .header {
            position: relative;
            text-align: center;
            min-height: 104px;
        }

        .school-logo {
	       position: absolute;
		    top: 0;
		    left: 0;
		    transform: translateX(33px);
		    width: 80px;
		    height: 80px;
		    object-fit: contain;
        }

        .school-name {
            margin: 0;
            color: #ff0000;
            font-family: "Edwardian Script ITC", "Brush Script MT", cursive;
            font-size: 44px;
            font-weight: 400;
            line-height: 1;
        }

        .branch-name,
        .branch-meta,
        .report-title {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.55;
        }
        
        .session-title {
            width: 320px;
            margin: 7px auto 14px;
            padding: 4px 8px;
            background: #c6d9f1;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }

        
        .branch-name {
            margin-top: 10px;
            text-transform: uppercase;
        }

        .report-bar {
				position: relative;
				padding: 6px 26px;
				background: #c6d9f1;
				text-align: center;
				font-size: 12px;
				width: fit-content;
				font-weight: 700;
				left: 40%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .student-info {
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 12px;
            margin-top: 12px;
        }

        .student-info td {
            padding: 5px 6px;
            white-space: nowrap;
        }

        .line-value {
            display: inline-block;
            min-width: 68px;
            border-bottom: 1px solid #000;
            text-align: center;
            font-weight: 400;
        }

        .line-value.wide {
            min-width: 118px;
        }

        .marks {
            margin-top: 12px;
            font-size: 11px;
            table-layout: fixed;
        }

        .marks th,
        .marks td {
            border: 1px solid #000;
            padding: 6px 7px;
            text-align: center;
            line-height: 1.1;
        }

        .marks th {
            font-weight: 700;
        }

        .marks .subject-cell {
            text-align: left;
            font-weight: 700;
            padding-left: 12px;
        }

        .marks .total-row td {
            font-weight: 700;
        }

        .summary-line {
            margin: 25px auto 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            font-size: 11px;
            font-weight: 700;
        }

        .summary-line .line-value {
            min-width: 74px;
        }

        .grading {
            width: 460px;
            margin: 18px auto;
            font-size: 10px;
            text-align: center;
        }

        .grading th {
            background: #c6d9f1;
            padding: 4px;
        }

        .grading th,
        .grading td {
            border: 1px solid #000;
            padding: 4px;
        }

        .remarks-box {
            margin-top: 30px;
            border: 1px solid #000;
            padding: 12px;
            min-height: 118px;
            font-size: 10px;
            line-height: 1.35;
            text-align: justify;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 120px;
            margin-top: 75px;
            font-size: 10px;
            text-align: center;
        }

        .signature-name {
            min-height: 14px;
            font-weight: 700;
        }

        .signature-line {
            border-top: 1px solid #000;
            padding-top: 4px;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {
            body {
                background: #fff;
            }

            .sheet {
                width: 90%;
                min-height: 90vh;
                margin: 0 auto;
                padding: 12mm 14mm;
            }
        }
    </style>
</head>

<body>
    @php
        $isTermOne = $termKey === 'term_one';
        $termTitle = $isTermOne ? 'First Term Report Card' : 'Second Term Report Card';
        $termName = $isTermOne ? 'First Term' : 'Second Term';
        $markField = $isTermOne ? 'term_one_mark' : 'term_two_mark';
        $totalField = $isTermOne ? 'term_one_total' : 'term_two_total';
        $subjectDefaultTotalField = $isTermOne ? 'term_one_marks' : 'term_two_marks';
        $storedPercentage = $isTermOne ? $student->percentage_term_one : $student->percentage_term_two;
        $workingDays = $isTermOne ? $student->t1_working_days : $student->t2_working_days;
        $sessionWorkingDays = $isTermOne ? $student->session?->t1_working_days : $student->session?->t2_working_days;
        $branchName = $branch?->name ?? $creator->branch_name ?? 'N/A';
        $branchEmail = $branch?->email ?? $creator->branch_email ?? 'N/A';
        $branchPhone = $branch?->phone ?? $creator->branch_phone ?? 'N/A';
        $principalName = $branch?->principal_headmistress ?: '';
        $marksBySubject = $student->marks->keyBy(fn($mark) => (int) $mark->subject_id);
        $totalMarks = 0;
        $obtainedMarks = 0;

        $gradeFromPercentage = function ($percentage) {
            if ($percentage >= 90) return 'A+';
            if ($percentage >= 80) return 'A';
            if ($percentage >= 70) return 'B';
            if ($percentage >= 60) return 'C';
            if ($percentage >= 50) return 'D';
            if ($percentage >= 40) return 'E';
            return 'U';
        };

        $formatNumber = function ($value) {
            $number = (float) ($value ?? 0);
            return fmod($number, 1.0) === 0.0 ? (string) (int) $number : number_format($number, 2);
        };
    @endphp

    <div class="sheet">
        <div class="header">
            <h1 class="school-name">The Lynx School</h1>
            <img src="{{ asset('images/lynx_logo.png') }}" alt="The Lynx School" class="school-logo">
            <div class="branch-name">{{ $branchName }}</div>
            <div class="branch-meta">Phone: {{ $branchPhone }} &nbsp;&nbsp;&nbsp; Email: {{ $branchEmail }}</div>
            <div class="session-title">Academic Year {{ $student->session?->title ?? '' }}</div>
            <div class="report-bar">{{ $termTitle }}</div>
        </div>

        <table class="student-info">
            <tr>
                <td colspan="2">Student Name: <span class="line-value wide">{{ $student->name }}</span></td>
                <td>Roll No: <span class="line-value">{{ $student->rollno }}</span></td>
            </tr>
            <tr>
                <td colspan="2">Class / Section: <span class="line-value">{{ $student->class }} / {{ $student->section }}</span></td>
                <td>Attendance: <span class="line-value">{{ $formatNumber($workingDays) }}/{{ $formatNumber($sessionWorkingDays) }}</span></td>
            </tr>
        </table>

        <table class="marks">
            <colgroup>
                <col style="width: 36px;">
                <col>
                <col style="width: 90px;">
                <col style="width: 90px;">
                <col style="width: 110px;">
            </colgroup>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>Total<br>Marks</th>
                <th>Marks<br>Obtained</th>
                <th>Subject Grade</th>
            </tr>
            @forelse ($subjects as $subject)
                @php
                    $mark = $marksBySubject->get((int) $subject->id);
                    $subjectTotal = (float) ($mark?->{$totalField} ?? $subject->{$subjectDefaultTotalField} ?? 0);
                    $subjectObtained = (float) ($mark?->{$markField} ?? 0);
                    $subjectPercentage = $subjectTotal > 0 ? ($subjectObtained / $subjectTotal) * 100 : 0;
                    $totalMarks += $subjectTotal;
                    $obtainedMarks += $subjectObtained;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="subject-cell">{{ $subject->subject_name }}</td>
                    <td>{{ $formatNumber($subjectTotal) }}</td>
                    <td>{{ $formatNumber($subjectObtained) }}</td>
                    <td>{{ $gradeFromPercentage($subjectPercentage) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No subjects assigned to this class.</td>
                </tr>
            @endforelse

            @php
                $termPercentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : (float) ($storedPercentage ?? 0);
                $termGrade = $gradeFromPercentage($termPercentage);
            @endphp
            <tr class="total-row">
                <td></td>
                <td>Total</td>
                <td>{{ $formatNumber($totalMarks) }}</td>
                <td>{{ $formatNumber($obtainedMarks) }}</td>
                <td></td>
            </tr>
        </table>

        <div class="summary-line">
            <span>Grade:</span>
            <span class="line-value">{{ $termGrade }}</span>
            <span>{{ $termName }} Percentage:</span>
            <span class="line-value">{{ number_format($termPercentage, 2) }}</span>
            <span>%</span>
        </div>

        <table class="grading">
            <tr>
                <th colspan="7">Grading System</th>
            </tr>
            <tr>
                <td>90 - 100</td>
                <td>80 - 89</td>
                <td>70 - 79</td>
                <td>60 - 69</td>
                <td>50 - 59</td>
                <td>40 - 49</td>
                <td>30 - 39</td>
            </tr>
            <tr>
                <td>A+</td>
                <td>A</td>
                <td>B</td>
                <td>C</td>
                <td>D</td>
                <td>E</td>
                <td>U</td>
            </tr>
        </table>

        <div class="remarks-box">
            <strong>Teacher's Remarks:</strong>
            <div>{{ $student->remarks ?: 'No remarks provided.' }}</div>
        </div>

        <div class="signatures">
            <div>
                <div class="signature-name">{{ $classTeacherName }}</div>
                <div class="signature-line">Class Teacher</div>
            </div>
            <div>
                <div class="signature-name">{{ $principalName }}</div>
                <div class="signature-line">Principal</div>
            </div>
        </div>
    </div>
</body>

</html>
