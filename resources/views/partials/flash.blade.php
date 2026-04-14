@if (session('status_success'))
    @php $statusSuccessTimeout = (int) session('status_success_timeout', 5000); @endphp
    <div class="alert alert-success alert-dismissible fade show flash-auto" role="alert" data-auto-dismiss="{{ $statusSuccessTimeout }}">
        {{ session('status_success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @if (session('status_success_redirect'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.setTimeout(function () {
                    window.location.href = @json(session('status_success_redirect'));
                }, {{ $statusSuccessTimeout }});
            });
        </script>
    @endif
@endif

@if (isset($status_success))
    <div class="alert alert-success alert-dismissible fade show flash-auto" role="alert" data-auto-dismiss="{{ (int) ($status_success_timeout ?? 5000) }}">
        {{ $status_success }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('status_error'))
    <div class="alert alert-danger" role="alert">{{ session('status_error') }}</div>
@endif

@if (isset($status_error))
    <div class="alert alert-danger" role="alert">{{ $status_error }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>Controleer je invoer:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
