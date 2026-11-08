{{-- Gaya tombol aksi & penanda status perpustakaan (mandiri, tidak bergantung pada build Tailwind) --}}
<style>
    .lib-actions { display: flex; flex-direction: row; flex-wrap: nowrap; gap: .5rem; align-items: center; justify-content: center; }
    .lib-actions > * { flex: 0 0 auto; }
    .lib-actions form { display: inline-flex; margin: 0; }
    td.lib-col-actions { white-space: nowrap; width: 1%; }
    .lib-btn { display: inline-flex; align-items: center; gap: .3rem; padding: .32rem .75rem; border-radius: .4rem;
        font-size: .75rem; font-weight: 600; line-height: 1.1; border: 1px solid transparent; cursor: pointer;
        text-decoration: none; background: #fff; white-space: nowrap; transition: background .15s, color .15s; }
    .lib-btn-edit    { color: #1d4ed8; border-color: #93c5fd; background: #eff6ff; }
    .lib-btn-edit:hover    { background: #1d4ed8; color: #fff; }
    .lib-btn-danger  { color: #b91c1c; border-color: #fca5a5; background: #fef2f2; }
    .lib-btn-danger:hover  { background: #b91c1c; color: #fff; }
    .lib-btn-success { color: #047857; border-color: #6ee7b7; background: #ecfdf5; }
    .lib-btn-success:hover { background: #047857; color: #fff; }
    .lib-btn-primary { color: #fff; background: linear-gradient(135deg, #6366f1, #4f46e5); border-color: #4f46e5; padding: .5rem 1rem; font-size: .85rem; box-shadow: 0 1px 2px rgba(79,70,229,.25); }
    .lib-btn-primary:hover { background: linear-gradient(135deg, #4f46e5, #4338ca); }
    .lib-btn-ghost   { color: #374151; border-color: #d1d5db; background: #fff; padding: .5rem 1rem; font-size: .85rem; }
    .lib-btn-ghost:hover   { background: #f3f4f6; }
    .lib-badge { white-space: nowrap; display: inline-block; padding: .15rem .6rem; border-radius: 9999px; font-size: .72rem; font-weight: 600; }
    .lib-badge-green { background: #d1fae5; color: #065f46; }
    .lib-badge-blue  { background: #dbeafe; color: #1e40af; }
    .lib-badge-red   { background: #fee2e2; color: #991b1b; }
    .lib-badge-amber { background: #fef3c7; color: #92400e; }
    .lib-badge-gray  { background: #e5e7eb; color: #374151; }

    /* ---------- tata letak halaman ---------- */
    .lib-wrap { margin: 0 auto; padding: 0 1rem; }
    .lib-w-2xl { max-width: 42rem; } .lib-w-3xl { max-width: 48rem; } .lib-w-5xl { max-width: 64rem; }
    .lib-card { background: #fff; border-radius: .75rem; box-shadow: 0 1px 3px rgba(17,24,39,.08); padding: 1.5rem; }

    /* ---------- form ---------- */
    .lib-form > div { margin-bottom: 1.15rem; }
    .lib-form label, .lib-filter label.lib-lbl { display: block; font-weight: 600; font-size: .82rem; color: #374151; margin-bottom: .35rem; }
    .lib-form input[type=text], .lib-form input[type=email], .lib-form input[type=number], .lib-form select, .lib-form textarea,
    .lib-filter input[type=text], .lib-filter select {
        width: 100%; box-sizing: border-box; border: 1px solid #d1d5db; border-radius: .5rem; padding: .55rem .8rem;
        font-size: .9rem; background: #fff; color: #111827; box-shadow: none; }
    .lib-form input:focus, .lib-form select:focus, .lib-form textarea:focus,
    .lib-filter input:focus, .lib-filter select:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.2); }
    .lib-form select[multiple] { min-height: 9.5rem; padding: .4rem; }
    .lib-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .lib-hint { font-size: .75rem; color: #6b7280; margin-top: .3rem; }
    .lib-item-row { display: grid; grid-template-columns: 1fr 5.5rem; gap: .6rem; margin-bottom: .6rem; }
    .lib-form-actions { display: flex; gap: .75rem; align-items: center; padding-top: 1.1rem; margin-top: .4rem; border-top: 1px solid #e5e7eb; }
    .lib-section-title { font-size: .72rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #4f46e5; margin-bottom: .6rem; }

    /* ---------- filter ---------- */
    .lib-filter { display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; margin-bottom: 1rem; padding: 1rem 1.25rem; }
    .lib-filter > div { min-width: 13rem; }
    .lib-filter .lib-check { display: inline-flex; align-items: center; gap: .45rem; font-size: .85rem; color: #374151; padding-bottom: .55rem; }
    .lib-tabs { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1rem; }
    .lib-tab { padding: .4rem .95rem; border-radius: 9999px; font-size: .8rem; font-weight: 600; color: #4338ca; background: #eef2ff; text-decoration: none; border: 1px solid #c7d2fe; }
    .lib-tab:hover { background: #e0e7ff; }
    .lib-tab-active { background: #4f46e5; color: #fff; border-color: #4f46e5; }

    /* ---------- tabel ---------- */
    .lib-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .85rem; border: 1px solid #c7d2fe; border-radius: .6rem; overflow: hidden; background: #fff; }
    .lib-table thead th { background: linear-gradient(135deg, #eef2ff, #e0e7ff); color: #3730a3; font-weight: 700; font-size: .72rem;
        letter-spacing: .05em; text-transform: uppercase; padding: .8rem 1rem; text-align: left; border-bottom: 2px solid #c7d2fe; }
    .lib-table td { padding: .75rem 1rem; border-bottom: 1px solid #eef0f6; vertical-align: middle; text-align: left; color: #374151; }
    .lib-table tbody tr:nth-child(even) { background: #fafaff; }
    .lib-table tbody tr:hover { background: #eef2ff; }
    .lib-table tbody tr:last-child td { border-bottom: 0; }
    .lib-table th.text-center, .lib-table td.text-center { text-align: center; }
    .lib-table th.text-right, .lib-table td.text-right { text-align: right; }
    .lib-table td.lib-nowrap { white-space: nowrap; }
    .lib-table a.lib-link { color: #4338ca; font-weight: 600; text-decoration: none; }
    .lib-table a.lib-link:hover { text-decoration: underline; }

    /* ---------- pagination ---------- */
    .lib-pager { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: .75rem; margin-top: 1.1rem; }
    .lib-pager-info { font-size: .8rem; color: #6b7280; }
    .lib-pager-list { display: flex; gap: .3rem; list-style: none; margin: 0; padding: 0; }
    .lib-pager-list a, .lib-pager-list span { display: inline-flex; align-items: center; justify-content: center; min-width: 2.1rem; height: 2.1rem;
        padding: 0 .55rem; border-radius: .5rem; font-size: .82rem; font-weight: 600; text-decoration: none; border: 1px solid #c7d2fe; background: #fff; color: #4338ca; box-sizing: border-box; }
    .lib-pager-list a:hover { background: #eef2ff; }
    .lib-pager-list .active span { background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; border-color: #4f46e5; }
    .lib-pager-list .disabled span { color: #9ca3af; background: #f9fafb; border-color: #e5e7eb; }
</style>
