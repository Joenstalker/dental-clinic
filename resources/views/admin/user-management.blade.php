@include('admin.layout.header')

<body class="hold-transition sidebar-mini">
  <div class="wrapper">

    @include('admin.layout.navbar')

    @include('admin.layout.sidebar')

    <!-------------------------------------- Main content ---------------------------------------->

    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">User Management</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">User Management</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3>{{ $totalUsers }}</h3>
              <p>Total Users</p>
            </div>
            <div class="icon">
              <i class="fas fa-users"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>{{ $activeUsers }}</h3>
              <p>Active Users</p>
            </div>
            <div class="icon">
              <i class="fas fa-user-check"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>{{ $inactiveUsers }}</h3>
              <p>Inactive Users</p>
            </div>
            <div class="icon">
              <i class="fas fa-user-times"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-primary">
            <div class="inner">
              <h3>{{ $patients->count() + $dentists->count() + $assistants->count() }}</h3>
              <p>Staff & Patients</p>
            </div>
            <div class="icon">
              <i class="fas fa-user-friends"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <ul class="nav nav-tabs card-header-tabs" id="userTabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="patients-tab" data-toggle="tab" href="#patients" role="tab">
                    <i class="fas fa-users"></i> Patients ({{ $patients->count() }})
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="dentists-tab" data-toggle="tab" href="#dentists" role="tab">
                    <i class="fas fa-user-md"></i> Dentists ({{ $dentists->count() }})
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="assistants-tab" data-toggle="tab" href="#assistants" role="tab">
                    <i class="fas fa-user-nurse"></i> Assistants ({{ $assistants->count() }})
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="admins-tab" data-toggle="tab" href="#admins" role="tab">
                    <i class="fas fa-user-shield"></i> Admins ({{ $admins->count() }})
                  </a>
                </li>
              </ul>
            </div>
            <div class="card-body">
              <div class="tab-content" id="userTabsContent">
                
                <!-- Patients Tab -->
                <div class="tab-pane fade show active" id="patients" role="tabpanel">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Patient List</h5>
                    <a href="{{ route('admin.patient') }}" class="btn btn-primary btn-sm">
                      <i class="fas fa-external-link-alt"></i> View Full Page
                    </a>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                      <thead class="thead-dark">
                        <tr>
                          <th>ID</th>
                          <th>Full Name</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($patients as $patient)
                          <tr>
                            <td>{{ $patient->id }}</td>
                            <td>{{ $patient->full_name }}</td>
                            <td>{{ $patient->email }}</td>
                            <td>{{ $patient->number }}</td>
                            <td>
                              <span class="badge badge-{{ $patient->status == 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($patient->status) }}
                              </span>
                            </td>
                            <td>
                              <a href="#" class="btn btn-sm btn-info view-patient" data-id="{{ $patient->id }}">
                                <i class="fas fa-eye"></i> View
                              </a>
                              @can('updateStatus', $patient)
                                <form action="{{ route('admin.update-status', $patient->id) }}" method="POST" style="display:inline;">
                                  @csrf
                                  @method('PATCH')
                                  <button type="submit" class="btn btn-sm btn-{{ $patient->status == 'active' ? 'warning' : 'success' }}">
                                    <i class="fas fa-{{ $patient->status == 'active' ? 'ban' : 'check' }}"></i>
                                    {{ $patient->status == 'active' ? 'Deactivate' : 'Activate' }}
                                  </button>
                                </form>
                              @endcan
                              @can('delete', $patient)
                                <form action="{{ route('admin.patient-delete', $patient->id) }}" method="POST" style="display:inline;">
                                  @csrf
                                  <button type="submit" class="btn btn-sm btn-danger" onclick="event.preventDefault(); deleteUser(event, this);">
                                    <i class="fas fa-trash"></i> Delete
                                  </button>
                                </form>
                              @endcan
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="6" class="text-center">No patients found</td>
                          </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Dentists Tab -->
                <div class="tab-pane fade" id="dentists" role="tabpanel">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Dentist List</h5>
                    <a href="{{ route('admin.dentist') }}" class="btn btn-primary btn-sm">
                      <i class="fas fa-external-link-alt"></i> View Full Page
                    </a>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                      <thead class="thead-dark">
                        <tr>
                          <th>ID</th>
                          <th>Full Name</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($dentists as $dentist)
                          <tr>
                            <td>{{ $dentist->id }}</td>
                            <td>{{ $dentist->full_name }}</td>
                            <td>{{ $dentist->email }}</td>
                            <td>{{ $dentist->number }}</td>
                            <td>
                              <span class="badge badge-{{ $dentist->status == 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($dentist->status) }}
                              </span>
                            </td>
                            <td>
                              @can('delete', $dentist)
                                <form action="{{ route('admin.dentist-delete', $dentist->id) }}" method="POST" style="display:inline;">
                                  @csrf
                                  <button type="submit" class="btn btn-sm btn-danger" onclick="event.preventDefault(); deleteUser(event, this);">
                                    <i class="fas fa-trash"></i> Delete
                                  </button>
                                </form>
                              @endcan
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="6" class="text-center">No dentists found</td>
                          </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Assistants Tab -->
                <div class="tab-pane fade" id="assistants" role="tabpanel">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Assistant List</h5>
                    <a href="{{ route('admin.assistant') }}" class="btn btn-primary btn-sm">
                      <i class="fas fa-external-link-alt"></i> View Full Page
                    </a>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                      <thead class="thead-dark">
                        <tr>
                          <th>ID</th>
                          <th>Full Name</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($assistants as $assistant)
                          <tr>
                            <td>{{ $assistant->id }}</td>
                            <td>{{ $assistant->full_name }}</td>
                            <td>{{ $assistant->email }}</td>
                            <td>{{ $assistant->number }}</td>
                            <td>
                              <span class="badge badge-{{ $assistant->status == 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($assistant->status) }}
                              </span>
                            </td>
                            <td>
                              @can('delete', $assistant)
                                <form action="{{ route('admin.assistant-delete', $assistant->id) }}" method="POST" style="display:inline;">
                                  @csrf
                                  <button type="submit" class="btn btn-sm btn-danger" onclick="event.preventDefault(); deleteUser(event, this);">
                                    <i class="fas fa-trash"></i> Delete
                                  </button>
                                </form>
                              @endcan
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="6" class="text-center">No assistants found</td>
                          </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Admins Tab -->
                <div class="tab-pane fade" id="admins" role="tabpanel">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Admin List</h5>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                      <thead class="thead-dark">
                        <tr>
                          <th>ID</th>
                          <th>Full Name</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($admins as $admin)
                          <tr>
                            <td>{{ $admin->id }}</td>
                            <td>{{ $admin->full_name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->number }}</td>
                            <td>
                              <span class="badge badge-{{ $admin->status == 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($admin->status) }}
                              </span>
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="5" class="text-center">No admins found</td>
                          </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Patient Details Modal -->
    <div class="modal fade" id="patientDetailsModal" tabindex="-1" aria-labelledby="patientDetailsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="patientDetailsModalLabel">Patient Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="patientDetailsContent">
            <!-- Patient details will be loaded here -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  @include('admin.layout.footer')

  <!----Sweet Alert---->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(document).ready(function() {
      // View patient details
      $('.view-patient').on('click', function(event) {
        event.preventDefault();
        var patientId = $(this).data('id');
        
        $.ajax({
          url: '{{ route('patients.view', '') }}/' + patientId,
          type: 'GET',
          success: function(response) {
            $('#patientDetailsContent').html(response.html);
            $('#patientDetailsModal').modal('show');
          },
          error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Error loading patient details. Please try again.',
                confirmButtonText: 'OK'
            });
          }
        });
      });
    });

    function deleteUser(event, button) {
      event.preventDefault();
      const form = button.closest('form');
      
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    }
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      @if (session('success'))
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: '{{ session('success') }}',
          confirmButtonText: 'OK'
        });
      @endif

      @if (session('error'))
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: '{{ session('error') }}',
          confirmButtonText: 'Try Again'
        });
      @endif
    });
  </script>
</body>
</html>

