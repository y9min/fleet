        public function downloadSample() {
                $filePath = public_path('assets/samples/vehicles_sample.txt');
                
                if (!file_exists($filePath)) {
                        abort(404, 'Sample file not found');
                }
                
                return response()->download($filePath, 'vehicles_sample.txt', [
                        'Content-Type' => 'text/plain',
                        'Content-Disposition' => 'attachment; filename="vehicles_sample.txt"'
                ]);
        }

