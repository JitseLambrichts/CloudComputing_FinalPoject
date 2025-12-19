<?php

namespace App\Models;

use Grpc\BaseStub;

class AnalyticsServiceClient extends BaseStub {

    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    public function StreamPlayerAnalytics($metadata = [], $options = []) {
        return $this->_bidiRequest(
            // PUNT 1: Verwijzing naar de .proto package 'be.cloud'
            '/be.cloud.AnalyticsService/StreamPlayerAnalytics',
            
            // PUNT 2: Verwijzing naar de PHP klasse in de juiste namespace
            ['\App\Models\AnalysisResponse', 'decode'],
            
            $metadata,
            $options
        );
    }
}