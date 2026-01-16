@include('assistant.layout.header')

<body class="hold-transition sidebar-mini">
  <div class="wrapper">

    @include('assistant.layout.navbar')

    @include('assistant.layout.sidebar')

    <!-------------------------------------- Main content ---------------------------------------->

    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Pending Appointments</h3>
            </div>
            <div class="card-body">
              @if($appointments->count() > 0)
                <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Patient Name</th>
                        <th>Doctor</th>
                        <th>Session Title</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($appointments as $appointment)
                      <tr>
                        <td>{{ $appointment->user->full_name }}</td>
                        <td>{{ $appointment->appointmentSession->user->full_name }}</td>
                        <td>{{ $appointment->appointmentSession->session_title }}</td>
                        <td>{{ date('F j, Y', strtotime($appointment->appointmentSession->schedule_date)) }}</td>
                        <td>{{ $appointment->time }}</td>
                        <td>
                          <span class="badge badge-warning">{{ ucfirst($appointment->status) }}</span>
                        </td>
                        <td>
                          <button class="btn btn-sm btn-info view-btn" data-id="{{ $appointment->id }}">
                            <i class="fas fa-eye"></i> View
                          </button>
                          <button class="btn btn-sm btn-success approve-btn" data-id="{{ $appointment->id }}">
                            <i class="fas fa-check"></i> Approve
                          </button>
                          <button class="btn btn-sm btn-danger disapprove-btn" data-id="{{ $appointment->id }}">
                            <i class="fas fa-times"></i> Disapprove
                          </button>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="alert alert-info text-center">
                  <i class="fas fa-info-circle"></i> No pending appointments at this time. All appointment requests have been processed.
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
            <h5 class="modal-title text-center" id="patientDetailsModalLabel">CUDAL-BLANCO DENTAL CLINIC<br>Appointment Completion Report</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="patientDetailsContent">
            <!-- Content will be populated dynamically -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="printButton">Print</button>
          </div>
        </div>
      </div>
    </div>

    @include('assistant.layout.footer')
  </div>

  <!----Sweet Alert---->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Wait for jQuery to be loaded
    document.addEventListener('DOMContentLoaded', function() {
      // Use setTimeout to ensure jQuery is loaded after footer includes
      setTimeout(function() {
        // Approve button logic
        $(document).on('click', '.approve-btn', function(e) {
          e.preventDefault();
          const appointmentId = $(this).data('id');
          const button = $(this);
          
          Swal.fire({
            title: 'Approve Appointment?',
            text: 'Are you sure you want to approve this appointment?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, approve it!',
            cancelButtonText: 'Cancel'
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                url: '/assistant/appointment-request/approve-appointment/' + appointmentId,
                method: 'POST',
                data: {_token: '{{ csrf_token() }}'},
                success: function(response) {
                  Swal.fire({
                    icon: 'success',
                    title: 'Approved!',
                    text: 'Appointment has been approved successfully.',
                    confirmButtonText: 'OK'
                  }).then(() => {
                    location.reload();
                  });
                },
                error: function(xhr) {
                  let errorMessage = 'Error approving appointment. Please try again.';
                  if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                  }
                  Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                  });
                }
              });
            }
          });
        });

        // Disapprove button logic
        $(document).on('click', '.disapprove-btn', function(e) {
          e.preventDefault();
          const appointmentId = $(this).data('id');
          const button = $(this);
          
          Swal.fire({
            title: 'Disapprove Appointment?',
            text: 'Are you sure you want to disapprove this appointment?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, disapprove it!',
            cancelButtonText: 'Cancel'
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                url: '/assistant/appointment-request/disapprove-appointment/' + appointmentId,
                method: 'POST',
                data: {_token: '{{ csrf_token() }}'},
                success: function(response) {
                  Swal.fire({
                    icon: 'success',
                    title: 'Disapproved!',
                    text: 'Appointment has been disapproved successfully.',
                    confirmButtonText: 'OK'
                  }).then(() => {
                    location.reload();
                  });
                },
                error: function(xhr) {
                  let errorMessage = 'Error disapproving appointment. Please try again.';
                  if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                  }
                  Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                  });
                }
              });
            }
          });
        });

        // View button logic
        $(document).on('click', '.view-btn', function(e) {
          e.preventDefault();
          const appointmentId = $(this).data('id');
          $.ajax({
            url: '/assistant/appointment-request/view-appointment-details/' + appointmentId,
            method: 'GET',
            success: function(response) {
            const appointmentDate = new Date(response.appointment_session.schedule_date).toLocaleString('default', {
              month: 'long', day: 'numeric', year: 'numeric'
            });

            $('#patientDetailsContent').html(`
              <div class="row">
                  <div class="col-md-6">
                      <p><strong>Patient Number:</strong> ${response.user.id}</p>
                      <p><strong>Appointment Date:</strong> ${appointmentDate}</p>
                  </div>
                  <div class="col-md-6 text-right">
                      <p><strong>Patient Name:</strong> <strong>${response.user.full_name}</strong></p>
                      <p><strong>Time:</strong> <strong>${response.time}</strong></p>
                  </div>
              </div>
              <p><strong>Doctor:</strong> ${response.appointment_session.user.full_name}</p>
              <p><strong>Session Title:</strong> ${response.appointment_session.session_title}</p>
              <p><strong>Price:</strong> ₱${response.appointment_session.price}.00</p>
              <p><strong>Status:</strong> ${response.status}</p>
              <p><strong>Address:</strong> ${response.user.address}</p>
            `);
            $('#patientDetailsModal').modal('show');
          },
          error: function(error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Error fetching appointment details. Please try again.',
                confirmButtonText: 'OK'
            });
          }
          });
        });

        // Print button logic
        $(document).on('click', '#printButton', function() {
        const printContent = $('#patientDetailsContent').html();
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Print Appointment Details</title>');
        printWindow.document.write('</head><body >');
        printWindow.document.write('<h1 style="text-align:center;">CUDAL-BLANCO DENTAL CLINIC</h1>');
        printWindow.document.write('<h2 style="text-align:center;">Appointment Completion Report</h2>');
        printWindow.document.write(printContent);
        printWindow.document.write('</body></html>');
        printWindow.document.close(); // necessary for IE >= 10
        printWindow.print();
        });
      }, 100); // Small delay to ensure jQuery is loaded
    });
  </script>

</body>
</html>
