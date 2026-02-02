<!DOCTYPE html>
<html>
<head>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            font-size: 12px; 
            margin:0; 
            padding:0; 
            color: #333;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        .header { 
            text-align: center; 
            margin-bottom: 25px; 
            padding-bottom: 20px;
            border-bottom: 1px solid #555;
        }
        .header img { 
            max-height: 80px; 
            margin-bottom: 10px; 
        }
        h2 { 
            margin: 0; 
            font-size: 22px; 
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        h4 { 
            margin: 5px 0; 
            font-size: 14px; 
            font-weight: normal; 
            color: #555;
        }
        .accent-text {
            color: #3498db;
            font-weight: 500;
        }
        .sheet-title {
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            display: inline-block;
            margin: 15px 0;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #333 !important;
        }
        th, td { 
            border: 1px solid #333 !important;
            border: 1px solid #ddd; 
            padding: 8px 6px; 
            text-align: center; 
            font-size: 12px; 
        }
        th {
            font-weight: 600;
            background-color: #f2f6fc;
            color: #2c3e50;
        }
        .left-align { 
            text-align: left; 
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tbody tr:hover {
            background-color: #f0f7ff;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 11px;
            color: #777;
            padding-top: 15px;
            border-top: 1px solid #555;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin: 15px 0;
            padding: 10px 15px;
            background-color: #f8fafd;
            border-radius: 4px;
            border-left: 4px solid #3498db;
        }
        .info-item {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        @php
            $school = \DB::table('schools')->first();

            /**
             * Custom function to format a score:
             * If the score is an integer (e.g., 12.00), it returns it without decimals (12).
             * If the score has decimals (e.g., 12.5), it keeps the decimals (12.5).
             * @param mixed $score The score value from the database.
             * @return string The formatted score string, or '-' if null/empty.
             */
            $formatScore = function ($score) {
                if (is_null($score) || $score === '') {
                    return '-';
                }
                $num = (float) $score;
                // Check if the number is mathematically equal to its integer cast (e.g., 12.0 == 12)
                if ($num == (int) $num) {
                    return (int) $num; // Returns 12
                }
                return number_format($num, 2, '.', ''); // Returns 12.50 or 12.5 (depending on how the number is stored)
            };
        @endphp

        <div class="header">
            @if($school && $school->logo)
                <img src="{{ public_path('uploads/school_logo/' . $school->logo) }}" alt="School Logo">
            @endif
            <h2>{{ $school->school_name ?? 'SCHOOL NAME' }}</h2>
            <h4>{{ $school->motto ?? '' }}</h4>
            <h4>{{ $school->address ?? '' }}</h4>
            <div class="sheet-title">MARK SHEET</div>
        </div>

        <div class="info-section">
            <div class="info-item">
                <span class="accent-text">Class:</span> {{ $section->section_name }} - {{ $class->class_name }} ({{ $arm->arm_name }})
            </div>
            <div class="info-item">
                <span class="accent-text">Subject:</span> {{ $subject->subject_name }}
            </div>
            <div class="info-item">
                <span class="accent-text">Term:</span> {{ $term->term_name }} | <span class="accent-text">Session:</span> {{ $session->session_name }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>S/N</th>
                    <th class="left-align">Reg Number</th>
                    <th class="left-align">Full Name</th>
                    {{-- Modification: Apply $formatScore to remove .00 --}}
                    <th>CA1 ({{ $formatScore($marksSetting->max_ca1) }})</th>
                    <th>CA2 ({{ $formatScore($marksSetting->max_ca2) }})</th>
                    <th>Exam ({{ $formatScore($marksSetting->max_exam) }})</th>
                    <th>Total</th>
                    <th>Grade</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $i => $s)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="left-align">{{ $s->reg_number }}</td>
                    <td class="left-align">{{ $s->full_name }}</td>
                    
                    {{-- APPLYING THE FORMATTING FUNCTION --}}
                    <td>{{ $formatScore($s->ca1) }}</td>
                    <td>{{ $formatScore($s->ca2) }}</td>
                    <td>{{ $formatScore($s->exam) }}</td>
                    <td>{{ $formatScore($s->total) }}</td>
                    
                    <td>{{ $s->grade ?? '' }}</td>
                    <td>{{ $s->remark ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            Generated on {{ date('F j, Y') }} | {{ $school->school_name ?? 'School Management System' }}
        </div>
    </div>
</body>
</html>