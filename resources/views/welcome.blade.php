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
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .header {
            /* background: linear-gradient(to bottom, #ffffff 0%, #e8e8e8 100%); */
            padding: 20px;
            /* border-bottom: 3px solid #c00; */
        }
        
        .school-name {
            font-family: 'Edwardian Script ITC', cursive;
            color:red;
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
            color:black;
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
    <div class="report-card">
        <!-- Header -->
        <div class="header position-relative">
           <div class="header-title">
            <div class=""></div>
            <div class=""></div>
                 <h1 class="school-name">The Lynx School</h1>
                 <img src="images/lynx_logo.png" alt="Lynx Logo" class="school-logo">
            </div>
            <div class="school-info">
                <div>699, Rehman Baba Road Sector I-8/4 Islamabad</div>
                <div>Phone: 051-4860932 &nbsp;&nbsp; Email: info@thelynxschool.edu.pk</div>
            </div>
        </div>
        
        <!-- Report Title -->
        <div class="report-title">First Term Report Card</div>
        <div class="report-title" style="background: #dddbdb;">Academic Year 2025-26</div>
        
        <!-- Student Information -->
        <table class="student-info-table" width="95%">
            <tr>
                <td class="label-cell"><b>Serial No:</b></td>
                <td width="15%">1</td>
                <td class="label-cell"><b>Class & Sec:</b></td>
                <td width="15%">5 Alfa</td>
                <td class="label-cell"><b>Attendance:</b></td>
                <td width="15%">/0</td>
            </tr>
            <tr>
                <td class="label-cell"><b>Student Name:</b></td>
                <td colspan="3">Arsalan Saeed</td>
                <td class="label-cell"><b>Roll No:</b></td>
                <td>AS14253</td>
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
                <tr>
                    <td>1</td>
                    <td style="text-align: left; padding-left: 20px;"><b>English</b></td>
                    <td>100</td>
                    <td>0</td>
                    <td></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td style="text-align: left; padding-left: 20px;"><b>Maths</b></td>
                    <td>100</td>
                    <td>0</td>
                    <td></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td style="text-align: left; padding-left: 20px;"><b>Urdu</b></td>
                    <td>100</td>
                    <td>0</td>
                    <td></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td style="text-align: left; padding-left: 20px;"><b>History/ Geography</b></td>
                    <td>50</td>
                    <td>0</td>
                    <td></td>
                </tr>
                <tr>
                    <td>5</td>
                    <td style="text-align: left; padding-left: 20px;"><b>Science</b></td>
                    <td>50</td>
                    <td>0</td>
                    <td></td>
                </tr>
                <tr>
                    <td>6</td>
                    <td style="text-align: left; padding-left: 20px;"><b>Islamiat</b></td>
                    <td>50</td>
                    <td>0</td>
                    <td></td>
                </tr>
                <tr>
                    <td>7</td>
                    <td style="text-align: left; padding-left: 20px;"><b>ICT</b></td>
                    <td>50</td>
                    <td>0</td>
                    <td></td>
                </tr>
                {{-- <tr class="total-row">
                    <td colspan="2" style="text-align: right; padding-right: 20px;"><b>Total</b></td>
                    <td>500</td>
                    <td>0</td>
                    <td></td>
                </tr> --}}
                <tr class="total-row">
                    <td></td>
                    <td style="text-align: right; padding-right: 20px;"><b>Total</b></td>
                    <td>500</td>
                     <td>0</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        
        <!-- Grade and Percentage -->
        <div class="grade-section">
            <div>
                <strong>Grade:</strong>
                <span class="grade-box">&nbsp;</span>
            </div>
            <div>
                <strong>Overall Percentage:</strong>
                <span class="percentage-box">0.00</span>
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
            {{-- <div class="remarks-title">Teacher's Remarks:</div> --}}
            <div style="line-height: 1.6; text-align: justify;">
                <b>Teacher's Remarks:</b> Osman Asim has a natural curiosity that drives him to explore his surroundings and gain knowledge about the world. He is always enthusiastic about participating in various class activities and approaches them with a thoughtful perspective. This term, he has truly blossomed as a progressive achiever, showing a strong understanding of all the academic concepts, developing independent writing skills, and attaining fluency in reading. However, is being encouraged to demonstrate good manners, respect his peers and follow class rules effectively. Keep shining!
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
</body>
</html>