@include('patient.layout.header')
<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    @include('patient.layout.navbar')
    @include('patient.layout.sidebar')

    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Book an Appointment</h1>
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#directBookingModal">
                <i class="fas fa-calendar-plus"></i> Direct Booking
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="container mt-4">
        <div id="calendar"></div>
      </div>
    </div>

    @include('patient.layout.footer')
  </div>

  <!-- FullCalendar and dependencies -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    var calendar; // Make calendar global
    document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events: '/patient/appointments',
      displayEventTime: false,

      eventDidMount: function(info) {
        // Extract remaining slots from the event title (assuming title contains "Slots: X")
        const eventTitleEl = info.el.querySelector('.fc-event-title');
        const remainingSlots = parseInt(eventTitleEl.textContent.split(':')[1]);

        // Set background color based on remaining slots
        if (remainingSlots === 0) {
          info.el.style.backgroundColor = '#dc3545';  // red for no slots
        } else if (remainingSlots > 1) {
          info.el.style.backgroundColor = '#28a745';  // green for available slots
        } else {
          info.el.style.backgroundColor = '#ffc107';  // yellow for few slots
        }

        // Ensure text color is white for readability
        info.el.style.color = 'white';
        info.el.style.padding = '2px 4px';
        info.el.style.borderRadius = '4px';
      },

      eventClick: function(info) {
        const appointmentId = info.event.extendedProps.appointmentId;
        const remainingSlots = parseInt(info.event.title.split(':')[1]);

        if (remainingSlots > 0) {
          window.location.href = `/patient/appointment/${appointmentId}/details`;
        } else {
          Swal.fire({
            title: 'No Available Slots',
            text: 'Sorry, this session is fully booked.',
            icon: 'error',
            confirmButtonText: 'OK'
          });
        }
      }
    });

    calendar.render();
  });
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

  <!-- Direct Booking Modal -->
  <div class="modal fade" id="directBookingModal" tabindex="-1" role="dialog" aria-labelledby="directBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="directBookingModalLabel">Direct Appointment Booking</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="directBookingForm">
            @csrf
            <div class="alert alert-danger d-none" id="directBookingErrors">
              <ul class="mb-0" id="directBookingErrorList"></ul>
            </div>

            <div class="form-group">
              <label for="directDoctor">Select Doctor: <span class="text-danger">*</span></label>
              <select class="form-control" id="directDoctor" name="dentist" required>
                <option value="">-- Select a Doctor --</option>
                @foreach($dentists ?? [] as $dentist)
                  <option value="{{ $dentist->id }}">{{ $dentist->full_name }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="directDate">Appointment Date: <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="directDate" name="appointment_date" required min="{{ date('Y-m-d') }}">
              <small class="form-text text-muted">Select a date from today onwards</small>
            </div>

            <div class="form-group">
              <label for="directTime">Select Time Slot: <span class="text-danger">*</span></label>
              <select class="form-control" id="directTime" name="appointment_time" required>
                <option value="">-- Select Time --</option>
                @php
                  $start = \Carbon\Carbon::createFromFormat('H:i', '08:00');
                @endphp
                @foreach(range(0, 15) as $i)
                  @php
                    $startTime = $start->copy()->addMinutes($i * 30);
                    $endTime = $startTime->copy()->addMinutes(30);
                    $formattedStartTime = $startTime->format('h:i A');
                    $formattedEndTime = $endTime->format('h:i A');
                    $slot = $formattedStartTime . ' - ' . $formattedEndTime;
                  @endphp
                  <option value="{{ $slot }}">{{ $slot }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="directService">Service/Reason: <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="directService" name="service" placeholder="e.g., General Checkup, Cleaning, Consultation" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="submitDirectBooking()">Book Appointment</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function submitDirectBooking() {
      const form = document.getElementById('directBookingForm');
      const formData = new FormData(form);
      
      // Clear previous errors
      document.getElementById('directBookingErrors').classList.add('d-none');
      document.getElementById('directBookingErrorList').innerHTML = '';

      $.ajax({
        url: "{{ route('patient.direct-booking') }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Booked!',
              text: response.message || 'Your appointment has been booked successfully.',
              confirmButtonText: 'OK'
            }).then(() => {
              $('#directBookingModal').modal('hide');
              form.reset();
              // Reload calendar
              if (typeof calendar !== 'undefined') {
                calendar.refetchEvents();
              }
              // Optionally reload page
              setTimeout(() => {
                window.location.reload();
              }, 1000);
            });
          }
        },
        error: function(xhr) {
          document.getElementById('directBookingErrors').classList.remove('d-none');
          if (xhr.responseJSON && xhr.responseJSON.errors) {
            const errors = xhr.responseJSON.errors;
            $.each(errors, function(key, value) {
              document.getElementById('directBookingErrorList').innerHTML += '<li>' + value[0] + '</li>';
            });
          } else {
            document.getElementById('directBookingErrorList').innerHTML = '<li>' + (xhr.responseJSON?.message || 'An error occurred. Please try again.') + '</li>';
          }
          Swal.fire({
            icon: 'error',
            title: 'Booking Failed',
            text: 'Please fix the errors below and try again.',
            confirmButtonText: 'OK'
          });
        }
      });
    }
  </script>
</body>
</html>