/* ================================================================
   shared.js — NSTP Portal shared state, data, helpers, and renderers
   Loaded by login.html, coordinator.html, instructor.html, rotc.html
================================================================ */
/* ================================================================
   STATE
================================================================ */
const S = {
  role: null,            // coordinator | instructor | rotc
  email: null,
  sidebarOpen: true,     // burger menu toggle state
  coordPage: 'Dashboard',
  instrPage: 'Overview',
  rotcPage: 'Overview',
  adminPage: 'Accounts',
  editingAccEmail: null,
  selRole: 'coordinator',
  secTab: 'all',
  selApproval: 0,
  calForm: false,
  sectionForm: false,
  inviteForm: false,
  selectedSection: null,
  instrSelectedSection: null,
  showAttachmentsModal: false,
  selectedPlanIndex: null,
  editingPlanIndex: null,
  selectedReportIndex: null,
  editingReportIndex: null,
  selectedCalActivity: null,
  certModal: null,
  batches: [
    { name: 'CWTS 1 Completion', count: 198, status: 'Ready', date: 'Eligible May 30', program: 'CWTS 1' },
    { name: 'LTS 2 Completion', count: 116, status: 'Ready', date: 'Eligible May 30', program: 'LTS 2' },
    { name: 'ROTC Basic Course — Batch 14', count: 84, status: 'Reviewing', date: 'Eligible Jun 6', program: 'ROTC' },
  ],
  recentCerts: [
    { name: 'Maria Aquino', program: 'CWTS 1', id: '2024-00345', issued: 'Today' },
    { name: 'Jose Reyes', program: 'LTS 2', id: '2024-00198', issued: 'Today' },
    { name: 'Anna Bautista', program: 'CWTS 1', id: '2024-00422', issued: 'Yesterday' },
    { name: 'Karl Domingo', program: 'ROTC', id: '2024-00057', issued: 'Yesterday' },
  ],
  selectedRecentCertIdx: null,
  selectedBatchIdx: null,
  newlyImportedBatchIndex: null,
  notifPanel: false,
  profilePanel: false,
  editingProfile: false,
  auditFilter: 'all',
  showAuditFilterMenu: false,
  classesFilter: 'all',
  rCalFilter: 'all',
  showSectionsFilterMenu: false,
  showClassesFilterMenu: false,
  showRostersFilterMenu: false,
  showRCalFilterMenu: false,
  auditSearch: '',
  secSearch: '',
  classesSearch: '',
  rosterSearch: '',
  calSearch: '',
  ocrUploads: [
    { file: 'BSCS-2A_Midterm.pdf', section: 'BSCS-2A', students: 42, status: 'Passed', time: 'Today 10:14 AM' },
    { file: 'BSIT-3B_Finals.jpg', section: 'BSIT-3B', students: 41, status: 'Failed', time: 'Today 9:02 AM' },
    { file: 'BSBA-1A_Midterm.pdf', section: 'BSBA-1A', students: 48, status: 'Passed', time: 'Yesterday' },
    { file: 'BSEd-4A_Finals.png', section: 'BSEd-4A', students: 36, status: 'Failed', time: 'Yesterday' },
  ],
  activities: [
    { title: 'Faculty Council Meeting', date: 'May 14, 2026', time: '10:00 AM', venue: "Dean's Conf. Room", scope: 'Faculty', color: 'bg-indigo-500', status: 'Submitted' },
    { title: 'Midterm Reports Deadline', date: 'May 16, 2026', time: '11:59 PM', venue: 'Coordinator Portal', scope: 'All Instructors', color: 'bg-rose-500', status: 'Submitted' },
    { title: 'OCR Grade Upload Window', date: 'May 20, 2026', time: 'All day', venue: 'Online', scope: 'All Programs', color: 'bg-emerald-500', status: 'Draft' },
    { title: 'Spring Commencement Rehearsal', date: 'May 24, 2026', time: '2:00 PM', venue: 'Main Auditorium', scope: 'Graduating Students', color: 'bg-amber-500', status: 'Submitted' },
  ],
  unassigned: [
    { id: 'c1', name: 'C/Pvt. Reyes, M.', rank: 'Pvt', spec: 'Rifle' },
    { id: 'c2', name: 'C/Cpl. De Leon, A.', rank: 'Cpl', spec: 'Signal' },
    { id: 'c3', name: 'C/Pvt. Aquino, J.', rank: 'Pvt', spec: 'Medical' },
    { id: 'c4', name: 'C/Pvt. Bautista, R.', rank: 'Pvt', spec: 'Support' },
    { id: 'c5', name: 'C/Cpl. Domingo, P.', rank: 'Cpl', spec: 'Rifle' },
    { id: 'c6', name: 'C/Pvt. Espinoza, K.', rank: 'Pvt', spec: 'Signal' },
  ],
  platoons: {
    'Alpha': [{ id: 'a1', name: 'C/Sgt. Cruz, L.', rank: 'Sgt', spec: 'Rifle' }, { id: 'a2', name: 'C/Pvt. Mendoza, F.', rank: 'Pvt', spec: 'Rifle' }, { id: 'a3', name: 'C/Pvt. Garcia, T.', rank: 'Pvt', spec: 'Medical' }],
    'Bravo': [{ id: 'b1', name: 'C/Sgt. Tan, V.', rank: 'Sgt', spec: 'Signal' }, { id: 'b2', name: 'C/Pvt. Lim, S.', rank: 'Pvt', spec: 'Signal' }],
    'Charlie': [{ id: 'ch1', name: 'C/Cpl. Navarro, O.', rank: 'Cpl', spec: 'Support' }],
  },
  dragging: null,
  platSearch: '',
  platoonFilter: 'All',
  platoonSemesters: {
    'Alpha': '1st Semester',
    'Bravo': '2nd Semester',
    'Charlie': '2nd Semester'
  },
  showPlatoonFilterMenu: false,
  revisionModal: false,
  revisionNote: '',
  studentArchive: [
    { studentNo: '2024-00104', name: 'Santos, Jose P.', gender: 'Male', section: 'CWTS-1A', program: 'CWTS', instructor: 'Prof. Julian Santos', midtermGrade: 1.5, finalGrade: 1.2, remarks: 'Passed', schoolYear: '2025-2026', semester: '1st Semester', dateArchived: 'May 12, 2026' },
    { studentNo: '2024-00215', name: 'Mendoza, Maria L.', gender: 'Female', section: 'LTS-1B', program: 'LTS', instructor: 'Prof. Adam Yusuf', midtermGrade: 1.75, finalGrade: 1.5, remarks: 'Passed', schoolYear: '2025-2026', semester: '1st Semester', dateArchived: 'May 12, 2026' },
    { studentNo: '2024-00302', name: 'Cruz, Lester G.', gender: 'Male', section: 'ROTC-1A', program: 'ROTC', instructor: '1Lt. Daniel Castillo', midtermGrade: 2.0, finalGrade: 1.75, remarks: 'Passed', schoolYear: '2025-2026', semester: '1st Semester', dateArchived: 'May 14, 2026' },
    { studentNo: '2024-00118', name: 'Garcia, Ana T.', gender: 'Female', section: 'CWTS-1B', program: 'CWTS', instructor: '1st Class Ofc. Rita Cruz', midtermGrade: 3.0, finalGrade: 3.0, remarks: 'Passed', schoolYear: '2025-2026', semester: '1st Semester', dateArchived: 'May 12, 2026' },
    { studentNo: '2024-00440', name: 'Aquino, Ferdinand R.', gender: 'Male', section: 'ROTC-1B', program: 'ROTC', instructor: 'Prof. Priya Garcia', midtermGrade: 5.0, finalGrade: 5.0, remarks: 'Failed', schoolYear: '2025-2026', semester: '1st Semester', dateArchived: 'May 14, 2026' },
    { studentNo: '2024-00289', name: 'Del Rosario, Clara M.', gender: 'Female', section: 'LTS-1C', program: 'LTS', instructor: 'Prof. Marco Lim', midtermGrade: 2.25, finalGrade: 2.0, remarks: 'Passed', schoolYear: '2025-2026', semester: '1st Semester', dateArchived: 'May 12, 2026' },
    { studentNo: '2024-00350', name: 'Navarro, Oscar S.', gender: 'Male', section: 'ROTC-1A', program: 'ROTC', instructor: '1Lt. Daniel Castillo', midtermGrade: 1.25, finalGrade: 1.0, remarks: 'Passed', schoolYear: '2025-2026', semester: '1st Semester', dateArchived: 'May 14, 2026' }
  ],
  archiveSearch: '',
  archiveFilterProgram: 'All',
  archiveFilterRemarks: 'All',
  selectedInstructorIndex: null,
  editingInstructor: false,
  instructors: [
    { name: '1st Class Ofc. Lester Tan', dept: 'Medic', sections: 'CWTS-1A', students: 158, status: 'Active' },
    { name: '1st Class Ofc. Rita Cruz', dept: 'Marching Band', sections: 'CWTS-1B', students: 124, status: 'Active' },
    { name: 'Prof. Adam Yusuf', dept: 'Mathematics', sections: 'LTS-1B', students: 203, status: 'Active' },
    { name: 'Prof. Priya Garcia', dept: 'Education', sections: 'ROTC-1B', students: 78, status: 'On Leave' },
    { name: 'Prof. Marco Lim', dept: 'Information Tech', sections: 'LTS-1C', students: 165, status: 'Active' },
    { name: 'Prof. Karen Domingo', dept: 'Business Admin', sections: 'ROTC-1A', students: 142, status: 'Active' },
  ],
  selectedActivityIndex: null,
  editingActivity: false
};

/* ================================================================
   ICONS (lucide-style inline SVG)
================================================================ */
const ICONS = {
  menu: `<line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>`,
  dashboard: `<rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>`,
  users: `<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>`,
  dnsc: `<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+ip1sAAAAASUVORK5CYII=" alt="Icon" />`,
  grad: `<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>`,
  filecheck: `<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><polyline points="9 15 11 17 15 13"/>`,
  scan: `<path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><line x1="3" y1="12" x2="21" y2="12"/>`,
  award: `<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>`,
  scroll: `<path d="M8 21h12a2 2 0 0 0 2-2v-2H10v2a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h12v4"/><line x1="16" y1="13" x2="18" y2="13"/><line x1="10" y1="13" x2="14" y2="13"/><line x1="10" y1="17" x2="14" y2="17"/>`,
  calendar: `<rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>`,
  shield: `<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>`,
  logout: `<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>`,
  search: `<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>`,
  bell: `<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>`,
  help: `<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>`,
  arrow: `<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>`,
  plus: `<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>`,
  send: `<line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>`,
  close: `<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>`,
  chevron: `<polyline points="9 18 15 12 9 6"/>`,
  download: `<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>`,
  upload: `<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>`,
  filter: `<polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>`,
  mail: `<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,4 12,13 2,4"/>`,
  lock: `<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>`,
  book: `<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>`,
  clipboard: `<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><line x1="12" y1="11" x2="16" y2="11"/><line x1="12" y1="16" x2="16" y2="16"/>`,
  filetext: `<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>`,
  megaphone: `<path d="m3 11 19-9-9 19-2-8-8-2z"/>`,
  grid: `<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>`,
  pin: `<line x1="12" y1="17" x2="12" y2="22"/><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V17z"/>`,
  mappin: `<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>`,
  clock: `<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>`,
  check2: `<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>`,
  pencil: `<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>`,
  alertc: `<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>`,
  trend: `<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>`,
  calrange: `<rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/>`,
  grip: `<circle cx="9" cy="12" r="1"/><circle cx="9" cy="5" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="19" r="1"/>`,
  userplus: `<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>`,
  more: `<circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/>`,
  arrowup: `<line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/>`,
  star: `<path d="M12 2l2.39 6.95H22l-6.18 4.49L18.21 22 12 17.27 5.79 22l2.39-8.56L2 8.95h7.61z" fill="currentColor" stroke="none"/>`,
  trash: `<path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>`,
  archive: `<path d="M21 8v13H3V8z"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/>`,
};

/* ================================================================
   PROFILE DATA (per role)
================================================================ */
const PROFILE_DATA = {
  coordinator: {
    fullName: 'Dr. Maya Reyes',
    contact: '+63 917 234 5678',
    gmail: 'maya.reyes@gmail.com',
    password: 'CoorD@2026!',
    degree: 'Masteral',
    degreeTitle: 'Master of Science in Educational Management',
  },
  instructor: {
    fullName: 'Prof. Julian Santos',
    contact: '+63 918 876 5432',
    gmail: 'julian.santos@gmail.com',
    password: 'Instr#2026!',
    degree: 'Bachelor',
    degreeTitle: 'Bachelor of Science in Education',
  },
  rotc: {
    fullName: '1Lt. Daniel Castillo',
    contact: '+63 921 555 7890',
    gmail: 'daniel.castillo@gmail.com',
    password: 'ROTC@2026!',
    degree: 'Bachelor',
    degreeTitle: 'Bachelor of Science in Criminology',
  },
  'admin123@dnsc.edu.ph': {
    fullName: 'System Administrator',
    contact: '+63 900 000 0000',
    gmail: 'admin123@dnsc.edu.ph',
    password: 'admin123',
    degree: 'Masteral',
    degreeTitle: 'Master of Science in Information Technology',
  },
};
function ico(name, cls = 'w-4 h-4') {
  return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="${cls}">${ICONS[name] || ''}</svg>`;
}

/* ================================================================
   HELPERS
================================================================ */
function pill(color, text) {
  const m = { slate: 'bg-slate-100 text-slate-700', indigo: 'bg-indigo-50 text-indigo-700', emerald: 'bg-emerald-50 text-emerald-700', amber: 'bg-amber-50 text-amber-700', rose: 'bg-rose-50 text-rose-700', violet: 'bg-violet-50 text-violet-700' };
  return `<span class="text-xs px-2 py-0.5 rounded-full ${m[color] || m.slate}">${text}</span>`;
}

function card(content, { title = '', subtitle = '', action = '', cls = '' } = {}) {
  const hdr = (title || action) ? `<div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3"><div class="min-w-0">${title ? `<div class="text-slate-900 tracking-tight">${title}</div>` : ''}${subtitle ? `<div class="text-xs text-slate-500">${subtitle}</div>` : ''}</div>${action}</div>` : '';
  return `<div class="bg-white rounded-2xl border border-slate-100 shadow-sm ${cls}">${hdr}<div class="p-5">${content}</div></div>`;
}

function pageHdr(title, sub = '', actions = '') {
  return `<div class="flex items-end justify-between gap-4 flex-wrap"><div><div class="text-slate-900 tracking-tight text-xl">${title}</div>${sub ? `<div class="text-sm text-slate-500 mt-0.5">${sub}</div>` : ''}</div>${actions ? `<div class="flex items-center gap-2">${actions}</div>` : ''}</div>`;
}

function tbl(cols, rows, rowAttr = null) {
  const thead = `<thead><tr class="text-left text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-100">${cols.map(c => `<th class="py-2 px-3 font-medium">${c.label}</th>`).join('')}</tr></thead>`;
  const tbody = `<tbody>${rows.map((row, i) => `<tr ${rowAttr ? rowAttr(row, i) : ''} class="border-b border-slate-50 hover:bg-slate-50 cursor-pointer transition">${cols.map(c => `<td class="py-3 px-3 text-slate-700">${c.fn ? c.fn(row) : row[c.key]}</td>`).join('')}</tr>`).join('')}</tbody>`;
  return `<div class="overflow-x-auto"><table class="w-full text-sm">${thead}${tbody}</table></div>`;
}

/* ================================================================
   CHARTS
================================================================ */
function enrollmentChart() {
  const data = [{ m: 'Aug', v: 2310 }, { m: 'Sep', v: 2480 }, { m: 'Oct', v: 2560 }, { m: 'Nov', v: 2620 }, { m: 'Dec', v: 2690 }, { m: 'Jan', v: 2745 }, { m: 'Feb', v: 2802 }, { m: 'Mar', v: 2847 }];
  const W = 640, H = 240, pL = 36, pR = 12, pT = 12, pB = 26, iW = W - pL - pR, iH = H - pT - pB;
  const minY = 2230, maxY = 2907;
  const xf = i => (pL + i * iW / (data.length - 1)).toFixed(1);
  const yf = v => (pT + iH - ((v - minY) / (maxY - minY)) * iH).toFixed(1);
  const ln = data.map((d, i) => `${i ? 'L' : 'M'} ${xf(i)} ${yf(d.v)}`).join(' ');
  const ar = `M ${xf(0)} ${pT + iH} ` + data.map((d, i) => `L ${xf(i)} ${yf(d.v)}`).join(' ') + ` L ${xf(data.length - 1)} ${pT + iH} Z`;
  const ticks = Array.from({ length: 5 }, (_, i) => ({ v: Math.round(minY + (maxY - minY) * i / 4), y: (pT + iH - i * iH / 4).toFixed(1) }));
  return `<div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
    <div class="flex items-start justify-between mb-1">
      <div><div class="text-slate-900 tracking-tight">NSTP Component</div><div class="text-sm text-slate-500">Total students enrolled per semester</div></div>
      <button class="w-7 h-7 rounded-md hover:bg-slate-50 flex items-center justify-center text-slate-400">${ico('more')}</button>
    </div>
    <div class="flex items-end gap-3 mt-3 mb-2"><div class="text-slate-900 tracking-tight text-3xl">2,847</div><div class="text-sm text-emerald-600 pb-1">â–² 4.2% vs last semester</div></div>
    <svg viewBox="0 0 ${W} ${H}" class="w-full h-64" preserveAspectRatio="none">
      ${ticks.map(t => `<line x1="${pL}" x2="${W - pR}" y1="${t.y}" y2="${t.y}" stroke="#f1f5f9" stroke-width="1"/><text x="${pL - 8}" y="${(parseFloat(t.y) + 4).toFixed(1)}" text-anchor="end" font-size="10" fill="#94a3b8">${t.v}</text>`).join('')}
      <path d="${ar}" fill="#6366f1" fill-opacity="0.15"/>
      <path d="${ln}" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linejoin="round"/>
      ${data.map((d, i) => `<circle cx="${xf(i)}" cy="${yf(d.v)}" r="3" fill="white" stroke="#6366f1" stroke-width="2"/><text x="${xf(i)}" y="${H - 8}" text-anchor="middle" font-size="11" fill="#94a3b8">${d.m}</text>`).join('')}
    </svg>
  </div>`;
}

function passFailChart() {
  const data = [{ p: 'BSCS', pass: 312, fail: 24 }, { p: 'BSIT', pass: 286, fail: 31 }, { p: 'BSBA', pass: 401, fail: 22 }, { p: 'BSEd', pass: 198, fail: 18 }, { p: 'BSN', pass: 264, fail: 15 }, { p: 'BSA', pass: 173, fail: 28 }];
  const W = 480, H = 260, pL = 36, pR = 12, pT = 16, pB = 28, iW = W - pL - pR, iH = H - pT - pB;
  const maxY = 441;
  const gW = iW / data.length, bW = 14;
  const yf = v => (pT + iH - (v / maxY) * iH).toFixed(1);
  const hf = v => ((v / maxY) * iH).toFixed(1);
  const ticks = Array.from({ length: 5 }, (_, i) => ({ v: Math.round(maxY * i / 4), y: (pT + iH - i * iH / 4).toFixed(1) }));
  return `<div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
    <div class="flex items-start justify-between mb-2"><div><div class="text-slate-900 tracking-tight">Pass / Fail by Program</div><div class="text-sm text-slate-500">Current semester outcomes</div></div></div>
    <div class="flex items-center gap-4 text-xs text-slate-600 mb-2"><span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>Passed</span><span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>Failed</span></div>
    <svg viewBox="0 0 ${W} ${H}" class="w-full h-64" preserveAspectRatio="none">
      ${ticks.map(t => `<line x1="${pL}" x2="${W - pR}" y1="${t.y}" y2="${t.y}" stroke="#f1f5f9" stroke-width="1"/><text x="${pL - 8}" y="${(parseFloat(t.y) + 4).toFixed(1)}" text-anchor="end" font-size="10" fill="#94a3b8">${t.v}</text>`).join('')}
      ${data.map((d, i) => { const cx = pL + gW * i + gW / 2; return `<rect x="${(cx - bW - 2).toFixed(1)}" y="${yf(d.pass)}" width="${bW}" height="${hf(d.pass)}" rx="4" fill="#10b981"/><rect x="${(cx + 2).toFixed(1)}" y="${yf(d.fail)}" width="${bW}" height="${hf(d.fail)}" rx="4" fill="#f43f5e"/><text x="${cx.toFixed(1)}" y="${H - 10}" text-anchor="middle" font-size="11" fill="#94a3b8">${d.p}</text>`; }).join('')}
    </svg>
  </div>`;
}

/* ================================================================
   SHELL
================================================================ */
const THEMES = {
  indigo: { bg: 'bg-indigo-50/60', bdr: 'border-indigo-100', brand: 'bg-gradient-to-br from-indigo-600 to-blue-500 text-white', active: 'bg-indigo-600 text-white', inactive: 'text-slate-600 hover:bg-white hover:text-slate-900', inactiveIco: 'text-slate-400 group-hover:text-slate-600', badge: 'bg-white/20 text-white', btn: 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-200', avatar: 'bg-gradient-to-br from-amber-400 to-rose-400 text-white', mil: false },
  emerald: { bg: 'bg-emerald-50/60', bdr: 'border-emerald-100', brand: 'bg-gradient-to-br from-emerald-500 to-teal-500 text-white', active: 'bg-emerald-600 text-white', inactive: 'text-slate-600 hover:bg-white hover:text-slate-900', inactiveIco: 'text-slate-400 group-hover:text-slate-600', badge: 'bg-white/20 text-white', btn: 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200', avatar: 'bg-gradient-to-br from-emerald-400 to-teal-500 text-white', mil: false },
  military: { bg: 'bg-slate-900', bdr: 'border-slate-800', brand: 'bg-slate-800 text-amber-300 ring-1 ring-slate-700', active: 'bg-amber-300 text-slate-900 border-amber-300', inactive: 'text-slate-300 border-transparent hover:bg-slate-800 hover:text-white', inactiveIco: 'text-slate-400 group-hover:text-white', badge: 'bg-slate-900 text-amber-300', btn: 'bg-amber-300 hover:bg-amber-400 text-slate-900', avatar: 'bg-slate-800 ring-1 ring-amber-300 text-amber-300', mil: true },
  purple: { bg: 'bg-purple-50/60', bdr: 'border-purple-100', brand: 'bg-gradient-to-br from-purple-600 to-indigo-600 text-white', active: 'bg-purple-600 text-white', inactive: 'text-slate-600 hover:bg-white hover:text-slate-900', inactiveIco: 'text-slate-400 group-hover:text-slate-600', badge: 'bg-white/20 text-white', btn: 'bg-purple-600 hover:bg-purple-700 shadow-purple-200', avatar: 'bg-gradient-to-br from-fuchsia-500 to-purple-600 text-white', mil: false },
};

function getInitials(name) {
  if (!name) return '';
  const clean = name.replace(/^(Dr\.|Prof\.|1Lt\.|Col\.|Capt\.|Lt\.)\s+/i, '');
  const parts = clean.split(' ');
  const first = parts[0]?.[0] || '';
  const last = parts[parts.length - 1]?.[0] || '';
  return (first + last).toUpperCase();
}

function renderShell({ theme = 'indigo', brand, brandSub, navItems, userName, userRole, userInitials, greeting, context, ctaLabel, content }) {
  const t = THEMES[theme];
  const mil = t.mil;
  const brandSvg = mil
    ? `<svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor"><path d="M12 2l2.39 6.95H22l-6.18 4.49L18.21 22 12 17.27 5.79 22l2.39-8.56L2 8.95h7.61z"/></svg>`
    : `<svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>`;

  const navHtml = navItems.map(item => {
    const activeClass = item.active ? t.active : t.inactive;
    const icoClass = item.active ? (mil ? 'text-slate-900' : 'text-white') : t.inactiveIco;
    const bdr = mil ? 'rounded-md border' : 'rounded-lg';
    const badge = item.badge != null ? `<span class="text-[10px] px-1.5 py-0.5 rounded ${item.active ? t.badge : mil ? 'bg-slate-800 text-slate-300' : 'bg-white text-slate-600 border border-slate-200'}">${item.badge}</span>` : '';
    return `<button data-nav="${item.name}" class="w-full group flex items-center gap-3 px-3 py-2.5 ${bdr} text-left transition ${activeClass}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[18px] h-[18px] ${icoClass}">${ICONS[item.ico] || ''}</svg>
      <span class="flex-1 text-sm">${item.name}</span>${badge}
    </button>`;
  }).join('');

  const textClr = mil ? 'text-white' : 'text-slate-900';
  const subClr = mil ? 'text-slate-400' : 'text-slate-500';
  const ctaBtnCls = mil ? `bg-amber-300 hover:bg-amber-400 text-slate-900` : `${t.btn} text-white`;

  return `<div class="min-h-screen w-full bg-slate-50 text-slate-800 flex">
    <aside class="transition-all duration-300 ease-in-out shrink-0 ${t.bg} border-r ${t.bdr} flex flex-col h-screen sticky top-0 ${S.sidebarOpen ? 'w-64' : 'w-0 overflow-hidden !border-r-0'}">
      <div class="px-6 py-6 border-b ${t.bdr}">
        <div class="flex items-center gap-3">
          <img src="DSNC.png" class="w-10 h-10 object-contain" alt="DNSC Logo" />
          <div>
            <div class="tracking-tight ${mil ? 'uppercase text-sm ' + textClr : textClr}">${brand}</div>
            <div class="text-[11px] ${mil ? 'uppercase tracking-wider ' + subClr : subClr}">${brandSub}</div>
          </div>
        </div>
      </div>
      <nav class="flex-1 px-3 py-5 space-y-1 sidebar-nav">
        ${navHtml}
      </nav>
      <div class="px-3 py-4 border-t ${t.bdr}">
        <div class="flex items-center gap-1 rounded-md hover:bg-black/5 transition group">
          <button data-profile-btn class="flex items-center gap-3 px-3 py-2 flex-1 min-w-0 text-left">
            <div class="${mil ? 'rounded-md' : 'rounded-full'} w-9 h-9 ${t.avatar} flex items-center justify-center text-sm shrink-0">${userInitials}</div>
            <div class="flex-1 min-w-0">
              <div class="text-sm truncate ${textClr} group-hover:underline">${userName}</div>
              <div class="text-[11px] truncate ${mil ? 'uppercase tracking-wider ' + subClr : subClr}">${userRole}</div>
            </div>
          </button>
          <button data-logout class="${mil ? 'text-slate-400 hover:text-white' : 'text-slate-400 hover:text-slate-700'} p-2 shrink-0">${ico('logout', 'w-4 h-4')}</button>
        </div>
      </div>
    </aside>
    <div class="flex-1 min-w-0 flex flex-col">
      <header class="flex items-center justify-between gap-6 px-8 py-5 bg-white/70 backdrop-blur border-b border-slate-200 sticky top-0 z-10">
        <div class="flex items-center gap-4">
          <button id="sidebarToggleBtn" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition duration-200 focus:outline-none flex items-center justify-center shrink-0" title="${S.sidebarOpen ? 'Collapse workspace' : 'Expand workspace'}">
            ${ico('menu', 'w-5 h-5')}
          </button>
          <div>
            <div class="text-xs text-slate-500 ${mil ? 'uppercase tracking-wider' : ''}">${context}</div>
            <div class="text-slate-900 tracking-tight text-lg">${greeting}</div>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <div class="relative hidden md:block">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">${ico('search', 'w-4 h-4')}</span>
            <input type="text" placeholder="Searchâ€¦" class="w-72 pl-9 pr-3 py-2 text-sm rounded-lg bg-slate-100 border border-transparent focus:bg-white focus:border-slate-300 focus:outline-none" />
          </div>

          <button id="notifBellBtn" class="w-9 h-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-700 relative">${ico('bell', 'w-[18px] h-[18px]')}<span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"></span></button>
        </div>
      </header>
      <main class="flex-1 px-8 py-7 space-y-6">${content}</main>
    </div>
  </div>
  ${S.profilePanel ? (() => {
      const pd = PROFILE_DATA[S.email] || PROFILE_DATA[S.role] || {};
      const showPw = S.profileShowPw;
      const isMasteral = pd.degree === 'Masteral';
      const mil = t.mil;
      const accentBtn = mil ? 'bg-amber-300 hover:bg-amber-400 text-slate-900' : (theme === 'emerald' ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-indigo-600 hover:bg-indigo-700 text-white');

      if (S.editingProfile) {
        return `<div id="profileOverlay" class="fixed inset-0 z-40 bg-slate-900/30 backdrop-blur-sm"></div>
  <div id="profileDrawer" class="fixed top-0 right-0 h-full w-96 max-w-full z-50 bg-white shadow-2xl border-l border-slate-200 flex flex-col" style="animation:slideInRight .22s cubic-bezier(.4,0,.2,1)">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-br from-slate-900 to-slate-800 shrink-0">
      <div>
        <div class="text-white tracking-tight text-base">Edit Account Info</div>
        <div class="text-slate-400 text-xs mt-0.5">Modify your profile details</div>
      </div>
      <button id="profileClose" class="text-slate-400 hover:text-white p-1 transition">${ico('close', 'w-4 h-4')}</button>
    </div>
    <div class="flex-1 overflow-y-auto p-6 space-y-4">
      <div>
        <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mb-1">Full Name</div>
        <input type="text" id="editProfName" value="${pd.fullName || ''}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none transition-all" />
      </div>
      <div>
        <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mb-1">Phone / Contact</div>
        <input type="text" id="editProfContact" value="${pd.contact || ''}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none transition-all" />
      </div>
      <div>
        <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mb-1">Gmail Address</div>
        <input type="email" id="editProfGmail" value="${pd.gmail || ''}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none transition-all" />
      </div>
      <div>
        <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mb-1">Password</div>
        <input type="text" id="editProfPassword" value="${pd.password || ''}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none font-mono transition-all" />
      </div>
      <div>
        <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mb-1">Degree Type</div>
        <select id="editProfDegree" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none transition-all">
          <option value="Bachelor" ${pd.degree === 'Bachelor' ? 'selected' : ''}>Bachelor</option>
          <option value="Masteral" ${pd.degree === 'Masteral' ? 'selected' : ''}>Masteral</option>
        </select>
      </div>
      <div>
        <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mb-1">Degree Description</div>
        <input type="text" id="editProfDegreeTitle" value="${pd.degreeTitle || ''}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none transition-all" />
      </div>
    </div>
    <div class="px-6 py-4 border-t border-slate-100 flex gap-3 shrink-0 bg-slate-50">
      <button id="cancelProfileBtn" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 text-sm transition">Cancel</button>
      <button id="saveProfileBtn" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl ${accentBtn} text-sm transition font-semibold shadow-sm">${ico('check2', 'w-4 h-4')} Save Changes</button>
    </div>
  </div>`;
      }

      return `<div id="profileOverlay" class="fixed inset-0 z-40 bg-slate-900/30 backdrop-blur-sm"></div>
  <div id="profileDrawer" class="fixed top-0 right-0 h-full w-96 max-w-full z-50 bg-white shadow-2xl border-l border-slate-200 flex flex-col" style="animation:slideInRight .22s cubic-bezier(.4,0,.2,1)">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-br from-slate-900 to-slate-800">
      <div>
        <div class="text-white tracking-tight text-base">Account Information</div>
        <div class="text-slate-400 text-xs mt-0.5">Your profile &amp; credentials</div>
      </div>
      <button id="profileClose" class="text-slate-400 hover:text-white p-1 transition">${ico('close', 'w-4 h-4')}</button>
    </div>
    <div class="flex-1 overflow-y-auto">
      <div class="px-6 py-6 flex flex-col items-center border-b border-slate-100 bg-slate-50">
        <div class="w-20 h-20 rounded-full ${t.avatar} flex items-center justify-center text-2xl font-semibold shadow-lg mb-3">${userInitials}</div>
        <div class="text-slate-900 font-semibold text-lg tracking-tight">${pd.fullName || userName}</div>
        <div class="text-sm text-slate-500 mt-0.5">${userRole}</div>
        ${isMasteral ? `<span class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-violet-50 text-violet-700 border border-violet-200">${ico('grad', 'w-3.5 h-3.5')} ${pd.degreeTitle}</span>` : ''}
      </div>
      <div class="px-6 py-5 space-y-5">
        <div class="text-[10px] uppercase tracking-widest text-slate-400 font-semibold">Contact Information</div>
        <div class="space-y-4">
          <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center shrink-0">${ico('users', 'w-4 h-4')}</div>
            <div class="min-w-0">
              <div class="text-[11px] text-slate-400 mb-0.5">Full Name</div>
              <div class="text-sm text-slate-900 font-medium">${pd.fullName || userName}</div>
            </div>
          </div>
          <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">${ico('bell', 'w-4 h-4')}</div>
            <div class="min-w-0">
              <div class="text-[11px] text-slate-400 mb-0.5">Phone / Contact</div>
              <div class="text-sm text-slate-900 font-medium">${pd.contact || '—'}</div>
            </div>
          </div>
          <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">${ico('mail', 'w-4 h-4')}</div>
            <div class="min-w-0">
              <div class="text-[11px] text-slate-400 mb-0.5">Gmail</div>
              <div class="text-sm text-slate-900 font-medium break-all">${pd.gmail || '—'}</div>
            </div>
          </div>
          <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">${ico('lock', 'w-4 h-4')}</div>
            <div class="flex-1 min-w-0">
              <div class="text-[11px] text-slate-400 mb-0.5">Password</div>
              <div class="flex items-center gap-2">
                <div class="text-sm text-slate-900 font-medium font-mono flex-1" id="profilePwDisplay">${showPw ? (pd.password || '—') : '••••••••••'}</div>
                <button id="profilePwToggle" class="text-slate-400 hover:text-slate-700 p-1 transition" title="${showPw ? 'Hide' : 'Show'} password">${ico(showPw ? 'close' : 'search', 'w-3.5 h-3.5')}</button>
              </div>
            </div>
          </div>
          ${isMasteral ? `
          <div class="flex items-start gap-3 p-3 rounded-xl bg-violet-50 border border-violet-100">
            <div class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center shrink-0">${ico('grad', 'w-4 h-4')}</div>
            <div class="min-w-0">
              <div class="text-[11px] text-violet-400 mb-0.5">Degree</div>
              <div class="text-sm text-violet-900 font-semibold">Masteral</div>
              <div class="text-xs text-violet-600 mt-0.5">${pd.degreeTitle}</div>
            </div>
          </div>` : ''}
        </div>
        <div class="pt-2">
          <button id="editProfileBtn" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm transition font-medium">${ico('pencil', 'w-4 h-4')} Edit Information</button>
        </div>
      </div>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">
      <button id="profileLogout" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm transition">${ico('logout', 'w-4 h-4')} Sign Out
      </button>
    </div>
  </div>`;
    })() : ''}
  ${S.notifPanel ? `<div id="notifOverlay" class="fixed inset-0 z-40 bg-slate-900/30 backdrop-blur-sm"></div>
  <div id="notifDrawer" class="fixed top-0 right-0 h-full w-80 max-w-full z-50 bg-white shadow-2xl border-l border-slate-200 flex flex-col" style="animation:slideInRight .22s cubic-bezier(.4,0,.2,1)">
    <style>@keyframes slideInRight{from{transform:translateX(100%)}to{transform:translateX(0)}}</style>
    <div class="px-5 py-5 border-b border-slate-100 flex items-center justify-between">
      <div>
        <div class="text-slate-900 tracking-tight">Notifications</div>
        <div class="text-xs text-slate-500">5 unread</div>
      </div>
      <button id="notifClose" class="text-slate-400 hover:text-slate-700 p-1">${ico('close', 'w-4 h-4')}</button>
    </div>
    <div class="flex-1 overflow-y-auto">
      <div class="px-5 pt-4 pb-1 text-[10px] uppercase tracking-wider text-slate-400">New</div>
      <ul class="divide-y divide-slate-50">
        <li class="px-5 py-3.5 flex gap-3 bg-indigo-50/50 hover:bg-indigo-50">
          <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">${ico('filecheck', 'w-4 h-4')}</div>
          <div class="flex-1 min-w-0"><div class="text-sm text-slate-900">4 reports awaiting approval</div><div class="text-xs text-slate-500 mt-0.5">Report & Activity Approvals Â· Just now</div></div>
          <span class="w-2 h-2 rounded-full bg-rose-500 mt-1.5 shrink-0"></span>
        </li>
        <li class="px-5 py-3.5 flex gap-3 bg-indigo-50/50 hover:bg-indigo-50">
          <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">${ico('check2', 'w-4 h-4')}</div>
          <div class="flex-1 min-w-0"><div class="text-sm text-slate-900">BSCS-2A OCR file processed</div><div class="text-xs text-slate-500 mt-0.5">OCR Grade Upload Â· 10:14 AM</div></div>
          <span class="w-2 h-2 rounded-full bg-rose-500 mt-1.5 shrink-0"></span>
        </li>
        <li class="px-5 py-3.5 flex gap-3 bg-indigo-50/50 hover:bg-indigo-50">
          <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">${ico('calendar', 'w-4 h-4')}</div>
          <div class="flex-1 min-w-0"><div class="text-sm text-slate-900">Midterm Reports due May 16</div><div class="text-xs text-slate-500 mt-0.5">Activity Calendar Â· Today</div></div>
          <span class="w-2 h-2 rounded-full bg-rose-500 mt-1.5 shrink-0"></span>
        </li>
      </ul>
      <div class="px-5 pt-4 pb-1 text-[10px] uppercase tracking-wider text-slate-400">Earlier</div>
      <ul class="divide-y divide-slate-50">
        <li class="px-5 py-3.5 flex gap-3 hover:bg-slate-50">
          <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">${ico('award', 'w-4 h-4')}</div>
          <div class="flex-1 min-w-0"><div class="text-sm text-slate-900">Certificates generated â€” CWTS 1</div><div class="text-xs text-slate-500 mt-0.5">Certificates Â· Today 9:30 AM</div></div>
        </li>
        <li class="px-5 py-3.5 flex gap-3 hover:bg-slate-50">
          <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">${ico('users', 'w-4 h-4')}</div>
          <div class="flex-1 min-w-0"><div class="text-sm text-slate-900">BSIT-3A section updated</div><div class="text-xs text-slate-500 mt-0.5">Sections Â· May 9</div></div>
        </li>
      </ul>
    </div>
    <div class="px-5 py-4 border-t border-slate-100">
      <button id="notifMarkAll" class="w-full text-sm text-indigo-600 hover:text-indigo-700 font-medium">Mark all as read</button>
    </div>
  </div>` : ''}`;
}

/* ================================================================
   LOGIN
================================================================ */
/* ----------------------------------------------------------------
   CREDENTIAL MAP  (email â†’ { role, label })
---------------------------------------------------------------- */
const CREDENTIALS = [
  { email: 'coor123', password: '123', role: 'coordinator', label: 'NSTP Coordinator', ico: 'grad', grad: 'from-indigo-600 to-blue-500' },
  { email: 'ins123', password: '123', role: 'instructor', label: 'CWTS/LTS Instructor', ico: 'book', grad: 'from-emerald-500 to-teal-500' },
  { email: 'ro123', password: '123', role: 'rotc', label: 'ROTC 1st Class Officer', ico: 'shield', grad: 'from-slate-800 to-slate-900' },
  { email: 'admin123@dnsc.edu.ph', password: 'admin123', role: 'admin', label: 'System Administrator', ico: 'users', grad: 'from-purple-600 to-indigo-600' },
];

function renderLogin() {
  const errHtml = S.loginError
    ? `<div id="loginError" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm">
            ${ico('alertc', 'w-4 h-4 shrink-0')} ${S.loginError}
           </div>`
    : '';

  return `<div class="min-h-screen w-full bg-slate-50 flex items-center justify-center p-6 relative">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-indigo-200/40 blur-3xl"></div>
      <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-emerald-200/40 blur-3xl"></div>
    </div>
    <div class="relative w-full max-w-5xl bg-white rounded-3xl shadow-xl border border-slate-100 grid grid-cols-1 md:grid-cols-2 overflow-hidden">

      <!-- Left brand panel -->
      <div class="p-8 md:p-10 bg-gradient-to-br from-slate-900 to-slate-800 text-white relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-white/5"></div>
        <div class="absolute bottom-0 -left-10 w-72 h-72 rounded-full bg-white/5"></div>
        <div class="relative">
          <img src="DSNC.png" class="w-16 h-16 object-contain mb-6" alt="DNSC Logo" />
          <div class="text-[11px] tracking-[0.3em] uppercase text-slate-400 mb-2">Davao Del Norte State College</div>
          <div class="text-white tracking-tight text-3xl leading-tight">NSTP Management System</div>
          <!-- Role hint cards -->
          <div class="mt-8 space-y-2">
             <div class="text-[10px] uppercase tracking-widest text-slate-500 mb-2">Accounts</div>
             ${CREDENTIALS.filter(c => c.email !== 'admin123@dnsc.edu.ph').map(c => `
             <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-white/5 border border-white/10">
               <div class="w-7 h-7 rounded-md bg-gradient-to-br ${c.grad} flex items-center justify-center text-white shrink-0">${ico(c.ico, 'w-3.5 h-3.5')}</div>
               <span class="text-xs text-slate-300">${c.label}</span>
             </div>`).join('')}
          </div>
        </div>
      </div>

      <!-- Right login form -->
      <div class="p-8 md:p-10 flex flex-col justify-center">
        <div class="text-xs uppercase tracking-[0.18em] text-slate-400">Sign In</div>
        <div class="text-slate-900 tracking-tight text-2xl mt-1">Welcome back</div>
        <p class="text-sm text-slate-500 mt-1">Enter your credentials to continue.</p>

        <div class="mt-6 space-y-4">
          ${errHtml}
          <label class="block">
            <span class="text-xs text-slate-500">Email</span>
            <div class="mt-1 relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">${ico('mail', 'w-4 h-4')}</span>
              <input id="loginEmail" type="email" placeholder="e.g. coordinator@dnsc.edu.ph"
                class="w-full pl-9 pr-3 py-2.5 text-sm rounded-lg border border-slate-200 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-50 focus:outline-none transition" />
            </div>
          </label>
          <label class="block">
            <span class="text-xs text-slate-500">Password</span>
            <div class="mt-1 relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">${ico('lock', 'w-4 h-4')}</span>
              <input id="loginPassword" type="password" placeholder="Enter your password"
                class="w-full pl-9 pr-3 py-2.5 text-sm rounded-lg border border-slate-200 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-50 focus:outline-none transition" />
            </div>
          </label>
        </div>

        <button id="loginBtn" class="mt-5 w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-slate-900 hover:bg-slate-800 active:scale-95 text-white text-sm shadow-md transition-all">
          ${ico('arrow', 'w-4 h-4')} Sign In
        </button>
        <div class="mt-4 text-xs text-slate-500 text-center">Trouble signing in? Contact the NSTP office.</div>
      </div>
    </div>
  </div>`;
}

/* ================================================================
   COORDINATOR PAGES
================================================================ */
const COORD_STATS = [
  { label: 'Total Students', value: '2,847', delta: '+4.2%', up: true, ico: 'users', color: 'from-indigo-500 to-blue-500' },
  { label: 'Active Sections', value: '126', delta: '+12', up: true, ico: 'book', color: 'from-emerald-500 to-teal-500' },
  { label: 'Pass Rate', value: '91.4%', delta: '+1.8%', up: true, ico: 'trend', color: 'from-violet-500 to-fuchsia-500' },
  { label: 'Reports Pending', value: '23', delta: 'âˆ’6', up: false, ico: 'filecheck', color: 'from-amber-500 to-orange-500' },
];

/* ----------------------------------------------------------------
   DASHBOARD WIDGETS: Calendar mini + Recent Activities
---------------------------------------------------------------- */
function calendarMiniWidget() {
  const now = new Date();
  const year = now.getFullYear();
  const month = now.getMonth();
  const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  const dayNames = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const today = now.getDate();

  let cells = '';
  for (let i = 0; i < firstDay; i++) cells += '<div></div>';
  for (let d = 1; d <= daysInMonth; d++) {
    const isToday = d === today;
    const actIdx = S.activities.findIndex(a => {
      try {
        const dateObj = new Date(a.date);
        return dateObj.getFullYear() === year && dateObj.getMonth() === month && dateObj.getDate() === d;
      } catch (e) { return false; }
    });
    const hasEvent = actIdx !== -1;

    let cls = '';
    let clickAttr = '';
    if (isToday) {
      cls = 'w-7 h-7 flex items-center justify-center text-xs rounded-full bg-indigo-600 text-white font-semibold cursor-pointer';
      if (hasEvent) {
        clickAttr = `onclick="S.selectedCalActivity = ${actIdx}; render();"`;
      }
    } else if (hasEvent) {
      cls = 'w-7 h-7 flex items-center justify-center text-xs rounded-full bg-indigo-50 text-indigo-700 font-semibold ring-1 ring-indigo-200 cursor-pointer hover:bg-indigo-100 transition';
      clickAttr = `onclick="S.selectedCalActivity = ${actIdx}; render();"`;
    } else {
      cls = 'w-7 h-7 flex items-center justify-center text-xs rounded-full text-slate-600 hover:bg-slate-100 cursor-pointer';
    }
    cells += `<div ${clickAttr} class="${cls}">${d}</div>`;
  }

  return `<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
    <div class="flex items-center justify-between mb-3">
      <div>
        <div class="text-slate-900 font-bold tracking-tight">Calendar of Activities</div>
        <div class="text-xs text-slate-500 mt-0.5">${monthNames[month]} ${year}</div>
      </div>
      <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center">${ico('calendar', 'w-4 h-4')}</div>
    </div>
    <div class="grid grid-cols-7 gap-1 mb-1">${dayNames.map(d => `<div class="text-center text-[10px] font-medium text-slate-400 uppercase">${d}</div>`).join('')}</div>
    <div class="grid grid-cols-7 gap-1">${cells}</div>
    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-4 text-xs text-slate-500">
      <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-600 inline-block"></span>Today</span>
      <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-50 ring-1 ring-indigo-200 inline-block font-semibold"></span>Has activity</span>
    </div>
  </div>`;
}

function recentActivitiesWidget() {
  const recent = S.activities.slice(0, 4);
  const rows = recent.map(a => {
    const statusCls = a.status === 'Submitted' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700';
    return `<li class="flex items-start gap-3 py-2.5 border-b border-slate-50 last:border-0">
          <span class="w-2 h-2 rounded-full mt-1.5 shrink-0 ${a.color}"></span>
          <div class="flex-1 min-w-0">
            <div class="text-sm text-slate-800 font-medium truncate">${a.title}</div>
            <div class="text-xs text-slate-500 mt-0.5">${a.date} &middot; ${a.time}</div>
          </div>
          <span class="text-[10px] px-2 py-0.5 rounded-full shrink-0 ${statusCls}">${a.status}</span>
        </li>`;
  }).join('');
  return `<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
          <div>
            <div class="text-slate-900 tracking-tight">Recent Activities</div>
            <div class="text-xs text-slate-500">Latest scheduled events</div>
          </div>
          <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center">${ico('calrange', 'w-4 h-4')}</div>
        </div>
        <ul>${rows}</ul>
      </div>`;
}

function cActivityModalHtml() {
  if (S.selectedCalActivity === null || S.selectedCalActivity === undefined) return '';
  const idx = S.selectedCalActivity;
  const act = S.activities[idx];
  if (!act) return '';

  if (S.editingActivity) {
    return `
    <div id="actDetailOverlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col animate-scaleUp">
        <style>
          @keyframes scaleUp {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
          }
          .animate-scaleUp {
            animation: scaleUp 0.15s cubic-bezier(0.16, 1, 0.3, 1) forwards;
          }
        </style>
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 shrink-0">
          <h3 class="font-bold text-slate-800 text-lg">Edit Activity</h3>
          <button onclick="S.editingActivity = false; render();" class="p-2 -mr-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-full transition-colors">${ico('close', 'w-5 h-5')}</button>
        </div>
        <div class="p-6 space-y-4 overflow-y-auto text-sm">
          <div>
            <label class="text-xs font-bold text-slate-500 uppercase">Activity Title</label>
            <input id="editActTitle" value="${act.title}" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300 font-medium" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase">Date</label>
              <input id="editActDate" value="${act.date}" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase">Time</label>
              <input id="editActTime" value="${act.time}" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
            </div>
          </div>
          <div>
            <label class="text-xs font-bold text-slate-500 uppercase">Venue</label>
            <input id="editActVenue" value="${act.venue}" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase">Audience / Scope</label>
              <select id="editActScope" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300 bg-white">
                <option value="All Programs" ${act.scope === 'All Programs' ? 'selected' : ''}>All Programs</option>
                <option value="All Instructors" ${act.scope === 'All Instructors' ? 'selected' : ''}>All Instructors</option>
                <option value="Faculty" ${act.scope === 'Faculty' ? 'selected' : ''}>Faculty</option>
                <option value="Graduating Students" ${act.scope === 'Graduating Students' ? 'selected' : ''}>Graduating Students</option>
                <option value="CWTS" ${act.scope === 'CWTS' ? 'selected' : ''}>CWTS</option>
                <option value="LTS" ${act.scope === 'LTS' ? 'selected' : ''}>LTS</option>
                <option value="ROTC" ${act.scope === 'ROTC' ? 'selected' : ''}>ROTC</option>
              </select>
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase">Status</label>
              <select id="editActStatus" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300 bg-white">
                <option value="Submitted" ${act.status === 'Submitted' ? 'selected' : ''}>Submitted</option>
                <option value="Pending" ${act.status === 'Pending' ? 'selected' : ''}>Pending</option>
                <option value="Draft" ${act.status === 'Draft' ? 'selected' : ''}>Draft</option>
              </select>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
          <button onclick="S.editingActivity = false; render();" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-100 transition">Cancel</button>
          <button onclick="saveActivityEdits(${idx});" class="px-5 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition shadow-sm">Save Changes</button>
        </div>
      </div>
    </div>
    `;
  }

  return `
  <div id="actDetailOverlay" onclick="if(event.target === this) { S.selectedCalActivity = null; render(); }" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col animate-scaleUp">
      <style>
        @keyframes scaleUp {
          from { opacity: 0; transform: scale(0.95); }
          to { opacity: 1; transform: scale(1); }
        }
        .animate-scaleUp {
          animation: scaleUp 0.15s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
      </style>
      <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 shrink-0">
        <h3 class="font-bold text-slate-800 text-lg">Activity Details</h3>
        <button onclick="S.selectedCalActivity = null; render();" class="p-2 -mr-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-full transition-colors">${ico('close', 'w-5 h-5')}</button>
      </div>
      <div class="p-6 space-y-6 overflow-y-auto">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-100 shrink-0">
            ${ico('calendar', 'w-6 h-6')}
          </div>
          <div>
            <h4 class="text-lg font-bold text-slate-900 leading-snug">${act.title}</h4>
            <div class="mt-2 flex gap-2">${pill('indigo', act.scope)}${pill(act.status === 'Submitted' ? 'emerald' : 'slate', act.status)}</div>
          </div>
        </div>
        
        <div class="border-t border-slate-100 pt-5 space-y-4 text-sm">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('calendar', 'w-4 h-4')}</div>
            <div>
              <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Date</div>
              <div class="font-semibold text-slate-800 mt-0.5">${act.date}</div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('clock', 'w-4 h-4')}</div>
            <div>
              <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Time</div>
              <div class="font-semibold text-slate-800 mt-0.5">${act.time}</div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('mappin', 'w-4 h-4')}</div>
            <div>
              <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Venue</div>
              <div class="font-semibold text-slate-800 mt-0.5">${act.venue}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between shrink-0">
        <button onclick="deleteActivity(${idx});" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 transition">${ico('close', 'w-4 h-4')} Delete</button>
        <div class="flex items-center gap-2">
          <button onclick="S.editingActivity = true; render();" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 transition">Edit</button>
          <button onclick="S.selectedCalActivity = null; render();" class="px-5 py-2 text-sm font-semibold rounded-lg bg-slate-900 text-white hover:bg-slate-800 transition shadow-sm">Close</button>
        </div>
      </div>
    </div>
  </div>
  `;
}

function cDashboard() {
  const statsHtml = COORD_STATS.map(s => `<div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
    <div class="flex items-start justify-between">
      <div class="w-10 h-10 rounded-xl bg-gradient-to-br ${s.color} flex items-center justify-center text-white shadow">${ico(s.ico, 'w-5 h-5')}</div>
      <span class="text-xs px-2 py-1 rounded-full ${s.up ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'}">${s.delta}</span>
    </div>
    <div class="mt-4 text-slate-900 tracking-tight text-2xl">${s.value}</div>
    <div class="text-sm text-slate-500 mt-0.5">${s.label}</div>
  </div>`).join('');

  const modalHtml = cActivityModalHtml();

  return `
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">${statsHtml}</div>
  <div class="grid grid-cols-1 xl:grid-cols-5 gap-6 mt-6">
    <div class="xl:col-span-3 flex flex-col gap-5">
      ${enrollmentChart()}
      ${passFailChart()}
    </div>
    <div class="xl:col-span-2 flex flex-col gap-5">
      ${calendarMiniWidget()}
      ${recentActivitiesWidget()}
    </div>
  </div>
  ${modalHtml}`;
}

const SECTIONS = [
  { code: 'CWTS-1A', program: 'BSIT', schoolYear: '2025-2026', students: 42, instructor: 'Prof. L. Tan', room: 'B-204', status: 'Active' },
  { code: 'CWTS-1B', program: 'BSIS', schoolYear: '2025-2026', students: 38, instructor: 'Prof. R. Cruz', room: 'B-206', status: 'Active' },
  { code: 'LTS-1B', program: 'BSDRM', schoolYear: '2025-2026', students: 45, instructor: 'Prof. A. Yusuf', room: 'C-101', status: 'Active' },
  { code: 'LTS-1C', program: 'BSDRM', schoolYear: '2025-2026', students: 41, instructor: 'Prof. M. Lim', room: 'C-103', status: 'Active' },
  { code: 'ROTC-1A', program: 'BSED', schoolYear: '2025-2026', students: 48, instructor: 'Prof. K. Domingo', room: 'A-205', status: 'Active' },
  { code: 'ROTC-1B', program: 'BSED', schoolYear: '2025-2026', students: 36, instructor: 'Prof. P. Garcia', room: 'D-110', status: 'Closed' },
];

const SECTION_STUDENTS = {
  'CWTS-1A': [
    { name: 'Aguila, Marco R.', studentNo: '2024-00001', program: 'BSIT' },
    { name: 'Bautista, Lea V.', studentNo: '2024-00002', program: 'BSIT' },
    { name: 'Cruz, Jana P.', studentNo: '2024-00003', program: 'BSIT' },
    { name: 'Dela Torre, Rey M.', studentNo: '2024-00004', program: 'BSIT' },
    { name: 'Espino, Kira A.', studentNo: '2024-00005', program: 'BSIT' },
    { name: 'Fernandez, Luis C.', studentNo: '2024-00006', program: 'BSIT' },
    { name: 'Garcia, Tina S.', studentNo: '2024-00007', program: 'BSIT' },
    { name: 'Hernandez, Paolo D.', studentNo: '2024-00008', program: 'BSIT' },
    { name: 'Jimenez, Ana G.', studentNo: '2024-00009', program: 'BSIT' },
    { name: 'Lim, Carl N.', studentNo: '2024-00010', program: 'BSIT' },
    { name: 'Magno, Rosa T.', studentNo: '2024-00011', program: 'BSIT' },
    { name: 'Navarro, Sam K.', studentNo: '2024-00012', program: 'BSIT' },
    { name: 'Ocampo, Beth J.', studentNo: '2024-00013', program: 'BSIT' },
    { name: 'Pascual, Dave L.', studentNo: '2024-00014', program: 'BSIT' },
    { name: 'Reyes, Mia O.', studentNo: '2024-00015', program: 'BSIT' },
  ],
  'CWTS-1B': [
    { name: 'Santos, Jay F.', studentNo: '2024-00016', program: 'BSIS' },
    { name: 'Torres, Elle M.', studentNo: '2024-00017', program: 'BSIS' },
    { name: 'Uy, Ryan B.', studentNo: '2024-00018', program: 'BSIS' },
    { name: 'Valdez, Nica Q.', studentNo: '2024-00019', program: 'BSIS' },
    { name: 'Villanueva, Eric H.', studentNo: '2024-00020', program: 'BSIS' },
    { name: 'Yap, Carla I.', studentNo: '2024-00021', program: 'BSIS' },
    { name: 'Zulueta, Mike A.', studentNo: '2024-00022', program: 'BSIS' },
    { name: 'Alcantara, Donna P.', studentNo: '2024-00023', program: 'BSIS' },
    { name: 'Buenaventura, Jed S.', studentNo: '2024-00024', program: 'BSIS' },
    { name: 'Caballero, Faye G.', studentNo: '2024-00025', program: 'BSIS' },
  ],
  'LTS-1B': [
    { name: 'Castillo, Luis A.', studentNo: '2024-00026', program: 'BSDRM' },
    { name: 'Dizon, Ana R.', studentNo: '2024-00027', program: 'BSDRM' },
    { name: 'Enriquez, Mark T.', studentNo: '2024-00028', program: 'BSDRM' },
    { name: 'Flores, Kim S.', studentNo: '2024-00029', program: 'BSDRM' },
    { name: 'Gomez, Rey B.', studentNo: '2024-00030', program: 'BSDRM' },
    { name: 'Ibarra, Pia L.', studentNo: '2024-00031', program: 'BSDRM' },
    { name: 'Javier, Noel C.', studentNo: '2024-00032', program: 'BSDRM' },
    { name: 'Lacson, Vina D.', studentNo: '2024-00033', program: 'BSDRM' },
    { name: 'Macaraeg, Jed E.', studentNo: '2024-00034', program: 'BSDRM' },
    { name: 'Manalo, Ria F.', studentNo: '2024-00035', program: 'BSDRM' },
    { name: 'Mendoza, Carlo G.', studentNo: '2024-00036', program: 'BSDRM' },
    { name: 'Miranda, Liza H.', studentNo: '2024-00037', program: 'BSDRM' },
    { name: 'Molina, Tony I.', studentNo: '2024-00038', program: 'BSDRM' },
    { name: 'Morales, Gina J.', studentNo: '2024-00039', program: 'BSDRM' },
    { name: 'Navarro, Ed K.', studentNo: '2024-00040', program: 'BSDRM' },
  ],
  'LTS-1C': [
    { name: 'Ocampo, Lea L.', studentNo: '2024-00041', program: 'BSDRM' },
    { name: 'Padilla, Rene M.', studentNo: '2024-00042', program: 'BSDRM' },
    { name: 'Perez, Dot N.', studentNo: '2024-00043', program: 'BSDRM' },
    { name: 'Pineda, Vic O.', studentNo: '2024-00044', program: 'BSDRM' },
    { name: 'Ramos, Joy P.', studentNo: '2024-00045', program: 'BSDRM' },
    { name: 'Rivera, Ben Q.', studentNo: '2024-00046', program: 'BSDRM' },
    { name: 'Rodriguez, Cris R.', studentNo: '2024-00047', program: 'BSDRM' },
    { name: 'Romero, Fely S.', studentNo: '2024-00048', program: 'BSDRM' },
    { name: 'Ruiz, Greg T.', studentNo: '2024-00049', program: 'BSDRM' },
    { name: 'Salvador, Ivy U.', studentNo: '2024-00050', program: 'BSDRM' },
  ],
  'ROTC-1A': [
    { name: 'Sanchez, Jan V.', studentNo: '2024-00051', program: 'BSED' },
    { name: 'Santiago, Ken W.', studentNo: '2024-00052', program: 'BSED' },
    { name: 'Santos, Luz X.', studentNo: '2024-00053', program: 'BSED' },
    { name: 'Serrano, Mae Y.', studentNo: '2024-00054', program: 'BSED' },
    { name: 'Sierra, Noe Z.', studentNo: '2024-00055', program: 'BSED' },
    { name: 'Silva, Ora A.', studentNo: '2024-00056', program: 'BSED' },
    { name: 'Simon, Pat B.', studentNo: '2024-00057', program: 'BSED' },
    { name: 'Soriano, Quinn C.', studentNo: '2024-00058', program: 'BSED' },
    { name: 'Soto, Rae D.', studentNo: '2024-00059', program: 'BSED' },
    { name: 'Suarez, Sam E.', studentNo: '2024-00060', program: 'BSED' },
    { name: 'Tan, Tina F.', studentNo: '2024-00061', program: 'BSED' },
    { name: 'Torres, Uma G.', studentNo: '2024-00062', program: 'BSED' },
    { name: 'Trinidad, Vic H.', studentNo: '2024-00063', program: 'BSED' },
    { name: 'Tuazon, Wanda I.', studentNo: '2024-00064', program: 'BSED' },
    { name: 'Uy, Xavier J.', studentNo: '2024-00065', program: 'BSED' },
  ],
  'ROTC-1B': [
    { name: 'Valdes, Ysa K.', studentNo: '2024-00066', program: 'BSED' },
    { name: 'Valencia, Zack L.', studentNo: '2024-00067', program: 'BSED' },
    { name: 'Vargas, Amy M.', studentNo: '2024-00068', program: 'BSED' },
    { name: 'Vega, Ben N.', studentNo: '2024-00069', program: 'BSED' },
    { name: 'Vera, Cara O.', studentNo: '2024-00070', program: 'BSED' },
    { name: 'Vergara, Dan P.', studentNo: '2024-00071', program: 'BSED' },
    { name: 'Vidal, Eva Q.', studentNo: '2024-00072', program: 'BSED' },
    { name: 'Villa, Fred R.', studentNo: '2024-00073', program: 'BSED' },
    { name: 'Villanueva, Gina S.', studentNo: '2024-00074', program: 'BSED' },
    { name: 'Villar, Hank T.', studentNo: '2024-00075', program: 'BSED' },
  ],
};

function cSections() {
  const sel = S.selectedSection ? SECTIONS.find(s => s.code === S.selectedSection) : null;

  // Filter sections by tab and search text
  const visibleSections = SECTIONS.filter(s => {
    if (S.secTab !== 'all' && !s.code.startsWith(S.secTab)) return false;
    if (S.secSearch) {
      const q = S.secSearch.toLowerCase();
      return s.code.toLowerCase().includes(q) || s.instructor.toLowerCase().includes(q) || s.program.toLowerCase().includes(q);
    }
    return true;
  });

  // Clickable table rows
  const thead = `<thead><tr class="text-left text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-100">
        <th class="py-2 px-3 font-medium">Section</th>
        <th class="py-2 px-3 font-medium">Program</th>
        <th class="py-2 px-3 font-medium">School Year</th>
        <th class="py-2 px-3 font-medium">Students</th>
        <th class="py-2 px-3 font-medium">Instructor</th>
        <th class="py-2 px-3 font-medium">Room</th>
      </tr></thead>`;
  const tbody = `<tbody>${visibleSections.length ? visibleSections.map(r => `<tr data-sec-row="${r.code}" class="border-b border-slate-50 hover:bg-indigo-50/40 cursor-pointer transition ${S.selectedSection === r.code ? 'bg-indigo-50' : ''}"><td class="py-3 px-3 text-slate-900">${r.code}</td><td class="py-3 px-3"><span class="text-xs px-2 py-0.5 rounded-full ${r.program === 'BSIT' || r.program === 'BSIS' ? 'bg-indigo-50 text-indigo-700' : r.program === 'BSDRM' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'}">${r.program}</span></td><td class="py-3 px-3"><span class="text-xs px-2 py-0.5 rounded-full ${r.schoolYear === '2025-2026' ? 'bg-violet-50 text-violet-700' : 'bg-rose-50 text-rose-700'}">${r.schoolYear}</span></td><td class="py-3 px-3 text-slate-700">${r.students}</td><td class="py-3 px-3 text-slate-700">${r.instructor}</td><td class="py-3 px-3 text-slate-700">${r.room}</td></tr>`).join('') : `<tr><td colspan="6" class="py-8 text-center text-slate-400 text-sm">No sections found for this program.</td></tr>`}</tbody>`;
  const clickableTbl = `<div class="overflow-x-auto"><table class="w-full text-sm">${thead}${tbody}</table></div>`;

  // Detail side panel
  const detailPanel = sel ? `
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm flex flex-col">
          <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
              <div class="text-slate-900 tracking-tight">${sel.code}</div>
              <div class="text-xs text-slate-500">${sel.program} Â· ${sel.instructor} Â· ${sel.room}</div>
            </div>
            <button id="secDetailClose" class="text-slate-400 hover:text-slate-700 p-1">${ico('close', 'w-4 h-4')}</button>
          </div>
          <div class="px-5 py-3 flex-1">
            <div class="text-xs uppercase tracking-wider text-slate-500 mb-3">Students â€” ${(SECTION_STUDENTS[sel.code] || []).length} enrolled</div>
            <ul class="space-y-0 max-h-72 overflow-y-auto divide-y divide-slate-50">
              ${(SECTION_STUDENTS[sel.code] || []).map((st, i) => `<li class="flex items-start gap-3 py-2.5">
                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px] font-medium shrink-0 mt-0.5">${i + 1}</span>
                <div class="flex-1 min-w-0">
                  <div class="text-sm text-slate-900 font-medium">${st.name}</div>
                  <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[11px] text-slate-500">${st.studentNo}</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full ${st.program === 'CWTS' ? 'bg-indigo-50 text-indigo-600' : st.program === 'LTS' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'}">${st.program}</span>
                  </div>
                </div>
              </li>`).join('')}
            </ul>
          </div>
          <div class="px-5 py-4 border-t border-slate-100 space-y-3">
            <div class="text-xs uppercase tracking-wider text-slate-500">Add Student</div>
            <div class="grid grid-cols-2 gap-2">
              <div class="col-span-2">
                <div class="text-[11px] text-slate-400 mb-1">Full Name</div>
                <input id="secStudentName" placeholder="e.g. Dela Cruz, Juan A." class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
              </div>
              <div>
                <div class="text-[11px] text-slate-400 mb-1">Student No.</div>
                <input id="secStudentNo" placeholder="e.g. 2024-00100" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
              </div>
              <div>
                <div class="text-[11px] text-slate-400 mb-1">Program</div>
                <input id="secStudentNo" placeholder="e.g. BSIT, BSIS" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
              </div>
            </div>
            <button id="secAddStudentBtn" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-sm rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">${ico('plus', 'w-4 h-4')} Add Student</button>
            <button id="secDeleteBtn" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200">${ico('close', 'w-4 h-4')} Delete Section</button>
          </div>
        </div>` : '';

  const modal = S.sectionForm ? `
  <div id="newSectionOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-lg mx-4">
      <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
        <div>
          <div class="text-slate-900 tracking-tight">New Section</div>
          <div class="text-xs text-slate-500">Fill in the details to add a new section</div>
        </div>
        <button id="sectionFormClose" class="text-slate-400 hover:text-slate-700 p-1">${ico('close', 'w-4 h-4')}</button>
      </div>
      <div class="p-6 space-y-4 text-sm">

        <div class="grid grid-cols-2 gap-4">
          <div>
            <div class="text-xs text-slate-500 mb-1">Section Code</div>
            <input id="secCode" placeholder="e.g. CWTS-1A" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
          </div>
          <div>
            <div class="text-xs text-slate-500 mb-1">Program</div>
            <input id="secProgram" placeholder="e.g. BSIT or BSIS" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <div class="text-xs text-slate-500 mb-1">School Year</div>
            <input id="secSchoolYear" placeholder="e.g. 2025-2026" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
          </div>
          <div>
            <!-- XLSX Import zone -->
            <div class="text-xs text-slate-500 mb-1">Import Class List XLSX File</div>
            <input type="file" id="modalXlsxInput" accept=".xlsx,.xls" class="hidden" />
            <button id="modalXlsxBtn" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border-2 border-dashed border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:border-emerald-400 transition text-sm">
              ${ico('upload', 'w-4 h-4')} Import XLSX File
            </button>
          </div>
        </div>
        <div>
          <div class="text-xs text-slate-500 mb-1">Instructor</div>
          <input id="secInstructor" placeholder="e.g. Prof. J. Santos" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <div class="text-xs text-slate-500 mb-1">Room</div>
            <input id="secRoom" placeholder="e.g. B-210" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
          </div>
        </div>
      </div>
      <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-2">
        <button id="sectionFormCancel" class="px-4 py-2 text-sm rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">Cancel</button>
        <button id="sectionFormCreate" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">${ico('users', 'w-4 h-4')} Create Section</button>
      </div>
    </div>
  </div>` : '';

  // Program summary cards data
  const progDefs = [
    { key: 'CWTS', letter: 'C', label: 'CWTS', full: 'Civic Welfare Training Service', color: 'bg-indigo-600', bar: 'bg-indigo-500', maxStudents: 800 },
    { key: 'LTS', letter: 'L', label: 'LTS', full: 'Literacy Training Service', color: 'bg-emerald-600', bar: 'bg-emerald-500', maxStudents: 400 },
    { key: 'ROTC', letter: 'R', label: 'ROTC', full: "Reserve Officers' Training Corps", color: 'bg-rose-500', bar: 'bg-rose-400', maxStudents: 400 },
  ];
  const progCards = progDefs.map(p => {
    const filteredSecs = SECTIONS.filter(s => s.code.startsWith(p.key));
    const totalStudents = filteredSecs.reduce((sum, s) => sum + (s.students || 0), 0);
    const pct = Math.min(100, Math.round((totalStudents / p.maxStudents) * 100));
    const isActive = S.secTab === p.key;
    const border = isActive ? 'border-2 border-indigo-400 shadow-md' : 'border border-slate-100 hover:border-slate-300 hover:shadow-md';
    return `<button data-sec-tab="${p.key}" class="bg-white rounded-2xl p-5 shadow-sm text-left transition-all ${border} cursor-pointer w-full">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full ${p.color} text-white flex items-center justify-center text-base font-bold shrink-0">${p.letter}</div>
            <div>
              <div class="text-slate-900 font-semibold tracking-tight">${p.label}</div>
              <div class="text-xs text-slate-500">${p.full}</div>
            </div>
          </div>
          <div class="space-y-3">
            <div>
              <div class="flex items-center justify-between text-sm mb-1">
                <span class="text-slate-500">Students</span>
                <span class="text-slate-700 font-medium">${totalStudents} / ${p.maxStudents}</span>
              </div>
              <div class="w-full h-1.5 rounded-full bg-slate-100"><div class="h-1.5 rounded-full ${p.bar} transition-all" style="width:${pct}%"></div></div>
            </div>
            <div class="flex items-center justify-between text-sm border-t border-slate-50 pt-3">
              <span class="text-slate-500">Sections</span>
              <span class="text-slate-700 font-medium">${filteredSecs.length}</span>
            </div>
          </div>
        </button>`;
  }).join('');

  return `<div class="space-y-5">
    ${modal}
    <input type="file" id="xlsxImportInput" accept=".xlsx,.xls" class="hidden" />
    ${pageHdr('Sections & Students', 'Click a row to view the student list', `
      <button id="importXlsxBtn" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100">${ico('upload', 'w-4 h-4')} Import Master List XLSX File</button>
      <button id="newSectionBtn" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">${ico('users', 'w-4 h-4')} New Section</button>`)}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">${progCards}</div>
    <div class="${sel ? 'grid grid-cols-1 xl:grid-cols-3 gap-5 items-start' : ''}">
      <div class="${sel ? 'xl:col-span-2' : ''}">
        ${card(`<div class="flex items-center gap-2 mb-4">
          <div class="relative flex-1 max-w-md"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">${ico('search', 'w-4 h-4')}</span><input id="sectionsSearchInput" placeholder="Search section or instructor…" class="w-full pl-9 pr-3 py-2 text-sm rounded-lg bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-indigo-200" value="${S.secSearch || ''}" /></div>
          <button data-sec-tab="all" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg border transition ${S.secTab === 'all' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-400 hover:text-slate-900'}">${ico('grid', 'w-4 h-4')} All Sections</button>
        </div>${clickableTbl}`)}
      </div>
      ${sel ? detailPanel : ''}
    </div>
  </div>`;
}

function cInstructorModalHtml() {
  if (S.selectedInstructorIndex === null || S.selectedInstructorIndex === undefined) return '';
  const idx = S.selectedInstructorIndex;
  const i = S.instructors[idx];
  if (!i) return '';

  const parts = i.name.split(' ');
  const initials = (parts[1]?.[0] || parts[0]?.[0] || '') + (parts[2]?.[0] || parts[1]?.[0] || '');
  const email = i.email || ((parts[1] || parts[0] || '').toLowerCase() + '@aurora.edu');

  // Dynamic Section Lookup
  const matchedSections = SECTIONS.filter(s => {
    try {
      const instructorLastName = i.name.split('. ').pop().split(' ').pop().toLowerCase();
      return s.instructor.toLowerCase().includes(instructorLastName);
    } catch (e) { return false; }
  }).map(s => s.code).join(', ') || i.sections || 'None';

  if (S.editingInstructor) {
    return `
    <div id="instDetailOverlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col animate-scaleUp">
        <style>
          @keyframes scaleUp {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
          }
          .animate-scaleUp {
            animation: scaleUp 0.15s cubic-bezier(0.16, 1, 0.3, 1) forwards;
          }
        </style>
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 shrink-0">
          <h3 class="font-bold text-slate-800 text-lg">Edit Personnel</h3>
          <button onclick="S.editingInstructor = false; render();" class="p-2 -mr-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-full transition-colors">${ico('close', 'w-5 h-5')}</button>
        </div>
        <div class="p-6 space-y-4 overflow-y-auto text-sm">
          <div>
            <label class="text-xs font-bold text-slate-500 uppercase">Full Name</label>
            <input id="editInstName" value="${i.name}" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300 font-medium" />
          </div>
          <div>
            <label class="text-xs font-bold text-slate-500 uppercase">Department / Role</label>
            <input id="editInstDept" value="${i.dept}" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase">Sections Load</label>
              <input id="editInstSections" value="${i.sections}" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase">Students Count</label>
              <input id="editInstStudents" type="number" value="${i.students}" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
            </div>
          </div>
          <div>
            <label class="text-xs font-bold text-slate-500 uppercase">Email Address</label>
            <input id="editInstEmail" type="email" value="${email}" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
          </div>
          <div>
            <label class="text-xs font-bold text-slate-500 uppercase">Status</label>
            <select id="editInstStatus" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300 bg-white">
              <option value="Active" ${i.status === 'Active' ? 'selected' : ''}>Active</option>
              <option value="On Leave" ${i.status === 'On Leave' ? 'selected' : ''}>On Leave</option>
            </select>
          </div>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
          <button onclick="S.editingInstructor = false; render();" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-100 transition">Cancel</button>
          <button onclick="saveInstructorEdits(${idx});" class="px-5 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition shadow-sm">Save Changes</button>
        </div>
      </div>
    </div>
    `;
  }

  return `
  <div id="instDetailOverlay" onclick="if(event.target === this) { S.selectedInstructorIndex = null; render(); }" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col animate-scaleUp">
      <style>
        @keyframes scaleUp {
          from { opacity: 0; transform: scale(0.95); }
          to { opacity: 1; transform: scale(1); }
        }
        .animate-scaleUp {
          animation: scaleUp 0.15s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
      </style>
      <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 shrink-0">
        <h3 class="font-bold text-slate-800 text-lg">Personnel Details</h3>
        <button onclick="S.selectedInstructorIndex = null; render();" class="p-2 -mr-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-full transition-colors">${ico('close', 'w-5 h-5')}</button>
      </div>
      <div class="p-6 space-y-6 overflow-y-auto">
        <div class="flex items-start gap-4">
          <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-500 to-blue-500 text-white flex items-center justify-center font-bold text-lg shadow-lg shrink-0">
            ${initials}
          </div>
          <div class="min-w-0">
            <h4 class="text-lg font-bold text-slate-900 leading-snug truncate">${i.name}</h4>
            <div class="text-sm text-slate-500 mt-0.5">${i.dept}</div>
            <div class="mt-2.5">${pill(i.status === 'Active' ? 'emerald' : 'slate', i.status)}</div>
          </div>
        </div>
        
        <div class="border-t border-slate-100 pt-5 space-y-4 text-sm">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('users', 'w-4 h-4')}</div>
            <div>
              <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Assigned Sections</div>
              <div class="font-semibold text-slate-800 mt-0.5">${matchedSections}</div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('book', 'w-4 h-4')}</div>
            <div>
              <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Handled Students</div>
              <div class="font-semibold text-slate-800 mt-0.5">${i.students} Cadets / Students</div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('mail', 'w-4 h-4')}</div>
            <div>
              <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Official Email</div>
              <div class="font-semibold text-slate-800 mt-0.5 text-indigo-600">${email}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between shrink-0">
        <button onclick="deleteInstructor(${idx});" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 transition">${ico('close', 'w-4 h-4')} Delete</button>
        <div class="flex items-center gap-2">
          <button onclick="S.editingInstructor = true; render();" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 transition">Edit</button>
          <button onclick="S.selectedInstructorIndex = null; render();" class="px-5 py-2 text-sm font-semibold rounded-lg bg-slate-900 text-white hover:bg-slate-800 transition shadow-sm">Close</button>
        </div>
      </div>
    </div>
  </div>
  `;
}

function cInstructors() {
  const cards = S.instructors.map((i, idx) => {
    const parts = i.name.split(' ');
    const initials = (parts[1]?.[0] || parts[0]?.[0] || '') + (parts[2]?.[0] || parts[1]?.[0] || '');
    const email = i.email || ((parts[1] || parts[0] || '').toLowerCase() + '@aurora.edu');
    return `<div onclick="S.selectedInstructorIndex = ${idx}; S.editingInstructor = false; render();" class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md hover:border-indigo-100 cursor-pointer transition transform hover:-translate-y-0.5 duration-200">
      <div class="flex items-start gap-3">
        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-blue-500 flex items-center justify-center text-white shrink-0 font-semibold">${initials}</div>
        <div class="flex-1 min-w-0">
          <div class="text-slate-900 font-normal truncate">${i.name}</div>
          <div class="text-xs text-slate-500 mt-0.5">${i.dept}</div>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3 mt-4 text-sm">
        <div><div class="text-xs text-slate-500">Sections</div><div class="text-slate-900 font-semibold">${i.sections}</div></div>
        <div><div class="text-xs text-slate-500">Students</div><div class="text-slate-900 font-semibold">${i.students}</div></div>
      </div>
      <div class="flex items-center gap-2 mt-4 text-xs text-slate-500">${ico('mail', 'w-3.5 h-3.5')} ${email}</div>
    </div>`;
  }).join('');
  const inviteModal = S.inviteForm ? `
  <div id="inviteOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-lg mx-4">
      <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
        <div>
          <div class="text-slate-900 tracking-tight">Invite Instructor</div>
          <div class="text-xs text-slate-500">Add a faculty member to the NSTP program</div>
        </div>
        <button id="inviteFormClose" class="text-slate-400 hover:text-slate-700 p-1">${ico('close', 'w-4 h-4')}</button>
      </div>
      <div class="p-6 space-y-4 text-sm">
        <div>
          <div class="text-xs text-slate-500 mb-1">Full Name</div>
          <input id="invName" placeholder="e.g. Prof. Juan Santos" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <div class="text-xs text-slate-500 mb-1">Department</div>
            <input id="invDept" placeholder="e.g. Computer Science" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
          </div>
          <div>
            <div class="text-xs text-slate-500 mb-1">University Email</div>
            <input id="invEmail" type="email" placeholder="e.g. j.santos@aurora.edu" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
          </div>
        </div>
        <div>
            <div class="text-xs text-slate-500 mb-1">Section Name</div>
            <input id="invSections" type="text" placeholder="e.g. BSCS-2A" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
          </div>
      </div>
      <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-2">
        <button id="inviteFormCancel" class="px-4 py-2 text-sm rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">Cancel</button>
        <button id="inviteFormSend" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">${ico('grad', 'w-4 h-4')} Add Instructor </button>
      </div>
    </div>
  </div>` : '';

  return `<div class="space-y-5">
    ${inviteModal}
    ${cInstructorModalHtml()}
    ${pageHdr('Instructors', 'Faculty directory and section load', `<button id="inviteInstructorBtn" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">${ico('grad', 'w-4 h-4')} Add Instructor </button>`)}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">${cards}</div>
  </div>`;
}

const APPROVALS = [
  { title: 'Tree-Planting Drive Report', instructor: 'Prof. Tan', section: 'CWTS 1-A', submitted: '2h ago', risk: 'Urgent' },
  { title: 'Adult Literacy Session #4', instructor: 'Prof. Santos', section: 'LTS 2-A', submitted: 'Yesterday', risk: 'Normal' },
  { title: 'Barangay Clean-Up Plan', instructor: 'Prof. Cruz', section: 'CWTS 1-C', submitted: 'May 9', risk: 'Normal' },
  { title: 'Reading Buddies Kick-off', instructor: 'Prof. Garcia', section: 'LTS 2-B', submitted: 'May 8', risk: 'Normal' },
];

function cApprovals() {
  const sel = APPROVALS[S.selApproval] || APPROVALS[0];
  const listItems = APPROVALS.map((a, i) => `<li data-approval="${i}" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 cursor-pointer ${S.selApproval === i ? 'bg-indigo-50/50' : ''}">
    <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">${ico('filetext', 'w-4 h-4')}</div>
    <div class="flex-1 min-w-0"><div class="text-sm text-slate-900 truncate">${a.title}</div><div class="text-xs text-slate-500 truncate">${a.instructor} · ${a.section}</div></div>
    ${pill(a.risk === 'Urgent' ? 'rose' : 'slate', a.submitted)}${ico('chevron', 'w-4 h-4 text-slate-300')}
  </li>`).join('');

  const attachmentModal = S.showAttachmentsModal ? `
      <div id="attachmentsModalOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 flex flex-col overflow-hidden">
          <style>@keyframes scaleIn{from{opacity:0;transform:scale(0.95)}to{opacity:1;transform:scale(1)}} .animate-scale-in{animation:scaleIn 0.2s ease-out forwards;}</style>
          <div class="animate-scale-in bg-white w-full h-full flex flex-col">
          <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div class="font-medium text-slate-900 tracking-tight">Activity Details & Attachments</div>
            <button id="closeAttachmentsBtn" class="text-slate-400 hover:text-slate-600 transition">${ico('close', 'w-5 h-5')}</button>
          </div>
          <div class="p-5 overflow-y-auto max-h-[70vh] space-y-5">
            <div>
              <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Activity Description</div>
              <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100">Activity executed on schedule at Barangay San Roque, with 28 cadets and 14 community volunteers participating. Total of 120 saplings planted across the riverside zone. All required documentation, attendance sheet, and beneficiary feedback forms attached.</p>
            </div>
            <div>
              <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Attached Files</div>
              <ul class="space-y-2">
                <li class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer group">
                  <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">${ico('filetext', 'w-4 h-4')}</div>
                  <div class="flex-1 text-sm text-slate-700 font-medium">Attendance_Sheet.pdf</div>
                  <button class="text-slate-400 hover:text-indigo-600 transition opacity-0 group-hover:opacity-100">${ico('download', 'w-4 h-4')}</button>
                </li>
                <li class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer group">
                  <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">${ico('image', 'w-4 h-4')}</div>
                  <div class="flex-1 text-sm text-slate-700 font-medium">IMG_20260515_1001.jpg</div>
                  <button class="text-slate-400 hover:text-indigo-600 transition opacity-0 group-hover:opacity-100">${ico('download', 'w-4 h-4')}</button>
                </li>
                <li class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer group">
                  <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">${ico('image', 'w-4 h-4')}</div>
                  <div class="flex-1 text-sm text-slate-700 font-medium">IMG_20260515_1002.jpg</div>
                  <button class="text-slate-400 hover:text-indigo-600 transition opacity-0 group-hover:opacity-100">${ico('download', 'w-4 h-4')}</button>
                </li>
              </ul>
            </div>
          </div>
          </div>
        </div>
      </div>
      ` : '';

  const revisionModal = S.revisionModal ? `
      <div id="revisionModalOverlay" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden" style="animation: scaleIn 0.2s ease-out forwards;">
          <style>@keyframes scaleIn{from{opacity:0;transform:scale(0.95)}to{opacity:1;transform:scale(1)}}</style>
          <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div class="font-semibold text-slate-900 tracking-tight">Revision Request</div>
            <button onclick="S.revisionModal = false; render();" class="text-slate-400 hover:text-slate-600 transition">${ico('close', 'w-5 h-5')}</button>
          </div>
          <div class="p-6 space-y-4">
            <div>
              <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">Revision Note / Feedback</label>
              <textarea id="revisionNoteArea" rows="4" class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-50/50 transition resize-none" placeholder="Explain what needs to be revised...">${S.revisionNote}</textarea>
            </div>
            <div class="text-xs text-slate-500 italic">
              This note will be visible to the instructor when they view the activity details.
            </div>
          </div>
          <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-3">
            <button onclick="S.revisionModal = false; render();" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 transition">Cancel</button>
            <button id="submitRevisionBtn" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200 transition">
              ${ico('send', 'w-4 h-4')} Send Revision Request
            </button>
          </div>
        </div>
      </div>
      ` : '';

  return `<div class="space-y-5">
    ${revisionModal}
    ${pageHdr('Report & Activity Approvals', 'Review and approve activity & accomplishment reports', `<button id="exportQueueBtn" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">${ico('download', 'w-4 h-4')} Export Queue</button>`)}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
      <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100"><div class="text-slate-900 tracking-tight">Pending Queue</div><div class="text-xs text-slate-500">${APPROVALS.length} awaiting review</div></div>
        <ul class="divide-y divide-slate-100">${listItems}</ul>
      </div>
      ${card(`<div class="grid grid-cols-3 gap-4 mb-5">
        <div class="p-3 rounded-lg bg-slate-50 border border-slate-100"><div class="text-xs text-slate-500">Submitted</div><div class="text-sm text-slate-900 mt-0.5">${sel.submitted}</div></div>
        <div class="p-3 rounded-lg bg-slate-50 border border-slate-100"><div class="text-xs text-slate-500">Beneficiaries</div><div class="text-sm text-slate-900 mt-0.5">42 community members</div></div>
        <button id="viewAttachmentsBtn" class="p-3 rounded-lg bg-slate-50 border border-slate-100 text-left hover:bg-indigo-50/60 transition cursor-pointer flex flex-col justify-center focus:outline-none focus:ring-2 focus:ring-indigo-500"><div class="text-xs text-slate-500">Attachments</div><div class="text-sm text-indigo-600 mt-0.5 font-medium flex items-center gap-1">${ico('filetext', 'w-3.5 h-3.5')} 6 photos · 1 PDF</div></button>
      </div>
      <div class="text-sm text-slate-700 leading-relaxed"><p>Activity executed on schedule at Barangay San Roque, with 28 cadets and 14 community volunteers participating. Total of 120 saplings planted across the riverside zone. All required documentation, attendance sheet, and beneficiary feedback forms attached.</p></div>
      <div class="mt-5 flex items-center gap-2 flex-wrap">
        <button onclick="
          APPROVALS.splice(S.selApproval || 0, 1);
          S.selApproval = 0;
          render();
        " class="inline-flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">${ico('check2', 'w-4 h-4')} Approve</button>
        <button onclick="S.revisionModal = true; render();" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">${ico('alertc', 'w-4 h-4')} Request Revisions</button>
        <button onclick="
          APPROVALS.splice(S.selApproval || 0, 1);
          S.selApproval = 0;
          render();
        " class="inline-flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg border border-slate-200 text-rose-600 hover:bg-rose-50">${ico('close', 'w-4 h-4')} Reject</button>
      </div>`, { title: sel.title, subtitle: `${sel.instructor} · ${sel.section}`, cls: 'lg:col-span-3' })}
    </div>
    ${attachmentModal}
  </div>`;
}

function cOCR() {
  const recentHtml = S.ocrUploads.length ? S.ocrUploads.map((r, ri) => `<li class="flex items-center gap-3 py-1">
    <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">${ico('filetext', 'w-4 h-4')}</div>
    <div class="flex-1 min-w-0"><div class="text-sm text-slate-900 truncate">${r.file}</div><div class="text-xs text-slate-500">${r.section} Â· ${r.students} students Â· ${r.time}</div></div>
    ${pill(r.status === 'Passed' ? 'emerald' : r.status === 'Failed' ? 'rose' : 'indigo', r.status)}
    ${r.status === 'Passed' ? `<button data-export-row="${ri}" title="Export student list as XLSX" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-700">${ico('download', 'w-3.5 h-3.5')} Export</button>` : ''}
  </li>`).join('') : `<li class="text-sm text-slate-400 text-center py-4">No uploads yet</li>`;
  return `<div class="space-y-5">
    ${pageHdr('OCR Grade Upload', 'Scan grade sheets and import directly into student records')}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
      ${card(`<div id="ocrDropZone" class="block border-2 border-dashed border-indigo-200 rounded-2xl p-10 text-center bg-indigo-50/40 cursor-pointer hover:bg-indigo-50 transition">
        <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-200">${ico('upload', 'w-6 h-6')}</div>
        <div class="mt-3 text-slate-900">Drop grade sheets here or click to upload</div>
        <div class="text-xs text-slate-500 mt-1">PDF, PNG, JPG up to 25 MB. Supports multi-page scans.</div>
        <div class="mt-4 inline-flex items-center gap-2 text-sm text-indigo-700">${ico('scan', 'w-4 h-4')} OCR engine v3.2</div>
        <input id="ocrFileInput" type="file" accept=".pdf,.png,.jpg,.jpeg" multiple class="hidden" />
      </div>
      <div class="mt-4 grid grid-cols-3 gap-3 text-sm">
        <div class="p-3 rounded-lg bg-slate-50 border border-slate-100"><div class="text-xs text-slate-500">Confidence</div><div class="text-slate-900 mt-0.5">98.4%</div></div>
        <div class="p-3 rounded-lg bg-slate-50 border border-slate-100"><div class="text-xs text-slate-500">Queue</div><div class="text-slate-900 mt-0.5" id="ocrQueueCount"> files</div></div>
        <div class="p-3 rounded-lg bg-slate-50 border border-slate-100"><div class="text-xs text-slate-500">Avg time</div><div class="text-slate-900 mt-0.5">~12s / page</div></div>
      </div>`, { cls: 'lg:col-span-2' })}
      ${card(`<ul class="space-y-3">${recentHtml}</ul>`, { title: 'Extracted Upload' })}
    </div>
  </div>`;
}

function cCalendar() {
  const calDays = [
    { d: 14, label: 'TUE', events: [{ t: 'Faculty Council', c: 'bg-indigo-500' }] },
    { d: 15, label: 'WED', events: [] },
    { d: 16, label: 'THU', events: [{ t: 'Midterm Reports Due', c: 'bg-rose-500' }] },
    { d: 17, label: 'FRI', events: [] },
    { d: 18, label: 'SAT', events: [] },
    { d: 19, label: 'SUN', events: [] },
    { d: 20, label: 'MON', events: [{ t: 'OCR Upload Window', c: 'bg-emerald-500' }] },
  ];
  const calGrid = calDays.map(d => `<div class="border border-slate-200 rounded-lg p-3 min-h-[140px] bg-white">
    <div class="text-[10px] uppercase tracking-wider text-slate-500">${d.label}</div>
    <div class="text-slate-900 tracking-tight text-xl mt-0.5">${d.d}</div>
    <div class="mt-3 space-y-1.5">${d.events.map(e => `<div class="${e.c} text-[11px] px-2 py-1 rounded text-white truncate">${e.t}</div>`).join('')}</div>
  </div>`).join('');
  const actList = S.activities.map((a, i) => `<li onclick="S.selectedCalActivity = ${i}; S.editingActivity = false; render();" class="px-5 py-4 flex items-center gap-4 hover:bg-slate-50 cursor-pointer transition transform hover:-translate-y-0.5 duration-200">
    <div class="w-1.5 self-stretch rounded-full ${a.color}"></div>
    <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">${ico('calendar', 'w-4 h-4')}</div>
    <div class="flex-1 min-w-0">
      <div class="text-sm text-slate-900 truncate">${a.title}</div>
      <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-2 flex-wrap">
        <span class="inline-flex items-center gap-1">${ico('calendar', 'w-3 h-3')}${a.date}</span>
        <span class="w-1 h-1 rounded-full bg-slate-300 inline-block"></span>
        <span class="inline-flex items-center gap-1">${ico('clock', 'w-3 h-3')}${a.time}</span>
        <span class="w-1 h-1 rounded-full bg-slate-300 inline-block"></span>
        <span class="inline-flex items-center gap-1">${ico('mappin', 'w-3 h-3')}${a.venue}</span>
      </div>
    </div>
    ${pill('indigo', a.scope)}
    ${pill(a.status === 'Submitted' ? 'emerald' : 'slate', a.status)}
  </li>`).join('');
  const formHtml = S.calForm ? card(`<div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
    <div class="md:col-span-2"><div class="text-xs text-slate-500 mb-1">Title</div><input id="calTitle" placeholder="e.g. Spring Commencement Rehearsal" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" /></div>
    <div><div class="text-xs text-slate-500 mb-1">Date</div><input type="date" id="calDate" class="w-full px-3 py-2 rounded-lg border border-slate-200" /></div>
    <div><div class="text-xs text-slate-500 mb-1">Time</div><input id="calTime" placeholder="e.g. 2:00 PM" class="w-full px-3 py-2 rounded-lg border border-slate-200" /></div>
    <div><div class="text-xs text-slate-500 mb-1">Venue</div><input id="calVenue" placeholder="e.g. Main Auditorium" class="w-full px-3 py-2 rounded-lg border border-slate-200" /></div>
    <div><div class="text-xs text-slate-500 mb-1">Audience</div>
      <select id="calScope" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white">
        <option>All Programs</option><option>All Instructors</option><option>Faculty</option><option>Graduating Students</option><option>CWTS</option><option>LTS</option><option>ROTC</option>
      </select>
    </div>
    <div class="md:col-span-2"><div class="text-xs text-slate-500 mb-1">Description</div><textarea rows="3" id="calDesc" placeholder="Briefly describe the activity, objectives, and requirements." class="w-full px-3 py-2 rounded-lg border border-slate-200"></textarea></div>
  </div>
  <div class="flex items-center gap-2 mt-4">
    <button id="calCancel" class="px-3 py-2 text-sm rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">Cancel</button>
    <button id="calCreate" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">${ico('plus', 'w-4 h-4')} Create Activity</button>
  </div>`, { title: 'New Activity', subtitle: 'Fill in the details and submit for publication', action: `<button id="calFormClose" class="text-slate-400 hover:text-slate-700">${ico('close', 'w-4 h-4')}</button>` }) : '';
  return `<div class="space-y-5">
    ${pageHdr('Activity Calendar', 'Plan, draft, and publish program-wide activities', `
      <button id="calFormOpen" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">${ico('plus', 'w-4 h-4')} Create Activity</button>
      `)}
    ${formHtml}
    ${cActivityModalHtml()}
    ${card(`<div class="grid grid-cols-7 gap-2">${calGrid}</div>`, { title: 'Week of May 14 – May 20, 2026' })}
    ${card(`<ul class="-mx-5 -my-5 divide-y divide-slate-100">${actList}</ul>`, { title: 'All Activities', subtitle: `${S.activities.length} scheduled` })}
  </div>`;
}

const BATCH_STUDENTS = [
  // 0 â€” CWTS 1
  ['Maria Aquino', 'Jose Reyes', 'Anna Bautista', 'Carlos Mendoza', 'Lea Santos', 'Ryan Torres', 'Gina Flores', 'Mark Ibarra', 'Pia Lacson', 'Jed Macaraeg', 'Ria Manalo', 'Carlo Mendoza', 'Liza Miranda', 'Tony Molina', 'Gina Morales', 'Ed Navarro', 'Lea Ocampo', 'Rene Padilla', 'Dot Perez', 'Vic Pineda', 'Joy Ramos', 'Ben Rivera', 'Cris Rodriguez', 'Fely Romero', 'Greg Ruiz', 'Ivy Salvador', 'Jan Sanchez', 'Ken Santiago', 'Luz Santos'],
  // 1 â€” LTS 2
  ['Karl Domingo', 'Ysa Valdes', 'Zack Valencia', 'Amy Vargas', 'Ben Vega', 'Cara Vera', 'Dan Vergara', 'Eva Vidal', 'Fred Villa', 'Gina Villanueva', 'Hank Villar', 'Jay Santos', 'Elle Torres', 'Ryan Uy', 'Nica Valdez', 'Eric Villanueva', 'Carla Yap', 'Mike Zulueta', 'Donna Alcantara', 'Jed Buenaventura'],
  // 2 â€” ROTC Batch 14
  ['Marco Aguila', 'Lea Bautista', 'Jana Cruz', 'Rey Dela Torre', 'Kira Espino', 'Luis Fernandez', 'Tina Garcia', 'Paolo Hernandez', 'Ana Jimenez', 'Carl Lim', 'Rosa Magno', 'Sam Navarro', 'Beth Ocampo', 'Dave Pascual', 'Mia Reyes'],
];

function cCertificates() {
  const batches = S.batches;
  const recent = S.recentCerts;

  // Student list modal
  const mb = S.certModal !== null ? batches[S.certModal] : null;
  const mbStudents = S.certModal !== null ? (BATCH_STUDENTS[S.certModal] || []) : [];
  const certModal = mb ? `
  <div id="certModalOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-lg mx-4 flex flex-col max-h-[85vh]">
      <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between shrink-0">
        <div>
          <div class="text-slate-900 tracking-tight">${mb.name}</div>
          <div class="text-xs text-slate-500">${mb.program} · ${mbStudents.length} students listed · ${mb.date}</div>
        </div>
        <button id="certModalClose" class="text-slate-400 hover:text-slate-700 p-1">${ico('close', 'w-4 h-4')}</button>
      </div>
      <div class="overflow-y-auto flex-1 px-6 py-3">
        <div class="text-xs uppercase tracking-wider text-slate-500 mb-3">Students</div>
        <ul class="space-y-1">
          ${mbStudents.map((name, si) => `<li class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0">
            <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px] font-medium shrink-0">${si + 1}</span>
            <span class="flex-1 text-sm text-slate-800">${name}</span>
            <button data-student-cert="${si}" data-batch-idx="${S.certModal}" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">${ico('award', 'w-3 h-3')} Certificate</button>
          </li>`).join('')}
        </ul>
      </div>
      <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between shrink-0">
        <span class="text-xs text-slate-500">${mbStudents.length} student(s)</span>
        <button id="certModalGenAll" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg bg-slate-900 text-white hover:bg-slate-800">${ico('award', 'w-4 h-4')} Generate All</button>
      </div>
    </div>
  </div>` : '';

  const batchCards = batches.map((b, bi) => {
    const isNew = S.newlyImportedBatchIndex === bi;
    const borderCls = isNew ? 'border-emerald-400 ring-2 ring-emerald-100 animate-pulse' : 'border-slate-100';
    const isSelected = S.selectedBatchIdx === bi;
    const deleteBtnHtml = isSelected
      ? `<button data-delete-batch="${bi}" class="px-3 py-1.5 text-sm rounded-lg bg-rose-600 hover:bg-rose-700 text-white flex items-center gap-1 transition-all duration-150 transform scale-100 shadow-sm shrink-0">
           ${ico('trash', 'w-3.5 h-3.5')} Delete
         </button>`
      : '';
    return `<div data-batch-card="${bi}" class="bg-white rounded-2xl border ${borderCls} p-5 shadow-sm flex items-center gap-4 transition-all duration-300 cursor-pointer hover:border-slate-300">
    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-500 flex items-center justify-center text-white shadow shrink-0">${ico('award', 'w-6 h-6')}</div>
    <div class="flex-1 min-w-0"><div class="text-slate-900 font-medium">${b.name}</div><div class="text-xs text-slate-500 mt-0.5">${b.count} students · ${b.date}</div></div>
    ${pill(b.status === 'Ready' ? 'emerald' : 'amber', b.status)}
    <div class="flex items-center gap-2 shrink-0">
      ${deleteBtnHtml}
      <button data-cert-batch="${bi}" class="px-3 py-1.5 text-sm rounded-lg bg-slate-900 text-white hover:bg-slate-800">Generate</button>
    </div>
  </div>`;
  }).join('');
  const recentHtml = recent.map((r, ri) => {
    const isSelected = S.selectedRecentCertIdx === ri;
    const actionHtml = isSelected
      ? `<button data-delete-recent="${ri}" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-rose-600 hover:bg-rose-700 text-white flex items-center gap-1 transition-all duration-150 transform scale-100 shadow-sm shrink-0">
           ${ico('trash', 'w-3 h-3')} Delete
         </button>`
      : `<div class="text-xs text-slate-500 transition-all duration-150 shrink-0">${r.issued}</div>`;
    return `<li data-recent-item="${ri}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 cursor-pointer transition-all duration-200">
      <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">${ico('check2', 'w-4 h-4')}</div>
      <div class="flex-1 min-w-0">
        <div class="text-sm text-slate-900 truncate font-medium">${r.name}</div>
        <div class="text-xs text-slate-500">${r.program} · ${r.id}</div>
      </div>
      ${actionHtml}
    </li>`;
  }).join('');
  return `<div class="space-y-5">
    ${certModal}
    ${pageHdr('Certificate Overview', 'Click a row to view the student list',
    `<input type="file" id="certXlsxInput" accept=".xlsx,.xls" class="hidden" />
            <button id="certXlsxBtn" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border-2 border-dashed border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:border-emerald-400 transition text-sm">
              ${ico('upload', 'w-4 h-4')} Import XLSX File of Grades
            </button>`)}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
      <div class="lg:col-span-2 space-y-4">${batchCards}</div>
      ${card(`<ul class="space-y-3">${recentHtml}</ul>`, { title: 'Recently Issued' })}
    </div>
  </div>`;
}

function cAudit() {
  const logs = [
    { actor: 'Maya Reyes', action: 'Approved', target: 'Tree-Planting Drive Report', time: 'Today 11:24 AM', type: 'approval' },
    { actor: 'OCR Engine', action: 'Passed', target: 'BSCS-2A_Midterm.pdf (42 records)', time: 'Today 10:14 AM', type: 'system' },
    { actor: 'Maya Reyes', action: 'Generated', target: 'Spring 2026 — CWTS 1 batch (198 certs)', time: 'Today 9:30 AM', type: 'system' },
    { actor: 'Lester Tan', action: 'Submitted', target: 'Adult Literacy Session #4', time: 'Yesterday', type: 'submission' },
    { actor: 'System', action: 'Failed Login Attempt', target: 'instructor: r.cruz@aurora.edu', time: 'Yesterday', type: 'alert' },
    { actor: 'Maya Reyes', action: 'Updated section', target: 'BSIT-3A (added 2 students)', time: 'May 9', type: 'edit' },
    { actor: 'Adam Yusuf', action: 'Requested revisions', target: 'Barangay Clean-Up Plan', time: 'May 8', type: 'approval' },
  ];
  const tc = { approval: 'emerald', system: 'indigo', submission: 'violet', alert: 'rose', edit: 'amber' };

  const activeFilter = S.auditFilter || 'all';
  const filteredLogs = logs.filter(l => {
    if (activeFilter !== 'all' && l.type !== activeFilter) return false;
    if (S.auditSearch) {
      const q = S.auditSearch.toLowerCase();
      return l.actor.toLowerCase().includes(q) ||
        l.target.toLowerCase().includes(q) ||
        l.action.toLowerCase().includes(q);
    }
    return true;
  });

  const items = filteredLogs.map(l => `<li class="flex items-start gap-3 py-2">
    <div class="w-2 h-2 mt-2 rounded-full bg-slate-300 shrink-0"></div>
    <div class="flex-1 min-w-0">
      <div class="text-sm text-slate-900"><span class="text-slate-700">${l.actor}</span> <span class="text-slate-500">${l.action.toLowerCase()}</span> <span class="text-slate-900">${l.target}</span></div>
      <div class="text-xs text-slate-500 mt-0.5">${l.time}</div>
    </div>
    ${pill(tc[l.type] || 'slate', l.action)}
  </li>`).join('');

  const activeLabel = { all: 'All Logs', approval: 'Approvals', system: 'System Logs', submission: 'Submissions', alert: 'Alerts', edit: 'Edits' }[activeFilter];
  const filterBtnHtml = `
    <div class="flex items-center gap-2">
      <div class="relative">
        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
          ${ico('search', 'w-3.5 h-3.5 text-slate-400')}
        </span>
        <input id="auditSearchInput" type="text" autocomplete="off"
               class="w-48 pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white text-slate-700 placeholder-slate-400 focus:outline-none focus:border-indigo-300 transition-colors shadow-sm"
               placeholder="Search logs..."
               value="${S.auditSearch || ''}" />
      </div>
      <div class="relative font-medium">
        <button id="auditFilterBtn" type="button"
               class="w-48 pl-3 pr-8 py-1.5 text-xs font-medium rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus:outline-none focus:border-indigo-300 transition-colors shadow-sm flex items-center justify-between cursor-pointer">
          <span>${activeLabel}</span>
          <span class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
            ${ico('chevron', 'w-3.5 h-3.5')}
          </span>
        </button>
        ${S.showAuditFilterMenu ? `
        <div id="auditFilterDropdown" class="absolute right-0 mt-1.5 w-48 rounded-xl bg-white border border-slate-200 shadow-lg py-1.5 z-50">
          <div class="px-3 py-1 text-[10px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-50 mb-1">Filter by type</div>
          ${[
        { val: 'all', label: 'All Logs' },
        { val: 'approval', label: 'Approvals' },
        { val: 'system', label: 'System Logs' },
        { val: 'submission', label: 'Submissions' },
        { val: 'alert', label: 'Alerts' },
        { val: 'edit', label: 'Edits' }
      ].map(opt => {
        const isSel = activeFilter === opt.val;
        return `
            <button data-audit-filter-opt="${opt.val}" class="w-full text-left px-3.5 py-1.5 text-sm hover:bg-slate-50 transition-colors flex items-center justify-between ${isSel ? 'text-indigo-600 font-semibold bg-indigo-50/40' : 'text-slate-700'}">
              <span>${opt.label}</span>
              ${isSel ? ico('check', 'w-4 h-4 text-indigo-600') : ''}
            </button>
            `;
      }).join('')}
        </div>
        ` : ''}
      </div>
    </div>
  `;

  return `<div class="space-y-5">
    ${pageHdr('Audit Logs Overview', 'Immutable record of every action taken in the program office', `
      <div class="flex items-center gap-2">
        ${filterBtnHtml}
        <button id="exportAuditCSV" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">${ico('download', 'w-4 h-4')} Export CSV</button>
      </div>
    `)}
    ${card(filteredLogs.length ? `<ul class="space-y-3">${items}</ul>` : `<div class="text-center text-sm text-slate-400 py-6">No logs found matching filter</div>`)}
  </div>`;
}

function cStudentArchive() {
  let entries = S.studentArchive || [];

  // Apply Search
  if (S.archiveSearch) {
    const q = S.archiveSearch.toLowerCase();
    entries = entries.filter(st => 
      st.name.toLowerCase().includes(q) ||
      st.studentNo.toLowerCase().includes(q) ||
      st.section.toLowerCase().includes(q) ||
      st.instructor.toLowerCase().includes(q)
    );
  }

  // Apply Program Filter
  if (S.archiveFilterProgram && S.archiveFilterProgram !== 'All') {
    entries = entries.filter(st => st.program === S.archiveFilterProgram);
  }

  // Apply Remarks Filter
  if (S.archiveFilterRemarks && S.archiveFilterRemarks !== 'All') {
    entries = entries.filter(st => st.remarks === S.archiveFilterRemarks);
  }

  // Calculations for stats
  const total = entries.length;
  const passed = entries.filter(st => st.remarks === 'Passed').length;
  const passRate = total ? Math.round((passed / total) * 100) : 0;
  const failRate = total ? 100 - passRate : 0;

  // Stats Cards
  const statsHtml = `
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
      ${card(`
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">${ico('users', 'w-6 h-6')}</div>
          <div>
            <div class="text-2xl font-bold text-slate-900">${total}</div>
            <div class="text-xs text-slate-500 font-medium">Archived Student Records</div>
          </div>
        </div>
      `, { cls: 'border border-slate-100 shadow-sm' })}
      ${card(`
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">${ico('check2', 'w-6 h-6')}</div>
          <div>
            <div class="text-2xl font-bold text-slate-900">${passRate}%</div>
            <div class="text-xs text-slate-500 font-medium">Passing Rate (Passed: ${passed})</div>
          </div>
        </div>
      `, { cls: 'border border-slate-100 shadow-sm' })}
      ${card(`
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">${ico('alertc', 'w-6 h-6')}</div>
          <div>
            <div class="text-2xl font-bold text-slate-900">${failRate}%</div>
            <div class="text-xs text-slate-500 font-medium">Failure / Rem. Rate (Failed: ${total - passed})</div>
          </div>
        </div>
      `, { cls: 'border border-slate-100 shadow-sm' })}
    </div>
  `;

  // Filter Actions Bar
  const filtersBarHtml = `
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
      <div class="flex items-center gap-3 flex-wrap flex-1 min-w-0">
        <div class="relative flex-1 max-w-xs min-w-[200px]">
          <span class="absolute left-3 top-2.5 text-slate-400">${ico('search', 'w-4 h-4')}</span>
          <input id="archiveSearchInput" value="${S.archiveSearch || ''}" placeholder="Search name, student no, section..." class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-slate-200 bg-white focus:outline-none focus:border-indigo-300" />
        </div>
        
        <div class="flex items-center gap-2">
          <span class="text-xs text-slate-500 font-medium">Program:</span>
          <select id="archiveProgSelect" onchange="S.archiveFilterProgram = this.value; render();" class="px-2.5 py-1.5 text-sm rounded-lg border border-slate-200 bg-white focus:outline-none focus:border-indigo-300">
            <option value="All" ${S.archiveFilterProgram === 'All' ? 'selected' : ''}>All Programs</option>
            <option value="CWTS" ${S.archiveFilterProgram === 'CWTS' ? 'selected' : ''}>CWTS</option>
            <option value="LTS" ${S.archiveFilterProgram === 'LTS' ? 'selected' : ''}>LTS</option>
            <option value="ROTC" ${S.archiveFilterProgram === 'ROTC' ? 'selected' : ''}>ROTC</option>
          </select>
        </div>

        <div class="flex items-center gap-2">
          <span class="text-xs text-slate-500 font-medium">Status:</span>
          <select id="archiveRemarksSelect" onchange="S.archiveFilterRemarks = this.value; render();" class="px-2.5 py-1.5 text-sm rounded-lg border border-slate-200 bg-white focus:outline-none focus:border-indigo-300">
            <option value="All" ${S.archiveFilterRemarks === 'All' ? 'selected' : ''}>All Remarks</option>
            <option value="Passed" ${S.archiveFilterRemarks === 'Passed' ? 'selected' : ''}>Passed Only</option>
            <option value="Failed" ${S.archiveFilterRemarks === 'Failed' ? 'selected' : ''}>Failed Only</option>
          </select>
        </div>
      </div>

      <div>
        <button id="archiveExportBtn" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition shadow-md shadow-indigo-100 w-full md:w-auto">
          ${ico('download', 'w-4 h-4')} Export Grades Archive
        </button>
      </div>
    </div>
  `;

  // Table Render
  const thead = `<thead><tr class="text-left text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-100">
    <th class="py-2.5 px-3 font-medium">Student No</th>
    <th class="py-2.5 px-3 font-medium">Student Name</th>
    <th class="py-2.5 px-3 font-medium">Gender</th>
    <th class="py-2.5 px-3 font-medium">Section</th>
    <th class="py-2.5 px-3 font-medium">Program</th>
    <th class="py-2.5 px-3 font-medium">Instructor</th>
    <th class="py-2.5 px-3 font-medium text-center">Midterm</th>
    <th class="py-2.5 px-3 font-medium text-center">Finals</th>
    <th class="py-2.5 px-3 font-medium">Remarks</th>
    <th class="py-2.5 px-3 font-medium">Date Archived</th>
  </tr></thead>`;

  const tbody = `<tbody>${entries.length ? entries.map(st => {
    return `<tr class="border-b border-slate-50 hover:bg-slate-50 transition">
      <td class="py-3 px-3 font-mono text-slate-500 text-xs">${st.studentNo}</td>
      <td class="py-3 px-3 text-slate-900 font-semibold">${st.name}</td>
      <td class="py-3 px-3 text-slate-600 text-xs">${st.gender}</td>
      <td class="py-3 px-3 text-slate-700 font-medium">${st.section}</td>
      <td class="py-3 px-3"><span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full ${st.program === 'CWTS' ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : st.program === 'LTS' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100'}">${st.program}</span></td>
      <td class="py-3 px-3 text-slate-600">${st.instructor}</td>
      <td class="py-3 px-3 text-indigo-600 text-center font-bold font-mono">${st.midtermGrade.toFixed(2)}</td>
      <td class="py-3 px-3 text-indigo-600 text-center font-bold font-mono">${st.finalGrade.toFixed(2)}</td>
      <td class="py-3 px-3">${pill(st.remarks === 'Passed' ? 'emerald' : 'rose', st.remarks)}</td>
      <td class="py-3 px-3 text-slate-500 text-xs">${st.dateArchived}</td>
    </tr>`;
  }).join('') : `<tr><td colspan="10" class="py-12 text-center text-slate-400 text-sm">No archived student grade records match the current filters.</td></tr>`}</tbody>`;

  const tableCardHtml = card(`
    <div class="overflow-x-auto">
      <table class="w-full text-sm min-w-[1100px]">${thead}${tbody}</table>
    </div>
  `, { cls: 'border border-slate-100 shadow-sm' });

  return `
    <div class="space-y-6">
      ${pageHdr('Student Grades Archiving System', 'Access completed historic records, enrollment profiles, and parsed grade extracts.')}
      ${statsHtml}
      ${filtersBarHtml}
      ${tableCardHtml}
    </div>
  `;
}

/* ================================================================
   INSTRUCTOR PAGES
================================================================ */
const I_CLASSES = [
  { code: 'CWTS 1', sec: 'Section A', title: 'Community Welfare Training Service', students: 42, room: 'Bldg. B · Rm 204', sched: 'Mon · 1:00–4:00 PM', prog: 68, accent: 'from-indigo-500 to-blue-500', badge: 'Active', bc: 'emerald' },
  { code: 'CWTS 1', sec: 'Section C', title: 'Community Welfare Training Service', students: 38, room: 'Bldg. B · Rm 206', sched: 'Tue · 9:00–12:00 PM', prog: 54, accent: 'from-violet-500 to-fuchsia-500', badge: 'Active', bc: 'emerald' },
  { code: 'LTS 2', sec: 'Section A', title: 'Literacy Training Service', students: 35, room: 'Bldg. D · Rm 110', sched: 'Wed · 1:00–4:00 PM', prog: 72, accent: 'from-amber-500 to-orange-500', badge: 'Field Day', bc: 'amber' },
  { code: 'LTS 2', sec: 'Section B', title: 'Literacy Training Service', students: 31, room: 'Bldg. D · Rm 112', sched: 'Thu · 9:00–12:00 PM', prog: 41, accent: 'from-emerald-500 to-teal-500', badge: 'Active', bc: 'emerald' },
];
const I_REPORTS = [
  { title: 'Tree-Planting Drive Report', cls: 'CWTS 1 · Sec A', due: 'Due May 15', status: 'Draft', beneficiaries: 42, narrative: 'Planted 50 saplings along the riverside. The community members were very helpful.', file: 'attendance.pdf' },
  { title: 'Adult Literacy Session #4', cls: 'LTS 2 · Sec A', due: 'Submitted May 9', status: 'Submitted', beneficiaries: 35, narrative: 'Conducted one-on-one reading sessions using the newly provided modules.', file: 'photos.zip' },
  { title: 'Barangay Clean-Up Plan', cls: 'CWTS 1 · Sec C', due: 'Approved May 6', status: 'Approved', beneficiaries: 38, narrative: 'Successfully cleared 500kg of trash from the coastal area.', file: 'report-final.pdf' },
  { title: 'Reading Buddies Kick-off', cls: 'LTS 2 · Sec B', due: 'Needs revisions', status: 'Revisions', beneficiaries: 31, narrative: 'Initial kickoff session with the kids. Needs more detailed attendance records.', file: 'draft-1.docx', feedback: 'Please attach the official DSWD clearance form as part of the documentation.' },
  { title: 'Mid-semester Accomplishment', cls: 'All sections', due: 'Due May 22', status: 'Draft', beneficiaries: 146, narrative: 'Consolidated report for all mid-semester activities across all assigned sections.', file: null },
];
const I_STATS = [
  { label: 'Assigned Sections', value: '4', sub: 'CWTS · LTS', ico: 'book', color: 'from-indigo-500 to-blue-500' },
  { label: 'Total Students', value: '146', sub: 'Across all sections', ico: 'users', color: 'from-emerald-500 to-teal-500' },
  { label: 'Reports Pending', value: '3', sub: '2 due this week', ico: 'filecheck', color: 'from-amber-500 to-orange-500' },
  { label: 'Approved YTD', value: '27', sub: '+5 this month', ico: 'check2', color: 'from-violet-500 to-fuchsia-500' },
];

function statusMeta(s) {
  return { Draft: { pill: 'slate', ico: 'pencil' }, Submitted: { pill: 'indigo', ico: 'clock' }, Approved: { pill: 'emerald', ico: 'check2' }, Revisions: { pill: 'rose', ico: 'alertc' } }[s] || { pill: 'slate', ico: 'pencil' };
}

function classCardHtml(c, mode = 'card') {
  const secKey = c.code.replace(' ', '-') + (c.sec ? c.sec.split(' ')[1] : '');
  const progBar = `<div class="mt-4"><div class="flex items-center justify-between text-xs text-slate-500 mb-1.5"><span>Semester progress</span><span class="text-slate-700">${c.prog}%</span></div><div class="h-1.5 rounded-full bg-slate-100 overflow-hidden"><div class="h-full rounded-full bg-gradient-to-r ${c.accent}" style="width:${c.prog}%"></div></div></div>`;
  if (mode === 'full') return `<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden transition hover:shadow-md cursor-pointer hover:border-indigo-200" data-instr-sec="${secKey}">
    <div class="px-5 py-4 bg-gradient-to-r ${c.accent} text-white flex items-center justify-between">
      <div><div class="tracking-tight">${c.code} · ${c.sec}</div><div class="text-xs text-white/85">${c.title}</div></div>
      ${pill(c.bc, c.badge)}
    </div>
    <div class="p-5">
      <div class="grid grid-cols-3 gap-3 text-xs text-slate-600">
        <div class="flex items-center gap-1.5">${ico('users', 'w-3.5 h-3.5 text-slate-400')}${c.students} students</div>
        <div class="flex items-center gap-1.5">${ico('mappin', 'w-3.5 h-3.5 text-slate-400')}${c.room}</div>
        <div class="flex items-center gap-1.5">${ico('clock', 'w-3.5 h-3.5 text-slate-400')}${c.sched}</div>
      </div>
      ${progBar}
    </div>
  </div>`;
  return `<div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm transition hover:shadow-md cursor-pointer hover:border-indigo-200" data-instr-sec="${secKey}">
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl bg-gradient-to-br ${c.accent} flex items-center justify-center text-white tracking-tight shrink-0">${c.code.split(' ')[0]}</div>
        <div><div class="text-slate-900 tracking-tight">${c.code} · ${c.sec}</div><div class="text-xs text-slate-500">${c.title}</div></div>
      </div>
      ${pill(c.bc, c.badge)}
    </div>
    <div class="grid grid-cols-3 gap-3 mt-5 text-xs text-slate-600">
      <div class="flex items-center gap-1.5">${ico('users', 'w-3.5 h-3.5 text-slate-400')}${c.students} students</div>
      <div class="flex items-center gap-1.5">${ico('mappin', 'w-3.5 h-3.5 text-slate-400')}${c.room}</div>
      <div class="flex items-center gap-1.5">${ico('clock', 'w-3.5 h-3.5 text-slate-400')}${c.sched}</div>
    </div>
    ${progBar}
  </div>`;
}

function iOverview() {
  const statsHtml = I_STATS.map(s => `<div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-gradient-to-br ${s.color} flex items-center justify-center text-white shadow shrink-0">${ico(s.ico, 'w-5 h-5')}</div>
      <div class="min-w-0"><div class="text-xs text-slate-500 truncate">${s.label}</div><div class="text-slate-900 tracking-tight text-2xl">${s.value}</div></div>
    </div>
    <div class="text-xs text-slate-500 mt-3">${s.sub}</div>
  </div>`).join('');
  const subTracker = I_REPORTS.map(r => {
    const sm = statusMeta(r.status); return `<li class="px-5 py-4 flex items-start gap-3 hover:bg-slate-50 cursor-pointer">
    <div class="flex-1 min-w-0">
      <div class="flex items-center justify-between gap-2">
        <div class="text-sm text-slate-900 truncate">${r.title}</div>
        ${pill(sm.pill, `<span class="inline-flex items-center gap-1">${ico(sm.ico, 'w-3 h-3')}${r.status}</span>`)}
      </div>
      <div class="text-xs text-slate-500 mt-0.5">${r.cls} · ${r.due}</div>
    </div>
  </li>`;
  }).join('');

  // Dynamic Calendar for Overview Dashboard
  const calDays = [
    { d: 14, label: 'TUE', events: [] },
    { d: 15, label: 'WED', events: [] },
    { d: 16, label: 'THU', events: [] },
    { d: 17, label: 'FRI', events: [] },
    { d: 18, label: 'SAT', events: [] },
    { d: 19, label: 'SUN', events: [] },
    { d: 20, label: 'MON', events: [] },
    { d: 21, label: 'TUE', events: [] },
    { d: 22, label: 'WED', events: [] },
    { d: 23, label: 'THU', events: [] },
    { d: 24, label: 'FRI', events: [] },
    { d: 25, label: 'SAT', events: [] },
    { d: 26, label: 'SUN', events: [] },
    { d: 27, label: 'MON', events: [] },
  ];

  S.activities.forEach(act => {
    const d = new Date(act.date);
    if (!isNaN(d) && d.getMonth() === 4) {
      const dateVal = d.getDate();
      const dayMatch = calDays.find(cd => cd.d === dateVal);
      if (dayMatch) {
        if (!dayMatch.events.some(e => e.t === act.title)) {
          dayMatch.events.push({ t: act.title, c: act.color || 'bg-emerald-500' });
        }
      }
    }
  });

  const calGrid = calDays.map(d => {
    let clickAttr = '';
    let cursorClass = '';
    if (d.events.length > 0) {
      const firstEvent = d.events[0];
      const actIdx = S.activities.findIndex(a => a.title === firstEvent.t);
      if (actIdx !== -1) {
        clickAttr = `onclick="S.selectedCalActivity = ${actIdx}; render();"`;
        cursorClass = 'cursor-pointer hover:bg-white hover:border-emerald-200 hover:shadow-md';
      }
    }
    return `<div ${clickAttr} class="border border-slate-100 rounded-xl p-2 min-h-[90px] bg-slate-50/50 transition duration-200 text-center flex flex-col justify-between ${cursorClass}">
    <div class="text-[9px] uppercase tracking-wider text-slate-400 font-semibold">${d.label}</div>
    <div class="text-slate-950 tracking-tight text-base font-bold my-0.5">${d.d}</div>
    <div class="w-full mt-1 flex justify-center gap-1 flex-wrap">${d.events.map(e => {
      const actIdx = S.activities.findIndex(a => a.title === e.t);
      const dotClickAttr = actIdx !== -1 ? `onclick="event.stopPropagation(); S.selectedCalActivity = ${actIdx}; render();"` : '';
      return `<span ${dotClickAttr} class="w-2 h-2 rounded-full ${e.c} cursor-pointer hover:scale-125 transition inline-block animate-pulse" title="${e.t}"></span>`;
    }).join('')}</div>
  </div>`;
  }).join('');

  const calWidgetHtml = `
  <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
    <div class="flex items-center justify-between mb-3.5">
      <div>
        <div class="text-slate-900 font-bold tracking-tight text-sm">Calendar of Activities</div>
        <div class="text-[11px] text-slate-500 mt-0.5">Two-week preview of official dates</div>
      </div>
      <span class="inline-flex items-center gap-1 text-[11px] text-slate-500 font-medium">${ico('calendar', 'w-3.5 h-3.5 text-slate-400')} May 2026</span>
    </div>
    <div class="grid grid-cols-7 gap-1.5">${calGrid}</div>
  </div>
  `;

  const modalHtml = (S.selectedCalActivity !== null && S.selectedCalActivity !== undefined) ? (() => {
    const act = S.activities[S.selectedCalActivity];
    if (!act) return '';
    return `
    <div id="calDetailOverlay" onclick="if(event.target === this) { S.selectedCalActivity = null; render(); }" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col animate-scaleUp">
        <style>
          @keyframes scaleUp {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
          }
          .animate-scaleUp {
            animation: scaleUp 0.15s cubic-bezier(0.16, 1, 0.3, 1) forwards;
          }
        </style>
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 shrink-0">
          <h3 class="font-bold text-slate-800 text-lg">Activity Details</h3>
          <button onclick="S.selectedCalActivity = null; render();" class="p-2 -mr-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-full transition-colors">${ico('close', 'w-5 h-5')}</button>
        </div>
        <div class="p-6 space-y-5 overflow-y-auto">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-emerald-100 shrink-0">
              ${ico('calendar', 'w-6 h-6')}
            </div>
            <div>
              <h4 class="text-lg font-bold text-slate-900 leading-snug">${act.title}</h4>
              <div class="mt-1.5 flex gap-2">${pill('indigo', act.scope)}${pill(act.status === 'Submitted' ? 'emerald' : 'slate', act.status)}</div>
            </div>
          </div>
          
          <div class="border-t border-slate-100 pt-5 space-y-4">
            <div class="flex items-center gap-3 text-sm text-slate-600">
              <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('calendar', 'w-4 h-4')}</div>
              <div>
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Date</div>
                <div class="font-semibold text-slate-800 mt-0.5">${act.date}</div>
              </div>
            </div>
            <div class="flex items-center gap-3 text-sm text-slate-600">
              <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('clock', 'w-4 h-4')}</div>
              <div>
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Time</div>
                <div class="font-semibold text-slate-800 mt-0.5">${act.time}</div>
              </div>
            </div>
            <div class="flex items-center gap-3 text-sm text-slate-600">
              <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('mappin', 'w-4 h-4')}</div>
              <div>
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Venue</div>
                <div class="font-semibold text-slate-800 mt-0.5">${act.venue}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end shrink-0">
          <button onclick="S.selectedCalActivity = null; render();" class="px-5 py-2 text-sm font-semibold rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition shadow-sm">Close</button>
        </div>
      </div>
    </div>
    `;
  })() : '';

  return `<div class="grid grid-cols-2 xl:grid-cols-4 gap-5">${statsHtml}</div>
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">
    <div class="xl:col-span-2 space-y-6">
      <div>
        <div class="flex items-end justify-between mb-4">
          <div><div class="text-slate-900 font-bold tracking-tight text-base">Your Sections</div><div class="text-xs text-slate-500 mt-0.5">CWTS &amp; LTS classes you handle this semester</div></div>
          <button class="text-sm text-emerald-700 inline-flex items-center gap-1">View all ${ico('arrowup', 'w-4 h-4')}</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">${I_CLASSES.map(c => classCardHtml(c, 'card')).join('')}</div>
      </div>
    </div>
    <div class="xl:col-span-1 space-y-6">
      ${calWidgetHtml}
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100"><div class="text-slate-900 font-bold tracking-tight">Submission Tracker</div><div class="text-xs text-slate-500 mt-0.5">Upcoming activity reports</div></div>
        <ul class="divide-y divide-slate-100">${subTracker}</ul>
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between">
          <div class="text-xs text-slate-500">2 due this week</div>
          <button onclick="S.instrPage = 'Accomplishment Reports'; render();" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm shadow-md shadow-emerald-200">Submit Report</button>
        </div>
      </div>
    </div>
  </div>
  ${modalHtml}`;
}

function iClasses() {
  const activeFilter = S.classesFilter || 'all';
  const filteredClasses = I_CLASSES.filter(c => {
    if (activeFilter !== 'all' && !c.code.startsWith(activeFilter)) return false;
    if (S.classesSearch) {
      const q = S.classesSearch.toLowerCase();
      return c.code.toLowerCase().includes(q) || c.schedule.toLowerCase().includes(q) || c.instructor.toLowerCase().includes(q);
    }
    return true;
  });

  const activeClassesLabel = { all: 'All Classes', CWTS: 'CWTS Only', LTS: 'LTS Only' }[activeFilter];
  const filterBtnHtml = `
    <div class="flex flex-col gap-1.5 w-48 text-left font-medium">
      <div class="relative">
        <button id="classesFilterBtn" type="button"
               class="w-full pl-3 pr-8 py-2 text-sm font-medium rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus:outline-none focus:border-emerald-300 transition-colors shadow-sm flex items-center justify-between cursor-pointer">
          <span>${activeClassesLabel}</span>
          <span class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
            ${ico('chevron', 'w-3.5 h-3.5')}
          </span>
        </button>
        ${S.showClassesFilterMenu ? `
        <div id="classesFilterDropdown" class="absolute right-0 mt-1.5 w-48 rounded-xl bg-white border border-slate-200 shadow-lg py-1.5 z-50">
          <div class="px-3 py-1 text-[10px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-50 mb-1">Filter by Program</div>
          ${[
        { val: 'all', label: 'All Classes' },
        { val: 'CWTS', label: 'CWTS Only' },
        { val: 'LTS', label: 'LTS Only' }
      ].map(opt => {
        const isSel = activeFilter === opt.val;
        return `
            <button data-classes-filter-opt="${opt.val}" class="w-full text-left px-3.5 py-1.5 text-sm hover:bg-slate-50 transition-colors flex items-center justify-between ${isSel ? 'text-emerald-600 font-semibold bg-emerald-50/40' : 'text-slate-700'}">
              <span>${opt.label}</span>
              ${isSel ? ico('check', 'w-4 h-4 text-emerald-600') : ''}
            </button>
            `;
      }).join('')}
        </div>
        ` : ''}
      </div>
      <div class="relative">
        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
          ${ico('search', 'w-3.5 h-3.5 text-slate-400')}
        </span>
        <input id="classesSearchInput" type="text" autocomplete="off"
               class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white text-slate-700 placeholder-slate-400 focus:outline-none focus:border-emerald-300 transition-colors shadow-sm"
               placeholder="Search classes..."
               value="${S.classesSearch || ''}" />
      </div>
    </div>
  `;

  return `<div class="space-y-5">
    ${pageHdr('My Classes Overview', 'All sections you handle this semester', `
      <div class="flex items-center gap-2">
        ${filterBtnHtml}
        <button class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">${ico('calendar', 'w-4 h-4')} Take Attendance</button>
      </div>`)}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">${filteredClasses.map(c => classCardHtml(c, 'full')).join('')}</div>
  </div>`;
}

const I_PLANS = [
  { title: 'Tree-Planting Drive', section: 'CWTS 1-A', date: 'May 21, 2026', duration: '3 hrs', venue: 'Brgy. San Roque Riverside', status: 'Approved', desc: 'Students will plant 50 mahogany saplings along the riverside to help prevent soil erosion and promote community-based environmental awareness. All tools will be provided by the local barangay.', file: 'Tree-Planting-Guidelines.pdf' },
  { title: 'Adult Literacy — Module 4', section: 'LTS 2-A', date: 'May 24, 2026', duration: '2 hrs', venue: 'Aurora Community Hall', status: 'Pending', desc: 'Continuation of the adult literacy program focusing on basic reading comprehension and writing skills. Instructors will conduct one-on-one reading sessions.', file: 'Module4-ReadingMaterials.pdf' },
  { title: 'Coastal Clean-up', section: 'CWTS 1-C', date: 'May 28, 2026', duration: '4 hrs', venue: 'Manila Bay Promenade', status: 'Revision', desc: 'A massive coastal clean-up drive in partnership with the local DENR office. Cadets are required to bring their own gloves and trash bags. Please revise to include safety protocols.', file: 'Clean-up-Waiver.docx', feedback: 'Please update the activity plan to include a clear risk assessment and emergency contact protocols. Also, ensure the local LGU permit is attached before resubmitting.' },
  { title: 'Reading Buddies — Session 2', section: 'LTS 2-B', date: 'Jun 4, 2026', duration: '2 hrs', venue: 'Aurora Elementary', status: 'Approved', desc: 'Interactive storytelling session with grade 3 students at Aurora Elementary School. Cadets will prepare their own visual aids.', file: 'Story-List.pdf' },
];

function iPlans() {
  const planTbl = `
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-100">
                <th class="py-2 px-3 font-medium">Activity</th>
                <th class="py-2 px-3 font-medium">Section</th>
                <th class="py-2 px-3 font-medium">Date</th>
                <th class="py-2 px-3 font-medium">Venue</th>
                <th class="py-2 px-3 font-medium w-[120px]">Status</th>
              </tr>
            </thead>
            <tbody>
              ${I_PLANS.map((r, i) => `
                <tr class="border-b border-slate-50 hover:bg-slate-50 cursor-pointer" onclick="S.selectedPlanIndex = ${i}; render();">
                  <td class="py-3 px-3 text-slate-900 font-medium">${r.title}</td>
                  <td class="py-3 px-3 text-slate-700">${r.section}</td>
                  <td class="py-3 px-3 text-slate-700">${r.date}</td>
                  <td class="py-3 px-3 text-slate-700">${r.venue}</td>
                  <td class="py-3 px-3 text-slate-700">${pill(r.status === 'Approved' ? 'emerald' : r.status === 'Pending' ? 'indigo' : r.status === 'Revision' ? 'amber' : 'slate', r.status)}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      `;
  const classOpts = I_CLASSES.map(c => `<option>${c.code} · ${c.sec}</option>`).join('');
  return `<div class="space-y-5">
    ${pageHdr('Create New Activity Plans', 'Plan, schedule, and submit activities for coordinator approval', `<button class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">${ico('plus', 'w-4 h-4')} New Activity Plan</button>`)}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
      ${card(planTbl, { title: 'Upcoming & Drafts', cls: 'lg:col-span-2' })}
      ${card(`<div class="space-y-3 text-sm">
        <div><div class="text-xs text-slate-500 mb-1">Title</div><input id="planTitleInput" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-emerald-300" placeholder="e.g. Tree-Planting Drive" /></div>
        <div class="grid grid-cols-2 gap-2">
          <div><div class="text-xs text-slate-500 mb-1">Date</div><input id="planDateInput" type="date" class="w-full px-3 py-2 rounded-lg border border-slate-200" /></div>
          <div><div class="text-xs text-slate-500 mb-1">Duration (hrs)</div><input id="planDurationInput" type="number" value="3" class="w-full px-3 py-2 rounded-lg border border-slate-200" /></div>
        </div>
        <div><div class="text-xs text-slate-500 mb-1">Section</div><select id="planSectionInput" class="w-full px-3 py-2 rounded-lg border border-slate-200">${classOpts}</select></div>
        <div><div class="text-xs text-slate-500 mb-1">Objectives</div><textarea id="planObjInput" rows="3" placeholder="State 2–3 measurable objectives…" class="w-full px-3 py-2 rounded-lg border border-slate-200"></textarea></div>
        <div>
          <div class="text-xs text-slate-500 mb-1">Attachments</div>
          <label class="flex items-center justify-center gap-2 w-full px-3 py-2 rounded-lg border border-dashed border-emerald-300 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 cursor-pointer transition text-sm">
            ${ico('upload', 'w-4 h-4')} <span id="planFileLabel">Add File</span>
            <input type="file" id="planFileInput" class="hidden" multiple />
          </label>
        </div>
        <div class="flex items-center gap-2 pt-1">
          <button id="planSaveDraftBtn" class="px-3 py-2 text-sm rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">Save Draft</button>
          <button id="planSubmitBtn" class="px-3 py-2 text-sm rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">Submit for Approval</button>
        </div>
      </div>`, { title: 'Plan Template' })}
    </div>
    ${S.selectedPlanIndex !== null ? (() => {
      const p = I_PLANS[S.selectedPlanIndex];
      return `
    <div id="planDetailModalOverlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
          <h3 class="font-semibold text-slate-800 text-lg">Activity Details</h3>
          <button onclick="S.selectedPlanIndex = null; render();" class="p-2 -mr-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-full transition-colors">${ico('close', 'w-5 h-5')}</button>
        </div>
        <div class="p-6 overflow-y-auto">
          <div class="flex items-start justify-between mb-6">
            <div>
              <h2 class="text-xl font-bold text-slate-900">${p.title}</h2>
              <div class="text-sm text-slate-500 mt-1">${p.section}</div>
            </div>
            ${pill(p.status === 'Approved' ? 'emerald' : p.status === 'Pending' ? 'indigo' : p.status === 'Revision' ? 'amber' : 'slate', p.status)}
          </div>
          
          ${p.status === 'Revision' && p.feedback ? `
          <div class="mb-6 bg-amber-50 rounded-lg p-4 border border-amber-200 shadow-sm relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-amber-500"></div>
            <div class="flex items-start gap-3 pl-2">
              <div class="text-amber-600 mt-0.5">${ico('alertc', 'w-5 h-5')}</div>
              <div>
                <h4 class="text-sm font-bold text-amber-900 mb-1 tracking-tight">Coordinator Feedback</h4>
                <p class="text-sm text-amber-800 leading-relaxed font-medium">${p.feedback}</p>
              </div>
            </div>
          </div>
          ` : ''}

          <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-slate-50 rounded-lg p-3">
              <div class="text-[11px] uppercase tracking-wider text-slate-500 mb-1">Date</div>
              <div class="text-sm font-medium text-slate-900">${p.date}</div>
            </div>
            <div class="bg-slate-50 rounded-lg p-3">
              <div class="text-[11px] uppercase tracking-wider text-slate-500 mb-1">Duration</div>
              <div class="text-sm font-medium text-slate-900">${p.duration}</div>
            </div>
            <div class="bg-slate-50 rounded-lg p-3 col-span-2">
              <div class="text-[11px] uppercase tracking-wider text-slate-500 mb-1">Venue</div>
              <div class="text-sm font-medium text-slate-900">${p.venue}</div>
            </div>
          </div>

          <div class="mb-6">
            <h4 class="text-sm font-medium text-slate-900 mb-2">Description</h4>
            <p class="text-sm text-slate-600 leading-relaxed">${p.desc || 'No description provided.'}</p>
          </div>

          ${p.file ? `
          <div>
            <h4 class="text-sm font-medium text-slate-900 mb-2">Attachments</h4>
            <div class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg">
              <div class="w-10 h-10 rounded bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                ${ico('filecheck', 'w-5 h-5')}
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-slate-900 truncate">${p.file}</div>
                <div class="text-xs text-slate-500">Document file</div>
              </div>
              <button class="px-3 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-50 rounded-md transition-colors">Download</button>
            </div>
          </div>
          ` : ''}
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 shrink-0">
          ${p.status === 'Revision' ? `
            <button onclick="
              S.editingPlanIndex = ${S.selectedPlanIndex};
              S.selectedPlanIndex = null;
              
              setTimeout(() => {
                const plan = I_PLANS[S.editingPlanIndex];
                const titleInput = document.getElementById('planTitleInput');
                if (titleInput) { titleInput.value = plan.title; titleInput.focus(); }
                const dInput = document.getElementById('planDateInput');
                if (dInput && plan.date) {
                  const d = new Date(plan.date);
                  if (!isNaN(d)) dInput.value = d.toISOString().split('T')[0];
                }
                const durInput = document.getElementById('planDurationInput');
                if (durInput) durInput.value = parseInt(plan.duration) || 3;
                const secInput = document.getElementById('planSectionInput');
                if (secInput) {
                  for(let i=0; i<secInput.options.length; i++) {
                    if(secInput.options[i].text.includes(plan.section)) { secInput.selectedIndex = i; break; }
                  }
                }
                const objInput = document.getElementById('planObjInput');
                if (objInput) objInput.value = plan.desc || '';
                const submitBtn = document.getElementById('planSubmitBtn');
                if (submitBtn) submitBtn.innerHTML = 'Update Activity';
              }, 50);
              render();
            " class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors flex items-center gap-2 shadow-sm">
              ${ico('edit', 'w-4 h-4')} Revise Activity
            </button>
          ` : ''}
          <button onclick="
            I_PLANS.splice(${S.selectedPlanIndex}, 1);
            S.selectedPlanIndex = null;
            render();
          " class="px-4 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-lg transition-colors flex items-center gap-2">
            ${ico('close', 'w-4 h-4')} Delete
          </button>
        </div>
      </div>
    </div>
    `
    })() : ''}
  </div>`;
}

function iReports() {
  const listHtml = I_REPORTS.map((r, i) => {
    const sm = statusMeta(r.status); return `<li class="px-5 py-4 flex items-center gap-3 hover:bg-slate-50 cursor-pointer" onclick="S.selectedReportIndex = ${i}; render();">
    <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">${ico('filetext', 'w-4 h-4')}</div>
    <div class="flex-1 min-w-0"><div class="text-sm text-slate-900 truncate">${r.title}</div><div class="text-xs text-slate-500">${r.cls} · ${r.due}</div></div>
    ${pill(sm.pill, `<span class="inline-flex items-center gap-1">${ico(sm.ico, 'w-3 h-3')}${r.status}</span>`)}
    ${ico('chevron', 'w-4 h-4 text-slate-300')}
  </li>`;
  }).join('');
  const planOpts = I_PLANS.map(p => `<option value="${p.title}">${p.title}</option>`).join('');
  return `<div class="space-y-5">
    <datalist id="activityDatalist">${planOpts}</datalist>
    ${pageHdr('Submit Accomplishment Reports', 'Document and submit completed activities')}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
      ${card(`<ul class="-mx-5 -my-5 divide-y divide-slate-100">${listHtml}</ul>`, { title: 'All Reports', cls: 'lg:col-span-2' })}
      ${card(`<div class="space-y-3 text-sm">
        <div><div class="text-xs text-slate-500 mb-1">Linked Activity</div><input type="text" id="reportLinkedActivity" list="activityDatalist" placeholder="Select or type an activity..." class="w-full px-3 py-2 rounded-lg border border-slate-200" /></div>
        <div><div class="text-xs text-slate-500 mb-1">Beneficiaries</div><input type="text" inputmode="numeric" pattern="[0-9]*" id="reportBenInput" value="42" class="w-full px-3 py-2 rounded-lg border border-slate-200" /></div>
        <div><div class="text-xs text-slate-500 mb-1">Narrative</div><textarea id="reportNarInput" rows="4" placeholder="Describe the activity, outputs, and impact…" class="w-full px-3 py-2 rounded-lg border border-slate-200"></textarea></div>
        <label class="block border-2 border-dashed border-emerald-200 rounded-lg p-4 text-center bg-emerald-50/50 cursor-pointer hover:bg-emerald-50">
          ${ico('upload', 'w-5 h-5 text-emerald-600 mx-auto')}
          <span id="reportFileLabel" class="text-xs text-slate-600 mt-1 block">Drop photos, attendance sheet, feedback forms</span>
          <input type="file" id="reportFileInput" class="hidden" multiple onchange="document.getElementById('reportFileLabel').innerText = this.files.length + ' file(s) attached'" />
        </label>
        <div class="flex items-center gap-2 pt-1">
          <button onclick="
            const t = document.getElementById('reportLinkedActivity').value;
            const b = document.getElementById('reportBenInput').value;
            const n = document.getElementById('reportNarInput').value;
            const r = { title: t + (t.includes('Report') ? '' : ' Report'), cls: 'CWTS 1 · Sec A', due: 'Saved just now', status: 'Draft', beneficiaries: parseInt(b)||0, narrative: n };
            if (S.editingReportIndex !== null) I_REPORTS[S.editingReportIndex] = { ...I_REPORTS[S.editingReportIndex], ...r };
            else I_REPORTS.unshift(r);
            S.editingReportIndex = null;
            render();
          " class="px-3 py-2 text-sm rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">Save Draft</button>
          <button onclick="
            const t = document.getElementById('reportLinkedActivity').value;
            const b = document.getElementById('reportBenInput').value;
            const n = document.getElementById('reportNarInput').value;
            const r = { title: t + (t.includes('Report') ? '' : ' Report'), cls: 'CWTS 1 · Sec A', due: 'Submitted just now', status: 'Submitted', beneficiaries: parseInt(b)||0, narrative: n };
            if (S.editingReportIndex !== null) I_REPORTS[S.editingReportIndex] = { ...I_REPORTS[S.editingReportIndex], ...r };
            else I_REPORTS.unshift(r);
            S.editingReportIndex = null;
            render();
          " class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">${ico('send', 'w-4 h-4')} Submit</button>
        </div>
      </div>`, { title: 'New Report Draft' })}
    </div>
    ${S.selectedReportIndex !== null ? (() => {
      const r = I_REPORTS[S.selectedReportIndex];
      return `
    <div id="reportDetailModalOverlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
          <h3 class="font-semibold text-slate-800 text-lg">Report Details</h3>
          <button onclick="S.selectedReportIndex = null; render();" class="p-2 -mr-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-full transition-colors">${ico('close', 'w-5 h-5')}</button>
        </div>
        <div class="p-6 overflow-y-auto">
          <div class="flex items-start justify-between mb-6">
            <div>
              <h2 class="text-xl font-bold text-slate-900">${r.title}</h2>
              <div class="text-sm text-slate-500 mt-1">${r.cls} · ${r.due}</div>
            </div>
            ${pill(statusMeta(r.status).pill, r.status)}
          </div>
          
          ${r.status === 'Revisions' && r.feedback ? `
          <div class="mb-6 bg-amber-50 rounded-lg p-4 border border-amber-200 shadow-sm relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-amber-500"></div>
            <div class="flex items-start gap-3 pl-2">
              <div class="text-amber-600 mt-0.5">${ico('alertc', 'w-5 h-5')}</div>
              <div>
                <h4 class="text-sm font-bold text-amber-900 mb-1 tracking-tight">Coordinator Feedback</h4>
                <p class="text-sm text-amber-800 leading-relaxed font-medium">${r.feedback}</p>
              </div>
            </div>
          </div>
          ` : ''}

          <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-slate-50 rounded-lg p-3">
              <div class="text-[11px] uppercase tracking-wider text-slate-500 mb-1">Beneficiaries</div>
              <div class="text-sm font-medium text-slate-900">${r.beneficiaries || 0}</div>
            </div>
          </div>

          <div class="mb-6">
            <h4 class="text-sm font-medium text-slate-900 mb-2">Narrative</h4>
            <p class="text-sm text-slate-600 leading-relaxed">${r.narrative || 'No narrative provided.'}</p>
          </div>

          ${r.file ? `
          <div>
            <h4 class="text-sm font-medium text-slate-900 mb-2">Attachments</h4>
            <div class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg">
              <div class="w-10 h-10 rounded bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                ${ico('filecheck', 'w-5 h-5')}
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-slate-900 truncate">${r.file}</div>
                <div class="text-xs text-slate-500">Document file</div>
              </div>
              <button class="px-3 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-50 rounded-md transition-colors">Download</button>
            </div>
          </div>
          ` : ''}
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 shrink-0">
          ${(r.status === 'Draft' || r.status === 'Revisions') ? `
            <button onclick="
              S.editingReportIndex = ${S.selectedReportIndex};
              S.selectedReportIndex = null;
              setTimeout(() => {
                const rep = I_REPORTS[S.editingReportIndex];
                const linkInput = document.getElementById('reportLinkedActivity');
                if (linkInput) {
                  let t = rep.title || '';
                  if (t.endsWith(' Report')) t = t.slice(0, -7);
                  linkInput.value = t;
                }
                const benInput = document.getElementById('reportBenInput');
                if (benInput) benInput.value = rep.beneficiaries || '';
                const narInput = document.getElementById('reportNarInput');
                if (narInput) { narInput.value = rep.narrative || ''; narInput.focus(); }
              }, 50);
              render();
            " class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors flex items-center gap-2 shadow-sm">
              ${ico('edit', 'w-4 h-4')} Edit Report
            </button>
          ` : ''}
          <button onclick="
            I_REPORTS.splice(${S.selectedReportIndex}, 1);
            S.selectedReportIndex = null;
            render();
          " class="px-4 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-lg transition-colors flex items-center gap-2">
            ${ico('close', 'w-4 h-4')} Delete
          </button>
        </div>
      </div>
    </div>
    `
    })() : ''}
  </div>`;
}

const I_ANNOUNCEMENTS = [
  { author: 'NSTP Office', initials: 'NO', color: 'from-indigo-500 to-blue-500', title: 'Mid-semester accomplishment reports due May 22', body: 'All instructors must submit consolidated accomplishment reports by 11:59 PM.', time: '2h ago', pinned: true },
  { author: "Dean's Office", initials: 'DO', color: 'from-emerald-500 to-teal-500', title: 'Field activity safety briefing â€” mandatory', body: 'Briefing scheduled Friday, May 16, 3:00 PM at the AVR.', time: 'Yesterday', pinned: false },
  { author: 'Program Coordinator', initials: 'PC', color: 'from-violet-500 to-fuchsia-500', title: 'Updated rubric for accomplishment reports', body: 'Rubric v3.2 is now in effect.', time: 'May 9', pinned: false },
  { author: 'Registrar', initials: 'RG', color: 'from-amber-500 to-orange-500', title: 'Grade encoding window opens May 25', body: 'OCR-assisted grade upload available end of month.', time: 'May 8', pinned: false },
];

function iAnnouncements() {
  const items = I_ANNOUNCEMENTS.map(a => `<li class="flex gap-4">
    <div class="w-10 h-10 shrink-0 rounded-full bg-gradient-to-br ${a.color} flex items-center justify-center text-white text-xs tracking-tight">${a.initials}</div>
    <div class="flex-1 min-w-0">
      <div class="flex items-center gap-2 text-xs text-slate-500">
        <span class="text-slate-700">${a.author}</span><span class="w-1 h-1 rounded-full bg-slate-300 inline-block"></span><span>${a.time}</span>
        ${a.pinned ? `<span class="ml-auto inline-flex items-center gap-1 text-[10px] uppercase tracking-wider text-emerald-700">${ico('pin', 'w-3 h-3')} Pinned</span>` : ''}
      </div>
      <div class="text-sm text-slate-900 mt-1">${a.title}</div>
      <p class="text-sm text-slate-600 mt-1 leading-relaxed">${a.body}</p>
    </div>
  </li>`).join('');
  return `<div class="space-y-5">
    ${pageHdr('Announcements Overview', 'Official updates from the NSTP & Dean\'s offices', `<div class="relative">${ico('search', 'w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2')}<input placeholder="Search announcementsâ€¦" class="pl-9 pr-3 py-2 text-sm rounded-lg bg-white border border-slate-200 w-64" /></div>`)}
    ${card(`<ul class="space-y-5">${items}</ul>`)}
  </div>`;
}

function iCalendar() {
  const calDays = [
    { d: 14, label: 'TUE', events: [{ t: 'Faculty Council Meeting', c: 'bg-indigo-500' }] },
    { d: 15, label: 'WED', events: [] },
    { d: 16, label: 'THU', events: [{ t: 'Midterm Reports Due', c: 'bg-rose-500' }] },
    { d: 17, label: 'FRI', events: [] },
    { d: 18, label: 'SAT', events: [] },
    { d: 19, label: 'SUN', events: [] },
    { d: 20, label: 'MON', events: [{ t: 'OCR Upload Window', c: 'bg-emerald-500' }] },
  ];

  S.activities.forEach(act => {
    const d = new Date(act.date);
    if (!isNaN(d) && d.getMonth() === 4) {
      const dateVal = d.getDate();
      const dayMatch = calDays.find(cd => cd.d === dateVal);
      if (dayMatch) {
        if (!dayMatch.events.some(e => e.t === act.title)) {
          dayMatch.events.push({ t: act.title, c: act.color || 'bg-emerald-500' });
        }
      }
    }
  });

  const calGrid = calDays.map(d => `<div class="border border-slate-200 rounded-2xl p-4 min-h-[140px] bg-white shadow-sm hover:border-emerald-300 hover:shadow-md transition duration-200">
    <div class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold">${d.label}</div>
    <div class="text-slate-900 tracking-tight text-2xl mt-1 font-bold">${d.d}</div>
    <div class="mt-3 space-y-1.5">${d.events.map(e => `<div class="${e.c} text-[11px] px-2.5 py-1.5 rounded-lg text-white truncate font-medium shadow-sm">${e.t}</div>`).join('')}</div>
  </div>`).join('');

  const actList = S.activities.map((a, i) => `<li onclick="S.selectedCalActivity = ${i}; render();" class="px-5 py-4 flex items-center gap-4 hover:bg-slate-50 cursor-pointer transition">
    <div class="w-1.5 self-stretch rounded-full ${a.color}"></div>
    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">${ico('calendar', 'w-5 h-5')}</div>
    <div class="flex-1 min-w-0">
      <div class="text-sm font-semibold text-slate-900 truncate">${a.title}</div>
      <div class="text-xs text-slate-500 mt-1 flex items-center gap-3 flex-wrap font-medium">
        <span class="inline-flex items-center gap-1">${ico('calendar', 'w-3.5 h-3.5 text-slate-400')}${a.date}</span>
        <span class="w-1 h-1 rounded-full bg-slate-300 inline-block"></span>
        <span class="inline-flex items-center gap-1">${ico('clock', 'w-3.5 h-3.5 text-slate-400')}${a.time}</span>
        <span class="w-1 h-1 rounded-full bg-slate-300 inline-block"></span>
        <span class="inline-flex items-center gap-1">${ico('mappin', 'w-3.5 h-3.5 text-slate-400')}${a.venue}</span>
      </div>
    </div>
    ${pill('indigo', a.scope)}
    ${pill(a.status === 'Submitted' ? 'emerald' : 'slate', a.status)}
  </li>`).join('');

  const modalHtml = S.selectedCalActivity !== null && S.selectedCalActivity !== undefined ? (() => {
    const act = S.activities[S.selectedCalActivity];
    if (!act) return '';
    return `
    <div id="calDetailOverlay" onclick="if(event.target === this) { S.selectedCalActivity = null; render(); }" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col animate-scaleUp">
        <style>
          @keyframes scaleUp {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
          }
          .animate-scaleUp {
            animation: scaleUp 0.15s cubic-bezier(0.16, 1, 0.3, 1) forwards;
          }
        </style>
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 shrink-0">
          <h3 class="font-bold text-slate-800 text-lg">Activity Details</h3>
          <button onclick="S.selectedCalActivity = null; render();" class="p-2 -mr-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-full transition-colors">${ico('close', 'w-5 h-5')}</button>
        </div>
        <div class="p-6 space-y-5 overflow-y-auto">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-emerald-100 shrink-0">
              ${ico('calendar', 'w-6 h-6')}
            </div>
            <div>
              <h4 class="text-lg font-bold text-slate-900 leading-snug">${act.title}</h4>
              <div class="mt-1.5 flex gap-2">${pill('indigo', act.scope)}${pill(act.status === 'Submitted' ? 'emerald' : 'slate', act.status)}</div>
            </div>
          </div>
          
          <div class="border-t border-slate-100 pt-5 space-y-4">
            <div class="flex items-center gap-3 text-sm text-slate-600">
              <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('calendar', 'w-4 h-4')}</div>
              <div>
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Date</div>
                <div class="font-semibold text-slate-800 mt-0.5">${act.date}</div>
              </div>
            </div>
            <div class="flex items-center gap-3 text-sm text-slate-600">
              <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('clock', 'w-4 h-4')}</div>
              <div>
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Time</div>
                <div class="font-semibold text-slate-800 mt-0.5">${act.time}</div>
              </div>
            </div>
            <div class="flex items-center gap-3 text-sm text-slate-600">
              <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('mappin', 'w-4 h-4')}</div>
              <div>
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Venue</div>
                <div class="font-semibold text-slate-800 mt-0.5">${act.venue}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end shrink-0">
          <button onclick="S.selectedCalActivity = null; render();" class="px-5 py-2 text-sm font-semibold rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition shadow-sm">Close</button>
        </div>
      </div>
    </div>
    `;
  })() : '';

  return `<div class="space-y-5">
    ${pageHdr('Activity Calendar', 'Plan and view official activities for CWTS & LTS')}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">${calGrid}</div>
    ${card(`<ul class="-mx-6 -my-5 divide-y divide-slate-100">${actList}</ul>`, { title: 'All Scheduled Activities', subtitle: `${S.activities.length} published events` })}
    ${modalHtml}
  </div>`;
}

/* ================================================================
   ROTC PAGES
================================================================ */
const R_REPORTS = [
  { title: 'Q1 Tactical Drill Accomplishment', status: 'draft', progress: 60, due: 'Due May 18' },
  { title: 'Civil-Military Operations Report', status: 'review', progress: 100, due: 'Submitted May 8' },
  { title: 'Community Outreach â€” Brgy. San Roque', status: 'approved', progress: 100, due: 'Approved May 4' },
  { title: 'Monthly Strength Report â€” May', status: 'revisions', progress: 75, due: 'Revisions requested' },
];
const R_BULLETIN = [
  { tag: 'ORDER', title: 'General Order 2026-14 â€” Drill Day moved to 0700H', time: 'Today', color: 'bg-rose-50 text-rose-700', pinned: true },
  { tag: 'SCHEDULE', title: 'Tactical inspection â€” May 23 at the parade grounds', time: '2d ago', color: 'bg-indigo-50 text-indigo-700', pinned: false },
  { tag: 'MEMO', title: 'Submit Q1 accomplishment reports by May 18, 2359H', time: 'May 9', color: 'bg-amber-50 text-amber-700', pinned: false },
  { tag: 'NOTICE', title: 'New cadets to be integrated into Alpha &amp; Bravo platoons', time: 'May 8', color: 'bg-emerald-50 text-emerald-700', pinned: false },
];

function rStatusMeta(s) {
  return { draft: { label: 'Draft', pill: 'slate', ico: 'pencil' }, review: { label: 'Under Review', pill: 'indigo', ico: 'send' }, approved: { label: 'Approved', pill: 'emerald', ico: 'check2' }, revisions: { label: 'Revisions', pill: 'rose', ico: 'alertc' } }[s] || { label: 'Draft', pill: 'slate', ico: 'pencil' };
}

function bulletinCard() {
  const items = R_BULLETIN.map(b => `<li class="px-6 py-4">
    <div class="flex items-center gap-2 mb-1">
      <span class="text-[10px] uppercase tracking-wider px-1.5 py-0.5 rounded ${b.color}">${b.tag}</span>
      ${b.pinned ? `<span class="inline-flex items-center gap-1 text-[10px] uppercase tracking-wider text-slate-500">${ico('pin', 'w-3 h-3')} Pinned</span>` : ''}
      <span class="ml-auto text-[11px] text-slate-400">${b.time}</span>
    </div>
    <div class="text-sm text-slate-900 leading-snug">${b.title}</div>
  </li>`).join('');
  return `<section class="bg-white border border-slate-200 rounded-md">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center gap-3">
      <div class="w-9 h-9 rounded bg-slate-900 text-amber-300 flex items-center justify-center">${ico('megaphone', 'w-4 h-4')}</div>
      <div><div class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Bulletin Board</div><div class="text-slate-900 tracking-tight">Official Notices</div></div>
    </div>
    <ul class="divide-y divide-slate-100">${items}</ul>
    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
      <div class="text-[11px] uppercase tracking-[0.18em] text-slate-500 mb-2">Next Formation</div>
      <div class="flex items-center gap-3 text-sm text-slate-700">
        <div class="flex items-center gap-1.5">${ico('clock', 'w-3.5 h-3.5 text-slate-400')} 0700H</div>
        <span class="w-1 h-1 rounded-full bg-slate-300 inline-block"></span>
        <div class="flex items-center gap-1.5">${ico('mappin', 'w-3.5 h-3.5 text-slate-400')} Parade Grounds</div>
      </div>
    </div>
  </section>`;
}

function rOverview() {
  const statBoxes = [
    { label: 'Total Cadets', value: '148', sub: '+12 this intake' },
    { label: 'Active Platoons', value: '3', sub: 'Alpha Â· Bravo Â· Charlie' },
    { label: 'Unassigned', value: '6', sub: 'Awaiting assignment' },
    { label: 'Reports Open', value: '4', sub: '2 due this week' },
  ].map(s => `<div class="bg-white border border-slate-200 rounded-md p-5">
    <div class="text-[11px] uppercase tracking-[0.18em] text-slate-500">${s.label}</div>
    <div class="text-slate-900 tracking-tight text-3xl mt-2">${s.value}</div>
    <div class="text-xs text-slate-500 mt-1">${s.sub}</div>
  </div>`).join('');
  const repItems = R_REPORTS.map(r => {
    const sm = rStatusMeta(r.status); const barClr = r.status === 'approved' ? 'bg-emerald-500' : r.status === 'revisions' ? 'bg-rose-500' : r.status === 'review' ? 'bg-indigo-500' : 'bg-slate-400'; return `<div class="px-6 py-4 hover:bg-slate-50">
    <div class="flex items-center justify-between gap-3">
      <div class="flex items-center gap-3 min-w-0">
        <div class="w-9 h-9 rounded bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">${ico('filetext', 'w-4 h-4')}</div>
        <div class="min-w-0"><div class="text-sm text-slate-900 truncate">${r.title}</div><div class="text-xs text-slate-500">${r.due}</div></div>
      </div>
      ${pill(sm.pill, `<span class="inline-flex items-center gap-1">${ico(sm.ico, 'w-3 h-3')}${sm.label}</span>`)}
    </div>
    <div class="mt-3 flex items-center gap-3">
      <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden"><div class="h-full rounded-full ${barClr}" style="width:${r.progress}%"></div></div>
      <div class="text-[11px] text-slate-500 w-10 text-right tabular-nums">${r.progress}%</div>
    </div>
  </div>`;
  }).join('');

  const now = new Date();
  const year = now.getFullYear();
  const month = now.getMonth();
  const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  const dayNames = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const today = now.getDate();

  let cells = '';
  for (let i = 0; i < firstDay; i++) cells += '<div></div>';
  for (let d = 1; d <= daysInMonth; d++) {
    const isToday = d === today;
    const actIdx = S.activities.findIndex(a => {
      try {
        const dateObj = new Date(a.date);
        return dateObj.getFullYear() === year && dateObj.getMonth() === month && dateObj.getDate() === d;
      } catch (e) { return false; }
    });
    const hasEvent = actIdx !== -1;

    let cls = '';
    let clickAttr = '';
    if (isToday) {
      cls = 'w-7 h-7 flex items-center justify-center text-xs rounded-full bg-indigo-600 text-white font-semibold cursor-pointer';
      if (hasEvent) {
        clickAttr = `onclick="S.selectedCalActivity = ${actIdx}; render();"`;
      }
    } else if (hasEvent) {
      cls = 'w-7 h-7 flex items-center justify-center text-xs rounded-full bg-indigo-50 text-indigo-700 font-semibold ring-1 ring-indigo-200 cursor-pointer hover:bg-indigo-100 transition';
      clickAttr = `onclick="S.selectedCalActivity = ${actIdx}; render();"`;
    } else {
      cls = 'w-7 h-7 flex items-center justify-center text-xs rounded-full text-slate-600';
    }
    cells += `<div ${clickAttr} class="${cls}">${d}</div>`;
  }

  const calWidgetHtml = `
  <div class="bg-white border border-slate-200 rounded-md p-5 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div>
        <div class="text-slate-900 font-bold tracking-tight text-sm">Calendar of Activities</div>
        <div class="text-xs text-slate-500 mt-0.5">${monthNames[month]} ${year}</div>
      </div>
      <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center">${ico('calendar', 'w-4 h-4')}</div>
    </div>
    <div class="grid grid-cols-7 gap-1 mb-1">${dayNames.map(d => `<div class="text-center text-[10px] font-medium text-slate-400 uppercase">${d}</div>`).join('')}</div>
    <div class="grid grid-cols-7 gap-1">${cells}</div>
    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-4 text-xs text-slate-500">
      <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-600 inline-block"></span>Today</span>
      <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-50 ring-1 ring-indigo-200 inline-block font-semibold"></span>Has activity</span>
    </div>
  </div>
  `;

  const modalHtml = (S.selectedCalActivity !== null && S.selectedCalActivity !== undefined) ? (() => {
    const act = S.activities[S.selectedCalActivity];
    if (!act) return '';
    return `
    <div id="calDetailOverlay" onclick="if(event.target === this) { S.selectedCalActivity = null; render(); }" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col animate-scaleUp">
        <style>
          @keyframes scaleUp {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
          }
          .animate-scaleUp {
            animation: scaleUp 0.15s cubic-bezier(0.16, 1, 0.3, 1) forwards;
          }
        </style>
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 shrink-0">
          <h3 class="font-bold text-slate-800 text-lg">Activity Details</h3>
          <button onclick="S.selectedCalActivity = null; render();" class="p-2 -mr-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-full transition-colors">${ico('close', 'w-5 h-5')}</button>
        </div>
        <div class="p-6 space-y-5 overflow-y-auto">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-emerald-100 shrink-0">
              ${ico('calendar', 'w-6 h-6')}
            </div>
            <div>
              <h4 class="text-lg font-bold text-slate-900 leading-snug">${act.title}</h4>
              <div class="mt-1.5 flex gap-2">${pill('indigo', act.scope)}${pill(act.status === 'Submitted' ? 'emerald' : 'slate', act.status)}</div>
            </div>
          </div>
          
          <div class="border-t border-slate-100 pt-5 space-y-4">
            <div class="flex items-center gap-3 text-sm text-slate-600">
              <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('calendar', 'w-4 h-4')}</div>
              <div>
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Date</div>
                <div class="font-semibold text-slate-800 mt-0.5">${act.date}</div>
              </div>
            </div>
            <div class="flex items-center gap-3 text-sm text-slate-600">
              <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('clock', 'w-4 h-4')}</div>
              <div>
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Time</div>
                <div class="font-semibold text-slate-800 mt-0.5">${act.time}</div>
              </div>
            </div>
            <div class="flex items-center gap-3 text-sm text-slate-600">
              <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">${ico('mappin', 'w-4 h-4')}</div>
              <div>
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Venue</div>
                <div class="font-semibold text-slate-800 mt-0.5">${act.venue}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end shrink-0">
          <button onclick="S.selectedCalActivity = null; render();" class="px-5 py-2 text-sm font-semibold rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition shadow-sm">Close</button>
        </div>
      </div>
    </div>
    `;
  })() : '';

  return `<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">${statBoxes}</div>
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">
    <section class="xl:col-span-2 bg-white border border-slate-200 rounded-md">
      <div class="px-6 py-4 border-b border-slate-200"><div class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Documentation</div><div class="text-slate-900 tracking-tight">Accomplishment Reports</div></div>
      <div class="divide-y divide-slate-100">${repItems}</div>
      <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between bg-slate-50/50">
        <div class="text-xs text-slate-500">2 reports require action this week</div>
        <button onclick="S.rotcPage = 'Report Submission'; render();" class="inline-flex items-center gap-2 px-4 py-1.5 text-xs uppercase tracking-wider rounded bg-amber-400 text-slate-900 hover:bg-amber-300">${ico('send', 'w-3.5 h-3.5')} Submit Report</button>
      </div>
    </section>
    <div class="xl:col-span-1 space-y-6">
      ${calWidgetHtml}
    </div>
  </div>
  ${modalHtml}`;
}

const SPEC_COLORS = { Rifle: 'bg-rose-50 text-rose-700 border-rose-100', Signal: 'bg-indigo-50 text-indigo-700 border-indigo-100', Medical: 'bg-emerald-50 text-emerald-700 border-emerald-100', Support: 'bg-amber-50 text-amber-700 border-amber-100' };
const SPEC_DOT = { Rifle: 'bg-rose-500', Signal: 'bg-indigo-500', Medical: 'bg-emerald-500', Support: 'bg-amber-500' };

function rPlatoon() {
  let entries = Object.entries(S.platoons);
  if (S.platSearch) {
    const term = S.platSearch.toLowerCase();
    entries = entries.filter(([name]) => name.toLowerCase().includes(term));
  }
  if (S.platoonFilter === '1st') {
    entries = entries.filter(([name]) => (S.platoonSemesters[name] || '1st Semester') === '1st Semester');
  } else if (S.platoonFilter === '2nd') {
    entries = entries.filter(([name]) => (S.platoonSemesters[name] || '1st Semester') === '2nd Semester');
  }

  const thead = `<thead><tr class="text-left text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-100">
        <th class="py-2 px-3 font-medium">Platoon Name</th>
        <th class="py-2 px-3 font-medium">Total Officers</th>
        <th class="py-2 px-3 font-medium">Status</th>
      </tr></thead>`;

  const tbody = `<tbody>${entries.map(([name, members]) => {
    const sem = S.platoonSemesters[name] || '1st Semester';
    return `<tr data-plat-row="${name}" class="border-b border-slate-50 hover:bg-slate-50 cursor-pointer transition">
          <td class="py-3 px-3 text-slate-900 font-medium">${name} Platoon</td>
          <td class="py-3 px-3 text-slate-700">${members.length} Officers</td>
          <td class="py-3 px-3">${pill(sem === '1st Semester' ? 'emerald' : 'amber', sem)}</td>
        </tr>`;
  }).join('')}</tbody>`;

  const clickableTbl = `<div class="overflow-x-auto"><table class="w-full text-sm">${thead}${tbody}</table></div>`;

  const newPlatoonModal = S.platoonForm ? `
  <div id="newPlatoonOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-lg mx-4">
      <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
        <div>
          <div class="text-slate-900 tracking-tight">New Platoon</div>
          <div class="text-xs text-slate-500">Fill in the details to add a new ROTC platoon</div>
        </div>
        <button id="platoonFormClose" class="text-slate-400 hover:text-slate-700 p-1">${ico('close', 'w-4 h-4')}</button>
      </div>
      <div class="p-6 space-y-4 text-sm">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <div class="text-xs text-slate-500 mb-1">Platoon Name</div>
            <input id="platNameInput" placeholder="e.g. Delta" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
          </div>
          <div>
            <div class="text-xs text-slate-500 mb-1">Status</div>
            <select id="platStatusInput" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white focus:outline-none focus:border-indigo-300">
              <option>1st Semester</option>
              <option>2nd Semester</option>
            </select>
          </div>
        </div>
        <div>
          <div class="text-xs text-slate-500 mb-1">Import Officer List XLSX File</div>
          <button id="modalPlatXlsxBtn" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border-2 border-dashed border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition text-sm">
            ${ico('upload', 'w-4 h-4')} Upload XLSX List
          </button>
        </div>
      </div>
      <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-2">
        <button id="platoonFormCancel" onclick="S.platoonForm = false; render();" class="px-4 py-2 text-sm rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">Cancel</button>
        <button id="platoonFormCreate" onclick="
          const name = document.getElementById('platNameInput')?.value?.trim();
          const sem = document.getElementById('platStatusInput')?.value || '1st Semester';
          if (!name) { document.getElementById('platNameInput').focus(); return; }
          if (!S.platoons[name]) S.platoons[name] = [];
          if (!S.platoonSemesters) S.platoonSemesters = {};
          S.platoonSemesters[name] = sem;
          S.platoonForm = false;
          render();
        " class="inline-flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg bg-slate-900 text-white hover:bg-slate-800">${ico('shield', 'w-4 h-4')} Create Platoon</button>
      </div>
    </div>
  </div>` : '';

  const totalCadets = Object.values(S.platoons).reduce((sum, arr) => sum + arr.length, 0);

  const platoonModal = S.selectedPlatoon ? (() => {
    const name = S.selectedPlatoon;
    const students = S.platoons[name] || [];
    const rows = students.map((st, i) => {
      const isSel = S.selectedStudentRow === i;
      return `<tr data-rotc-student-row="${i}" class="border-b border-slate-50 cursor-pointer transition ${isSel ? 'bg-rose-50/60' : 'hover:bg-indigo-50/40'}">
            <td class="py-2.5 px-3 text-slate-400 text-xs text-center">${i + 1}</td>
            <td class="py-2.5 px-3 font-medium text-slate-900 text-sm">${st.name}</td>
            <td class="py-2.5 px-3"><span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">${st.rank}</span></td>
            <td class="py-2.5 px-3"><span class="text-xs px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700">${st.spec}</span></td>
            <td class="py-2.5 px-3 text-slate-600 text-sm">${st.dob || '—'}</td>
            <td class="py-2.5 px-3 text-slate-600 text-sm">${st.gender || '—'}</td>
            <td class="py-2.5 px-3 text-slate-600 text-sm max-w-[160px] truncate">${st.address || '—'}</td>
            <td class="py-2.5 px-3 text-slate-600 text-sm">${st.cellNo || '—'}</td>
            <td class="py-2.5 px-3 text-slate-600 text-sm">${st.email || '—'}</td>
            <td class="py-2.5 px-3 text-right w-24">${isSel ? `<button onclick="event.stopPropagation(); S.platoons['${name}'].splice(${i}, 1); S.selectedStudentRow = null; render();" style="animation:slideInRight .18s ease-out" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded-lg bg-rose-600 text-white hover:bg-rose-700">${ico('close', 'w-3 h-3')} Remove</button>` : `<span class="text-slate-300">${ico('chevron', 'w-4 h-4')}</span>`}</td>
          </tr>`;
    }).join('');
    const emptyRow = `<tr><td colspan="10" class="py-10 text-center text-slate-400 text-sm">No officers assigned to this section.</td></tr>`;
    return `
  <div id="platoonModalOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
     <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-6xl mx-4 flex flex-col max-h-[88vh]">
      <style>@keyframes slideInRight{from{opacity:0;transform:translateX(32px)}to{opacity:1;transform:translateX(0)}}</style>
      <div class="px-6 py-4 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between shrink-0 rounded-t-2xl">
        <div>
          <div class="font-semibold tracking-tight">${name} Platoon — Assign Officer Section</div>
          <div class="text-slate-300 text-xs mt-0.5">${students.length} assigned · Click a row to select &amp; remove</div>
        </div>
        <div class="flex items-center gap-2">
          <button onclick="S.selectedPlatoon = null; S.selectedStudentRow = null; render();" class="text-slate-300 hover:text-white p-1">${ico('close', 'w-5 h-5')}</button>
        </div>
      </div>
      <div class="overflow-auto flex-1">
        <table class="w-full text-sm min-w-[1000px]">
          <thead class="sticky top-0 bg-slate-50 z-10 border-b border-slate-200">
            <tr class="text-left text-[11px] uppercase tracking-wider text-slate-500">
              <th class="py-3 px-3 font-medium w-8">#</th>
              <th class="py-3 px-3 font-medium">Officer Name</th>
              <th class="py-3 px-3 font-medium">Rank</th>
              <th class="py-3 px-3 font-medium">Specialty</th>
              <th class="py-3 px-3 font-medium">Date of Birth</th>
              <th class="py-3 px-3 font-medium">Gender</th>
              <th class="py-3 px-3 font-medium">Residential Address</th>
              <th class="py-3 px-3 font-medium">Cell #</th>
              <th class="py-3 px-3 font-medium">Email Address</th>
              <th class="py-3 px-3 font-medium w-24">Action</th>
            </tr>
          </thead>
          <tbody>${students.length ? rows : emptyRow}</tbody>
        </table>
      </div>
      <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/60 shrink-0">
        <div class="text-[11px] uppercase tracking-wider text-slate-500 mb-3">Assign Officer to ${name} Section</div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-2">
          <input id="platStudentName" placeholder="Officer Name (Last, First M.)" class="px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
          <select id="platStudentRank" class="px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white focus:outline-none focus:border-indigo-300"><option value="">Rank</option><option>Pvt</option><option>Cpl</option><option>Sgt</option></select>
          <select id="platStudentSpec" class="px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white focus:outline-none focus:border-indigo-300"><option value="">Specialty</option><option>Rifle</option><option>Signal</option><option>Medical</option><option>Support</option></select>
          <input id="platStudentDob" placeholder="Date of Birth" class="px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
          <select id="platStudentGender" class="px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white focus:outline-none focus:border-indigo-300"><option value="">Gender</option><option>Male</option><option>Female</option></select>
          <input id="platStudentAddr" placeholder="Residential Address" class="col-span-2 px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
          <input id="platStudentCell" placeholder="Cell #" class="px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
        </div>
        <div class="flex items-center gap-2">
          <input id="platStudentEmail" placeholder="Email Address" class="flex-1 max-w-sm px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" />
          <button onclick="
            const n = document.getElementById('platStudentName').value;
            const r = document.getElementById('platStudentRank').value || 'Pvt';
            const s = document.getElementById('platStudentSpec').value || 'Rifle';
            if(n) { S.platoons['${name}'].push({ id: 'c'+Date.now(), name: n, rank: r, spec: s, dob: document.getElementById('platStudentDob').value, gender: document.getElementById('platStudentGender').value, address: document.getElementById('platStudentAddr').value, cellNo: document.getElementById('platStudentCell').value, email: document.getElementById('platStudentEmail').value }); render(); }
          " class="inline-flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg bg-slate-900 text-white hover:bg-slate-800">${ico('plus', 'w-4 h-4')} Assign Officer</button>
        </div>
      </div>
    </div>
  </div>`;
  })() : '';

  const activePlatoonFilterLabel = { All: 'All Platoons', '1st': '1st Semester', '2nd': '2nd Semester' }[S.platoonFilter || 'All'];
  const filterBtnHtml = `
    <div class="relative inline-block text-left font-medium">
      <button id="platFilterBtn" type="button"
             onclick="event.stopPropagation(); S.showPlatoonFilterMenu = !S.showPlatoonFilterMenu; render();"
             class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus:outline-none focus:border-indigo-300 transition shadow-sm cursor-pointer" title="Click to filter platoons">
        ${ico('filter', 'w-4 h-4 text-slate-500')}
        <span>${activePlatoonFilterLabel}</span>
        <span class="text-slate-400 ml-1 pointer-events-none">
          ${ico('chevron', 'w-3.5 h-3.5')}
        </span>
      </button>
      ${S.showPlatoonFilterMenu ? `
      <div id="platFilterDropdown" class="absolute right-0 mt-1.5 w-48 rounded-xl bg-white border border-slate-200 shadow-lg py-1.5 z-50 text-left">
        <div class="px-3 py-1 text-[10px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-50 mb-1">Filter by Semester</div>
        ${[
        { val: 'All', label: 'All Platoons' },
        { val: '1st', label: '1st Semester' },
        { val: '2nd', label: '2nd Semester' }
      ].map(opt => {
        const isSel = S.platoonFilter === opt.val;
        return `
          <button onclick="event.stopPropagation(); S.platoonFilter = '${opt.val}'; S.showPlatoonFilterMenu = false; render();" class="w-full text-left px-3.5 py-1.5 text-sm hover:bg-slate-50 transition-colors flex items-center justify-between ${isSel ? 'text-indigo-600 font-semibold bg-indigo-50/40' : 'text-slate-700'}">
            <span>${opt.label}</span>
            ${isSel ? ico('check', 'w-4 h-4 text-indigo-600') : ''}
          </button>
          `;
      }).join('')}
      </div>
      ` : ''}
    </div>
  `;

  return `<div class="space-y-5">
        ${newPlatoonModal}
        <input type="file" id="platXlsxImportInput" accept=".xlsx,.xls" class="hidden" />
        <input type="file" id="modalPlatXlsxInput" accept=".xlsx,.xls" class="hidden" />
        ${pageHdr('Assign Officer Section', 'Manage platoons and their assigned officers', `
          ${filterBtnHtml}
          <button id="newPlatoonBtn" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg bg-slate-900 text-white hover:bg-slate-800 transition">${ico('shield', 'w-4 h-4')} New Platoon</button>
          <button id="importPlatXlsxBtn" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">${ico('upload', 'w-4 h-4')} Import Master List</button>
        `)}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          ${card(`<div class="text-3xl font-bold text-slate-900 tracking-tight">${totalCadets}</div><div class="text-sm text-slate-500 mt-1">Total Active Officers</div>`, { cls: 'md:col-span-1' })}
          ${card(`<div class="text-3xl font-bold text-slate-900 tracking-tight">${Object.keys(S.platoons).length}</div><div class="text-sm text-slate-500 mt-1">Active Platoons</div>`, { cls: 'md:col-span-1' })}
          ${card(`<div class="text-3xl font-bold text-slate-900 tracking-tight">${(S.unassigned || []).length}</div><div class="text-sm text-slate-500 mt-1">Unassigned Officers</div>`, { cls: 'md:col-span-2' })}
        </div>
        ${card(`<div class="flex items-center gap-2 mb-4 justify-start">
          <div class="relative flex-1 max-w-md">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
              ${ico('search', 'w-4 h-4')}
            </span>
            <input id="platSearchInput" type="text" autocomplete="off"
                   placeholder="Search platoons…" 
                   value="${S.platSearch || ''}" 
                   class="w-full pl-9 pr-3 py-2 text-sm rounded-lg bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-indigo-300 transition" />
          </div>
        </div>${clickableTbl}`)}

        ${platoonModal}
      </div>`;
}

const R_ROSTER = [
  { id: '2024-00057', name: 'Officer Domingo, Karl A.', rank: 'Cpl', platoon: 'Unassigned', spec: 'Rifle', year: '2nd', status: 'Active' },
  { id: '2024-00198', name: 'Officer Reyes, Jose M.', rank: 'Pvt', platoon: 'Alpha', spec: 'Medical', year: '1st', status: 'Active' },
  { id: '2024-00345', name: 'Officer Aquino, Maria L.', rank: 'Sgt', platoon: 'Bravo', spec: 'Signal', year: '3rd', status: 'Active' },
  { id: '2024-00422', name: 'Officer Bautista, Anna R.', rank: 'Pvt', platoon: 'Charlie', spec: 'Support', year: '1st', status: 'Active' },
  { id: '2024-00513', name: 'Officer Cruz, Luis P.', rank: 'Sgt', platoon: 'Alpha', spec: 'Rifle', year: '3rd', status: 'Active' },
  { id: '2024-00622', name: 'Officer Tan, Vince N.', rank: 'Sgt', platoon: 'Bravo', spec: 'Signal', year: '3rd', status: 'Active' },
  { id: '2024-00714', name: 'Officer Lim, Sophie U.', rank: 'Pvt', platoon: 'Bravo', spec: 'Signal', year: '1st', status: 'Leave' },
];

function rRosters() {
  let filteredRoster = R_ROSTER;
  if (S.rosterSearch) {
    const term = S.rosterSearch.toLowerCase();
    filteredRoster = filteredRoster.filter(r => r.name.toLowerCase().includes(term) || r.id.toLowerCase().includes(term));
  }
  if (S.rosterFilterPlatoon && S.rosterFilterPlatoon !== 'All') {
    filteredRoster = filteredRoster.filter(r => r.platoon === S.rosterFilterPlatoon);
  }

  const rTbl = tbl([
    { key: 'id', label: 'Officer ID' },
    { key: 'name', label: 'Name', fn: r => `<span class="text-slate-900">${r.name}</span>` },
    { key: 'rank', label: 'Rank' },
    { key: 'platoon', label: 'Platoon', fn: r => pill(r.platoon === 'Unassigned' ? 'slate' : 'indigo', r.platoon) },
    { key: 'spec', label: 'Specialty' },
    { key: 'year', label: 'Year' },
    { key: 'status', label: 'Status', fn: r => pill(r.status === 'Active' ? 'emerald' : 'amber', r.status) },
  ], filteredRoster, (r) => `data-officer-row="${r.id}"`);

  const officerModal = S.selectedOfficer ? (() => {
    const off = R_ROSTER.find(o => o.id === S.selectedOfficer);
    if (!off) return '';
    const offStudents = [
      { name: 'Cadet Abalos, Mark J.', gender: 'M', dob: '2005-04-12', address: 'Poblacion, Aurora' },
      { name: 'Cadet Bautista, Sarah L.', gender: 'F', dob: '2005-08-22', address: 'San Isidro, Aurora' },
      { name: 'Cadet Cruz, John Paul', gender: 'M', dob: '2004-11-05', address: 'Reserva, Aurora' },
      { name: 'Cadet De Leon, Ana Marie', gender: 'F', dob: '2006-01-14', address: 'Baler, Aurora' }
    ];
    const rows = offStudents.map((st, i) => `<tr class="border-b border-slate-50 hover:bg-slate-50 transition">
      <td class="py-2.5 px-3 text-slate-400 text-xs text-center">${i + 1}</td>
      <td class="py-2.5 px-3 font-medium text-slate-900 text-sm">${st.name}</td>
      <td class="py-2.5 px-3 text-slate-600 text-sm">${st.gender}</td>
      <td class="py-2.5 px-3 text-slate-600 text-sm">${st.dob}</td>
      <td class="py-2.5 px-3 text-slate-600 text-sm">${st.address}</td>
    </tr>`).join('');

    return `
    <div id="officerStudentsOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-3xl mx-4 flex flex-col max-h-[85vh]">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between shrink-0">
          <div>
            <div class="text-slate-900 tracking-tight text-lg">Students under ${off.name}</div>
            <div class="text-xs text-slate-500">${off.platoon} Platoon · ${off.rank} · ${offStudents.length} students assigned</div>
          </div>
          <div class="flex items-center gap-2">
            <button id="clearOfficerListBtn" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-amber-200 bg-white text-amber-600 hover:bg-amber-50 transition">${ico('archive', 'w-3.5 h-3.5')} Clear List</button>
            <button id="deleteOfficerSecBtn" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-rose-200 bg-white text-rose-600 hover:bg-rose-50 transition">${ico('trash', 'w-3.5 h-3.5')} Delete Section</button>
            <button id="officerStudentsClose" class="text-slate-400 hover:text-slate-700 p-1 bg-slate-50 hover:bg-slate-100 rounded-md transition">${ico('close', 'w-5 h-5')}</button>
          </div>
        </div>
        <div class="overflow-y-auto flex-1 p-0">
          <table class="w-full text-sm text-left">
            <thead class="sticky top-0 bg-slate-50 border-b border-slate-100">
              <tr class="text-[11px] uppercase tracking-wider text-slate-500">
                <th class="py-2.5 px-3 text-center w-12">#</th>
                <th class="py-2.5 px-3 font-medium">Student Name</th>
                <th class="py-2.5 px-3 font-medium">Gender</th>
                <th class="py-2.5 px-3 font-medium">Date of Birth</th>
                <th class="py-2.5 px-3 font-medium">Address</th>
              </tr>
            </thead>
            <tbody>${rows}</tbody>
          </table>
        </div>
      </div>
    </div>`;
  })() : '';

  const activeRosterLabel = { All: 'All Platoons', Alpha: 'Alpha Platoon', Bravo: 'Bravo Platoon', Charlie: 'Charlie Platoon', Unassigned: 'Unassigned' }[S.rosterFilterPlatoon || 'All'];
  const filterControlsHtml = `
    <div class="flex flex-wrap items-center gap-3 mb-4 justify-start">
      <div class="flex items-center gap-1.5 max-w-md w-full">
        <div class="relative flex-1">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
            ${ico('search', 'w-4 h-4 text-slate-400')}
          </span>
          <input id="rosterSearchInput" type="text" autocomplete="off"
                 placeholder="Search officer ID or name…" 
                 value="${S.rosterSearch || ''}" 
                 class="w-full pl-9 pr-3 py-1.5 text-xs rounded-lg bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:border-indigo-300 transition shadow-sm" />
        </div>
        <button id="rosterSearchBtn" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-900 text-white hover:bg-slate-800 transition shadow-sm cursor-pointer">
          Search
        </button>
      </div>

      <div class="relative font-medium w-48 shrink-0">
        <button id="rostersFilterBtn" type="button"
               class="w-full pl-3 pr-8 py-1.5 text-xs font-medium rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus:outline-none focus:border-indigo-300 transition shadow-sm flex items-center justify-between cursor-pointer">
          <span>${activeRosterLabel}</span>
          <span class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
            ${ico('chevron', 'w-3.5 h-3.5')}
          </span>
        </button>
        ${S.showRostersFilterMenu ? `
        <div id="rostersFilterDropdown" class="absolute right-0 mt-1.5 w-48 rounded-xl bg-white border border-slate-200 shadow-lg py-1.5 z-50">
          <div class="px-3 py-1 text-[10px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-50 mb-1">Filter by Platoon</div>
          ${[
        { val: 'All', label: 'All Platoons' },
        { val: 'Alpha', label: 'Alpha Platoon' },
        { val: 'Bravo', label: 'Bravo Platoon' },
        { val: 'Charlie', label: 'Charlie Platoon' },
        { val: 'Unassigned', label: 'Unassigned' }
      ].map(opt => {
        const isSel = S.rosterFilterPlatoon === opt.val;
        return `
            <button data-rosters-filter-opt="${opt.val}" class="w-full text-left px-3.5 py-1.5 text-sm hover:bg-slate-50 transition-colors flex items-center justify-between ${isSel ? 'text-indigo-600 font-semibold bg-indigo-50/40' : 'text-slate-700'}">
              <span>${opt.label}</span>
              ${isSel ? ico('check', 'w-4 h-4 text-indigo-600') : ''}
            </button>
            `;
      }).join('')}
        </div>
        ` : ''}
      </div>
    </div>
  `;

  return `<div class="space-y-5">
    ${pageHdr('Assign Officer Section', 'Master list of officers with rank, platoon, and specialty', `
      <div class="flex items-center gap-2">
        <button id="exportOfficerBtn" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">${ico('download', 'w-4 h-4')} Export</button>
        <button id="addOfficerBtn" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg bg-slate-900 text-white hover:bg-slate-800 transition">${ico('userplus', 'w-4 h-4')} Add Officer</button>
      </div>`)}
    ${card(`${filterControlsHtml}${rTbl}`)}
    ${officerModal}
    ${S.officerForm ? `
    <div id="officerFormOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-lg mx-4">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
          <div><div class="text-slate-900 font-semibold tracking-tight">Add New Officer</div><div class="text-xs text-slate-500">Enter officer details to add to the section</div></div>
          <button id="officerFormClose" class="text-slate-400 hover:text-slate-700 p-1">${ico('close', 'w-5 h-5')}</button>
        </div>
        <div class="p-6 space-y-4 text-sm">
          <div class="grid grid-cols-2 gap-3">
            <div><div class="text-xs text-slate-500 mb-1">Officer ID</div><input id="newOffId" placeholder="e.g. 2024-XXXXX" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" /></div>
            <div><div class="text-xs text-slate-500 mb-1">Rank</div><select id="newOffRank" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300"><option>Pvt</option><option>Cpl</option><option>Sgt</option><option>2Lt</option><option>1Lt</option></select></div>
          </div>
          <div><div class="text-xs text-slate-500 mb-1">Full Name</div><input id="newOffName" placeholder="Last, First M." class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300" /></div>
          <div class="grid grid-cols-2 gap-3">
            <div><div class="text-xs text-slate-500 mb-1">Platoon</div><select id="newOffPlat" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300"><option>Unassigned</option><option>Alpha</option><option>Bravo</option><option>Charlie</option></select></div>
            <div><div class="text-xs text-slate-500 mb-1">Specialty</div><select id="newOffSpec" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-300"><option>Rifle</option><option>Medical</option><option>Signal</option><option>Support</option></select></div>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-2 bg-slate-50/50 rounded-b-2xl">
          <button id="officerFormCancel" class="px-4 py-2 text-sm rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 transition">Cancel</button>
          <button id="officerFormSave" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg bg-slate-900 text-white hover:bg-slate-800 transition">${ico('check', 'w-4 h-4')} Save Officer</button>
        </div>
      </div>
    </div>` : ''}
  </div>`;
}

const R_DESIGNS = [
  { title: 'Tactical Drill Sequence — Section 2', phase: 'Field Exercise', date: 'May 23, 2026', duration: '6 hrs', status: 'Approved' },
  { title: 'Civil-Military Outreach — Brgy. San Pablo', phase: 'Community', date: 'May 30, 2026', duration: '4 hrs', status: 'Pending' },
  { title: 'First-Aid & Trauma Response Workshop', phase: 'Training', date: 'Jun 6, 2026', duration: '3 hrs', status: 'Draft' },
  { title: 'Bivouac & Survival Camp', phase: 'Field Exercise', date: 'Jun 20, 2026', duration: '2 days', status: 'Pending' },
];

function rDesigns() {
  const dTbl = tbl([
    { key: 'title', label: 'Activity', fn: r => `<span class="text-slate-900">${r.title}</span>` },
    { key: 'phase', label: 'Phase' },
    { key: 'date', label: 'Date' },
    { key: 'duration', label: 'Duration' },
    { key: 'status', label: 'Status', fn: r => pill(r.status === 'Approved' ? 'emerald' : r.status === 'Pending' ? 'indigo' : 'slate', r.status) },
  ], R_DESIGNS);
  return `<div class="space-y-5">
    ${pageHdr('Activity Designs', 'Plan drill exercises, training, and community operations', `<button class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg bg-slate-900 text-white hover:bg-slate-800">${ico('plus', 'w-4 h-4')} New Activity Design</button>`)}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
      ${card(dTbl, { title: 'Designs in Cycle', cls: 'lg:col-span-2' })}
      ${card(`<div class="space-y-3 text-sm">
        <div><div class="text-xs text-slate-500 mb-1">Activity Title</div><input class="w-full px-3 py-2 rounded-lg border border-slate-200" placeholder="e.g. Tactical Drill Sequence" /></div>
        <div><div class="text-xs text-slate-500 mb-1">Phase</div><select class="w-full px-3 py-2 rounded-lg border border-slate-200"><option>Field Exercise</option><option>Training</option><option>Community</option><option>Inspection</option></select></div>
        <div class="grid grid-cols-2 gap-2">
          <div><div class="text-xs text-slate-500 mb-1">Date</div><input type="date" class="w-full px-3 py-2 rounded-lg border border-slate-200" /></div>
          <div><div class="text-xs text-slate-500 mb-1">Duration</div><input value="3 hrs" class="w-full px-3 py-2 rounded-lg border border-slate-200" /></div>
        </div>
        <div><div class="text-xs text-slate-500 mb-1">Objectives</div><textarea rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-200" placeholder="Mission, expected outcomes, safety considerationsâ€¦"></textarea></div>
        <div class="flex items-center gap-2 pt-1">
          <button class="px-3 py-2 text-sm rounded-lg border border-slate-200 text-slate-700">Save Draft</button>
          <button class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg bg-amber-400 text-slate-900">${ico('send', 'w-4 h-4')} Submit</button>
        </div>
      </div>`, { title: 'Design Brief' })}
    </div>
  </div>`;
}

const R_CAL_DAYS = [
  { d: 14, label: 'TUE', events: [] },
  { d: 15, label: 'WED', events: [{ t: 'Section briefing', c: 'bg-indigo-500' }] },
  { d: 16, label: 'THU', events: [{ t: 'Drill day 07:00H', c: 'bg-amber-500' }] },
  { d: 17, label: 'FRI', events: [] },
  { d: 18, label: 'SAT', events: [{ t: 'Q1 Reports due', c: 'bg-rose-500' }] },
  { d: 19, label: 'SUN', events: [] },
  { d: 20, label: 'MON', events: [{ t: 'Strength report', c: 'bg-emerald-500' }] },
];

function rCalendar() {
  const calGrid = R_CAL_DAYS.map(d => `<div class="border border-slate-200 rounded-md p-3 min-h-[160px] bg-white">
    <div class="text-[10px] uppercase tracking-wider text-slate-500">${d.label}</div>
    <div class="text-slate-900 tracking-tight text-xl mt-0.5">${d.d}</div>
    <div class="mt-3 space-y-1.5">${d.events.map(e => `<div class="${e.c} text-[11px] px-2 py-1 rounded text-white truncate">${e.t}</div>`).join('')}</div>
  </div>`).join('');

  const upcomingEvents = [
    { d: 'MAY 16', t: 'Saturday Drill — Formation 0700H', v: 'Parade Grounds', color: 'bg-amber-500', type: 'drill' },
    { d: 'MAY 18', t: 'Q1 Accomplishment Reports Due', v: 'Officer Console', color: 'bg-rose-500', type: 'report' },
    { d: 'MAY 23', t: 'Tactical Inspection', v: 'Field Site B', color: 'bg-indigo-500', type: 'briefing' },
    { d: 'MAY 30', t: 'Civil-Military Outreach', v: 'Brgy. San Pablo', color: 'bg-emerald-500', type: 'outreach' },
  ];

  const activeFilter = S.rCalFilter || 'all';
  const filteredEvents = upcomingEvents.filter(e => {
    if (activeFilter !== 'all' && e.type !== activeFilter) return false;
    if (S.calSearch) {
      const q = S.calSearch.toLowerCase();
      const labelMap = { briefing: 'tactical/briefings', drill: 'drills/exercises', report: 'reports', outreach: 'community outreach' };
      if (labelMap[e.type] !== q) {
        return e.t.toLowerCase().includes(q) || e.v.toLowerCase().includes(q);
      }
    }
    return true;
  });

  const upcoming = filteredEvents.map(e => `<li class="px-5 py-4 flex items-center gap-4">
    <div class="w-14 text-center shrink-0"><div class="text-[10px] tracking-wider text-slate-400">${e.d.split(' ')[0]}</div><div class="text-slate-900 tracking-tight">${e.d.split(' ')[1]}</div></div>
    <div class="w-1 self-stretch rounded-full ${e.color}"></div>
    <div class="flex-1 min-w-0"><div class="text-sm text-slate-900">${e.t}</div><div class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">${ico('mappin', 'w-3 h-3')} ${e.v}</div></div>
    ${ico('chevron', 'w-4 h-4 text-slate-300')}
  </li>`).join('');

  const activeRCalLabel = { all: 'All Events', briefing: 'Tactical/Briefings', drill: 'Drills/Exercises', report: 'Reports', outreach: 'Community Outreach' }[activeFilter];
  const filterBtnHtml = `
    <div class="flex flex-col gap-1.5 w-48 text-left font-medium">
      <div class="relative">
        <button id="rCalFilterBtn" type="button"
               class="w-full pl-3 pr-8 py-2 text-sm font-medium rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus:outline-none focus:border-indigo-300 transition-colors shadow-sm flex items-center justify-between cursor-pointer">
          <span>${activeRCalLabel}</span>
          <span class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
            ${ico('chevron', 'w-3.5 h-3.5')}
          </span>
        </button>
        ${S.showRCalFilterMenu ? `
        <div id="rCalFilterDropdown" class="absolute right-0 mt-1.5 w-48 rounded-xl bg-white border border-slate-200 shadow-lg py-1.5 z-50">
          <div class="px-3 py-1 text-[10px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-50 mb-1">Filter by Type</div>
          ${[
        { val: 'all', label: 'All Events' },
        { val: 'briefing', label: 'Tactical/Briefings' },
        { val: 'drill', label: 'Drills/Exercises' },
        { val: 'report', label: 'Reports' },
        { val: 'outreach', label: 'Community Outreach' }
      ].map(opt => {
        const isSel = activeFilter === opt.val;
        return `
            <button data-rcal-filter-opt="${opt.val}" class="w-full text-left px-3.5 py-1.5 text-sm hover:bg-slate-50 transition-colors flex items-center justify-between ${isSel ? 'text-slate-950 font-semibold bg-slate-100' : 'text-slate-700'}">
              <span>${opt.label}</span>
              ${isSel ? ico('check', 'w-4 h-4 text-slate-950') : ''}
            </button>
            `;
      }).join('')}
        </div>
        ` : ''}
      </div>
      <div class="relative">
        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
          ${ico('search', 'w-3.5 h-3.5 text-slate-400')}
        </span>
        <input id="rCalSearchInput" type="text" autocomplete="off"
               class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white text-slate-700 placeholder-slate-400 focus:outline-none focus:border-indigo-300 transition-colors shadow-sm"
               placeholder="Search events..."
               value="${S.calSearch || ''}" />
      </div>
    </div>
  `;

  return `<div class="space-y-5">
    ${pageHdr('Master Calendar', 'Drill days, inspections, and field operations across all platoons', `
      <div class="flex items-center gap-2">
        ${filterBtnHtml}
        <button class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg bg-slate-900 text-white">${ico('plus', 'w-4 h-4')} Add Event</button>
      </div>`)}
    ${card(`<div class="grid grid-cols-7 gap-2">${calGrid}</div>`, { title: 'Week of May 14 – May 20, 2026' })}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
      ${card(upcoming.length ? `<ul class="divide-y divide-slate-100 -mx-5 -my-5">${upcoming}</ul>` : `<div class="text-center text-sm text-slate-400 py-6">No events found matching filter</div>`, { title: 'Upcoming', cls: 'lg:col-span-2' })}
      ${bulletinCard()}
    </div>
  </div>`;
}

/* ================================================================
   DASHBOARD RENDERS
================================================================ */
function renderCoordinator() {
  const pd = PROFILE_DATA[S.email] || PROFILE_DATA.coordinator || {};
  const userName = pd.fullName || 'Dr. Maya Reyes';
  const userInitials = getInitials(userName) || 'MR';
  const first = userName.replace(/^(Dr\.|Prof\.|1Lt\.|Col\.|Capt\.|Lt\.)\s+/i, '').split(' ')[0];
  const nav = [
    { name: 'Dashboard', ico: 'dashboard' },
    { name: 'Sections & Students', ico: 'users' },
    { name: 'Instructors & ROTC Officers', ico: 'grad' },
    { name: 'Report & Activity Approvals', ico: 'filecheck', badge: 4 },
    { name: 'OCR Grade Upload', ico: 'scan' },
    { name: 'Activity Calendar', ico: 'calendar' },
    { name: 'Certificates', ico: 'award' },
    { name: 'Student Archive', ico: 'archive' },
    { name: 'Audit Logs', ico: 'scroll' },
  ].map(n => ({ ...n, active: S.coordPage === n.name }));
  const pageMap = { 'Dashboard': cDashboard(), 'Sections & Students': cSections(), 'Instructors & ROTC Officers': cInstructors(), 'Report & Activity Approvals': cApprovals(), 'OCR Grade Upload': cOCR(), 'Activity Calendar': cCalendar(), 'Certificates': cCertificates(), 'Student Archive': cStudentArchive(), 'Audit Logs': cAudit() };
  return renderShell({ theme: 'indigo', brand: 'DNSC NSTP', brandSub: 'Coordinator', navItems: nav, userName: userName, userRole: 'Coordinator', userInitials: userInitials, greeting: S.coordPage === 'Dashboard' ? `Welcome back, ${first}` : S.coordPage, context: 'Davao Del Norte State College', ctaLabel: '', content: pageMap[S.coordPage] || '' });
}

function renderInstructor() {
  const pd = PROFILE_DATA[S.email] || PROFILE_DATA.instructor || {};
  const userName = pd.fullName || 'Prof. Julian Santos';
  const userInitials = getInitials(userName) || 'JS';
  const first = userName.replace(/^(Dr\.|Prof\.|1Lt\.|Col\.|Capt\.|Lt\.)\s+/i, '').split(' ')[0];
  const nav = [
    { name: 'Overview', ico: 'grid' },
    { name: 'My Classes', ico: 'book', badge: 4 },
    { name: 'Activity Plans', ico: 'clipboard' },
    { name: 'Accomplishment Reports', ico: 'filetext', badge: 3 },
    { name: 'Announcements', ico: 'megaphone' },
  ].map(n => ({ ...n, active: S.instrPage === n.name }));
  const pageMap = { 'Overview': iOverview(), 'My Classes': iClasses(), 'Activity Plans': iPlans(), 'Accomplishment Reports': iReports(), 'Announcements': iAnnouncements() };
  return renderShell({ theme: 'emerald', brand: 'DNSC NSTP', brandSub: 'Instructor Portal', navItems: nav, userName: userName, userRole: 'CWTS · LTS Instructor', userInitials: userInitials, greeting: S.instrPage === 'Overview' ? `Good morning, ${first}` : S.instrPage, context: 'Davao Del Norte State College', ctaLabel: S.instrPage === 'Activity Plans' ? 'New Activity Plan' : S.instrPage === 'Accomplishment Reports' ? 'New Activity Plan' : '', content: pageMap[S.instrPage] || '' });
}

function renderROTC() {
  const pd = PROFILE_DATA[S.email] || PROFILE_DATA.rotc || {};
  const userName = pd.fullName || '1Lt. Daniel Castillo';
  const userInitials = getInitials(userName) || 'DC';
  const last = userName.split(' ').pop();
  const nav = [
    { name: 'Overview', ico: 'grid' },
    { name: 'Assign Officer Section', ico: 'users', badge: 4 },
    { name: 'Activity Designs', ico: 'clipboard', badge: 2 },
  ].map(n => ({ ...n, active: S.rotcPage === n.name }));
  const pageMap = { 'Overview': rOverview(), 'Assign Officer Section': rPlatoon(), 'Activity Designs': rDesigns() };
  return renderShell({ theme: 'military', brand: 'Aurora ROTC', brandSub: 'Officer Console', navItems: nav, userName: userName, userRole: 'First Class Officer', userInitials: userInitials, greeting: S.rotcPage === 'Overview' ? `Stand-to, Lt. ${last}` : S.rotcPage, context: 'Saturday Drill · May 16, 2026', ctaLabel: 'New Activity Design', content: pageMap[S.rotcPage] || '' });
}

/* ================================================================
   MAIN RENDER
================================================================ */
function adminAccountsPage() {
  const accountsList = CREDENTIALS.filter(c => c.email !== 'admin123@dnsc.edu.ph');
  const isEditing = S.editingAccEmail != null;
  const editAcc = isEditing ? CREDENTIALS.find(c => c.email === S.editingAccEmail) : null;
  const editPd = isEditing ? (PROFILE_DATA[S.editingAccEmail] || PROFILE_DATA[editAcc?.role] || {}) : {};

  const formTitle = isEditing ? 'Edit Account' : 'Create Account';
  const formSubtitle = isEditing ? 'Update administrative credentials & details' : 'Register new system coordinator or instructor';
  const formIcon = isEditing ? 'pencil' : 'userplus';
  const submitText = isEditing ? 'Save Changes' : 'Create Account';

  const rows = accountsList.map((acc, idx) => {
    const pd = PROFILE_DATA[acc.email] || PROFILE_DATA[acc.role] || {};
    const initials = getInitials(pd.fullName || acc.label) || 'US';
    const roleColor = acc.role === 'coordinator' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' 
                    : acc.role === 'instructor' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' 
                    : 'bg-slate-100 text-slate-800 border-slate-200';
    return `
    <tr class="border-b border-slate-100 hover:bg-slate-50 transition duration-150">
      <td class="py-3.5 px-4">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-full bg-gradient-to-br ${acc.grad} text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
            ${initials}
          </div>
          <div>
            <div class="text-sm font-semibold text-slate-900">${pd.fullName || 'New User'}</div>
            <div class="text-xs text-slate-500 mt-0.5">${pd.gmail || acc.email}</div>
          </div>
        </div>
      </td>
      <td class="py-3.5 px-4">
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold border ${roleColor}">
          ${ico(acc.ico, 'w-3 h-3')} ${acc.label}
        </span>
      </td>
      <td class="py-3.5 px-4">
        <div class="text-xs text-slate-700 font-medium">${pd.contact || '—'}</div>
      </td>
      <td class="py-3.5 px-4">
        <div class="text-xs font-semibold text-slate-800">${pd.degree || '—'}</div>
        <div class="text-[10px] text-slate-500 truncate max-w-[150px]" title="${pd.degreeTitle || ''}">${pd.degreeTitle || '—'}</div>
      </td>
      <td class="py-3.5 px-4 font-mono text-xs text-slate-600">
        ${acc.password}
      </td>
      <td class="py-3.5 px-4 text-right">
        <div class="flex items-center justify-end gap-1.5">
          <button onclick="editAccount('${acc.email}')" class="p-1.5 rounded-lg hover:bg-purple-50 text-slate-400 hover:text-purple-600 transition duration-200" title="Edit account">
            ${ico('pencil', 'w-4 h-4')}
          </button>
          <button onclick="deleteAccount('${acc.email}')" class="p-1.5 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition duration-200" title="Delete account">
            ${ico('trash', 'w-4 h-4')}
          </button>
        </div>
      </td>
    </tr>`;
  }).join('');

  return `
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
    <div class="xl:col-span-1 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
      <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-5">
        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shadow-sm">
          ${ico(formIcon, 'w-5 h-5')}
        </div>
        <div>
          <h3 class="font-bold text-slate-900 tracking-tight text-base">${formTitle}</h3>
          <p class="text-xs text-slate-500 mt-0.5">${formSubtitle}</p>
        </div>
      </div>
      <div class="space-y-4 text-sm">
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Full Name</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">${ico('users', 'w-4 h-4')}</span>
            <input id="newAccName" type="text" placeholder="e.g. Dr. Juan Dela Cruz" value="${editPd.fullName || ''}" class="w-full pl-9 pr-3 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-50 focus:outline-none transition" />
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Contact Number</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">${ico('bell', 'w-4 h-4')}</span>
            <input id="newAccContact" type="text" placeholder="e.g. +63 917 123 4567" value="${editPd.contact || ''}" class="w-full pl-9 pr-3 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-50 focus:outline-none transition" />
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Gmail Address (Login Email)</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">${ico('mail', 'w-4 h-4')}</span>
            <input id="newAccGmail" type="email" placeholder="e.g. j.delacruz@dnsc.edu.ph" value="${editPd.gmail || editAcc?.email || ''}" class="w-full pl-9 pr-3 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-50 focus:outline-none transition" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Degree Type</label>
            <select id="newAccDegree" class="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-50 bg-white focus:outline-none transition">
              <option value="Bachelor" ${editPd.degree === 'Bachelor' ? 'selected' : ''}>Bachelor</option>
              <option value="Masteral" ${editPd.degree === 'Masteral' ? 'selected' : ''}>Masteral</option>
              <option value="Doctoral" ${editPd.degree === 'Doctoral' ? 'selected' : ''}>Doctoral</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Account Role</label>
            <select id="newAccRole" class="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-50 bg-white focus:outline-none transition">
              <option value="coordinator" ${editAcc?.role === 'coordinator' ? 'selected' : ''}>Coordinator</option>
              <option value="instructor" ${editAcc?.role === 'instructor' ? 'selected' : ''}>CWTS/LTS Instructor</option>
              <option value="rotcofficer" ${editAcc?.role === 'rotcofficer' ? 'selected' : ''}>ROTC Officer</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Degree Description / Title</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">${ico('grad', 'w-4 h-4')}</span>
            <input id="newAccDegreeTitle" type="text" placeholder="e.g. Master of Science in Information Technology" value="${editPd.degreeTitle || ''}" class="w-full pl-9 pr-3 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-50 focus:outline-none transition" />
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Password</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">${ico('lock', 'w-4 h-4')}</span>
            <input id="newAccPassword" type="password" placeholder="Create a secure password" value="${editAcc?.password || ''}" class="w-full pl-9 pr-3 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-50 focus:outline-none transition" />
          </div>
        </div>
        <div class="flex gap-3">
          ${isEditing ? `
          <button onclick="cancelEditAccount()" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition-all">
            Cancel
          </button>` : ''}
          <button onclick="${isEditing ? `saveAccountChanges()` : `createNewAccount()`}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 active:scale-95 text-white font-semibold text-sm shadow-md shadow-purple-100 transition-all">
            ${ico(isEditing ? 'filecheck' : 'userplus', 'w-4 h-4')} ${submitText}
          </button>
        </div>
      </div>
    </div>
    <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h3 class="font-bold text-slate-900 tracking-tight text-base">Registered Accounts Registry</h3>
          <p class="text-xs text-slate-500 mt-0.5">Manage administrative credentials & profile details</p>
        </div>
        <div class="text-xs font-semibold px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-slate-600">
          Total: ${accountsList.length} User(s)
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-[10px] uppercase tracking-wider text-slate-400 bg-slate-50/50 border-b border-slate-100">
              <th class="py-3.5 px-4 font-bold">User Details</th>
              <th class="py-3.5 px-4 font-bold">Assigned Role</th>
              <th class="py-3.5 px-4 font-bold">Contact No</th>
              <th class="py-3.5 px-4 font-bold">Degree Info</th>
              <th class="py-3.5 px-4 font-bold">Password</th>
              <th class="py-3.5 px-4 font-bold text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            ${rows.length ? rows : `
            <tr>
              <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                No registered accounts found.
              </td>
            </tr>`}
          </tbody>
        </table>
      </div>
    </div>
  </div>`;
}

window.editAccount = function(email) {
  S.editingAccEmail = email;
  render();
};

window.cancelEditAccount = function() {
  S.editingAccEmail = null;
  render();
};

window.saveAccountChanges = function() {
  const email = S.editingAccEmail;
  if (!email) return;

  const name = (document.getElementById('newAccName')?.value || '').trim();
  const contact = (document.getElementById('newAccContact')?.value || '').trim();
  const gmail = (document.getElementById('newAccGmail')?.value || '').trim();
  const degree = document.getElementById('newAccDegree')?.value || '';
  const degreeTitle = (document.getElementById('newAccDegreeTitle')?.value || '').trim();
  const password = (document.getElementById('newAccPassword')?.value || '').trim();
  const role = document.getElementById('newAccRole')?.value || '';

  if (!name || !contact || !gmail || !password) {
    alert('Please fill out all required fields: Name, Contact, Gmail, and Password.');
    return;
  }

  const cred = CREDENTIALS.find(c => c.email === email);
  if (cred) {
    if (gmail.toLowerCase() !== email.toLowerCase()) {
      const exists = CREDENTIALS.some(c => c.email.toLowerCase() === gmail.toLowerCase());
      if (exists) {
        alert('An account with this email/Gmail address already exists.');
        return;
      }
    }

    let label = 'NSTP Coordinator';
    let icoVal = 'grad';
    let grad = 'from-indigo-600 to-blue-500';

    if (role === 'instructor') {
      label = 'CWTS/LTS Instructor';
      icoVal = 'book';
      grad = 'from-emerald-500 to-teal-500';
    } else if (role === 'rotcofficer') {
      label = 'ROTC 1st Class Officer';
      icoVal = 'shield';
      grad = 'from-slate-800 to-slate-900';
    }

    cred.email = gmail;
    cred.password = password;
    cred.role = role;
    cred.label = label;
    cred.ico = icoVal;
    cred.grad = grad;
  }

  const oldPd = PROFILE_DATA[email];
  if (oldPd) delete PROFILE_DATA[email];

  PROFILE_DATA[gmail] = {
    fullName: name,
    contact: contact,
    gmail: gmail,
    password: password,
    degree: degree,
    degreeTitle: degreeTitle
  };

  S.editingAccEmail = null;
  alert('Account updated successfully!');
  render();
};

window.createNewAccount = function() {
  const name = (document.getElementById('newAccName')?.value || '').trim();
  const contact = (document.getElementById('newAccContact')?.value || '').trim();
  const gmail = (document.getElementById('newAccGmail')?.value || '').trim();
  const degree = document.getElementById('newAccDegree')?.value || '';
  const degreeTitle = (document.getElementById('newAccDegreeTitle')?.value || '').trim();
  const password = (document.getElementById('newAccPassword')?.value || '').trim();
  const role = document.getElementById('newAccRole')?.value || '';

  if (!name || !contact || !gmail || !password) {
    alert('Please fill out all required fields: Name, Contact, Gmail, and Password.');
    return;
  }
  const exists = CREDENTIALS.some(c => c.email.toLowerCase() === gmail.toLowerCase());
  if (exists) {
    alert('An account with this email/Gmail address already exists.');
    return;
  }

  let label = 'NSTP Coordinator';
  let icoVal = 'grad';
  let grad = 'from-indigo-600 to-blue-500';

  if (role === 'instructor') {
    label = 'CWTS/LTS Instructor';
    icoVal = 'book';
    grad = 'from-emerald-500 to-teal-500';
  } else if (role === 'rotcofficer') {
    label = 'ROTC 1st Class Officer';
    icoVal = 'shield';
    grad = 'from-slate-800 to-slate-900';
  }

  CREDENTIALS.push({
    email: gmail,
    password: password,
    role: role,
    label: label,
    ico: icoVal,
    grad: grad
  });

  PROFILE_DATA[gmail] = {
    fullName: name,
    contact: contact,
    gmail: gmail,
    password: password,
    degree: degree,
    degreeTitle: degreeTitle
  };

  alert('Account created successfully!');
  render();
};

window.deleteAccount = function(email) {
  if (email === 'admin123@dnsc.edu.ph') {
    alert("Cannot delete the system administrator account!");
    return;
  }
  if (confirm(`Are you sure you want to delete the account for ${email}?`)) {
    const cIdx = CREDENTIALS.findIndex(c => c.email === email);
    if (cIdx !== -1) CREDENTIALS.splice(cIdx, 1);
    if (email === 'coor123' || email === 'maya.reyes@gmail.com') delete PROFILE_DATA.coordinator;
    else if (email === 'ins123' || email === 'julian.santos@gmail.com') delete PROFILE_DATA.instructor;
    else if (email === 'rotc123' || email === 'daniel.castillo@gmail.com') delete PROFILE_DATA.rotc;
    delete PROFILE_DATA[email];
    render();
  }
};

function renderAdmin() {
  const pd = PROFILE_DATA[S.email] || PROFILE_DATA.coordinator || {};
  const userName = pd.fullName || 'System Admin';
  const userInitials = getInitials(userName) || 'AD';
  const nav = [
    { name: 'Accounts', ico: 'users' }
  ].map(n => ({ ...n, active: S.adminPage === n.name }));
  return renderShell({ theme: 'purple', brand: 'DNSC NSTP', brandSub: 'Admin Console', navItems: nav, userName: userName, userRole: 'System Administrator', userInitials: userInitials, greeting: `System Administrator Console`, context: 'Davao Del Norte State College', ctaLabel: '', content: adminAccountsPage() });
}

function render() {
  const app = document.getElementById('app');
  if (!S.role) {
    app.innerHTML = renderLogin();
  } else if (S.role === 'coordinator') {
    app.innerHTML = renderCoordinator();
  } else if (S.role === 'instructor') {
    app.innerHTML = renderInstructor();
  } else if (S.role === 'admin') {
    app.innerHTML = renderAdmin();
  } else {
    app.innerHTML = renderROTC();
  }
  attachEvents();
}

/* ================================================================
   MOVE CADET HELPER
================================================================ */
function moveCadet(id, from, to) {
  if (from === to) return;
  let cadet;
  if (from === 'unassigned') {
    cadet = S.unassigned.find(c => c.id === id);
    S.unassigned = S.unassigned.filter(c => c.id !== id);
  } else {
    cadet = (S.platoons[from] || []).find(c => c.id === id);
    if (S.platoons[from]) S.platoons[from] = S.platoons[from].filter(c => c.id !== id);
  }
  if (!cadet) return;
  if (to === 'unassigned') {
    S.unassigned = [...S.unassigned, cadet];
  } else {
    if (!S.platoons[to]) S.platoons[to] = [];
    S.platoons[to] = [...S.platoons[to], cadet];
  }
}

function onDrop(e, zone) {
  e.preventDefault();
  document.querySelectorAll('.drop-zone').forEach(el => el.classList.remove('dz-over'));
  if (S.dragging) {
    moveCadet(S.dragging.id, S.dragging.from, zone);
    S.dragging = null;
    render();
  }
}
window.onDrop = onDrop;

/* ================================================================
   EVENT ATTACHMENT
================================================================ */
function attachEvents() {
  // Sidebar Workspace Toggle
  const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
  if (sidebarToggleBtn) {
    sidebarToggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      S.sidebarOpen = !S.sidebarOpen;
      render();
    });
  }

  // Profile panel — open/close
  document.querySelectorAll('[data-profile-btn]').forEach(btn => {
    btn.addEventListener('click', () => { S.profilePanel = !S.profilePanel; S.profileShowPw = false; S.editingProfile = false; render(); });
  });
  const profileClose = document.getElementById('profileClose');
  if (profileClose) profileClose.addEventListener('click', () => { S.profilePanel = false; S.editingProfile = false; render(); });
  const profileOverlay = document.getElementById('profileOverlay');
  if (profileOverlay) profileOverlay.addEventListener('click', () => { S.profilePanel = false; S.editingProfile = false; render(); });
  const profileLogout = document.getElementById('profileLogout');
  if (profileLogout) profileLogout.addEventListener('click', () => { sessionStorage.removeItem('nstp_role'); window.location.href = 'login.html'; });
  const profilePwToggle = document.getElementById('profilePwToggle');
  if (profilePwToggle) profilePwToggle.addEventListener('click', () => { S.profileShowPw = !S.profileShowPw; render(); });

  const editProfileBtn = document.getElementById('editProfileBtn');
  if (editProfileBtn) editProfileBtn.addEventListener('click', () => { S.editingProfile = true; render(); });
  const cancelProfileBtn = document.getElementById('cancelProfileBtn');
  if (cancelProfileBtn) cancelProfileBtn.addEventListener('click', () => { S.editingProfile = false; render(); });
  const saveProfileBtn = document.getElementById('saveProfileBtn');
  if (saveProfileBtn) {
    saveProfileBtn.addEventListener('click', () => {
      const newName = document.getElementById('editProfName')?.value || '';
      const newContact = document.getElementById('editProfContact')?.value || '';
      const newGmail = document.getElementById('editProfGmail')?.value || '';
      const newPassword = document.getElementById('editProfPassword')?.value || '';
      const newDegree = document.getElementById('editProfDegree')?.value || '';
      const newDegreeTitle = document.getElementById('editProfDegreeTitle')?.value || '';

      if (!newName.trim()) { alert('Full Name is required.'); return; }

      const pd = PROFILE_DATA[S.email] || PROFILE_DATA[S.role];
      if (pd) {
        const oldEmail = pd.gmail || S.email;
        pd.fullName = newName.trim();
        pd.contact = newContact.trim();
        pd.gmail = newGmail.trim();
        pd.password = newPassword.trim();
        pd.degree = newDegree;
        pd.degreeTitle = newDegreeTitle.trim();

        const cred = CREDENTIALS.find(c => c.email === oldEmail || c.email === S.email);
        if (cred) {
          cred.email = newGmail.trim();
          cred.password = newPassword.trim();
        }
        S.email = newGmail.trim();
      }

      S.editingProfile = false;
      render();
    });
  }

  // Notification bell â€” toggle slide-in panel
  const notifBellBtn = document.getElementById('notifBellBtn');
  if (notifBellBtn) notifBellBtn.addEventListener('click', () => { S.notifPanel = !S.notifPanel; render(); });
  const notifClose = document.getElementById('notifClose');
  if (notifClose) notifClose.addEventListener('click', () => { S.notifPanel = false; render(); });
  const notifOverlay = document.getElementById('notifOverlay');
  if (notifOverlay) notifOverlay.addEventListener('click', () => { S.notifPanel = false; render(); });
  const notifMarkAll = document.getElementById('notifMarkAll');
  if (notifMarkAll) notifMarkAll.addEventListener('click', () => { S.notifPanel = false; render(); });

  // Revision Modal logic
  const submitRevisionBtn = document.getElementById('submitRevisionBtn');
  if (submitRevisionBtn) {
    submitRevisionBtn.addEventListener('click', () => {
      const note = document.getElementById('revisionNoteArea').value.trim();
      if (!note) {
        alert('Please provide a revision note.');
        return;
      }

      const item = APPROVALS[S.selApproval || 0];
      if (item) {
        // Find matching plan in I_PLANS to simulate persistence
        const plan = I_PLANS.find(p => p.title.includes(item.title.replace(' Report', '').replace(' Activity', '')));
        if (plan) {
          plan.status = 'Revision';
          plan.feedback = note;
        }
      }

      alert('Revision request sent successfully!');
      APPROVALS.splice(S.selApproval || 0, 1);
      S.selApproval = 0;
      S.revisionModal = false;
      S.revisionNote = '';
      render();
    });
  }
  const revisionNoteArea = document.getElementById('revisionNoteArea');
  if (revisionNoteArea) {
    revisionNoteArea.addEventListener('input', (e) => {
      S.revisionNote = e.target.value;
    });
  }


  // Click an officer to see their assigned students
  document.querySelectorAll('[data-officer-row]').forEach(row => {
    row.addEventListener('click', () => {
      S.selectedOfficer = row.dataset.officerRow;
      render();
    });
  });
  const offModalClose = document.getElementById('officerStudentsClose');
  if (offModalClose) offModalClose.addEventListener('click', () => { S.selectedOfficer = null; render(); });
  const officerStudentsOverlay = document.getElementById('officerStudentsOverlay');
  if (officerStudentsOverlay) officerStudentsOverlay.addEventListener('click', e => { if (e.target === officerStudentsOverlay) { S.selectedOfficer = null; render(); } });

  const clearOfficerListBtn = document.getElementById('clearOfficerListBtn');
  if (clearOfficerListBtn) clearOfficerListBtn.addEventListener('click', () => {
    if (confirm("Are you sure you want to clear all students from this officer's list?")) {
      if (S.officerStudents) S.officerStudents[S.selectedOfficer] = [];
      render();
    }
  });

  const deleteOfficerSecBtn = document.getElementById('deleteOfficerSecBtn');
  if (deleteOfficerSecBtn) deleteOfficerSecBtn.addEventListener('click', () => {
    if (confirm("Are you sure you want to completely delete this officer and their assigned list?")) {
      const offIdx = R_ROSTER.findIndex(o => o.id === S.selectedOfficer);
      if (offIdx !== -1) R_ROSTER.splice(offIdx, 1);
      if (S.officerStudents) delete S.officerStudents[S.selectedOfficer];
      S.selectedOfficer = null;
      render();
    }
  });

  const officerFormClose = document.getElementById('officerFormClose');

  // Audit Logs — Export CSV & Filter Button Click
  const auditFilterBtn = document.getElementById('auditFilterBtn');
  if (auditFilterBtn) {
    auditFilterBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      S.showAuditFilterMenu = !S.showAuditFilterMenu;
      render();
    });
  }
  const auditSearchInput = document.getElementById('auditSearchInput');
  if (auditSearchInput) {
    auditSearchInput.addEventListener('input', (e) => {
      S.auditSearch = e.target.value;
      S.focusedFilterInputId = 'auditSearchInput';
      S.focusedFilterCursor = e.target.selectionStart;
      render();
    });
  }

  document.querySelectorAll('[data-audit-filter-opt]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      S.auditFilter = btn.dataset.auditFilterOpt;
      S.showAuditFilterMenu = false;
      render();
    });
  });

  // Student Archive Events
  const archiveSearchInput = document.getElementById('archiveSearchInput');
  if (archiveSearchInput) {
    archiveSearchInput.addEventListener('input', (e) => {
      S.archiveSearch = e.target.value;
      S.focusedFilterInputId = 'archiveSearchInput';
      S.focusedFilterCursor = e.target.selectionStart;
      render();
    });
  }

  const archiveExportBtn = document.getElementById('archiveExportBtn');
  if (archiveExportBtn) {
    archiveExportBtn.addEventListener('click', () => {
      const entries = S.studentArchive || [];
      if (!entries.length) { alert('No archived records to export.'); return; }
      let csv = 'Student No,Student Name,Gender,Section,Program,Instructor,Midterm Grade,Final Grade,Remarks,Date Archived\n';
      entries.forEach(st => {
        csv += `"${st.studentNo}","${st.name}","${st.gender}","${st.section}","${st.program}","${st.instructor}",${st.midtermGrade},${st.finalGrade},"${st.remarks}","${st.dateArchived}"\n`;
      });
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.setAttribute('download', `NSTP_Grades_Archive_${new Date().getFullYear()}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    });
  }

  // Sections & Students Filter
  const sectionsFilterBtn = document.getElementById('sectionsFilterBtn');
  if (sectionsFilterBtn) {
    sectionsFilterBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      S.showSectionsFilterMenu = !S.showSectionsFilterMenu;
      render();
    });
  }
  const sectionsSearchInput = document.getElementById('sectionsSearchInput');
  if (sectionsSearchInput) {
    sectionsSearchInput.addEventListener('input', (e) => {
      S.secSearch = e.target.value;
      S.focusedFilterInputId = 'sectionsSearchInput';
      S.focusedFilterCursor = e.target.selectionStart;
      render();
    });
  }

  document.querySelectorAll('[data-sections-filter-opt]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      S.secTab = btn.dataset.sectionsFilterOpt;
      S.showSectionsFilterMenu = false;
      render();
    });
  });

  // My Classes Filter
  const classesFilterBtn = document.getElementById('classesFilterBtn');
  if (classesFilterBtn) {
    classesFilterBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      S.showClassesFilterMenu = !S.showClassesFilterMenu;
      render();
    });
  }
  const classesSearchInput = document.getElementById('classesSearchInput');
  if (classesSearchInput) {
    classesSearchInput.addEventListener('input', (e) => {
      S.classesSearch = e.target.value;
      S.focusedFilterInputId = 'classesSearchInput';
      S.focusedFilterCursor = e.target.selectionStart;
      render();
    });
  }

  document.querySelectorAll('[data-classes-filter-opt]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      S.classesFilter = btn.dataset.classesFilterOpt;
      S.showClassesFilterMenu = false;
      render();
    });
  });

  // Assign Officer Section (Rosters) Filter
  const rostersFilterBtn = document.getElementById('rostersFilterBtn');
  if (rostersFilterBtn) {
    rostersFilterBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      S.showRostersFilterMenu = !S.showRostersFilterMenu;
      render();
    });
  }
  const rosterSearchInput = document.getElementById('rosterSearchInput');
  if (rosterSearchInput) {
    rosterSearchInput.addEventListener('input', (e) => {
      S.rosterSearch = e.target.value;
      S.focusedFilterInputId = 'rosterSearchInput';
      S.focusedFilterCursor = e.target.selectionStart;
      render();
    });
  }
  const rosterSearchBtn = document.getElementById('rosterSearchBtn');
  if (rosterSearchBtn) {
    rosterSearchBtn.addEventListener('click', () => {
      render();
    });
  }

  document.querySelectorAll('[data-rosters-filter-opt]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      S.rosterFilterPlatoon = btn.dataset.rostersFilterOpt;
      S.showRostersFilterMenu = false;
      render();
    });
  });

  // ROTC Calendar Filter
  const rCalFilterBtn = document.getElementById('rCalFilterBtn');
  if (rCalFilterBtn) {
    rCalFilterBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      S.showRCalFilterMenu = !S.showRCalFilterMenu;
      render();
    });
  }
  const rCalSearchInput = document.getElementById('rCalSearchInput');
  if (rCalSearchInput) {
    rCalSearchInput.addEventListener('input', (e) => {
      S.calSearch = e.target.value;
      S.focusedFilterInputId = 'rCalSearchInput';
      S.focusedFilterCursor = e.target.selectionStart;
      render();
    });
  }

  document.querySelectorAll('[data-rcal-filter-opt]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      S.rCalFilter = btn.dataset.rcalFilterOpt;
      S.showRCalFilterMenu = false;
      render();
    });
  });

  // Platoon Management Search and Filter
  const platFilterBtn = document.getElementById('platFilterBtn');
  if (platFilterBtn) {
    platFilterBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      S.showPlatoonFilterMenu = !S.showPlatoonFilterMenu;
      render();
    });
  }
  const platSearchInput = document.getElementById('platSearchInput');
  if (platSearchInput) {
    platSearchInput.addEventListener('input', (e) => {
      S.platSearch = e.target.value;
      S.focusedFilterInputId = 'platSearchInput';
      S.focusedFilterCursor = e.target.selectionStart;
      render();
    });
  }

  document.querySelectorAll('[data-plat-filter-opt]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      S.platoonFilter = btn.dataset.platFilterOpt;
      S.showPlatoonFilterMenu = false;
      render();
    });
  });

  // Keep focus on the active input and restore cursor position after DOM render updates
  if (S.focusedFilterInputId) {
    const inp = document.getElementById(S.focusedFilterInputId);
    if (inp) {
      inp.focus();
      const pos = S.focusedFilterCursor || inp.value.length;
      inp.setSelectionRange(pos, pos);
    }
    S.focusedFilterInputId = null;
    S.focusedFilterCursor = null;
  }

  if (!window.hasAuditFilterOutsideClickListener) {
    document.addEventListener('click', () => {
      let changed = false;
      if (S.showAuditFilterMenu) { S.showAuditFilterMenu = false; changed = true; }
      if (S.showSectionsFilterMenu) { S.showSectionsFilterMenu = false; changed = true; }
      if (S.showClassesFilterMenu) { S.showClassesFilterMenu = false; changed = true; }
      if (S.showRostersFilterMenu) { S.showRostersFilterMenu = false; changed = true; }
      if (S.showRCalFilterMenu) { S.showRCalFilterMenu = false; changed = true; }
      if (S.showPlatoonFilterMenu) { S.showPlatoonFilterMenu = false; changed = true; }
      if (changed) render();
    });
    window.hasAuditFilterOutsideClickListener = true;
  }

  const exportAuditCSV = document.getElementById('exportAuditCSV');
  if (exportAuditCSV) exportAuditCSV.addEventListener('click', () => {
    const logs = [
      { actor: 'Maya Reyes', action: 'Approved', target: 'Tree-Planting Drive Report', time: 'Today 11:24 AM', type: 'approval' },
      { actor: 'OCR Engine', action: 'Passed', target: 'BSCS-2A_Midterm.pdf (42 records)', time: 'Today 10:14 AM', type: 'system' },
      { actor: 'Maya Reyes', action: 'Generated', target: 'Spring 2026 — CWTS 1 batch (198 certs)', time: 'Today 9:30 AM', type: 'system' },
      { actor: 'Lester Tan', action: 'Submitted', target: 'Adult Literacy Session #4', time: 'Yesterday', type: 'submission' },
      { actor: 'System', action: 'Failed Login Attempt', target: 'instructor: r.cruz@aurora.edu', time: 'Yesterday', type: 'alert' },
      { actor: 'Maya Reyes', action: 'Updated section', target: 'BSIT-3A (added 2 students)', time: 'May 9', type: 'edit' },
      { actor: 'Adam Yusuf', action: 'Requested revisions', target: 'Barangay Clean-Up Plan', time: 'May 8', type: 'approval' },
    ];
    const activeFilter = S.auditFilter || 'all';
    const filteredLogs = activeFilter === 'all' ? logs : logs.filter(l => l.type === activeFilter);
    const esc = v => `"${String(v).replace(/"/g, '""')}"`;
    const header = ['#', 'Actor', 'Action', 'Target / Description', 'Timestamp', 'Category'];
    const rows = filteredLogs.map((l, i) => [i + 1, l.actor, l.action, l.target, l.time, l.type].map(esc).join(','));
    const csv = [header.join(','), ...rows].join('\r\n');
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `NSTP_Audit_Log_${activeFilter}_${new Date().toISOString().slice(0, 10)}.csv`;
    a.click();
    URL.revokeObjectURL(url);
  });

  // Login submit â€” credential-based role detection
  const loginBtn = document.getElementById('loginBtn');
  if (loginBtn) {
    loginBtn.addEventListener('click', () => {
      const email = (document.getElementById('loginEmail')?.value || '').trim().toLowerCase();
      const password = (document.getElementById('loginPassword')?.value || '').trim();
      const match = CREDENTIALS.find(
        c => c.email === email && c.password === password
      );
      if (match) {
        S.role = match.role;
        S.email = match.email;
        S.sidebarOpen = match.role !== 'admin';
        S.loginError = null;
        render();
      } else {
        S.loginError = 'Invalid email or password. Please try again.';
        render();
        document.getElementById('loginPassword')?.focus();
      }
    });
    // Allow Enter key on password field
    document.getElementById('loginPassword')?.addEventListener('keydown', e => {
      if (e.key === 'Enter') loginBtn.click();
    });
    document.getElementById('loginEmail')?.addEventListener('keydown', e => {
      if (e.key === 'Enter') document.getElementById('loginPassword')?.focus();
    });
  }

  // Logout
  document.querySelectorAll('[data-logout]').forEach(btn => {
    btn.addEventListener('click', () => {
      sessionStorage.removeItem('nstp_role'); window.location.href = 'login.html';
    });
  });

  // Nav items
  document.querySelectorAll('[data-nav]').forEach(btn => {
    btn.addEventListener('click', () => {
      const page = btn.dataset.nav;
      if (S.role === 'coordinator') S.coordPage = page;
      else if (S.role === 'instructor') S.instrPage = page;
      else if (S.role === 'admin') S.adminPage = page;
      else S.rotcPage = page;
      render();
    });
  });

  // Approval selection
  document.querySelectorAll('[data-approval]').forEach(el => {
    el.addEventListener('click', () => {
      S.selApproval = parseInt(el.dataset.approval);
      render();
    });
  });

  // Export Queue â†’ PDF download
  const exportQueueBtn = document.getElementById('exportQueueBtn');
  if (exportQueueBtn) exportQueueBtn.addEventListener('click', () => {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ unit: 'pt', format: 'a4' });
    const pageW = doc.internal.pageSize.getWidth();
    const now = new Date().toLocaleString();

    // Header
    doc.setFillColor(79, 70, 229);
    doc.rect(0, 0, pageW, 56, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text('NSTP â€” Pending Report Approvals', 40, 34);
    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    doc.text(`Exported: ${now}`, 40, 48);

    // Table header
    let y = 80;
    doc.setFontSize(9);
    doc.setFont('helvetica', 'bold');
    doc.setTextColor(100, 116, 139);
    doc.text('#', 40, y);
    doc.text('Report Title', 65, y);
    doc.text('Instructor', 280, y);
    doc.text('Section', 390, y);
    doc.text('Submitted', 470, y);
    doc.text('Priority', 540, y);
    y += 6;
    doc.setDrawColor(226, 232, 240);
    doc.line(40, y, pageW - 40, y);
    y += 14;

    // Rows
    doc.setFont('helvetica', 'normal');
    APPROVALS.forEach((a, i) => {
      doc.setTextColor(30, 41, 59);
      doc.text(String(i + 1), 40, y);
      doc.text(doc.splitTextToSize(a.title, 200)[0], 65, y);
      doc.text(a.instructor, 280, y);
      doc.text(a.section, 390, y);
      doc.text(a.submitted, 470, y);
      // Priority badge colour
      if (a.risk === 'Urgent') doc.setTextColor(220, 38, 38);
      else doc.setTextColor(100, 116, 139);
      doc.text(a.risk, 540, y);
      doc.setTextColor(30, 41, 59);
      y += 6;
      doc.setDrawColor(241, 245, 249);
      doc.line(40, y, pageW - 40, y);
      y += 14;
    });

    // Footer
    y += 10;
    doc.setFontSize(8);
    doc.setTextColor(148, 163, 184);
    doc.text(`Total: ${APPROVALS.length} item(s) pending review  |  Aurora University â€” NSTP Program Office`, 40, y);

    doc.save('NSTP_Approval_Queue.pdf');
  });

  // Certificate generation helper
  function generateCertPDF(batch) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
    const W = doc.internal.pageSize.getWidth();
    const H = doc.internal.pageSize.getHeight();

    // Outer border
    doc.setDrawColor(79, 70, 229);
    doc.setLineWidth(8);
    doc.rect(16, 16, W - 32, H - 32);
    doc.setDrawColor(199, 210, 254);
    doc.setLineWidth(2);
    doc.rect(24, 24, W - 48, H - 48);

    // Top indigo banner
    doc.setFillColor(79, 70, 229);
    doc.rect(24, 24, W - 48, 48, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(11);
    doc.setFont('helvetica', 'bold');
    doc.text('DAVAO DEL NORTE STATE COLLEGE', W / 2, 53, { align: 'center' });

    // Title
    doc.setTextColor(30, 41, 59);
    doc.setFontSize(30);
    doc.setFont('helvetica', 'bold');
    doc.text('CERTIFICATE OF COMPLETION', W / 2, 120, { align: 'center' });

    // Subtitle line
    doc.setFontSize(11);
    doc.setFont('helvetica', 'normal');
    doc.setTextColor(100, 116, 139);
    doc.text('National Service Training Program (NSTP)', W / 2, 142, { align: 'center' });

    // Decorative line
    doc.setDrawColor(199, 210, 254);
    doc.setLineWidth(1.5);
    doc.line(80, 154, W - 80, 154);

    // Body text
    doc.setTextColor(30, 41, 59);
    doc.setFontSize(13);
    doc.setFont('helvetica', 'normal');
    doc.text('This is to certify that all eligible students under', W / 2, 185, { align: 'center' });

    doc.setFontSize(20);
    doc.setFont('helvetica', 'bold');
    doc.setTextColor(79, 70, 229);
    doc.text(batch.name, W / 2, 215, { align: 'center' });

    doc.setFontSize(13);
    doc.setFont('helvetica', 'normal');
    doc.setTextColor(30, 41, 59);
    doc.text(`(${batch.count} students)  have successfully completed all requirements`, W / 2, 240, { align: 'center' });
    doc.text('of the National Service Training Program for Academic Year 2025 â€“ 2026.', W / 2, 260, { align: 'center' });

    // Bottom line
    doc.setDrawColor(199, 210, 254);
    doc.setLineWidth(1.5);
    doc.line(80, 285, W - 80, 285);

    // Signature blocks
    const sigY = 330;
    const cols = [W * 0.22, W * 0.5, W * 0.78];
    const labels = ['NSTP Coordinator', 'College President', 'Registrar'];
    cols.forEach((x, i) => {
      doc.setDrawColor(148, 163, 184);
      doc.setLineWidth(1);
      doc.line(x - 70, sigY, x + 70, sigY);
      doc.setFontSize(9);
      doc.setTextColor(100, 116, 139);
      doc.setFont('helvetica', 'bold');
      doc.text(labels[i], x, sigY + 14, { align: 'center' });
      doc.setFont('helvetica', 'normal');
      doc.text('Davao Del Norte State College', x, sigY + 26, { align: 'center' });
    });

    // Date + control no.
    doc.setFontSize(8);
    doc.setTextColor(148, 163, 184);
    const issued = new Date().toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });
    doc.text(`Issued: ${issued}`, 40, H - 34);
    doc.text(`Control No.: NSTP-${Date.now().toString().slice(-6)}`, W - 40, H - 34, { align: 'right' });

    const safeName = batch.name.replace(/[^a-z0-9]/gi, '_');
    doc.save(`Certificate_${safeName}.pdf`);
  }

  // Per-row Generate buttons â†’ open student modal
  document.querySelectorAll('[data-cert-batch]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      S.certModal = parseInt(btn.dataset.certBatch);
      render();
    });
  });

  // Batch card click → toggle delete button
  document.querySelectorAll('[data-batch-card]').forEach(card => {
    card.addEventListener('click', (e) => {
      if (e.target.closest('[data-delete-batch]') || e.target.closest('[data-cert-batch]')) return;
      const bi = parseInt(card.dataset.batchCard);
      S.selectedBatchIdx = S.selectedBatchIdx === bi ? null : bi;
      render();
    });
  });

  // Delete batch card
  document.querySelectorAll('[data-delete-batch]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const bi = parseInt(btn.dataset.deleteBatch);
      S.batches.splice(bi, 1);
      if (BATCH_STUDENTS[bi]) BATCH_STUDENTS.splice(bi, 1);
      S.selectedBatchIdx = null;
      render();
    });
  });

  // Cert modal close
  const certModalClose = document.getElementById('certModalClose');
  if (certModalClose) certModalClose.addEventListener('click', () => { S.certModal = null; render(); });
  const certModalOverlay = document.getElementById('certModalOverlay');
  if (certModalOverlay) certModalOverlay.addEventListener('click', e => { if (e.target === certModalOverlay) { S.certModal = null; render(); } });

  // Per-student certificate button
  function generateStudentCertPDF(studentName, batchIdx) {
    const batch = S.batches[batchIdx];
    if (!batch) return;
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
    const W = doc.internal.pageSize.getWidth();
    const H = doc.internal.pageSize.getHeight();
    // Border
    doc.setDrawColor(79, 70, 229); doc.setLineWidth(8); doc.rect(16, 16, W - 32, H - 32);
    doc.setDrawColor(199, 210, 254); doc.setLineWidth(2); doc.rect(24, 24, W - 48, H - 48);
    // Banner
    doc.setFillColor(79, 70, 229); doc.rect(24, 24, W - 48, 48, 'F');
    doc.setTextColor(255, 255, 255); doc.setFontSize(11); doc.setFont('helvetica', 'bold');
    doc.text('DAVAO DEL NORTE STATE COLLEGE', W / 2, 53, { align: 'center' });
    // Title
    doc.setTextColor(30, 41, 59); doc.setFontSize(28); doc.setFont('helvetica', 'bold');
    doc.text('CERTIFICATE OF COMPLETION', W / 2, 118, { align: 'center' });
    doc.setFontSize(11); doc.setFont('helvetica', 'normal'); doc.setTextColor(100, 116, 139);
    doc.text('National Service Training Program (NSTP)', W / 2, 140, { align: 'center' });
    doc.setDrawColor(199, 210, 254); doc.setLineWidth(1.5); doc.line(80, 152, W - 80, 152);
    // Body
    doc.setFontSize(12); doc.setTextColor(30, 41, 59); doc.setFont('helvetica', 'normal');
    doc.text('This is to certify that', W / 2, 182, { align: 'center' });
    doc.setFontSize(24); doc.setFont('helvetica', 'bold'); doc.setTextColor(79, 70, 229);
    doc.text(studentName, W / 2, 212, { align: 'center' });
    doc.setFontSize(12); doc.setFont('helvetica', 'normal'); doc.setTextColor(30, 41, 59);
    doc.text(`has successfully completed all requirements of the`, W / 2, 238, { align: 'center' });
    doc.setFontSize(14); doc.setFont('helvetica', 'bold'); doc.setTextColor(30, 41, 59);
    doc.text(`${batch.program} â€” ${batch.name}`, W / 2, 260, { align: 'center' });
    doc.setFontSize(12); doc.setFont('helvetica', 'normal');
    doc.text('for Academic Year 2025 â€“ 2026.', W / 2, 278, { align: 'center' });
    doc.setDrawColor(199, 210, 254); doc.setLineWidth(1.5); doc.line(80, 293, W - 80, 293);
    // Signatures
    const sigY = 338;
    [W * 0.22, W * 0.5, W * 0.78].forEach((x, i) => {
      const lbl = ['NSTP Coordinator', 'College President', 'Registrar'][i];
      doc.setDrawColor(148, 163, 184); doc.setLineWidth(1); doc.line(x - 70, sigY, x + 70, sigY);
      doc.setFontSize(9); doc.setTextColor(100, 116, 139);
      doc.setFont('helvetica', 'bold'); doc.text(lbl, x, sigY + 14, { align: 'center' });
      doc.setFont('helvetica', 'normal'); doc.text('Davao Del Norte State College', x, sigY + 26, { align: 'center' });
    });
    // Footer
    const issued = new Date().toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });
    doc.setFontSize(8); doc.setTextColor(148, 163, 184);
    doc.text(`Issued: ${issued}`, 40, H - 34);
    doc.text(`Control No.: NSTP-${Date.now().toString().slice(-6)}`, W - 40, H - 34, { align: 'right' });
    const safe = studentName.replace(/[^a-z0-9]/gi, '_');
    doc.save(`Certificate_${safe}.pdf`);
  }

  document.querySelectorAll('[data-student-cert]').forEach(btn => {
    btn.addEventListener('click', () => {
      const si = parseInt(btn.dataset.studentCert);
      const bi = parseInt(btn.dataset.batchIdx);
      const name = BATCH_STUDENTS[bi]?.[si];
      if (name) generateStudentCertPDF(name, bi);
    });
  });

  // Generate All from modal footer
  const certModalGenAll = document.getElementById('certModalGenAll');
  if (certModalGenAll) certModalGenAll.addEventListener('click', () => {
    const bi = S.certModal;
    if (bi === null) return;
    (BATCH_STUDENTS[bi] || []).forEach((name, i) => setTimeout(() => generateStudentCertPDF(name, bi), i * 300));
  });

  // Generate Batch (all) header button â†’ generate batch PDFs
  const generateBatchAllBtn = document.getElementById('generateBatchAllBtn');
  if (generateBatchAllBtn) generateBatchAllBtn.addEventListener('click', () => {
    S.batches.forEach((b, i) => setTimeout(() => generateCertPDF(b), i * 400));
  });

  // Recently Issued list item click → toggle delete button
  document.querySelectorAll('[data-recent-item]').forEach(item => {
    item.addEventListener('click', (e) => {
      if (e.target.closest('[data-delete-recent]')) return;
      const ri = parseInt(item.dataset.recentItem);
      S.selectedRecentCertIdx = S.selectedRecentCertIdx === ri ? null : ri;
      render();
    });
  });

  // Delete recent certificate
  document.querySelectorAll('[data-delete-recent]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const ri = parseInt(btn.dataset.deleteRecent);
      S.recentCerts.splice(ri, 1);
      S.selectedRecentCertIdx = null;
      render();
    });
  });

  // Certificates — Import XLSX: process files and add to S.batches + BATCH_STUDENTS
  const certXlsxBtn = document.getElementById('certXlsxBtn');
  const certXlsxInput = document.getElementById('certXlsxInput');
  if (certXlsxBtn) certXlsxBtn.addEventListener('click', () => certXlsxInput && certXlsxInput.click());
  if (certXlsxInput) {
    certXlsxInput.addEventListener('change', e => {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = evt => {
        try {
          const wb = XLSX.read(evt.target.result, { type: 'array' });
          const ws = wb.Sheets[wb.SheetNames[0]];
          const rows = XLSX.utils.sheet_to_json(ws, { defval: '' });
          if (!rows.length) { alert('No data rows found in the XLSX file.'); return; }
          
          const studentNames = [];
          let sectionCode = '';
          let programName = '';
          
          rows.forEach(row => {
            const getVal = (...keys) => {
              for (const k of keys) {
                const found = Object.keys(row).find(rk => rk.trim().toLowerCase() === k.toLowerCase());
                if (found !== undefined && row[found] !== '') return String(row[found]).trim();
              }
              return '';
            };
            
            const name = getVal('Name', 'Student Name', 'Full Name', 'Student_Name', 'Student');
            if (name) {
              studentNames.push(name);
            }
            
            if (!sectionCode) {
              sectionCode = getVal('Section Code', 'Section', 'Class', 'section');
            }
            if (!programName) {
              programName = getVal('Program', 'NSTP Program', 'Component', 'programme');
            }
          });
          
          if (!studentNames.length) {
            alert('No student names found in the XLSX file. Please ensure there is a "Name" or "Student Name" column.');
            return;
          }
          
          const fileNameNoExt = file.name.replace(/\.[^/.]+$/, "");
          const detectedSection = sectionCode || 'Imported';
          const detectedProgram = programName || (file.name.toUpperCase().includes('LTS') ? 'LTS' : file.name.toUpperCase().includes('ROTC') ? 'ROTC' : 'CWTS');
          
          const batchName = `${detectedProgram} Completion — ${detectedSection} (${fileNameNoExt})`;
          const newBatchIdx = S.batches.length;
          
          // Push to global student list
          BATCH_STUDENTS.push(studentNames);
          
          // Push to S.batches
          S.batches.push({
            name: batchName,
            count: studentNames.length,
            status: 'Ready',
            date: 'Eligible Today',
            program: detectedProgram
          });
          
          // Prepend to S.recentCerts for a premium view
          S.recentCerts.unshift({
            name: studentNames[0],
            program: detectedProgram,
            id: `2024-00${Math.floor(100 + Math.random() * 900)}`,
            issued: 'Just now'
          });
          
          // Set highlight index
          S.newlyImportedBatchIndex = newBatchIdx;
          
          // Clear highlight after 5 seconds
          setTimeout(() => {
            if (S.newlyImportedBatchIndex === newBatchIdx) {
              S.newlyImportedBatchIndex = null;
              render();
            }
          }, 5000);
          
          certXlsxInput.value = '';
          render();
          
          alert(`Successfully imported certificate batch: "${batchName}" with ${studentNames.length} students!`);
        } catch (err) {
          alert('Failed to read the XLSX file. Please make sure it is a valid Excel workbook.');
          console.error(err);
        }
      };
      reader.readAsArrayBuffer(file);
    });
  }

  // OCR Export XLSX â€” per processed upload
  document.querySelectorAll('[data-export-row]').forEach(btn => {
    btn.addEventListener('click', () => {
      const ri = parseInt(btn.dataset.exportRow);
      const upload = S.ocrUploads[ri];
      if (!upload) return;
      const sectionCode = upload.section;
      // Build student rows from SECTION_STUDENTS, falling back to generated names
      const knownStudents = SECTION_STUDENTS[sectionCode] || [];
      const wb = XLSX.utils.book_new();
      // Collect all sections to export (include all known sections for a full section export)
      const sectionsToExport = Object.keys(SECTION_STUDENTS).length
        ? Object.keys(SECTION_STUDENTS)
        : [sectionCode];
      sectionsToExport.forEach(sec => {
        const students = SECTION_STUDENTS[sec] || [];
        const rows = [['#', 'Student Name', 'Student No.', 'Section', 'Program', 'Status', 'Exported']];
        students.forEach((st, i) => {
          const sName = typeof st === 'string' ? st : st.name;
          const sNo = typeof st === 'string' ? 'â€”' : st.studentNo;
          const sProg = typeof st === 'string' ? sec.replace(/-\d+[A-Z]$/, '') : st.program;
          rows.push([i + 1, sName, sNo, sec, sProg, 'Passed', new Date().toLocaleDateString('en-PH')]);
        });
        const ws = XLSX.utils.aoa_to_sheet(rows);
        // Column widths
        ws['!cols'] = [{ wch: 4 }, { wch: 24 }, { wch: 12 }, { wch: 8 }, { wch: 10 }, { wch: 16 }];
        XLSX.utils.book_append_sheet(wb, ws, sec);
      });
      const fileName = `NSTP_Students_${sectionCode}_${Date.now().toString().slice(-6)}.xlsx`;
      XLSX.writeFile(wb, fileName);
    });
  });

  // OCR upload â€” file input & drag-drop
  const ocrDropZone = document.getElementById('ocrDropZone');
  const ocrFileInput = document.getElementById('ocrFileInput');
  function handleOCRFiles(files) {
    const allowed = ['application/pdf', 'image/png', 'image/jpeg'];
    const maxMB = 25;
    Array.from(files).forEach(file => {
      if (!allowed.includes(file.type)) {
        alert(`"${file.name}" is not supported. Please upload PDF, PNG, or JPG files.`);
        return;
      }
      if (file.size > maxMB * 1024 * 1024) {
        alert(`"${file.name}" exceeds the 25 MB limit.`);
        return;
      }
      const now = new Date();
      const timeStr = `Today ${now.getHours()}:${String(now.getMinutes()).padStart(2, '0')} ${now.getHours() >= 12 ? 'PM' : 'AM'}`;
      const entry = { file: file.name, section: 'â€”', students: 0, status: 'Processing', time: timeStr };
      S.ocrUploads.unshift(entry);
      render();
      // Simulate OCR processing: update to Processed after 2s
      setTimeout(() => {
        const idx = S.ocrUploads.indexOf(entry);
        if (idx !== -1) S.ocrUploads[idx] = { ...entry, status: 'Processed' };
        render();
      }, 2000);
    });
    // Reset input so same file can be re-selected
    if (ocrFileInput) ocrFileInput.value = '';
  }
  if (ocrDropZone) {
    ocrDropZone.addEventListener('click', () => ocrFileInput && ocrFileInput.click());
    ocrDropZone.addEventListener('dragover', e => { e.preventDefault(); ocrDropZone.classList.add('bg-indigo-100'); });
    ocrDropZone.addEventListener('dragleave', () => ocrDropZone.classList.remove('bg-indigo-100'));
    ocrDropZone.addEventListener('drop', e => { e.preventDefault(); ocrDropZone.classList.remove('bg-indigo-100'); handleOCRFiles(e.dataTransfer.files); });
  }
  if (ocrFileInput) ocrFileInput.addEventListener('change', e => handleOCRFiles(e.target.files));

  // Calendar form open
  const calOpen = document.getElementById('calFormOpen');
  if (calOpen) calOpen.addEventListener('click', () => { S.calForm = true; render(); });
  const calClose = document.getElementById('calFormClose');
  if (calClose) calClose.addEventListener('click', () => { S.calForm = false; render(); });
  const calCancel = document.getElementById('calCancel');
  if (calCancel) calCancel.addEventListener('click', () => { S.calForm = false; render(); });

  // Calendar submit all drafts
  const calSubmitAll = document.getElementById('calSubmitAll');
  if (calSubmitAll) calSubmitAll.addEventListener('click', () => {
    S.activities = S.activities.map(a => a.status === 'Draft' ? { ...a, status: 'Submitted' } : a);
    render();
  });

  // Calendar create
  const calCreate = document.getElementById('calCreate');
  if (calCreate) calCreate.addEventListener('click', () => {
    const title = document.getElementById('calTitle')?.value?.trim();
    const date = document.getElementById('calDate')?.value;
    if (!title) return;
    S.activities = [...S.activities, {
      title, date: date || 'TBD',
      time: document.getElementById('calTime')?.value || 'TBD',
      venue: document.getElementById('calVenue')?.value || 'TBD',
      scope: document.getElementById('calScope')?.value || 'All Programs',
      color: 'bg-violet-500', status: 'Draft'
    }];
    S.calForm = false;
    render();
  });

  // Calendar create & submit
  const calCS = document.getElementById('calCreateSubmit');
  if (calCS) calCS.addEventListener('click', () => {
    const title = document.getElementById('calTitle')?.value?.trim();
    if (!title) return;
    S.activities = [...S.activities, {
      title, date: document.getElementById('calDate')?.value || 'TBD',
      time: document.getElementById('calTime')?.value || 'TBD',
      venue: document.getElementById('calVenue')?.value || 'TBD',
      scope: document.getElementById('calScope')?.value || 'All Programs',
      color: 'bg-violet-500', status: 'Submitted'
    }];
    S.calForm = false;
    render();
  });

  // Import XLSX â€” read workbook and populate SECTIONS
  const importXlsxBtn = document.getElementById('importXlsxBtn');
  const xlsxImportInput = document.getElementById('xlsxImportInput');
  if (importXlsxBtn) importXlsxBtn.addEventListener('click', () => xlsxImportInput && xlsxImportInput.click());
  if (xlsxImportInput) xlsxImportInput.addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = evt => {
      try {
        const wb = XLSX.read(evt.target.result, { type: 'array' });
        let imported = 0;
        wb.SheetNames.forEach(sheetName => {
          const ws = wb.Sheets[sheetName];
          const rows = XLSX.utils.sheet_to_json(ws, { defval: '' });
          rows.forEach(row => {
            // Support flexible column names (case-insensitive, trimmed)
            const get = (...keys) => {
              for (const k of keys) {
                const found = Object.keys(row).find(rk => rk.trim().toLowerCase() === k.toLowerCase());
                if (found !== undefined && row[found] !== '') return String(row[found]).trim();
              }
              return '';
            };
            const code = get('Section Code', 'Section', 'Code', 'section_code');
            if (!code) return; // skip blank rows
            const program = get('Program', 'programme') || 'CWTS';
            const schoolYear = get('School Year', 'Year', 'school_year') || '2025-2026';
            const instructor = get('Instructor', 'Teacher', 'Faculty') || 'TBA';
            const room = get('Room', 'Venue', 'Location') || 'TBA';
            const students = parseInt(get('Students', 'Max Students', 'Enrollment')) || 0;
            const status = get('Status') || 'Active';
            const existing = SECTIONS.findIndex(s => s.code === code);
            const entry = { code, program, schoolYear, students, instructor, room, status };
            if (existing !== -1) {
              SECTIONS[existing] = entry; // update
            } else {
              SECTIONS.push(entry);        // add new
              imported++;
            }
          });
        });
        xlsxImportInput.value = '';
        render();
        alert(`XLSX imported successfully! ${imported} new section(s) added.`);
      } catch (err) {
        alert('Failed to read the XLSX file. Please make sure it is a valid Excel workbook.');
        console.error(err);
      }
    };
    reader.readAsArrayBuffer(file);
  });

  // Modal â€” Import XLSX: auto-fill form fields from first data row
  const modalXlsxBtn = document.getElementById('modalXlsxBtn');
  const modalXlsxInput = document.getElementById('modalXlsxInput');
  if (modalXlsxBtn) modalXlsxBtn.addEventListener('click', () => modalXlsxInput && modalXlsxInput.click());
  if (modalXlsxInput) modalXlsxInput.addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = evt => {
      try {
        const wb = XLSX.read(evt.target.result, { type: 'array' });
        const ws = wb.Sheets[wb.SheetNames[0]];
        const rows = XLSX.utils.sheet_to_json(ws, { defval: '' });
        if (!rows.length) { alert('No data rows found in the XLSX file.'); return; }
        const row = rows[0];
        // Flexible column name matching
        const get = (...keys) => {
          for (const k of keys) {
            const found = Object.keys(row).find(rk => rk.trim().toLowerCase() === k.toLowerCase());
            if (found !== undefined && row[found] !== '') return String(row[found]).trim();
          }
          return '';
        };
        const fill = (id, val) => { const el = document.getElementById(id); if (el && val) el.value = val; };
        fill('secCode', get('Section Code', 'Section', 'Code'));
        fill('secProgram', get('Program', 'Programme'));
        fill('secSchoolYear', get('School Year', 'Year', 'school_year'));
        fill('secInstructor', get('Instructor', 'Teacher', 'Faculty'));
        fill('secRoom', get('Room', 'Venue', 'Location'));
        modalXlsxInput.value = '';
        // Visual feedback on the button
        if (modalXlsxBtn) {
          modalXlsxBtn.textContent = 'âœ“ Fields filled from ' + file.name;
          modalXlsxBtn.classList.add('border-emerald-500', 'bg-emerald-100', 'text-emerald-800');
        }
      } catch (err) {
        alert('Could not read the XLSX file. Please make sure it is a valid Excel workbook.');
        console.error(err);
      }
    };
    reader.readAsArrayBuffer(file);
  });

  // New Section modal
  const newSectionBtn = document.getElementById('newSectionBtn');
  if (newSectionBtn) newSectionBtn.addEventListener('click', () => { S.sectionForm = true; render(); });
  const sectionFormClose = document.getElementById('sectionFormClose');
  if (sectionFormClose) sectionFormClose.addEventListener('click', () => { S.sectionForm = false; render(); });
  const sectionFormCancel = document.getElementById('sectionFormCancel');
  if (sectionFormCancel) sectionFormCancel.addEventListener('click', () => { S.sectionForm = false; render(); });
  const newSectionOverlay = document.getElementById('newSectionOverlay');
  if (newSectionOverlay) newSectionOverlay.addEventListener('click', e => { if (e.target === newSectionOverlay) { S.sectionForm = false; render(); } });
  const sectionFormCreate = document.getElementById('sectionFormCreate');
  if (sectionFormCreate) sectionFormCreate.addEventListener('click', () => {
    const code = document.getElementById('secCode')?.value?.trim();
    if (!code) { document.getElementById('secCode').focus(); return; }
    const instructor = document.getElementById('secInstructor')?.value?.trim() || 'TBA';
    const program = document.getElementById('secProgram')?.value || 'CWTS';
    const schoolYear = document.getElementById('secSchoolYear')?.value || '1st';
    const room = document.getElementById('secRoom')?.value?.trim() || 'TBA';
    const students = parseInt(document.getElementById('secStudents')?.value) || 0;
    SECTIONS.push({ code, program, schoolYear, students, instructor, room, status: 'Active' });
    S.sectionForm = false;
    render();
  });

  // Program tab buttons
  document.querySelectorAll('[data-sec-tab]').forEach(btn => {
    btn.addEventListener('click', () => { S.secTab = btn.dataset.secTab; S.selectedSection = null; render(); });
  });

  // Section row click â†’ show detail panel
  document.querySelectorAll('[data-sec-row]').forEach(row => {
    row.addEventListener('click', () => {
      S.selectedSection = S.selectedSection === row.dataset.secRow ? null : row.dataset.secRow;
      render();
    });
  });
  const secDetailClose = document.getElementById('secDetailClose');
  if (secDetailClose) secDetailClose.addEventListener('click', () => { S.selectedSection = null; render(); });
  const secDeleteBtn = document.getElementById('secDeleteBtn');
  if (secDeleteBtn) secDeleteBtn.addEventListener('click', () => {
    const code = S.selectedSection;
    if (!code) return;
    if (!confirm(`Delete section ${code}? This cannot be undone.`)) return;
    const idx = SECTIONS.findIndex(s => s.code === code);
    if (idx !== -1) SECTIONS.splice(idx, 1);
    S.selectedSection = null;
    render();
  });

  // Add Student to section
  const secAddStudentBtn = document.getElementById('secAddStudentBtn');
  if (secAddStudentBtn) secAddStudentBtn.addEventListener('click', () => {
    const nameInput = document.getElementById('secStudentName');
    const noInput = document.getElementById('secStudentNo');
    const progSel = document.getElementById('secStudentProgram');
    const name = nameInput?.value?.trim();
    const studentNo = noInput?.value?.trim();
    const program = progSel?.value || 'CWTS';
    if (!name) { nameInput?.focus(); return; }
    if (!studentNo) { noInput?.focus(); return; }
    const code = S.selectedSection;
    if (!code) return;
    if (!SECTION_STUDENTS[code]) SECTION_STUDENTS[code] = [];
    SECTION_STUDENTS[code].push({ name, studentNo, program });
    const sec = SECTIONS.find(s => s.code === code);
    if (sec) sec.students = SECTION_STUDENTS[code].length;
    nameInput.value = '';
    noInput.value = '';
    render();
  });

  // Allow pressing Enter in name or student no field to submit
  ['secStudentName', 'secStudentNo'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('keydown', e => {
      if (e.key === 'Enter') document.getElementById('secAddStudentBtn')?.click();
    });
  });

  window.saveInstructorEdits = function (idx) {
    const nameVal = document.getElementById('editInstName')?.value;
    const deptVal = document.getElementById('editInstDept')?.value;
    const sectionsVal = document.getElementById('editInstSections')?.value;
    const studentsVal = parseInt(document.getElementById('editInstStudents')?.value) || 0;
    const emailVal = document.getElementById('editInstEmail')?.value;
    const statusVal = document.getElementById('editInstStatus')?.value;

    if (!nameVal || !nameVal.trim()) { alert('Name is required.'); return; }

    S.instructors[idx] = {
      name: nameVal.trim(),
      dept: deptVal?.trim() || 'General',
      sections: sectionsVal?.trim() || '',
      students: studentsVal,
      email: emailVal?.trim() || '',
      status: statusVal
    };
    S.editingInstructor = false;
    render();
  };

  window.deleteInstructor = function (idx) {
    if (confirm('Are you sure you want to delete this personnel?')) {
      S.instructors.splice(idx, 1);
      S.selectedInstructorIndex = null;
      S.editingInstructor = false;
      render();
    }
  };

  window.saveActivityEdits = function (idx) {
    const titleVal = document.getElementById('editActTitle')?.value;
    const dateVal = document.getElementById('editActDate')?.value;
    const timeVal = document.getElementById('editActTime')?.value;
    const venueVal = document.getElementById('editActVenue')?.value;
    const scopeVal = document.getElementById('editActScope')?.value;
    const statusVal = document.getElementById('editActStatus')?.value;

    if (!titleVal || !titleVal.trim()) { alert('Activity title is required.'); return; }

    S.activities[idx] = {
      ...S.activities[idx],
      title: titleVal.trim(),
      date: dateVal?.trim() || 'TBD',
      time: timeVal?.trim() || 'TBD',
      venue: venueVal?.trim() || 'TBD',
      scope: scopeVal,
      status: statusVal
    };
    S.editingActivity = false;
    render();
  };

  window.deleteActivity = function (idx) {
    if (confirm('Are you sure you want to delete this activity?')) {
      S.activities.splice(idx, 1);
      S.selectedCalActivity = null;
      S.editingActivity = false;
      render();
    }
  };

  const inviteInstructorBtn = document.getElementById('inviteInstructorBtn');
  if (inviteInstructorBtn) inviteInstructorBtn.addEventListener('click', () => { S.inviteForm = true; render(); });
  const inviteFormClose = document.getElementById('inviteFormClose');
  if (inviteFormClose) inviteFormClose.addEventListener('click', () => { S.inviteForm = false; render(); });
  const inviteFormCancel = document.getElementById('inviteFormCancel');
  if (inviteFormCancel) inviteFormCancel.addEventListener('click', () => { S.inviteForm = false; render(); });
  const inviteOverlay = document.getElementById('inviteOverlay');
  if (inviteOverlay) inviteOverlay.addEventListener('click', e => { if (e.target === inviteOverlay) { S.inviteForm = false; render(); } });
  const inviteFormSend = document.getElementById('inviteFormSend');
  if (inviteFormSend) inviteFormSend.addEventListener('click', () => {
    const name = document.getElementById('invName')?.value?.trim();
    if (!name) { document.getElementById('invName').focus(); return; }
    const dept = document.getElementById('invDept')?.value?.trim() || 'General';
    const sections = document.getElementById('invSections')?.value?.trim() || '';
    const email = document.getElementById('invEmail')?.value?.trim() || '';
    S.instructors.push({ name, dept, sections, students: 0, status: 'Active', email });
    S.inviteForm = false;
    render();
  });

  // Platoon search
  const platSearch = document.getElementById('platSearch');
  if (platSearch) {
    platSearch.addEventListener('input', e => {
      S.platSearch = e.target.value;
      render();
    });
  }

  // Drag events for cadet cards
  document.querySelectorAll('[data-cadet-id]').forEach(card => {
    card.addEventListener('dragstart', e => {
      S.dragging = { id: card.dataset.cadetId, from: card.dataset.cadetFrom };
      e.dataTransfer.effectAllowed = 'move';
    });
    card.addEventListener('dragend', () => {
      S.dragging = null;
    });
  });

  // Instructor Student Modal
  document.querySelectorAll('[data-instr-sec]').forEach(card => {
    card.addEventListener('click', () => {
      S.instrSelectedSection = card.dataset.instrSec;
      render();
    });
  });
  const instrStudentModalClose = document.getElementById('instrStudentModalClose');
  if (instrStudentModalClose) instrStudentModalClose.addEventListener('click', () => { S.instrSelectedSection = null; render(); });
  const instrStudentModalOverlay = document.getElementById('instrStudentModalOverlay');
  if (instrStudentModalOverlay) instrStudentModalOverlay.addEventListener('click', e => { if (e.target === instrStudentModalOverlay) { S.instrSelectedSection = null; render(); } });

  // View Attachments Modal
  const viewAttachmentsBtn = document.getElementById('viewAttachmentsBtn');
  if (viewAttachmentsBtn) viewAttachmentsBtn.addEventListener('click', () => { S.showAttachmentsModal = true; render(); });
  const closeAttachmentsBtn = document.getElementById('closeAttachmentsBtn');
  if (closeAttachmentsBtn) closeAttachmentsBtn.addEventListener('click', () => { S.showAttachmentsModal = false; render(); });
  const attachmentsModalOverlay = document.getElementById('attachmentsModalOverlay');
  if (attachmentsModalOverlay) attachmentsModalOverlay.addEventListener('click', e => { if (e.target === attachmentsModalOverlay) { S.showAttachmentsModal = false; render(); } });

  // Activity Plans - Instructor
  const planFileInput = document.getElementById('planFileInput');
  const planFileLabel = document.getElementById('planFileLabel');
  if (planFileInput && planFileLabel) {
    planFileInput.addEventListener('change', (e) => {
      const count = e.target.files.length;
      planFileLabel.textContent = count > 0 ? `${count} file${count > 1 ? 's' : ''} selected` : 'Add File';
    });
  }

  window.editPlan = (index) => {
    S.editingPlanIndex = index;
    const plan = I_PLANS[index];
    const titleInput = document.getElementById('planTitleInput');
    if (titleInput) {
      titleInput.value = plan.title;
      titleInput.focus();
    }

    const dInput = document.getElementById('planDateInput');
    if (dInput && plan.date) {
      const d = new Date(plan.date);
      if (!isNaN(d)) dInput.value = d.toISOString().split('T')[0];
    }

    const durInput = document.getElementById('planDurationInput');
    if (durInput) durInput.value = parseInt(plan.duration) || 3;

    const secInput = document.getElementById('planSectionInput');
    if (secInput) {
      for (let i = 0; i < secInput.options.length; i++) {
        if (secInput.options[i].text.includes(plan.section)) {
          secInput.selectedIndex = i;
          break;
        }
      }
    }

    const objInput = document.getElementById('planObjInput');
    if (objInput) objInput.value = plan.desc || '';

    const submitBtn = document.getElementById('planSubmitBtn');
    if (submitBtn) submitBtn.innerHTML = "Update Activity";
  };

  const savePlan = (status) => {
    const titleInput = document.getElementById('planTitleInput');
    if (!titleInput || !titleInput.value.trim()) { alert('Please enter a title for the activity plan'); return; }

    const dateInput = document.getElementById('planDateInput');
    const durationInput = document.getElementById('planDurationInput');
    const sectionInput = document.getElementById('planSectionInput');

    let displayDate = dateInput.value;
    if (displayDate) {
      const d = new Date(displayDate);
      displayDate = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    } else {
      displayDate = 'TBD';
    }

    const planData = {
      title: titleInput.value.trim(),
      section: sectionInput.value.split(' · ')[0],
      date: displayDate,
      duration: durationInput.value + ' hrs',
      venue: 'TBD',
      status: status
    };

    if (S.editingPlanIndex !== null && S.editingPlanIndex !== undefined) {
      I_PLANS[S.editingPlanIndex] = planData;
      S.editingPlanIndex = null;
    } else {
      I_PLANS.unshift(planData);
    }

    render();
  };

  const planSaveDraftBtn = document.getElementById('planSaveDraftBtn');
  if (planSaveDraftBtn) planSaveDraftBtn.addEventListener('click', () => savePlan('Draft'));

  const planSubmitBtn = document.getElementById('planSubmitBtn');
  if (planSubmitBtn) planSubmitBtn.addEventListener('click', () => savePlan('Pending'));
}

/* ================================================================
   MAIN RENDER (multi-page: role is set from sessionStorage)
================================================================ */
function render() {
  const app = document.getElementById('app');
  if (S.role === 'coordinator') {
    app.innerHTML = renderCoordinator();
  } else if (S.role === 'instructor') {
    app.innerHTML = renderInstructor();
  } else if (S.role === 'admin') {
    app.innerHTML = renderAdmin();
  } else if (S.role === 'rotc') {
    app.innerHTML = renderROTC();
  }
  attachEvents();
}

