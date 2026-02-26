@extends('layouts.dashboard')

@section('title', 'BroadSheet')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

tbody td:first-child {
    position:sticky;
    left:0;
    background-color:#f8f9fa;
    z-index:5;
    font-weight:500;
}

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

.summary-card {
    background: linear-gradient(135deg, #679767 0%, #4a6d4a 100%);
    color:white;
    border-radius:0.5rem;
    padding:1.25rem;
    margin-bottom:1rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    min-height: 100px;
}

.chart-container {
    position:relative;
    margin:auto;
    height:400px; 
}

@media print {
    body * { visibility: hidden; }
    #printableTable, #printableTable * { visibility: visible; }
    #printableTable { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>

<div class="container-fluid mt-3">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0" style="color:#679767;">
                    <i class="fa-solid fa-table me-2"></i> BroadSheet
                </h5>
                <small class="text-muted" id="currentSelection">Filter to visualize performance</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-success btn-sm" id="printBtn" disabled><i class="fa-solid fa-print me-1"></i> Print</button>
                <button class="btn btn-outline-success btn-sm" id="exportBtn" disabled><i class="fa-solid fa-download me-1"></i> Excel</button>
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-4" id="summaryCards" style="display:none;">
                <div class="col-md-3">
                    <div class="summary-card">
                        <small class="opacity-75 text-uppercase">Total Students</small>
                        <h3 class="mb-0 fw-bold" id="totalStudents">0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <small class="opacity-75 text-uppercase">Total Subjects</small>
                        <h3 class="mb-0 fw-bold" id="totalSubjects">0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <small class="opacity-75 text-uppercase">Class Average</small>
                        <h3 class="mb-0 fw-bold"><span id="classAverage">0</span>%</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <small class="opacity-75 text-uppercase">Top Performer</small>
                        <h5 class="mb-0 fw-bold mt-1 text-truncate" id="topStudentName">-</h5>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="horizontal-panel">
                        <div class="flex-grow-1">
                            <label class="form-label small text-muted mb-1">Active Session/Term</label>
                            <div class="d-flex gap-2">
                                <input type="text" class="form-control filter-input bg-light" value="{{ $activeSession?->session_name }}" readonly>
                                <input type="text" class="form-control filter-input bg-light" value="{{ $activeTerm?->term_name }}" readonly>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <label class="form-label small text-muted mb-1">Select Class Information</label>
                            <div class="d-flex gap-2">
                                <select id="section_id" class="form-select filter-input">
                                    <option value="">-- Section --</option>
                                    @foreach ($sections as $sec)
                                        <option value="{{ $sec->section_id }}">{{ $sec->section_name }}</option>
                                    @endforeach
                                </select>
                                <select id="class_id" class="form-select filter-input"><option value="">-- Class --</option></select>
                                <select id="arm_id" class="form-select filter-input"><option value="">-- Arm --</option></select>
                                <button id="loadBtn" class="btn btn-success px-4"><i class="fa-solid fa-rotate me-1"></i> Load</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="term_id" value="{{ $activeTerm?->term_id }}">
            <input type="hidden" id="session_id" value="{{ $activeSession?->session_id }}">

            <div class="card border-0 shadow-sm" id="printableTable">
                <div class="table-fixed">
                    <table class="table table-bordered table-hover mb-0" id="broadsheetTable">
                        <thead id="thead"></thead>
                        <tbody id="tbody"></tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white"><h6 class="fw-bold mb-0" style="color:#679767;">Subject Average Distribution</h6></div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="subjectChart"></canvas>
                            </div>
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
<script>
$(function(){
    let subjectChart = null;

    /* --- Cascade Filters --- */
    $('#section_id').change(function(){
        $.get('/marks-entry/get-classes/'+this.value, function(res){
            $('#class_id').html('<option value="">-- Class --</option>');
            res.forEach(c => $('#class_id').append(`<option value="${c.class_id}">${c.class_name}</option>`));
        });
    });

    $('#class_id').change(function(){
        $.get('/marks-entry/get-arms/'+this.value, function(res){
            $('#arm_id').html('<option value="">-- Arm --</option>');
            res.forEach(a => $('#arm_id').append(`<option value="${a.arm_id}">${a.arm_name}</option>`));
        });
    });

    /* --- Load Data --- */
    $('#loadBtn').click(function(){
        const $btn = $(this);
        let params = {
            section_id: $('#section_id').val(),
            class_id: $('#class_id').val(),
            arm_id: $('#arm_id').val(),
            term_id: $('#term_id').val(),
            session_id: $('#session_id').val()
        };

        if(!params.section_id || !params.class_id || !params.arm_id) return alert('Please select Section, Class and Arm');

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        
        $.get("{{ route('broadsheet.load') }}", params, function(res){
            if(res.students && res.students.length > 0){
                buildTable(res.subjects, res.students);
                updateSummary(res.subjects, res.students);
                buildPieChart(res.subjects, res.students);
                $('#printBtn, #exportBtn').prop('disabled', false);
            } else {
                alert('No data found for this selection');
                resetUI();
            }
        }).always(() => $btn.prop('disabled', false).html('<i class="fa-solid fa-rotate me-1"></i> Load'));
    });

    function buildTable(subjects, students){
        let head = `<tr><th>S/N</th><th>Reg No</th><th>Name</th>`;
        subjects.forEach(s => head += `<th>${s.subject.subject_name}</th>`);
        head += `<th>Total</th><th>Avg</th><th>Pos</th></tr>`;
        $('#thead').html(head);

        let rows = '';
        students.forEach((st, i) => {
            rows += `<tr><td>${i+1}</td><td>${st.reg || ''}</td><td class="text-start">${st.name}</td>`;
            subjects.forEach(s => {
                let score = st.scores[s.subject.subject_id] ?? '-';
                rows += `<td>${score}</td>`;
            });
            rows += `<td class="fw-bold">${st.total}</td><td>${st.avg}</td><td>${st.position}</td></tr>`;
        });
        $('#tbody').html(rows);
    }

    function updateSummary(subjects, students){
        let classTotalAvg = 0;
        let topStudent = { name: '-', avg: -1 };

        students.forEach(st => {
            let avg = parseFloat(st.avg) || 0;
            classTotalAvg += avg;
            if(avg > topStudent.avg) {
                topStudent = { name: st.name, avg: avg };
            }
        });

        $('#totalStudents').text(students.length);
        $('#totalSubjects').text(subjects.length);
        $('#classAverage').text((classTotalAvg / students.length).toFixed(1));
        $('#topStudentName').text(topStudent.name);
        $('#summaryCards').fadeIn();
    }

    function buildPieChart(subjects, students){
        const labels = [], data = [], colors = [
            '#679767', '#3498db', '#e67e22', '#e74c3c', '#9b59b6', 
            '#1abc9c', '#f1c40f', '#34495e', '#7f8c8d', '#2ecc71'
        ];
        
        subjects.forEach(sub => {
            let sum = 0, count = 0;
            students.forEach(st => {
                let s = st.scores[sub.subject.subject_id];
                if(s && !isNaN(s)){ sum += parseFloat(s); count++; }
            });
            labels.push(sub.subject.subject_name);
            data.push(count ? (sum / count).toFixed(1) : 0);
        });

        const ctx = document.getElementById('subjectChart').getContext('2d');
        if(subjectChart) subjectChart.destroy();
        
        subjectChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.label}: ${ctx.raw}% Average`
                        }
                    }
                }
            }
        });
    }

    /* --- Utilities --- */
    $('#exportBtn').click(function(){
        let html = document.getElementById("broadsheetTable").outerHTML;
        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
    });

    $('#printBtn').click(() => window.print());

    function resetUI(){
        $('#thead, #tbody').html('');
        $('#summaryCards').hide();
        if(subjectChart) subjectChart.destroy();
    }
});
</script>
@endsection