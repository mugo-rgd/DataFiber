@extends('layouts.app')

@section('title', 'AI Recommendations')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="page-title mb-0">
                            <i class="fas fa-lightbulb text-warning me-2"></i>AI Recommendations
                        </h4>
                        <p class="text-muted mb-0">Intelligent recommendations to optimize debt collection and reduce risk</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('finance.ai.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                        </a>
                        <button onclick="exportRecommendations()" class="btn btn-outline-success">
                            <i class="fas fa-download me-1"></i> Export Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendation Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0" id="criticalCount">0</h3>
                    <small>Critical Actions</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0" id="highCount">0</h3>
                    <small>High Priority</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0" id="mediumCount">0</h3>
                    <small>Medium Priority</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0" id="lowCount">0</h3>
                    <small>Low Priority</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical & High Priority Recommendations -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Critical & High Priority Actions
                    </h5>
                </div>
                <div class="card-body" id="criticalRecommendations">
                    <div class="text-center py-4">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <br>Loading recommendations...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medium Priority Recommendations -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Medium Priority Actions
                    </h5>
                </div>
                <div class="card-body" id="mediumRecommendations">
                    <div class="text-center py-4">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <br>Loading recommendations...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Priority Recommendations -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Low Priority & Strategic Actions
                    </h5>
                </div>
                <div class="card-body" id="lowRecommendations">
                    <div class="text-center py-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <br>Loading recommendations...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Implementation Tracker -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks me-2"></i>Recommendation Implementation Tracker
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Recommendation</th>
                                    <th>Priority</th>
                                    <th>Expected Impact</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="trackerTable">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <br>Loading tracker...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load recommendations from AI
function loadRecommendations() {
    // Simulate API call
    setTimeout(() => {
        const recommendations = {
            critical: [
                {
                    title: "Immediate Collection Action Required",
                    description: "MINISTRY OF ICT has outstanding balance of $11,503,102.05 overdue by 45 days. Initiate legal collection process.",
                    impact: "Potential recovery of $11.5M",
                    deadline: "Immediate",
                    action: "contact_legal"
                },
                {
                    title: "High-Value Customer Risk",
                    description: "KENGEN PLC with $9,253,248.00 outstanding. Schedule executive-level meeting to discuss payment plan.",
                    impact: "Prevent default of $9.25M",
                    deadline: "Within 48 hours",
                    action: "schedule_meeting"
                },
                {
                    title: "Aging Debt Over 90 Days",
                    description: "3 customers have invoices overdue by more than 90 days totaling $2.1M. Escalate to collection agency.",
                    impact: "Recover $2.1M",
                    deadline: "This week",
                    action: "escalate"
                }
            ],
            high: [
                {
                    title: "Automated Payment Reminders",
                    description: "Implement automated SMS and email reminders for customers with invoices due in 7 days.",
                    impact: "Reduce overdue rate by 25%",
                    deadline: "Next week",
                    action: "implement"
                },
                {
                    title: "Payment Plan Offers",
                    description: "Offer structured payment plans to 5 customers with outstanding >$50,000 each.",
                    impact: "Secure $500K+ in committed payments",
                    deadline: "2 weeks",
                    action: "create_plans"
                }
            ],
            medium: [
                {
                    title: "Customer Credit Review",
                    description: "Review and adjust credit limits for customers who consistently pay late.",
                    impact: "Reduce future risk exposure",
                    deadline: "This month",
                    action: "review"
                },
                {
                    title: "Early Payment Discount Program",
                    description: "Introduce 2% discount for payments within 10 days to improve cash flow.",
                    impact: "Improve collection rate by 15%",
                    deadline: "Next month",
                    action: "implement"
                }
            ],
            low: [
                {
                    title: "Customer Portal Enhancement",
                    description: "Add self-service payment options and real-time balance tracking to customer portal.",
                    impact: "Reduce administrative overhead",
                    deadline: "Quarterly",
                    action: "plan"
                },
                {
                    title: "Predictive Analytics Integration",
                    description: "Integrate AI models to predict payment delays before they occur.",
                    impact: "Proactive risk management",
                    deadline: "Next quarter",
                    action: "research"
                }
            ]
        };

        // Update counts
        document.getElementById('criticalCount').innerHTML = recommendations.critical.length;
        document.getElementById('highCount').innerHTML = recommendations.high.length;
        document.getElementById('mediumCount').innerHTML = recommendations.medium.length;
        document.getElementById('lowCount').innerHTML = recommendations.low.length;

        // Render critical recommendations
        renderRecommendations('criticalRecommendations', recommendations.critical, 'danger');
        renderRecommendations('mediumRecommendations', recommendations.medium, 'warning');
        renderRecommendations('lowRecommendations', recommendations.low, 'success');

        // Render tracker table
        renderTracker(recommendations);

    }, 1000);
}

function renderRecommendations(containerId, recommendations, color) {
    const container = document.getElementById(containerId);
    if (!recommendations.length) {
        container.innerHTML = '<div class="alert alert-info">No recommendations in this category.</div>';
        return;
    }

    let html = '<div class="row">';
    recommendations.forEach((rec, index) => {
        html += `
            <div class="col-md-6 mb-3">
                <div class="card border-${color} h-100">
                    <div class="card-header bg-${color} bg-opacity-10">
                        <h6 class="mb-0">
                            <i class="fas fa-${color == 'danger' ? 'exclamation-triangle' : (color == 'warning' ? 'clock' : 'info-circle')} me-2"></i>
                            ${rec.title}
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">${rec.description}</p>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">Expected Impact</small>
                                <strong class="text-success">${rec.impact}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Deadline</small>
                                <strong class="text-${color}">${rec.deadline}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <button class="btn btn-sm btn-${color}" onclick="takeAction('${rec.action}')">
                            <i class="fas fa-check-circle me-1"></i> Take Action
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="dismissRecommendation(${index})">
                            <i class="fas fa-times me-1"></i> Dismiss
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
}

function renderTracker(recommendations) {
    const allRecs = [
        ...recommendations.critical.map(r => ({...r, priority: 'Critical', status: 'Not Started'})),
        ...recommendations.high.map(r => ({...r, priority: 'High', status: 'In Progress'})),
        ...recommendations.medium.map(r => ({...r, priority: 'Medium', status: 'Planned'})),
        ...recommendations.low.map(r => ({...r, priority: 'Low', status: 'Under Review'}))
    ];

    const priorityColors = {
        'Critical': 'danger',
        'High': 'warning',
        'Medium': 'info',
        'Low': 'success'
    };

    const statusColors = {
        'Not Started': 'secondary',
        'In Progress': 'primary',
        'Planned': 'info',
        'Under Review': 'warning',
        'Completed': 'success'
    };

    let html = '';
    allRecs.forEach((rec, index) => {
        html += `
            <tr>
                <td>${rec.title}</td>
                <td>
                    <span class="badge bg-${priorityColors[rec.priority]}">${rec.priority}</span>
                </td>
                <td>${rec.impact}</td>
                <td>
                    <span class="badge bg-${statusColors[rec.status]}">${rec.status}</span>
                </td>
                <td>
                    <select class="form-select form-select-sm" onchange="updateStatus(${index}, this.value)" style="width: 130px;">
                        <option value="Not Started" ${rec.status == 'Not Started' ? 'selected' : ''}>Not Started</option>
                        <option value="In Progress" ${rec.status == 'In Progress' ? 'selected' : ''}>In Progress</option>
                        <option value="Planned" ${rec.status == 'Planned' ? 'selected' : ''}>Planned</option>
                        <option value="Under Review" ${rec.status == 'Under Review' ? 'selected' : ''}>Under Review</option>
                        <option value="Completed" ${rec.status == 'Completed' ? 'selected' : ''}>Completed</option>
                    </select>
                </td>
            </tr>
        `;
    });

    document.getElementById('trackerTable').innerHTML = html;
}

function takeAction(action) {
    alert(`Action triggered: ${action}\n\nThis feature will be implemented soon.`);
}

function dismissRecommendation(index) {
    if (confirm('Are you sure you want to dismiss this recommendation?')) {
        alert('Recommendation dismissed. It will be archived.');
        location.reload();
    }
}

function updateStatus(index, status) {
    alert(`Status updated to: ${status}\n\nThis change will be saved.`);
}

function exportRecommendations() {
    alert('Export functionality will generate a PDF report of all recommendations.');
}

// Load recommendations on page load
document.addEventListener('DOMContentLoaded', loadRecommendations);
</script>
@endsection
