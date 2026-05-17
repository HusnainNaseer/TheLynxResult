<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>The Lynx School - Report Card</title>

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
            position: relative;
            width: 1120px;
            min-height: 740px;
            margin: 18px auto;
            background: #fff;
            border: 0px solid #111;
            padding: 46px 112px 34px;
        }

        .header {
            position: relative;
            text-align: center;
            min-height: 96px;
            padding: 0 84px;
        }

        .school-logo,
        .result-qr {
            position: absolute;
            top: 0;
            width: 58px;
            height: 58px;
            object-fit: contain;
        }

        .school-logo {
            left: 8px;
        }

        .result-qr {
            right: 8px;
        }

        .school-name {
            margin: 0;
            color: #ff0000;
            font-family: "Edwardian Script ITC", "Brush Script MT", cursive;
            font-size: 32px;
            font-weight: 400;
            line-height: 1;
        }

        .branch-name {
            margin-top: 10px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .branch-meta {
            margin-top: 5px;
            font-size: 8px;
            font-weight: 700;
        }

        .report-title {
            width: 320px;
            margin: 14px auto 14px;
            padding: 4px 8px;
            background: #c6d9f1;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .student-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 8px;
            font-weight: 700;
        }

        .student-info td {
            padding: 0 8px 6px 0;
            white-space: nowrap;
        }

        .line-value {
            display: inline-block;
            min-width: 74px;
            margin-left: 5px;
            border-bottom: 1px solid #000;
            text-align: center;
            font-weight: 400;
        }

        .line-value.wide {
            min-width: 126px;
        }

        .marks {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 8px;
        }

        .marks th,
        .marks td {
            border: 1px solid #000;
            padding: 2px 3px;
            text-align: center;
            line-height: 1.15;
        }

        .marks th {
            font-weight: 700;
        }

        .marks .subject-cell {
            text-align: left;
            padding-left: 8px;
            font-weight: 700;
        }

        .marks .total-row td {
            font-weight: 700;
        }

        .term-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            margin-top: 10px;
            font-size: 8px;
        }

        .term-title {
            background: #c6d9f1;
            text-align: center;
            font-weight: 700;
            padding: 4px;
            margin-bottom: 12px;
        }

        .term-summary {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-weight: 700;
        }

        .term-summary td {
            padding: 4px 3px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .term-summary .summary-label {
            width: 50%;
            text-align: right;
        }

        .term-summary .summary-value {
            width: 28%;
            text-align: center;
        }

        .term-summary .summary-unit {
            width: 22%;
            text-align: left;
        }

        .term-summary .line-value {
            min-width: 52px;
            margin-left: 0;
        }

        .bottom-grid {
            display: grid;
            grid-template-columns: 390px 1fr;
            align-items: end;
            column-gap: 100px;
            margin-top: 26px;
            font-size: 8px;
        }

        .grade-range {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .grade-range th {
            background: #c6d9f1;
            padding: 4px;
            font-weight: 700;
        }

        .grade-range th,
        .grade-range td {
            border: 1px solid #000;
            padding: 3px;
        }

        .promoted-box {
            font-weight: 700;
        }

        .promotion-row {
            display: grid;
            grid-template-columns: 115px 1fr 160px;
            align-items: center;
            gap: 0;
        }

        .promotion-label {
            text-align: right;
            padding-right: 6px;
        }

        .promotion-value {
            border-bottom: 1px solid #000;
            text-align: center;
        }

        .success-box {
            background: #00b050;
            color: #000;
            font-weight: 700;
            text-align: center;
            padding: 6px;
        }

        .remarks-line {
            margin-top: 10px;
            line-height: 1.35;
            min-height: 18px;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 90px;
            margin-top: 18px;
            font-size: 8px;
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

        .system-note {
            position: absolute;
            left: 112px;
            bottom: 8px;
            text-align: left;
            font-size: 7px;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        @media print {
            html,
            body {
                width: 297mm;
                min-height: 210mm;
            }

            body {
                background: #fff;
            }

            .sheet {
                width: 297mm;
                min-height: 210mm;
                margin: 0 auto;
                border: 0;
                padding: 12mm 24mm 9mm;
                page-break-after: always;
            }

            .system-note {
                left: 24mm;
                bottom: 3mm;
                text-align: left;
            }
        }
    </style>
</head>

<body>
    @php
        $branchName = $branch?->name ?? $creator->branch_name ?? 'N/A';
        $branchEmail = $branch?->email ?? $creator->branch_email ?? 'N/A';
        $branchPhone = $branch?->phone ?? $creator->branch_phone ?? 'N/A';
        $branchAddress = $branch?->address ?? $creator->branch_address ?? 'N/A';
        $principalName = $branch?->principal_headmistress ?: '';
        $executiveDirector = $branch?->executive_director_islamabad ?: '';
        $marksBySubject = $student->marks->keyBy(fn($mark) => (int) $mark->subject_id);

        $gradeFromPercentage = function ($percentage) {
            if ($percentage >= 90) {
                return 'A+';
            }
            if ($percentage >= 80) {
                return 'A';
            }
            if ($percentage >= 70) {
                return 'B';
            }
            if ($percentage >= 60) {
                return 'C';
            }
            if ($percentage >= 50) {
                return 'D';
            }
            if ($percentage >= 40) {
                return 'E';
            }

            return 'U';
        };

        $remarksFromGrade = function ($grade) {
            return match ($grade) {
                'A+' => 'Excellent',
                'A' => 'Very Good',
                'B' => 'Good',
                'C' => 'Satisfactory',
                'D' => 'Pass',
                'E' => 'Marginal',
                default => 'Needs Improvement',
            };
        };

        $formatNumber = function ($value) {
            $number = (float) ($value ?? 0);
            return fmod($number, 1.0) === 0.0 ? (string) (int) $number : number_format($number, 2);
        };

        $termOneTotal = 0;
        $termOneObtained = 0;
        $termTwoTotal = 0;
        $termTwoObtained = 0;
        $annualTotal = 0;
        $annualObtained = 0;
        $verificationUrl = $publicResultUrl ?? route('public.result', $encodedResultId);
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=58x58&margin=0&data=' . urlencode($verificationUrl);
    @endphp

    <div class="sheet">
        <div class="header">
            <img src="{{ asset('images/lynx_logo.png') }}" alt="The Lynx School" class="school-logo">
            <img src="{{ $qrCodeUrl }}" alt="Result verification QR" class="result-qr">
            <h1 class="school-name">The Lynx School</h1>
            <div class="branch-name">{{ $branchName }}</div>
            <div class="branch-meta">
                Phone: {{ $branchPhone }} &nbsp;&nbsp;&nbsp; Email: {{ $branchEmail }}
            </div>
            <div class="branch-meta">{{ $branchAddress }}</div>
            <div class="report-title">Report Card Academic Year {{ $student->session?->title ?? '' }}</div>
        </div>

        <table class="student-info">
            <tr>
                <td>Roll No: <span class="line-value">{{ $student->rollno }}</span></td>
                <td>Student Name: <span class="line-value wide">{{ $student->name }}</span></td>
                <td>Father Name: <span class="line-value wide">{{ $student->student?->fathername ?? '' }}</span></td>
                <td>Class: <span class="line-value">{{ $student->class }}</span></td>
                <td>Section: <span class="line-value">{{ $student->section }}</span></td>
            </tr>
        </table>

        <table class="marks">
            <colgroup>
                <col style="width: 28px;">
                <col style="width: 160px;">
                <col span="4">
                <col span="4">
                <col span="4">
            </colgroup>
            <tr>
                <th rowspan="2"></th>
                <th rowspan="2">Subject</th>
                <th colspan="4">First Term</th>
                <th colspan="4">Second Term</th>
                <th colspan="4">Annual Result</th>
            </tr>
            <tr>
                <th>Total<br>Marks</th>
                <th>Marks<br>Obtained</th>
                <th>Grade</th>
                <th>Remarks</th>
                <th>Total<br>Marks</th>
                <th>Marks<br>Obtained</th>
                <th>Grade</th>
                <th>Remarks</th>
                <th>Total<br>Marks</th>
                <th>Marks<br>Obtained</th>
                <th>Grade</th>
                <th>Remarks</th>
            </tr>

            @forelse ($subjects as $subject)
                @php
                    $mark = $marksBySubject->get((int) $subject->id);
                    $t1Total = (float) ($mark?->term_one_total ?? $subject->term_one_marks ?? 0);
                    $t2Total = (float) ($mark?->term_two_total ?? $subject->term_two_marks ?? 0);
                    $t1Obtained = (float) ($mark?->term_one_mark ?? 0);
                    $t2Obtained = (float) ($mark?->term_two_mark ?? 0);
                    $subjectAnnualTotal = $t1Total + $t2Total;
                    $subjectAnnualObtained = $t1Obtained + $t2Obtained;
                    $t1Grade = $t1Total > 0 ? $gradeFromPercentage(($t1Obtained / $t1Total) * 100) : '';
                    $t2Grade = $t2Total > 0 ? $gradeFromPercentage(($t2Obtained / $t2Total) * 100) : '';
                    $annualGrade = $subjectAnnualTotal > 0 ? $gradeFromPercentage(($subjectAnnualObtained / $subjectAnnualTotal) * 100) : '';

                    $termOneTotal += $t1Total;
                    $termOneObtained += $t1Obtained;
                    $termTwoTotal += $t2Total;
                    $termTwoObtained += $t2Obtained;
                    $annualTotal += $subjectAnnualTotal;
                    $annualObtained += $subjectAnnualObtained;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="subject-cell">{{ $subject->subject_name }}</td>
                    <td>{{ $formatNumber($t1Total) }}</td>
                    <td>{{ $formatNumber($t1Obtained) }}</td>
                    <td>{{ $t1Grade }}</td>
                    <td>{{ $t1Grade ? $remarksFromGrade($t1Grade) : '' }}</td>
                    <td>{{ $formatNumber($t2Total) }}</td>
                    <td>{{ $formatNumber($t2Obtained) }}</td>
                    <td>{{ $t2Grade }}</td>
                    <td>{{ $t2Grade ? $remarksFromGrade($t2Grade) : '' }}</td>
                    <td>{{ $formatNumber($subjectAnnualTotal) }}</td>
                    <td>{{ $formatNumber($subjectAnnualObtained) }}</td>
                    <td>{{ $annualGrade }}</td>
                    <td>{{ $annualGrade ? $remarksFromGrade($annualGrade) : '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14">No subjects assigned to this class.</td>
                </tr>
            @endforelse

            @php
                $termOnePercentage = $termOneTotal > 0 ? round(($termOneObtained / $termOneTotal) * 100, 2) : 0;
                $termTwoPercentage = $termTwoTotal > 0 ? round(($termTwoObtained / $termTwoTotal) * 100, 2) : 0;
                $annualPercentage = $annualTotal > 0 ? round(($annualObtained / $annualTotal) * 100, 2) : 0;
                $termOneGrade = $termOneTotal > 0 ? $gradeFromPercentage($termOnePercentage) : '';
                $termTwoGrade = $termTwoTotal > 0 ? $gradeFromPercentage($termTwoPercentage) : '';
            @endphp

            <tr class="total-row">
                <td></td>
                <td>Total</td>
                <td>{{ $formatNumber($termOneTotal) }}</td>
                <td>{{ $formatNumber($termOneObtained) }}</td>
                <td></td>
                <td></td>
                <td>{{ $formatNumber($termTwoTotal) }}</td>
                <td>{{ $formatNumber($termTwoObtained) }}</td>
                <td></td>
                <td></td>
                <td>{{ $formatNumber($annualTotal) }}</td>
                <td>{{ $formatNumber($annualObtained) }}</td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <div class="term-grid">
            <div>
                <div class="term-title">First Term</div>
                <table class="term-summary">
                    <tr>
                        <td class="summary-label">Attendance:</td>
                        <td class="summary-value">
                            <span class="line-value">{{ $formatNumber($student->t1_working_days) }} / {{ $formatNumber($student->session?->t1_working_days) }}</span>
                        </td>
                        <td class="summary-unit"></td>
                        <td class="summary-label">Obtained Grade:</td>
                        <td class="summary-value"><span class="line-value">{{ $termOneGrade }}</span></td>
                    </tr>
                    <tr>
                        <td class="summary-label">Highest Class Percentage:</td>
                        <td class="summary-value">
                            <span class="line-value">{{ number_format($highestPercentages['term_one'] ?? 0, 2) }} %</span>
                        </td>
                        <td class="summary-unit"></td>
                    
                        <td class="summary-label">Obtained Percentage:</td>
                        <td class="summary-value">
                            <span class="line-value">{{ number_format($termOnePercentage, 2) }} %</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div>
                <div class="term-title">Second Term</div>
                <table class="term-summary">
                    <tr>
                        <td class="summary-label">Attendance:</td>
                        <td class="summary-value">
                            <span class="line-value">{{ $formatNumber($student->t2_working_days) }} / {{ $formatNumber($student->session?->t2_working_days) }}</span>
                            </td>
                            <td class="summary-unit"></td>
                            <td class="summary-label">Obtained Grade:</td>
                            <td class="summary-value"><span class="line-value">{{ $termTwoGrade }}</span></td>
                    </tr>
                    <tr>
                        <td class="summary-label">Highest Class Percentage:</td>
                        <td class="summary-value">
                            <span class="line-value">{{ number_format($highestPercentages['term_two'] ?? 0, 2) }} %</span>
                        </td>
                        <td class="summary-unit"></td>
                        <td class="summary-label">Obtained Percentage:</td>
                        <td class="summary-value">
                            <span class="line-value">{{ number_format($termTwoPercentage, 2) }} %</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="bottom-grid">
            <table class="grade-range">
                <tr>
                    <th colspan="7">Grade Range</th>
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

            <div class="promoted-box">
                <div class="promotion-row">
                    <div class="promotion-label">Promoted To:</div>
                    <div class="promotion-value">{{ $student->promoted_class }}</div>
                    <div class="success-box">Congratulation</div>
                </div>
              
            </div>
        </div>

        <div class="signatures">
            <div>
                <div class="signature-name">{{ $classTeacherName }}</div>
                <div class="signature-line">Class Teacher</div>
            </div>
            <div>
                <div class="signature-name">{{ $principalName }}</div>
                <div class="signature-line">Principal/Headmistress</div>
            </div>
            <div>
                <div class="signature-name">{{ $executiveDirector }}</div>
                <div class="signature-line">Executive Director Islamabad</div>
            </div>
        </div>

        <div class="system-note">system generated report , signatures are not required</div>
    </div>
</body>

</html>
