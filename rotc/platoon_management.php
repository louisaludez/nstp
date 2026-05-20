<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ROTC') {
    header("Location: ../login.php");
    exit;
}

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/rotc_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100" style="background-color: #F8FAFC; min-height: 100vh;">
    
    <?php include '../includes/topbar.php'; ?>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <h5 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.15rem;">Platoon Management Overview</h5>
            <div class="text-muted" style="font-size: 0.85rem;">Click a platoon to view its assigned officers or upload a master list.</div>
        </div>
        <div class="d-flex gap-3">
            <button class="btn" style="background: white; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 0.85rem; font-weight: 500; color: #475569; padding: 8px 16px; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                <i class="bi bi-funnel"></i> All Platoons <i class="bi bi-chevron-right text-muted" style="font-size: 0.75rem;"></i>
            </button>
            <button class="btn" data-bs-toggle="modal" data-bs-target="#newPlatoonModal" style="background: #0F172A; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 500; color: white; padding: 8px 16px; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-shield"></i> New Platoon
            </button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <!-- Officers -->
        <div class="col-md-6">
            <div style="background: white; border: 1px solid #E2E8F0; border-radius: 12px; padding: 24px; display: flex; align-items: center; gap: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: #EEF2FF; color: #6366F1; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #0F172A; line-height: 1.2;">6</div>
                    <div style="font-size: 0.8rem; color: #64748B;">Total Assigned Officers</div>
                </div>
            </div>
        </div>
        
        <!-- Active Platoons -->
        <div class="col-md-6">
            <div style="background: white; border: 1px solid #E2E8F0; border-radius: 12px; padding: 24px; display: flex; align-items: center; gap: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: #FFFBEB; color: #F59E0B; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="bi bi-shield"></i>
                </div>
                <div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #0F172A; line-height: 1.2;">3</div>
                    <div style="font-size: 0.8rem; color: #64748B;">Active Platoons</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Platoon List Panel -->
    <div style="background: white; border: 1px solid #E2E8F0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden;">
        <!-- Search -->
        <div style="padding: 16px 24px; border-bottom: 1px solid #F1F5F9;">
            <div class="position-relative" style="width: 320px;">
                <i class="bi bi-search position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
                <input type="text" class="form-control" placeholder="Search platoons..." style="padding-left: 36px; border-radius: 8px; font-size: 0.85rem; border: 1px solid #E2E8F0; box-shadow: none; background: #F8FAFC;">
            </div>
        </div>
        
        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-borderless mb-0" style="font-size: 0.85rem;">
                <thead style="border-bottom: 1px solid #F1F5F9;">
                    <tr>
                        <th style="padding: 16px 24px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Platoon Name</th>
                        <th style="padding: 16px 24px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Total Cadets</th>
                        <th style="padding: 16px 24px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid #F8FAFC; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'" data-bs-toggle="modal" data-bs-target="#alphaPlatoonModal">
                        <td style="padding: 20px 24px; font-weight: 600; color: #0F172A;">Alpha Platoon</td>
                        <td style="padding: 20px 24px; color: #475569;">3 Cadets</td>
                        <td style="padding: 20px 24px;">
                            <span style="background: #ECFDF5; color: #10B981; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 500;">1st Semester</span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #F8FAFC; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 20px 24px; font-weight: 600; color: #0F172A;">Bravo Platoon</td>
                        <td style="padding: 20px 24px; color: #475569;">2 Cadets</td>
                        <td style="padding: 20px 24px;">
                            <span style="background: #FFFBEB; color: #F59E0B; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 500;">2nd Semester</span>
                        </td>
                    </tr>
                    <tr style="cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 20px 24px; font-weight: 600; color: #0F172A;">Charlie Platoon</td>
                        <td style="padding: 20px 24px; color: #475569;">1 Cadets</td>
                        <td style="padding: 20px 24px;">
                            <span style="background: #FFFBEB; color: #F59E0B; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 500;">2nd Semester</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- New Platoon Modal -->
<div class="modal fade" id="newPlatoonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content" style="border: none; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <div class="modal-header border-0" style="padding: 24px 24px 16px;">
                <div>
                    <h5 class="modal-title fw-bold mb-1" style="color: #0F172A; font-size: 1.15rem;">New Platoon</h5>
                    <div class="text-muted" style="font-size: 0.85rem;">Fill in the details to add a new ROTC platoon</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>
            <div class="modal-body" style="padding: 16px 24px;">
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Platoon Name</label>
                        <input type="text" class="form-control" placeholder="e.g. Delta" style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;">
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Status</label>
                        <select class="form-select" style="border-radius: 8px; border-color: #6366F1; font-size: 0.85rem; padding: 10px 12px; box-shadow: none; color: #0F172A; background-color: #F8FAFC;">
                            <option>1st Semester</option>
                            <option>2nd Semester</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-2">
                    <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Import Cadets List XLSX File</label>
                    <div style="border: 1px dashed #A7F3D0; background: #ECFDF5; border-radius: 8px; padding: 24px; text-align: center; cursor: pointer;">
                        <i class="bi bi-upload" style="color: #10B981; margin-right: 6px;"></i>
                        <span style="color: #10B981; font-size: 0.85rem; font-weight: 500;">Upload XLSX List</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0" style="padding: 16px 24px 24px;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="padding: 10px 20px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; color: #475569; border: 1px solid #E2E8F0; background: white;">Cancel</button>
                <button type="button" class="btn btn-dark" style="padding: 10px 20px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; background: #0F172A; border: none; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-shield"></i> Create Platoon
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alpha Platoon Modal -->
<div class="modal fade" id="alphaPlatoonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            
            <!-- Dark Header -->
            <div class="modal-header border-0 d-flex justify-content-between align-items-start" style="background: #0F172A; padding: 24px; border-radius: 12px 12px 0 0;">
                <div>
                    <h4 class="modal-title fw-bold mb-1" style="color: white; font-size: 1.25rem;">Alpha Platoon — Assign Cadets Section</h4>
                    <div style="color: #94A3B8; font-size: 0.85rem;">3 assigned · Click a row to select & remove</div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-danger" style="border-radius: 8px; font-size: 0.8rem; font-weight: 500; padding: 6px 16px; border-color: rgba(239,68,68,0.3); color: #FCA5A5; display: flex; align-items: center; gap: 6px;">
                        <i class="bi bi-trash3"></i> Delete Section
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            
            <!-- Table -->
            <div class="modal-body p-0" style="background: white; max-height: 400px; overflow-y: auto;">
                <table class="table table-borderless mb-0" style="font-size: 0.85rem;">
                    <thead style="position: sticky; top: 0; background: white; border-bottom: 1px solid #F1F5F9; z-index: 1;">
                        <tr>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">#</th>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Officer Name</th>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Rank</th>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Specialty</th>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Date of Birth</th>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Gender</th>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Residential Address</th>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Cell #</th>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase;">Email Address</th>
                            <th style="padding: 16px; font-size: 0.7rem; font-weight: 600; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #F8FAFC; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 16px; color: #64748B;">1</td>
                            <td style="padding: 16px; font-weight: 600; color: #0F172A;">C/Sgt. Cruz, L.</td>
                            <td style="padding: 16px; color: #64748B;">Sgt</td>
                            <td style="padding: 16px;">
                                <span style="background: #EEF2FF; color: #6366F1; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 500;">Rifle</span>
                            </td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #94A3B8; text-align: center;"><i class="bi bi-chevron-right"></i></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #F8FAFC; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 16px; color: #64748B;">2</td>
                            <td style="padding: 16px; font-weight: 600; color: #0F172A;">C/Pvt. Mendoza, F.</td>
                            <td style="padding: 16px; color: #64748B;">Pvt</td>
                            <td style="padding: 16px;">
                                <span style="background: #EEF2FF; color: #6366F1; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 500;">Rifle</span>
                            </td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #94A3B8; text-align: center;"><i class="bi bi-chevron-right"></i></td>
                        </tr>
                        <tr style="cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 16px; color: #64748B;">3</td>
                            <td style="padding: 16px; font-weight: 600; color: #0F172A;">C/Pvt. Garcia, T.</td>
                            <td style="padding: 16px; color: #64748B;">Pvt</td>
                            <td style="padding: 16px;">
                                <span style="background: #EEF2FF; color: #6366F1; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 500;">Medical</span>
                            </td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #64748B;">—</td>
                            <td style="padding: 16px; color: #94A3B8; text-align: center;"><i class="bi bi-chevron-right"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Assign Form -->
            <div class="modal-footer border-0" style="background: white; padding: 24px; border-top: 1px solid #F1F5F9; display: block;">
                <div style="font-size: 0.75rem; font-weight: 700; color: #94A3B8; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 16px;">Assign Officer to Alpha Section</div>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Officer Name</label>
                        <input type="text" class="form-control" placeholder="Officer Name (Last, First M.)" style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Rank</label>
                        <input type="text" class="form-control" placeholder="Rank" style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Specialty</label>
                        <input type="text" class="form-control" placeholder="Specialty" style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Date of Birth</label>
                        <input type="text" class="form-control" placeholder="Date of Birth" style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;">
                    </div>
                </div>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Gender</label>
                        <select class="form-select" style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none; color: #475569;">
                            <option>Gender</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Residential Address</label>
                        <input type="text" class="form-control" placeholder="Residential Address" style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Cell #</label>
                        <input type="text" class="form-control" placeholder="Cell #" style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;">
                    </div>
                </div>
                
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 500; color: #64748B;">Email Address</label>
                        <input type="text" class="form-control" placeholder="Email Address" style="border-radius: 8px; border-color: #E2E8F0; font-size: 0.85rem; padding: 10px 12px; box-shadow: none;">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-dark w-100" style="padding: 10px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; background: #0F172A; border: none; display: flex; justify-content: center; align-items: center; gap: 6px;">
                            <i class="bi bi-plus-lg"></i> Add Cadets
                        </button>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
