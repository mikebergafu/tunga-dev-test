<?php


    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Response;

    function log_JSON_file($my_data, $tag='untagged')
    {
        if(env('APP_DEBUG')===true){
            $data = json_encode($my_data);
            $fileName = $tag.'_' .time() . '_datafile.json';
            $dir = '/json_logs/';
            create_directory(storage_path($dir));
            File::put(storage_path($dir . $fileName), $data);
            $file_path = storage_path($dir . $fileName);
            return Response::download($file_path);
        }
    }

    function log_slack($message, $url='https://hooks.slack.com/services/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')
    {

        try {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(["text" => json_encode($message)]));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        } catch (Throwable $throwable) {
            return $throwable->getMessage();
        }
    }

    function clean_throwable(Throwable $throwable){
        return json_encode( [
            'message'=>$throwable->getMessage(),
            'line'=>$throwable->getLine(),
            'file'=>$throwable->getFile()
        ]);
    }

    function update_process_status($file_path){
        $process_tracker = \App\Models\ProcessTracker::where('file_path', $file_path)->first();
        $process_tracker->completed_count += 1;
        return [
            'success'=>$process_tracker->save(),
            'total'=>$process_tracker->total,
            'completed_count'=>$process_tracker->completed_count
        ];
    }

    function initiate_process_status($file_path,$total){
        $process_tracker = new \App\Models\ProcessTracker();
        $process_tracker->name =$file_path;
        $process_tracker->file_path =$file_path;
        $process_tracker->total =$total;
        $process_tracker->completed_count = 0;
        return $process_tracker->save();
    }



