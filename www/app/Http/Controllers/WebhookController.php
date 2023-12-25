<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\http\Controllers\DeviceController;
use App\Models\company_leads;
use App\Models\jobs;
use App\Models\people_leads;
use Symfony\Component\HttpFoundation\StreamedResponse;
use League\Csv\Writer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Exception;


class WebhookController extends Controller
{
    public function updateJobStatus(){
        $highestId = jobs::max('ID');
        jobs::where('ID',$highestId)->update(['job_status'=>'done']);
        
        
    }

    
   
    
    public function exportToCSV(Request $request)
    {
        $search_id = $request->header('searchId');
        $columns = [
            'id',
            'full_name',
            'company_name',
            'company_id',
            'regular_company_url',
            'title',
            'mail',
            'person_url',
            'connection_degree',
            'company_location',
            'person_location'
        ];
    
        $data = DB::table('people_leads')
            ->where('search_id', $search_id)
            ->select($columns)
            ->get();
    
        $fileName = 'results_search' . $search_id . '.csv';
    
        $handle = fopen('php://temp', 'w+');
    
        // Add CSV headers
        fputcsv($handle, $columns);
    
        // Add data rows
        foreach ($data as $row) {
            fputcsv($handle, (array)$row);
        }
    
        rewind($handle);
    
        $csvContent = stream_get_contents($handle);
        fclose($handle);
    
        $response = new Response($csvContent, 200, [
            'Content-Type' => 'text/csv',
        ]);
    
        return $response;
    }
    
    

    
    //   // Set appropriate headers for the download
    //   header('Content-Description: File Transfer');
    //   header('Content-Type: application/octet-stream');
    //   header('Content-Disposition: attachment; filename="' . $filename . '"');
    //   header('Expires: 0');
    //   header('Cache-Control: must-revalidate');
    //   header('Pragma: public');

    //   // Read the file and output it directly to the user
    //   readfile($url);
    //   exit;
    public function handle(Request $request)
    {
        $data = $request->all();
       
        $data =  $data = jobs::where('ID', jobs::max('ID'))->select()->first();
        
        if($data->company_search_id != null){
            $search_id = $data->company_search_id;
            $type = "company";
        }
        else{
            $search_id = $data->people_search_id;
            $type = "people";
        }


     


        // require_once('vendor/autoload.php');

        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 'https://api.phantombuster.com/api/v2/agents/fetch?id=8697827096363829', [
        'headers' => [
            'X-Phantombuster-Key' => 'tvKJdE1a7UnxDkVpbj6p4Ju6wOlbP4LVhVgitqfPCEc',
            'accept' => 'application/json',
        ],
        ]);

        $responseBody = json_decode($response->getBody(), true);

        // Store the s3Folder and orgs3Folder values in variables
        $s3Folder = $responseBody['s3Folder'];
        $orgs3Folder = $responseBody['orgS3Folder'];

        // You can do further processing or return the values as needed

        $url = "https://phantombuster.s3.amazonaws.com/{$orgs3Folder}/{$s3Folder}/result.json";

        $data = file_get_contents($url);

        if ($data === false) {
            // Error handling if the request fails
            echo "Failed to fetch data from the URL.";
          } else {
            $jsonData = json_decode($data);

            if ($jsonData === null) {
              // Error handling if JSON decoding fails
              echo "Failed to decode JSON data.";
            }
            else {
              // Process the decoded JSON data
              try {
                if($type=="company"){
                foreach ($jsonData as $data) {
                   if (isset($data->companyId)) {
                        $companyId = $data->companyId;
                    } else {
                        $companyId = 'No id available';
                    }
                    if (isset($data->companyName)) {
                        $companyName = $data->companyName;
                    } else {
                        $companyName = 'No name available';
                    }
                    if (isset($data->description)) {
                        $description = $data->description;
                    } else {
                        $description = 'No description available';
                    }
                   if (isset($data->companyUrl)) {
                        $companyUrl = $data->companyUrl;
                    } else {
                        $companyUrl = 'No url available';
                    }
                    if (isset($data->headcount)) {
                        $headcount = $data->headcount;
                    } else {
                        $headcount = 'No headcount available';
                    }


                    $companyLead = new company_leads();
                    $companyLead->company_id = $companyId;
                    $companyLead->name = $companyName;
                   
                    $companyLead->description = $description;
                    $companyLead->company_url = $companyUrl;
                    $companyLead->headcount = $headcount;
                    $companyLead->search_id = $search_id;
                    $companyLead->created_at = now();
                    $companyLead->updated_at = now();
                    $companyLead->is_active = 1;

                    $companyLead->save();
                }}
                if($type=="people"){
                    foreach ($jsonData as $record) {


                        if (!isset($record->companyId) || !isset($record->regularCompanyUrl) || !isset($record->fullName)) {
                            continue; // Skip this record if companyId or regularCompanyUrl is missing
                        }
                        $peopleLead = new people_leads();


                        $peopleLead->full_name = $record->fullName;
                        $peopleLead->company_name = isset($record->companyName) ? $record->companyName : null;
                        $peopleLead->company_id = $record->companyId;
                        $peopleLead->regular_company_url = $record->regularCompanyUrl;
                        $peopleLead->title = isset($record->title) ? $record->title : null;
                        $peopleLead->mail = isset($record->mail) ? $record->mail : null;
                        $peopleLead->person_url = isset($record->profileUrl) ? $record->profileUrl : null;
                        $peopleLead->connection_degree = isset($record->connectionDegree) ? $record->connectionDegree : null;
                        $peopleLead->company_location = isset($record->companyLocation) ? $record->companyLocation : null;
                        $peopleLead->person_location = isset($record->location) ? $record->location : null;
                        $peopleLead->search_id = $search_id; // Change this value to the appropriate search ID
                        $peopleLead->created_at = now();
                        $peopleLead->updated_at = now();
                        $peopleLead->is_active = 1;

                        $peopleLead->save();
                }

            }
                else{
                    echo "No data found";
                }

                echo "Data inserted successfully!";
                $this->updateJobStatus();
              
            }
            catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
            }
          }


    }
    
    }


