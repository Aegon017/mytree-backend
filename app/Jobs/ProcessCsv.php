<?php

namespace App\Jobs;

use App\Models\Admin\Leads;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $filePath;
    protected $empId;
    /**
     * Create a new job instance.
     */
    public function __construct($filePath,$empId)
    {
        Log::debug('111111111111111111111111111.');
        $this->filePath = $filePath;
        $this->empId = $empId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
         // Get the file path
         $path = Storage::path($this->filePath);

         // Open and process the file in chunks
         $handle = fopen($path, 'r');
         $leadsArray = [];
         while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
             // Process each line, for example:
                // Log::debug('running.');
                $leadsArray[] = [
                 'emp_id' => $this->empId,
                 'name' => $data[0],
                 'email' => $data[1],
                 'mobile' => $data[2],
             ];
         }
 
         fclose($handle);
         Leads::insert($leadsArray);
         // Optionally, delete the file after processing
         Storage::delete($this->filePath);
     
    }
}
