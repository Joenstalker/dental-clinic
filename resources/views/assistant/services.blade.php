@include('assistant.layout.header')

<body class="hold-transition sidebar-mini">
  <div class="wrapper">

    @include('assistant.layout.navbar')
    @include('assistant.layout.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Add Service</h1>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <!-- Add Service Form -->
            <div class="col-md-6">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title"><i class="fas fa-plus"></i> Add New Service</h3>
                </div>
                <div class="card-body">
                  <form method="post" action="{{route('assistant.add-service')}}">
                    @csrf
                    <div class="form-group">
                      <label for="service">Service Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="service" name="service" placeholder="e.g., Consultation, Cleaning" required>
                      <small class="form-text text-muted">Enter a unique service name</small>
                    </div>

                    <div class="form-group">
                      <label for="price">Price (₱) <span class="text-danger">*</span></label>
                      <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" placeholder="0.00" required>
                      <small class="form-text text-muted">Enter the price for this service</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                      <i class="fas fa-save"></i> Add Service
                    </button>
                  </form>
                </div>
              </div>
            </div>

            <!-- Services List -->
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title"><i class="fas fa-list"></i> Existing Services</h3>
                </div>
                <div class="card-body">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Service Name</th>
                        <th>Price</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($services ?? [] as $service)
                        <tr>
                          <td>{{ $service->service }}</td>
                          <td>₱{{ number_format($service->price, 2) }}</td>
                          <td>
                            @can('update-services')
                              <button class="btn btn-sm btn-warning" onclick="editService({{ $service->id }}, '{{ $service->service }}', {{ $service->price }})">
                                <i class="fas fa-edit"></i> Edit
                              </button>
                            @endcan
                            @can('delete-services')
                              <form action="{{ route('assistant.delete-service', $service->id) }}" method="POST" style="display:inline;" id="deleteServiceForm{{ $service->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteService({{ $service->id }})">
                                  <i class="fas fa-trash"></i> Delete
                                </button>
                              </form>
                            @endcan
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="3" class="text-center">No services found. Add your first service above.</td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Edit Service Modal -->
      <div class="modal fade" id="editServiceModal" tabindex="-1" role="dialog" aria-labelledby="editServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form method="post" id="editServiceForm">
              @csrf
              @method('PUT')
              <div class="modal-body">
                <div class="form-group">
                  <label for="edit_service">Service Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="edit_service" name="service" required>
                </div>
                <div class="form-group">
                  <label for="edit_price">Price (₱) <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" min="0" class="form-control" id="edit_price" name="price" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Service</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    @include('assistant.layout.footer')
  </div>

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

    function editService(id, service, price) {
        document.getElementById('editServiceForm').action = '{{ url("assistant/services") }}/' + id;
        document.getElementById('edit_service').value = service;
        document.getElementById('edit_price').value = price;
        $('#editServiceModal').modal('show');
    }

    function deleteService(id) {
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
          document.getElementById('deleteServiceForm' + id).submit();
        }
      });
    }
    </script>

</body>
</html>
