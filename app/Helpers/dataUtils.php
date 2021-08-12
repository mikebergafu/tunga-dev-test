<?php

    use App\Models\AccountHolder;
    use Illuminate\Database\Eloquent\Model;

    function normalize_csv(string $filename): array
    {
        $array = array_map('str_getcsv', file($filename));
        $records = array();
        if (sizeof($array) > 1) {
            $keys = $array[0];
            for ($i = 1; $i < sizeof($array); $i++) {
                $csv_record = array_map('trim', $array[$i]);
                array_push($records, array_combine($keys, $csv_record));
            }
        }

        return $records;
    }

    function normalize_json(string $filename): array
    {
        return json_decode(file_get_contents($filename));
    }

    function normalize_xml(string $filename): array
    {
        return json_decode(json_encode(simplexml_load_string(file_get_contents($filename))));
    }

    function load_only_json_data_file(string $filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if ($ext === 'json') {
            $data_file = json_decode(file_get_contents($filename), true);
        }
        // throw new Exception('Unsupported file format ('.$ext.')');

        return $data_file;
    }

    function load_data_file(string $filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        switch ($ext) {
            case 'json':
                return normalize_json($filename);
            case 'csv':
                return normalize_csv($filename);
            case 'xml':
                return normalize_xml($filename);
            default:
                throw new Exception('Unsupported file format ('.$ext.')');
        }
    }

    function credit_card_number_filter(string $card_number): bool
    {
        if (strlen($card_number) < 3) {
            return false;
        }
        $i = 0;
        $j = 1;
        $k = 2;
        while ($k < strlen($card_number)) {
            if ($card_number[$i] === $card_number[$j] && $card_number[$j] === $card_number[$k]) {
                return true;
            }
            $i++;
            $j++;
            $k++;
        }
        return false;
    }


