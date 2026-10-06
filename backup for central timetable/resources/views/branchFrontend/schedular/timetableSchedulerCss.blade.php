<style>

.class-card .teacher-select {
    flex: 1 !important;
    min-width: 0 !important;
    background: rgba(255, 255, 255, 0.32) !important;
    border: 2.5px solid rgba(255, 255, 255, 0.5) !important;
    backdropOutside: blur(12px);
    color: white !important;
    font-weight: 700 !important;
    padding: 12px 44px 12px 18px !important;
    border-radius: 14px !important;
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.2), 0 4px 15px rgba(30,64,175,0.2);
    appearance: none !important;
    cursor: pointer;
    transition: all 0.3s ease;

    /* Custom Arrow */
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23fff' viewBox='0 0 16 16'%3E%3Cpath d='M8 12l-5-5h10l-5 5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 14px;
}

.class-card .teacher-select:hover {
    background: rgba(255, 255, 255, 0.45) !important;
    transform: translateY(-2px);
}

.class-card .teacher-select:focus {
    outline: none !important;
    box-shadow: 0 0 0 4px rgba(96,165,250,0.5) !important;
}

/* Bootstrap کا گندا override ختم */
.teacher-select.form-select-custom {
    all: unset !important;
}
:root {
    --primary: #1e40af;
    --primary-light: #3b82f6;
    --accent: #60a5fa;
    --success: #10b981;
    --danger: #dc3545;
    --bg: #f9fafb;
    --card-bg: #ffffff;
    --text: #111827;
    --muted: #6b7280;
    --border: #e5e7eb;
    --shadow: rgba(30, 64, 175, 0.12);
}

/* ============== MAIN LAYOUT ============== */
.main-content {
    background: transparent;
    padding: 24px;
    border-radius: 14px;
    box-shadow: 0 4px 18px var(--shadow);
    border: 1px solid var(--primary);
}

.main-content h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 8px;
}

.main-content p {
    font-size: 15.5px;
    color: var(--primary);
    margin-bottom: 16px;
}

/* ============== CLASS CARD - MAIN CONTAINER ============== */
.class-card {
    border: 2px solid var(--primary);
    border-radius: 18px;
    background: var(--card-bg);
    box-shadow: 0 8px 26px var(--shadow);
    transition: all 0.3s ease;
    overflow: visible;
    position: relative;
}

.class-card:hover {
    transform: translateY(-10px) scale(1.018);
    box-shadow: 0 18px 40px rgba(30, 64, 175, 0.24);
    border-color: var(--primary-light);
    z-index: 10;
}

/* ============== CARD HEADER - SAME LINE GUARANTEED ============== */
.class-card .card-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 16px !important;
    flex-wrap: nowrap !important;
    padding: 16px 20px !important;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: white !important;
    border-radius: 16px 16px 0 0 !important;
    min-height: 62px !important;
    width: 100% !important;
    box-sizing: border-box !important;
    position: relative;
    z-index: 2;
}

/* ============== TEACHER DROPDOWN - PROFESSIONAL LOOK ============== */
.class-card .teacher-select {
    flex: 1 !important;
    min-width: 0 !important;

    /* Premium glassmorphism dropdown */
    background: rgba(255, 255, 255, 0.30) !important;
    border: 2.5px solid rgba(255, 255, 255, 0.5) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    color: white !important;
    font-weight: 700 !important;
    font-size: 0.95rem !important;
    padding: 12px 44px 12px 18px !important;
    border-radius: 14px !important;
    box-shadow:
        inset 0 2px 10px rgba(0,0,0,0.2),
        0 4px 15px rgba(30, 64, 175, 0.15);
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    appearance: none !important;
    cursor: pointer;
    transition: all 0.35s ease;
}

/* Custom arrow */
.class-card .teacher-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' fill='%23ffffff' viewBox='0 0 16 16'%3E%3Cpath d='M8 12l-5-5h10l-5 5z' fill-rule='evenodd'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 16px;
}

/* Hover & Focus */
.class-card .teacher-select:hover {
    background: rgba(255, 255, 255, 0.42) !important;
    border-color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 0 20px rgba(255,255,255,0.5);
}

.class-card .teacher-select:focus {
    outline: none !important;
    box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.6) !important;
    border-color: white !important;
}

/* ============== DELETE BUTTON - DUSTBIN vs CROSS ============== */
.class-card .delete-class-btn {
    flex-shrink: 0 !important;
    width: 50px !important;
    height: 50px !important;
    background: var(--danger) !important;
    color: white !important;
    border: none !important;
    border-radius: 16px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 22px !important;
    cursor: pointer !important;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: 0 6px 18px rgba(220, 53, 69, 0.5);
    position: relative;
    z-index: 10;
}

.class-card .delete-class-btn:hover {
    background: #c82333 !important;
    transform: scale(1.22) rotate(5deg) !important;
    box-shadow: 0 12px 30px rgba(220, 53, 69, 0.7);
}

/* Icons Control */
.delete-class-btn .fa-trash-alt { display: block; }
.delete-class-btn .fa-times { display: none; }

/* Existing class = Cross */
.class-card[data-existing="true"] .delete-class-btn .fa-times {
    display: block !important;
}
.class-card[data-existing="true"] .delete-class-btn .fa-trash-alt {
    display: none !important;
}

/* ============== TABLE STYLING ============== */
.class-card .table-responsive {
    flex-grow: 1;
    max-height: 420px;
    overflow-y: auto;
    padding: 8px 0;
}

.class-card table {
    margin: 0;
    width: 100% !important;
    font-size: 13px;
    border-collapse: separate;
    border-spacing: 0 8px;
    table-layout: auto;
}

.class-card table th {
    background: #e0f2fe;
    color: var(--primary);
    font-weight: 700;
    font-size: 12.8px;
    text-align: center;
    padding: 14px 10px;
    position: sticky;
    top: 0;
    z-index: 2;
    border-bottom: 3px solid var(--primary);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.class-card table td {
    padding: 10px 8px;
    vertical-align: middle;
    background: #fff;
    border: 1.8px solid #e2e8f0;
    font-size: 13px;
    border-radius: 8px;
}

/* ============== MOBILE RESPONSIVE ============== */
@media (max-width: 768px) {
    .row-cols-md-3 > * {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (max-width: 576px) {
    .main-content {
        padding: 16px;
    }

    .class-card .card-header {
        padding: 12px 14px !important;
        gap: 12px !important;
        min-height: 56px !important;
    }

    .class-card .teacher-select {
        font-size: 0.88rem !important;
        padding: 10px 38px 10px 14px !important;
    }

    .class-card .delete-class-btn {
        width: 44px !important;
        height: 44px !important;
        font-size: 19px !important;
    }

    .row-cols-md-3 > * {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (max-width: 400px) {
    .class-card .teacher-select {
        font-size: 0.82rem !important;
    }
}

/* ============== FORCE OVERRIDE - KOI CSS NA BACHE ============== */
* { box-sizing: border-box !important; }
.class-card .card-header > * { margin: 0 !important; }
select { -webkit-appearance: none !important; }
</style>
