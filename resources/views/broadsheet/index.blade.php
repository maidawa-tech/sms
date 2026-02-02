@extends('layouts.dashboard')

@section('title', 'BroadSheet')

@section('content')
<style>
/* INPUT + SELECT STYLING */
.filter-input{
    background:#fff;
    border:1px solid #679767;
    height:38px;
}

.filter-input:focus,
.form-select:focus {
    border-color:#679767 !important;
    box-shadow:0 0 0 0.20rem rgba(103,151,103,0.25) !important;
}

.table-fixed {
    max-height:550px;
    overflow-y:auto;
    overflow-x:auto;
    white-space:nowrap;
    border:1px solid #e9ecef;
    border-radius:0.375rem;
}

thead th{ 
    white-space:nowrap; 
    position:sticky;
    top:0;
    background-color:#679767 !important;
    color:white !important;
    z-index:10;
    font-weight:600;
}

/* Sticky serial number column */
tbody td:first-child {
    position:sticky;
    left:0;
    background-color:#f8f9fa;
    z-index:5;
    font-weight:500;
}

/* Hover effects */
.table-hover tbody tr:hover {
    background-color:rgba(103,151,103,0.05);
}

.horizontal-panel{
    display:flex;
    gap:10px;
    align-items:center;
    margin-bottom:1rem;
    flex-wrap:wrap;
}

.horizontal-panel select,
.horizontal-panel button{
    flex:1;
    min-width:150px;
    height:38px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .horizontal-panel select,
    .horizontal-panel button {
        min-width:100%;
    }
    
    .table-fixed {
        font-size:0.875rem;
    }
}

.toast-container{
    position:fixed;
    top:1rem;
    right:1rem;
    z-index:1080;
}

/* EMPTY STATE */
.empty-state{
    padding:3rem;
    text-align:center;
    color:#6c757d;
    background:#f8f9fa;
    border-radius:0.375rem;
    border:2px dashed #dee2e6;
}

.empty-state i {
    font-size:3rem;
    margin-bottom:1rem;
    color:#adb5bd;
}

/* Card improvements */
.card {
    box-shadow:0 0.125rem 0.25rem rgba(0,0,0,0.075);
    transition:box-shadow 0.2s ease-in-out;
}

.card:hover {
    box-shadow:0 0.5rem 1rem rgba(0,0,0,0.1);
}

/* Score highlighting */
.high-score {
    color:#198754;
    font-weight:600;
}

.low-score {
    color:#dc3545;
    font-weight:500;
}

/* Position badges */
.position-badge {
    display:inline-block;
    padding:0.25rem 0.5rem;
    border-radius:50rem;
    font-size:0.75rem;
    font-weight:600;
}

.position-1 { background-color:#ffd700; color:#000; }
.position-2 { background-color:#c0c0c0; color:#000; }
.position-3 { background-color:#cd7f32; color:#fff; }
.position-other { background-color:#e9ecef; color:#495057; }

/* ADJUSTED: Reduced height for tighter spacing */
.chart-container {
    position:relative;
    margin:auto;
    height:350px; /* Reduced from 400px */
}

/* Summary cards */
.summary-card {
    background:linear-gradient(135deg, #679767 0%, #8bc34a 100%);
    color:white;
    border-radius:0.5rem;
    padding:1rem;
    margin-bottom:1rem;
}

/* Filter badge */
.filter-badge {
    background-color:#e8f5e8;
    color:#679767;
    border:1px solid #679767;
    border-radius:0.375rem;
    padding:0.25rem 0.5rem;
    font-size:0.875rem;
    margin-left:0.5rem;
}
</style>

<div class="toast-container" id="toastContainer"></div>

<div class="container-fluid mt-3">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0" style="color:#679767;">
                    <i class="fa-solid fa-table me-2"></i> BroadSheet
                </h5>
                <small class="text-muted" id="currentSelection">Select filters to load data</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-success btn-sm" id="printBtn" disabled>
                    <i class="fa-solid fa-print me-1"></i> Print
                </button>
                <button class="btn btn-outline-success btn-sm" id="exportBtn" disabled>
                    <i class="fa-solid fa-download me-1"></i> Export
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-4" id="summaryCards" style="display:none;">
                <div class="col-md-3">
                    <div class="summary-card">
                        <h6 class="mb-0"><i class="fa-solid fa-users me-2"></i> Total Students</h6>
                        <h3 class="mb-0 mt-2" id="totalStudents">0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <h6 class="mb-0"><i class="fa-solid fa-book-open me-2"></i> Total Subjects</h6>
                        <h3 class="mb-0 mt-2" id="totalSubjects">0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <h6 class="mb-0"><i class="fa-solid fa-chart-line me-2"></i> Class Average</h6>
                        <h3 class="mb-0 mt-2" id="classAverage">0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <h6 class="mb-0"><i class="fa-solid fa-trophy me-2"></i> Top Student</h6>
                        <h6 class="mb-0 mt-2" id="topStudent">-</h6>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3" style="color:#679767;">
                        <i class="fa-solid fa-filter me-2"></i> Filters
                    </h6>
                    <div class="horizontal-panel">
                        <div class="w-100">
                            <label class="form-label small text-muted mb-1">Academic Year</label>
                            <div class="d-flex align-items-center">
                                <input type="text" class="form-control filter-input me-2" 
                                    value="{{ $activeSession?->session_name ?? 'N/A' }}" readonly 
                                    style="background-color:#f8f9fa;">
                                <input type="text" class="form-control filter-input" 
                                    value="{{ $activeTerm?->term_name ?? 'N/A' }}" readonly 
                                    style="background-color:#f8f9fa;">
                            </div>
                        </div>
                        
                        <div class="w-100 mt-2">
                            <label class="form-label small text-muted mb-1">Class Selection</label>
                            <div class="horizontal-panel">
                                <select id="section_id" class="form-select filter-input">
                                    <option value="">-- Select Section --</option>
                                    @foreach ($sections as $sec)
                                        <option value="{{ $sec->section_id }}">{{ $sec->section_name }}</option>
                                    @endforeach
                                </select>

                                <select id="class_id" class="form-select filter-input">
                                    <option value="">-- Select Class --</option>
                                </select>

                                <select id="arm_id" class="form-select filter-input">
                                    <option value="">-- Select Arm --</option>
                                </select>

                                <button id="loadBtn" class="btn btn-success d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-eye me-2"></i> Load BroadSheet
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="term_id" value="{{ $activeTerm?->term_id }}">
            <input type="hidden" id="session_id" value="{{ $activeSession?->session_id }}">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0" style="color:#679767;">
                        <i class="fa-solid fa-list me-2"></i> Student Results
                    </h6>
                    <div class="small text-muted" id="resultCount">No data loaded</div>
                </div>
                <div class="card-body p-0">
                    <div class="table-fixed">
                        <table class="table table-bordered table-hover mb-0 mt-2" id="broadsheetTable">
                            <thead id="thead"></thead>
                            <tbody id="tbody"></tbody>
                        </table>
                    </div>
                    <div id="tableFooter" class="p-3 bg-light border-top">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fa-solid fa-info-circle me-1"></i>
                                    <span id="tableInfo">Select filters and load data</span>
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted" id="lastUpdated">
                                    Last updated: {{ now()->format('M d, Y h:i A') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4 border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0" style="color:#679767;">
                        <i class="fa-solid fa-chart-pie me-2"></i> Subject Performance Distribution
                    </h6>
                    <div class="small text-muted" id="chartInfo">Average scores by subject</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="chart-container" id="chartWrapper">
                                <canvas id="subjectChart"></canvas>
                            </div>
                            <div class="text-center mt-3" id="chartLegend"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>
@endsection

@section('scripts')
@vite(['resources/js/app.js'])

<script>
$(function(){

    let subjectChart = null;
    let currentData = null;

    $.ajaxSetup({
        headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}
    });

    /* ---------------- LOAD CLASSES ---------------- */
    $('#section_id').change(function(){
        $('#class_id').html('<option value="">Loading...</option>');
        $('#arm_id').html('<option value="">-- Select Arm --</option>');
        resetDisplay();

        $.get('/marks-entry/get-classes/'+this.value, function(res){
            $('#class_id').html('<option value="">-- Select Class --</option>');
            res.forEach(c => $('#class_id').append(
                `<option value="${c.class_id}">${c.class_name}</option>`
            ));
        });
    });

    /* ---------------- LOAD ARMS ---------------- */
    $('#class_id').change(function(){
        $('#arm_id').html('<option value="">Loading...</option>');
        resetDisplay();

        $.get('/marks-entry/get-arms/'+this.value, function(res){
            $('#arm_id').html('<option value="">-- Select Arm --</option>');
            res.forEach(a => $('#arm_id').append(
                `<option value="${a.arm_id}">${a.arm_name}</option>`
            ));
        });
    });

    /* ---------------- LOAD BROADSHEET ---------------- */
    $('#loadBtn').click(function(){
        const $btn = $(this);
        const originalHtml = $btn.html();
        
        let section = $('#section_id').val(),
            cls     = $('#class_id').val(),
            arm     = $('#arm_id').val(),
            term    = $('#term_id').val(),
            session = $('#session_id').val();

        if(!section || !cls || !arm){
            showToast('warning','Please select Section, Class and Arm.');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Loading...');
        
        const sectionName = $('#section_id option:selected').text();
        const className = $('#class_id option:selected').text();
        const armName = $('#arm_id option:selected').text();
        $('#currentSelection').html(`<span class="filter-badge">${sectionName}</span> <span class="filter-badge">${className}</span> <span class="filter-badge">${armName}</span>`);

        $('#thead').html(`<tr><th colspan="50" class="text-center"><i class="fa fa-spinner fa-spin me-2"></i> Loading results...</th></tr>`);
        $('#tbody').html('');
        clearChart();
        $('#summaryCards').hide();

        $.get("{{ route('broadsheet.load') }}",
        {section_id:section,class_id:cls,arm_id:arm,term_id:term,session_id:session},
        function(res){
            currentData = res;
            
            if(!res.status || !res.students || !res.students.length){
                showEmptyTable('No students found for the selected criteria.');
                showEmptyChart('No performance data available.');
                showToast('info','No data found for the selected filters.');
                $('#printBtn, #exportBtn').prop('disabled', true);
            } else {
                if(!res.subjects || !res.subjects.length){
                    showEmptyTable('No subjects assigned to this class/arm.');
                    showEmptyChart('Subject analysis unavailable (no subjects assigned).');
                    showToast('warning','No subjects assigned to this class.');
                } else {
                    buildTable(res.subjects, res.students);
                    buildSubjectPieChart(res.subjects, res.students);
                    updateSummaryCards(res.subjects, res.students);
                    $('#printBtn, #exportBtn').prop('disabled', false);
                    $('#resultCount').html(`${res.students.length} student(s) found`);
                    $('#tableInfo').html(`${res.subjects.length} subjects • ${res.students.length} students`);
                }
            }
        }).fail(function(){
            showToast('danger','Failed to load data. Please try again.');
        }).always(function(){
            $btn.prop('disabled', false).html(originalHtml);
        });
    });

    /* ---------------- TABLE BUILDER ---------------- */
    function buildTable(subjects, students){
        let head = `<tr><th width="50">S/N</th><th width="120">Reg Number</th><th width="200">Full Name</th>`;
        subjects.forEach(s => {
            const name = s.subject ? s.subject.subject_name : 'Subject';
            head += `<th width="100">${name}</th>`;
        });
        head += `<th width="100">Subjects</th><th width="100">Total</th><th width="100">Average</th><th width="100">Position</th></tr>`;
        $('#thead').html(head);

        let rows = '';
        students.forEach((st,i)=>{
            rows += `<tr>
                <td class="text-center fw-medium">${i+1}</td>
                <td class="text-muted">${st.reg || 'N/A'}</td>
                <td class="fw-medium">${st.name || 'Unknown'}</td>`;
            
            subjects.forEach(s=>{
                const subId = s.subject ? s.subject.subject_id : null;
                const score = (subId && st.scores) ? (st.scores[subId] ?? '-') : '-';
                const scoreClass = getScoreClass(score, (s.subject ? s.subject.passing_score : 40));
                rows += `<td class="text-center ${scoreClass}">${formatScoreDisplay(score)}</td>`;
            });
            
            const positionClass = st.position <= 3 ? `position-${st.position}` : 'position-other';
            const count = st.scores ? Object.values(st.scores).filter(v=>v!=='-').length : 0;
            
            rows += `<td class="text-center fw-medium">${count}</td>
                     <td class="text-center fw-bold ${getTotalScoreClass(st.total, subjects.length)}">${formatScoreDisplay(st.total)}</td>
                     <td class="text-center fw-bold">${formatScoreDisplay(st.avg)}</td>
                     <td class="text-center"><span class="position-badge ${positionClass}">${st.position || '-'}</span></td>
                     </tr>`;
        });
        $('#tbody').html(rows);
        $('#broadsheetTable tbody tr:even').addClass('table-light');
    }

    function getScoreClass(score, passingScore = 40) {
        if (score === '-' || score === null || score === undefined) return '';
        const num = parseFloat(score);
        if (isNaN(num)) return '';
        if (num >= 80) return 'high-score';
        if (num < passingScore) return 'low-score';
        return '';
    }

    function getTotalScoreClass(total, subjectCount) {
        if (!total || !subjectCount) return '';
        const avg = total / subjectCount;
        if (avg >= 80) return 'high-score';
        if (avg < 40) return 'low-score';
        return '';
    }

    /* ---------------- SUMMARY CARDS ---------------- */
    function updateSummaryCards(subjects, students) {
        if (!students.length) return;
        const totalAvg = students.reduce((sum, st) => sum + parseFloat(st.avg || 0), 0);
        const classAverage = totalAvg / students.length;
        const topStudent = students.find(st => parseInt(st.position) === 1);
        
        $('#totalStudents').text(students.length);
        $('#totalSubjects').text(subjects.length);
        $('#classAverage').text(formatScoreDisplay(classAverage));
        $('#topStudent').text(topStudent ? topStudent.name : '-');
        $('#summaryCards').fadeIn();
    }

    /* ---------------- PIE CHART BUILDER ---------------- */
    function buildSubjectPieChart(subjects, students){
        let labels = [], data = [], backgroundColors = [];
        
        // Generate a color palette based on the number of subjects
        const baseColors = [
            '#679767', '#4A90E2', '#F5A623', '#7ED321', '#BD10E0',
            '#50E3C2', '#B8E986', '#D0021B', '#9013FE', '#417505',
            '#8B572A', '#4A4A4A', '#F8E71C', '#7B9CFF', '#FF6B6B'
        ];
        
        // Calculate average scores for each subject
        subjects.forEach((sub, index)=>{
            if(!sub.subject) return;
            let total = 0, count = 0;
            students.forEach(st=>{
                let v = st.scores ? st.scores[sub.subject.subject_id] : undefined;
                if(v !== '-' && v !== undefined && v !== null){
                    total += parseFloat(v);
                    count++;
                }
            });
            const avg = count ? (total / count) : 0;
            
            labels.push(`${sub.subject.subject_name}\n${formatScoreDisplay(avg)}%`);
            data.push(avg);
            backgroundColors.push(baseColors[index % baseColors.length]);
        });

        renderPieChart(labels, data, backgroundColors);
    }

    function renderPieChart(labels, data, backgroundColors){
        const canvas = document.getElementById('subjectChart');
        if(!canvas) return;
        const ctx = canvas.getContext('2d');
        if(subjectChart) subjectChart.destroy();
        
        // Calculate total average for chart title
        const totalAvg = data.reduce((sum, val) => sum + val, 0);
        const avgScore = data.length ? (totalAvg / data.length).toFixed(1) : 0;
        
        $('#chartInfo').html(`Class Average: ${avgScore}% | ${labels.length} Subjects`);
        
        subjectChart = new Chart(ctx, {
            type:'pie',
            data:{ 
                labels, 
                datasets:[{ 
                    data,
                    backgroundColor: backgroundColors,
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverBorderColor: '#679767',
                    hoverBorderWidth: 3
                }] 
            },
            options:{
                responsive:true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        display: true,
                        position: 'right',
                        labels: {
                            color: '#444',
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map(function(label, i) {
                                        const value = data.datasets[0].data[i];
                                        return {
                                            text: `${label.split('\n')[0]}: ${formatScoreDisplay(value)}%`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            strokeStyle: '#fff',
                                            lineWidth: 2,
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: { 
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label.split('\n')[0]}: ${formatScoreDisplay(value)}% (${percentage}% of total)`;
                            }
                        }
                    },
                    title: {
                        display: false
                    }
                },
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 10,
                        bottom: 10
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                cutout: '40%', // Makes it a donut chart, remove for regular pie
                radius: '85%'
            }
        });
    }

    /* ---------------- UTILITIES ---------------- */
    function showEmptyTable(message){
        $('#thead').html(`<tr><th colspan="50" class="text-center py-4">${message}</th></tr>`);
        $('#tbody').html('');
    }

    function showEmptyChart(message){
        $('#chartWrapper').html(`<div class="empty-state"><i class="fa-solid fa-chart-pie"></i><p>${message}</p></div>`);
        $('#chartInfo').html('No data available');
    }

    function clearChart(){
        if(subjectChart){ subjectChart.destroy(); subjectChart = null; }
        if(!document.getElementById('subjectChart')){
            $('#chartWrapper').html('<canvas id="subjectChart"></canvas>');
        }
        $('#chartInfo').html('Average scores by subject');
    }

    function resetDisplay(){
        $('#thead, #tbody').html('');
        $('#summaryCards').hide();
        $('#printBtn, #exportBtn').prop('disabled', true);
        $('#currentSelection').text('Select filters to load data');
        clearChart();
    }

    function formatScoreDisplay(value){
        if(value === '-' || value === null || value === undefined) return '-';
        let num = parseFloat(value);
        if (isNaN(num)) return value;
        return parseFloat(num.toFixed(2));
    }

    function showToast(type,message){
        let bg = type==='danger'?'bg-danger':type==='warning'?'bg-warning':'bg-success';
        let id='toast'+Date.now();
        $('#toastContainer').append(`
            <div id="${id}" class="toast text-white ${bg} mb-2 border-0 shadow-sm" data-bs-delay="4000">
                <div class="d-flex align-items-center">
                    <div class="toast-body">${message}</div>
                    <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`);
        new bootstrap.Toast(document.getElementById(id)).show();
    }

    $(window).resize(function(){
        if(subjectChart) subjectChart.resize();
    });
});
</script>
@endsection