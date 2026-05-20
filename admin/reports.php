<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require 'controllers/ReportsController.php';

$extra_css = ['../assets/css/style.css'];
$extra_js_head = ['https://cdn.jsdelivr.net/npm/chart.js'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100">
    
    <?php include '../includes/topbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Archive Overview</h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Access completed historic records, enrollment profiles, and parsed grade extracts.</p>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <!-- Card 1 -->
        <div class="col-md-4">
            <div class="dash-panel d-flex align-items-center gap-3 p-3 px-4" style="border-radius: 12px; border: 1px solid #E2E8F0; background: white;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: #EEF2FF; color: #6366F1; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M22 11h-6"/></svg>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1;">7</div>
                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">Archived Student Records</div>
                </div>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="col-md-4">
            <div class="dash-panel d-flex align-items-center gap-3 p-3 px-4" style="border-radius: 12px; border: 1px solid #E2E8F0; background: white;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: #ECFDF5; color: #10B981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1;">86%</div>
                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">Passing Rate (Passed: 6)</div>
                </div>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="col-md-4">
            <div class="dash-panel d-flex align-items-center gap-3 p-3 px-4" style="border-radius: 12px; border: 1px solid #E2E8F0; background: white;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF2F2; color: #EF4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1;">14%</div>
                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">Failure / Rem. Rate (Failed: 1)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Row -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="position-relative">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9CA3AF;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="form-control" placeholder="Search name, student no, section..." style="padding-left: 32px; font-size: 0.8rem; border-radius: 8px; border: 1px solid #E2E8F0; width: 280px; box-shadow: none;">
            </div>
            <div class="d-flex align-items-center gap-2">
                <span style="font-size: 0.8rem; color: #64748B;">Program:</span>
                <select class="form-select border-0 shadow-none bg-transparent fw-medium" style="font-size: 0.85rem; color: #1E293B; width: auto; padding-left: 0; padding-right: 24px; cursor: pointer;">
                    <option>All Programs</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span style="font-size: 0.8rem; color: #64748B;">Status:</span>
                <select class="form-select border-0 shadow-none bg-transparent fw-medium" style="font-size: 0.85rem; color: #1E293B; width: auto; padding-left: 0; padding-right: 24px; cursor: pointer;">
                    <option>All Remarks</option>
                </select>
            </div>
        </div>
        <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-2" style="background: #4F46E5; color: white; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 500;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export Archive
        </button>
    </div>

    <!-- Data Table -->
    <div class="dash-panel p-0" style="overflow: hidden; border-radius: 12px; border: 1px solid #E2E8F0; background: white;">
        <table class="table m-0" style="font-size: 0.8rem; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #E2E8F0;">
                    <th style="padding: 16px 24px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">STUDENT NO</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">STUDENT NAME</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">GENDER</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">SECTION</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">PROGRAM</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">INSTRUCTOR</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">MIDTERM</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">FINALS</th>
                    <th style="padding: 16px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">REMARKS</th>
                    <th style="padding: 16px 24px; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border: none;">DATE ARCHIVED</th>
                </tr>
            </thead>
            <tbody>
                <!-- Row 1 -->
                <tr style="border-bottom: 1px solid #F1F5F9; transition: background-color 0.15s; cursor: pointer;" onmouseover="this.style.backgroundColor='#F8FAFC'" onmouseout="this.style.backgroundColor='transparent'">
                    <td style="padding: 16px 24px; color: #64748B; border: none;">2024-00104</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 500; border: none;">Santos, Jose P.</td>
                    <td style="padding: 16px; color: #64748B; border: none;">Male</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 500; border: none;">CWTS-1A</td>
                    <td style="padding: 16px; border: none;"><span style="background: #EEF2FF; color: #6366F1; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600;">CWTS</span></td>
                    <td style="padding: 16px; color: #64748B; border: none;">Prof. Julian Santos</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 600; font-family: monospace; border: none;">1.50</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 600; font-family: monospace; border: none;">1.20</td>
                    <td style="padding: 16px; border: none;"><span style="background: #ECFDF5; color: #10B981; padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 500;">Passed</span></td>
                    <td style="padding: 16px 24px; color: #64748B; border: none;">May 12, 2026</td>
                </tr>
                <!-- Row 2 -->
                <tr style="border-bottom: 1px solid #F1F5F9; transition: background-color 0.15s; cursor: pointer;" onmouseover="this.style.backgroundColor='#F8FAFC'" onmouseout="this.style.backgroundColor='transparent'">
                    <td style="padding: 16px 24px; color: #64748B; border: none;">2024-00215</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 500; border: none;">Mendoza, Maria L.</td>
                    <td style="padding: 16px; color: #64748B; border: none;">Female</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 500; border: none;">LTS-1B</td>
                    <td style="padding: 16px; border: none;"><span style="background: #ECFDF5; color: #10B981; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600;">LTS</span></td>
                    <td style="padding: 16px; color: #64748B; border: none;">Prof. Adam Yusuf</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 600; font-family: monospace; border: none;">1.75</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 600; font-family: monospace; border: none;">1.50</td>
                    <td style="padding: 16px; border: none;"><span style="background: #ECFDF5; color: #10B981; padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 500;">Passed</span></td>
                    <td style="padding: 16px 24px; color: #64748B; border: none;">May 12, 2026</td>
                </tr>
                <!-- Row 3 -->
                <tr style="border-bottom: 1px solid #F1F5F9; transition: background-color 0.15s; cursor: pointer;" onmouseover="this.style.backgroundColor='#F8FAFC'" onmouseout="this.style.backgroundColor='transparent'">
                    <td style="padding: 16px 24px; color: #64748B; border: none;">2024-00302</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 500; border: none;">Cruz, Lester G.</td>
                    <td style="padding: 16px; color: #64748B; border: none;">Male</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 500; border: none;">ROTC-1A</td>
                    <td style="padding: 16px; border: none;"><span style="background: #FFF7ED; color: #F59E0B; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600;">ROTC</span></td>
                    <td style="padding: 16px; color: #64748B; border: none;">1Lt. Daniel Castillo</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 600; font-family: monospace; border: none;">2.00</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 600; font-family: monospace; border: none;">1.75</td>
                    <td style="padding: 16px; border: none;"><span style="background: #ECFDF5; color: #10B981; padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 500;">Passed</span></td>
                    <td style="padding: 16px 24px; color: #64748B; border: none;">May 14, 2026</td>
                </tr>
                <!-- Row 4 -->
                <tr style="border-bottom: 1px solid #F1F5F9; transition: background-color 0.15s; cursor: pointer;" onmouseover="this.style.backgroundColor='#F8FAFC'" onmouseout="this.style.backgroundColor='transparent'">
                    <td style="padding: 16px 24px; color: #64748B; border: none;">2024-00118</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 500; border: none;">Garcia, Ana T.</td>
                    <td style="padding: 16px; color: #64748B; border: none;">Female</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 500; border: none;">CWTS-1B</td>
                    <td style="padding: 16px; border: none;"><span style="background: #EEF2FF; color: #6366F1; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600;">CWTS</span></td>
                    <td style="padding: 16px; color: #64748B; border: none;">1st Class Ofc. Rita Cruz</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 600; font-family: monospace; border: none;">3.00</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 600; font-family: monospace; border: none;">3.00</td>
                    <td style="padding: 16px; border: none;"><span style="background: #ECFDF5; color: #10B981; padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 500;">Passed</span></td>
                    <td style="padding: 16px 24px; color: #64748B; border: none;">May 12, 2026</td>
                </tr>
                <!-- Row 5 -->
                <tr style="border-bottom: 1px solid #F1F5F9; transition: background-color 0.15s; cursor: pointer;" onmouseover="this.style.backgroundColor='#F8FAFC'" onmouseout="this.style.backgroundColor='transparent'">
                    <td style="padding: 16px 24px; color: #64748B; border: none;">2024-00448</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 500; border: none;">Aquino, Ferdinand R.</td>
                    <td style="padding: 16px; color: #64748B; border: none;">Male</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 500; border: none;">ROTC-1B</td>
                    <td style="padding: 16px; border: none;"><span style="background: #FFF7ED; color: #F59E0B; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600;">ROTC</span></td>
                    <td style="padding: 16px; color: #64748B; border: none;">Prof. Priya Garcia</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 600; font-family: monospace; border: none;">5.00</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 600; font-family: monospace; border: none;">5.00</td>
                    <td style="padding: 16px; border: none;"><span style="background: #FEF2F2; color: #EF4444; padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 500;">Failed</span></td>
                    <td style="padding: 16px 24px; color: #64748B; border: none;">May 14, 2026</td>
                </tr>
                <!-- Row 6 -->
                <tr style="transition: background-color 0.15s; cursor: pointer;" onmouseover="this.style.backgroundColor='#F8FAFC'" onmouseout="this.style.backgroundColor='transparent'">
                    <td style="padding: 16px 24px; color: #64748B; border: none;">2024-00289</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 500; border: none;">Del Rosario, Clara M.</td>
                    <td style="padding: 16px; color: #64748B; border: none;">Female</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 500; border: none;">LTS-1C</td>
                    <td style="padding: 16px; border: none;"><span style="background: #ECFDF5; color: #10B981; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600;">LTS</span></td>
                    <td style="padding: 16px; color: #64748B; border: none;">Prof. Marco Lim</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 600; font-family: monospace; border: none;">2.25</td>
                    <td style="padding: 16px; color: #0F172A; font-weight: 600; font-family: monospace; border: none;">2.00</td>
                    <td style="padding: 16px; border: none;"><span style="background: #ECFDF5; color: #10B981; padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 500;">Passed</span></td>
                    <td style="padding: 16px 24px; color: #64748B; border: none;">May 12, 2026</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
