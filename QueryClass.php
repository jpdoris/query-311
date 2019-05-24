<?php

class QueryClass {

    public function __consruct()
    {

    }

    /**
     * Send query request to 311 API
     *
     * @param $params
     * @return string
     */
    public function queryApi($params, $args)
    {
        $url = "https://seeclickfix.com/open311/v2/requests.json";
        $request = sprintf("%s?%s", $url, http_build_query($params));
        header('Content-type: application/json');
        $response = file_get_contents($request);
        $this->handleResponse($response, $args);

        return "Query finished.\n";
    }

    /**
     * Handle API response data
     *
     * @param $response
     * @return string
     */
    private function handleResponse($response, $args)
    {
        $data = json_decode($response, JSON_OBJECT_AS_ARRAY);

        if (array_key_exists('full_errors', $data)) {
            $this->handleErrors($data);
            return "There was an error with the query request. ";
        }

        $filters = [
            'service_request_id',
            'description',
            'service_name',
        ];

        if ($args) {
            $add = explode(',', $args);
            foreach ($add as $key => $value) {
                array_push($filters, $value);
            }
        }

        print_r($filters);

        $filtered_data = [0 => $filters];
        $row = [];
        foreach ($data as $record) {
            foreach ($filters as $filter) {
                $row[$filter] = $record[$filter];
            }
            $filtered_data[] = $row;
        }

        $this->generateCsv($filtered_data);
        return "Received response.";
    }

    /**
     * Handle query errors by returning the error message
     *
     * @param $response
     * @return mixed
     */
    private function handleErrors($response)
    {
        $this->generateCsv($response);
        return $response['description'];
    }

    /**
     * Generate CSV file from API response
     *
     * @param $data
     * @return string
     */
    private function generateCsv($data)
    {
        $filename = '311.csv';
        $fp = fopen($filename, 'w');
        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);

        return "Generated CSV file $filename.\n";
    }
}