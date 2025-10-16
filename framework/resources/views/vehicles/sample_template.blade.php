@extends('layouts.sample')

@section('title', 'Vehicle Import Sample Template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt"></i> Vehicle Import Sample Template
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" onclick="window.close()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Instructions:</strong> Copy the data below and paste it into Excel or any spreadsheet application. Each row represents a vehicle with all the required and optional fields.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Registration Plate</th>
                                    <th>Make</th>
                                    <th>Model</th>
                                    <th>Year</th>
                                    <th>Color</th>
                                    <th>Vehicle Type</th>
                                    <th>Fuel Type</th>
                                    <th>Mileage</th>
                                    <th>VIN</th>
                                    <th>Price</th>
                                    <th>Price Period</th>
                                    <th>Initial Cost</th>
                                    <th>MOT Expiry</th>
                                    <th>Telematics Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $lines = explode("\n", $content);
                                    $headerSkipped = false;
                                @endphp
                                @foreach($lines as $line)
                                    @if(trim($line) && !$headerSkipped)
                                        @php $headerSkipped = true; @endphp
                                        @continue
                                    @endif
                                    @if(trim($line))
                                        @php
                                            $fields = explode("\t", $line);
                                        @endphp
                                        <tr>
                                            @foreach($fields as $field)
                                                <td>{{ trim($field) }}</td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <h5>Field Descriptions:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li><strong>Registration Plate:</strong> Vehicle registration number (Required)</li>
                                    <li><strong>Make:</strong> Vehicle manufacturer (Required)</li>
                                    <li><strong>Model:</strong> Vehicle model (Required)</li>
                                    <li><strong>Year:</strong> Manufacturing year (Optional)</li>
                                    <li><strong>Color:</strong> Vehicle color (Optional)</li>
                                    <li><strong>Vehicle Type:</strong> Convertible, Coupe, Estate, Hatchback, Minibus, MPV, Pickup, Saloon, SUV (Optional)</li>
                                    <li><strong>Fuel Type:</strong> Petrol, Diesel, Electric, Hybrid (Optional)</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li><strong>Mileage:</strong> Current mileage in miles (Optional)</li>
                                    <li><strong>VIN:</strong> Vehicle Identification Number (Optional)</li>
                                    <li><strong>Price:</strong> Rental price in £ (Optional)</li>
                                    <li><strong>Price Period:</strong> Weekly or Monthly (Optional)</li>
                                    <li><strong>Initial Cost:</strong> Purchase cost in £ (Optional)</li>
                                    <li><strong>MOT Expiry:</strong> DD/MM/YY format (Optional)</li>
                                    <li><strong>Telematics Link:</strong> URL to telematics system (Optional)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="button" class="btn btn-primary" onclick="copyToClipboard()">
                            <i class="fas fa-copy"></i> Copy All Data
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="window.close()">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard() {
    const content = `{!! addslashes($content) !!}`;
    navigator.clipboard.writeText(content).then(function() {
        alert('Sample data copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = content;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Sample data copied to clipboard!');
    });
}
</script>
@endsection
