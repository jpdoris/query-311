<?php

class ResponseCodeException extends Exception {};
class InputException extends Exception {};

class QueryClass {

    /**
     * Send query request to 311 API
     *
     * @param $argv
     * @return bool
     */
    public function queryApi($argv)
    {
        $input = $this->processInput($argv);
        $geo = $input[0];
        $args = $input[1];

        if (!empty($geo)) {
            $url = "https://seeclickfix.com/open311/v2/requests.json";
            $request = sprintf("%s?%s", $url, http_build_query($geo));
            header('Content-type: application/json');
            $response = file_get_contents($request);
            $this->handleResponse($response, $args);

            echo "Query finished.\n";
            return true;
        }

        return false;
    }

    /**
     * Process user input
     *
     * @param $argv
     * @return array
     */
    private function processInput($argv)
    {
        $args = [];
        $geo = [
            'lat' => array_key_exists(1, $argv) ? $argv[1] : "",
            'long' => array_key_exists(2, $argv) ? $argv[2] : "",
        ];

        try {
            if (!preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/', $geo['lat']) ||
                !preg_match('/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $geo['long'])) {
                $geo = [];
                throw new InputException();
            } else {
                $args = (array_key_exists(3, $argv) && $argv[3]) ? [explode(',', $argv[3])] : [];
            }
        } catch (InputException $e) {
            echo "Invalid coordinates entered.\n";
        }

        return [$geo, $args];
    }

    /**
     * Handle API response data
     *
     * @param $response
     * @param $args
     * @return bool
     */
    private function handleResponse($response, $args)
    {
        $data = json_decode($response, JSON_OBJECT_AS_ARRAY);

        try {
            if (array_key_exists('full_errors', $data)) {
                throw new ResponseCodeException();
            } else {
                $filters = [
                    'service_request_id',
                    'description',
                    'service_name',
                ];

                if (is_array($args) && array_key_exists(0, $args)) {
                    foreach ($args[0] as $key => $value) {
                        array_push($filters, $value);
                    }
                }

                $filtered_data = [0 => $filters];
                $row = [];
                foreach ($data as $record) {
                    foreach ($filters as $filter) {
                        $row[$filter] = array_key_exists($filter, $record) ? $record[$filter] : '';
                    }
                    $filtered_data[] = $row;
                }

                $this->generateCsv($filtered_data);
            }
        } catch (ResponseCodeException $e) {
            echo "The service returned an error response: " . $filtered_data['full_errors'] . "\n";
        }

        echo "Received response.\n";
        return true;
    }

    /**
     * Generate CSV file from API response
     *
     * @param $data
     * @return bool
     */
    private function generateCsv($data)
    {
        $filename = '311.csv';

        try {
            $fp = fopen($filename, 'w');
            foreach ($data as $fields) {
                fputcsv($fp, $fields);
            }
            fclose($fp);
        } catch (Exception $e) {
            echo "Could not write to CSV file: $e";
        }

        echo "Generated CSV file $filename.\n";
        return true;
    }
}