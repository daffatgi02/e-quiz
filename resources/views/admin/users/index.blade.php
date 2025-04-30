{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <style>
        .search-result-item:hover {
            background-color: #f8f9fa;
        }

        mark {
            background-color: #ffeaa7;
            padding: 0;
        }

        #searchResults {
            border: 1px solid #dee2e6;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>{{ __('general.users') }}</h1>
                    <div>
                        <div class="dropdown d-inline-block me-2">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="exportDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download"></i> Download ID-Trainer
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                <li><a class="dropdown-item" href="{{ route('admin.tokens.download') }}">Semua Peserta</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <h6 class="dropdown-header">Berdasarkan Filter Saat Ini</h6>
                                </li>
                                @if (request()->has('department') || request()->has('position') || request()->has('perusahaan') || request()->has('search'))
                                    <li><a class="dropdown-item"
                                            href="{{ route('admin.tokens.download', [
                                                'department' => request('department'),
                                                'position' => request('position'),
                                                'perusahaan' => request('perusahaan'),
                                                'search' => request('search'),
                                            ]) }}">
                                            {{ request('department') ? 'Dept: ' . request('department') : '' }}
                                            {{ request('position') ? 'Posisi: ' . request('position') : '' }}
                                            {{ request('perusahaan') ? 'Perusahaan: ' . request('perusahaan') : '' }}
                                            {{ request('search') ? 'Kata kunci: ' . request('search') : '' }}
                                        </a></li>
                                @endif
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                {{-- <li>
                                    <h6 class="dropdown-header">Berdasarkan Departemen</h6>
                                </li>
                                @foreach ($departments as $dept)
                                    <li><a class="dropdown-item"
                                            href="{{ route('admin.tokens.download', ['department' => $dept]) }}">{{ $dept }}</a>
                                    </li>
                                @endforeach --}}
                                {{-- <li>
                                    <h6 class="dropdown-header">Berdasarkan Posisi</h6>
                                </li>
                                @foreach ($positions as $pos)
                                    <li><a class="dropdown-item"
                                            href="{{ route('admin.tokens.download', ['position' => $pos]) }}">{{ $pos }}</a>
                                    </li>
                                @endforeach --}}
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <h6 class="dropdown-header">Berdasarkan Perusahaan</h6>
                                </li>
                                @foreach ($companies as $company)
                                    <li><a class="dropdown-item"
                                            href="{{ route('admin.tokens.download', ['perusahaan' => $company]) }}">{{ $company }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            {{ __('general.create') }} {{ __('general.user') }}
                        </a>
                    </div>
                </div>

                <!-- Search & Filter Section -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <form method="GET" id="searchFilterForm" class="row g-3">
                            <!-- Search Bar (Live Search) -->
                            <div class="col-md-12 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" name="search" id="liveSearch" class="form-control"
                                        placeholder="Cari berdasarkan nama, NIK, posisi, departemen, atau perusahaan..."
                                        value="{{ request('search') }}" autocomplete="off">
                                    @if (request('search'))
                                        <button type="button" id="clearSearch" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                                <div id="searchResults"
                                    class="position-absolute bg-white shadow-sm rounded mt-1 w-100 d-none"
                                    style="z-index: 1000; max-height: 300px; overflow-y: auto;"></div>
                            </div>

                            <!-- Filter Fields -->
                            <div class="col-md-12 mb-3">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <select name="department" class="form-select">
                                            <option value="">{{ __('general.all_departments') }}</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <select name="position" class="form-select">
                                            <option value="">{{ __('general.all_positions') }}</option>
                                            @foreach($positions as $pos)
                                                <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <select name="perusahaan" class="form-select">
                                            <option value="">{{ __('general.all_companies') }}</option>
                                            @foreach($companies as $company)
                                                <option value="{{ $company }}" {{ request('perusahaan') == $company ? 'selected' : '' }}>{{ $company }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-1"></i> {{ __('general.filter') }}
                                    </button>
                                    @if(request()->anyFilled(['department', 'position', 'perusahaan', 'search']))
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary ms-2">
                                            <i class="fas fa-times me-1"></i> {{ __('general.clear_filters') }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="card shadow">
                                <div class="card-body">
                                    @if ($users->isEmpty())
                                        <div class="alert alert-info text-center">
                                            <i class="fas fa-info-circle me-2"></i> Tidak ada data yang ditemukan dengan
                                            kriteria pencarian tersebut.
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>{{ __('general.name') }}</th>
                                                        <th>{{ __('general.nik') }}</th>
                                                        <th>ID-Trainer</th>
                                                        <th>PIN Status</th>
                                                        <th>{{ __('general.department') }}</th>
                                                        <th>{{ __('general.position') }}</th>
                                                        <th>{{ __('general.company') }}</th>
                                                        <th>{{ __('general.status') }}</th>
                                                        <th>{{ __('general.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($users as $user)
                                                        <tr>
                                                            <td>{{ $user->name }}</td>
                                                            <td>{{ $user->nik }}</td>
                                                            <td>
                                                                @if ($user->login_token)
                                                                    <span
                                                                        class="badge bg-light text-dark font-monospace">{{ $user->login_token }}</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($user->pin_set)
                                                                    <span class="badge bg-success">PIN Set</span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark">PIN Not
                                                                        Set</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $user->department }}</td>
                                                            <td>{{ $user->position }}</td>
                                                            <td>{{ $user->perusahaan ?? '-' }}</td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                                                    {{ __('general.' . ($user->is_active ? 'active' : 'inactive')) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="dropdown">
                                                                    <button class="btn btn-sm btn-secondary dropdown-toggle"
                                                                        type="button"
                                                                        id="actionDropdown{{ $user->id }}"
                                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                                        {{ __('general.actions') }}
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-end"
                                                                        aria-labelledby="actionDropdown{{ $user->id }}">
                                                                        @if (!$user->login_token)
                                                                            <li>
                                                                                <form
                                                                                    action="{{ route('admin.tokens.generate', $user) }}"
                                                                                    method="POST">
                                                                                    @csrf
                                                                                    <button type="submit"
                                                                                        class="dropdown-item">
                                                                                        <i class="fas fa-key me-2"></i>
                                                                                        Generate Token
                                                                                    </button>
                                                                                </form>
                                                                            </li>
                                                                        @endif

                                                                        @if ($user->pin_set)
                                                                            <li>
                                                                                <form
                                                                                    action="{{ route('admin.tokens.reset-pin', $user) }}"
                                                                                    method="POST">
                                                                                    @csrf
                                                                                    <button type="submit"
                                                                                        class="dropdown-item"
                                                                                        onclick="return confirm('Reset PIN untuk {{ $user->name }}?')">
                                                                                        <i class="fas fa-undo me-2"></i>
                                                                                        Reset PIN
                                                                                    </button>
                                                                                </form>
                                                                            </li>
                                                                        @endif

                                                                        <li>
                                                                            <a class="dropdown-item"
                                                                                href="{{ route('admin.users.edit', $user) }}">
                                                                                <i class="fas fa-edit me-2"></i>
                                                                                {{ __('general.edit') }}
                                                                            </a>
                                                                        </li>

                                                                        <li>
                                                                            <form
                                                                                action="{{ route('admin.users.toggle-status', $user) }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                <button type="submit"
                                                                                    class="dropdown-item">
                                                                                    <i
                                                                                        class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} me-2 text-{{ $user->is_active ? 'danger' : 'success' }}"></i>
                                                                                    {{ $user->is_active ? __('general.deactivate') : __('general.activate') }}
                                                                                </button>
                                                                            </form>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    <!-- Improved Pagination -->
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <p class="text-muted">
                                                Menampilkan {{ $users->firstItem() ?? 0 }} sampai
                                                {{ $users->lastItem() ?? 0 }} dari
                                                {{ $users->total() }} data
                                            </p>
                                        </div>
                                        <div>
                                            {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('liveSearch');
                const searchResultsContainer = document.getElementById('searchResults');
                const searchForm = document.getElementById('searchFilterForm');
                const clearSearchBtn = document.getElementById('clearSearch');
                let searchTimeout;

                // Live search as user types
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.trim();

                    // Clear previous timeout
                    clearTimeout(searchTimeout);

                    if (searchTerm.length < 2) {
                        searchResultsContainer.classList.add('d-none');
                        return;
                    }

                    // Set a small delay to avoid too many requests
                    searchTimeout = setTimeout(() => {
                        fetchSearchResults(searchTerm);
                    }, 300);
                });

                // Focus out - hide results after a short delay (allows for clicking results)
                searchInput.addEventListener('blur', function() {
                    setTimeout(() => {
                        searchResultsContainer.classList.add('d-none');
                    }, 200);
                });

                // Focus in - show results if we have a search term
                searchInput.addEventListener('focus', function() {
                    if (this.value.trim().length >= 2) {
                        fetchSearchResults(this.value.trim());
                    }
                });

                // Clear search button
                if (clearSearchBtn) {
                    clearSearchBtn.addEventListener('click', function() {
                        searchInput.value = '';
                        searchResultsContainer.classList.add('d-none');
                        // Submit the form to reset search
                        searchForm.submit();
                    });
                }

                // Fetch search results with AJAX
                function fetchSearchResults(searchTerm) {
                    fetch(`{{ route('admin.users.search') }}?term=${encodeURIComponent(searchTerm)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                renderSearchResults(data);
                                searchResultsContainer.classList.remove('d-none');
                            } else {
                                searchResultsContainer.innerHTML =
                                    `<div class="p-3 text-muted">Tidak ada hasil ditemukan</div>`;
                                searchResultsContainer.classList.remove('d-none');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching search results:', error);
                        });
                }

                // Render search results
                function renderSearchResults(results) {
                    searchResultsContainer.innerHTML = '';

                    results.forEach(user => {
                        const resultItem = document.createElement('div');
                        resultItem.className = 'p-2 search-result-item border-bottom';
                        resultItem.style.cursor = 'pointer';

                        resultItem.innerHTML = `
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>${highlightMatch(user.name, searchInput.value)}</strong>
                        <small class="d-block text-muted">NIK: ${highlightMatch(user.nik, searchInput.value)}</small>
                        <small class="d-block text-muted">${highlightMatch(user.perusahaan || '-', searchInput.value)}</small>
                    </div>
                    <div class="text-end">
                        <small class="badge bg-secondary">${highlightMatch(user.department || '-', searchInput.value)}</small>
                        <small class="d-block">${highlightMatch(user.position, searchInput.value)}</small>
                    </div>
                </div>
            `;

                        resultItem.addEventListener('click', function() {
                            window.location.href = "{{ route('admin.users.index') }}?search=" +
                                encodeURIComponent(user.name);
                        });

                        searchResultsContainer.appendChild(resultItem);
                    });
                }

                // Highlight matching text
                function highlightMatch(text, query) {
                    if (!text) return '';

                    const regex = new RegExp(`(${query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')})`, 'gi');
                    return text.replace(regex, '<mark>$1</mark>');
                }

                // Make department, position, and company filters submit the form on change
                document.querySelectorAll('select[name="department"], select[name="position"], select[name="perusahaan"]').forEach(select => {
                    select.addEventListener('change', function() {
                        searchForm.submit();
                    });
                });
            });
        </script>
    @endpush
@endsection
