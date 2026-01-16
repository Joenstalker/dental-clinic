@include('assistant.layout.header')

<body class="hold-transition sidebar-mini">
  <div class="wrapper">

    @include('assistant.layout.navbar')

    @include('assistant.layout.sidebar')

    <!-------------------------------------- Main content ---------------------------------------->

    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title" style="font-size: 2em">Pending Account</h3>
            </div>
            <div class="card-body">
              @if($patients->count() > 0)
                <table id="patientTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Full Name</th>
                      <th>Email</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($patients as $patient)
                      <tr>
                        <td>{{ $patient->full_name }}</td>
                        <td>{{ $patient->email }}</td>
                        <td>
                          <span class="badge badge-warning">{{ ucfirst($patient->status) }}</span>
                        </td>
                        <td>
                          <form action="{{ route('assistant.update-status', ['id' => $patient->id]) }}" method="post">
                            @csrf
                            @method('patch')
                            <button type="submit" class="btn btn-success">
                              <i class="fas fa-check"></i> Approve Account
                            </button>
                          </form>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              @else
                <div class="alert alert-info text-center">
                  <i class="fas fa-info-circle"></i> No pending accounts at this time. All patient accounts have been approved.
                </div>
              @endif
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
    <!-------------------------------------- Main content ---------------------------------------->

    @include('assistant.layout.footer')
  </div>

  <!----Sweet Alert---->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

  <!-- Include DataTables CSS and JS -->
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
  <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

  <script>
    $(function () {
      $("#patientTable").DataTable({
        responsive: true,
        autoWidth: false,
      });
    });
  </script>

  
</body>
</html>
