<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>The Lynx School – Report Card</title>

    <style>
        body {
            background: #eaeaea;
            font-family: Calibri, Arial, sans-serif;
        }

        .sheet {
            width: 1100px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border: 2px solid #000;
        }

        /* HEADER */
        .header {
            text-align: center;
            position: relative;
        }

        .school-name {
            font-family: 'Edwardian Script ITC', cursive;
            color: red;
            font-size: 48px;
            /* text-align: center; */
            margin: 0;
            font-weight: normal;
            margin-left: -180px !important;
        }

        .school-info {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }

        .logo {
            position: absolute;
            right: 10px;
            top: 0;
            width: 80px;
        }

        .academic-year {
            margin: 10px auto;
            background: #00b0f0;
            color: #000;
            font-weight: bold;
            width: 300px;
            padding: 4px;
            font-size: 13px;
        }

        /* STUDENT INFO */
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }

        .info td {
            /* border: 1px solid #000; */
            padding: 5px;
            font-weight: bold;
            width: 25%;
            text-align: center;

        }

        .info td span {
            font-weight: normal;
        }

        /* MARKS TABLE */
        .marks th,
        .marks td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        .marks th {
            background: #d9d9d9;
            font-weight: bold;
        }

        .subject {
            text-align: left !important;
            padding-left: 6px !important;
            font-weight: bold;
        }

        .total-row td {
            font-weight: bold;
        }

        /* FOOTER */
        .footer {
            margin-top: 15px;
            font-size: 12px;
        }

        .footer table td {
            padding: 5px;
        }

        .signature {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }

        .line {
            border-top: 1px solid #000;
            width: 200px;
            text-align: center;
            padding-top: 3px;
            font-weight: bold;
        }

        .school-logo {
            width: 120px;
            height: auto;
        }

        .header-title {

            font-family: "Edwardian Script ITC", "Adwardian SC", cursive;
            font-size: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .info {
            font-weight: bolder;
            font-size: :12px;
        }

        .remarks-section {
            /* margin: 0px; */
            border: 1px solid #000;
            /* padding: 15px; */
            padding-left: 10px;
            /* margin: 0 20px 20px 20px; */
            /* background: #f9f9f9; */
        }

        .remarks-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .grading-table {
            /* margin: 0 20px 20px 20px; */
        }

        .grading-table td {
            font-family: Calibri, sans-serif;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            /* font-weight: bold; */
        }

        .grading-system {
            margin: 20px;
            font-family: Calibri, sans-serif;
            background: #64B5F6;
            color: black;
            padding: 10px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="sheet">

        <!-- HEADER -->
        <div class="header">
            <div class="header-title">
                <div class=""></div>
                <div class=""></div>
                <h1 class="school-name">The Lynx School</h1>
                <img src="{{ asset('images/lynx_logo.png') }}" alt="Lynx Logo" class="school-logo">
            </div>
            <div class="school-info">
                <span style="font-size: 18px;">
                    {{ $creator->branch_name ?? 'N/A' }}
                </span>
                Adress: {{ $creator->branch_address ?? 'N/A' }}<br>
                Contact No: {{ $creator->branch_phone ?? 'N/A' }} &nbsp;|
                Email: &nbsp; {{ $creator->branch_email ?? 'N/A' }}
            </div>
            {{-- <img src="{{ asset('images/lynx_logo.png') }}" class="logo"> --}}

            <div class="academic-year">Academic Year {{ @$student->session ? $student->session->title : '' }}</div>
        </div>

        <!-- STUDENT INFO -->
        <table class="info">
            <tr>
                <td>Roll No: <u>&nbsp;&nbsp;&nbsp;&nbsp;{{ $student->rollno }}&nbsp;&nbsp;&nbsp;&nbsp;</u></td>
                <td>Student Name: <u>&nbsp;&nbsp;&nbsp;&nbsp;{{ $student->name }}&nbsp;&nbsp;&nbsp;&nbsp;</u></td>
                <td> Class/section: <u>&nbsp;&nbsp;&nbsp;&nbsp;{{ $student->class }} /
                        {{ $student->section }}&nbsp;&nbsp;&nbsp;&nbsp;</u></td>
                <td>Promoted To: __<u>{{ $student->promoted_class }}</u>___</td>
            </tr>
        </table>

        <!-- MARKS TABLE -->
        <table class="marks" style="margin-top:10px;">
            <tr>
                <th rowspan="2"></th>
                <th rowspan="2">Subject</th>
                <th colspan="4">First Term</th>
                <th colspan="4">Second Term</th>
            </tr>
            <tr>
                <th>Total</th>
                <th>Obt</th>
                <th>Grade</th>
                <th>Remarks</th>
                <th>Total</th>
                <th>Obt</th>
                <th>Grade</th>
                <th>Remarks</th>

            </tr>

            @php
                $t1_total = 0;
                $t1_obt = 0;
                $t2_total = 0;
                $t2_obt = 0;

                $gradeFromPercentage = function ($p) {
                    if ($p >= 90) {
                        return 'A+';
                    }
                    if ($p >= 80) {
                        return 'A';
                    }
                    if ($p >= 70) {
                        return 'B';
                    }
                    if ($p >= 60) {
                        return 'C';
                    }
                    if ($p >= 50) {
                        return 'D';
                    }
                    if ($p >= 40) {
                        return 'E';
                    }
                    if ($p >= 30) {
                        return 'U';
                    }
                    return 'F'; // Below 30
                };

                $remarksFromGrade = function ($g) {
                    return match ($g) {
                        'A+' => 'Excellent',
                        'A' => 'Very Good',
                        'B' => 'Good',
                        'C' => 'Satisfactory',
                        'D' => 'Pass',
                        'E' => 'Marginal',
                        'U' => 'Unsatisfactory',
                        default => 'Fail',
                    };
                };
            @endphp

            @foreach ($student->marks as $mark)
                @php
                    // Calculate percentage for this subject
                    $t1_subject_percentage =
                        $mark->subject->term_one_marks > 0
                            ? ($mark->term_one_mark / $mark->subject->term_one_marks) * 100
                            : 0;
                    $t2_subject_percentage =
                        $mark->subject->term_two_marks > 0
                            ? ($mark->term_two_mark / $mark->subject->term_two_marks) * 100
                            : 0;

                    // Calculate grades dynamically
                    $t1_subject_grade = $gradeFromPercentage($t1_subject_percentage);
                    $t2_subject_grade = $gradeFromPercentage($t2_subject_percentage);
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="subject">{{ $mark->subject->subject_name }}</td>

                    <td>{{ $mark->subject->term_one_marks }}</td>
                    <td>{{ $mark->term_one_mark }}</td>
                    <td>{{ $t1_subject_grade }}</td>
                    <td>{{ $remarksFromGrade($t1_subject_grade) }}</td>

                    <td>{{ $mark->subject->term_two_marks }}</td>
                    <td>{{ $mark->term_two_mark }}</td>
                    <td>{{ $t2_subject_grade }}</td>
                    <td>{{ $remarksFromGrade($t2_subject_grade) }}</td>
                </tr>

                @php
                    $t1_total += $mark->subject->term_one_marks;
                    $t1_obt += $mark->term_one_mark;
                    $t2_total += $mark->subject->term_two_marks;
                    $t2_obt += $mark->term_two_mark;
                @endphp
            @endforeach

            @php
                $term1_percentage = $t1_total > 0 ? round(($t1_obt / $t1_total) * 100, 2) : 0;
                $term2_percentage = $t2_total > 0 ? round(($t2_obt / $t2_total) * 100, 2) : 0;

                $term1_grade = $gradeFromPercentage($term1_percentage);
                $term2_grade = $gradeFromPercentage($term2_percentage);
            @endphp

            <tr class="total-row">
                <td></td>
                <td>Total</td>
                <td>{{ $t1_total }}</td>
                <td>{{ $t1_obt }}</td>
                <td></td>
                <td></td>
                <td>{{ $t2_total }}</td>
                <td>{{ $t2_obt }}</td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <br>
        <!-- FOOTER -->
        <div class="footer">
            <table style="padding:0px 50px;">

                {{-- <tr> <td>Term 1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> --}}
                {{-- <tr> <td>term 2</td> --}}
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Attendance:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b
                            style="text-decoration: underline">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $student->attendance }}
                            /
                            {{ @$student->session ? $student->session->t1_working_days + $student->session->t2_working_days : '0' }}
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
                    <td>Term-1
                        Rank:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        ______________
                    </td>
                    <td>Term-2
                        Rank:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        ________________</td>

                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Code of Conduct: _____________</td>

                    <td>Grade:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_______<u>{{ $term1_grade }}</u>______
                    </td>
                    <td>Grade:
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;________<u>{{ $term1_grade }}</u>_______
                    </td>

                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>School Reopens: &nbsp;&nbsp;_____________</td>
                    <td>Term One Percentage:&nbsp;&nbsp;&nbsp; &nbsp;_____<U>{{ $term1_percentage }}%</U>____
                    </td>
                    <td>Term Two
                        Percentage:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;______<u>{{ $term2_percentage }}%_____
                    </td>
                </tr>
            </table>
        </div>
        <br><br>

        <table class="grading-table" width="80%" style="margin: 20px auto;">
            <tr>
                <td colspan="7" class="grading-system">Grading System</td>
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
        <div class="remarks-section">
            <div style="line-height: 1.6; text-align: justify;">
                <b>Teacher's Remarks:</b>
                <p>{{ $student->remarks ?? 'No remarks provided.' }}</p>
            </div>
        </div>
        <br><br><br><br>
        <!-- SIGNATURES -->
        <div class="signature">
            <div class="line">Class Teacher's Signature</div>
            <div class="line">Principal's Signature</div>

            <div class="line">Parent's Signature</div>
        </div>

    </div>

</body>

</html>
