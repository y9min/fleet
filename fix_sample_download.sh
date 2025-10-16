#!/bin/bash

# Fix the sample download issue by implementing proper Laravel route

echo "Fixing sample download issue..."

# 1. Add downloadSample method to VehiclesController
echo "Adding downloadSample method to VehiclesController..."
sed -i '' '67a\
        public function downloadSample() {\
                $filePath = public_path("assets/samples/vehicles_sample.txt");\
                \
                if (!file_exists($filePath)) {\
                        abort(404, "Sample file not found");\
                }\
                \
                return response()->download($filePath, "vehicles_sample.txt", [\
                        "Content-Type" => "text/plain",\
                        "Content-Disposition" => "attachment; filename=\"vehicles_sample.txt\""\
                ]);\
        }
' /Users/yaminahmed/fleet/framework/app/Http/Controllers/Admin/VehiclesController.php

# 2. Add route to admin.php
echo "Adding download route to admin.php..."
sed -i '' '122a\
        Route::get("download-vehicle-sample", "VehiclesController@downloadSample")->name("download-vehicle-sample");
' /Users/yaminahmed/fleet/framework/routes/admin.php

# 3. Update the download link in the view
echo "Updating download link in vehicles index view..."
sed -i '' 's|{{ asset("assets/samples/vehicles_sample.txt") }}|{{ route("download-vehicle-sample") }}|g' /Users/yaminahmed/fleet/framework/resources/views/vehicles/index.blade.php

echo "Fix applied successfully!"
echo "The sample download should now work properly."

