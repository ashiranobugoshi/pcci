@extends('layouts.admin')

@section('title', 'Board of Trustees - PCCI Admin')

@section('content')

@include('partials.api-config')

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800;900&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  *,
  *::before,
  *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  :root {
    --red: #be1e38;
    --red-dark: #7a1420;
    --red-mid: #b52233;
    --bg: #f3f4f6;
    --white: #ffffff;
    --border: #e2e4e8;
    --text: #1c1c1e;
    --muted: #6b7280;
    --light-muted: #9ca3af;
    --tag-bg: #efefef;
    --tag-text: #555;
  }

  /* PAGE WRAPPER */
  .ev-page {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: var(--bg);
  }

  /* HEADER BANNER */
  .ev-header {
    background: var(--red);
    padding: 36px 40px;
    flex-shrink: 0;
    border-radius: 10px;
  }

  .ev-header h1 {
    font-size: 2rem;
    font-weight: 900;
    color: #fff;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin: 0;
    line-height: 1.2;
  }

  /* TOOLBAR */
  .ev-toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 18px 32px;
    background: var(--bg);
    flex-shrink: 0;
  }

  .ev-search {
    display: flex;
    align-items: center;
    gap: 9px;
    background: var(--white);
    border: 1.5px solid var(--border);
    border-radius: 7px;
    padding: 9px 15px;
    width: 300px;
    transition: border-color .2s;
  }

  .ev-search:focus-within {
    border-color: var(--red);
  }

  .ev-search i {
    color: var(--light-muted);
    font-size: 13px;
  }

  .ev-search input {
    border: none;
    outline: none;
    background: transparent;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    color: var(--text);
    width: 100%;
    letter-spacing: .4px;
    text-transform: uppercase;
  }

  .ev-search input::placeholder {
    color: var(--light-muted);
    text-transform: uppercase;
  }

  .ev-add-btn {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--red);
    color: #fff;
    border: none;
    border-radius: 7px;
    padding: 10px 20px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    cursor: pointer;
    transition: background .2s;
  }

  .ev-add-btn:hover {
    background: var(--red-dark);
  }

  /* CARDS AREA */
  .ev-body {
    flex: 1;
    overflow-y: auto;
    padding: 24px 32px 32px;
  }

  .ev-body::-webkit-scrollbar {
    width: 5px;
  }

  .ev-body::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 10px;
  }

  .ev-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(286px, 1fr));
    gap: 20px;
  }

  /* CARD */
  .ev-card {
    background: var(--white);
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid var(--border);
    box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
    display: flex;
    flex-direction: column;
    transition: transform .2s, box-shadow .2s;
    animation: cardIn .35s ease both;
  }

  .ev-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(155, 27, 42, .12);
  }

  @keyframes cardIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .ev-card:nth-child(1) {
    animation-delay: .04s;
  }

  .ev-card:nth-child(2) {
    animation-delay: .08s;
  }

  .ev-card:nth-child(3) {
    animation-delay: .12s;
  }

  .ev-card:nth-child(4) {
    animation-delay: .16s;
  }

  .ev-card:nth-child(5) {
    animation-delay: .20s;
  }

  .ev-card:nth-child(6) {
    animation-delay: .24s;
  }

  .ev-card-img {
    width: 100%;
    height: 240px;
    object-fit: cover;
    object-position: top center;
    display: block;
  }

  .ev-card-img-placeholder {
    width: 100%;
    height: 240px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
  }

  .ev-card-img-placeholder.c1 {
    background: linear-gradient(135deg, #c0392b, #7d1a1a);
  }

  .ev-card-img-placeholder.c2 {
    background: linear-gradient(135deg, #8e44ad, #4a235a);
  }

  .ev-card-img-placeholder.c3 {
    background: linear-gradient(135deg, #1a6691, #0d3349);
  }

  .ev-card-img-placeholder.c4 {
    background: linear-gradient(135deg, #27ae60, #145a32);
  }

  .ev-card-img-placeholder.c5 {
    background: linear-gradient(135deg, #e67e22, #784212);
  }

  .ev-card-img-placeholder.c6 {
    background: linear-gradient(135deg, #2c3e50, #0d1117);
  }

  .ev-card-img-placeholder .ph-icon {
    font-size: 38px;
    z-index: 1;
    opacity: .85;
    color: #fff;
  }

  .ev-card-img-placeholder::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, .08);
  }

  .ev-card-body {
    padding: 14px 15px 10px;
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .ev-card-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 9px;
  }

  .ev-tag {
    background: var(--tag-bg);
    color: var(--tag-text);
    font-family: 'DM Sans', sans-serif;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 11px;
    border-radius: 20px;
    letter-spacing: .3px;
  }

  .ev-card-title {
    font-family: 'Poppins', sans-serif;
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text);
    line-height: 1.3;
    margin-bottom: 6px;
  }

  .ev-card-details {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 9px;
  }

  .ev-detail-row {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-family: 'DM Sans', sans-serif;
    font-size: 11.5px;
    color: #555;
  }

  .ev-detail-row i {
    color: var(--red);
    font-size: 11px;
    width: 13px;
    text-align: center;
    margin-top: 1px;
    flex-shrink: 0;
  }

  /* Actions */
  .ev-card-actions {
    display: flex;
    gap: 8px;
    padding: 11px 15px 13px;
    border-top: 1px solid var(--border);
  }

  .ev-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 7px 6px;
    border-radius: 5px;
    font-family: 'Poppins', sans-serif;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .6px;
    text-transform: uppercase;
    cursor: pointer;
    transition: background .15s, color .15s, border-color .15s;
    text-decoration: none;
  }

  .ev-btn i {
    font-size: 10px;
  }

  .ev-btn-view {
    background: var(--white);
    border: 1.5px solid var(--border);
    color: var(--muted);
  }

  .ev-btn-view:hover {
    border-color: var(--red);
    color: var(--red);
    background: #fff5f5;
  }

  .ev-btn-edit {
    background: var(--white);
    border: 1.5px solid var(--border);
    color: var(--muted);
  }

  .ev-btn-edit:hover {
    border-color: var(--red);
    color: var(--red);
    background: #fff5f5;
  }

  .ev-btn-delete {
    background: var(--white);
    border: 1.5px solid var(--border);
    color: var(--muted);
  }

  .ev-btn-delete:hover {
    border-color: #dc2626;
    color: #dc2626;
    background: #fef2f2;
  }

  /* MODAL */
  .ev-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .55);
    z-index: 200;
    align-items: center;
    justify-content: center;
    padding: 16px;
  }

  .ev-modal-overlay.open {
    display: flex;
  }

  .ev-modal {
    background: var(--white);
    border-radius: 14px;
    width: 520px;
    max-width: 98vw;
    max-height: 92vh;
    overflow-y: auto;
    box-shadow: 0 28px 72px rgba(0, 0, 0, .28);
    animation: modalIn .22s ease;
  }

  @keyframes modalIn {
    from {
      opacity: 0;
      transform: translateY(16px) scale(.97);
    }

    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  .ev-modal::-webkit-scrollbar {
    width: 4px;
  }

  .ev-modal::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 10px;
  }

  .ev-modal-header {
    background: var(--white);
    padding: 22px 24px 16px;
    border-radius: 14px 14px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
  }

  .ev-modal-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .ev-modal-icon {
    width: 44px;
    height: 44px;
    background: var(--text);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .ev-modal-icon i {
    color: #fff;
    font-size: 18px;
  }

  .ev-modal-header h2 {
    font-family: 'Poppins', sans-serif;
    font-size: 20px;
    font-weight: 900;
    color: var(--text);
    letter-spacing: 1.5px;
    text-transform: uppercase;
  }

  .ev-modal-close {
    width: 32px;
    height: 32px;
    border: 1.5px solid var(--border);
    background: var(--white);
    border-radius: 6px;
    color: var(--text);
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s, border-color .15s;
    flex-shrink: 0;
  }

  .ev-modal-close:hover {
    background: #f5f5f5;
    border-color: #aaa;
  }

  .ev-modal-body {
    padding: 20px 24px 4px;
  }

  .ev-form-group {
    margin-bottom: 14px;
  }

  .ev-form-group label {
    display: block;
    margin-bottom: 5px;
    font-family: 'Poppins', sans-serif;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #555;
  }

  .ev-input-wrap {
    display: flex;
    align-items: center;
    border: 1.5px solid var(--red);
    border-radius: 6px;
    background: var(--white);
    overflow: hidden;
    transition: box-shadow .2s;
  }

  .ev-input-wrap:focus-within {
    box-shadow: 0 0 0 3px rgba(155, 27, 42, .1);
  }

  .ev-input-wrap .ev-input-icon {
    width: 40px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f8f8;
    border-right: 1.5px solid var(--red);
    flex-shrink: 0;
    padding: 11px 0;
  }

  .ev-input-wrap .ev-input-icon i {
    color: var(--red);
    font-size: 13px;
  }

  .ev-input-wrap input,
  .ev-input-wrap textarea {
    flex: 1;
    border: none;
    outline: none;
    padding: 11px 13px;
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    font-weight: 600;
    color: var(--text);
    letter-spacing: .8px;
    text-transform: uppercase;
    background: transparent;
    width: 100%;
  }

  .ev-input-wrap input::placeholder,
  .ev-input-wrap textarea::placeholder {
    color: #bbb;
    font-weight: 600;
  }

  .ev-input-wrap textarea {
    resize: vertical;
    min-height: 100px;
    padding-top: 11px;
  }

  .ev-upload-label-text {
    font-family: 'Poppins', sans-serif;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #555;
    margin-bottom: 5px;
    display: block;
  }

  .ev-file-row {
    display: flex;
    align-items: center;
    border: 1.5px solid var(--red);
    border-radius: 6px;
    overflow: hidden;
    cursor: pointer;
  }

  .ev-file-row input[type="file"] {
    display: none;
  }

  .ev-file-btn {
    background: var(--white);
    border-right: 1.5px solid var(--red);
    padding: 10px 18px;
    font-family: 'Poppins', sans-serif;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .8px;
    text-transform: uppercase;
    color: var(--text);
    white-space: nowrap;
    cursor: pointer;
    transition: background .15s;
  }

  .ev-file-btn:hover {
    background: #f5f5f5;
  }

  .ev-file-name {
    padding: 10px 14px;
    font-family: 'Poppins', sans-serif;
    font-size: 11px;
    font-weight: 600;
    color: var(--muted);
    letter-spacing: .5px;
    text-transform: uppercase;
    flex: 1;
  }

  .ev-img-thumb-preview {
    width: 100%;
    max-height: 130px;
    object-fit: cover;
    border-radius: 6px;
    margin-top: 8px;
    display: none;
    border: 1.5px solid var(--border);
  }

  .ev-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }

  /* Footer buttons */
  .ev-modal-footer {
    display: flex;
    gap: 10px;
    padding: 14px 24px 22px;
  }

  .ev-btn-clear {
    flex: 1;
    padding: 12px;
    border: 1.5px solid var(--red);
    background: var(--white);
    border-radius: 6px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    cursor: pointer;
    color: var(--red);
    transition: background .15s;
  }

  .ev-btn-clear:hover {
    background: #fff5f5;
  }

  .ev-btn-confirm {
    flex: 1;
    padding: 12px;
    background: var(--red);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    cursor: pointer;
    transition: background .2s;
  }

  .ev-btn-confirm:hover {
    background: var(--red-dark);
  }

  .ev-btn-confirm:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  /* Empty state */
  .ev-empty {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: var(--light-muted);
  }

  .ev-empty i {
    font-size: 40px;
    margin-bottom: 12px;
    display: block;
  }

  .ev-empty p {
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
  }

  /* Delete confirm modal */
  .ev-delete-body {
    text-align: center;
    padding: 24px 24px 8px;
  }

  .ev-delete-body i {
    font-size: 48px;
    color: #dc2626;
    margin-bottom: 16px;
  }

  .ev-delete-body p {
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    color: var(--muted);
    line-height: 1.6;
  }

  .ev-delete-body .ev-delete-name {
    font-weight: 700;
    color: var(--text);
  }

  /* Position dropdown */
  .ev-cat-wrap {
    position: relative;
  }

  .ev-cat-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1.5px solid var(--red);
    border-radius: 6px;
    padding: 11px 13px;
    cursor: pointer;
    background: var(--white);
    transition: box-shadow .2s;
  }

  .ev-cat-trigger:focus-within,
  .ev-cat-trigger.open {
    box-shadow: 0 0 0 3px rgba(155, 27, 42, .1);
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
  }

  .ev-cat-trigger-left {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .ev-cat-trigger-left i {
    color: var(--red);
    font-size: 13px;
  }

  .ev-cat-trigger span {
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    font-weight: 600;
    color: #bbb;
    letter-spacing: .8px;
    text-transform: uppercase;
  }

  .ev-cat-trigger span.selected {
    color: var(--text);
  }

  .ev-cat-trigger .ev-cat-chevron {
    color: var(--red);
    font-size: 13px;
    transition: transform .2s;
  }

  .ev-cat-trigger.open .ev-cat-chevron {
    transform: rotate(180deg);
  }

  .ev-cat-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--white);
    border: 1.5px solid var(--red);
    border-top: none;
    border-radius: 0 0 6px 6px;
    z-index: 50;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0, 0, 0, .1);
    max-height: 220px;
    overflow-y: auto;
  }

  .ev-cat-dropdown.open {
    display: block;
  }

  .ev-cat-option {
    padding: 10px 16px;
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    font-weight: 600;
    color: var(--muted);
    letter-spacing: .5px;
    text-transform: uppercase;
    cursor: pointer;
    transition: background .15s, color .15s;
  }

  .ev-cat-option:hover {
    background: #fdf0f1;
    color: var(--red);
  }

  .ev-cat-option.add-cat {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text);
    border-top: 1px solid var(--border);
    padding: 10px 14px;
  }

  .ev-cat-option.add-cat i {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--text);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    flex-shrink: 0;
  }

  @media (max-width: 992px) {
    .ev-header {
      padding: 24px;
    }

    .ev-header h1 {
      font-size: 1.6rem;
      letter-spacing: 1.5px;
    }

    .ev-toolbar {
      padding: 14px 18px;
      flex-wrap: wrap;
      gap: 8px;
    }

    .ev-search {
      width: 100%;
    }

    .ev-add-btn {
      margin-left: 0;
    }

    .ev-body {
      padding: 16px;
    }

    .ev-grid {
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 14px;
    }

    .ev-card-img,
    .ev-card-img-placeholder {
      height: 210px;
    }
  }

  @media (max-width: 576px) {
    .ev-header {
      padding: 20px 14px;
    }

    .ev-header h1 {
      font-size: 1.25rem;
      letter-spacing: 1px;
    }

    .ev-add-btn {
      width: 100%;
      justify-content: center;
    }

    .ev-grid {
      grid-template-columns: 1fr;
    }

    .ev-card-actions {
      flex-direction: column;
    }

    .ev-btn {
      width: 100%;
    }
  }
</style>

<div class="ev-page">

  {{-- HEADER --}}
  <div class="ev-header">
    <h1>Board of Trustees</h1>
  </div>

  {{-- TOOLBAR --}}
  <div class="ev-toolbar">
    <div class="ev-search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="tSearch" placeholder="Search trustees" oninput="tFilter(this.value)">
    </div>

    {{-- Position Filter --}}
    <div class="ev-cat-wrap" id="tFilterWrap" style="position:relative;">
      <div class="ev-cat-trigger" id="tFilterTrigger" onclick="tToggleFilter()" style="padding:9px 13px;min-width:180px;">
        <div class="ev-cat-trigger-left">
          <i class="fa-solid fa-filter" style="font-size:12px;"></i>
          <span id="tFilterDisplay" style="font-size:12px;">All Positions</span>
        </div>
        <i class="fa-solid fa-chevron-down ev-cat-chevron" style="font-size:11px;"></i>
      </div>
      <div class="ev-cat-dropdown" id="tFilterDropdown">
        <div class="ev-cat-option" onclick="tSelectFilter('', 'All Positions')">All Positions</div>
        {{-- Positions loaded dynamically --}}
      </div>
    </div>

    <button class="ev-add-btn" onclick="tOpenModal()">
      <i class="fa-solid fa-plus"></i> Add Trustee
    </button>
  </div>

  {{-- CARDS --}}
  <div class="ev-body">
    <div class="ev-grid" id="tGrid">
      <div class="ev-empty"><i class="fa-solid fa-circle-notch fa-spin"></i>
        <p>Loading trustees...</p>
      </div>
    </div>
  </div>

</div>

{{-- ADD / EDIT MODAL --}}
<div class="ev-modal-overlay" id="tModal" onclick="tHandleOverlay(event)">
  <div class="ev-modal">

    {{-- Header --}}
    <div class="ev-modal-header">
      <div class="ev-modal-header-left">
        <div class="ev-modal-icon"><i class="fa-solid fa-user-tie"></i></div>
        <h2 id="tModalTitle">Add Trustee</h2>
      </div>
      <button class="ev-modal-close" onclick="tCloseModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="ev-modal-body">
      <input type="hidden" id="tEditId">

      {{-- First Name --}}
      <div class="ev-form-group">
        <div class="ev-input-wrap">
          <div class="ev-input-icon"><i class="fa-solid fa-user"></i></div>
          <input type="text" id="tFFirstName" placeholder="First Name">
        </div>
      </div>

      {{-- Middle Name --}}
      <div class="ev-form-group">
        <div class="ev-input-wrap">
          <div class="ev-input-icon"><i class="fa-solid fa-user"></i></div>
          <input type="text" id="tFMiddleName" placeholder="Middle Name (Optional)">
        </div>
      </div>

      {{-- Last Name --}}
      <div class="ev-form-group">
        <div class="ev-input-wrap">
          <div class="ev-input-icon"><i class="fa-solid fa-user"></i></div>
          <input type="text" id="tFLastName" placeholder="Last Name">
        </div>
      </div>

      {{-- Gender --}}
      <div class="ev-form-group">
        <div class="ev-cat-wrap" id="tGenderWrap">
          <div class="ev-cat-trigger" id="tGenderTrigger" onclick="tToggleGender()">
            <div class="ev-cat-trigger-left">
              <i class="fa-solid fa-venus-mars"></i>
              <span id="tGenderDisplay">Gender</span>
            </div>
            <i class="fa-solid fa-chevron-down ev-cat-chevron"></i>
          </div>
          <div class="ev-cat-dropdown" id="tGenderDropdown">
            <div class="ev-cat-option" onclick="tSelectGender('male')">Male</div>
            <div class="ev-cat-option" onclick="tSelectGender('female')">Female</div>
          </div>
          <input type="hidden" id="tFGender" value="">
        </div>
      </div>

      {{-- Status --}}
      <div class="ev-form-group">
        <div class="ev-cat-wrap" id="tStatusWrap">
          <div class="ev-cat-trigger" id="tStatusTrigger" onclick="tToggleStatus()">
            <div class="ev-cat-trigger-left">
              <i class="fa-solid fa-circle-check"></i>
              <span id="tStatusDisplay">Status</span>
            </div>
            <i class="fa-solid fa-chevron-down ev-cat-chevron"></i>
          </div>
          <div class="ev-cat-dropdown" id="tStatusDropdown">
            <div class="ev-cat-option" onclick="tSelectStatus('active')">Active</div>
            <div class="ev-cat-option" onclick="tSelectStatus('inactive')">Inactive</div>
          </div>
          <input type="hidden" id="tFStatus" value="">
        </div>
      </div>

      {{-- Position Dropdown --}}
      <div class="ev-form-group">
        <div class="ev-cat-wrap" id="tPosWrap">
          <div class="ev-cat-trigger" id="tPosTrigger" onclick="tTogglePos()">
            <div class="ev-cat-trigger-left">
              <i class="fa-solid fa-briefcase"></i>
              <span id="tPosDisplay">Position</span>
            </div>
            <i class="fa-solid fa-chevron-down ev-cat-chevron"></i>
          </div>
          <div class="ev-cat-dropdown" id="tPosDropdown">
            {{-- Positions loaded dynamically --}}
            <div class="ev-cat-option add-cat" onclick="tAddPosition()">
              <i class="fa-solid fa-plus"></i> Add Position
            </div>
          </div>
          <input type="hidden" id="tFPositionId" value="">
        </div>
      </div>

      {{-- Upload Photo --}}
      <div class="ev-form-group">
        <span class="ev-upload-label-text">Upload Photo (.jpg, .png)</span>
        <div class="ev-file-row" onclick="document.getElementById('tFImg').click()">
          <input type="file" id="tFImg" accept="image/*" onchange="tPreviewImg(this)">
          <div class="ev-file-btn">Choose File</div>
          <span class="ev-file-name" id="tFileName">No File Upload</span>
        </div>
        <img id="tImgThumb" src="" alt="" class="ev-img-thumb-preview">
      </div>

    </div>

    <div class="ev-modal-footer">
      <button class="ev-btn-clear" onclick="tClearForm(false)">Clear Form</button>
      <button class="ev-btn-confirm" id="tSaveBtn" onclick="tSave()">Confirm</button>
    </div>

  </div>
</div>

{{-- VIEW TRUSTEE MODAL --}}
<div class="ev-modal-overlay" id="tViewModal" onclick="tHandleViewOverlay(event)">
  <div class="ev-modal" style="width:480px;max-width:96vw;">

    <div class="ev-modal-header" style="border-bottom:1px solid var(--border);">
      <div class="ev-modal-header-left">
        <div class="ev-modal-icon" style="background:var(--red);"><i class="fa-solid fa-user-tie"></i></div>
        <h2>Trustee Details</h2>
      </div>
      <button class="ev-modal-close" onclick="tCloseViewModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="ev-modal-body" style="padding:24px;text-align:center;">
      <img id="tViewImg" src="" alt=""
        style="width:180px;height:180px;object-fit:cover;border-radius:50%;display:none;border:3px solid var(--border);margin:0 auto 16px;">
      <div id="tViewImgPlaceholder"
        style="width:180px;height:180px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:48px;margin:0 auto 16px;"
        class="ev-card-img-placeholder c1"></div>
      <h3 id="tViewName" style="font-family:'Poppins',sans-serif;font-size:20px;font-weight:800;color:var(--text);margin-bottom:6px;text-transform:uppercase;letter-spacing:1px;"></h3>
      <p id="tViewPosition" style="font-family:'DM Sans',sans-serif;font-size:14px;color:var(--muted);font-weight:600;"></p>
    </div>

    <div class="ev-modal-footer" style="justify-content:flex-end;padding-top:0;">
      <button class="ev-btn-clear" onclick="tCloseViewModal()">Close</button>
      <button class="ev-btn-confirm" id="tViewEditBtn" onclick="">Edit</button>
    </div>

  </div>
</div>

{{-- DELETE CONFIRMATION MODAL --}}
<div class="ev-modal-overlay" id="tDeleteModal" onclick="tHandleDeleteOverlay(event)">
  <div class="ev-modal" style="width:420px;max-width:95vw;">
    <div class="ev-modal-header">
      <div class="ev-modal-header-left">
        <div class="ev-modal-icon" style="background:#dc2626;">
          <i class="fa-solid fa-trash" style="font-size:18px;"></i>
        </div>
        <h2>Delete Trustee</h2>
      </div>
      <button class="ev-modal-close" onclick="tCloseDeleteModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="ev-delete-body">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <p>Are you sure you want to delete<br><span class="ev-delete-name" id="tDeleteName"></span>?</p>
      <p style="font-size:12px;margin-top:8px;">This action cannot be undone.</p>
    </div>
    <div class="ev-modal-footer">
      <button class="ev-btn-clear" onclick="tCloseDeleteModal()">Cancel</button>
      <button class="ev-btn-confirm" id="tDeleteBtn" onclick="tConfirmDelete()" style="background:#dc2626;">Delete</button>
    </div>
  </div>
</div>

{{-- ADD POSITION MODAL --}}
<div class="ev-modal-overlay" id="tPosModal" onclick="tHandlePosOverlay(event)">
  <div class="ev-modal" style="width:420px;max-width:95vw;">
    <div class="ev-modal-header">
      <div class="ev-modal-header-left">
        <div class="ev-modal-icon" style="background:var(--red);">
          <i class="fa-solid fa-briefcase" style="font-size:18px;"></i>
        </div>
        <h2 id="tPosModalTitle">Add Position</h2>
      </div>
      <button class="ev-modal-close" onclick="tClosePosModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="ev-modal-body" style="padding:24px 24px 8px;">
      <input type="hidden" id="tPosEditId">
      <div class="ev-form-group">
        <div class="ev-input-wrap">
          <input type="text" id="tNewPosInput" placeholder="Type the Position Title"
            onkeydown="if(event.key==='Enter') tConfirmPosition()">
        </div>
      </div>
    </div>
    <div class="ev-modal-footer">
      <button class="ev-btn-clear" onclick="document.getElementById('tNewPosInput').value='';document.getElementById('tPosEditId').value='';">Clear</button>
      <button class="ev-btn-confirm" id="tPosSaveBtn" onclick="tConfirmPosition()">Confirm</button>
    </div>
  </div>
</div>

<script>
  // Authentication check
  var token = localStorage.getItem('token');
  if (!token) window.location.href = '/login';

  // Trustees API base URL
  var TRUSTEES_API = window.API_BASE_URL;

  let tTrustees = [];
  let tPositions = [];
  let tCurrentSearch = '';
  let tCurrentFilterPosId = '';
  let tDeleteId = null;
  const tColors = ['c1', 'c2', 'c3', 'c4', 'c5', 'c6'];

  // Helper: build full name from API fields
  function tFullName(t) {
    const first = t.firstname || '';
    const middle = t.middlename ? ` ${t.middlename} ` : ' ';
    const last = t.lastname || '';
    return (first + middle + last).trim();
  }

  // Helper: extract position string from nested object
  function tPositionName(t) {
    if (t.position && typeof t.position === 'object') return t.position.position || 'Trustee';
    if (typeof t.position === 'string') return t.position || 'Trustee';
    return 'Trustee';
  }

  // Helper: extract position ID from nested object
  function tPositionId(t) {
    if (t.position && typeof t.position === 'object') return t.position.id || '';
    if (t.position_id) return t.position_id;
    return '';
  }

  // Helper: get image URL with fallback
  function tImageUrl(t) {
    if (t.image_url) return t.image_url;
    const name = encodeURIComponent((t.firstname || '') + ' ' + (t.lastname || ''));
    return `https://ui-avatars.com/api/?name=${name}&background=f3f4f6&color=b61b2a&bold=true&size=400`;
  }

  function initBoardOfTrusteesPage() {
    fetchPositions();
    fetchTrustees();
  }

  if (document.readyState !== 'loading') {
    initBoardOfTrusteesPage();
  } else {
    document.addEventListener('DOMContentLoaded', initBoardOfTrusteesPage);
  }

  /* ═══════════════════════════════════════
     POSITIONS API
     ═══════════════════════════════════════ */

  async function fetchPositions() {
    try {
      const response = await fetch(`${TRUSTEES_API}/v1/positions`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });

      if (!response.ok) return;
      const data = await response.json();
      tPositions = data.data ? data.data : data;
      renderPositionDropdown();
    } catch (err) {
      console.error('Error fetching positions:', err);
    }
  }

  function renderPositionDropdown() {
    const dropdown = document.getElementById('tPosDropdown');
    const addBtn = dropdown.querySelector('.add-cat');

    // Remove all options except the Add button
    dropdown.querySelectorAll('.ev-cat-option:not(.add-cat)').forEach(el => el.remove());

    // Insert position options before the Add button
    tPositions.forEach(pos => {
      const opt = document.createElement('div');
      opt.className = 'ev-cat-option';
      opt.textContent = pos.position || pos.name || 'Untitled';
      opt.onclick = () => tSelectPos(pos.position || pos.name, pos.id);
      dropdown.insertBefore(opt, addBtn);
    });

    // Also populate the filter dropdown
    renderFilterDropdown();
  }

  function renderFilterDropdown() {
    const dropdown = document.getElementById('tFilterDropdown');
    const allOption = dropdown.querySelector('.ev-cat-option');

    // Remove all options except the "All Positions" option
    dropdown.querySelectorAll('.ev-cat-option:not(:first-child)').forEach(el => el.remove());

    tPositions.forEach(pos => {
      const opt = document.createElement('div');
      opt.className = 'ev-cat-option';
      opt.textContent = pos.position || pos.name || 'Untitled';
      opt.onclick = () => tSelectFilter(pos.id, pos.position || pos.name);
      dropdown.appendChild(opt);
    });
  }

  function tToggleFilter() {
    document.getElementById('tFilterTrigger').classList.toggle('open');
    document.getElementById('tFilterDropdown').classList.toggle('open');
  }

  function tSelectFilter(posId, label) {
    tCurrentFilterPosId = posId;
    const disp = document.getElementById('tFilterDisplay');
    disp.textContent = label;
    if (posId) {
      disp.classList.add('selected');
    } else {
      disp.classList.remove('selected');
    }
    document.getElementById('tFilterTrigger').classList.remove('open');
    document.getElementById('tFilterDropdown').classList.remove('open');
    tVisible();
  }

  function tTogglePos() {
    document.getElementById('tPosTrigger').classList.toggle('open');
    document.getElementById('tPosDropdown').classList.toggle('open');
  }

  function tSelectPos(name, id) {
    document.getElementById('tFPositionId').value = id;
    const disp = document.getElementById('tPosDisplay');
    disp.textContent = name;
    disp.classList.add('selected');
    document.getElementById('tPosTrigger').classList.remove('open');
    document.getElementById('tPosDropdown').classList.remove('open');
  }

  // Close dropdowns when clicking outside
  document.addEventListener('click', e => {
    if (!document.getElementById('tFilterWrap')?.contains(e.target)) {
      document.getElementById('tFilterTrigger')?.classList.remove('open');
      document.getElementById('tFilterDropdown')?.classList.remove('open');
    }
    if (!document.getElementById('tPosWrap')?.contains(e.target)) {
      document.getElementById('tPosTrigger')?.classList.remove('open');
      document.getElementById('tPosDropdown')?.classList.remove('open');
    }
    if (!document.getElementById('tGenderWrap')?.contains(e.target)) {
      document.getElementById('tGenderTrigger')?.classList.remove('open');
      document.getElementById('tGenderDropdown')?.classList.remove('open');
    }
    if (!document.getElementById('tStatusWrap')?.contains(e.target)) {
      document.getElementById('tStatusTrigger')?.classList.remove('open');
      document.getElementById('tStatusDropdown')?.classList.remove('open');
    }
  });

  /* ─── GENDER DROPDOWN ─── */
  function tToggleGender() {
    document.getElementById('tGenderTrigger').classList.toggle('open');
    document.getElementById('tGenderDropdown').classList.toggle('open');
  }

  function tSelectGender(val) {
    document.getElementById('tFGender').value = val;
    const disp = document.getElementById('tGenderDisplay');
    disp.textContent = val.charAt(0).toUpperCase() + val.slice(1);
    disp.classList.add('selected');
    document.getElementById('tGenderTrigger').classList.remove('open');
    document.getElementById('tGenderDropdown').classList.remove('open');
  }

  /* ─── STATUS DROPDOWN ─── */
  function tToggleStatus() {
    document.getElementById('tStatusTrigger').classList.toggle('open');
    document.getElementById('tStatusDropdown').classList.toggle('open');
  }

  function tSelectStatus(val) {
    document.getElementById('tFStatus').value = val;
    const disp = document.getElementById('tStatusDisplay');
    disp.textContent = val.charAt(0).toUpperCase() + val.slice(1);
    disp.classList.add('selected');
    document.getElementById('tStatusTrigger').classList.remove('open');
    document.getElementById('tStatusDropdown').classList.remove('open');
  }

  /* ─── ADD POSITION MODAL ─── */
  function tAddPosition() {
    document.getElementById('tPosTrigger').classList.remove('open');
    document.getElementById('tPosDropdown').classList.remove('open');
    document.getElementById('tNewPosInput').value = '';
    document.getElementById('tPosEditId').value = '';
    document.getElementById('tPosModalTitle').textContent = 'Add Position';
    document.getElementById('tPosModal').classList.add('open');
    setTimeout(() => document.getElementById('tNewPosInput').focus(), 100);
  }

  function tClosePosModal() {
    document.getElementById('tPosModal').classList.remove('open');
  }

  function tHandlePosOverlay(e) {
    if (e.target === document.getElementById('tPosModal')) tClosePosModal();
  }

  async function tConfirmPosition() {
    const name = document.getElementById('tNewPosInput').value.trim();
    const editId = document.getElementById('tPosEditId').value;
    if (!name) {
      document.getElementById('tNewPosInput').focus();
      return;
    }

    const saveBtn = document.getElementById('tPosSaveBtn');
    saveBtn.disabled = true;
    saveBtn.innerText = 'Saving...';

    const url = editId ?
      `${TRUSTEES_API}/v1/positions/${editId}` :
      `${TRUSTEES_API}/v1/positions`;

    const method = editId ? 'PUT' : 'POST';

    try {
      const response = await fetch(url, {
        method: method,
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          position: name
        })
      });

      const data = await response.json();

      if (response.ok || response.status === 201) {
        tClosePosModal();
        await fetchPositions(); // Refresh the dropdown

        // Auto-select the newly created/updated position
        const newPos = data.data || data;
        if (newPos && newPos.id) {
          tSelectPos(newPos.position || name, newPos.id);
        }
      } else {
        alert('Failed to save position: ' + (data.message || 'Error'));
      }
    } catch (err) {
      console.error(err);
      alert('Network error. Failed to save position.');
    } finally {
      saveBtn.disabled = false;
      saveBtn.innerText = 'Confirm';
    }
  }

  /* ═══════════════════════════════════════
     TRUSTEES API
     ═══════════════════════════════════════ */

  async function fetchTrustees() {
    try {
      const response = await fetch(`${TRUSTEES_API}/v1/trustees`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });

      if (response.status === 401) {
        localStorage.removeItem('token');
        window.location.href = '/login';
        return;
      }

      const data = await response.json();
      if (response.ok) {
        tTrustees = data.data ? data.data : data;
        tVisible();
      } else {
        document.getElementById('tGrid').innerHTML = `<div class="ev-empty"><p style="color:red;">Failed to load trustees: ${data.message}</p></div>`;
      }
    } catch (error) {
      console.error(error);
      document.getElementById('tGrid').innerHTML = `<div class="ev-empty"><p style="color:red;">Network error fetching trustees.</p></div>`;
    }
  }

  /* ─── RENDER ─── */
  function tRender(list) {
    const grid = document.getElementById('tGrid');
    if (!list.length) {
      grid.innerHTML = `<div class="ev-empty"><i class="fa-solid fa-users-slash"></i><p>No trustees found.</p></div>`;
      return;
    }

    grid.innerHTML = list.map((t, i) => {
      const color = tColors[i % tColors.length];
      const imgSrc = tImageUrl(t);

      const imgHtml = t.image_url ?
        `<img class="ev-card-img" src="${imgSrc}" alt="${tFullName(t)}"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
         <div class="ev-card-img-placeholder ${color}" style="display:none">
           <span class="ph-icon"><i class="fa-solid fa-user-tie"></i></span>
         </div>` :
        `<div class="ev-card-img-placeholder ${color}">
           <span class="ph-icon"><i class="fa-solid fa-user-tie"></i></span>
         </div>`;

      const safeName = tFullName(t) || 'Unknown';
      const safePosition = tPositionName(t);

      return `
    <div class="ev-card" id="tCard-${t.id}" style="animation-delay:${i*0.05}s">
      ${imgHtml}
      <div class="ev-card-body">
        <div class="ev-card-title">${safeName}</div>
        <div class="ev-card-details">
          <div class="ev-detail-row"><i class="fa-solid fa-briefcase"></i><span>${safePosition}</span></div>
        </div>
      </div>
      <div class="ev-card-actions">
        <button class="ev-btn ev-btn-view" onclick="tView(${t.id})">
          <i class="fa-regular fa-eye"></i> View
        </button>
        <button class="ev-btn ev-btn-edit" onclick="tEdit(${t.id})">
          <i class="fa-regular fa-pen-to-square"></i> Edit
        </button>
        <button class="ev-btn ev-btn-delete" onclick="tDelete(${t.id})">
          <i class="fa-solid fa-trash"></i> Delete
        </button>
      </div>
    </div>`;
    }).join('');
  }

  function tVisible() {
    let list = [...tTrustees];

    // Filter by position
    if (tCurrentFilterPosId) {
      list = list.filter(t => {
        const tPosId = t.board_position_id || (t.position && t.position.id) || null;
        return Number(tPosId) === Number(tCurrentFilterPosId);
      });
    }

    // Filter by search
    if (tCurrentSearch) {
      const q = tCurrentSearch.toLowerCase();
      list = list.filter(t =>
        tFullName(t).toLowerCase().includes(q) ||
        tPositionName(t).toLowerCase().includes(q)
      );
    }

    // Sort by position order (based on position id)
    list.sort((a, b) => {
      const aPosId = a.board_position_id || (a.position && a.position.id) || 999;
      const bPosId = b.board_position_id || (b.position && b.position.id) || 999;
      return Number(aPosId) - Number(bPosId);
    });

    tRender(list);
  }

  /* ─── SEARCH ─── */
  function tFilter(val) {
    tCurrentSearch = val;
    tVisible();
  }

  /* ─── ADD/EDIT TRUSTEE MODAL ─── */
  function tOpenModal(prefill) {
    tClearForm();
    document.getElementById('tModalTitle').textContent = prefill ? 'Edit Trustee' : 'Add Trustee';
    document.getElementById('tEditId').value = prefill ? prefill.id : '';

    if (prefill) {
      document.getElementById('tFFirstName').value = prefill.firstname || '';
      document.getElementById('tFMiddleName').value = prefill.middlename || '';
      document.getElementById('tFLastName').value = prefill.lastname || '';

      // Set position dropdown from prefill data
      const posId = tPositionId(prefill);
      const posName = tPositionName(prefill);
      if (posId) {
        tSelectPos(posName, posId);
      }

      // Set gender
      if (prefill.gender) {
        tSelectGender(prefill.gender);
      }

      // Set status
      if (prefill.status) {
        tSelectStatus(prefill.status);
      }

      if (prefill.image_url) {
        const thumb = document.getElementById('tImgThumb');
        thumb.src = prefill.image_url;
        thumb.style.display = 'block';
        document.getElementById('tFileName').textContent = 'Current image';
      }
    }
    document.getElementById('tModal').classList.add('open');
  }

  function tPreviewImg(input) {
    const file = input.files[0];
    const thumb = document.getElementById('tImgThumb');
    const label = document.getElementById('tFileName');
    if (file) {
      label.textContent = file.name;
      const reader = new FileReader();
      reader.onload = e => {
        thumb.src = e.target.result;
        thumb.style.display = 'block';
      };
      reader.readAsDataURL(file);
    }
  }

  function tClearForm(resetEditId = true) {
    if (resetEditId) document.getElementById('tEditId').value = '';
    document.getElementById('tFFirstName').value = '';
    document.getElementById('tFMiddleName').value = '';
    document.getElementById('tFLastName').value = '';
    document.getElementById('tFPositionId').value = '';
    document.getElementById('tPosDisplay').textContent = 'Position';
    document.getElementById('tPosDisplay').classList.remove('selected');
    document.getElementById('tFGender').value = '';
    document.getElementById('tGenderDisplay').textContent = 'Gender';
    document.getElementById('tGenderDisplay').classList.remove('selected');
    document.getElementById('tFStatus').value = '';
    document.getElementById('tStatusDisplay').textContent = 'Status';
    document.getElementById('tStatusDisplay').classList.remove('selected');
    document.getElementById('tFImg').value = '';
    document.getElementById('tFileName').textContent = 'No File Upload';
    document.getElementById('tImgThumb').style.display = 'none';
    document.getElementById('tImgThumb').src = '';
  }

  function tCloseModal() {
    document.getElementById('tModal').classList.remove('open');
  }

  function tHandleOverlay(e) {
    if (e.target === document.getElementById('tModal')) tCloseModal();
  }

  /* ─── API SAVE TRUSTEE ─── */
  async function tSave() {
    const firstname = document.getElementById('tFFirstName').value.trim();
    const middlename = document.getElementById('tFMiddleName').value.trim();
    const lastname = document.getElementById('tFLastName').value.trim();
    const gender = document.getElementById('tFGender').value;
    const status = document.getElementById('tFStatus').value;
    const positionId = document.getElementById('tFPositionId').value;
    const editId = document.getElementById('tEditId').value;
    const fileInput = document.getElementById('tFImg');

    if (!firstname || !lastname) {
      alert('Please fill in the first name and last name.');
      return;
    }
    if (!gender) {
      alert('Please select a gender.');
      return;
    }
    if (!positionId) {
      alert('Please select a position.');
      return;
    }
    if (!status) {
      alert('Please select a status.');
      return;
    }

    // Check if position is already taken by another trustee
    const posIdNum = Number(positionId);
    const editIdNum = editId ? Number(editId) : null;
    const existingTrustee = tTrustees.find(t => {
      const tPosId = t.board_position_id || (t.position && t.position.id) || null;
      return Number(tPosId) === posIdNum && t.id !== editIdNum;
    });

    if (existingTrustee) {
      const posName = document.getElementById('tPosDisplay').textContent;
      const takenBy = tFullName(existingTrustee);
      alert(`The position "${posName}" is already assigned to ${takenBy}. Each position can only have one trustee.`);
      return;
    }

    const saveBtn = document.getElementById('tSaveBtn');
    saveBtn.disabled = true;
    saveBtn.innerText = 'Saving...';

    const formData = new FormData();
    // Spoof PUT method for Laravel when editing
    if (editId) {
      formData.append('_method', 'PUT');
    }
    formData.append('firstname', firstname);
    formData.append('middlename', middlename);
    formData.append('lastname', lastname);
    formData.append('gender', gender);
    formData.append('board_position_id', positionId);
    formData.append('status', status);

    if (fileInput.files.length > 0) {
      formData.append('image', fileInput.files[0]);
    }

    // Always POST — Laravel uses _method field to handle PUT
    const url = editId ?
      `${TRUSTEES_API}/v1/trustees/${editId}` :
      `${TRUSTEES_API}/v1/trustees`;

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        },
        body: formData
      });

      const data = await response.json();

      if (response.ok || response.status === 201) {
        tCloseModal();
        fetchTrustees();
      } else {
        alert('Failed to save trustee: ' + (data.message || 'Validation Error'));
      }
    } catch (err) {
      console.error(err);
      alert('Network error. Failed to save.');
    } finally {
      saveBtn.disabled = false;
      saveBtn.innerText = 'Confirm';
    }
  }

  /* ─── VIEW MODAL ─── */
  function tView(id) {
    const t = tTrustees.find(e => e.id === id);
    if (!t) return;

    document.getElementById('tViewName').textContent = tFullName(t) || 'Unknown';
    document.getElementById('tViewPosition').textContent = tPositionName(t);

    const img = document.getElementById('tViewImg');
    const ph = document.getElementById('tViewImgPlaceholder');
    if (t.image_url) {
      img.src = t.image_url;
      img.style.display = 'block';
      ph.style.display = 'none';
    } else {
      img.style.display = 'none';
      ph.style.display = 'flex';
    }

    document.getElementById('tViewEditBtn').onclick = () => {
      tCloseViewModal();
      tEdit(id);
    };
    document.getElementById('tViewModal').classList.add('open');
  }

  function tCloseViewModal() {
    document.getElementById('tViewModal').classList.remove('open');
  }

  function tHandleViewOverlay(e) {
    if (e.target === document.getElementById('tViewModal')) tCloseViewModal();
  }

  function tEdit(id) {
    const t = tTrustees.find(e => e.id === id);
    if (t) tOpenModal(t);
  }

  /* ─── DELETE ─── */
  function tDelete(id) {
    const t = tTrustees.find(e => e.id === id);
    if (!t) return;
    tDeleteId = id;
    document.getElementById('tDeleteName').textContent = tFullName(t) || 'this trustee';
    document.getElementById('tDeleteModal').classList.add('open');
  }

  function tCloseDeleteModal() {
    document.getElementById('tDeleteModal').classList.remove('open');
    tDeleteId = null;
  }

  function tHandleDeleteOverlay(e) {
    if (e.target === document.getElementById('tDeleteModal')) tCloseDeleteModal();
  }

  async function tConfirmDelete() {
    if (!tDeleteId) return;

    const deleteBtn = document.getElementById('tDeleteBtn');
    deleteBtn.disabled = true;
    deleteBtn.innerText = 'Deleting...';

    try {
      const response = await fetch(`${TRUSTEES_API}/v1/trustees/${tDeleteId}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });

      if (response.ok) {
        tCloseDeleteModal();
        fetchTrustees();
      } else {
        const data = await response.json();
        alert('Failed to delete trustee: ' + (data.message || 'Error'));
      }
    } catch (err) {
      console.error(err);
      alert('Network error. Failed to delete.');
    } finally {
      deleteBtn.disabled = false;
      deleteBtn.innerText = 'Delete';
    }
  }
</script>
@endsection