{{-- Shared visual system for Dashboard + Data Master pages. Scope: body.master-ui --}}
<style>
    body.master-ui {
        --mui-primary: #1462ad;
        --mui-primary-strong: #0f5496;
        --mui-primary-soft: #eaf2fb;
        --mui-surface: #ffffff;
        --mui-surface-soft: #f8fafc;
        --mui-line: #e6ebf2;
        --mui-line-soft: #f1f5f9;
        --mui-text: #0f172a;
        --mui-muted: #64748b;
        --mui-danger: #c0392b;
    }

    html.dark-mode body.master-ui {
        --mui-primary: #3b82f6;
        --mui-primary-strong: #60a5fa;
        --mui-primary-soft: rgba(96, 165, 250, 0.12);
        --mui-surface: #1e293b;
        --mui-surface-soft: #172033;
        --mui-line: rgba(148, 163, 184, 0.16);
        --mui-line-soft: rgba(148, 163, 184, 0.10);
        --mui-text: #e2e8f0;
        --mui-muted: #94a3b8;
        --mui-danger: #f87171;
    }

    body.master-ui .content-wrapper {
        overflow-x: clip;
    }

    body.master-ui .content-header h1 {
        font-size: 1.35rem !important;
        letter-spacing: -.01em;
    }

    /* ---------- Cards ---------- */
    body.master-ui .card {
        background: var(--mui-surface) !important;
        border: 1px solid var(--mui-line) !important;
        border-radius: 16px !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 18px 30px -28px rgba(15, 23, 42, .35) !important;
        overflow: hidden;
    }

    body.master-ui .card-body {
        padding: 20px 22px !important;
        background: transparent !important;
    }

    body.master-ui .card-header {
        background: var(--mui-surface) !important;
        color: var(--mui-text) !important;
        border: 0 !important;
        border-bottom: 1px solid var(--mui-line) !important;
        padding: 16px 22px !important;
        min-height: 68px;
    }

    body.master-ui .card-header h5,
    body.master-ui .card-header h3,
    body.master-ui .card-header .card-title {
        display: flex;
        align-items: center;
        margin: 0 !important;
        font-size: .98rem !important;
        font-weight: 700 !important;
        color: var(--mui-text) !important;
        letter-spacing: -.01em;
    }

    body.master-ui .card-header h5 > i,
    body.master-ui .card-header h3 > i,
    body.master-ui .card-header .card-title > i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 32px;
        width: 32px;
        height: 32px;
        margin-right: 10px !important;
        border-radius: 9px;
        background: var(--mui-primary-soft);
        color: var(--mui-primary) !important;
        font-size: .8rem;
    }

    body.master-ui .card-heading {
        min-width: 0;
    }

    body.master-ui .card-heading-sub {
        margin: 4px 0 0;
        font-size: .76rem;
        color: var(--mui-muted);
        line-height: 1.4;
    }

    body.master-ui .card-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-left: auto;
    }

    /* ---------- Buttons ---------- */
    body.master-ui .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: 36px;
        padding: 0 14px !important;
        border: 1px solid transparent;
        border-radius: 9px !important;
        font-size: .8rem !important;
        font-weight: 600 !important;
        line-height: 1;
        box-shadow: none !important;
        transition: background .15s ease, border-color .15s ease, color .15s ease, box-shadow .15s ease;
    }

    body.master-ui .btn-sm {
        height: 32px;
        padding: 0 12px !important;
        font-size: .78rem !important;
    }

    body.master-ui .btn i {
        font-size: .78rem;
    }

    body.master-ui .btn-primary {
        background: var(--mui-primary) !important;
        border-color: var(--mui-primary) !important;
        color: #ffffff !important;
    }

    body.master-ui .btn-primary:hover,
    body.master-ui .btn-primary:focus {
        background: var(--mui-primary-strong) !important;
        border-color: var(--mui-primary-strong) !important;
        box-shadow: 0 6px 14px -8px rgba(20, 98, 173, .8) !important;
    }

    body.master-ui .btn-secondary,
    body.master-ui .btn-outline-secondary {
        background: var(--mui-surface) !important;
        border-color: var(--mui-line) !important;
        color: var(--mui-muted) !important;
    }

    body.master-ui .btn-secondary:hover,
    body.master-ui .btn-outline-secondary:hover,
    body.master-ui .btn-secondary:focus,
    body.master-ui .btn-outline-secondary:focus {
        background: var(--mui-surface-soft) !important;
        border-color: var(--mui-line) !important;
        color: var(--mui-text) !important;
    }

    body.master-ui .btn-outline-primary {
        background: transparent !important;
        border-color: rgba(20, 98, 173, .35) !important;
        color: var(--mui-primary) !important;
    }

    html.dark-mode body.master-ui .btn-outline-primary {
        border-color: rgba(96, 165, 250, .35) !important;
    }

    body.master-ui .btn-outline-primary:hover,
    body.master-ui .btn-outline-primary:focus {
        background: var(--mui-primary-soft) !important;
        border-color: var(--mui-primary) !important;
        color: var(--mui-primary) !important;
    }

    body.master-ui .btn-danger {
        background: var(--mui-danger) !important;
        border-color: var(--mui-danger) !important;
        color: #ffffff !important;
    }

    body.master-ui .btn-danger:hover,
    body.master-ui .btn-danger:focus {
        background: #a93226 !important;
        border-color: #a93226 !important;
    }

    body.master-ui .btn-outline-danger {
        background: transparent !important;
        border-color: rgba(192, 57, 43, .35) !important;
        color: var(--mui-danger) !important;
    }

    body.master-ui .btn-outline-danger:hover,
    body.master-ui .btn-outline-danger:focus {
        background: rgba(192, 57, 43, .08) !important;
        border-color: var(--mui-danger) !important;
        color: var(--mui-danger) !important;
    }

    body.master-ui .btn-success {
        background: #16a34a !important;
        border-color: #16a34a !important;
        color: #ffffff !important;
    }

    body.master-ui .btn-success:hover,
    body.master-ui .btn-success:focus {
        background: #15803d !important;
        border-color: #15803d !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(22, 163, 74, .25) !important;
    }

    body.master-ui .btn-warning {
        background: #f59e0b !important;
        border-color: #f59e0b !important;
        color: #ffffff !important;
    }

    body.master-ui .btn-warning:hover,
    body.master-ui .btn-warning:focus {
        background: #d97706 !important;
        border-color: #d97706 !important;
        color: #ffffff !important;
    }

    body.master-ui .btn-info {
        background: transparent !important;
        border-color: rgba(20, 98, 173, .35) !important;
        color: var(--mui-primary) !important;
    }

    body.master-ui .btn-info:hover {
        background: var(--mui-primary-soft) !important;
        border-color: var(--mui-primary) !important;
        color: var(--mui-primary) !important;
    }

    body.master-ui .btn:disabled,
    body.master-ui .btn.disabled {
        opacity: .55;
        box-shadow: none !important;
    }

    /* ---------- Tables ---------- */
    body.master-ui .table {
        margin-bottom: 0;
        color: var(--mui-text);
        border-color: var(--mui-line-soft);
    }

    body.master-ui .table thead th {
        background: var(--mui-surface-soft) !important;
        color: var(--mui-muted) !important;
        border-top: 0 !important;
        border-bottom: 1px solid var(--mui-line) !important;
        padding: 12px 16px !important;
        font-size: .7rem !important;
        font-weight: 700 !important;
        letter-spacing: .06em;
        text-transform: uppercase;
        white-space: nowrap;
        vertical-align: middle;
    }

    body.master-ui .table thead tr:nth-child(2) th {
        background: var(--mui-surface) !important;
        padding: 6px 10px 12px !important;
        border-bottom: 1px solid var(--mui-line) !important;
        letter-spacing: 0;
        text-transform: none;
    }

    body.master-ui .column-search {
        height: 34px !important;
        padding: 6px 10px !important;
        font-size: .78rem !important;
        border-radius: 8px !important;
        border: 1px solid var(--mui-line) !important;
        background: var(--mui-surface-soft) !important;
        color: var(--mui-text) !important;
        box-shadow: none !important;
    }

    body.master-ui thead .column-search {
        min-width: 110px;
    }

    body.master-ui .column-search:focus {
        background: var(--mui-surface) !important;
        border-color: var(--mui-primary) !important;
        box-shadow: 0 0 0 3px rgba(20, 98, 173, .10) !important;
    }

    body.master-ui .table tbody td {
        padding: 12px 16px !important;
        font-size: .82rem !important;
        vertical-align: middle !important;
        border-bottom: 1px solid var(--mui-line-soft) !important;
        border-top: 0 !important;
        background: transparent !important;
        color: var(--mui-text) !important;
    }

    body.master-ui .table tbody tr:last-child td {
        border-bottom: 0 !important;
    }

    body.master-ui .table-striped tbody tr:nth-of-type(odd) {
        background: transparent !important;
    }

    body.master-ui .table-hover tbody tr:hover {
        background: var(--mui-surface-soft) !important;
    }

    body.master-ui .table a:not(.btn) {
        color: var(--mui-primary) !important;
        font-weight: 500;
        text-decoration: none;
    }

    body.master-ui .table a:not(.btn):hover {
        color: var(--mui-primary-strong) !important;
        text-decoration: underline;
    }

    body.master-ui .table .btn {
        height: 28px;
        padding: 0 9px !important;
        font-size: .72rem !important;
        border-radius: 7px !important;
    }

    body.master-ui .table .btn i {
        font-size: .7rem;
    }

    /* ---------- Badges ---------- */
    body.master-ui .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 9px;
        border: 1px solid transparent;
        border-radius: 999px;
        font-size: .66rem;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
    }

    body.master-ui .badge.bg-success,
    body.master-ui .badge.badge-success {
        background: #e8f7ef !important;
        border-color: #b9e6cb !important;
        color: #157347 !important;
    }

    body.master-ui .badge.bg-danger,
    body.master-ui .badge.badge-danger {
        background: #fdecec !important;
        border-color: #f5c6c2 !important;
        color: #b42318 !important;
    }

    html.dark-mode body.master-ui .badge.bg-success,
    html.dark-mode body.master-ui .badge.badge-success {
        background: rgba(34, 197, 94, .12) !important;
        border-color: rgba(34, 197, 94, .28) !important;
        color: #4ade80 !important;
    }

    html.dark-mode body.master-ui .badge.bg-danger,
    html.dark-mode body.master-ui .badge.badge-danger {
        background: rgba(248, 113, 113, .12) !important;
        border-color: rgba(248, 113, 113, .28) !important;
        color: #f87171 !important;
    }

    /* ---------- Pagination ---------- */
    body.master-ui .pagination {
        margin: 0;
    }

    body.master-ui .pagination .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        margin: 0 2px;
        border: 1px solid var(--mui-line) !important;
        border-radius: 8px !important;
        background: var(--mui-surface) !important;
        color: var(--mui-muted) !important;
        font-size: .78rem;
        font-weight: 600;
        box-shadow: none !important;
    }

    body.master-ui .pagination .page-item .page-link:hover {
        background: var(--mui-surface-soft) !important;
        color: var(--mui-text) !important;
    }

    body.master-ui .pagination .page-item.active .page-link {
        background: var(--mui-primary) !important;
        border-color: var(--mui-primary) !important;
        color: #ffffff !important;
    }

    body.master-ui .pagination .page-item.disabled .page-link {
        background: var(--mui-surface-soft) !important;
        color: var(--mui-muted) !important;
        opacity: .5;
    }

    body.master-ui .table-responsive > .d-flex.justify-content-between {
        padding-top: 14px;
    }

    /* ---------- Forms ---------- */
    body.master-ui .form-label {
        margin-bottom: 6px !important;
        font-size: .76rem !important;
        font-weight: 700 !important;
        color: var(--mui-muted) !important;
    }

    body.master-ui .form-control {
        height: auto !important;
        padding: 9px 12px !important;
        border: 1px solid var(--mui-line) !important;
        border-radius: 10px !important;
        background: var(--mui-surface) !important;
        color: var(--mui-text) !important;
        font-size: .82rem !important;
        box-shadow: none !important;
    }

    body.master-ui .form-control:focus {
        border-color: var(--mui-primary) !important;
        box-shadow: 0 0 0 3px rgba(20, 98, 173, .12) !important;
    }

    body.master-ui .form-control[readonly],
    body.master-ui .form-control:disabled {
        background: var(--mui-surface-soft) !important;
        color: var(--mui-muted) !important;
    }

    body.master-ui select.form-control {
        padding-right: 28px !important;
    }

    html.dark-mode body.master-ui .form-control option {
        background: var(--mui-surface);
        color: var(--mui-text);
    }

    body.master-ui .d-flex.gap-2 { gap: 8px; }
    body.master-ui .d-flex.gap-3 { gap: 16px; }
    body.master-ui .d-flex.gap-4 { gap: 24px; }

    body.master-ui .form-check {
        padding-left: 1.45rem;
    }

    body.master-ui .form-check-input {
        width: 15px;
        height: 15px;
        margin-top: .2em;
        accent-color: var(--mui-primary);
    }

    body.master-ui .form-check-label {
        font-size: .82rem;
        color: var(--mui-text);
    }

    body.master-ui .form-text,
    body.master-ui small.text-muted {
        font-size: .72rem !important;
        color: var(--mui-muted) !important;
    }

    body.master-ui #roleList,
    body.master-ui #prodiList,
    body.master-ui #prodiListTu {
        padding: 10px 12px !important;
        border: 1px solid var(--mui-line) !important;
        border-radius: 10px !important;
        background: var(--mui-surface) !important;
    }

    body.master-ui #formContainer form > .d-flex.justify-content-end:last-child {
        margin-top: 22px !important;
        padding-top: 16px;
        border-top: 1px solid var(--mui-line);
    }

    body.master-ui #formContainer .card-header h5 {
        font-size: .95rem !important;
    }

    /* ---------- Modals ---------- */
    body.master-ui .modal-content {
        border: 0;
        border-radius: 16px;
        background: var(--mui-surface);
        box-shadow: 0 24px 60px -20px rgba(2, 6, 23, .5);
    }

    body.master-ui .modal-header {
        padding: 16px 22px;
        border-bottom: 1px solid var(--mui-line);
    }

    body.master-ui .modal-title {
        font-size: .95rem;
        font-weight: 700;
        color: var(--mui-text);
    }

    body.master-ui .modal-body {
        color: var(--mui-text);
    }

    body.master-ui .modal-footer {
        padding: 14px 20px;
        border-top: 1px solid var(--mui-line);
    }

    /* ---------- Dashboard ---------- */
    body.master-ui .stat-grid {
        margin-bottom: 4px;
    }

    body.master-ui .stat-card {
        border-radius: 16px !important;
    }

    body.master-ui .stat-card .card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 20px !important;
    }

    body.master-ui .stat-label {
        margin: 0 0 6px;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--mui-muted);
    }

    body.master-ui .stat-value {
        margin: 0;
        font-size: 1.7rem;
        font-weight: 700;
        line-height: 1.1;
        color: var(--mui-text);
    }

    body.master-ui .stat-card .icon-bg {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--mui-primary-soft);
        color: var(--mui-primary);
        font-size: 1rem;
    }

    body.master-ui .chart-card .card-body {
        padding: 18px 22px 22px !important;
    }

    body.master-ui .chart-canvas {
        position: relative;
        height: 340px;
    }

    body.master-ui .table .btn-danger {
        background: transparent !important;
        border-color: rgba(192, 57, 43, .35) !important;
        color: var(--mui-danger) !important;
    }

    body.master-ui .table .btn-danger:hover {
        background: rgba(192, 57, 43, .08) !important;
        border-color: var(--mui-danger) !important;
    }

    /* ---------- ITB account lookup (Data User) ---------- */
    body.master-ui .itb-lookup-wrap {
        position: relative;
    }

    body.master-ui .itb-lookup-list {
        position: absolute;
        z-index: 1080;
        left: 0;
        right: 0;
        top: calc(100% + 4px);
        max-height: 280px;
        overflow-y: auto;
        margin: 0;
        padding: 4px;
        list-style: none;
        border: 1px solid var(--mui-line);
        border-radius: 10px;
        background: var(--mui-surface);
        box-shadow: 0 12px 28px -12px rgba(15, 23, 42, .25);
    }

    body.master-ui .itb-lookup-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 11px;
        border: 1px solid transparent;
        border-radius: 8px;
        font-size: .82rem;
        line-height: 1.35;
        cursor: pointer;
    }

    body.master-ui .itb-lookup-item + .itb-lookup-item {
        margin-top: 2px;
    }

    body.master-ui .itb-lookup-item:hover,
    body.master-ui .itb-lookup-item.active {
        background: var(--mui-primary-soft);
        border-color: rgba(20, 98, 173, .18);
    }

    body.master-ui .itb-lookup-item .avatar {
        display: flex;
        flex: 0 0 30px;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--mui-primary-soft);
        color: var(--mui-primary);
        font-size: .7rem;
        font-weight: 700;
    }

    body.master-ui .itb-lookup-item .body {
        flex: 1;
        min-width: 0;
    }

    body.master-ui .itb-lookup-item .title {
        font-weight: 600;
        color: var(--mui-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    body.master-ui .itb-lookup-item .meta {
        font-size: .74rem;
        color: var(--mui-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    body.master-ui .itb-lookup-item .badge-status {
        flex: 0 0 auto;
        padding: 3px 8px;
        border: 1px solid #b9e6cb;
        border-radius: 999px;
        background: #e8f7ef;
        color: #157347;
        font-size: .66rem;
        font-weight: 700;
    }

    html.dark-mode body.master-ui .itb-lookup-item .badge-status {
        border-color: rgba(34, 197, 94, .28);
        background: rgba(34, 197, 94, .12);
        color: #4ade80;
    }

    body.master-ui .itb-lookup-empty {
        padding: 12px;
        text-align: center;
        color: var(--mui-muted);
        font-size: .78rem;
    }

    /* ---------- Sidang & Report ---------- */
    body.master-ui #trackingCard .card-header {
        flex-wrap: wrap;
        gap: 12px;
    }

    body.master-ui .card-header .form-group {
        margin-bottom: 0 !important;
    }

    body.master-ui .card-header .form-control {
        height: 34px !important;
        padding: 4px 10px !important;
        font-size: .78rem !important;
        max-width: 340px;
    }

    body.master-ui .table-bordered {
        border: 1px solid var(--mui-line) !important;
    }

    body.master-ui .table-bordered > thead > tr > th,
    body.master-ui .table-bordered > thead > tr > td,
    body.master-ui .table-bordered > tbody > tr > th,
    body.master-ui .table-bordered > tbody > tr > td,
    body.master-ui .table-bordered > tfoot > tr > th,
    body.master-ui .table-bordered > tfoot > tr > td {
        border: 1px solid var(--mui-line-soft) !important;
    }

    body.master-ui #trackingCard table {
        width: 100% !important;
        table-layout: fixed !important;
    }

    body.master-ui #trackingCard thead .column-search {
        min-width: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        height: 32px !important;
        padding: 4px 8px !important;
        font-size: .74rem !important;
        border-radius: 8px !important;
        border: 1px solid var(--mui-line) !important;
        background: var(--mui-surface-soft) !important;
        color: var(--mui-text) !important;
        box-shadow: none !important;
    }

    body.master-ui #trackingCard thead select.column-search {
        padding: 3px 6px !important;
        font-size: .7rem !important;
    }

    body.master-ui #trackingCard thead .column-search:focus {
        background: var(--mui-surface) !important;
        border-color: var(--mui-primary) !important;
        box-shadow: 0 0 0 3px rgba(20, 98, 173, .10) !important;
    }

    body.master-ui #trackingCard .table thead tr:not(:last-child) th {
        background: var(--mui-surface-soft) !important;
        color: var(--mui-muted) !important;
        padding: 10px 6px !important;
        font-size: .7rem !important;
        font-weight: 700 !important;
        letter-spacing: .05em;
        text-transform: uppercase;
        white-space: normal !important;
        vertical-align: middle !important;
        border: 1px solid var(--mui-line-soft) !important;
    }

    body.master-ui #trackingCard .table thead tr:last-child th {
        background: var(--mui-surface) !important;
        padding: 6px 6px 10px !important;
        text-transform: none;
        letter-spacing: 0;
        vertical-align: middle !important;
        border: 1px solid var(--mui-line-soft) !important;
    }

    body.master-ui #trackingCard .table tbody td {
        padding: 10px 8px !important;
        font-size: .78rem !important;
        white-space: normal !important;
        vertical-align: middle !important;
        border: 1px solid var(--mui-line-soft) !important;
    }

    body.master-ui #trackingCard .table .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        font-size: .68rem;
        font-weight: 600;
        line-height: 1.25;
        border-radius: 9999px;
        cursor: pointer;
        text-align: center;
        white-space: normal;
        word-break: break-word;
        max-width: 100%;
        transition: transform .15s ease, filter .15s ease, box-shadow .15s ease;
    }

    body.master-ui #trackingCard .table .badge:hover {
        filter: brightness(0.92);
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(15, 23, 42, .12);
    }

    body.master-ui #tahapModal .modal-body {
        max-height: calc(100vh - 140px);
        overflow-y: auto;
    }

    body.master-ui #tahapModal .select2-dropdown {
        z-index: 1056;
    }

    body.master-ui #tahapModal .btn {
        border-radius: 8px !important;
        font-weight: 600 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    body.master-ui #tahapModal .btn-outline-primary {
        background: #ffffff !important;
        border: 1px solid var(--mui-primary) !important;
        color: var(--mui-primary) !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04) !important;
    }

    body.master-ui #tahapModal .btn-outline-primary:hover,
    body.master-ui #tahapModal .btn-outline-primary:focus {
        background: var(--mui-primary-soft) !important;
        color: var(--mui-primary-strong) !important;
        border-color: var(--mui-primary) !important;
    }

    html.dark-mode body.master-ui #tahapModal .btn-outline-primary {
        background: var(--mui-surface) !important;
        border-color: var(--mui-primary) !important;
        color: var(--mui-primary) !important;
    }

    html.dark-mode body.master-ui #tahapModal .btn-outline-primary:hover {
        background: var(--mui-primary-soft) !important;
    }

    body.master-ui #tahapModal .btn-outline-secondary {
        background: #ffffff !important;
        border: 1px solid var(--mui-line) !important;
        color: var(--mui-muted) !important;
    }

    body.master-ui #tahapModal .btn-outline-secondary:hover {
        background: var(--mui-surface-soft) !important;
        border-color: var(--mui-line) !important;
        color: var(--mui-text) !important;
    }

    html.dark-mode body.master-ui #tahapModal .btn-outline-secondary {
        background: var(--mui-surface) !important;
        border-color: var(--mui-line) !important;
        color: var(--mui-muted) !important;
    }

    html.dark-mode body.master-ui #tahapModal .btn-outline-secondary:hover {
        background: var(--mui-surface-soft) !important;
        color: var(--mui-text) !important;
    }

    body.master-ui #reportTable {
        table-layout: fixed !important;
        width: 100% !important;
    }

    body.master-ui #reportTable th,
    body.master-ui #reportTable td {
        padding: 8px !important;
        font-size: .74rem !important;
        white-space: normal !important;
        word-break: normal !important;
        overflow-wrap: normal !important;
        vertical-align: middle !important;
    }

    body.master-ui #reportTable thead th {
        font-size: .62rem !important;
        padding: 8px 6px !important;
        letter-spacing: .04em;
    }

    body.master-ui #reportTable td:nth-child(3),
    body.master-ui #reportTable td:nth-child(6) {
        word-break: break-all !important;
        font-size: .7rem !important;
    }

    body.master-ui #reportTable .badge {
        padding: 2px 6px;
        font-size: .58rem;
        line-height: 1.3;
        white-space: normal;
        word-break: normal;
        overflow-wrap: normal;
        text-align: left;
        max-width: 100%;
    }

    /* Status badge palette (bg-* from Bootstrap rows + legacy badge-* classes) */
    body.master-ui .badge.bg-secondary,
    body.master-ui .badge.badge-secondary {
        background: #eef2f6 !important;
        border-color: #d8e0e9 !important;
        color: #526274 !important;
    }

    body.master-ui .badge.bg-primary,
    body.master-ui .badge.badge-primary {
        background: #e8f1fb !important;
        border-color: #c3dbf3 !important;
        color: #1462ad !important;
    }

    body.master-ui .badge.bg-info,
    body.master-ui .badge.badge-info {
        background: #e8f6fb !important;
        border-color: #bfe4f2 !important;
        color: #0b6a8a !important;
    }

    body.master-ui .badge.bg-warning,
    body.master-ui .badge.badge-warning {
        background: #fdf5e3 !important;
        border-color: #f3ddab !important;
        color: #9a6700 !important;
    }

    body.master-ui .badge.bg-orange,
    body.master-ui .badge.badge-orange {
        background: #fdf0e6 !important;
        border-color: #f6d3b3 !important;
        color: #b45309 !important;
    }

    body.master-ui .badge.bg-purple,
    body.master-ui .badge.badge-purple {
        background: #f2edfc !important;
        border-color: #dcccf5 !important;
        color: #6d3bbf !important;
    }

    html.dark-mode body.master-ui .badge.bg-secondary,
    html.dark-mode body.master-ui .badge.badge-secondary {
        background: rgba(148, 163, 184, .12) !important;
        border-color: rgba(148, 163, 184, .28) !important;
        color: #cbd5e1 !important;
    }

    html.dark-mode body.master-ui .badge.bg-primary,
    html.dark-mode body.master-ui .badge.badge-primary {
        background: rgba(96, 165, 250, .14) !important;
        border-color: rgba(96, 165, 250, .3) !important;
        color: #93c5fd !important;
    }

    html.dark-mode body.master-ui .badge.bg-info,
    html.dark-mode body.master-ui .badge.badge-info {
        background: rgba(56, 189, 248, .12) !important;
        border-color: rgba(56, 189, 248, .3) !important;
        color: #7dd3fc !important;
    }

    html.dark-mode body.master-ui .badge.bg-warning,
    html.dark-mode body.master-ui .badge.badge-warning {
        background: rgba(245, 158, 11, .14) !important;
        border-color: rgba(245, 158, 11, .3) !important;
        color: #fbbf24 !important;
    }

    html.dark-mode body.master-ui .badge.bg-orange,
    html.dark-mode body.master-ui .badge.badge-orange {
        background: rgba(249, 115, 22, .14) !important;
        border-color: rgba(249, 115, 22, .3) !important;
        color: #fdba74 !important;
    }

    html.dark-mode body.master-ui .badge.bg-purple,
    html.dark-mode body.master-ui .badge.badge-purple {
        background: rgba(167, 139, 250, .14) !important;
        border-color: rgba(167, 139, 250, .3) !important;
        color: #c4b5fd !important;
    }

    body.master-ui .btn-light {
        background: var(--mui-surface) !important;
        border-color: var(--mui-line) !important;
        color: var(--mui-muted) !important;
    }

    body.master-ui .btn-light:hover,
    body.master-ui .btn-light:focus {
        background: var(--mui-surface-soft) !important;
        border-color: var(--mui-line) !important;
        color: var(--mui-text) !important;
    }

    body.master-ui .btn-dark {
        background: var(--mui-primary) !important;
        border-color: var(--mui-primary) !important;
        color: #ffffff !important;
    }

    body.master-ui .btn-dark:hover,
    body.master-ui .btn-dark:focus {
        background: var(--mui-primary-strong) !important;
        border-color: var(--mui-primary-strong) !important;
    }

    body.master-ui .modal-header {
        background: var(--mui-surface) !important;
        color: var(--mui-text) !important;
        border-bottom: 1px solid var(--mui-line) !important;
    }

    body.master-ui .modal-header .modal-title,
    body.master-ui .modal-header .close {
        color: var(--mui-text) !important;
        text-shadow: none !important;
    }

    body.master-ui .modal-header .close {
        opacity: .55;
    }

    body.master-ui .modal-header .close:hover {
        opacity: .9;
    }

    body.master-ui .nav-tabs {
        border-bottom: 0;
    }

    body.master-ui .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-radius: 8px;
        padding: 7px 12px;
        color: var(--mui-muted);
        font-size: .78rem;
        font-weight: 600;
    }

    body.master-ui .nav-tabs .nav-link.active {
        background: var(--mui-primary-soft);
        border-color: transparent;
        color: var(--mui-primary) !important;
    }

    /* ---------- Jadwal Sidang ---------- */
    body.master-ui .jadwal-calendar {
        table-layout: fixed;
    }

    body.master-ui .jadwal-calendar thead th {
        background: var(--mui-surface-soft) !important;
        color: var(--mui-muted) !important;
        padding: 10px 8px !important;
        font-size: .7rem !important;
        letter-spacing: .06em;
        text-transform: uppercase;
        border-color: var(--mui-line-soft) !important;
    }

    body.master-ui .jadwal-calendar tbody td {
        height: 140px;
        vertical-align: top;
        background: var(--mui-surface) !important;
        border-color: var(--mui-line-soft) !important;
    }

    body.master-ui .jadwal-calendar tbody td.is-empty {
        background: var(--mui-surface-soft) !important;
        opacity: .45;
    }

    body.master-ui .jadwal-calendar tbody td.is-today {
        background: var(--mui-primary-soft) !important;
        border: 2px solid var(--mui-primary) !important;
    }

    body.master-ui .jadwal-cell-day {
        font-size: 1rem;
        font-weight: 700;
        color: var(--mui-text);
    }

    body.master-ui .jadwal-event {
        display: block;
        margin-bottom: 4px;
        padding: 6px 8px;
        border-left: 3px solid var(--mui-primary);
        border-radius: 6px;
        background: var(--mui-surface-soft);
        color: var(--mui-text);
        font-size: .72rem;
        line-height: 1.35;
        text-align: left;
        cursor: pointer;
    }

    body.master-ui .jadwal-event:hover {
        background: var(--mui-primary-soft);
    }

    body.master-ui .jadwal-event strong {
        display: block;
        color: var(--mui-text);
    }

    body.master-ui .jadwal-event .text-muted {
        color: var(--mui-muted) !important;
        font-size: .68rem;
    }

    body.master-ui .jadwal-day-card {
        margin-bottom: 18px;
        border: 1px solid var(--mui-line) !important;
        border-radius: 14px !important;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(15, 23, 42, .05) !important;
    }

    body.master-ui .jadwal-day-head {
        padding: 12px 18px !important;
        border-bottom: 0 !important;
        color: #ffffff !important;
        font-size: .92rem !important;
        font-weight: 700 !important;
        letter-spacing: -.01em;
    }

    body.master-ui .jadwal-day-head-s1 {
        background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%) !important;
    }

    body.master-ui .jadwal-day-head-s2 {
        background: linear-gradient(135deg, #047857 0%, #10b981 100%) !important;
    }

    body.master-ui .jadwal-day-head-s3 {
        background: linear-gradient(135deg, #6d28d9 0%, #8b5cf6 100%) !important;
    }

    body.master-ui .jadwal-day-head-default {
        background: linear-gradient(135deg, #0f5496 0%, #1462ad 100%) !important;
    }

    html.dark-mode body.master-ui .jadwal-day-head-s1 {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%) !important;
    }

    html.dark-mode body.master-ui .jadwal-day-head-s2 {
        background: linear-gradient(135deg, #064e3b 0%, #059669 100%) !important;
    }

    html.dark-mode body.master-ui .jadwal-day-head-s3 {
        background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 100%) !important;
    }

    html.dark-mode body.master-ui .jadwal-day-head-default {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%) !important;
    }

    body.master-ui .jadwal-row {
        background: var(--mui-surface) !important;
        border-bottom: 1px solid var(--mui-line) !important;
        padding: 14px 18px;
    }

    body.master-ui .jadwal-row:last-child {
        border-bottom: 0 !important;
    }

    body.master-ui .jadwal-row-time {
        width: 170px;
        flex: 0 0 170px;
        padding-left: 0;
        padding-right: 18px;
        border-right: 2px solid var(--mui-primary-soft);
        margin-right: 18px;
    }

    body.master-ui .jadwal-row-time .time {
        font-size: .85rem;
        font-weight: 700;
        color: var(--mui-text);
    }

    body.master-ui .jadwal-row-time .room,
    body.master-ui .jadwal-row-time .unit {
        font-size: .75rem;
        color: var(--mui-muted);
    }

    body.master-ui .jadwal-row table {
        width: 100%;
        border: 0;
        font-size: .8rem;
        line-height: 1.5;
    }

    body.master-ui .jadwal-row table td {
        padding: 3px 6px 3px 0 !important;
        border: 0 !important;
        background: transparent !important;
        color: var(--mui-text) !important;
        font-size: .82rem !important;
        line-height: 1.5 !important;
    }

    body.master-ui .jadwal-row table td:first-child {
        color: var(--mui-muted) !important;
        font-weight: 600 !important;
    }

    body.master-ui #jadwalCard h4 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--mui-text);
    }

    /* Card view "Tabel" button */
    body.master-ui .jadwal-row .btn-outline-primary {
        white-space: nowrap;
    }

    @media (max-width: 576px) {
        body.master-ui .jadwal-row-time {
            width: 120px;
            flex: 0 0 120px;
            padding-right: 12px;
            margin-right: 12px;
        }

        body.master-ui .jadwal-row {
            padding: 12px 14px;
        }
    }

    /* ---------- Strata Color Coding ---------- */
    body.master-ui .jadwal-strata-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 11px;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .02em;
        white-space: nowrap;
    }

    body.master-ui .jadwal-event-strata {
        display: inline-block;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .03em;
        margin-bottom: 2px;
    }

    /* Card row strata left border */
    body.master-ui .jadwal-row[data-strata="s1"] {
        border-left: 4px solid #3b82f6 !important;
    }
    body.master-ui .jadwal-row[data-strata="s2"] {
        border-left: 4px solid #10b981 !important;
    }
    body.master-ui .jadwal-row[data-strata="s3"] {
        border-left: 4px solid #8b5cf6 !important;
    }

    /* Calendar event strata colors */
    body.master-ui .jadwal-event[data-strata="s1"] {
        border-left-color: #3b82f6 !important;
        background: #eff6ff;
    }
    body.master-ui .jadwal-event[data-strata="s2"] {
        border-left-color: #10b981 !important;
        background: #ecfdf5;
    }
    body.master-ui .jadwal-event[data-strata="s3"] {
        border-left-color: #8b5cf6 !important;
        background: #f5f3ff;
    }

    body.master-ui .jadwal-event[data-strata="s1"]:hover { background: #dbeafe; }
    body.master-ui .jadwal-event[data-strata="s2"]:hover { background: #d1fae5; }
    body.master-ui .jadwal-event[data-strata="s3"]:hover { background: #ede9fe; }

    /* Dark mode strata overrides */
    html.dark-mode body.master-ui .jadwal-event[data-strata="s1"] {
        border-left-color: #60a5fa !important;
        background: rgba(96,165,250,.12);
    }
    html.dark-mode body.master-ui .jadwal-event[data-strata="s2"] {
        border-left-color: #34d399 !important;
        background: rgba(52,211,153,.12);
    }
    html.dark-mode body.master-ui .jadwal-event[data-strata="s3"] {
        border-left-color: #a78bfa !important;
        background: rgba(167,139,250,.12);
    }
    html.dark-mode body.master-ui .jadwal-event[data-strata="s1"]:hover { background: rgba(96,165,250,.2); }
    html.dark-mode body.master-ui .jadwal-event[data-strata="s2"]:hover { background: rgba(52,211,153,.2); }
    html.dark-mode body.master-ui .jadwal-event[data-strata="s3"]:hover { background: rgba(167,139,250,.2); }

    html.dark-mode body.master-ui .jadwal-legend-item {
        background: transparent;
    }

    html.dark-mode body.master-ui .jadwal-strata-badge {
        background: transparent !important;
    }
</style>
