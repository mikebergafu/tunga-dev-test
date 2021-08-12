<?php

    use Illuminate\Support\Facades\File;

    function data_path($main_dir=''){
        return $main_dir.'/data/';
    }

    function create_directory($path)
    {
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
            return $path;
        }
        if (File::isDirectory($path)) {
            return $path;
        }
    }

    function move_processed_delete($path, $file_name): bool
    {
        try {
            create_directory(public_path('completed/'));
            return rename($path, public_path('/completed/'.$file_name));
        } catch (Throwable $throwable) {
            log_slack(clean_throwable($throwable));
            return false;
        }
    }
