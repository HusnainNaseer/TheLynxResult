<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Lynx School - Report Card</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.cdnfonts.com/css/edwardian-script-itc');

        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .report-card {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .header {
            /* background: linear-gradient(to bottom, #ffffff 0%, #e8e8e8 100%); */
            padding: 20px;
            /* border-bottom: 3px solid #c00; */
        }

        .school-name {
            font-family: 'Edwardian Script ITC', cursive;
            color: red;
            font-size: 48px;
            text-align: center;
            margin: 0;
            font-weight: normal;
        }

        .school-info {
            font-weight: bold;
            text-align: center;
            font-family: Calibri, sans-serif;
            font-size: 12px;
            margin-top: 10px;
            font-size: 13px;
        }

        .logo {
            position: absolute;
            right: 30px;
            top: 20px;
            width: 60px;
            height: 60px;
            background: #6B46C1;
            border-radius: 10px;
        }

        .report-title {
            /* background: #d3d3d3; */
            text-align: center;
            padding: 8px;
            font-weight: bold;
            font-family: Calibri, sans-serif;
            font-size: 14px;
            margin: 0;
        }

        .student-info-table {
            /* width: 100%; */
            margin: 20px;
        }

        .student-info-table td {
            font-family: Calibri, sans-serif;
            padding: 8px;
            font-size: 11px;
            border: 1px solid #ddd;
        }

        .label-cell {
            /* background: #f8f9fa; */
            font-weight: bolder;
            width: 120px;
        }

        .marks-table {
            margin: 20px;
            border-collapse: collapse;
        }

        .marks-table th {

            /* background: #f8f9fa; */
            padding: 10px;
            border: 1px solid #000;
            font-weight: bolder;
            text-align: center;
        }

        .marks-table td {
            font-family: Calibri, sans-serif;
            font-weight: 11px;
            padding: 10px;
            border: 1px solid #000;
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            /* background: #f8f9fa; */
        }

        .grade-section {
            margin: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 30px;
        }

        .grade-box {
            background: yellow;
            border: 3px solid #000;
            padding: 5px 40px;
            /* font-weight: bold; */
        }

        .percentage-box {
            background: #8BC34A;
            border: 3px solid #000;
            padding: 5px 20px;
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

        .grading-table {
            margin: 0 20px 20px 20px;
        }

        .grading-table td {
            font-family: Calibri, sans-serif;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            /* font-weight: bold; */
        }

        .remarks-section {
            margin: 20px;
            border: 1px solid #000;
            padding: 15px;
            /* background: #f9f9f9; */
        }

        .remarks-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .signatures {
            font-family: Calibri, sans-serif;
            margin: 30px 20px 20px 20px;
            display: flex;
            justify-content: space-between;
        }

        .signature-block {
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            margin-bottom: 5px;
        }

        .header-title {

            font-family: "Edwardian Script ITC", "Adwardian SC", cursive;
            font-size: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .school-logo {
            width: 120px;
            height: auto;
        }
    </style>
</head>

<body>
    @forelse ($subjects as $student)
        <div class="report-card">
            <!-- Header -->
            <div class="header position-relative">
                <div class="header-title">
                    <div class=""></div>
                    <div class=""></div>
                    <h1 class="school-name">The Lynx School</h1>
                    <img src="{{ asset('images/lynx_logo.png') }}" alt="Lynx Logo" class="school-logo">

                </div>
                <div class="school-info">
                    <div>699, Rehman Baba Road Sector I-8/4 Islamabad</div>
                    <div>Phone: 051-4860932 &nbsp;&nbsp; Email: info@thelynxschool.edu.pk</div>
                </div>
            </div>

            <!-- Report Title -->
            <div class="report-title">First Term Report Card</div>
            <div class="report-title" style="background: #dddbdb;">Academic Year
                {{ @$student->session ? $student->session->title : '' }}</div>

            <!-- Student Information -->
            <table class="student-info-table" width="95%">
                <tr>
                    <td class="label-cell"><b>Serial No:</b></td>
                    <td width="15%">{{ $student->id }}</td>
                    <td class="label-cell"><b>Class / Sec:</b></td>
                    <td width="15%">{{ $student->class }} / {{ $student->section }}</td>
                    <td class="label-cell"><b>Attendance:</b></td>
                    <td width="15%"> {{ $student->t1_working_days }} /
                        {{ @$student->session ? $student->session->t1_working_days : '' }}</td>
                </tr>
                <tr>
                    <td class="label-cell"><b>Student Name:</b></td>
                    <td colspan="3">{{ $student->name }}</td>
                    <td class="label-cell"><b>Roll No:</b></td>
                    <td>{{ $student->rollno }}</td>
                </tr>
            </table>

            <!-- Marks Table -->
            <table class="marks-table" width="95%">
                <thead>
                    <tr>
                        <th width="5%"></th>
                        <th width="40%">Subject</th>
                        <th width="18%">Total Marks</th>
                        <th width="18%">Marks Obtained</th>
                        <th width="19%">Subject Grade</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @dd($student->marks) --}}
                    @php
                        $total_marks = 0;
                        $total_obtained = 0;
                    @endphp
                    @foreach ($student->marks as $mark)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td style="text-align: left; padding-left: 20px;">
                                <b>{{ @$mark->subject->subject_name }}</b>
                            </td>
                            <td>{{ @$mark->subject->term_one_marks }}</td>
                            <td>{{ @$mark->term_one_mark }}</td>
                            <td>{{ @$mark->term_one_grade }}</td>
                        </tr>
                        @php
                            $total_marks += @$mark->subject->term_one_marks;
                            $total_obtained += $mark->term_one_mark;

                        @endphp
                    @endforeach
                    <tr class="total-row">
                        <td></td>
                        <td style="text-align: right; padding-right: 20px;"><b>Total</b></td>
                        <td>{{ $total_marks }}</td>
                        <td>{{ $total_obtained }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            @php
                function calculatePercentage($obtained, $total)
                {
                    if ($total == 0) {
                        return 0;
                    }
                    return ($obtained / $total) * 100;
                }

                function calculateGrade($percent)
                {
                    if ($percent >= 90) {
                        return 'A+';
                    }
                    if ($percent >= 80) {
                        return 'A';
                    }
                    if ($percent >= 70) {
                        return 'B';
                    }
                    if ($percent >= 60) {
                        return 'C';
                    }
                    if ($percent >= 50) {
                        return 'D';
                    }
                    return 'F';
                }
                $overall_percentage = calculatePercentage($total_obtained, $total_marks);
                $overall_grade = calculateGrade($overall_percentage);

            @endphp
            <!-- Grade and Percentage -->
            <div class="grade-section">
                <div>
                    <strong>Grade:</strong>
                    <span class="grade-box">&nbsp{{ $overall_grade }}</span>
                </div>
                <div>
                    <strong>Overall Percentage:</strong>
                    <span class="percentage-box">{{ round($overall_percentage, 2) }}</span>
                    <strong>%</strong>
                </div>
            </div>

            <!-- Grading System -->
            {{-- <div class="grading-system">Grading System</div> --}}
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

            <!-- Teacher's Remarks -->
            <div class="remarks-section">
                <div style="line-height: 1.6; text-align: justify;">
                    <b>Teacher's Remarks:</b>
                    <p>{{ $student->remarks ?? 'No remarks provided.' }}</p>
                </div>
            </div>


            <!-- Signatures -->
            <div class="signatures">
                <div class="signature-block">
                    <div class="signature-line">Arsalan Saeed</div>
                    <div><b>Class Teacher</b></div>
                </div>
                <div class="signature-block">
                    <div class="signature-line">Mrs. Atiya Mahroof</div>
                    <div><b>Principal</b></div>
                </div>
            </div>
        </div>
        <div style="page-break-after: always;"></div>
    @empty
        <div style="padding: 40px; text-align: center;">
            <h2>No Results Found</h2>
            <p>No student results are available for display.</p>
        </div>
    @endforelse
</body>

</html>
