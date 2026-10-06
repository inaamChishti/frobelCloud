@extends('layouts.branchDashboardApp')

@section('content')
<style>
    .main-content {
        background: #ffffff;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        margin: 20px;
        border: 1px solid #20439F;
    }
    
    .dashboard-title {
        color: #20439F;
        margin-bottom: 30px;
        font-size: 32px;
        font-weight: 700;
    }
    
    .sections-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }
    
    .section-card {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border: 2px solid #e0e7ff;
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        display: block;
        color: inherit;
    }
    
    .section-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(32, 67, 159, 0.2);
        border-color: #20439F;
        text-decoration: none;
        color: inherit;
    }
    
    .section-card-icon {
        font-size: 48px;
        color: #20439F;
        margin-bottom: 20px;
    }
    
    .section-card-title {
        font-size: 22px;
        font-weight: 700;
        color: #20439F;
        margin-bottom: 10px;
    }
    
    .section-card-description {
        font-size: 14px;
        color: #6c757d;
        line-height: 1.6;
    }
    
    .section-card-badge {
        display: inline-block;
        background: #20439F;
        color: #ffffff;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 15px;
    }
</style>

<div class="main-content">
    <h1 class="dashboard-title">
        <i class="fas fa-bolt"></i> Crash Course Management
    </h1>
    
    <p style="color: #6c757d; font-size: 16px; margin-bottom: 30px;">
        Manage all aspects of crash course packages, registrations, payments, attendance, and records from the sections below.
    </p>
    
    <div class="sections-grid">
        @if (!empty($pagePermissions['crash_course_package_management']) &&
            $pagePermissions['crash_course_package_management'] === 'on')
            <a href="{{ url('crash-course/packages') }}" class="section-card">
                <div class="section-card-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="section-card-title">Package Management</div>
                <div class="section-card-description">
                    Create, update, and manage crash course packages with pricing information.
                </div>
                <span class="section-card-badge">Section 1</span>
            </a>
        @endif

        @if (!empty($pagePermissions['crash_course_registration']) &&
            $pagePermissions['crash_course_registration'] === 'on')
            <a href="{{ url('crash-course/registration') }}" class="section-card">
                <div class="section-card-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="section-card-title">Registration & Payment</div>
                <div class="section-card-description">
                    Register students for packages and manage payment transactions.
                </div>
                <span class="section-card-badge">Section 2</span>
            </a>
        @endif

        @if (!empty($pagePermissions['crash_course_attendance']) &&
            $pagePermissions['crash_course_attendance'] === 'on')
            <a href="{{ url('crash-course/attendance') }}" class="section-card">
                <div class="section-card-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="section-card-title">Attendance</div>
                <div class="section-card-description">
                    Mark and manage attendance records for crash course sessions.
                </div>
                <span class="section-card-badge">Section 3</span>
            </a>
        @endif

        @if (!empty($pagePermissions['crash_course_view_records']) &&
            $pagePermissions['crash_course_view_records'] === 'on')
            <a href="{{ url('crash-course/records') }}" class="section-card">
                <div class="section-card-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="section-card-title">View Records</div>
                <div class="section-card-description">
                    View and export all crash course records including registrations, payments, and attendance.
                </div>
                <span class="section-card-badge">Section 4</span>
            </a>
        @endif
    </div>
</div>
@endsection

