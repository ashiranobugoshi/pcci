@extends('layouts.admin')

@section('title', 'Events - PCCI Admin')

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

  /* ── PAGE WRAPPER ── */
  .ev-page {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: var(--bg);
  }

  /* ── HEADER BANNER ── */
  .ev-header {
    background: var(--red);
    padding: 36px 40px;
    flex-shrink: 0;
    border-radius: 10px;
  }

  .ev-header h1,
  .page-header h1,
  .admin-header h1,
  .admin-page-title {
    font-size: 2rem;
    font-weight: 900;
    color: #fff;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin: 0;
    line-height: 1.2;
  }

  /* ── TOOLBAR ── */
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

  .ev-filter-btn {
    display: flex;
    align-items: center;
    gap: 7px;
    background: var(--white);
    border: 1.5px solid var(--border);
    border-radius: 7px;
    padding: 9px 16px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: var(--muted);
    cursor: pointer;
    letter-spacing: .5px;
    text-transform: uppercase;
    transition: border-color .2s, color .2s;
  }

  .ev-filter-btn:hover,
  .ev-filter-btn.active {
    border-color: var(--red);
    color: var(--red);
  }

  .ev-filter-btn i {
    font-size: 12px;
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

  /* ── CARDS AREA ── */
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

  /* ── CARD ── */
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
    height: 172px;
    object-fit: cover;
    display: block;
  }

  .ev-card-img-placeholder {
    width: 100%;
    height: 172px;
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

  .ev-status {
    font-family: 'DM Sans', sans-serif;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .3px;
  }

  .ev-status.completed {
    color: #374151;
  }

  .ev-status.upcoming {
    color: #1d6fa8;
  }

  .ev-status.ongoing {
    color: #b45309;
  }

  .ev-status.cancelled {
    color: #b91c1c;
  }

  .ev-card-title {
    font-family: 'Poppins', sans-serif;
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text);
    line-height: 1.3;
    margin-bottom: 10px;
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

  .ev-card-desc {
    font-family: 'DM Sans', sans-serif;
    font-size: 11px;
    color: var(--muted);
    line-height: 1.55;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
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
    border-color: #dc3545;
    color: #fff;
    background: #dc3545;
  }

  /* ── MODAL ── */
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

  .ev-form-group input.ev-plain,
  .ev-form-group select.ev-plain,
  .ev-form-group textarea.ev-plain {
    width: 100%;
    padding: 11px 13px;
    border: 1.5px solid var(--red);
    border-radius: 6px;
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    font-weight: 600;
    color: var(--text);
    letter-spacing: .8px;
    outline: none;
    background: var(--white);
    transition: box-shadow .2s;
    appearance: none;
  }

  .ev-form-group input.ev-plain:focus,
  .ev-form-group select.ev-plain:focus {
    box-shadow: 0 0 0 3px rgba(155, 27, 42, .1);
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

  /* Category dropdown custom */
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
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .ev-cat-option:hover {
    background: #fdf0f1;
    color: var(--red);
  }

  .ev-cat-option .ev-cat-edit-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--muted);
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 4px;
    transition: color .15s, background .15s;
    flex-shrink: 0;
  }

  .ev-cat-option:hover .ev-cat-edit-btn {
    color: var(--red);
  }

  .ev-cat-option .ev-cat-edit-btn:hover {
    background: rgba(190, 30, 56, .1);
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

  /* View modal (clean card layout) */
  .ev-view-modal {
    width: 760px;
    max-width: 96vw;
  }

  .ev-view-header {
    border-bottom: 1px solid var(--border);
  }

  .ev-view-header-left {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 7px;
    min-width: 0;
  }

  .ev-view-title {
    font-family: 'Poppins', sans-serif;
    font-size: 1.45rem;
    font-weight: 800;
    letter-spacing: .3px;
    line-height: 1.2;
    color: var(--text);
    margin: 0;
  }

  .ev-view-body {
    padding: 22px 24px;
  }

  .ev-view-content {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 22px;
    align-items: start;
  }

  .ev-view-media {
    width: 100%;
  }

  .ev-view-img,
  .ev-view-placeholder {
    width: 100%;
    height: 260px;
    border-radius: 10px;
  }

  .ev-view-img {
    object-fit: cover;
    border: 1px solid var(--border);
    display: none;
    background: #fff;
  }

  .ev-view-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    border: 1px solid rgba(0, 0, 0, 0.04);
  }

  .ev-view-info {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .ev-view-meta {
    display: flex;
    flex-direction: column;
    gap: 11px;
  }

  .ev-view-meta-row {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    color: var(--text);
    font-weight: 600;
    min-width: 0;
  }

  .ev-view-meta-row i {
    color: var(--red);
    width: 18px;
    text-align: center;
    flex-shrink: 0;
  }

  .ev-view-meta-row span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .ev-view-status-wrap {
    margin-top: 2px;
  }

  .ev-view-desc {
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    color: var(--muted);
    line-height: 1.7;
    max-height: 260px;
    overflow-y: auto;
    padding-right: 4px;
  }

  .ev-view-footer {
    justify-content: flex-end;
    padding-top: 0;
  }

  .ev-view-footer .ev-btn-clear,
  .ev-view-footer .ev-btn-confirm {
    max-width: 170px;
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

  @media (max-width: 992px) {
    .ev-header {
      padding: 24px;
    }

    .ev-header h1,
    .page-header h1,
    .admin-header h1,
    .admin-page-title {
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
  }

  @media (max-width: 576px) {
    .ev-header {
      padding: 20px 14px;
    }

    .ev-header h1,
    .page-header h1,
    .admin-header h1,
    .admin-page-title {
      font-size: 1.25rem;
      letter-spacing: 1px;
    }

    .ev-filter-btn,
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

    .ev-view-title {
      font-size: 1.15rem;
    }

    .ev-view-content {
      grid-template-columns: 1fr;
      gap: 14px;
    }

    .ev-view-img,
    .ev-view-placeholder {
      height: 210px;
    }

    .ev-view-footer {
      flex-direction: column-reverse;
    }

    .ev-view-footer .ev-btn-clear,
    .ev-view-footer .ev-btn-confirm {
      max-width: 100%;
      width: 100%;
    }
  }
</style>

<div class="ev-page">

  {{-- HEADER --}}
  <div class="ev-header">
    <h1>Events</h1>
  </div>

  {{-- TOOLBAR --}}
  <div class="ev-toolbar">
    <div class="ev-search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="evSearch" placeholder="Search" oninput="evFilter(this.value)">
    </div>

    <button class="ev-filter-btn" id="evFilterBtn" onclick="evToggleFilter()">
      <i class="fa-solid fa-sliders"></i> Filtered
    </button>

    <button class="ev-add-btn" onclick="evOpenModal()">
      <i class="fa-solid fa-plus"></i> Add
    </button>
  </div>

  {{-- CARDS --}}
  <div class="ev-body">
    <div class="ev-grid" id="evGrid">
      <div class="ev-empty"><i class="fa-solid fa-circle-notch fa-spin"></i>
        <p>Loading events...</p>
      </div>
    </div>
  </div>

</div>{{-- ADD / EDIT MODAL --}}
<div class="ev-modal-overlay" id="evModal" onclick="evHandleOverlay(event)">
  <div class="ev-modal">

    {{-- Header --}}
    <div class="ev-modal-header">
      <div class="ev-modal-header-left">
        <div class="ev-modal-icon"><i class="fa-solid fa-calendar-check"></i></div>
        <h2 id="evModalTitle">Add Events</h2>
      </div>
      <button class="ev-modal-close" onclick="evCloseModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="ev-modal-body">
      <input type="hidden" id="evEditId">
      <input type="hidden" id="evFImgExisting">

      {{-- Title --}}
      <div class="ev-form-group">
        <div class="ev-input-wrap">
          <div class="ev-input-icon"><i class="fa-regular fa-calendar-check"></i></div>
          <input type="text" id="evFTitle" placeholder="Event / Workshop Title">
        </div>
      </div>

      {{-- Upload Photo --}}
      <div class="ev-form-group">
        <span class="ev-upload-label-text">Upload Photo (.jpg, .png, )</span>
        <div class="ev-file-row" onclick="document.getElementById('evFImg').click()">
          <input type="file" id="evFImg" accept="image/*" onchange="evPreviewImg(this)">
          <div class="ev-file-btn">Choose File</div>
          <span class="ev-file-name" id="evFileName">No File Upload</span>
        </div>
        <img id="evImgThumb" src="" alt="" class="ev-img-thumb-preview">
      </div>

      {{-- Category custom dropdown --}}
      <div class="ev-form-group">
        <div class="ev-cat-wrap" id="evCatWrap">
          <div class="ev-cat-trigger" id="evCatTrigger" onclick="evToggleCat()">
            <div class="ev-cat-trigger-left">
              <i class="fa-solid fa-bars"></i>
              <span id="evCatDisplay">Category</span>
            </div>
            <i class="fa-solid fa-chevron-down ev-cat-chevron"></i>
          </div>
          <div class="ev-cat-dropdown" id="evCatDropdown">
            <div id="evCatOptions">
              <div class="ev-cat-option" style="color:#999;pointer-events:none;">Loading categories...</div>
            </div>
            <div class="ev-cat-option add-cat" onclick="evAddCategory()">
              <i class="fa-solid fa-plus"></i> Add Category
            </div>
          </div>
          <input type="hidden" id="evFCategoryId" value="">
        </div>
      </div>

      {{-- Date & Time --}}
      <div class="ev-form-row">
        <div class="ev-form-group">
          <div class="ev-input-wrap">
            <div class="ev-input-icon"><i class="fa-regular fa-calendar"></i></div>
            <input type="date" id="evFDate">
          </div>
        </div>
        <div class="ev-form-group">
          <div class="ev-input-wrap">
            <div class="ev-input-icon"><i class="fa-regular fa-clock"></i></div>
            <input type="time" id="evFTime">
          </div>
        </div>
      </div>

      {{-- Location --}}
      <div class="ev-form-group">
        <div class="ev-input-wrap">
          <div class="ev-input-icon"><i class="fa-solid fa-location-dot"></i></div>
          <input type="text" id="evFLocation" placeholder="Location">
        </div>
      </div>

      {{-- Description --}}
      <div class="ev-form-group">
        <div class="ev-input-wrap" style="align-items:flex-start">
          <textarea id="evFDesc" placeholder="Description of the Event" style="min-height:110px"></textarea>
        </div>
      </div>

      {{-- Status --}}
      <div class="ev-form-group" style="position:relative">
        <div class="ev-input-wrap" style="padding:0;">
          <select id="evFStatus" style="width:100%;border:none;padding:11px 36px 11px 13px;background:transparent;font-size:12px;letter-spacing:.8px;text-transform:uppercase;font-family:'Poppins',sans-serif;font-weight:600;color:#bbb;appearance:none;cursor:pointer;outline:none;" onchange="this.style.color=this.value?'var(--text)':'#bbb'">
            <option value="" disabled selected>Status</option>
            <option value="upcoming">Upcoming</option>
            <option value="ongoing">Ongoing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <i class="fa-solid fa-chevron-down" style="position:absolute;right:13px;top:50%;transform:translateY(-50%);color:var(--red);font-size:12px;pointer-events:none;"></i>
      </div>

    </div>

    <div class="ev-modal-footer">
      <button class="ev-btn-clear" onclick="evClearForm()">Clear Form</button>
      <button class="ev-btn-confirm" id="saveBtn" onclick="evSave()">Confirm</button>
    </div>

  </div>
</div>

{{-- ADD CATEGORY MODAL --}}
<div class="ev-modal-overlay" id="evCatModal" onclick="evHandleCatOverlay(event)">
  <div class="ev-modal" style="width:420px;max-width:95vw;">
    <div class="ev-modal-header">
      <div class="ev-modal-header-left">
        <div class="ev-modal-icon" style="background:var(--red);">
          <i class="fa-solid fa-user" style="font-size:20px;"></i>
        </div>
        <h2>Add Category</h2>
      </div>
      <button class="ev-modal-close" onclick="evCloseCatModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="ev-modal-body" style="padding:24px 24px 8px;">
      <div class="ev-form-group">
        <div class="ev-input-wrap">
          <input type="text" id="evNewCatInput" placeholder="Type the Category"
            onkeydown="if(event.key==='Enter') evConfirmCategory()">
        </div>
      </div>
    </div>
    <div class="ev-modal-footer">
      <button class="ev-btn-clear" onclick="document.getElementById('evNewCatInput').value=''">Clear</button>
      <button class="ev-btn-confirm" onclick="evConfirmCategory()">Confirm</button>
    </div>
  </div>
</div>

{{-- EDIT CATEGORY MODAL --}}
<div class="ev-modal-overlay" id="evEditCatModal" onclick="evHandleEditCatOverlay(event)">
  <div class="ev-modal" style="width:420px;max-width:95vw;">
    <div class="ev-modal-header">
      <div class="ev-modal-header-left">
        <div class="ev-modal-icon" style="background:var(--red);">
          <i class="fa-solid fa-pen" style="font-size:18px;"></i>
        </div>
        <h2>Edit Category</h2>
      </div>
      <button class="ev-modal-close" onclick="evCloseEditCatModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="ev-modal-body" style="padding:24px 24px 8px;">
      <input type="hidden" id="evEditCatId">
      <div class="ev-form-group">
        <div class="ev-input-wrap">
          <input type="text" id="evEditCatInput" placeholder="Category Name"
            onkeydown="if(event.key==='Enter') evUpdateCategory()">
        </div>
      </div>
    </div>
    <div class="ev-modal-footer">
      <button class="ev-btn-clear" onclick="evCloseEditCatModal()">Cancel</button>
      <button class="ev-btn-confirm" onclick="evUpdateCategory()">Update</button>
    </div>
  </div>
</div>

{{-- VIEW EVENT DETAILS MODAL --}}
<div class="ev-modal-overlay" id="evViewModal" onclick="evHandleViewOverlay(event)">
  <div class="ev-modal ev-view-modal">

    <div class="ev-modal-header ev-view-header">
      <div class="ev-view-header-left">
        <h2 id="evViewTitle" class="ev-view-title">Event Details</h2>
        <span id="evViewTag" class="ev-tag"></span>
      </div>
      <button class="ev-modal-close" onclick="evCloseViewModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="ev-modal-body ev-view-body">
      <div class="ev-view-content">

        {{-- Left: Image --}}
        <div class="ev-view-media">
          <img id="evViewImg" src="" alt="" class="ev-view-img">
          <div id="evViewImgPlaceholder"
            class="ev-card-img-placeholder ev-view-placeholder c1"></div>
        </div>

        {{-- Right: Info --}}
        <div class="ev-view-info">
          <div class="ev-view-meta">
            <div class="ev-view-meta-row">
              <i class="fa-regular fa-calendar"></i>
              <span id="evViewDate"></span>
            </div>
            <div class="ev-view-meta-row">
              <i class="fa-regular fa-clock"></i>
              <span id="evViewTime"></span>
            </div>
            <div class="ev-view-meta-row">
              <i class="fa-solid fa-location-dot"></i>
              <span id="evViewLocation"></span>
            </div>
          </div>

          {{-- Status badge --}}
          <div class="ev-view-status-wrap">
            <span id="evViewStatus" class="ev-status"></span>
          </div>

          {{-- Description --}}
          <div id="evViewDesc"
            class="ev-view-desc">
          </div>
        </div>

      </div>
    </div>

    <div class="ev-modal-footer ev-view-footer">
      <button class="ev-btn-clear" onclick="evCloseViewModal()">Close</button>
      <button class="ev-btn-confirm" id="evViewEditBtn" onclick="">Edit Event</button>
    </div>

  </div>
</div>

<script>
  // Authentication check
  var token = localStorage.getItem('token');
  if (!token) window.location.href = '/login';

  let evEvents = [];
  let evCategories = [];
  let evFilterActive = false;
  let evCurrentSearch = '';
  var evColors = ['c1', 'c2', 'c3', 'c4', 'c5', 'c6'];

  function initEventsPage() {
    fetchCategories();
    fetchEvents();
  }

  if (document.readyState !== 'loading') {
    initEventsPage();
  } else {
    document.addEventListener('DOMContentLoaded', initEventsPage);
  }

  /* ─── API FETCH EVENTS ─── */
  async function fetchEvents() {
    try {
      const response = await fetch(`${window.API_BASE_URL}/v1/events`, {
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
        // Check if your Laravel wrapper uses 'data'
        evEvents = data.data ? data.data : data;
        evVisible();
      } else {
        document.getElementById('evGrid').innerHTML = `<div class="ev-empty"><p style="color:red;">Failed to load events: ${data.message}</p></div>`;
      }
    } catch (error) {
      console.error(error);
      document.getElementById('evGrid').innerHTML = `<div class="ev-empty"><p style="color:red;">Network error fetching events.</p></div>`;
    }
  }

  /* ─── API FETCH CATEGORIES ─── */
  async function fetchCategories() {
    try {
      const response = await fetch(`${window.API_BASE_URL}/v1/categories`, {
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
        evCategories = data.data ? data.data : (Array.isArray(data) ? data : []);
        renderCategoryOptions();
      } else {
        console.error('Categories error:', data);
        document.getElementById('evCatOptions').innerHTML =
          '<div class="ev-cat-option" style="color:#b91c1c;pointer-events:none;">Failed to load categories</div>';
      }
    } catch (error) {
      console.error('Error fetching categories:', error);
      document.getElementById('evCatOptions').innerHTML =
        '<div class="ev-cat-option" style="color:#b91c1c;pointer-events:none;">Error loading categories</div>';
    }
  }

  function renderCategoryOptions() {
    const container = document.getElementById('evCatOptions');
    if (!evCategories.length) {
      container.innerHTML = '<div class="ev-cat-option" style="color:#999;pointer-events:none;">No categories found</div>';
      return;
    }
    container.innerHTML = evCategories.map(cat => {
      const catName = cat.name || cat.category_name || cat.title || 'Unknown';
      const catId = cat.id;
      const escapedName = catName.replace(/'/g, "\\'");
      return `<div class="ev-cat-option">
            <span onclick="evSelectCat('${escapedName}', ${catId})" style="flex:1;">${catName}</span>
            <button class="ev-cat-edit-btn" onclick="event.stopPropagation(); evEditCategory(${catId}, '${escapedName}')" title="Edit category">
                <i class="fa-solid fa-pen"></i>
            </button>
        </div>`;
    }).join('');
  }

  /* ─── RENDER ─── */
  function evRender(list) {
    const grid = document.getElementById('evGrid');
    if (!list.length) {
      grid.innerHTML = `<div class="ev-empty"><i class="fa-regular fa-calendar-xmark"></i><p>No events found.</p></div>`;
      return;
    }

    // NOTE: If your backend returns property names differently (like 'event_title' instead of 'title'), 
    // you must change ev.title to ev.event_title below.
    grid.innerHTML = list.map((ev, i) => {
      // Determine image URL (API returns "imagel")
      const rawImg = ev.imagel || ev.image || null;
      const imgSrc = rawImg ?
        (rawImg.startsWith('http') || rawImg.startsWith('data:') ? rawImg : '/storage/' + rawImg) :
        null;

      const imgHtml = imgSrc ?
        `<img class="ev-card-img" src="${imgSrc}" alt="${ev.title || 'Event'}"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
         <div class="ev-card-img-placeholder ${ev.color||'c1'}" style="display:none">
           <span class="ph-icon">📅</span>
         </div>` :
        `<div class="ev-card-img-placeholder ${ev.color||'c1'}">
           <span class="ph-icon">📅</span>
         </div>`;

      // Handle missing data fallbacks
      const safeTitle = ev.title || 'Untitled Event';
      // category is a nested object: { id, name, ... }
      let safeCat = 'General';
      if (ev.category && typeof ev.category === 'object') {
        safeCat = ev.category.name || 'General';
      } else if (typeof ev.category === 'string') {
        safeCat = ev.category;
      }
      const safeStatus = ev.status || 'Upcoming';
      const safeDate = ev.date || 'TBA';
      const safeTime = ev.time || 'TBA';
      const safeLoc = ev.location || 'TBA';
      const safeDesc = ev.description || ev.desc || 'No description provided.';

      return `
    <div class="ev-card" id="evCard-${ev.id}" style="animation-delay:${i*0.05}s">
      ${imgHtml}
      <div class="ev-card-body">
        <div class="ev-card-meta">
          <span class="ev-tag">${safeCat}</span>
          <span class="ev-status ${safeStatus.toLowerCase()}">${safeStatus}</span>
        </div>
        <div class="ev-card-title">${safeTitle}</div>
        <div class="ev-card-details">
          <div class="ev-detail-row"><i class="fa-regular fa-calendar"></i><span>${safeDate}</span></div>
          <div class="ev-detail-row"><i class="fa-regular fa-clock"></i><span>${safeTime}</span></div>
          <div class="ev-detail-row"><i class="fa-solid fa-location-dot"></i><span>${safeLoc}</span></div>
        </div>
        <div class="ev-card-desc">${safeDesc}</div>
      </div>
      <div class="ev-card-actions">
        <button class="ev-btn ev-btn-view" onclick="evView(${ev.id})">
          <i class="fa-regular fa-eye"></i> View
        </button>
        <button class="ev-btn ev-btn-edit" onclick="evEdit(${ev.id})">
          <i class="fa-regular fa-pen-to-square"></i> Edit
        </button>
        <button class="ev-btn ev-btn-delete" onclick="evDelete(${ev.id})">
          <i class="fa-regular fa-trash-can"></i> Delete
        </button>
      </div>
    </div>`;
    }).join('');
  }

  function evVisible() {
    let list = evFilterActive ?
      evEvents.filter(e => e.status === 'Completed') : [...evEvents];
    if (evCurrentSearch) {
      const q = evCurrentSearch.toLowerCase();
      list = list.filter(e =>
        (e.title || '').toLowerCase().includes(q) ||
        ((e.category && typeof e.category === 'object' ? e.category.name : e.category) || '').toLowerCase().includes(q) ||
        (e.location || '').toLowerCase().includes(q) ||
        (e.status || '').toLowerCase().includes(q)
      );
    }
    evRender(list);
  }

  /* ─── FILTER / SEARCH ─── */
  function evFilter(val) {
    evCurrentSearch = val;
    evVisible();
  }

  function evToggleFilter() {
    evFilterActive = !evFilterActive;
    document.getElementById('evFilterBtn').classList.toggle('active', evFilterActive);
    evVisible();
  }

  /* ─── MODAL ─── */
  function evOpenModal(prefill) {
    evClearForm();
    document.getElementById('evModalTitle').textContent = prefill ? 'Edit Event' : 'Add Events';
    document.getElementById('evEditId').value = prefill ? prefill.id : '';

    if (prefill) {
      document.getElementById('evFTitle').value = prefill.title || '';
      document.getElementById('evFLocation').value = prefill.location || '';
      document.getElementById('evFDesc').value = prefill.description || prefill.desc || '';
      document.getElementById('evFDate').value = prefill.date || ''; // Assuming backend holds YYYY-MM-DD
      document.getElementById('evFTime').value = prefill.time || ''; // Assuming backend holds HH:mm:ss
      document.getElementById('evFImgExisting').value = prefill.imagel || prefill.image || '';

      if (prefill.category && typeof prefill.category === 'object') {
        evSelectCat(prefill.category.name, prefill.category.id);
      } else if (prefill.category_id) {
        const found = evCategories.find(c => c.id === prefill.category_id);
        const catName = found ? found.name : 'Unknown';
        evSelectCat(catName, prefill.category_id);
      } else if (typeof prefill.category === 'string') {
        evSelectCat(prefill.category);
      }

      const statusEl = document.getElementById('evFStatus');
      statusEl.value = prefill.status || '';
      statusEl.style.color = prefill.status ? 'var(--text)' : '#bbb';

      const prefillImg = prefill.imagel || prefill.image || null;
      if (prefillImg) {
        const thumb = document.getElementById('evImgThumb');
        const src = prefillImg.startsWith('http') || prefillImg.startsWith('data:') ?
          prefillImg : '/storage/' + prefillImg;
        thumb.src = src;
        thumb.style.display = 'block';
        document.getElementById('evFileName').textContent = 'Current image';
      }
    }
    document.getElementById('evModal').classList.add('open');
  }

  function evPreviewImg(input) {
    const file = input.files[0];
    const thumb = document.getElementById('evImgThumb');
    const label = document.getElementById('evFileName');
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

  /* Category dropdown */
  function evToggleCat() {
    document.getElementById('evCatTrigger').classList.toggle('open');
    document.getElementById('evCatDropdown').classList.toggle('open');
  }

  function evSelectCat(name, id) {
    document.getElementById('evFCategoryId').value = id; // Store the ID
    const disp = document.getElementById('evCatDisplay');
    disp.textContent = name;
    disp.classList.add('selected');
    document.getElementById('evCatTrigger').classList.remove('open');
    document.getElementById('evCatDropdown').classList.remove('open');
  }

  function evAddCategory() {
    document.getElementById('evCatTrigger').classList.remove('open');
    document.getElementById('evCatDropdown').classList.remove('open');
    document.getElementById('evNewCatInput').value = '';
    document.getElementById('evCatModal').classList.add('open');
    setTimeout(() => document.getElementById('evNewCatInput').focus(), 100);
  }

  function evCloseCatModal() {
    document.getElementById('evCatModal').classList.remove('open');
  }

  function evHandleCatOverlay(e) {
    if (e.target === document.getElementById('evCatModal')) evCloseCatModal();
  }
  async function evConfirmCategory() {
    const name = document.getElementById('evNewCatInput').value.trim();
    if (!name) {
      document.getElementById('evNewCatInput').focus();
      return;
    }

    try {
      const response = await fetch(`${window.API_BASE_URL}/v1/categories`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          name: name
        })
      });

      const data = await response.json();

      if (response.ok || response.status === 201) {
        const newCat = data.data ? data.data : data;
        evCategories.push(newCat);
        renderCategoryOptions();
        evSelectCat(newCat.name || newCat.category_name || newCat.title || name, newCat.id);
        evCloseCatModal();
      } else {
        alert('Failed to add category: ' + (data.message || 'Unknown error'));
      }
    } catch (err) {
      console.error('Error adding category:', err);
      alert('Network error. Failed to add category.');
    }
  }
  /* ─── EDIT / UPDATE CATEGORY ─── */
  function evEditCategory(id, name) {
    document.getElementById('evEditCatId').value = id;
    document.getElementById('evEditCatInput').value = name;
    document.getElementById('evCatTrigger').classList.remove('open');
    document.getElementById('evCatDropdown').classList.remove('open');
    document.getElementById('evEditCatModal').classList.add('open');
    setTimeout(() => document.getElementById('evEditCatInput').focus(), 100);
  }

  function evCloseEditCatModal() {
    document.getElementById('evEditCatModal').classList.remove('open');
  }

  function evHandleEditCatOverlay(e) {
    if (e.target === document.getElementById('evEditCatModal')) evCloseEditCatModal();
  }

  async function evUpdateCategory() {
    const id = document.getElementById('evEditCatId').value;
    const name = document.getElementById('evEditCatInput').value.trim();
    if (!name) {
      document.getElementById('evEditCatInput').focus();
      return;
    }

    try {
      const response = await fetch(`${window.API_BASE_URL}/v1/categories/${id}`, {
        method: 'PUT',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          name: name
        })
      });

      const data = await response.json();

      if (response.ok) {
        const idx = evCategories.findIndex(c => c.id == id);
        if (idx !== -1) {
          evCategories[idx] = data.data ? data.data : {
            ...evCategories[idx],
            name: name
          };
        }
        renderCategoryOptions();
        evCloseEditCatModal();
        // Refresh events to show updated category name
        evVisible();
      } else {
        alert('Failed to update category: ' + (data.message || 'Unknown error'));
      }
    } catch (err) {
      console.error('Error updating category:', err);
      alert('Network error. Failed to update category.');
    }
  }

  document.addEventListener('click', e => {
    if (!document.getElementById('evCatWrap')?.contains(e.target)) {
      document.getElementById('evCatTrigger')?.classList.remove('open');
      document.getElementById('evCatDropdown')?.classList.remove('open');
    }
  });

  function evClearForm() {
    document.getElementById('evFTitle').value = '';

    // UPDATE THIS LINE: change 'evFCategory' to 'evFCategoryId'
    if (document.getElementById('evFCategoryId')) {
      document.getElementById('evFCategoryId').value = '';
    }

    document.getElementById('evCatDisplay').textContent = 'Category';
    document.getElementById('evCatDisplay').classList.remove('selected');
    document.getElementById('evFDate').value = '';
    document.getElementById('evFTime').value = '';
    document.getElementById('evFLocation').value = '';
    document.getElementById('evFDesc').value = '';
    document.getElementById('evFStatus').value = '';
    document.getElementById('evFStatus').style.color = '#bbb';
    document.getElementById('evFImg').value = '';
    document.getElementById('evFileName').textContent = 'No File Upload';
    document.getElementById('evImgThumb').style.display = 'none';
    document.getElementById('evImgThumb').src = '';
    document.getElementById('evFImgExisting').value = '';
  }

  function evCloseModal() {
    document.getElementById('evModal').classList.remove('open');
  }

  function evHandleOverlay(e) {
    if (e.target === document.getElementById('evModal')) evCloseModal();
  }

  /* ─── API SAVE EVENT ─── */
  async function evSave() {
    const title = document.getElementById('evFTitle').value.trim();
    //const category = document.getElementById('evFCategory').value || 'General';
    const categoryId = document.getElementById('evFCategoryId').value;
    const status = document.getElementById('evFStatus').value;
    const date = document.getElementById('evFDate').value;
    const time = document.getElementById('evFTime').value;
    const location = document.getElementById('evFLocation').value.trim();
    const desc = document.getElementById('evFDesc').value.trim();
    const editId = document.getElementById('evEditId').value;

    const fileInput = document.getElementById('evFImg');

    if (!title || !location) {
      alert('Please fill in the title and location.');
      return;
    }

    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled = true;
    saveBtn.innerText = 'Saving...';

    // Since we are uploading images, we must use FormData
    let formData = new FormData();
    formData.append('title', title);
    formData.append('category_id', categoryId);
    if (status) formData.append('status', status);
    if (date) formData.append('date', date);
    if (time) formData.append('time', time);
    formData.append('location', location);
    formData.append('description', desc);

    // Append file if selected
    if (fileInput.files.length > 0) {
      formData.append('image', fileInput.files[0]);
    }

    // For update, use _method PUT (Laravel method spoofing)
    if (editId) {
      formData.append('_method', 'PUT');
    }

    const url = editId ? `${window.API_BASE_URL}/v1/events/${editId}` : `${window.API_BASE_URL}/v1/events`;

    try {
      const response = await fetch(url, {
        method: 'POST', // POST for both create and update when sending multipart/form-data
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
          // NOTE: Do NOT set 'Content-Type': 'multipart/form-data'. 
          // The browser automatically sets it with the correct boundary when using FormData.
        },
        body: formData
      });

      const data = await response.json();

      if (response.ok || response.status === 201) {
        evCloseModal();
        fetchEvents(); // Reload the UI with fresh data from DB
      } else {
        let errorMsg = data.message || 'Validation Error';
        if (data.errors) {
          errorMsg += '\n' + Object.entries(data.errors).map(([field, msgs]) => `${field}: ${Array.isArray(msgs) ? msgs.join(', ') : msgs}`).join('\n');
        }
        alert('Failed to save event: ' + errorMsg);
      }
    } catch (err) {
      console.error(err);
      alert('Network error. Failed to save.');
    } finally {
      saveBtn.disabled = false;
      saveBtn.innerText = 'Confirm';
    }
  }

  /* ─── DELETE EVENT ─── */
  async function evDelete(id) {
    if (!confirm('Are you sure you want to delete this event?')) return;

    try {
      const response = await fetch(`${window.API_BASE_URL}/v1/events/${id}`, {
        method: 'DELETE',
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

      if (response.ok || response.status === 204) {
        fetchEvents();
      } else {
        const data = await response.json();
        alert('Failed to delete event: ' + (data.message || 'Unknown error'));
      }
    } catch (err) {
      console.error(err);
      alert('Network error. Failed to delete.');
    }
  }

  /* ─── VIEW MODAL ─── */
  function evView(id) {
    const ev = evEvents.find(e => e.id === id);
    if (!ev) return;

    document.getElementById('evViewTitle').textContent = ev.title || 'Untitled';
    let viewCat = 'General';
    if (ev.category && typeof ev.category === 'object') {
      viewCat = ev.category.name || 'General';
    } else if (typeof ev.category === 'string') {
      viewCat = ev.category;
    }
    document.getElementById('evViewTag').textContent = viewCat;

    document.getElementById('evViewDate').textContent = ev.date || 'TBA';
    document.getElementById('evViewTime').textContent = ev.time || 'TBA';
    document.getElementById('evViewLocation').textContent = ev.location || 'TBA';

    const statusEl = document.getElementById('evViewStatus');
    const stat = ev.status || 'Upcoming';
    statusEl.textContent = stat;
    statusEl.className = 'ev-status ' + stat.toLowerCase();

    document.getElementById('evViewDesc').textContent = ev.description || ev.desc || 'No description provided.';

    const img = document.getElementById('evViewImg');
    const ph = document.getElementById('evViewImgPlaceholder');
    ph.className = 'ev-card-img-placeholder ' + (ev.color || 'c1');
    const viewImg = ev.imagel || ev.image || null;
    if (viewImg) {
      const src = viewImg.startsWith('http') || viewImg.startsWith('data:') ?
        viewImg : '/storage/' + viewImg;
      img.src = src;
      img.style.display = 'block';
      ph.style.display = 'none';
    } else {
      img.style.display = 'none';
      ph.style.display = 'flex';
    }

    document.getElementById('evViewEditBtn').onclick = () => {
      evCloseViewModal();
      evEdit(id);
    };
    document.getElementById('evViewModal').classList.add('open');
  }

  function evCloseViewModal() {
    document.getElementById('evViewModal').classList.remove('open');
  }

  function evHandleViewOverlay(e) {
    if (e.target === document.getElementById('evViewModal')) evCloseViewModal();
  }

  function evEdit(id) {
    const ev = evEvents.find(e => e.id === id);
    if (ev) evOpenModal(ev);
  }
</script>
@endsection